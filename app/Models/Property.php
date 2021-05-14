<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class Property extends Model
{
    protected $table = 'property';
    public $timestamps = false;

    public static function findField(string $key, string $value, bool $strict = true) : object
    {
        $operator = "=";

        if(!$strict){
            $value = "%" . $value . "%";
            $operator = "like";
        }
        return self::where($key, $operator, $value)
            ->get();
    }

    public static function findRange(int $min, int $max, string $column) : array
    {
        return self::whereBetween($column, [$min, $max])
            ->get()
            ->toArray();
    }

    public static function findByArrayOfFields(array $data) : object
    {
        $data = self::exceptKeys($data, 'name');
        $whereData = $data;
        unset($whereData['price_min'], $whereData['price_max']);

        return self::where($whereData)
            ->whereBetween('price', [$data['price_min'], $data['price_max']])
            ->get();
    }

    //ищет значение поля $key по нестрогому соответствию
    public static function findContains(string $key, string $value) : object
    {
        return self::where("$key", 'like', "%$value%")
            ->get('name');
    }

    public static function getMaxPrice() : string
    {
        return self::max('price');
    }

    //перезаписывает значение поля $key в массиве на значение, найденное по нестрогому соответствию
    private static function exceptKeys(array $data, string $key) : array
    {
        if(isset($data[$key])){
            $nameFieldObj = self::findContains($key, $data[$key])
                ->toArray();

            $nameField = array_shift($nameFieldObj);
            !isset($nameField[$key]) ?: $data[$key] = $nameField[$key]; //если не найдено, оставляем без изменений
        }
        return $data;
    }

    public static function loadColumnNames() : array
    {
        $result = array_flip(collect(self::first())
            ->except('id')
            ->keys()
            ->toArray());

            if($result['price']) {
                unset($result['price']);
                $result += ['price_min'=>''];
                $result += ['price_max'=>''];
        }

        return $result;
    }
}
