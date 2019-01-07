<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ 'Chatzera com pusher' }}</title>

    <!-- Scripts -->
    <script src="js/app.js" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">

    <!-- Styles -->
    <link href="css/app.css" rel="stylesheet">
</head>
<body>
    <div id="app">
        <v-toolbar>
            <v-toolbar-side-icon></v-toolbar-side-icon>
            <v-toolbar-title> Chat da galera</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-toolbar-items>
                    @guest
                        <v-btn flat href="login">Login</v-btn>
                        <v-btn flat href="register">Register</v-btn>
                    @else
                        <v-btn flat> {{ Auth::user()->name }}</v-btn>
                         <v-btn flat href="private">Chama no PV</v-btn>
                        <v-btn flat
                        @click=" $refs.logoutForm.submit(); ">
                        Logout</v-btn>
                    @endguest
                    <form ref="logoutForm" action="/logout" method="POST" style="display: none;">@csrf</form>
            </v-toolbar-items>
        </v-toolbar>

        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>