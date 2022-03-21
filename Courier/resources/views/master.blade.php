<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="UTF-8">
        <title>Serwis obsługi przesyłek</title>
        <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css"/>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
        <!--<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">-->
        <!--<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.dataTables.min.css">-->
        @yield('graphhead')
        @yield('additionalcss')
    </head>
    <body>
        <div class="container">
            <div class="page-header">
                <div class="header-right p-1">
                    <p align="right">
                        @if(Auth::check())
                            Witaj
                            <a href="{{url('users/'.Auth::user()->id)}}">
                                <strong>{{{Auth::user()->name}}}</strong>
                            </a>!
                            {{link_to('logout', 'Wyloguj się')}}
                        @else
                            Jesteś kurierem?&nbsp
                            {{link_to('login', 'Zaloguj się')}}&nbsp&nbsp
                            Nie masz konta?&nbsp
                            {{link_to('register', 'Zarejestruj się')}}
                        @endif
                    </p>
                </div>
                <div class="card text-white bg-primary p-3">
                    <div class="card-heading">
                        @if(Auth::check())
                            @if(Auth::user()->isAdmin())
                                <div class="row">
                                    <div class="col-sm-3">
                                        <a href="{{url('/')}}" class="text-white">
                                            <p align="center">Przeglądaj przesyłki</p>
                                        </a>
                                        <!--&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp-->
                                    </div>
                                    <div class="col-sm-3">
                                        <a href="{{url('parcels/create')}}" class="text-white">
                                            <p align="center">Dodaj nową przesyłkę</p>
                                        </a>
                                    </div>
                                    <div class="col-sm-3">
                                        <!--&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp-->
                                        <a href="{{url('addresses')}}" class="text-white">
                                            <p align="center">Zarządzaj miejscami predefiniowanymi</p>
                                        </a>
                                    </div>
                                    <div class="col-sm-3">
                                        <!--&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp-->
                                        <a href="{{url('users')}}" class="text-white">
                                            <p align="center">Zarządzaj użytkownikami</p>
                                        </a>
                                    </div>
                                </div>
                                <div class="row"></div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <!--&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp-->
                                        <a href="{{url('TSP')}}" class="text-white">
                                            <p align="center">Wyznacz optymalną trasę</p>
                                        </a>
                                    </div>
                                    <div class="col-sm-3">
                                        <!--&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp-->
                                        <a href="{{url('parcels/order/menu')}}" class="text-white">
                                            <p align="center">Wyznacz/zobacz kolejność przesyłek</p>
                                        </a>
                                    </div>
                                    <div class="col-sm-3">
                                        <!--&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp-->
                                        <a href="{{url('mail')}}" class="text-white">
                                            <p align="center">Wyślij wiadomość</p>
                                        </a>
                                    </div>
                                    <div class="col-sm-3">
                                        <a href="{{url('download/apk')}}" class="text-white">
                                            <p align="center">Pobierz aplikację mobilną</p>
                                        </a>
                                    </div>
                                </div>
                            @elseif(Auth::user()->isCourier())
                                <div class="row">
                                    <div class="col-sm-4">
                                        <a href="{{url('/')}}" class="text-white">
                                            <p align="center">Przeglądaj przesyłki</p>
                                        </a>
                                    </div>
                                    <div class="col-sm-4">
                                        <!--&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp-->
                                        <a href="{{url('users')}}" class="text-white">
                                            <p align="center">Dane użytkownika</p>
                                        </a>
                                    </div>
                                    <div class="col-sm-4">
                                        <!--&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp-->
                                        <a href="{{url('download/apk')}}" class="text-white">
                                            <p align="center">Pobierz aplikację mobilną</p>
                                        </a>
                                    </div>
                                </div>
                                <div class="row"></div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <!--&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp-->
                                        <a href="{{url('TSP')}}" class="text-white">
                                            <p align="center">Wyznacz optymalną trasę</p>
                                        </a>
                                    </div>
                                    <div class="col-sm-4">
                                        <!--&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp-->
                                        <a href="{{url('parcels/order/menu')}}" class="text-white">
                                            <p align="center">Wyznacz/zobacz kolejność przesyłek</p>
                                        </a>
                                    </div>
                                    <div class="col-sm-4">
                                        <!--&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp-->
                                        <a href="{{url('mail')}}" class="text-white">
                                            <p align="center">Wyślij wiadomość</p>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @else
                            <a href="{{url('/')}}" class="text-white">
                                <p align="center">Zbadaj przesyłkę</p>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="title p-2">
                    <h2 align="center">
                        @yield('header')
                    </h2>
                </div>
            </div>

            @if(Session::has('message'))
                <div class="alert alert-success">
                    {{Session::get('message')}}
                </div>
            @endif
            @if(Session::has('error'))
                <div class="alert alert-danger">
                    {{Session::get('error')}}
                </div>
            @endif
            @yield('content')
            @yield('graphbody')
        </div>

    <footer>
        <script src="//code.jquery.com/jquery-1.10.2.js"></script>
        <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
        <script>
            $(function() {
                $( "#datepicker" ).datepicker();
            });
        </script>
        <script>
            $(function() {
                $( "#datepicker2" ).datepicker();
            });
        </script>
    </footer>
        <!--<script src="https://code.jquery.com/jquery-3.3.1.js"></script>-->
        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
        <!--<script src="https://cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>-->
        @yield('datatablescripts')
        @yield('additionalscripts')
    </body>
</html>
