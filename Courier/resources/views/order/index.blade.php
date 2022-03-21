@extends('masterwithscripts')

@section('header')
    Wyznaczanie (podgląd) kolejności przesyłek
@stop

@section('additionalcss')
    <style>
        input.filters {
            width: 100%;
        }
        <!--input.ordercol {
            width: 3vw;
        }-->
    </style>
@stop

@section('additionalscripts')
<!--drag & drop table rows-->
    <script type="text/javascript">
        $('tbody').sortable();
    </script>
@stop

@section('content')
    <p><b>Kurier:</b> {{$courier->name}}</p>
    <p><b>Data:</b> {{$date}}</p>
    <p><b>Lokalizacja aktualna:</b> {{$localization}}</p>
    <h5>W celu zmiany kolejności przesyłek:</h5>
    <ul>
        <li><b>Przeciągnij i upuść wiersz tabeli, żeby zmienić kolejność.</b></li>
        <li>W celu łatwiejszego odnalezienia przesyłki możesz skorzystać z filtrów wyszukiwania (drugi rząd tabeli).</li>
        <li>Po uporządkowaniu wszystkich przesyłek <b>nie zapomnij usunąć filtrów wyszukiwania</b> w celu poprawnego działania programu.</li>
        <li><b>Zaznacz przycisk "Zmień kolejność"</b> poniżej tabeli, <b>w celu zatwierdzenia zmian</b>.</li>
    </ul>
    <div class="parcel">
        {{Form::open()}}
        {{csrf_field()}}
        <table id="ordertable" class="table table-striped table-hover table-sm table-responsive" width="100%">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Numer przesyłki</th>
                    <th scope="col">Typ adresu</th>
                    <th scope="col">Adres</th>
                    <th scope="col">Data</th>
                    <th scope="col">Status przesyłki</th>
                    <th scope="col">Kurier</th>
                </tr>
            </thead>
            <tbody>
                @foreach($parcels as $parcel)
                    @if(strpos($parcel->date_of_delivery, $date) !== false)
                    <!-- adres docelowy -->
                    <tr>
                        <td scope="row">
                            {{{$parcel->deliver_order}}}
                            {{Form::hidden('parcel[]', $parcel)}}
                            {{Form::hidden('address_type[]', "Adres docelowy")}}
                        </td>
                        <td>
                            <a href="{{url('parcels/'.$parcel->id)}}">
                                <strong>{{{$parcel->SSCC_number}}}</strong></a>
                        </td>
                        <td>Adres docelowy</td>
                        <td>{{{$parcel->address}}}</td>
                        <td>{{{$parcel->date_of_delivery}}}</td>
                        <td>{{{$parcel->state_of_delivery}}}</td>
                        <td>
                            {{{$courier->name}}}
                        </td>
                    </tr>
                    @endif
                    @if(strpos($parcel->date_of_get_delivery, $date) !== false)
                    <!-- adres nadawcy -->
                    <tr>
                        <td scope="row">
                            {{{$parcel->get_order}}}
                            {{Form::hidden('parcel[]', $parcel)}}
                            {{Form::hidden('address_type[]', "Adres nadawcy")}}
                        </td>
                        <td>
                            <a href="{{url('parcels/'.$parcel->id)}}">
                                <strong>{{{$parcel->SSCC_number}}}</strong></a>
                        </td>
                        <td>Adres nadawcy</td>
                        <td>{{{$parcel->sender_address}}}</td>
                        <td>{{{$parcel->date_of_get_delivery}}}</td>
                        <td>{{{$parcel->state_of_delivery}}}</td>
                        <td>
                            {{{$courier->name}}}
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <table id="noname" class="table table-sm table-responsive table-borderless"
            <thead>
                <tr>
                    <th>
                        @if(count($parcels)>0)
                            {{Form::submit("Zmień kolejność", array("class"=>"btn btn-primary"))}}
                        @endif
                    </th>
                    <th>
                        <a href="{{url('parcels/')}}" class="btn btn-secondary">Anuluj</a>
                    </th>
                </tr>
            </thead>
        </table>
        {{Form::close()}}
    </div>
@stop
