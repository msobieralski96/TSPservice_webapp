@extends('master')

@section('header')
    @if($method == 'post')
        Dodaj nowe konto użytkownika
    @elseif($method == 'delete')
        Usuń konto użytkownika <strong>{{$user->name}}</strong>
    @else
        Edytuj dane konta użytkownika przesyłki o id: <strong>{{$user->name}}</strong>
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
    {{Form::open()}}
    {{csrf_field()}}
    @unless($method == 'delete')
    <table class="table table-sm table-responsive table-borderless">
        <thead>
            <tr>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <div class="form-group">
                    <th scope="row">{{Form::label('* Nazwa użytkownika:')}}</th>
                    <td>{{Form::text('name')}}</td>
                </div>
            </tr>
            <tr>
                <div class="form-group">
                    <th scope="row">{{Form::label('* Adres e-mail:')}}</th>
                    <td>{{Form::text('email')}}</td>
                </div>
            </tr>
            <tr>
                <div class="form-group">
                    <th scope="row">{{Form::label('  Numer telefonu:')}}</th>
                    <td>{{Form::text('phone_number')}}</td>
                </div>
            </tr>
            <tr>
                <div class="form-group">
                    <th scope="row">{{Form::label('* Hasło:')}}</th>
                    <td>{{Form::password('password')}}</td>
                </div>
            </tr>
            <tr>
                <div class="form-group">
                    <th scope="row">{{Form::label('* Powtórz hasło:')}}</th>
                    <td>{{Form::password('password_confirmation')}}</td>
                </div>
            </tr>
            <tr>
                <th scope="row">
                    {{Form::submit("Utwórz konto", array("class"=>"btn btn-default"))}}
                </th>
                <td></td>
            </tr>
            <tr>
                <th scope="row">
                    Pola oznaczone gwiazdką [*] są obowiązkowe.
                </th>
                <td></td>
            </tr>
        </tbody>
    </table>
    @else
        {{Form::submit("Usuń", array("class"=>"btn btn-default"))}}
    @endif
    {{Form::close()}}
@stop
