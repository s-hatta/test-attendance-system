@extends('/layouts.common')
@section('title','会員登録')

@section('content')
    <form method="POST">
        @csrf
        <p>名前</p>
        <input class="border border-solid border-black" type="text" name="name" value="{{old('name')}}">
        <p>メールアドレス</p>
        <input class="border border-solid border-black" type="text" name="email" value="{{old('email')}}">
        <p>パスワード</p>
        <input class="border border-solid border-black" type="password" name="password">
        <p>パスワード確認</p>
        <input class="border border-solid border-black" type="password" name="password_confirmation">
        <button class="bg-black text-white" type="submit">登録する</button>
    </form>
    <a href="{{route('user.login')}}">ログインはこちら</a>
@endsection