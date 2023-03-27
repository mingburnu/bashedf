@auth('admin')
    <audio id="myAudio">
        <source src="{{asset('voice/voice_205511.aac')}}" type="audio/ogg">
    </audio>
    <script>
        Echo.private('checker').listen('UncheckedOrderEvent', (e) => {
            if ($('meta[name="app-debug"]').attr('content')) {
                console.log(e.unchecked_order_count);
            } else {
                if (e.unchecked_order_count > 0) {
                    document.getElementById("myAudio").play();
                }
            }
        });
    </script>
@endauth
@include('layouts.navbars.sidebar')
<main id="main" class="page-content bg-trueGray-100">
    <div id="overlay" class="overlay"></div>
    @yield('content')
</main>