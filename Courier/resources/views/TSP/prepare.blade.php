@extends('masterwithscripts')

@section('header')
    Wyznaczanie optymalnej trasy</h2>
    <p>Powered by: Service © openrouteservice.org | Map data © OpenStreetMap contributors</p><h2>
@stop

@section('additionalcss')
    <style>
        input.filters {
            width: 100%;
        }
    </style>
@stop

@section('content')
    <h5>W celu wyznaczenia trasy:</h5>
    <ul>
        <li>Zanzacz wybrane adresy w kolumnie "Wybierz".</li>
        <li>Wyznacz pierwszy/ostatni punkt w kolumnie "Pierwszy".</li>
        <li>W celu łatwiejszego odnalezienia przesyłki możesz skorzystać z filtrów wyszukiwania (drugi rząd tabeli).</li>
        <li>Zalecane filtry: wspólna lokalizacja aktualna, data, kurier.</li>
        <li>Po zaznaczeniu wszystkich adresów <b>nie zapomnij usunąć filtrów wyszukiwania</b> w celu poprawnego działania programu.</li>
        <li>Poniżej tabeli wybierz rodzaj odległości między dwoma punktami (linie proste/z uwzględnieniem dróg)</li>
        <li>oraz kryterium wyszukiwania, <i>jeśli wybrano odległości z uwzględnieniem dróg</i> (względem dystansu/czasu).</li>
        <li>Zaznacz przycisk "Znajdź optymalną trasę" i poczekaj od kilkunastu sekund do kilku minut na wyniki.</li>
    </ul>
    <div class="parcel">
        {{Form::open()}}
        {{csrf_field()}}
        <table id="tsptable" class="table table-striped table-hover table-sm table-responsive" width="100%">
            <thead>
                <tr>
                    <th scope="col">Numer przesyłki (SSCC)</th>
                    <th scope="col">Typ adresu</th>
                    <th scope="col">Adres</th>
                    <th scope="col">Lokalizacja aktualna</th>
                    <th scope="col">Szacowana data dostawy/odbioru</th>
                    <th scope="col">Status przesyłki</th>
                    <th scope="col">Kurier</th>
                    <td scope="col">Wybierz</td>
                    <td scope="col">Pierwszy</td>
                </tr>
            </thead>
            <tbody>
                @foreach($places as $key => $place)
                    <tr
                        @if($key%2 == 0)
                            class="table-info">
                        @else
                            class="table-primary">
                        @endif
                        <td scope="row"></td>
                        <td>#{{{$place->name}}}</td>
                        <td>{{{$place->address}}}</td>
                        <td>{{{$place->address}}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            {{Form::checkbox('addresses[]', $place->id."|Miejsce predefiniowane", false)}}
                        </td>
                        <td>
                            {{Form::radio('first', $place->id."|Miejsce predefiniowane", false)}}
                        </td>
                    </tr>
                @endforeach
                @foreach($parcels as $key => $parcel)
                    <!-- adres docelowy -->
                    <tr>
                        <td scope="row"><a href="{{url('parcels/'.$parcel->id)}}">
                            <strong>{{{$parcel->SSCC_number}}}</strong></a>
                        </td>
                        <td>Adres docelowy</td>
                        <td>{{{$parcel->address}}}</td>
                        <td>{{{$parcel->current_address}}}</td>
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
                        <td>
                            {{Form::checkbox('addresses[]', $parcel->id."|Adres docelowy", false)}}
                        </td>
                        <td>
                            {{Form::radio('first', $parcel->id."|Adres docelowy", false)}}
                        </td>
                    </tr>
                    <!-- adres nadawcy -->
                    <tr>
                        <td scope="row"><a href="{{url('parcels/'.$parcel->id)}}">
                            <strong>{{{$parcel->SSCC_number}}}</strong></a>
                        </td>
                        <td>Adres nadawcy</td>
                        <td>{{{$parcel->sender_address}}}</td>
                        <td>{{{$parcel->current_address}}}</td>
                        <td>{{{$parcel->date_of_get_delivery}}}</td>
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
                        <td>
                            {{Form::checkbox('addresses[]', $parcel->id."|Adres nadawcy", false)}}
                        </td>
                        <td>
                            {{Form::radio('first', $parcel->id."|Adres nadawcy", false)}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <table id="noname" class="table table-sm table-responsive table-borderless"
            <thead>
                <tr>
                    <th colspan="2" scope="col">Opcje wyszukiwania:</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Odległości między dwoma punktami:</td>
                </tr>
                <tr>
                    <td>
                        <p align="center">{{Form::radio('edges', "linear", false)}} Odległości w linii prostej (szybciej; 3-60 adresów)</p>
                    </td>
                    <td>
                        <p align="center">{{Form::radio('edges', "real", true)}} Odległości na podstawie dróg (dokładniej; 3-12 adresów)</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">Kryterium wyszukiwania (ważne tylko przy wyborze odległości na podstawie dróg):</td>
                </tr>
                <tr>
                    <td>
                        <p align="center">{{Form::radio('option', "distance", true)}} Znajdź trasę w oparciu o dystans [km]</p>
                    </td>
                    <td>
                        <p align="center">{{Form::radio('option', "duration", false)}} Znajdź trasę w oparciu o czas [min]</p>
                    </td>
                </tr>
            </tbody>
        </table>
        {{Form::submit("Znajdź optymalną trasę", array("class"=>"btn btn-primary"))}}
        Wyznaczanie trasy może potrwać do kilku minut.
        {{Form::close()}}
    </div>
@stop
