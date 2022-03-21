@extends('master')

@section('header')
    Wyślij wiadomość
@stop

@section('additionalcss')
    <style>
        input.mailinput {
            width: 100%;
        }
    </style>
@stop

@section('content')
    <div class="email">
        {{Form::open(array('method' => 'post', 'url' => 'mail'))}}
        {{csrf_field()}}
        <table id="mailtable" class="table table-sm table-responsive table-borderless" width="100%">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @if(Auth::check())
                    <div class="form-group">
                        {{Form::hidden('isClient', false)}}
                    </div>
                    <tr>
                        <div class="form-group">
                            <th scope="row">
                                {{Form::label('Email odbiorcy:')}}
                            </th>
                            <td>
                                {{Form::text('toEmail', $toEmail, array('class' => 'mailinput'))}}
                            </td>
                        </div>
                    </tr>
                    <tr>
                        <div class="form-group">
                            <th scope="row">
                                {{Form::label('Temat:')}}
                            </th>
                            <td>
                                @if(strlen($parcelId) > 0)
                                    {{Form::text('subject', "Przesyłka ".$parcelId, array('class' => 'mailinput'))}}
                                @else
                                    {{Form::text('subject', "", array('class' => 'mailinput'))}}
                                @endif
                            </td>
                        </div>
                    </tr>
                @else
                    <div class="form-group">
                        {{Form::hidden('isClient', true)}}
                    </div>
                    <div class="form-group">
                        {{Form::hidden('toEmail', $toEmail)}}
                    </div>
                    <tr>
                        <div class="form-group">
                            <th scope="row">
                                {{Form::label('Temat:')}}
                            </th>
                            <td>
                                {{Form::text('subject', "Przesyłka ".$parcelId, array('class' => 'mailinput'))}}
                            </td>
                        </div>
                    </tr>
                @endif
                <tr>
                    <div class="form-group">
                        <th scope="row">
                            {{Form::label('Treść:')}}
                        </th>
                        <td>
                            {{Form::textarea('body', "", ['rows' => 10, 'cols' => 100])}}
                        </td>
                    </div>
                </tr>
                <tr>
                    <th scope="row">
                        {{Form::submit("Wyślij", array("class"=>"btn btn-primary"))}}
                    </th>
                    <td></td>
                </tr>
            </tbody>
        </table>
        {{Form::close()}}
    </div>
@stop
