<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Parcel;
use Auth;
use Redirect;
use View;
use Image;
require 'BarcodeController.php';

class PDFController extends Controller
{
    public function generatePDF(Parcel $parcel)
    {
        if(Auth::check()){
            if(Auth::user()->canEdit($parcel)){
                $SSCC_number = $parcel->SSCC_number;
                $client_name = $parcel->client_first_name." ".$parcel->client_last_name;
                $client_phone_number = $parcel->client_phone_number;
                $client_address = $parcel->address;
                $sender_name = $parcel->sender_first_name." ".$parcel->sender_last_name;
                $sender_phone_number = $parcel->sender_phone_number;
                $sender_address = $parcel->sender_address;
                $supplier_name = "TSP Service";
                $supplier_phone_number = "777777777";
                $supplier_address = "Przyczółkowa 107, 02-968, Bartyki, Warszawa";
                $size = $parcel->size;
                $mass = $parcel->mass;
                $parcel_content = $parcel->parcel_content;

                $data = ['SSCC_number' => $SSCC_number,
                         'client_name' => $client_name,
                         'client_phone_number' => $client_phone_number,
                         'client_address' => $client_address,
                         'sender_name' => $sender_name,
                         'sender_phone_number' => $sender_phone_number,
                         'sender_address' => $sender_address,
                         'supplier_name' => $supplier_name,
                         'supplier_phone_number' => $supplier_phone_number,
                         'supplier_address' => $supplier_address,
                         'size' => $size,
                         'mass' => $mass,
                         'parcel_content' => $parcel_content];

                $savePicture = new BarcodeController();
                $savePicture->save($SSCC_number);

                $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true,
                                        'isRemoteEnabled' => true,
                                        //'defaultFont' => 'courier',
                                        'debugPng' => true]);
                $pdf->loadView('pdf.label', $data)
                    ->setPaper('a6');

                return $pdf->download('Etykieta '.$SSCC_number.'.pdf');

            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz odpowiednich uprawnień do tej funkcji");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz odpowiednich uprawnień do tej funkcji");
        }
    }
}
