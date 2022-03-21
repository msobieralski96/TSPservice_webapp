<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Parcel;
use App\User;
use App\Role;
use App\ParcelHistory;
use Input;
use Redirect;
use View;
use Carbon\Carbon;
use App\Http\Controllers\MailController;

class ApiParcelController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if($user !== null && !($user->isNotConfirmed())){
            if($user->isAdmin()){
                $parcels = Parcel::all();
            } elseif($user->isCourier()){
                $parcels = Parcel::where('courier_id', $user->id)->get();
            }
            $couriers = collect();
            foreach($parcels as $parcel){
                $couriers->push(User::where('id', $parcel->courier_id)->first());
            }
            return response()->json([
                'parcels' => $parcels,
                'couriers' => $couriers,
                'result' => 'true'
            ], 201);
        } else {
            return response()->json([
                'message' => "Nie masz odpowiednich uprawnień, aby wyświetlać przesyłki",
                'result' => 'false'
            ], 401);
        }
    }
/*    public function create(Request $request)
    {
        if(Auth::check()){
            if(Auth::user()->isAdmin()) {
                $parcel = new Parcel;
                return View::make('parcels.edit')
                    ->with('parcel', $parcel)
                    ->with('customParcelStatus', $this->ifCustomParcelStatus($parcel->state_of_delivery))
                    ->with('method', 'post');
            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz odpowiednich uprawnień, aby dodać przesyłkę");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz odpowiednich uprawnień, aby dodać przesyłkę");
        }
    }

    public function store(Request $request)
    {
        if(Auth::check()){
            if(Auth::user()->isAdmin()){
                $validatedData = $request->validate([
                    'SSCC_number' => 'required|min:3|unique:parcels',
                    'address' => 'required|min:3',
                    'sender_address' => 'required|min:3',
                    'current_address' => 'required|min:3',
                    'state_of_delivery' => 'required|min:3',
                    'date_of_delivery' => 'required|date',
                    'date_of_get_delivery' => 'required|date',
                    'mass' => 'string|nullable',
                    'size' => 'string|nullable',
                    'client_first_name' => 'required|string',
                    'client_last_name' => 'required|string',
                    'client_phone_number' => 'numeric|nullable',
                    'client_email' => 'string|email|max:100|nullable',
                    'deliver_order' => 'numeric|nullable',
                    'get_order' => 'numeric|nullable'
                    'sender_first_name' => 'required|string',
                    'sender_last_name' => 'required|string',
                    'sender_phone_number' => 'numeric|nullable',
                    'sender_email' => 'string|email|max:100|nullable',
                    'parcel_content' => 'required',
                ]);

                $parcel = Parcel::create(Input::all());

                $history = new ParcelHistory();
                $history->parcel_id = $parcel->id;
                $history->date_of_action = Carbon::now();
                $history->state_of_delivery = $parcel->state_of_delivery;
                $history->localisation = $parcel->current_address;

                if($parcel->state_of_delivery == "Inny"){
                    $parcel->state_of_delivery = Input::get('parcel_status');
                    $history->state_of_delivery = Input::get('parcel_status');
                } elseif(($parcel->state_of_delivery == "Przesyłka zarejestrowana w systemie" ||
                         $parcel->state_of_delivery == "Przygotowana do nadania" ||
                         $parcel->state_of_delivery == "W drodze do klienta") &&
                         strlen($parcel->client_email) > 0){
                    $mail_con = new MailController();
                    $mail_con->sendNotification($parcel->state_of_delivery,
                        $parcel->SSCC_number, $parcel->client_email, $parcel->current_address);
                }

                if($parcel->save() && $history->save()){
                    return Redirect::to('parcels/' . $parcel->id)
                        ->with('message', 'Dodano nową przesyłkę!');
                } else {
                    return Redirect::back()
                        ->with('error', 'Nie udało się dodać przesyłki!');
                }
            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz odpowiednich uprawnień, aby dodać przesyłkę");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz odpowiednich uprawnień, aby dodać przesyłkę");
        }
    }
*/
    public function show(Request $request, Parcel $parcel)
    {
        $user = $request->user();
        if($user !== null && !($user->isNotConfirmed())){
            $courier = User::where('id', $parcel->courier_id)->first();
            /*if ($courier == null){
                $courier_phone = null;
                $courier_email = null;
            } else {
                $courier_phone = $courier->phone_number;
                $courier_email = $courier->email;
            }*/
            return response()->json([
                'parcel' => $parcel,
                'courier' => $courier,
                //'courier_phone' => $courier_phone,
                //'courier_email' => $courier_email,
                /*'clientRequest' => false,*/
                'result' => 'true'
            ], 201);
        } else {
            return response()->json([
                'message' => "Nie masz odpowiednich uprawnień, aby wyświetlać przesyłki",
                'result' => 'false'
            ], 401);
        }
    }

    public function showBySSCC(Request $request, $SSCC)
    {
        $user = $request->user();
        if($user !== null && !($user->isNotConfirmed())){
            $parcel = Parcel::where('SSCC_number', $SSCC)->first();
            if ($parcel == null){
                return response()->json([
                    'message' => "Nie znaleziono przesyłki o danym SSCC w bazie danych",
                    'result' => 'false'
                ], 401);
            }
            $courier = User::where('id', $parcel->courier_id)->first();
            return response()->json([
                'parcel' => $parcel,
                'courier' => $courier,
                'result' => 'true'
            ], 201);
        } else {
            return response()->json([
                'message' => "Nie masz odpowiednich uprawnień, aby wyświetlać przesyłki",
                'result' => 'false'
            ], 401);
        }
    }

