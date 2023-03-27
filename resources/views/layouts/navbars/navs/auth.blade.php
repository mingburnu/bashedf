@auth('admin')
    @if (is_null(Auth::user()->google2fa_secret) || (session('google2fa')['auth_passed'] ?? false) === true)
        @include('layouts.navbars.navs.admin')
    @endif
@elseauth('user')
    @if (is_null(Auth::user()->google2fa_secret) || (session('google2fa')['auth_passed'] ?? false) === true)
        @include('layouts.navbars.navs.user')
    @endif
@endauth