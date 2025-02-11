@extends('/layouts.common')
@section('title','スタッフ一覧（管理者）')

@section('content')
<div class="max-w-[900px] mx-auto px-5 py-6">
    {{-- 見出し --}}
    <h1 class="text-[30px] font-bold mb-8 pl-[20px] border-l-[8px] mt-[50px] border-black">スタッフ一覧</h1>
    
    {{-- スタッフテーブル --}}
    <div class="bg-[#ffffff] rounded-[10px] mt-[46px]">
        <table class="min-w-full divide-y-[3px] divide-[#e1e1e1]">
            {{-- ヘッダ --}}
            <thead class="text-[16px] font-bold text-center text-[#737373]">
                <tr>
                    <th class="px-6 py-3 ">名前</th>
                    <th class="px-6 py-3 ">メールアドレス</th>
                    <th class="px-6 py-3 ">月次勤怠</th>
                </tr>
            </thead>
            {{-- データ --}}
            <tbody class="divide-y-[2px] divide-[#e1e1e1]">
                @forelse($users as $user)
                <tr class="text-[16px] font-bold text-center text-[#737373] hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $user->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $user->email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('admin.staff.show', ['id' => $user->id]) }}" 
                            class="text-black hover:text-gray-600">
                            詳細
                        </a>
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