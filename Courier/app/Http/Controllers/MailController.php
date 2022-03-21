<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
//use App\Parcel;
use App\User;
//use App\Role;
//use App\ParcelHistory;
use Input;
use Redirect;
use View;
use Carbon\Carbon;
use Mail;
use Crypt;

class MailController extends Controller
{
    public function create(Request $request)
    {
        if(Auth::check()){
            $toEmail = $request->input('toEmail');
            if(strlen($toEmail) <= 0){
                $toEmail = '';
            }
            $parcelId = $request->input('parcelId');
            if(strlen($parcelId) <= 0){
                $parcelId = '';
            }
            return View::make('email.mail')
                ->with('toEmail', $toEmail)
                ->with('parcelId', $parcelId);
        } else {
            $toEmail = $request->input('toEmail');
            if(strlen($toEmail) <= 0){
                return Redirect::to('home/')
                    ->with('error', "Nie masz odpowiednich uprawnień do tej funkcji");
            }
            //$toEmail = Crypt::decrypt($toEmail);
            $parcelId = $request->input('parcelId');
            return View::make('email.mail')
                ->with('toEmail', $toEmail)
                ->with('parcelId', $parcelId);
        }
    }
//z linka, zalogowany
//$toEmail = $request->input('toEmail');
//$parcelId = '';
//bez linka zalogowany
//toEmail -> null
//parcelId -> null
//z linka, niezalogowany
//$toEmail = $request->input('toEmail');
//$parcelId = $request->input('parcelId');
//bez linka, niezalogowany
//NIE MOŻE

    public function store(Request $request)
    {
        $toEmail = $request->input('toEmail');
        $subject = $request->input('subject');
        $body = $request->input('body');
        $isClient = $request->input('isClient');

        $validationArray = $this->validateForm($toEmail, $subject, $body, $isClient);
        if(!$validationArray["success"]){
            return Redirect::back()
                ->withInput()
                ->with('error', $validationArray["message"]);
        }

        if($isClient){
            $toEmail = Crypt::decrypt($toEmail);
        }

        if(Auth::check()){
            $user = User::where('id', Auth::user()->id)->first()->name;
        } else {
            $user = "Klient";
        }
        $user = $user." / TSP Service";

        Mail::send('email.template', array('body' => $body),
            function($message) use ($toEmail, $subject, $user){
            $message->to($toEmail)
                ->subject($subject);
            $message->from(env('MAIL_USERNAME', ''), $user);
        });

        if(Auth::check()){
            return Redirect::to('parcels/')
                ->with('message', 'Email został wysłany pomyślnie!');
        } else {
            return Redirect::to('home/')
                ->with('message', 'Email został wysłany pomyślnie!');
        }
    }

    public function validateForm($toEmail, $subject, $body, $isClient){
        if (!$isClient){
            if (strlen($toEmail) <= 0){
                return array(
                    'success' => false,
                    'message' => 'Pole to_email jest wymagane.'
                );
            } elseif (strlen($toEmail) > 101){
                return array(
                    'success' => false,
                    'message' => 'Pole to_email musi mieć poniżej 101 znaków.'
                );
            } elseif (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
                return array(
                    'success' => false,
                    'message' => 'to_email nie jest adresem email.'
                );
            }
        }
        if (strlen($subject) <= 0){
            return array(
                'success' => false,
                'message' => 'Pole subject jest wymagane.'
            );
        }
        if (strlen($body) <= 0){
            return array(
                'success' => false,
                'message' => 'Pole body jest wymagane.'
            );
        }
        return array(
            'success' => true,
            'message' => 'Walidacja zakończona powodzeniem.'
        );
    }

    public function sendNotification($state_of_delivery, $SSCC_number, $client_email, $current_address){
        $toEmail = $client_email;
        $subject = "Przesyłka ".$SSCC_number." : ".$state_of_delivery;
        $body1 = "Witaj!";
        $body2 = "Przesyłka ".$SSCC_number." otrzymała nowy status: ".$state_of_delivery.".";
        $body3 = "Aktualna lokalizacja przesyłki: ".$current_address.".";
        $body4 = "Data aktualizacji: ".Carbon::now().".";
        $user = "TSP Service";

        Mail::send('email.notification', array('body1' => $body1,
                   'body2' => $body2, 'body3' => $body3, 'body4' => $body4),
            function($message) use ($toEmail, $subject, $user){
            $message->to($toEmail)
                ->subject($subject);
            $message->from(env('MAIL_USERNAME', ''), $user);
        });
    }
}
