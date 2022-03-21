@extends('masterwithscripts')

@section('header')
    Miejsca predefiniowane
@stop

@section('content')
    <div class="parcel">
        <table class="table table-hover table-sm table-responsive">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nazwa</th>
                    <th scope="col">Adres</th>
                    <th scope="col">Opcje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($addresses as $address)
                    <tr>
                        <td scope="row">{{{$address->id}}}</td>
                        <td>{{{$address->name}}}</td>
                        <td>{{{$address->address}}}</td>
                        <td>
                            <a href="{{url('addresses/'.$address->id.'/edit')}}" class="btn btn-secondary">
                                Edycja
                            </a>
                            <a href="{{url('addresses/'.$address->id.'/remove')}}" class="btn btn-danger">
                                Usuń
                            </a>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td scope="row"></td>
                    <td>Dodaj nowe miejsce:</td>
                    <td></td>
                    <td>
                        <a href="{{url('addresses/create')}}" class="btn btn-primary">
                            Dodaj
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@stop

