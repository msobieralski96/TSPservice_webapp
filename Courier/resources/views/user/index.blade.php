@extends('masterwithscripts')

@section('header')
    Zarządzaj użytkownikami
@stop

@section('additionalcss')
    <style>
        input.filters {
            width: 100%;
        }
    </style>
@stop

@section('content')
    <div class="user">
        <table id="userstable" class="table table-striped table-hover table-sm table-responsive" width="100%">
            <thead>
                <tr>
                    <th scope="col">Nazwa użytkownika</th>
                    <th scope="col">Rola użytkownika w systemie</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $key => $user)
                <tr>
                    <td scope="row">
                        <a href="{{url('users/'.$user->id)}}">
                            {{{$user->name}}}</a>
                    </td>
                    <td>
                        @if ($user->getRole() == 1)
                            admin
                        @elseif ($user->getRole() == 2)
                            kurier
                        @elseif ($user->getRole() == 3)
                            użytkownik niezatwierdzony
                        @elseif ($user->getRole() == 4)
                            kurier-admin
                        @else
                           ???
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@stop
