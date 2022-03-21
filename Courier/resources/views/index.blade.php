@extends('master')

@section('header')
    Witaj na stronie głównej serwisu obsługi przesyłek kurierskich.
@stop

@section('content')
    <div class="greeting">
        {{Form::open(array('method' => 'post', 'url' => 'parcel/detail'))}}
        {{csrf_field()}}
        <table id="clienttable" class="table table-sm table-responsive table-borderless" width="100%">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <div class="form-group">
                        <th scope="row">
                            {{Form::label('Numer przesyłki:')}}
                        </th>
                        <td>
                            {{Form::text('parcelCode')}}
                        </td>
                    </div>
                </tr>
                <tr>
                    <th scope="row">
                        {{Form::submit("Sprawdź", array("class"=>"btn btn-primary"))}}
                    </th>
                    <td></td>
                </tr>
            </tbody>
        </table>
        {{Form::close()}}
    </div>
@stop

