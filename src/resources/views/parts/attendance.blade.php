@php
    $tableRowClass = "relative border-b-[2px] border-[#e1e1e1] px-10 py-4 flex min-h-[91px] flex-wrap";
    $tableHeaderClass = "w-[250px] text-[#737373] text-[16px] font-bold flex items-center";
    $tableDataClass = "flex w-[316px]";
    $tableDataSpanClass = "text-black m-auto font-bold flex-1 text-center";
    $tableTextInputClass = "w-[103px] h-[29px] m-auto border border-[#e1e1e1] rounded-[4px] text-[16px] font-bold flex-1 text-center";
    $tableTextareaClass = "w-full px-2 py-1 border border-[#e1e1e1] rounded-[4px] text-[14px] font-bold resize-none flex-1";
    $errorMessageClass = "w-full text-red-500 text-sm mt-1 pl-[250px]";
@endphp

<form method="POST" action="{{ route($routeAction, ['id' => $id]) }}">
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
                    {{-- 年 --}}
                    <input
                        type="text"
                        name="year"
                        value="{{$param['year']}}"
                        class="year-input {{$tableTextInputClass}} @error('date_time') border-red-500 @enderror"
                        data-original-value="{{$param['year']}}"
                        @if(isset($param['is_pending'])) disabled @endif
                    >
                    <span class="{{$tableDataSpanClass}}"></span>
                    {{-- 月日 --}}
                    <input
                        type="text"
                        name="date"
                        value="{{$param['date']}}"
                        class="date-input {{$tableTextInputClass}} @error('date_time') border-red-500 @enderror"
                        data-original-value="{{$param['date']}}"
                        @if(isset($param['is_pending'])) disabled @endif
                    >
                </div>
                @error('date_time')
                    <div class="{{ $errorMessageClass }}">{{ $message }}</div>
                @enderror
            </div>

            {{-- 出勤：退勤 --}}
            <div class="{{ $tableRowClass }}">
                <div class="{{ $tableHeaderClass }}">出勤・退勤</div>
                <div class="{{ $tableDataClass }}">
                    {{-- 出勤時刻 --}}
                    <input
                        type="text"
                        name="clock_in"
                        value="{{$param['clock_in']}}"
                        class="time-input {{$tableTextInputClass}} @error('clock_in') border-red-500 @enderror"
                        data-original-value="{{$param['clock_in']}}"
                        @if(isset($param['is_pending'])) disabled @endif
                    >
                    <span class="{{$tableDataSpanClass}}">〜</span>
                    {{-- 退勤時刻 --}}
                    <input
                        type="text"
                        name="clock_out"
                        value="{{$param['clock_out']}}"
                        class="time-input {{$tableTextInputClass}} @error('clock_out') border-red-500 @enderror"
                        data-original-value="{{$param['clock_out']}}"
                        @if(isset($param['is_pending'])) disabled @endif
                    >
                </div>
                @if( $errors->has('clock_in') )
                    <div class="{{ $errorMessageClass }}">{{ $errors->first('clock_in') }}</div>
                @else
                    @if( $errors->has('clock_out') )
                        <div class="{{ $errorMessageClass }}">{{ $errors->first('clock_out') }}</div>
                    @endif
                @endif

            </div>

            {{-- 休憩 --}}
            @foreach($param['break_times'] as $breakTime)
            <div class="{{ $tableRowClass }}">
                <div class="{{ $tableHeaderClass }}">休憩</div>
                <div class="{{ $tableDataClass }}">
                    {{-- 開始時刻 --}}
                    <input
                        type="text"
                        name="break_times[{{$loop->index}}][start]"
                        value="{{$breakTime['start']}}"
                        class="time-input {{$tableTextInputClass}} @error('break_times.'.$loop->index.'.start') border-red-500 @enderror"
                        data-original-value="{{$breakTime['start']}}"
                        @if(isset($param['is_pending'])) disabled @endif
                    >
                    <span class="{{$tableDataSpanClass}}">〜</span>
                    {{-- 終了時刻 --}}
                    <input
                        type="text"
                        name="break_times[{{$loop->index}}][end]"
                        value="{{$breakTime['end']}}"
                        class="time-input {{$tableTextInputClass}} @error('break_times.'.$loop->index.'.end') border-red-500 @enderror"
                        data-original-value="{{$breakTime['end']}}"
                        @if(isset($param['is_pending'])) disabled @endif
                    >
                </div>
                @if( $errors->has('break_times.'.$loop->index.'.start') )
                    <div class="{{ $errorMessageClass }}">{{ $errors->first('break_times.'.$loop->index.'.start') }}</div>
                @else
                    @if( $errors->has('break_times.'.$loop->index.'.end') )
                        <div class="{{ $errorMessageClass }}">{{ $errors->first('break_times.'.$loop->index.'.end') }}</div>
                    @endif
                @endif
            </div>
            @endforeach

            {{-- 備考 --}}
            <div class="{{ $tableRowClass }}">
                <div class="{{ $tableHeaderClass }}">備考</div>
                <div class="{{ $tableDataClass }}">
                    <textarea
                        name="remark"
                        class="{{$tableTextareaClass}} @error('remark') border-red-500 @enderror"
                        rows="3"
                        @if(isset($param['is_pending'])) disabled @endif
                    >{{$param['remark']}}</textarea>
                </div>
                @error('remark')
                    <div class="{{ $errorMessageClass }}">{{ $message }}</div>
                @enderror
            </div>
        </div>
        {{-- 修正申請ボタン --}}
        <div class="flex justify-end space-x-4">
            @if(isset($param['is_pending']))
            <p class="text-red-500">※承認待ちのため修正はできません</p>
            @else
            <button
                type="submit"
                class="bg-black text-white my-20 px-[43px] py-[9px] rounded-[5px] text-[22px] font-bold hover:opacity-80 transition-opacity">
                修正
            </button>
            @endif
        </div>
    </form>
