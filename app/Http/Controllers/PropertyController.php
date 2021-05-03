<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function requestAction(Request $request)
    {
//        $data = [
//            'name'=>'The Victoria',
//            'price'=>'100500',
//            'bedrooms'=>'4',
//            'bathrooms'=>'2',
//            'storeys'=>'2',
//            'garages'=>'2'
//        ];
//
//        return json_encode($data);
        $data = json_decode($request->getContent(), true);
        $errorMessage = ['error'=>'no JSON format'];

        if (empty($data)) {
            return new JsonResponse($errorMessage);
        }

//        $data

//        $output = $data;

        return new JsonResponse($data);
    }
}
