@extends('/layouts.common')
@section('title','打刻画面')

@php
    $statusClass = "bg-[#c8c8c8] rounded-[50px] w-[100px] h-[40px] text-[18px] font-bold text-[#696969] flex items-center justify-center";
    $buttonClockClass = "bg-[#000000] rounded-[20px] w-[221px] h-[77px] text-[32px] font-bold text-white";
    $buttonBreakClass = "bg-[#ffffff] rounded-[20px] w-[221px] h-[77px] text-[32px] font-bold text-black";
    $statusMessage = $attendance ? $attendance->getStatusMessage() : '勤務外';
@endphp

@section('content')
    <div class="h-[calc(100vh-80px)] flex items-center justify-center -mt-[80px]">
        <div>
            <div class="flex justify-center">
                {{-- ステータス --}}
                <p class="{{ $statusClass }}">{{ $statusMessage }}</p>
            </div>
            <div>
                {{-- 日付と時間 --}}
                <p id="current-date" class="text-[40px] font-normal text-center mt-[32px]"></p>
                <p id="current-time" class="text-[80px] font-bold text-center mt-[32px]"></p>
                
                {{-- ボタンもしくはメッセージ --}}
                <div class="flex justify-center mt-[87px] gap-[82px]">
                    @if(!$attendance || $attendance->status === 0)
                        <form method="POST" action="{{ route('user.timecard.clockIn') }}">
                            @csrf
                            <input type="hidden" name="timestamp" value="{{ now() }}">
                            <button type="submit" class="{{ $buttonClockClass }}">出勤</button>
                        </form>
                    @elseif($attendance->status === 1)
                        <div class="flex gap-[82px]">
                            <form method="POST" action="{{ route('user.timecard.clockOut') }}">
                                @csrf
                                <input type="hidden" name="timestamp" value="{{ now() }}">
                                <button type="submit" class="{{ $buttonClockClass }}">退勤</button>
                            </form>
                            <form method="POST" action="{{ route('user.timecard.startBreak') }}">
                                @csrf
                                <input type="hidden" name="timestamp" value="{{ now() }}">
                                <button type="submit" class="{{ $buttonBreakClass }}">休憩入</button>
                            </form>
                        </div>
                    @elseif($attendance->status === 2)
                        <form method="POST" action="{{ route('user.timecard.endBreak') }}">
                            @csrf
                            <input type="hidden" name="timestamp" value="{{ now() }}">
                            <button type="submit" class="{{ $buttonBreakClass }}">休憩戻</button>
                        </form>
                    @elseif($attendance->status === 3)
                        <p class="text-[25px] font-bold">お疲れさまでした。</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/clock.js'])
@endsection