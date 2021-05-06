<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    private $allowedKeys = [
        'name'=>'',
        'price'=>'',
        'bedrooms'=>'',
        'bathrooms'=>'',
        'storeys'=>'',
        'garages'=>''
    ];

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
        $data = json_decode($request->getContent(), true);
        $errorMessage = 'no JSON format or empty request';
        $incorrectKeys = array_diff_key($this->allowedKeys, $data);

        if (empty($data)) {
            return new JsonResponse(['error'=>$errorMessage]);
        }

        $correctKeys = array_intersect_key($data, $this->allowedKeys);




        return $correctKeys;
//        return new JsonResponse($data);
    }

    public function testDatabase()
    {
//        return DB::table('property')->get();
        return Property::where('name', 'The Victoria')
            ->get();
    }
}