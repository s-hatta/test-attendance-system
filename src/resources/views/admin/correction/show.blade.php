@extends('/layouts.common')
@section('title','申請詳細（管理者）')

@php
    $tableRowClass = "relative border-b-[2px] border-[#e1e1e1] px-10 py-4 flex min-h-[91px] flex-wrap";
    $tableHeaderClass = "w-[250px] text-[#737373] text-[16px] font-bold flex items-center";
    $tableDataClass = "flex w-[316px]";
    $tableDataSpanClass = "text-black m-auto font-bold flex-1 text-center";
    $tableTextInputClass = "w-[103px] h-[29px] m-auto text-[16px] font-bold flex-1 text-center";
    $tableTextareaClass = "w-full px-2 py-1 text-[14px] font-bold resize-none flex-1";
@endphp

@section('content')
<div class="max-w-[800px] mx-auto px-5 py-6">
    
    {{-- 見出し --}}
    <h1 class="text-[30px] font-bold mb-8 pl-[20px] border-l-[8px] mt-[50px] border-black">勤怠詳細</h1>
    
    {{-- 勤怠詳細 --}}
    <form method="POST" action="{{ route('admin.correction.update', ['attendance_correct_request' => $attendance_correct_request]) }}">
        @csrf
        <div class="bg-white rounded-lg shadow-sm mb-6 mt-[50px]">
            {{-- 名前 --}}
            <div class="{{ $tableRowClass }}">
                <div class="{{ $tableHeaderClass }}">名前</div>
                <div class="{{ $tableDataClass }}">
                    <div class="flex-1 text-[16px] font-bold my-auto">{{$param['name']}}</div>
                </div>
            </div>
            
            {{-- 日付 --}}
            <div class="{{ $tableRowClass }}">
                <div class="{{ $tableHeaderClass }}">日付</div>
                <div class="{{ $tableDataClass }}">
                    <p class="{{$tableTextInputClass}}">{{$param['year']}}</p>
                    <span class="{{$tableDataSpanClass}}"></span>
                    <p class="{{$tableTextInputClass}}">{{$param['date']}}</p>
                </div>
            </div>
            
            {{-- 出勤：退勤 --}}
            <div class="{{ $tableRowClass }}">
                <div class="{{ $tableHeaderClass }}">出勤・退勤</div>
                <div class="{{ $tableDataClass }}">
                    <p class="{{$tableTextInputClass}}">{{$param['clock_in']}}</p>
                    <span class="{{$tableDataSpanClass}}">〜</span>
                    <p class="{{$tableTextInputClass}}">{{$param['clock_out']}}</p>
                </div>
            </div>
            
            {{-- 休憩 --}}
            @foreach($param['break_times'] as $breakTime)
            <div class="{{ $tableRowClass }}">
                <div class="{{ $tableHeaderClass }}">休憩</div>
                <div class="{{ $tableDataClass }}">
                    {{-- 開始時刻 --}}
                    <p class="{{$tableTextInputClass}} time-input">
                        {{$breakTime['start']}}
                    </p>
                    <span class="{{$tableDataSpanClass}}">〜</span>
                    {{-- 終了時刻 --}}
                    <p class="{{$tableTextInputClass}} time-input">
                        {{$breakTime['end']}}
                    </p>
                </div>
            </div>
            @endforeach
            
            {{-- 備考 --}}
            <div class="{{ $tableRowClass }}">
                <div class="{{ $tableHeaderClass }}">備考</div>
                <div class="{{ $tableDataClass }}">
                    <p 
                        name="remark" 
                        class="{{$tableTextareaClass}}"
                        rows="3"
                    >{{$param['remark']}}</p>
                </div>
            </div>
        </div>
        {{-- 申請承認ボタン --}}
        <div class="flex justify-end space-x-4">
            @switch($param['status']->value)
                @case(0)
                    <button 
                        type="submit"
                        class="bg-black text-white my-10 px-[43px] py-[9px] rounded-[5px] text-[22px] font-bold hover:opacity-80 transition-opacity">
                        承認
                    </button>
                    @break
                @case(1)
                    <p class="bg-[#696969] text-white my-10 px-[22px] py-[9px] rounded-[5px] text-[22px] font-bold">承認済み</p>
                    @break
                @default
                    
            @endswitch
            
        </div>
    </form>
</div>
@endsection