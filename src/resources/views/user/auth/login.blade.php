@extends('/layouts.common')
@section('title','ログイン')

@section('content')
    <form method="POST" method="route{{'user.login'}}">
        @csrf
        <p>メールアドレス</p>
        <p>{{$errors->first('email')}}</p>
        <input class="border border-solid border-black" type="text" name="email" value="{{old('email')}}">
        <p>パスワード</p>
        <p>{{$errors->first('password')}}</p>
        <input class="border border-solid border-black" type="password" name="password">
        <button class="bg-black text-white" type="submit">ログインする</button>
    </form>
    <a href="{{route('user.register')}}">会員登録はこちら</a>
@endsection