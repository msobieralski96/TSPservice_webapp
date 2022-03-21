@extends('master')

@section('header')
    Wyznaczanie (podgląd) kolejności przesyłek
@stop

@section('content')
    <h5>W celu wyznaczenia/zobaczenia kolejności przesyłek wybierz:</h5>
    <ul>
        <li>lokalizację aktualną</li>
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
    {{Form::open(array('method' => 'post', 'url' => 'parcels/order/menu/next/'.$courier.'/'.$date.'/'))}}
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
                    <th scope="row">{{Form::label('Lokalizacja aktualna:')}}</th>
                    <td>{{Form::select('localization', $localization_options)}}</td>
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

