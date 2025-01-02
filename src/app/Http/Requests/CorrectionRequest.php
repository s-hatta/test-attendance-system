<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\CorrectionStatus;
use Carbon\Carbon;

class CorrectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    
    public function getValidatorInstance()
    {
        $dateTime = $this->input('year'). $this->input('date');
        $this->merge([
            'date_time' => $dateTime,
        ]);
        
        return parent::getValidatorInstance();
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date_time' => 'required|date_format:Y年m月d日',
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i|after:clock_in',
            'break_times.*.start' => 'required|date_format:H:i|after:clock_in|before:clock_out',
            'break_times.*.end' => 'required|date_format:H:i|before:clock_out',
            'remark' => 'required',
        ];
    }
    
    public function messages(): array
    {
        return [
            'date_time.*' => '日付が不適切な値です',
            
            'clock_in.*' => '出勤時間もしくは退勤時間が不適切な値です',
            
            'clock_out.*' => '出勤時間もしくは退勤時間が不適切な値です',
            
            'break_times.*.start.required' => '休憩時間が不適切な値です', 
            'break_times.*.start.date_format' => '休憩時間が不適切な値です', 
            'break_times.*.start.after' => '休憩時間が勤務時間外です',
            'break_times.*.start.before' => '休憩時間が勤務時間外です',
            
            'break_times.*.end.required' => '休憩時間が不適切な値です', 
            'break_times.*.end.date_format' => '休憩時間が不適切な値です', 
            'break_times.*.end.before' => '休憩時間が勤務時間外です',
            
            'remark.required' => '備考を記入してください',
        ];
    }
    
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $validator->getData();
            
            if (isset($data['break_times'])) {
                foreach ($data['break_times'] as $index => $break) {
                    /* 休憩終了は休憩開始より後であること */
                    if (isset($break['end']) && isset($break['start'])) {
                        if ($break['end'] <= $break['start']) {
                            $validator->errors()->add(
                                "break_times.{$index}.end",
                                '休憩時間が不適切な値です'
                            );
                        }
                    }
                    
                    /* 休憩終了は出勤時間より後であること */
                    if (isset($break['end']) && isset($data['clock_in'])) {
                        if ($break['end'] <= $data['clock_in']) {
                            $validator->errors()->add(
                                "break_times.{$index}.end",
                                '休憩時間が勤務時間外です'
                            );
                        }
                    }
                }
            }
        });
    }
    
    /**
     * データ整形
     */
    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated();
        $dateStr = $validated['date_time'];
        
        return [
            'date' => Carbon::createFromFormat('Y年m月d日', $dateStr)->startOfDay(),
            'clock_in_at' => Carbon::createFromFormat('Y年m月d日 H:i', $dateStr . ' ' . $validated['clock_in']),
            'clock_out_at' => Carbon::createFromFormat('Y年m月d日 H:i', $dateStr . ' ' . $validated['clock_out']),
            'remark' => $validated['remark'],
            'break_times' => $validated['break_times'] ?? [],
        ];
    }
}