/*
    public function editView(Request $request, Parcel $parcel)
    {
        if(Auth::check()) {
            if(Auth::user()->canEdit($parcel)){
                return View::make('parcels.edit')
                    ->with('parcel', $parcel)
                    ->with('customParcelStatus', $this->ifCustomParcelStatus($parcel->state_of_delivery))
                    ->with('method', 'put');
            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz odpowiednich uprawnień, aby edytować przesyłkę");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz odpowiednich uprawnień, aby edytować przesyłkę");
        }
    }

    public function ifCustomParcelStatus($state_of_delivery){
        if($state_of_delivery == "Przesyłka zarejestrowana w systemie" ||
            $state_of_delivery == "Odebrana od nadawcy" ||
            $state_of_delivery == "W magazynie" ||
            $state_of_delivery == "Przygotowana do nadania" ||
            $state_of_delivery == "W trasie" ||
            $state_of_delivery == "W sortowni" ||
            $state_of_delivery == "W oddziale docelowym" ||
            $state_of_delivery == "W drodze do klienta" ||
            $state_of_delivery == "Doręczona" ||
            $state_of_delivery == "Awizo" ||
            $state_of_delivery == "Zwrócona do nadawcy" ||
            $state_of_delivery == null){
            return false;
        }
        return true;
    }

    public function destroyView(Request $request, Parcel $parcel)
    {
        if(Auth::check()) {
            if(Auth::user()->isAdmin()){
                return View::make('parcels.edit')
                    ->with('parcel', $parcel)
                    ->with('method', 'delete');
            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz odpowiednich uprawnień, aby usunąć przesyłkę");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz odpowiednich uprawnień, aby usunąć przesyłkę");
        }
    }
*/
    public function edit(Request $request, Parcel $parcel)
    {
        $user = $request->user();

        if($user !== null && !($user->isNotConfirmed())){
            if($user->canEdit($parcel)) {
                $request->validate([
                    'current_address' => 'required|min:3',
                    'state_of_delivery' => 'required|min:3',
                    'date_of_delivery' => 'required|date',
                    'date_of_get_delivery' => 'required|date',
                    'deliver_order'  => 'numeric|nullable',
                    'get_order' => 'numeric|nullable'
                ]);

                $parcel -> update(Input::all());
                $history = new ParcelHistory();
                $history->parcel_id = $parcel->id;
                $history->date_of_action = Carbon::now();
                $history->state_of_delivery = $parcel->state_of_delivery;
                $history->localisation = $parcel->current_address;

                if($parcel->state_of_delivery == "Inny"){
                    $parcel->state_of_delivery = Input::get('parcel_status');
                    $parcel->save();
                    $history->state_of_delivery = Input::get('parcel_status');
                } elseif(($parcel->state_of_delivery == "Przesyłka zarejestrowana w systemie" ||
                         $parcel->state_of_delivery == "Przygotowana do nadania" ||
                         $parcel->state_of_delivery == "W drodze do klienta") &&
                         strlen($parcel->client_email) > 0){
                    $mail_con = new MailController();
                    $mail_con->sendNotification($parcel->state_of_delivery,
                        $parcel->SSCC_number, $parcel->client_email, $parcel->current_address);
                }

                $history->save();
                return response()->json([
                    'message' => 'Edytowano właściwości przesyłki!',
                    'result' => 'true'
                ], 201);
            } else {
                return response()->json([
                    'message' => "Nie masz odpowiednich uprawnień, aby edytować przesyłkę",
                    'result' => 'false'
                ], 401);
            }
        } else {
            return response()->json([
                'message' => "Nie masz odpowiednich uprawnień, aby edytować przesyłkę",
                'result' => 'false'
            ], 401);
        }
    }

    public function deliver(Request $request, Parcel $parcel)
    {
        $user = $request->user();

        if($user !== null && !($user->isNotConfirmed())){
            if($user->canEdit($parcel)) {

                //if i'll upload 'signature' as image:
                /*if($request->hasFile('signature')){//upload a handwritten signature from our client
                    $request->signature->storeAs('signatures', 'sign_'.$parcel->SSCC_number.'.png');
                }*/

                //i'm uploading 'signature' as Base64 String:
                if($request->signature != null){
                    $pic = $request->signature;  // your base64 encoded
                    $pic = str_replace('data:image/png;base64,', '', $pic);
                    $pic = str_replace(' ', '+', $pic);
                    $fileName = 'sign_'.$parcel->SSCC_number.'.png';
                    \File::put(storage_path(). '/app/signatures/' . $fileName, base64_decode($pic));
                }

                //$parcel -> update(Input::all());
                $parcel->current_address = $parcel->address;
                $parcel->state_of_delivery = "Doręczona";
                //$parcel->date_of_delivery = Carbon::now();
                $parcel->date_of_delivery = Carbon::today();
                $parcel->save();

                $history = new ParcelHistory();
                $history->parcel_id = $parcel->id;
                $history->date_of_action = Carbon::now();
                $history->state_of_delivery = $parcel->state_of_delivery;
                $history->localisation = $parcel->current_address;
                $history->save();

                return response()->json([
                    'message' => 'Edytowano właściwości przesyłki!',
                    'result' => 'true'
                ], 201);
            } else {
                return response()->json([
                    'message' => "Nie masz odpowiednich uprawnień, aby edytować przesyłkę",
                    'result' => 'false'
                ], 401);
            }
        } else {
            return response()->json([
                'message' => "Nie masz odpowiednich uprawnień, aby edytować przesyłkę",
                'result' => 'false'
            ], 401);
        }
    }


