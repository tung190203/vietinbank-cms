<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FileManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:file_manager');
    }

    protected $selectedMainMenu = 'file_manager';

    public function index()
    {
        return view('admin.filemanager.index');
    }

    public function ckfinder()
    {
        return view('admin.filemanager.ckfinder');
    }
}
