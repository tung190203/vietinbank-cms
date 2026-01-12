<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Country extends Model
{
    protected $table = 'countries';

    public static function getAll()
    {
        $countries = Country::where('state', 1)->get();
        return $countries;
    }
}
