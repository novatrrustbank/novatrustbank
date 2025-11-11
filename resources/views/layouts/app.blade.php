<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'NovaTrust Bank') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    <div id="app">
        @yield('content')
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')

    </body>
    <!-- Identify the current visitor (for logged-in users) -->
    <script type="text/javascript">
        var Tawk_API = Tawk_API || {};
        Tawk_API.visitor = {
            name : "{{ Auth::check() ? Auth::user()->name : 'Guest User' }}",
            email : "{{ Auth::check() ? Auth::user()->email : '' }}"
        };
    </script>

    <!-- Start of Tawk.to Script -->
    <script type="text/javascript">
        var Tawk_LoadStart = new Date();
        (function() {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/69129b3e36dfb3195ff1a2b0/1j9oasreo';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>
    <!-- End of Tawk.to Script -->
</body>
</html>
