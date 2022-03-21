@extends('master')

@section('header')
    Odzyskiwanie konta
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <p>Na podany adres e-mail prześlemy link, za pomocą którego można będzie zresetować hasło do konta</p>
    <form class="form" method="POST" action="{{ route('password.email') }}">
    {{csrf_field()}}
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
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
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
                    <th scope="row">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                Wyślij link
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
