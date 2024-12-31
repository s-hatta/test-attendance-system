@extends('/layouts.common')
@section('title','会員登録')

@section('content')
    <form method="POST" method="{{route('user.register')}}">
        @csrf
        <p>名前</p>
        <p>{{$errors->first('name')}}</p>
        <input class="border border-solid border-black" type="text" name="name" value="{{old('name')}}">
        <p>メールアドレス</p>
        <p>{{$errors->first('email')}}</p>
        <input class="border border-solid border-black" type="text" name="email" value="{{old('email')}}">
        <p>パスワード</p>
        <p>{{$errors->first('password')}}</p>
        <input class="border border-solid border-black" type="password" name="password">
        <p>パスワード確認</p>
        <input class="border border-solid border-black" type="password" name="password_confirmation">
        <button class="bg-black text-white" type="submit">登録する</button>
    </form>
    <a href="{{route('user.login')}}">ログインはこちら</a>
@endsection