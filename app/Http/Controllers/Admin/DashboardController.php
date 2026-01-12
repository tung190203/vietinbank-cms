<?php

namespace App\Http\Controllers\Admin;

use App\Models\Feedback;
use App\Models\Post;
use App\Models\Project;
use App\Models\Recruitment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        return redirect(route('admin_vr_popup'));
        // $total['product'] = Project::where('state', 1)->count();
        // $total['news'] = Post::where('state', 1)->count();
        // $total['feedback'] = Feedback::count();
        // $total['order'] = Recruitment::count();
        // return view('admin.dashboard.index', compact('total', $total));
    }
}

