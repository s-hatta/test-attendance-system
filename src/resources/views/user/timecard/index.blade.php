@extends('/layouts.common')
@section('title','打刻画面')

@section('content')
    <div class="text-center">
        <p id="current-date" class="text-[40px] font-normal"></p>
        <p id="current-time" class="text-[80px] font-bold"></p>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/clock.js'])
@endsection