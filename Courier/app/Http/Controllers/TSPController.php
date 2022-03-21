<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use View;
use Redirect;
use Input;
use App\Parcel;
use GuzzleHttp\Client;
use App\User;
use App\Place;
//require 'ChartController.php';
//use Carbon\Carbon;
//use Khill\Lavacharts\Lavacharts;

class TSPController extends Controller
{

    public function create(Request $request)
    {
        if(Auth::check()) {
            $parcels = collect();
            if(Auth::user()->isAdmin()){
                $parcels = Parcel::all();
            } elseif(Auth::user()->isCourier()){
                $parcels = Parcel::where('courier_id', Auth::user()->id)->get();
            }
            $places = Place::all();
            $couriers = collect();
            foreach($parcels as $parcel){
                $couriers->push(User::where('id', $parcel->courier_id)->first());
            }
            return View::make('TSP.prepare')
                ->with('parcels', $parcels)
                ->with('places', $places)
                ->with('couriers', $couriers);
        } else {
            return Redirect::back()
                ->with('error', "Nie masz odpowiednich uprawnień, aby uzyskać dostęp do tej funkcji");
        }
    }

    public function store(Request $request)
    {
        if(Auth::check()) {
            $delayCounter = 0;
            $allParcels = Parcel::all();
            $allPlaces = Place::all();
            $TSPdata = array();
            $parcelData = array();
            $addressesList = array();
            $distanceData = array();
            $numberOfDiffAddresses = 0;

            $parcelInput = $request->input('addresses');
            $firstInput = explode('|', Input::get('first'));
            $option = $request->input('option');
            $edges = $request->input('edges');

            if($parcelInput !== null && count($parcelInput) >= 1 && strlen($firstInput[0]) > 0) {

                foreach($parcelInput as $parcelInfo){
                    array_push($parcelData, explode('|', $parcelInfo));
                }

                $numberOfDiffAddresses = $this->checkNumberOfDiffAddresses(array_merge(array($firstInput), $parcelData), $allParcels, $allPlaces);

                if($numberOfDiffAddresses >= 3 && (($numberOfDiffAddresses <= 12 && $edges == "real") || ($numberOfDiffAddresses <= 60 && $edges == "linear"))){

                    try {

                        $TSPdata = $this->addParcelToTSP($firstInput, $allParcels, $allPlaces, $TSPdata);

                        foreach($parcelData as $parcelInfo){
                            $TSPdata = $this->addParcelToTSP($parcelInfo, $allParcels, $allPlaces, $TSPdata);
                        }

                        $addressesList = $this->getAllAddressesData($addressesList, $TSPdata, $delayCounter);
                        $delayCounter = $addressesList[count($addressesList)-1][0];
                        unset($addressesList[count($addressesList)-1]);

                        if($edges == "real"){
                            $distanceData = $this->findAllDistances($distanceData, $addressesList, $delayCounter);
                            $delayCounter = $distanceData[count($distanceData)-1][0];
                            unset($distanceData[count($distanceData)-1]);
                        } else {
                            $distanceData = $this->calculateAllDistances($distanceData, $addressesList);
                        }

                        $TSPdata = $this->addXandYtoTSPData($TSPdata, $addressesList);

                        $TSPdata = $this->TSPResolve($TSPdata, $distanceData, $addressesList, $option);

                        $totalDistance = $this->countTotal($TSPdata, "distance");
                        $totalTime = $this->setNumbersToTextInArray(array($this->countTotal($TSPdata, "duration")), 2)[0];
                        //$helperArray = array($totalTime);
                        //$totalTime =$this->setNumbersToTextInArray(

                        //$chartCtrl = new ChartController();

                        return View::make('TSP.resolve')
                            //->with('lava', $chartCtrl->getData($TSPdata))
                            ->with('parcels', $TSPdata)
                            ->with('totalDistance', $totalDistance)
                            ->with('totalTime', $totalTime)
                            ->with('pointAddresses', array_column($TSPdata, 'address'))
                            ->with('pointXs', $this->setNumbersToTextInArray(array_column($TSPdata, 'x'), 1))
                            ->with('pointXGraphs', array_column($TSPdata, 'xGraph'))
                            ->with('pointYs', $this->setNumbersToTextInArray(array_column($TSPdata, 'y'), 1))
                            ->with('pointYGraphs', array_column($TSPdata, 'yGraph'))
                            ->with('pointDistances', array_column($TSPdata, 'distance'))
                            ->with('pointDurations', $this->setNumbersToTextInArray(array_column($TSPdata, 'duration'), 2))
                            ->with('options', $this->checkSettings($option, $edges));
                    } catch (Exception $e){
                        return Redirect::action('TSPController@create')
                            ->withInput()
                            ->with('error', "Błąd aplikacji: ".$e->getMessage()." [".$e->getCode()."]");
                    }
                } elseif ($edges == "real"){
                    return Redirect::action('TSPController@create')
                        ->withInput()
                        ->with('error', "Możesz wybrać przesyłki, dla których łączna ilość różnych adresów wynosi od 3 do 12! (dla danych ustawień)
                            Wybrano: ".$numberOfDiffAddresses." różnych adresów.");
                } else {
                    return Redirect::action('TSPController@create')
                        ->withInput()
                        ->with('error', "Możesz wybrać przesyłki, dla których łączna ilość różnych adresów wynosi od 3 do 60! (dla danych ustawień)
                            Wybrano: ".$numberOfDiffAddresses." różnych adresów.");
                }
            } elseif ($option == null || strlen($option) <= 0 || $edges == null || strlen($edges) <= 0) {
                return Redirect::action('TSPController@create')
                    ->withInput()
                    ->with('error', "Musisz wybrać opcje wyszukiwania!");
            } elseif ($parcelInput == null){
                return Redirect::action('TSPController@create')
                    ->withInput()
                    ->with('error', "Musisz wybrać przesyłki!");
            } else/*if (strlen($firstInput[0]) <= 0) */{
                return Redirect::action('TSPController@create')
                    ->withInput()
                    ->with('error', "Musisz wybrać pierwszą przesyłkę!");
            }
        } else {
            return Redirect::back()
                ->with('error', "Nie masz odpowiednich uprawnień, aby uzyskać dostęp do tej funkcji");
        }
    }

    public function checkNumberOfDiffAddresses($parcelData, $allParcels, $allPlaces){
        $addresses = array();
        foreach($parcelData as $inputData){
            if($inputData[1] == "Miejsce predefiniowane"){
                foreach($allPlaces as $place){
                    $address = " ";
                    if($place->id == $inputData[0]){
                        $address = $place->address;
                    }
                    if($address != " "){
                        $alreadyAdded = false;
                        if(count($addresses) > 0){
                            foreach($addresses as $existingAddress){
                                if($existingAddress == $address){
                                    $alreadyAdded = true;
                                    break;
                                }
                            }
                        }
                        if(!$alreadyAdded){
                            array_push($addresses, $address);
                            break;
                        }
                    }
                    //break;
                }
            } else {
                foreach($allParcels as $parcel){
                    if($parcel->id == $inputData[0]){
                        if ($inputData[1] == "Adres nadawcy"){
                            $address = $parcel->sender_address;
                        } elseif ($inputData[1] == "Lokalizacja aktualna"){
                            $address = $parcel->current_address;
                        } else/*if ($inputData[1] == "Adres docelowy") */{
                            $address = $parcel->address;
                        }
                        $alreadyAdded = false;
                        if(count($addresses) > 0){
                            foreach($addresses as $existingAddress){
                                if($existingAddress == $address){
                                    $alreadyAdded = true;
                                    break;
                                }
                            }
                        }
                        if(!$alreadyAdded){
                            array_push($addresses, $address);
                        }
                        break;
                    }
                }
            }
        }
        return count($addresses);
    }

    public function addParcelToTSP($inputData, $allParcels, $allPlaces, $TSPdata){
        //$parcelDetail = json_decode($inputData, true);
        if($inputData[1] == "Miejsce predefiniowane"){
            foreach($allPlaces as $place){
                if($place->id == $inputData[0]){
                    $deliveryPoint = array(
                        'parcelId' => $place->id,
                        'SSCC_number' => null,
                        'addressType' => $place->name,
                        'address' => $place->address,
                        'date_of_delivery' => null,
                        'state_of_delivery' => null,
                        'courier_id' => null,
                        'courier_name' => null,
                        'x' => 0,
                        'xGraph' => 0,
                        'y' => 0,
                        'yGraph' => 0,
                        'distance' => -1,
                        'duration' => -1,
                        'mass' => null,
                        'size' => null,
                        'client_first_name' => null,
                        'client_last_name' => null,
                        'client_phone_number' => null,
                        'client_email' => null,
                        'courier_phone_number' => null,
                        'deliver_order' => null,
                        'get_order' => null,
                        'sender_first_name' => null,
                        'sender_last_name' => null,
                        'sender_phone_number' => null,
                        'sender_email' => null,
                        'parcel_content' => null,
                    );
                    if(count($TSPdata) == 0 || $TSPdata[0] !== $deliveryPoint){
                        array_push($TSPdata, $deliveryPoint);
                    }
                }
            }
        } else {
            foreach($allParcels as $parcel){
                if($parcel->id == $inputData[0]){
                    $parcelId = $parcel->id;
                    $SSCC_number = $parcel->SSCC_number;
                    if ($inputData[1] == "Adres nadawcy"){
                        $addressType = "Adres nadawcy";
                        $address = $parcel->sender_address;
                        $date_of_delivery = $parcel->date_of_get_delivery;
                    } elseif ($inputData[1] == "Lokalizacja aktualna"){
                        $addressType = "Lokalizacja aktualna";
                        $address = $parcel->current_address;
                        $date_of_delivery = $parcel->date_of_delivery;
                    } else {
                        $addressType = "Adres docelowy";
                        $address = $parcel->address;
                        $date_of_delivery = $parcel->date_of_delivery;
                    }
                    //$date_of_delivery = $parcel->date_of_delivery;
                    //$date_of_get_delivery = $parcel->date_of_get_delivery;
                    $state_of_delivery = $parcel->state_of_delivery;
                    $courier_id = $parcel->courier_id;
                    if($courier_id == null){
                        $courier_name = null;
                        $courier_phone_number = null;
                    } else {
                        $courier = User::where('id', $parcel->courier_id)->first();
                        $courier_name = $courier->name;
                        $courier_phone_number = $courier->phone_number;
                    }
                    //$locationInfo = $this->getLocation($address);
                    //$x = $locationInfo["x"];
                    //$y = $locationInfo["y"];
                    $distance = -1;
                    $duration = -1;
                    $mass = $parcel->mass;
                    $size = $parcel->size;
                    $client_first_name = $parcel->client_first_name;
                    $client_last_name = $parcel->client_last_name;
                    $client_phone_number = $parcel->client_phone_number;
                    $client_email = $parcel->client_email;
                    $deliver_order = $parcel->deliver_order;
                    $get_order = $parcel->get_order;
                    $sender_first_name = $parcel->sender_first_name;
                    $sender_last_name = $parcel->sender_last_name;
                    $sender_phone_number = $parcel->sender_phone_number;
                    $sender_email = $parcel->sender_email;
                    $parcel_content = $parcel->parcel_content;
                    $deliveryPoint = array(
                        'parcelId' => $parcelId,
                        'SSCC_number' => $SSCC_number,
                        'addressType' => $addressType,
                        'address' => $address,
                        'date_of_delivery' => $date_of_delivery,
                        //'date_of_get_delivery' => $date_of_get_delivery,
                        'state_of_delivery' => $state_of_delivery,
                        'courier_id' => $courier_id,
                        'courier_name' => $courier_name,
                        'x' => 0,//$this->getTrueXorY($x),
                        'xGraph' => 0,//$x,
                        'y' => 0,//$this->getTrueXorY($y),
                        'yGraph' => 0,//$y,
                        'distance' => $distance,
                        'duration' => $duration,
                        'mass' => $mass,
                        'size' => $size,
                        'client_first_name' => $client_first_name,
                        'client_last_name' => $client_last_name,
                        'client_phone_number' => $client_phone_number,
                        'client_email' => $client_email,
                        'courier_phone_number' => $courier_phone_number,
                        'deliver_order' => $deliver_order,
                        'get_order' => $get_order,
                        'sender_first_name' => $sender_first_name,
                        'sender_last_name' => $sender_last_name,
                        'sender_phone_number' => $sender_phone_number,
                        'sender_email' => $sender_email,
                        'parcel_content' => $parcel_content
                    );
                    if(count($TSPdata) == 0 || $TSPdata[0] !== $deliveryPoint){
                        array_push($TSPdata, $deliveryPoint);
                    }
                    break;
                }
            }
        }
        return $TSPdata;
    }

    public function getTrueXorY($number){
        $degrees = floor($number);
        $minutes = ($number - $degrees)*0.6;
        return $degrees + $minutes;
    }

    public function getGraphXorY($number){
        $degrees = floor($number);
        $minutes = ($number - $degrees)*5/3;
        return $degrees + $minutes;
    }

    public function sine($angle){
        return sin(deg2rad($angle));
    }

    public function cosine($angle){
        return cos(deg2rad($angle));
    }

    public function getAllAddressesData($addressesList, $TSPdata, $delayCounter){
        foreach($TSPdata as $parcel){
            if(count($addressesList) == 0){
                $addressesList = $this->addAddressData($addressesList, $parcel);
                $delayCounter = $this->checkDelay($delayCounter);
            } else {
                $repeatedAddress = false;
                foreach($addressesList as $address){
                    if($address["address"] == $parcel["address"]){
                        $repeatedAddress = true;
                        break;
                    }
                }
                if(!$repeatedAddress){
                    $addressesList = $this->addAddressData($addressesList, $parcel);
                    $delayCounter = $this->checkDelay($delayCounter);
                }
            }
        }
        return array_merge($addressesList, array($delayCounter));
    }

    public function addAddressData($addressesList, $parcel){
        $locationInfo = $this->getLocation($parcel["address"]);
        $x = $locationInfo["x"];
        $y = $locationInfo["y"];
        $newAddress = array(
            'address' => $parcel["address"],
            'xGraph' => $x,
            'yGraph' => $y
        );
        array_push($addressesList, $newAddress);
        return $addressesList;
    }

    public function checkDelay($delayCounter){
        $delayCounter++;
        if($delayCounter >= 10){
            sleep(10);
            $delayCounter = 0;
        }
        return $delayCounter;
    }

    public function getLocation($address)
    {
        $client = new Client();
        $params = [
            'api_key' => env('ORS_KEY', ''),
            'text' => $address,
        ];

        $response = $client->request('GET', 'https://api.openrouteservice.org/geocode/search',
            ['query' => $params]);
        $statusCode = $response->getStatusCode(); // 200

        $content = $response->getBody()->getContents();
        $contentArray = json_decode($content, true);
        $info = array(
            'x' => $contentArray["features"][0]["geometry"]["coordinates"][0],
            'y' => $contentArray["features"][0]["geometry"]["coordinates"][1]
        );
        sleep(1);
        return $info;
    }

    public function calculateAllDistances($distanceData, $addressesList){
        $i = 0;
        foreach ($addressesList as $point){
            $j = 0;
            foreach ($addressesList as $point2){
                if($point["address"] == $point2["address"]){
                    array_push($distanceData, array(
                        'distance' => 0,
                        'duration' => 0,
                        'point1' => $point["address"],
                        'point2' => $point2["address"])
                    );
                } else {
                    $distance = $this->calculateDistance($point, $point2);
                    array_push($distanceData, array(
                       'distance' => $distance,
                       'duration' => $distance,
                       'point1' => $point["address"],
                       'point2' => $point2["address"])
                    );
                }
                $j++;
            }
            $i++;
        }
        return $distanceData;
    }

    public function calculateDistance($point, $point2)
    {
        //Based on: https://pl.wikibooks.org/wiki/Astronomiczne_podstawy_geografii/Odleg%C5%82o%C5%9Bci
        $pole = 90;
        //AP -> arc length from A to pole (in degrees)
        $AP = $pole - $point["yGraph"];
        //BP -> arc length from B to pole (in degrees)
        $BP = $pole - $point2["yGraph"];
        //P -> differences between the geographical lengths of the two points (A and B)
        $P = abs($point["xGraph"] - $point2["xGraph"]);
        $cosAB = ($this->cosine($AP) * $this->cosine($BP)) +
                 ($this->sine($AP) * $this->sine($BP) * $this->cosine($P));
        $distance = rad2deg(acos($cosAB)) * 111.1;
        return $distance;
    }

    public function findAllDistances($distanceData, $addressesList, $delayCounter){
        $i = 0;
        foreach ($addressesList as $point){
            $j = 0;
            foreach ($addressesList as $point2){
                if($point["address"] == $point2["address"]){
                    array_push($distanceData, array(
                        'distance' => 0,
                        'duration' => 0,
                        'point1' => $point["address"],
                        'point2' => $point2["address"])
                    );
                } else {
                    $distanceAndDuration = $this->getDistance($point["xGraph"],$point["yGraph"],
                            $point2["xGraph"],$point2["yGraph"]);
                    array_push($distanceData, array(
                       'distance' => $distanceAndDuration["distance"],
                       'duration' => $distanceAndDuration["duration"]/60,
                       'point1' => $point["address"],
                       'point2' => $point2["address"])
                    );
                    $delayCounter = $this->checkDelay($delayCounter);
                }
                $j++;
            }
            $i++;
        }
        return array_merge($distanceData, array($delayCounter));
    }

    public function getDistance($x1, $y1, $x2, $y2)
    {
        $client = new Client();
        $params = [
            'api_key' => env('ORS_KEY', ''),
            //'coordinates' => '18.01434,53.122091|18.615426,53.027474',
            'coordinates' => $x1.','.$y1.'|'.$x2.','.$y2,
            'profile' => 'driving-car',
            'instructions' => 'false',
            'language' => 'en',
            'geometry' => 'false',
            'units' => 'km',
        ];

        $response = $client->request('GET', 'https://api.openrouteservice.org/directions',
            ['query' => $params]);
        $statusCode = $response->getStatusCode(); // 200

        $content = $response->getBody()->getContents();
        $contentArray = json_decode($content, true);
        $info = array(
            'distance' => $contentArray["routes"][0]["summary"]["distance"],
            'duration' => $contentArray["routes"][0]["summary"]["duration"]
        );
        sleep(1);
        return $info;
    }

    public function addXandYtoTSPData($TSPdata, $addressesList){
        $newTSPdata = array();
        foreach($TSPdata as $parcel){
            foreach($addressesList as $address){
                if($parcel["address"] == $address["address"]){
                    $parcel["x"] = $this->getTrueXorY($address["xGraph"]);
                    $parcel["xGraph"] = $address["xGraph"];
                    $parcel["y"] = $this->getTrueXorY($address["yGraph"]);
                    $parcel["yGraph"] = $address["yGraph"];
                    array_push($newTSPdata, $parcel);
                    break;
                }
            }
        }
        return $newTSPdata;
    }

    public function TSPResolve($TSPdata, $distanceData, $addressesList ,$option){
        $avgDistanceData = $this->returnAvgDistanceArray(/*$TSPdata*/$addressesList, $distanceData, $option);
        $forestEdges = $this->getMinimumSpanningTree($avgDistanceData);
        $forest = $forestEdges[count($forestEdges)-1];
        unset($forestEdges[count($forestEdges)-1]);
        $forest = $this->setFirstPoint(/*$TSPdata*/$addressesList, $forest);
        $nodes = $this->depthFirstSerach($forest, $forestEdges);
        $finalData = $this->getFinalData($TSPdata, $distanceData, $nodes, $option);
        $finalData = $this->addRepeatedAddresses($TSPdata, $finalData);
        return $finalData;
    }

    function returnAvgDistanceArray(/*$TSPdata*/$addressesList, $distanceData, $option){
        $avgDistanceData = array();
        $pointsData = array();
        foreach(/*$TSPdata*/$addressesList as $element){
            array_push($pointsData, $element['address']);
        }
        $distance1 = 0;
        $distance2 = 0;
        $p1 = "none";
        $p2 = "none";
        $flag1 = 0;
        $flag2 = 0;
        foreach($pointsData as $point1){
            foreach($pointsData as $point2){
                foreach($distanceData as $key => $dat){
                    if($dat['point1'] == $point1 && $dat['point2'] == $point2){
                        if($dat[$option] !== 0){
                            $distance1 = $dat[$option];
                            $p1 = $dat['point1'];
                            $p2 = $dat['point2'];
                            $flag1 = 1;
                        }
                        unset($distanceData[$key]);
                    }
                    if($dat['point1'] == $point2 && $dat['point2'] == $point1){
                        if($dat[$option] !== 0){
                            $distance2 = $dat[$option];
                            $p1 = $dat['point2'];
                            $p2 = $dat['point1'];
                            $flag2 = 1;
                        }
                        unset($distanceData[$key]);
                    }
                    if($flag1 == 1 && $flag2 == 1){
                        $avgDistance = ($distance1+$distance2)/2.0;
                        array_push($avgDistanceData, array(
                            'distance' => $avgDistance,
                            'point1' => $p1,
                            'point2' => $p2));
                        $distance1 = 0;
                        $distance2 = 0;
                        $pe1 = "none";
                        $pe2 = "none";
                        $flag1 = 0;
                        $flag2 = 0;
                        break;
                    }
                }
            }
        }
        return $avgDistanceData;
    }

    function getMinimumSpanningTree($avgDistanceData){
        $edges = array();
        $forestEdges = array();
        $forest = array();
        foreach($avgDistanceData as $dat){
            $addPointToForest = true;
            $addPoint2ToForest = true;
            if($dat['distance'] > 0){
                array_push($edges, $dat);
            }
            foreach($forest as $tree){
                if($dat['point1'] == $tree[0]){
                    $addPointToForest = false;
                    break;
                }
            }
            if($addPointToForest){
                array_push($forest, array($dat['point1']));
            }
            foreach($forest as $tree){
                if($dat['point2'] == $tree[0]){
                    $addPoint2ToForest = false;
                    break;
                }
            }
            if($addPoint2ToForest){
                array_push($forest, array($dat['point2']));
            }
        }
        usort($edges, array($this, "sortByDistance"));

        for($i = count($edges)-1; $i >= 0; $i--){
            if(count($forest)>1){
                $curEdge = $edges[$i];
                unset($edges[$i]);
                $tree1 = null;
                $tree2 = null;
                for($j = count($forest)-1; $j >= 0; $j--){
                    $stopSearch = false;
                    foreach($forest[$j] as $point){
                        if($curEdge['point1'] == $point){
                            $stopSearch = true;
                            $tree1 = $forest[$j];
                            unset($forest[$j]);
                            break;
                        }
                    }
                    if($stopSearch){
                        break;
                    }
                }

                $forestTemp = array();
                foreach($forest as $tree){
                    array_push($forestTemp, $tree);
                }
                $forest = $forestTemp;
                for($j = count($forest)-1; $j >= 0; $j--){
                    $stopSearch = false;
                    foreach($forest[$j] as $point){
                        if($curEdge['point2'] == $point){
                            $stopSearch = true;
                            $tree2 = $forest[$j];
                            unset($forest[$j]);
                            break;
                        }
                    }
                    if($stopSearch){
                        break;
                    }
                }

                $forestTemp = array();
                foreach($forest as $tree){
                    array_push($forestTemp, $tree);
                }
                $forest = $forestTemp;

                if($tree1 !== $tree2 && $tree1 !== null && $tree2 !== null){
                    array_push($forest, array_merge($tree1, $tree2));
                    array_push($forestEdges, $curEdge);
                } elseif ($tree1 !== null && $tree2 == null) {
                    array_push($forest, $tree1);
                } elseif ($tree1 == null && $tree2 !== null) {
                    array_push($forest, $tree2);
                }
            }
        }
        return array_merge($forestEdges, $forest);
    }

    function sortByDistance($value1, $value2) {
        return $value1['distance']<$value2['distance'];
    }

    function setFirstPoint(/*$TSPdata*/$addressesList, $forest){
        $firstPoint = /*$TSPdata*/$addressesList[0]['address'];
        $forestWithFirstPoint = array();
        foreach($forest as $key => $tree){
            if($tree == $firstPoint){
                array_push($forestWithFirstPoint, $tree);
                unset($forest[$key]);
                break;
            }
        }
        foreach($forest as $tree){
            array_push($forestWithFirstPoint, $tree);
        }
        return $forestWithFirstPoint;
    }

    function depthFirstSerach($forest, $forestEdges){
        $nodes = $this->depthFirstSerachGetGraph($forest, $forestEdges);
        array_push($nodes, array());
        for($j = 0; $j < count($nodes)-1; $j++){
            if($nodes[$j]["visited"] == 0){
                $nodes = $this->visitNode($nodes, $j);
            }
        }
        $nodes = $nodes[count($nodes)-1];
        array_push($nodes, $nodes[0]);
        return $nodes;
    }

    function depthFirstSerachGetGraph($forest, $forestEdges){
        $nodes = array();
        foreach($forest as $node){
            $neighbors = array();
            foreach($forestEdges as $edge){
                if($edge['point1'] == $node){
                    $newNeighbor = true;
                    foreach($neighbors as $neighbor){
                        if($edge['point2'] == $neighbor){
                            $newNeighbor = false;
                            break;
                        }
                    }
                    if($newNeighbor){
                        array_push($neighbors, $edge['point2']);
                    }
                }
                if($edge['point2'] == $node){
                    $newNeighbor = true;
                    foreach($neighbors as $neighbor){
                        if($edge['point1'] == $neighbor){
                            $newNeighbor = false;
                            break;
                        }
                    }
                    if($newNeighbor){
                        array_push($neighbors, $edge['point1']);
                    }
                }
            }
            array_push($nodes, array(
                'name' => $node,
                'visited' => 0,
                'neighbors' => $neighbors
                )
            );
        }
        return $nodes;
    }

    function visitNode($nodes, $currentNode){
        $nodes[$currentNode]["visited"] = 1;
        array_push($nodes[count($nodes)-1], $nodes[$currentNode]["name"]);
        foreach($nodes[$currentNode]["neighbors"] as $neighbor){
            $nodeNum = $this->findNode($nodes, $neighbor);
            if($nodes[$nodeNum]["visited"] == 0){
                $nodes = $this->visitNode($nodes, $nodeNum);
            }
        }
        return $nodes;
    }

    function findNode($nodes, $name){
        for($i = 0; $i < count($nodes); $i++){
            if($nodes[$i]["name"] == $name){
                return $i;
            }
        }
    }

    function getFinalData($TSPdata, $distanceData, $nodes, $option){
        $finalData = array();
        foreach($nodes as $node){
            foreach($TSPdata as $dat){
                if($node == $dat['address']){
                    array_push($finalData, $dat);
                    break;
                }
            }
        }
        $finalData = $this->checkQuickerLoop($finalData, $distanceData, $nodes, $option);
        return $finalData;
    }

    function checkQuickerLoop($finalData, $distanceData, $nodes, $option){
        $roadDataNormal = $this->getRoadData($distanceData, $nodes, $option);
        $nodesReverse = $this->reverseNodes($nodes);
        $roadDataReverse = $this->getRoadData($distanceData, $nodesReverse, $option);
        if($roadDataNormal[0] <= $roadDataReverse[0]){
            $finalData = $this->addDistancesToFinalData($finalData, $roadDataNormal);
        } else {
            $finalData = $this->reverseFinalData($finalData);
            $finalData = $this->addDistancesToFinalData($finalData, $roadDataReverse);
        }
        return $finalData;
    }

    function getRoadData($distanceData, $nodes, $option){
        $roadData = array();
        $totalDistance = 0;
        for($i = 1; $i < count($nodes); $i++){
            $point1 = $nodes[$i-1];
            $point2 = $nodes[$i];
            foreach($distanceData as $road){
                if($road["point1"] == $point1 && $road["point2"] == $point2){
                    array_push($roadData, array(
                            'distance' => $road['distance'],
                            'duration' => $road['duration']
                        ));
                    //array_push($roadData, $road[$option]);
                    $totalDistance += $road[$option];
                    break;
                }
            }
        }
        return array_merge(array($totalDistance), $roadData);
    }

    function reverseNodes($nodes){
        $nodesReverse = array();
        for($i = count($nodes)-1; $i>=0; $i--){
            array_push($nodesReverse, $nodes[$i]);
        }
        return $nodesReverse;
    }

    function addDistancesToFinalData($finalData, $roadData){
        for($i = 1; $i < count($roadData); $i++){
            $finalData[$i-1]["distance"] = $roadData[$i]["distance"];
            $finalData[$i-1]["duration"] = $roadData[$i]["duration"];
        }
        $finalData[count($finalData)-1]["distance"] = $roadData[1]["distance"];
        $finalData[count($finalData)-1]["duration"] = $roadData[1]["duration"];
        return $finalData;
    }

    function reverseFinalData($finalData){
        $finalDataReverse = array();
        for($i = count($finalData)-1; $i>=0; $i--){
            array_push($finalDataReverse, $finalData[$i]);
        }
        return $finalDataReverse;
    }

    public function countTotal($TSPdata, $measure){
        $number = 0;
        foreach($TSPdata as $node){
            $number += $node[$measure];
        }
        $number -= $TSPdata[count($TSPdata)-1][$measure];
        return $number;
    }

    public function addRepeatedAddresses($TSPdata, $finalData){
        $finalDataWithRepeatedAddresses = array();
        foreach($finalData as $fDat){
            foreach($TSPdata as $dat){
                if(($fDat["address"] == $dat["address"]) && ($fDat["parcelId"] !== $dat["parcelId"])){
                    $dat["distance"] = 0;
                    $dat["duration"] = 0;
                    array_push($finalDataWithRepeatedAddresses, $dat);
                }
            }
            array_push($finalDataWithRepeatedAddresses, $fDat);
        }
        return $finalDataWithRepeatedAddresses;
    }

    function setNumbersToTextInArray($array, $coorOrTime){
        //$coorOrTime:
        //1 - coordinates
        //2 - time
        $newArray = array();
        foreach($array as $coordinate){
            $integerPart = floor($coordinate);
            if($coorOrTime == 1){
                $fractionalPart = substr(round($coordinate - $integerPart, 2), 2);
                if(strlen($fractionalPart) == 1){
                    $fractionalPart = $fractionalPart."0";
                }
                elseif(strlen($fractionalPart) == 0)
                {
                    $fractionalPart = "00";
                }
                $hours = "";
                $str1 = "";
                $str2 = "°";
                $str3 = "'";
            } else/*if (coorOrTime == 2)*/{
                $fractionalPart = substr(round(($coordinate - $integerPart)/100*60, 2), 2);
                if(strlen($fractionalPart) == 1){
                    $fractionalPart = $fractionalPart."0";
                }
                elseif(strlen($fractionalPart) == 0)
                {
                    $fractionalPart = "00";
                }
                $hours = floor($integerPart / 60);
                $integerPart -= $hours*60;
                $str1 = " h ";
                $str2 = " min ";
                $str3 = " s";
            }
            $coor = $hours.$str1.$integerPart.$str2.$fractionalPart.$str3;
            array_push($newArray, $coor);
        }
        return $newArray;
    }

    function checkSettings($option, $edges){
        if($edges == "real"){
            if($option == "distance"){
                return array("z uwzględnieniem dróg", "dystans");
            } else {
                return array("z uwzględnieniem dróg", "czas");
            }
        } else {
            return array("w linii prostej", "");
        }
    }

    public function edit(Request $request, $change)
    {
        if(Auth::check()) {
            $parcelInput = $request->input('parcel');
            $address_typeInput = $request->input('addressType');
            //$parcelInput = json_decode(json_encode($parcelInput), true);
            $iterator = 1;
            foreach($parcelInput as $key => $iParcel){
                //$iParcel = json_decode($iParcel, true);
                if(!($address_typeInput[$key][0] == '#')){
                    $parcel = Parcel::where('id', $iParcel/*["id"]*/)->first();
                    if($address_typeInput[$key] == "Adres docelowy"){
                        $parcel->deliver_order = $iterator;
                        $iterator++;
                        $parcel->save();
                    } elseif($address_typeInput[$key] == "Adres nadawcy"){
                        $parcel->get_order = $iterator;
                        $iterator++;
                        $parcel->save();
                    }
                }
            }
            return Redirect::to('TSP')
                ->with('message', "Zmieniono kolejność przesyłek.");
        } else {
            return Redirect::to('home')
                ->with('error', "Nie masz odpowiednich uprawnień, aby uzyskać dostęp do tej funkcji");
        }

    }
}

