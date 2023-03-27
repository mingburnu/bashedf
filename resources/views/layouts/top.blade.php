<nav class="navbar navbar-expand-lg navbar-transparent  bg-primary  navbar-absolute">
  <div class="container-fluid">
    {{--<div class="navbar-wrapper">--}}
    {{--<a class="navbar-brand" href="#pablo">{{ $namePage }}</a>--}}
    {{--</div>--}}
    <button class="navbar-toggler d-none" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-bar navbar-kebab"></span>
      <span class="navbar-toggler-bar navbar-kebab"></span>
      <span class="navbar-toggler-bar navbar-kebab"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navigation">
      <ul class="navbar-nav">
        {{--<li class="nav-item">--}}
          {{--<a href="{{ route('login') }}" class="nav-link">--}}
            {{--<i class="now-ui-icons design_app"></i> {{ __("Dashboard") }}--}}
          {{--</a>--}}
        {{--</li>--}}
        {{-- <li class="nav-item @if ($activePage == 'register') active @endif">
          @isset($url)
            <a href='{{ url("register/$url") }}' class="nav-link">
              <i class="now-ui-icons tech_mobile"></i> {{ __("註冊") }}
            </a>
          @else
            <a href="{{ route('register') }}" class="nav-link">
              <i class="now-ui-icons tech_mobile"></i> {{ __("註冊") }}
            </a>
          @endisset
        </li>
        <li class="nav-item @if ($activePage == 'login') active @endif ">
          @isset($url)
            <a href='{{ url("login/$url") }}' class="nav-link">
              <i class="now-ui-icons gestures_tap-01"></i> {{ __("登入") }}
            </a>
          @else
            <a href="{{ route('login') }}" class="nav-link">
              <i class="now-ui-icons gestures_tap-01"></i> {{ __("登入") }}
            </a>
          @endisset
        </li> --}}
      </ul>
    </div>
  </div>
</nav>
