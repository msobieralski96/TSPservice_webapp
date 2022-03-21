@extends('master')

@section('header')
    Przesyłka {{{$parcel->SSCC_number}}}
@stop

@section('content')
    <div class="parcel">
        <table class="table table-hover table-sm table-responsive">
            <thead>
                <tr class="table-primary">
                    <th scope="col">Właściwość</th>
                    <th scope="col">Wartość</th>
                </tr>
            </thead>
            <tbody>
                <tr class="table-secondary">
                    <th colspan="2">DANE PRZESYŁKI:</th>
                </tr>
                <tr>
                    <th scope="row">Numer przesyłki (SSCC)</th>
                    <td>{{{$parcel->SSCC_number}}}</td>
                </tr>
                <tr>
                    <th scope="row">Zawartość przesyłki</th>
                    <td>{{{$parcel->parcel_content}}}</td>
                </tr>
                @if(strlen($parcel->mass)>0)
                <tr>
                    <th scope="row">Masa przesyłki</th>
                    <td>{{{$parcel->mass}}}</td>
                </tr>
                @endif
                @if(strlen($parcel->size)>0)
                <tr>
                    <th scope="row">Rozmiar przesyłki</th>
                    <td>{{{$parcel->size}}}</td>
                </tr>
                @endif
                <tr class="table-secondary">
                    <th colspan="2">DANE ODBIORCY:</th>
                </tr>
                <tr>
                    <th scope="row">Adres dostawy</th>
                    <td>{{{$parcel->address}}}</td>
                </tr>
                @if(Auth::check())
                <tr>
                    <th scope="row">Imię</th>
                    <td>{{{$parcel->client_first_name}}}</td>
                </tr>
                <tr>
                    <th scope="row">Nazwisko</th>
                    <td>{{{$parcel->client_last_name}}}</td>
                </tr>
                    @if(strlen($parcel->client_phone_number)>0)
                    <tr>
                        <th scope="row">Numer telefonu</th>
                        <td>{{{$parcel->client_phone_number}}}</td>
                    </tr>
                    @endif
                    @if(strlen($parcel->client_email)>0)
                    <tr>
                        <th scope="row">e-mail</th>
                        <td>{{{$parcel->client_email}}}</td>
                    </tr>
                    <tr>
                        <th scope="row">Wyślij maila odbiorcy</th>
                        <td>
                            {{Form::open(array('method' => 'get', 'url' => 'mail'))}}
                                {{csrf_field()}}
                                {{Form::hidden('toEmail', $parcel->client_email)}}
                                {{Form::hidden('parcelId', $parcel->SSCC_number)}}
                                {{Form::submit("Napisz", array("class"=>"btn btn-warning"))}}
                            {{Form::close()}}
                        </td>
                    </tr>
                    @endif
                    @if(\File::exists(storage_path('app/signatures/sign_'.$parcel->SSCC_number.'.png')))
                    <tr>
                        <th scope="row">Podpis odbiorcy</th>
                        <td>
                            <a href="https://demotspservice.pl/storage/signatures/<?php echo $parcel->SSCC_number ?>">
                                <img src="https://demotspservice.pl/storage/signatures/<?php echo $parcel->SSCC_number ?>" width="20%" height="20%">
                            </a>
                        </td>
                    </tr>
                    @endif
                @endif
                <tr class="table-secondary">
                    <th colspan="2">DANE NADAWCY:</th>
                </tr>
                <tr>
                    <th scope="row">Adres nadawcy</th>
                    <td>{{{$parcel->sender_address}}}</td>
                </tr>
                @if(Auth::check())
                <tr>
                    <th scope="row">Imię</th>
                    <td>{{{$parcel->sender_first_name}}}</td>
                </tr>
                <tr>
                    <th scope="row">Nazwisko</th>
                    <td>{{{$parcel->sender_last_name}}}</td>
                </tr>
                    @if(strlen($parcel->sender_phone_number)>0)
                    <tr>
                        <th scope="row">Numer telefonu</th>
                        <td>{{{$parcel->sender_phone_number}}}</td>
                    </tr>
                    @endif
                    @if(strlen($parcel->sender_email)>0)
                    <tr>
                        <th scope="row">e-mail</th>
                        <td>{{{$parcel->sender_email}}}</td>
                    </tr>
                    <tr>
                        <th scope="row">Wyślij maila nadawcy</th>
                        <td>
                            {{Form::open(array('method' => 'get', 'url' => 'mail'))}}
                                {{csrf_field()}}
                                {{Form::hidden('toEmail', $parcel->sender_email)}}
                                {{Form::hidden('parcelId', $parcel->SSCC_number)}}
                                {{Form::submit("Napisz", array("class"=>"btn btn-warning"))}}
                            {{Form::close()}}
                        </td>
                    </tr>
                    @endif
                @endif
                <tr class="table-secondary">
                    <th colspan="2">STATUS PRZESYŁKI:</th>
                </tr>
                <tr>
                    <th scope="row">Aktualna lokalizacja</th>
                    <td>{{{$parcel->current_address}}}</td>
                </tr>
                <tr>
                    <th scope="row">Status przesyłki</th>
                    <td>{{{$parcel->state_of_delivery}}}</td>
                </tr>
                <tr>
                    <th scope="row">Szacowany dzień odbioru od nadawcy</th>
                    <td>{{{$parcel->date_of_get_delivery}}}</td>
                </tr>
                <tr>
                    <th scope="row">Szacowany dzień dostawy</th>
                    <td>{{{$parcel->date_of_delivery}}}</td>
                </tr>
                <tr class="table-secondary">
                    <th colspan="2">DANE KURIERA:</th>
                </tr>
                @if(!$clientRequest)
                    <tr>
                        <th scope="row">Kurier</th>
                        <td>
                            @if($parcel->courier_id !== null)
                                @if(Auth::user()->id == $parcel->courier_id
                                    || Auth::user()->isAdmin())
                                    <a href="{{url('users/'.$parcel->courier_id)}}">
                                        {{{$courier->name}}}
                                    </a>
                                @else
                                    {{{$courier->name}}}
                                @endif
                            @else
                               brak
                            @endif
                        </td>
                    </tr>
                @endif
                @if(strlen($courier_phone)>0)
                    <tr>
                        <th scope="row">Numer telefonu</th>
                        <td>{{{$courier_phone}}}</td>
                    </tr>
                @endif
                @if(strlen($courier_email)>0)
                    @if((!Auth::check()) || (Auth::user()->email !== $courier_email))
                    <tr>
                        <th scope="row">Wyślij maila kurierowi</th>
                        <td>
                            {{Form::open(array('method' => 'get', 'url' => 'mail'))}}
                                {{csrf_field()}}
                                {{Form::hidden('toEmail', $courier_email)}}
                                {{Form::hidden('parcelId', $parcel->SSCC_number)}}
                                {{Form::submit("Napisz", array("class"=>"btn btn-warning"))}}
                            {{Form::close()}}
                        </td>
                    </tr>
                    @endif
                @endif
                <tr class="table-secondary">
                    <th colspan="2">INNE:</th>
                </tr>
                @if(Auth::check())
                <tr>
                    <th scope="row">Numer Id w systemie</th>
                    <td>{{{$parcel->id}}}</td>
                </tr>
                @endif
                <tr>
                    <th scope="row">Zarejestrowana w systemie</th>
                    <td>{{{$parcel->created_at}}}</td>
                </tr>
                <tr>
                    <th scope="row">Ostatnia edycja danych</th>
                    <td>{{{$parcel->updated_at}}}</td>
                </tr>
                <tr class="table-secondary">
                    <th colspan="2">AKCJE:</th>
                </tr>
                <tr>
                    <th scope="row">Historia przesyłki</th>
                    <td>
                        @if(Auth::check())
                            <a href="{{url('parcels/'.$parcel->id.'/history')}}" class="btn btn-info">Pokaż</a>
                        @else
                            {{Form::open(array('method' => 'post', 'url' => 'parcel/history'))}}
                                {{csrf_field()}}
                                {{Form::hidden('parcel', $parcel->id/*SSCC_number*/)}}
                                {{Form::hidden('courier_phone', $courier_phone)}}
                                {{Form::submit("Pokaż", array("class"=>"btn btn-info"))}}
                            {{Form::close()}}
                        @endif
                    </td>
                </tr>
                @if(Auth::check() and Auth::user()->canEdit($parcel))
                    <tr>
                        <th scope="row">Etykieta logistyczna</th>
                        <td>
                            <a href="{{url('parcels/'.$parcel->id.'/generate_label')}}" class="btn btn-primary">
                                Generuj
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Edycja przesyłki</th>
                        <td>
                            <a href="{{url('parcels/'.$parcel->id.'/edit')}}" class="btn btn-secondary">
                                <span class="glyphicon glyphicon-edit"></span>Edycja
                            </a>
                        </td>
                    </tr>
                @endif
                @if(Auth::check() and Auth::user()->isAdmin())
                    <tr>
                        <th scope="row">Usuń przesyłkę</th>
                        <td>
                            <a href="{{url('parcels/'.$parcel->id.'/remove')}}" class="btn btn-danger">
                                <span class="glyphicon glyphicon-trash"></span>Usuń
                            </a>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    @if(!Auth::check())
        <a href="{{url('home/')}}" class="btn btn-light">Wyszukaj inną przesyłkę</a>
    @endif
@stop
