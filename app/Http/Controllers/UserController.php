<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::orderBy(request('column') ? request('column') : 'updated_at', request('direction') ? request('direction') : 'desc')
            ->search(request('search'))
            ->paginate());
    }

    public function search()
    {
        return response()->json(User::search(request('search'))->get());
    }


    public function store(Request $request, User $oldData)
    {
        $input = $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => '',
            'password_confirmation' => 'same:password',
            'file' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        if (trim($request->password) == '') {
            $input = $request->except('password');
        } else {
            $input['password'] = bcrypt($request->password);
        }
        $input = $request->except('file');
        $user = User::updateOrCreate(
            ['id' => $request->id, 'email' => $input['email']],
            $input
        );
        if ($request->roles) {
            $user->syncRoles($request['roles']);
        }


        if ($file = $request->file('file')) {
            $manager = new ImageManager();
            $name = time() . $file->getClientOriginalName();
            list($width, $height, $type, $attr) = getimagesize($file);

            $resize = $manager->make($request->file('file')->getRealPath())
                ->fit(round($width/3), round($height/3));
            $path = "upload/100_{$name}";

            $resize->save(public_path($path));
            $file->move('upload', $name);


            if ($request->id && $request->image['url']) {
                if (file_exists(public_path('/upload/' . $request->image['url']))) {
                    unlink(public_path('/upload/' . $request->image['url']));
                }
                if (file_exists(public_path('/upload/100_' . $request->image['url']))) {
                    unlink(public_path('/upload/100_' . $request->image['url']));
                }

                $user->image()->update(['url' => $name, 'data->type' => 'profile']);
            } else {
                $user->image()->create(['url' => $name, 'data' => ['type' =>'profile']]);
            }
        }
        return response()->json($user, 201);
    }


    public function create()
    {
        $roles = \App\Role::with('permissions')->get();
        return response()->json(['roles' => $roles], 200);
    }

    public function show(User $user)
    {
        $roles = \App\Role::all();
        $permissions = \App\Permission::all();
        return response()->json(['user' => User::where('id', $user->id)->with('roles', 'permissions', 'image')->first(), 'roles' => $roles, 'permissions' => $permissions], 200);
    }

    public function update(Request $request, User $user)
    {
        $input = $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => '',
            'password_confirmation' => 'same:password'
        ]);
        if (trim($request->password) == '') {
            $input = $request->except('password');
        } else {
            $input['password'] = bcrypt($request->password);
            $user->password = $input['password'];
        }
        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->save();
        return response()->json($user, 201);
    }

    public function destroy(User $user)
    {
        return response()->json($user->delete());
    }
}
