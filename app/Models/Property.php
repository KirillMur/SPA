<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Property extends Model
{
    protected $table = 'property';
    public $timestamps = false;

    //поиск по одной колонке, при параметре $strict = false использует нестрогое соответствие
    public static function findField(string $key, string $value, bool $strict = true) : object
    {
        $operator = "=";

        if (!$strict){
            $value = "%$value%";
            $operator = "like";
        }
        return self::where($key, $operator, $value)
            ->get();
    }

    //поиск по диапазону значений
    public static function findRange(int $min, int $max, string $column) : array
    {
        return self::whereBetween($column, [$min, $max])
            ->get()
            ->toArray();
    }

    //ищет по пассиву значений; для значений 'price_min' и 'price_max' отдельно производится поиск в колонке 'price'
    //по диапазону (в одном запросе), имя при этом ищется по нестрогому соответствию
    public static function findByArrayOfFields(array $data) : object
    {
        return self::where($data['main'])
            ->where('name', 'like', '%' . $data['name']['name'] . '%')
            ->whereBetween('price', [$data['price']['price_min'], $data['price']['price_max']])
            ->get();
    }

    //получение дефолтного значения для поиска (вызывается в случае отсутствующего входящего значения 'price_max')
    public static function getMaxPrice() : int
    {
        return self::max('price');
    }

    //подготавливает поля для сравнения корректности входных данных
    public static function loadColumnNames() : array
    {
        $result = array_flip(collect(self::first())
            ->except('id')
            ->keys()
            ->toArray());

            if ($result['price']) {
                unset($result['price']);
                $result['price_min'] = '';
                $result['price_max'] = '';
        }

        return $result;
    }
}
