<header class="fixed top-0 left-0 right-0 bg-black text-white z-1000">
    <div class="container mx-auto px-4">
        <nav class="flex items-center justify-between h-20">
            <div class="flex-shrink-0">
                <a href="/" class="flex items-center">
                    <img src="{{ asset('logo.svg') }}" alt="COACHTECH" class="h-8">
                </a>
            </div>
            
            <nav class="flex items-center">
                @if (Route::is('user.login') || Route::is('admin.login') || Route::is('user.register'))
                    {{-- ログイン・登録画面ではロゴのみ表示 --}}
                @elseif (Auth::guard('admin')->check())
                    {{-- 管理者用メニュー --}}
                    <a href="{{route('admin.attendance.index')}}" class="mx-5 text-2xl text-white hover:text-gray-300">勤怠一覧</a>
                    <a href="{{route('admin.staff.index')}}" class="mx-5 text-2xl text-white hover:text-gray-300">スタッフ一覧</a>
                    <a href="{{route('correction.index')}}"  class="mx-5 text-2xl text-white hover:text-gray-300">申請一覧</a>
                    <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="ml-5 text-2xl text-white hover:text-gray-300">ログアウト</button>
                    </form>
                @elseif (Auth::guard('user')->check())
                    {{-- 一般ユーザー用メニュー --}}
                    <a href="{{route('user.timecard')}}" class="mx-5 text-2xl text-white hover:text-gray-300">勤怠</a>
                    <a href="{{route('user.attendance.index')}}" class="mx-5 text-2xl text-white hover:text-gray-300">勤怠一覧</a>
                    <a href="{{route('correction.index')}}"  class="mx-5 text-2xl text-white hover:text-gray-300">申請</a>
                    <form method="POST" action="{{ route('user.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="ml-5 text-2xl text-white hover:text-gray-300">ログアウト</button>
                    </form>
                @endif
            </nav>
        </nav>
    </div>
</header>