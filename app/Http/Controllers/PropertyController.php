<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Void_;
use Symfony\Component\HttpFoundation\Response;

class PropertyController extends Controller
{
    private $allowedKeys;
    private $notJsonMsg = ['error:'=>'no JSON format or empty request'];
    private $incorrectFieldsMsg = 'error, incorrect field name(s)';
    private $notFoundMsg = ['result'=>'not found'];

    public function requestAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if(empty($data)) return new JsonResponse(['No JSON'=>$this->notJsonMsg]);

        $incorrectKeys = array_diff_key($data, $this->allowedKeys); //для вывода названий некорректных полей
        if(!empty($incorrectKeys)) return new JsonResponse([$this->incorrectFieldsMsg => array_keys($incorrectKeys)]);

        array_walk($data, function(&$value){
            return $value = trim($value);
        });

        $notValid = $this->validateInput($data);
        if($notValid) return new JsonResponse(['Error'=>['type error'=>$notValid]]); //валидация полей

        if(!isset($data['price_min'])) $data['price_min'] = 0;
        if(!isset($data['price_max'])) $data['price_max'] = Property::getMaxPrice();

        return new JsonResponse($this->findTogether($data) + $this->findEachField($data));
    }

    private function findEachField(array $data) : array
    {
        $result = [];

        foreach($data as $key=> $value)
        {
            switch($key){
                case 'name': $find = Property::findField($key, $value, false)->toArray(); break;
                case 'price_min': $find = Property::findRange($data['price_min'], $data['price_max'], 'price'); break;
                case 'price_max': continue 2;
                default: $find = Property::findField($key, $value)->toArray();
            }

            $find = $this->unsetIdField($find);
            $key !== 'price_min' ?: $key = 'price';
            $result["By $key"] = + !empty($find) ? array_merge($find) : $this->notFoundMsg;
        }

        return $result;
    }

    private function findTogether(array $data) : array
    {
        $find = Property::findByArrayOfFields($data)->toArray();
        $find = $this->unsetIdField($find);
        $result = !empty($find) ? $find : $this->notFoundMsg;

        return ['Search with all parameters'=>$result];
    }

    //валидация полей
     private function validateInput(array $data)
     {
         foreach ($data as $key=>$value) {
             if ($key === 'name' && !preg_match('/^[\w ]+$/u', $value)) {
                 $errorMsg = "restricted symbol '$value' in field '$key'";
                 break;
             } elseif ($key !== 'name' && !preg_match('/^[\d]+$/', $value)) {
                 $errorMsg = "restricted symbol '$value' in field '$key'; digit allow only";
                 break;
             }
         }

         return isset($errorMsg) ? $errorMsg : null;
     }

    private function unsetIdField(array $data) : array
    {
        foreach ($data as &$item) {
            if($item['id'])
                unset($item['id']);
        }

        return $data;
    }

    public function __construct()
    {
        $this->allowedKeys = Property::loadColumnNames();
    }
}