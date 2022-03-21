@extends('master')

@section('header')
    Użytkownik {{{$info->name}}}
@stop

@section('content')
    <div class="user">
        <table class="table table-hover table-sm table-responsive">
            <thead>
                <tr>
                    <th scope="col">Właściwość</th>
                    <th scope="col">Wartość</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Id użytkownika</th>
                    <td>{{{$info->id}}}</td>
                <tr>
                <tr>
                    <th scope="row">Nazwa użytkownika</th>
                    <td>{{{$info->name}}}</td>
                <tr>
                <tr>
                    <th scope="row">Adres e-mail</th>
                    <td>{{{$info->email}}}</td>
                <tr>
                @if(strlen($info->phone_number)>0)
                    <tr>
                        <th scope="row">Numer telefonu</th>
                        <td>{{{$info->phone_number}}}</td>
                    <tr>
                @endif
                <tr>
                    <th scope="row">Rola użytkownika w systemie</th>
                    <td>
                        @if ($info->getRole() == 1)
                            admin
                        @elseif ($info->getRole() == 2)
                            kurier
                        @elseif ($info->getRole() == 3)
                            użytkownik niezatwierdzony
                        @elseif ($info->getRole() == 4)
                            kurier-admin
                        @else
                           ???
                        @endif
                    </td>
                <tr>
                @if(Auth::user()->email !== $info->email)
                    <tr>
                        <th scope="row">Wyślij maila</th>
                        <td>
                            {{Form::open(array('method' => 'get', 'url' => 'mail'))}}
                            {{csrf_field()}}
                                {{Form::hidden('toEmail', $info->email)}}
                                {{Form::hidden('parcelId', '')}}
                                {{Form::submit("Napisz", array("class"=>"btn btn-warning"))}}
                            {{Form::close()}}
                        </td>
                    </tr>
                @endif
                @if(Auth::user()->id == $info->id)
                    <tr>
                        <th scope="row">Edycja</th>
                        <td>
                            <a href="{{url('users/'.$info->id.'/edit')}}" class="btn btn-primary">Edytuj dane</a>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Zmień hasło</th>
                        <td>
                            <a href="{{url('users/'.$info->id.'/edit/password')}}" class="btn btn-secondary">Zmień</a>
                        </td>
                    </tr>
                @elseif(Auth::user()->isAdmin())
                    <tr>
                        <th scope="row">Edycja</th>
                        <td>
                            <a href="{{url('users/'.$info->id.'/edit')}}" class="btn btn-primary">Zmień rolę</a>
                        </td>
                    </tr>
                @endif
                @if(Auth::user()->isAdmin())
                    <tr>
                        <th scope="row">Usuń</th>
                        <td>
                            <a href="{{url('users/'.$info->id.'/remove')}}" class="btn btn-danger">Usuń użytkownika</a>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
@stop

