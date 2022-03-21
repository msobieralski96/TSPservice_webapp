@extends('master')

@section('header')
    Zmień hasło
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
    @if(Auth::user()->id == $user->id)
        {{Form::open(array('method' => $method, 'url' => 'users/'.$user->id.'/password'))}}
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
                        <th scope="row">{{Form::label('* Aktualne hasło:')}}</th>
                        <td>{{Form::password('currentpassword')}}</td>
                    </div>
                </tr>
                <tr>
                    <div class="form-group">
                        <th scope="row">{{Form::label('* Hasło:')}}</th>
                        <td>{{Form::password('newpassword')}}</td>
                    </div>
                </tr>
                <tr>
                    <div class="form-group">
                        <th scope="row">{{Form::label('* Powtórz hasło:')}}</th>
                        <td>{{Form::password('newpassword_confirmation')}}</td>
                    </div>
                </tr>
                <tr>
                    <th scope="row">
                        {{Form::submit("Zmień hasło", array("class"=>"btn btn-primary"))}}
                    </th>
                    <td>
                        <a href="{{url('users/'.$user->id.'')}}" class="btn btn-secondary">Anuluj</a>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        Pola oznaczone gwiazdką [*] są obowiązkowe.
                    </th>
                    <td></td>
                </tr>
            </tbody>
        </table>
        {{Form::close()}}
    @endif
@stop

