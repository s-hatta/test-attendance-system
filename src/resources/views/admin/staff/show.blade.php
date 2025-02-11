@extends('/layouts.common')
@section('title','スタッフ別勤怠一覧（管理者）')

@section('content')
<div class="max-w-[900px] mx-auto px-5 py-6">

    {{-- 見出し --}}
    <h1 class="text-[30px] font-bold mb-8 pl-[20px] border-l-[8px] mt-[50px] border-black">{{$name}}さんの勤怠</h1>

    {{-- 月選択ナビゲーション --}}
    <div class="bg-white rounded-[10px]">
        <div class="flex items-center justify-between px-6 py-4">
            <a href="{{ route('admin.staff.show', ['id' => Route::current()->parameter('id'), 'year' => $prevMonth->year, 'month' => $prevMonth->month]) }}"
               class="text-[#737373] hover:text-black text-[16px] font-bold">
                ← 前月
            </a>
            <div class="text-[20px] font-bold">
                📅 {{ $currentDate->format('Y/m') }}
            </div>
            <a href="{{ route('admin.staff.show', ['id' => Route::current()->parameter('id'), 'year' => $nextMonth->year, 'month' => $nextMonth->month]) }}"
               class="text-[#737373] hover:text-black text-[16px] font-bold">
                翌月 →
            </a>
        </div>
    </div>

    {{-- 勤怠テーブル --}}
    <div class="bg-[#ffffff] rounded-[10px] mt-[46px]">
        <table class="min-w-full divide-y-[3px] divide-[#e1e1e1]">
            {{-- ヘッダ --}}
            <thead class="text-[16px] font-bold text-center text-[#737373]">
                <tr>
                    <th class="px-6 py-3 ">日付</th>
                    <th class="px-6 py-3 ">出勤</th>
                    <th class="px-6 py-3 ">退勤</th>
                    <th class="px-6 py-3 ">休憩</th>
                    <th class="px-6 py-3 ">合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            {{-- データ --}}
            <tbody class="divide-y-[2px] divide-[#e1e1e1]">
                @foreach($dates as $dateData)
                <tr class="text-[16px] font-bold text-center text-[#737373] hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $dateData['date']->format('m/d') }}({{ $dateData['date']->isoFormat('ddd') }})
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $dateData['attendance'] ? $dateData['attendance']->clock_in_at?->format('H:i') : '' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $dateData['attendance'] ? $dateData['attendance']->clock_out_at?->format('H:i') : '' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $dateData['attendance'] ? $dateData['attendance']->total_break_time : '' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $dateData['attendance'] ? $dateData['attendance']->total_work_time : '' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($dateData['attendance'])
                            <a href="{{ route('admin.attendance.show', [
                                    'id' => $dateData['attendance']->id
                                ]) }}"
                                class="text-black hover:text-gray-600">
                                詳細
                            </a>
                        @else
                            <a href="{{ route('admin.attendance.show', [
                                    'id' => 0,
                                    'userId' => $userId,
                                    'date' => $dateData['date']->format('m/d')
                                ]) }}"
                                class="text-black hover:text-gray-600">
                                詳細
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    {{-- CSV出力 --}}
    <div class="flex justify-end space-x-4">
        <form method="POST" action="{{ route('admin.staff.export', ['id' => Route::current()->parameter('id'), 'year' => $currentDate->year, 'month' => $currentDate->month]) }}">
            @csrf
            <button
                type="submit"
                class="bg-black text-white my-10 px-[43px] py-[9px] rounded-[5px] text-[22px] font-bold hover:opacity-80 transition-opacity">
                CSV出力
            </button>
        </form>
    </div>
</div>
@endsection