/*
    public function destroy(Request $request, Parcel $parcel)
    {
        if(Auth::check()){
            if(Auth::user()->isAdmin()) {
                $history = ParcelHistory::all();
                foreach($history as $piece){
                    if($piece->parcel_id == $parcel->id){
                        $piece -> delete();
                    }
                }
                $parcel -> delete();
                return Redirect::to('parcels')
                    ->with('message', 'Usunięto przesyłkę!');
            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz odpowiednich uprawnień, aby usunąć przesyłkę");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz odpowiednich uprawnień, aby usunąć przesyłkę");
        }
    }*/
}
/*
View::composer('parcels.edit', function($view) {
    $roles = Role::where('role', 2)->orWhere('role', 4)->get();
    $allUsers = User::all();
    $couriers = collect();
    $noUser = new User();
    $noUser->id = null;
    $noUser->name = '';
    $couriers->push($noUser);
    foreach ($roles as $role){
        foreach ($allUsers as $user){
            if ($role->user_id == $user->id){
                $couriers->push($user);
            }
        }
    }
    if(count($couriers) > 0){
        $courier_options = $couriers->pluck('name', 'id');
    } else {
        $courier_options = array(null, 'nieokreślony');
    }
    $statuses = array(
        "Przesyłka zarejestrowana w systemie" => "Przesyłka zarejestrowana w systemie",
        "Odebrana od nadawcy" => "Odebrana od nadawcy",
        "W magazynie" => "W magazynie",
        "Przygotowana do nadania" => "Przygotowana do nadania",
        "W trasie" => "W trasie",
        "W sortowni" => "W sortowni",
        "W oddziale docelowym" => "W oddziale docelowym",
        "W drodze do klienta" => "W drodze do klienta",
        "Doręczona" => "Doręczona",
        "Awizo" => "Awizo",
        "Zwrócona do nadawcy" => "Zwrócona do nadawcy",
        "Inny" => "Inny"
    );
    $view->with('courier_options', $courier_options)
        ->with('statuses', $statuses);
});
*/
