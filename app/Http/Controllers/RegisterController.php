<?php

namespace App\Http\Controllers;

// use App\Http\Requests\RegistrationRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;

class RegisterController extends Controller
{
    //
    public function register(Request $request){
        $validator = Validator::make($request->only(["name", "email", "password", "password_confirmation"]), [
            'name' => 'required|max:10|unique:users',
            'email' => 'required|email|unique:users|max:50',
            'password' => 'required|confirmed|string|min:6|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => '成功しました'
        ], 201);
    }
}
