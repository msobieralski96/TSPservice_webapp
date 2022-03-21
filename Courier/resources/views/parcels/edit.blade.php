@extends('master')

@section('header')
    @if($method == 'post')
        Dodaj nową przesyłkę
    @elseif($method == 'delete')
        Czy na pewno chcesz usunąć przesyłkę {{$parcel->SSCC_number}} z bazy danych?
    @else
        Edytuj właściwości przesyłki {{$parcel->SSCC_number}}
    @endif
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{Form::open(array('method' => $method, 'url' => 'parcels/'.$parcel->id))}}
    {{csrf_field()}}
    @unless($method == 'delete')
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
                        <th colspan="2">DANE PRZESYŁKI:</th>
                    </tr>
                    <tr>
                        <div class="form-group">
                            {{Form::hidden('SSCC_number', ($parcel->SSCC_number == null ? "NOWY SSCC" : $parcel->SSCC_number))}}
                        </div>
                    </tr>
                    <tr>
                        <div class="form-group">
                            <th scope="row">{{Form::label('* Zawartość przesyłki:')}}</th>
                            <td>{{Form::text('parcel_content', $parcel->parcel_content)}}</td>
                        </div>
                    </tr>
                    <tr>
                        <div class="form-group">
                            <th scope="row">{{Form::label('  Masa przesyłki:')}}</th>
                            <td>{{Form::text('mass', $parcel->mass)}}</td>
                        </div>
                    </tr>
                    <tr>
                        <div class="form-group">
                            <th scope="row">{{Form::label('  Rozmiar przesyłki:')}}</th>
                            <td>{{Form::text('size', $parcel->size)}}</td>
                        </div>
                    </tr>
                    <tr>
                        <th colspan="2">DANE ODBIORCY:</th>
                    </tr>
                    <tr>
                        <div class="form-group">
                            <th scope="row">{{Form::label('* Adres dostawy:')}}</th>
                            <td>{{Form::text('address', $parcel->address)}}</td>
                        </div>
                    </tr>
                    <tr>
                        <div class="form-group">
                            <th scope="row">{{Form::label('* Imię:')}}</th>
                            <td>{{Form::text('client_first_name', $parcel->client_first_name)}}</td>
                        </div>
                    </tr>
                    <tr>
                        <div class="form-group">
                            <th scope="row">{{Form::label('* Nazwisko:')}}</th>
                            <td>{{Form::text('client_last_name', $parcel->client_last_name)}}</td>
                        </div>
                    </tr>
                    <tr>
                        <div class="form-group">
                            <th scope="row">{{Form::label('  Numer telefonu:')}}</th>
                            <td>{{Form::text('client_phone_number', $parcel->client_phone_number)}}</td>
                        </div>
                    </tr>
                    <tr>
                        <div class="form-group">
                            <th scope="row">{{Form::label('  e-mail:')}}</th>
                            <td>{{Form::text('client_email', $parcel->client_email)}}</td>
                        </div>
                    </tr>
                    <tr>
                        <th colspan="2">DANE NADAWCY:</th>
                    </tr>
                    <tr>
                        <div class="form-group">
                            <th scope="row">{{Form::label('* Adres nadawcy:')}}</th>
                            <td>{{Form::text('sender_address', $parcel->sender_address)}}</td>
                        </div>
                    </tr>
                    <tr>
                        <div class="form-group">
                            <th scope="row">{{Form::label('* Imię:')}}</th>
                            <td>{{Form::text('sender_first_name', $parcel->sender_first_name)}}</td>
                        </div>
                    </tr>
                    <tr>
                        <div class="form-group">
                            <th scope="row">{{Form::label('* Nazwisko:')}}</th>
                            <td>{{Form::text('sender_last_name', $parcel->sender_last_name)}}</td>
                        </div>
                    </tr>
                    <tr>
                        <div class="form-group">
                            <th scope="row">{{Form::label('  Numer telefonu:')}}</th>
                            <td>{{Form::text('sender_phone_number', $parcel->sender_phone_number)}}</td>
                        </div>
                    </tr>
                    <tr>
                        <div class="form-group">
                            <th scope="row">{{Form::label('  e-mail:')}}</th>
                            <td>{{Form::text('sender_email', $parcel->sender_email)}}</td>
                        </div>
                    </tr>
                @endif
                <tr>
                   <th colspan="2">STATUS PRZESYŁKI:</th>
                </tr>
                <tr>
                    <div class="form-group">
                        <th scope="row">{{Form::label('* Aktualna lokalizacja:')}}</th>
                        <td>{{Form::text('current_address', $parcel->current_address)}}</td>
                    </div>
                </tr>
                <tr>
                    <div class="form-group">
                        <th scope="row">{{Form::label('* Status przesyłki:')}}</th>
                        <td>
                            {{Form::select('state_of_delivery', $statuses, $customParcelStatus ? "Inny" : $parcel->state_of_delivery,
                                array('id' => 'state_of_delivery'))}}
                        </td>
                    </div>
                </tr>
                <tr id="parcel_state">
                    <div class="form-group">
                        <th scope="row"></th>
                        <td>{{Form::text('parcel_status', $customParcelStatus ? $parcel->state_of_delivery : "")}}</td>
                    </div>
                </tr>
                <tr>
                    <div class="form-group">
                        <th scope="row">{{Form::label('* Szacowany dzień odbioru od nadawcy:')}}</th>
                        <td>
                            {{Form::text('date_of_get_delivery',
                                $parcel->date_of_get_delivery,
                                array('id' => 'datepicker2'))}}
                        </td>
                    </div>
                </tr>
                <tr>
                    <div class="form-group">
                        <th scope="row">{{Form::label('* Szacowany dzień dostawy:')}}</th>
                        <td>
                            {{Form::text('date_of_delivery',
                                $parcel->date_of_delivery,
                                array('id' => 'datepicker'))}}
                        </td>
                    </div>
                </tr>
                @if(Auth::user()->isAdmin())
                    <tr>
                       <th colspan="2">DANE KURIERA:</th>
                    </tr>
                    <tr>
                        <div class="form-group">
                            <th scope="row">{{Form::label('  Kurier:')}}</th>
                            <td>{{Form::select('courier_id', $courier_options, $parcel->courier_id)}}</td>
                        </div>
                    </tr>
                @endif
                <tr>
                   <th colspan="2">KOLEJNOŚĆ:</th>
                </tr>
                <tr>
                    <div class="form-group">
                        <th scope="row">{{Form::label('Kolejność dla odbioru od nadawcy:')}}</th>
                        <td>{{Form::text('get_order', $parcel->get_order)}}</td>
                    </div>
                </tr>
                <tr>
                    <div class="form-group">
                        <th scope="row">{{Form::label('Kolejność dla dostarczenia przesyłki:')}}</th>
                        <td>{{Form::text('deliver_order', $parcel->deliver_order)}}</td>
                    </div>
                </tr>
                <tr>
                   <th colspan="2">PODSUMOWANIE:</th>
                </tr>
                <tr>
                    <th scope="row">
                        {{Form::submit("Zapisz zmiany", array("class"=>"btn btn-primary"))}}
                    </th>
                    <td><a href="{{url('parcels/'.$parcel->id.'')}}" class="btn btn-secondary">Anuluj</a></td>
                </tr>
                <tr>
                    <th scope="row">
                        Pola oznaczone gwiazdką [*] są obowiązkowe.
                    </th>
                    <td></td>
                </tr>
            </tbody>
        </table>
    @else
        {{Form::submit("Tak", array("class"=>"btn btn-danger"))}}
        <a href="{{url('parcels/'.$parcel->id.'')}}" class="btn btn-success">Nie</a>
    @endif

    {{Form::close()}}
@stop

@section('additionalscripts')
    <script>
        $(function(){
            if ($('#state_of_delivery').val() == "Inny"){
                $('#parcel_state').show();
            } else {
                $('#parcel_state').hide();
            }

            $('#state_of_delivery').on('change', function() {
                var v = this.value == "Inny" ? 'show' : 'hide';
                $.fn[v].call($('#parcel_state'));
            });
        });
    </script>
@stop

