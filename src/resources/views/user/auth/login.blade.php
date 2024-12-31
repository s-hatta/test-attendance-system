@extends('/layouts.common')
@section('title','ログイン')

@section('content')
    <form method="POST">
        @csrf
        <p>メールアドレス</p>
        <input class="border border-solid border-black" type="text" name="email" value="{{old('email')}}">
        <p>パスワード</p>
        <input class="border border-solid border-black" type="password" name="password">
        <button class="bg-black text-white" type="submit">登録する</button>
    </form>
    <a href="">会員登録はこちら</a>
@endsection