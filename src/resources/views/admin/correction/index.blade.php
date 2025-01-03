@extends('/layouts.common')
@section('title','申請一覧（管理者）')

@section('content')
<div class="max-w-[900px] mx-auto px-5 py-6">
    {{-- 見出し --}}
    <h1 class="text-[30px] font-bold mb-8 pl-[20px] border-l-[8px] mt-[50px] border-black">申請一覧</h1>
    
    {{-- タブナビゲーション --}}
    <div class="flex border-b border-black mt-[32px]">
        <a href="{{ route('admin.correction.index', ['status' => 0]) }}" 
           class="px-10 py-2 text-[16px] {{ $status == 0 ? 'font-bold' : 'hover:text-gray-500' }}">
            承認待ち
        </a>
        <a href="{{ route('admin.correction.index', ['status' => 1]) }}"
           class="px-10 py-2 text-[16px] {{ $status == 1 ? 'font-bold' : 'hover:text-gray-500' }}">
            承認済み
        </a>
    </div>

    {{-- 申請テーブル --}}
    <div class="bg-[#ffffff] rounded-[10px] mt-[46px]">
        <table class="min-w-full divide-y-[3px] divide-[#e1e1e1]">
            {{-- ヘッダー --}}
            <thead class="text-[16px] font-bold text-[#737373]">
                <tr class="text-[14px] font-bold text-[#737373]">
                    <th class="text-center py-4 w-28">状態</th>
                    <th class="text-left py-4 w-28">名前</th>
                    <th class="text-left py-4 w-28">対象日時</th>
                    <th class="text-left py-4 ">申請理由</th>
                    <th class="text-left py-4 w-28">申請日時</th>
                    <th class="text-left py-4 w-28">詳細</th>
                </tr>
            </thead>
            {{-- データ --}}
            <tbody class="divide-y-[2px] divide-[#e1e1e1]">
                @forelse($corrections as $correction)
                <tr class="text-[16px] font-bold text-[#737373] hover:bg-gray-50">
                    <td class="text-center py-4 whitespace-nowrap">
                        {{ $correction->status->getMessage() }}
                    </td>
                    <td class="text-left py-4 whitespace-nowrap">
                        {{ $correction->user->name }}
                    </td>
                    <td class="text-left py-4 whitespace-nowrap">
                        {{ $correction->date->format('Y/m/d') }}
                    </td>
                    <td class="text-left py-4">
                        {{ $correction->remark }}
                    </td>
                    <td class="text-left py-4 whitespace-nowrap">
                        {{ $correction->created_at->format('Y/m/d') }}
                    </td>
                    <td class="text-left py-4 whitespace-nowrap">
                        <a href="{{ route('admin.attendance.show', ['id' => $correction->attendance_id]) }}" 
                           class="text-black hover:text-gray-600 font-bold">詳細</a>
                    </td>
                </tr>
                @empty
                <tr class="text-[16px] font-bold text-center text-[#737373] hover:bg-gray-50">
                    <td colspan="6" class="py-4">
                        データがありません
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection