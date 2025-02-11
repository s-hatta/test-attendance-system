@extends('/layouts.common')
@section('title','申請一覧（管理者）')

@section('content')
<div class="max-w-[900px] mx-auto px-5 py-6">
    {{-- 見出し --}}
    <h1 class="text-[30px] font-bold mb-8 pl-[20px] border-l-[8px] mt-[50px] border-black">申請一覧</h1>
    
    {{-- タブナビゲーション --}}
    <div class="flex border-b border-black mt-[32px]">
        <button 
            class="px-10 py-2 text-[16px] text-black tab-button [&.active]:font-bold active" 
            data-tab="pending">
            承認待ち
        </button>
        <button 
            class="px-10 py-2 text-[16px] text-black tab-button [&.active]:font-bold" 
            data-tab="approved">
            承認済み
        </button>
    </div>
    
    {{-- タブコンテンツ --}}
    <div class="tab-content active" id="pending-content">
        @include('parts.corrections', ['corrections' => $pendingCorrections])
    </div>
    <div class="tab-content hidden" id="approved-content">
        @include('parts.corrections', ['corrections' => $approvedCorrections])
    </div>
</div>
@endsection


@section('scripts')
    @vite(['resources/js/tab-switcher.js'])
@endsection