<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    private $feedback;

    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
        $this->selectedMainMenu = 'feedback';

        parent::__construct();

        $this->middleware('can:feedback');
    }

    public function index(Request $request)
    {
        $this->selectedSubMenu('feedback');
        $filter['keyword'] = $request->get('keyword');
        $filter['type'] = $request->get('type', -1);

        $query = Feedback::query();
        if ($filter['keyword']) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%' . $filter['keyword'] . '%')
                    ->orWhere('email', 'like', '%' . $filter['keyword'] . '%')
                    ->orWhere('phone', 'like', '%' . $filter['keyword'] . '%');
            });
        }
        if ($filter['type'] > -1) {
            $query->where('type', $filter['type']);
        }
        $feedback = $query->orderBy('id', 'desc')->paginate(20);
        return view('admin.feedback.index', compact('feedback', 'filter'));
    }

    public function edit(Feedback $feedback)
    {
        if ($feedback->exists) {
            $feedback->update(['state' => 1]);
        }
        return view('admin.feedback.create', compact('feedback'));
    }

    public function save(Feedback $feedback, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|string',
        ]);

        $feedback->name = $request->get('name');
        $feedback->email = $request->get('email');
        $feedback->phone = $request->get('phone');
        $feedback->address = $request->get('address');
        $feedback->content = $request->get('content');
        $feedback->save();
        return redirect()->route('admin_feedback');
    }

    public function deleteCheckbox(Request $request)
    {
        $this->validate($request, ['ids' => 'required|array']);

        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->responseJsonBadRequest();
        }

        $this->feedback->destroy($ids);
        return $this->responseJsonOk();
    }
}

