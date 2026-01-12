<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->selectedMainMenu = 'user';

        parent::__construct();

        $this->user = $user;
        $this->middleware('can:user');
    }

    public function index(Request $request)
    {
        $this->selectedSubMenu('admin_user');
        $user = Auth::user();

        $query = User::where('id', '<>', 1);
        if (!$user->isSuperAdmin()) {
            $query->where('id', $user->id);
        }

        $users = $query->orderBy('name')->paginate();

        return view('admin.user.index', compact('users'));
    }

    public function edit(User $user)
    {
        $ability = $user->exists ? 'update' : 'create';
        $this->authorize($ability, $user);

        $permissions = config('permission');
        $user_permissions = data_get($user, 'user_permissions');
        $user_permissions = json_decode($user_permissions);

        return view('admin.user.create', compact('user', 'permissions', 'user_permissions'));
    }

    public function save(User $user, Request $request)
    {
        $ability = $user->exists ? 'update' : 'create';
        $this->authorize($ability, $user);

        $roles = [
            'username' => 'required|min:3|unique:users,username,' . $user->id,
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email, ' . $user->id,
            'phone' => 'nullable|string|unique:users,phone, ' . $user->id,
            'user_permissions' => 'array'
        ];

        if (!$user->exists) {
            $roles['password'] = 'required|min:6';
        }

        $this->validate($request, $roles);

        if ($request->get('password')) {
            // Update user password
            $user->password = Hash::make($request->get('password'));
        }

        $user_permissions = $request->get('user_permissions', []);

        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->phone = $request->get('phone');
        $user->username = $request->get('username');
        $user->avatar = $request->get('avatar');
        if (Auth::user()->isSuperAdmin()) {
            $user->state = $request->get('state', 0);
            $user->super_admin = $request->get('super_admin', 0);
            $user->user_permissions = json_encode($user_permissions);
        }

        $user->save();

        return redirect()->route('admin_user')->with('success', 'Cập nhật thông tin thành công');
    }

    public function delete(Request $request, User $user)
    {
        $this->authorize('delete', $user);
        if (Auth::id() === $user->id) {
            abort('403');
        }
        User::destroy($user->id);

        return redirect()->route('admin_user')->with('success', 'Xóa user thành công');
    }
}
