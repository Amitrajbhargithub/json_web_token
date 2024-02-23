<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
class UserController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['userLogin']]);
    // }
    public function hello() {
        dd("helo");
    }
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:5'
        ]);
        if($validate->fails()) {
            return response()->json($validate->errors(),400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'User Created Successfully',
            'user' => $user
        ]);
    }


    public function userLogin(Request $request) {
        $validate = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|min:5'
        ]);
        if($validate->fails()) {
            return response()->json($validate->errors(),400);
        }

        if(!$token = auth()->attempt($validate->validated())) {
            return response()->json(['error' => 'Unauthrized']);
        }

        return $this->responedWithToken($token);
    }

    protected function responedWithToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_id' => auth()->factory()->getTTL()*60

        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        return response()->json(auth()->user());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function refresh()
    {
        return $this->responedWithToken(auth()->refresh());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function logout()
    {
        auth()->logout();
        return response()->json(['message'=>'User Loggedout Successfully.']);
    }
}
