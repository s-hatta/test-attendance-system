@extends('/layouts.auth')
@section('title','ログイン')

@php
    $labelClass = "block text-[24px] font-bold mb-1";
    $inputClass = "w-full text-[26px] font-bold px-3 py-2 border border-black rounded-[4px]";
    $alermClass = "text-base font-bold text-[#f00] min-h-6";
    $buttonClass = "w-full text-[26px] font-bold bg-black text-white py-4 px-4 mt-8 rounded-[4px] hover:bg-gray-800"
@endphp

@section('content')
<div class="flex justify-center">
    <div class="w-full max-w-[680px] p-6 mt-10">
        <h1 class="text-center text-[36px] font-bold">ログイン</h1>
        
        <form method="POST" action="{{ route('user.login') }}" class="space-y-6">
            @csrf
            {{-- メールアドレス --}}
            <div>
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
            {{-- ログインボタン --}}
            <div>
                <button type="submit" class="{{$buttonClass}}">
                    ログインする
                </button>
            </div>
        </form>
        
        {{-- 会員登録ページへのリンク --}}
        <div class="text-center text-[20px] text-[#0073cc] mt-8">
            <a href="{{route('user.register')}}">会員登録はこちら</a>
        </div>
    </div>
</div>
@endsection