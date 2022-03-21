@extends('master')

@section('header')
    @if($method == 'post')
        Dodaj nowe miejsce predefiniowane
    @elseif($method == 'delete')
        Czy na pewno chcesz usunąć miejsce predefiniowane: {{$address->name}}:{{$address->address}} z bazy danych?
    @else
        Edytuj właściwości miejsca predefiniowanego {{$address->name}}:{{$address->address}}
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
    @if(Auth::user()->isAdmin())
        {{Form::open(array('method' => $method, 'url' => 'addresses/'.$address->id))}}
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
                    <tr>
                        <div class="form-group">
                            <th scope="row">{{Form::label('* Nazwa:')}}</th>
                            <td>{{Form::text('name', $address->name)}}</td>
                        </div>
                     </tr>
                     <tr>
                        <div class="form-group">
                            <th scope="row">{{Form::label('* Adres:')}}</th>
                            <td>{{Form::text('address', $address->address)}}</td>
                        </div>
                    </tr>
                    <tr>
                        <th scope="row">
                            {{Form::submit("Zapisz zmiany", array("class"=>"btn btn-primary"))}}
                        </th>
                        <td><a href="{{url('addresses/')}}" class="btn btn-secondary">Anuluj</a></td>
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
            <a href="{{url('addresses/')}}" class="btn btn-success">Nie</a>
        @endif

        {{Form::close()}}
    @endif
@stop
