@extends('/layouts.auth')
@section('title','会員登録')

@php
    $labelClass = "block text-[24px] font-bold mb-1";
    $inputClass = "w-full text-[26px] font-bold px-3 py-2 border border-black rounded-[4px]";
    $alermClass = "text-base font-bold text-[#f00] min-h-6";
    $buttonClass = "w-full text-[26px] font-bold bg-black text-white py-4 px-4 mt-8 rounded-[4px] hover:bg-gray-800"
@endphp

@section('content')
<div class="flex justify-center">
    <div class="w-full max-w-[680px] p-6 mt-10">
        <h1 class="text-center text-[36px] font-bold">会員登録</h1>
        
        <form method="POST" action="{{ route('user.register') }}" class="space-y-6">
            @csrf
            {{-- 名前 --}}
            <div>
                <label class="{{$labelClass}}">名前</label>
                <input type="text" name="name" class="{{$inputClass}}" value="{{old('name')}}">
                <p class="{{$alermClass}}">@error('name'){{ $message }}@enderror</p>
            </div>
            {{-- メールアドレス --}}
            <div class="">
                <label class="{{$labelClass}}">メールアドレス</label>
                <input type="text" name="email" class="{{$inputClass}}" value="{{old('email')}}">
                <p class="{{$alermClass}}">@error('email'){{ $message }}@enderror</p>
            </div>
            {{-- パスワード --}}
            <div>
                <label class="{{$labelClass}}">パスワード</label>
                <input type="password" name="password" class="{{$inputClass}}">
                <p class="{{$alermClass}}">@error('password'){{ $message }}@enderror</p>
            </div>
            {{-- パスワード確認 --}}
            <div>
                <label class="{{$labelClass}}">パスワード確認</label>
                <input type="password" name="password_confirmation" class="{{$inputClass}}">
            </div>
            {{-- ログインボタン --}}
            <div>
                <button type="submit" class="{{$buttonClass}}">
                    登録する
                </button>
            </div>
        </form>
        
        {{-- ログインページへのリンク --}}
        <div class="text-center text-[20px] text-[#0073cc] mt-8">
            <a href="{{route('user.login')}}">ログインはこちら</a>
        </div>
    </div>
</div>
@endsection