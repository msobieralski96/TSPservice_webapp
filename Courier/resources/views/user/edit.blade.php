@extends('master')

@section('header')
    @if($method == 'delete')
        Czy na pewno chcesz usunąć użytkownika {{$user->name}} z systemu?
    @else
        @if(Auth::user()->isAdmin())
            Edytuj dane użytkownika {{$user->name}}
        @else
            Edytuj dane
        @endif
    @endif
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{Form::open(array('method' => $method, 'url' => 'users/'.$user->id))}}
    {{csrf_field()}}
    @unless($method == 'delete')
        <table class="table table-sm table-responsive table-borderless">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @if(Auth::user()->id == $user->id)
                    <tr>
                        <div class="form-group">
                            <th scope="row">{{Form::label('* Adres e-mail:')}}</th>
                            <td>{{Form::text('email', $user->email)}}</td>
                            <td></td>
                            <td></td>
                        </div>
                    </tr>
                    <tr>
                        <div class="form-group">
                            <th scope="row">{{Form::label('  Numer telefonu:')}}</th>
                            <td>{{Form::text('phone_number', $user->phone_number)}}</td>
                            <td></td>
                            <td></td>
                        </div>
                    </tr>
                @endif
                @if(Auth::user()->isAdmin())
                    <tr>
                        <th scope="row">* Rola użytkownika w systemie:</th>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <p align="center">Kurier-admin</p>
                        </th>
                        <td>
                            <p align="center">Administrator</p>
                        </td>
                        <td>
                            <p align="center">Kurier</p>
                        </td>
                        <td>
                            <p align="center">Użytkownik niezatwierdzony</p>
                        </td>
                    </tr>
                    <tr>
                        <div class="form-group">
                            <th scope="row">
                                <p align="center">{{Form::radio('role', 4, $user->isSuperAdmin())}}</p>
                            </th>
                            <td>
                                <p align="center">{{Form::radio('role', 1, ($user->isAdmin() && !($user->isSuperAdmin())))}}</p>
                            </td>
                            <td>
                                <p align="center">{{Form::radio('role', 2, ($user->isCourier() && !($user->isSuperAdmin())))}}</p>
                            </td>
                            <td>
                                <p align="center">{{Form::radio('role', 3, $user->isNotConfirmed())}}</p>
                            </td>
                        </div>
                    </tr>
                @endif
                <tr>
                    <th scope="row"></th>
                    <td>
                        {{Form::submit("Zapisz zmiany", array("class"=>"btn btn-primary"))}}
                    </td>
                    <td>
                        <a href="{{url('users/'.$user->id.'')}}" class="btn btn-secondary">Anuluj</a>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th scope="row">
                        Pola oznaczone gwiazdką [*] są obowiązkowe.
                    </th>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    @else
        {{Form::submit("Tak", array("class"=>"btn btn-danger"))}}
        <a href="{{url('users/'.$user->id.'')}}" class="btn btn-success">Nie</a>
    @endif
    {{Form::close()}}
@stop
