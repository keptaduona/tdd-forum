<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Forum</title>

    <!-- Styles -->
    
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>

    <style media="screen">
        body {
            padding-bottom: 10px;
        }
        .level {
            display: flex;
            align-items: center;
        }
        .flex {
            flex: 1;
        }
    </style>
</head>
<body style="padding-bottom: 100px">
    <div id="app">
        @include('layouts.nav')

        @yield('content')

        <flash message="{{session('flash')}}"></flash>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
