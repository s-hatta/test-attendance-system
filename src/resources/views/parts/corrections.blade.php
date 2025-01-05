{{-- 申請一覧 --}}
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
                    @if (Auth::guard('admin')->check())
                    <a href="{{ route('admin.correction.show', ['attendance_correct_request' => $correction->id]) }}" 
                        class="text-black hover:text-gray-600 font-bold">詳細</a>
                    @elseif (Auth::guard('user')->check())
                    <a href="{{ route('user.attendance.show', ['id' => $correction->attendance_id]) }}" 
                        class="text-black hover:text-gray-600 font-bold">詳細</a>
                    @endif
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