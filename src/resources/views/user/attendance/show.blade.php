@extends('/layouts.common')
@section('title','勤怠詳細')

@section('content')
<div class="max-w-[800px] mx-auto px-5 py-6">
    
    {{-- 見出し --}}
    <h1 class="text-[30px] font-bold mb-8 pl-[20px] border-l-[8px] mt-[50px] border-black">勤怠詳細</h1>
    
    {{-- 勤怠詳細 --}}
    @include('parts.attendance', ['routeAction' => 'user.attendance.store'])
</div>
@endsection

@section('scripts')
    @vite([
        'resources/js/formatter/year-input-formatter.js',
        'resources/js/formatter/date-input-formatter.js',
        'resources/js/formatter/time-input-formatter.js',
    ])
@endsection