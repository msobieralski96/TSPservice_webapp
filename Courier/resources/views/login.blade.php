@extends('master')

@section('header')
    Zaloguj się
@stop

@section('content')
    {{Form::open()}}
    {{csrf_field()}}
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
                        <th scope="row">{{Form::label('Nazwa użytkownika:')}}</th>
                        <td>{{Form::text('name')}}</td>
                    </div>
                </tr>
                <tr>
                    <div class="form-group">
                        <th scope="row">{{Form::label('Hasło:')}}</th>
                        <td>{{Form::password('password')}}</td>
                    </div>
                </tr>
                <tr>
                    <th scope="row">
                        {{Form::submit("Zaloguj się", array("class"=>"btn btn-default"))}}
                    </th>
                    <td></td>
                </tr>
                <tr>
                    <th scope="row">
                        <a href='password/reset'>Nie pamiętasz hasła?</a>
                    </th>
                    <td></td>
                </tr>
            </tbody>
    {{Form::close()}}
@stop
