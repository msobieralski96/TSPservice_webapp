<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Parcel;
use Redirect;
use Ayeo\Barcode;

class BarcodeController extends Controller
{
    public function show(Request $request, $SSCC_number)
    {
        if(Auth::check()){
            $builder = $this->save($SSCC_number);
            return response($builder->output('(00)'.$SSCC_number))->header('Content-type','image/png');
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz odpowiednich uprawnień do tej funkcji");
        }
    }

    public function save($SSCC_number){
        if(Auth::check()){
            //use Ayeo\Barcode;
            //generates GS1 128 code
            $builder = new Barcode\Builder();
            $builder->setBarcodeType('gs1-128');
            $builder->setFilename('../storage/app/barcodes/(00)'.$SSCC_number.'.png');//WE DON'T WANT TO SAVE THIS IN '/public'
            $builder->setImageFormat('png');
            //$builder->setWidth(500);
            $builder->setWidth(320);
            //$builder->setHeight(150);
            $builder->setHeight(96);
            $builder->setFontPath('FreeSans.ttf');
            //$builder->setFontSize(15);
            $builder->setFontSize(9.6);
            $builder->setBackgroundColor(255, 255, 255);
            $builder->setPaintColor(0, 0, 0);
            $builder->saveImage('(00)'.$SSCC_number);
            return $builder;
        } else {
            return null;
        }
    }
}
