@extends('master')

@section('header')
    Wyznaczanie (podgląd) kolejności przesyłek
@stop

@section('content')
    <h5>W celu wyznaczenia/zobaczenia kolejności przesyłek wybierz:</h5>
    <ul>
        @if(Auth::user()->isAdmin())
            <li>kuriera</li>
        @endif
        <li>datę</li>
    </ul>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{Form::open(array('method' => 'post', 'url' => 'parcels/order/menu'))}}
    {{csrf_field()}}
        <table class="table table-sm table-responsive table-borderless">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @if(Auth::user()->isAdmin())
                    <tr>
                        <th scope="row">{{Form::label('Kurier:')}}</th>
                        <td>{{Form::select('courier', $courier_options)}}</td>
                    </tr>
                @else
                    {{Form::hidden('courier', Auth::user()->id)}}
                @endif
                <tr>
                    <th scope="row">{{Form::label('Data (MM/dd/yyyy):')}}</th>
                    <td>
                        {{Form::text('date', $date,
                            array('id' => 'datepicker'))}}
                    </td>
                </tr>
                <tr>
                    <th scope="row"></th>
                    <td>
                        {{Form::submit("Wybierz", array("class"=>"btn btn-primary"))}}
                    </td>
                    <td>
                        <a href="{{url('parcels')}}" class="btn btn-secondary">Anuluj</a>
                    </td>
                </tr>
            </tbody>
        </table>
    {{Form::close()}}
@stop
