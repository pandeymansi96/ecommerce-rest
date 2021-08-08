<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json(['data' => $users], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

        return response()->json(['data' => $user], 201);
    }

    
    public function show(User $user)
    {
        return response()->json(['data' => $user], 200);
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
                return response()->json(['error'=> 'Only verified users can become admin','code'=> 409], 409);
            }
            $user->admin = $request->admin;
        }

        if(! $user->isDirty()){
            return response()->json(['error'=> 'You need to justify a different value to update', 'code' => 422], 422);
        }

        $user->save();

        return response()->json(['data' => $user], 200);
    }

    
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['data'=> $user], 200);
    }
}
