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
    private $notJsonMsg = ['unsupported format' => 'no JSON format or empty request'];
    private $incorrectFieldsMsg = 'incorrect field name(s)';
    private $notFoundMsg = ['result' => 'not found'];

    //обработчик запроса
    public function requestAction(Request $request)
    {
        //проверяет входящие данные - ключи и значения
        $data = $this->requestValidator($request);
        if (isset($data['error'])) return new JsonResponse(['Error'=>$data['error']], 400);

        //подготавливает значения для Property::findRange(), приводим к числовому виду
        //и формируем базовые значния в случае отсутствия на входе
        $data = $this->preparePriceValues($data);

        //если все проврки пройдены, ищет и выводит результаты
        return new JsonResponse($this->findTogether($data) + $this->findEachField($data));
    }

    public function requestValidator($request) : array
    {
        //преобразовывает вводные данные в php-массив и проверяет является ли данные соответствующими формату JSON
        $data = json_decode($request->getContent(), true);
        if (empty($data)) return ['error'=>$this->notJsonMsg];

        //сравнивает имена ключей вводных данных с верными
        $incorrectKeys = array_diff_key($data, $this->allowedKeys);
        if (!empty($incorrectKeys)) return ['error'=>[$this->incorrectFieldsMsg=>array_keys($incorrectKeys)]];

        //удвляет пробелы в каждом значении
        array_walk($data, function(&$value){
            return $value = trim($value);
        });

        //проводит валидацию значений полей в соответствии формату (для "name" алфавитно-цифровые
        //и цифровые целочисленные для остальных)
        $notValid = $this->validateInput($data);
        if ($notValid) return ['error'=>['type error'=>$notValid]];

        return $data;
    }

    //добавляет поля price_min и price_max при их отсутствии на входе
    private function preparePriceValues($data) : array
    {
        $data['price_min'] = isset($data['price_min']) ? (int) $data['price_min'] : false;
        $data['price_max'] = isset($data['price_max']) ? (int) $data['price_max'] : Property::getMaxPrice();

        return $data;
    }

    //осуществляет поиск по входным полям (ключам) индивидуально
    private function findEachField(array $data) : array
    {
        $result = [];

        foreach ($data as $key => $value) {
            switch($key) {
                case 'name': $find = Property::findField($key, $value, false)->toArray(); break;
                case 'price_min' && $value === false: continue 2;
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

    //поиск по всем входным данным одновременно: по диапазону значений цены, по нестрогому соответствию имени и
    //строгому соответствию остальных данных
    private function findTogether(array $data) : array
    {
        //готовим данные для поиска одновременно по всем полям
        $data = $this->arrayDivideMainPrice($data);

        $result = Property::findByArrayOfFields($data)->toArray();
        $result = $this->unsetIdField($result);
        $result = !empty($result) ? $result : $this->notFoundMsg;

        return ['Search with all parameters' => $result];
    }

    //разбиваем данные на 3 подмассива: 'price' - как данные для поиска по интервалу значений,
    //'name' для поиска нестрогого соответствия и 'main' - остальные
    private function arrayDivideMainPrice(array $data) : array
    {
        $price['price'] = ['price_min' => $data['price_min'], 'price_max' => $data['price_max']];
        $name['name'] = ['name' => $data['name']];
        $main['main'] = array_diff_assoc($data, $price['price'], $name['name']);

        return array_merge($name, $main, $price);
    }

    //валидация полей, отдельно для поля "name". Возвращает null если проверки пройдены,
    //иначе ошибку с указанием полной строки
    private function validateInput(array $data)
     {
         foreach ($data as $key=>$value) {
             if ($key === 'name' && !preg_match('/^[\w ]+$/u', $value)) {
                 $errorMsg = "restricted symbol '$value' in field '$key'";
                 break;
             } elseif ($key !== 'name' && !preg_match('/^[\d]+$/', $value)) {
                 $errorMsg = "restricted symbol '$value' in field '$key'; integer digits allow only";
                 break;
             }
         }

         return isset($errorMsg) ? $errorMsg : null;
     }

    //удаляет поле "id" из результатов поиска
    private function unsetIdField(array $data) : array
    {
        foreach ($data as &$item) {
            if ($item['id'])
                unset($item['id']);
        }

        return $data;
    }

    //получает массив существующих полей в таблице БД, заменяя поле 'price' полями 'price_min' и 'price_max'
    public function __construct()
    {
        $this->allowedKeys = Property::loadColumnNames();
    }
}