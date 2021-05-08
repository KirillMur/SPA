<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Property extends Model
{
    protected $table = 'property';
    public $timestamps = false;

    public static function finder(string $key, string $value)
    {
        return self::where($key, $value)
            ->get();
    }

    public static function findByArrayOfFields(array $data)
    {
        return self::where($data)
            ->get();
    }

    public static function loadColumnNames()
    {
        return array_flip(collect(self::first())->keys()->toArray());
    }
}
