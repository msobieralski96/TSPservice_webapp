@extends('master')

@section('header')
    Resetowanie hasła
@stop

@section('content')
    <form class="form" method="POST" action="{{ route('password.request') }}">
    {{csrf_field()}}
        <input type="hidden" name="token" value="{{ $token }}">
        <table class="table table-sm table-responsive table-borderless">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <th scope="row">
                            <label for="email" class="control-label">* Adres e-mail:</label>
                        </th>
                        <td>
                            <input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}" required autofocus>
                        </td>
                        <td>
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </td>
                    </div>
                </tr>
                <tr>
                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <th scope="row">
                            <label for="password" class="control-label">* Nowe hasło:</label>
                        </th>
                        <td>
                            <input id="password" type="password" class="form-control" name="password" required>
                        </td>
                        <td>
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </td>
                    </div>
                </tr>
                <tr>
                    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                        <th scope="row">
                            <label for="password-confirm" class="control-label">* Potwierdź hasło:</label>
                        </th>
                        <td>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                        </td>
                        <td>
                            @if ($errors->has('password_confirmation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                            @endif
                        </td>
                    </div>
                </tr>
                <tr>
                    <th scope="row">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                Zresetuj hasło
                            </button>
                        </div>
                    </th>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th scope="row">
                        Pola oznaczone gwiazdką [*] są obowiązkowe.
                    </th>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
    {{Form::close()}}
@stop
