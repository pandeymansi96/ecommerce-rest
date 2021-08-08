<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    
    public function index()
    {
        $users = User::all();
        return $this->showAll($users);
    }


    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:5',
            'email' => 'required|unique:users',
            'password' => 'required|min:8|confirmed'
        ];

        $this->validate($request, $rules);

        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_token'] = User::generateVerificationCode();
        $data['admin'] = User::REGULAR_USER;

        $user = User::create($data);

        return $this->showOne($user, 201);
    }

    
    public function show(User $user)
    {
        return $this->showOne($user);
    }

    
    public function edit($id)
    {
        //
    }

    
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'min:5',
            'email' => 'email|unique:users',
            'password' => 'min:8|confirmed',
            'admin' => 'in:'.User::REGULAR_USER . ', '. User::ADMIN_USER
        ];

        $this->validate($request, $rules);

        if($request->has('name')){
            $user->name = $request->name;
        }

        if($request->has('email')){
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationCode();
            $user->email = $request->email;
        }

        if($request->has('password')){
            $user->password = bcrypt($request->password);
        }

        if($request->has('admin')){
            if(! $user->isVerified()){
                return $this->reportMultipleErrors('Only verified users can become admin', 409);
            }
            $user->admin = $request->admin;
        }

        if(! $user->isDirty()){
            return $this->reportMultipleErrors('You need to justify a different value to update', 422);
        }

        $user->save();

        return $this->showOne($user);
    }

    
    public function destroy(User $user)
    {
        $user->delete();
        return $this->showOne($user, 200);
    }
}
