<header class="fixed top-0 left-0 right-0 bg-black text-white z-1000">
    <div class="container mx-auto px-4">
        <nav class="flex items-center justify-between h-20">
            <div class="flex-shrink-0">
                <a href="/" class="flex items-center">
                    <img src="{{ asset('logo.svg') }}" alt="COACHTECH" class="h-8">
                </a>
            </div>

            <!-- ハンバーガーメニューボタン (1100px以下で表示) -->
            <button id="hamburger" class="block min-[1101px]:hidden p-2">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path id="hamburger-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path id="close-icon" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- メインナビゲーション (1100px以上で表示) -->
            <nav id="main-nav" class="flex items-center max-[1100px]:hidden">
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

            <!-- モバイルナビゲーション (1100px以下でハンバーガーメニューとして表示) -->
            <nav id="mobile-nav" class="hidden fixed top-20 left-0 right-0 bg-black p-4">
                <div class="flex flex-col space-y-4">
                    @if (Route::is('user.login') || Route::is('admin.login') || Route::is('user.register'))
                        {{-- ログイン・登録画面ではロゴのみ表示 --}}
                    @elseif (Auth::guard('admin')->check())
                        {{-- 管理者用メニュー --}}
                        <a href="{{route('admin.attendance.index')}}" class="text-xl text-white hover:text-gray-300">勤怠一覧</a>
                        <a href="{{route('admin.staff.index')}}" class="text-xl text-white hover:text-gray-300">スタッフ一覧</a>
                        <a href="{{route('correction.index')}}" class="text-xl text-white hover:text-gray-300">申請一覧</a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="text-xl text-white hover:text-gray-300">ログアウト</button>
                        </form>
                    @elseif (Auth::guard('user')->check())
                        {{-- 一般ユーザー用メニュー --}}
                        <a href="{{route('user.timecard')}}" class="text-xl text-white hover:text-gray-300">勤怠</a>
                        <a href="{{route('user.attendance.index')}}" class="text-xl text-white hover:text-gray-300">勤怠一覧</a>
                        <a href="{{route('correction.index')}}" class="text-xl text-white hover:text-gray-300">申請</a>
                        <form method="POST" action="{{ route('user.logout') }}">
                            @csrf
                            <button type="submit" class="text-xl text-white hover:text-gray-300">ログアウト</button>
                        </form>
                    @endif
                </div>
            </nav>
        </nav>
    </div>
</header>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.getElementById('hamburger');
    const mobileNav = document.getElementById('mobile-nav');
    const hamburgerIcon = document.getElementById('hamburger-icon');
    const closeIcon = document.getElementById('close-icon');
    let isOpen = false;

    function toggleMenu() {
        isOpen = !isOpen;
        mobileNav.classList.toggle('hidden');
        hamburgerIcon.classList.toggle('hidden');
        closeIcon.classList.toggle('hidden');
    }

    hamburger.addEventListener('click', toggleMenu);
});
</script>
@endsection
