<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class Guestbook extends BaseModel
{
    use HasFactory;

    protected $table = 'guestbook';

    public static function indexItemListConfig()
    {
        $clsDataGrid = array();
        array_push(
            $clsDataGrid,
            array(
                'field' => 'email',
                'title' => "Email",
                'style' => "width='15%'"
            )
        );
        array_push(
            $clsDataGrid,
            array(
                'field' => 'phone',
                'title' => "Số điện thoại",
                'style' => "width='15%'"
            )
        );
        
        $object = array(
            'name'  => 'guestbook',
            'title' => 'Sổ lưu bút',
            'clsDataGrid' => $clsDataGrid
        );
        return $object;
    }
}
