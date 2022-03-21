<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Redirect;
use File;
use Response;

class StorageController extends Controller
{
    public function showBarcode(Request $request, $SSCC_number)
    {
        if(Auth::check()){
            $path = storage_path('app/barcodes/(00)' . $SSCC_number . '.png');

            if (!File::exists($path)) {
                abort(404);
            }

            $file = File::get($path);
            $type = File::mimeType($path);

            $response = Response::make($file, 200);
            $response->header("Content-Type", $type);

            return $response;

        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz odpowiednich uprawnień do tej funkcji");
        }
    }

    public function showSignature(Request $request, $SSCC_number)
    {
        if(Auth::check()){
            $path = storage_path('app/signatures/sign_' . $SSCC_number . '.png');

            if (!File::exists($path)) {
                abort(404);
            }

            $file = File::get($path);
            $type = File::mimeType($path);

            $response = Response::make($file, 200);
            $response->header("Content-Type", $type);

            return $response;

        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz odpowiednich uprawnień do tej funkcji");
        }
    }

    public function downloadapk(Request $request) {
        if(Auth::check()){
            $path = storage_path('app/tspservice.apk');

            if (!File::exists($path)) {
                abort(404);
            }

            return response()->download($path);

        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz odpowiednich uprawnień do tej funkcji");
        }

    }
}
