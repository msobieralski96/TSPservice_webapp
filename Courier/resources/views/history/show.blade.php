@extends('master')

@section('header')
    Historia przesyłki
    @if(Auth::check())
        <a href="{{url('parcels/'.$parcel->id)}}">
            {{$parcel->SSCC_number}}
        </a>
    @else
        {{$parcelCode}}
    @endif
@stop

@section('content')
    <div class="history">
        <table class="table table-striped table-hover table-sm table-responsive">
            <thead>
                <tr>
                    <th scope="col">Data aktualizacji</th>
                    <th scope="col">Status przesyłki</th>
                    <th scope="col">Lokalizacja przesyłki</th>
                </tr>
            </thead>
            <tbody>
                @foreach($histories as $key => $history)
                    <tr>
                        <th scope="row">
                            {{{$history->date_of_action}}}
                        </th>
                        <td>
                            {{{$history->state_of_delivery}}}
                        </td>
                        <td>
                            {{{$history->localisation}}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if(Auth::check())
        <a href="{{url('parcels/'.$parcel->id.'')}}" class="btn btn-secondary">Powrót</a>
    @else
        <table class="table table-borderless table-sm table-responsive">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">
                        {{Form::open(array('method' => 'post', 'url' => 'parcel/detail'))}}
                        {{csrf_field()}}
                            {{Form::hidden('parcelCode', $parcelCode)}}
                            {{Form::hidden('courier_phone', $courier_phone)}}
                            {{Form::hidden('courier_email', $courier_email)}}
                            {{Form::submit("Powrót", array("class"=>"btn btn-secondary"))}}
                        {{Form::close()}}
                    </th>
                    <td>
                        <a href="{{url('home/')}}" class="btn btn-light">Wyszukaj inną przesyłkę</a>
                    </td>
                </tr>
            </tbody>
        </table>
    @endif
@stop

