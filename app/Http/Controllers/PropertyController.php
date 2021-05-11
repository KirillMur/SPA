<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PropertyController extends Controller
{
    private $allowedKeys;
    private $notJsonMsg = ['error'=>'no JSON format or empty request'];
    private $incorrectFieldsMsg = 'error, incorrect fields name(s)';
    private $notFoundMsg = ['result'=>'not found'];

    public function requestAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if(empty($data)) {
            return new JsonResponse($this->notJsonMsg);
        }

        $incorrectKeys = array_diff_key($data, $this->allowedKeys); //для вывода названий некорректных полей

        if(!empty($incorrectKeys)) {
            return new JsonResponse([$this->incorrectFieldsMsg => $incorrectKeys]);
        }

        $correctKeys = array_intersect_key($data, $this->allowedKeys);

        return new JsonResponse($this->findEachField($correctKeys) + $this->findTogether($correctKeys));
    }

    private function findEachField(array $data) : array
    {
        $result = [];
        foreach($data as $key=> $value)
        {
            $find = Property::finder($key, $value)->toArray();
            $x["By $key"] = !empty($find) ? array_merge($find) : $this->notFoundMsg;
            $result += $x;
        }

        return $result;
    }

    private function findTogether(array $data)
    {
        $find = Property::findByArrayOfFields($data)->toArray();
        $result = !empty($find) ? $find : $this->notFoundMsg;

        return ['search with all parameters'=>$result];
    }

    public function __construct()
    {
        $this->allowedKeys = Property::loadColumnNames();
    }
}