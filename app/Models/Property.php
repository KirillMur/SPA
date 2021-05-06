<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Property extends Model
{
//    use HasFactory;
//    use RefreshDatabase;

    protected $table = 'property';
    public $timestamps = false;
}
