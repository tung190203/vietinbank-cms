<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Vrarea;
use Illuminate\Http\Request;

class VrareaController extends Controller
{
     public function index()
    {
        $areas = Vrarea::query()->where('state', 1)->orderBy('id', 'ASC')
            ->get([
                'id',
                'name',
                'slug',
                'media_index',
                'skin_label',
            ]);

        return response()->json([
            'status' => true,
            'data' => $areas,
        ]);
    }
}
