@extends('masterwithscripts')

@section('header')
    Wszystkie przesyłki
@stop

@section('additionalcss')
    <style>
        input.filters {
            width: 100%;
        }
    </style>
@stop

@section('content')
    <h5>W celu wyświetlenia przesyłek do rozwiezienia w danym dniu przez kuriera, zobacz
    <a href="{{url('parcels/order/menu')}}">
        <strong>tutaj</strong>
    </a>.</h5>
    <div class="parcel">
        <table id="parcelstable" class="table table-striped table-hover table-sm table-responsive" width="100%">
            <thead>
                <tr>
                    <th scope="col">Numer przesyłki (SSCC)</th>
                    <th scope="col">Adres docelowy</th>
                    <th scope="col">Szacowana data dostawy</th>
                    <th scope="col">Status przesyłki</th>
                    <th scope="col">Kurier</th>
                </tr>
            </thead>
            <tbody>
                @foreach($parcels as $key => $parcel)
                    <tr>
                        <td scope="row"><a href="{{url('parcels/'.$parcel->id)}}">
                            <strong>{{{$parcel->SSCC_number}}}</strong></a>
                        </td>
                        <td>{{{$parcel->address}}}</td>
                        <td>{{{$parcel->date_of_delivery}}}</td>
                        <td>{{{$parcel->state_of_delivery}}}</td>
                        <td>
                            @if($parcel->courier_id !== null)
                                @if(Auth::user()->id == $parcel->courier_id
                                    || Auth::user()->isAdmin())
                                    <a href="{{url('users/'.$parcel->courier_id)}}">
                                        {{{$couriers[$key]->name}}}</a>
                                @else
                                    {{{$couriers[$key]->name}}}
                                @endif
                            @else
                                brak
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@stop

