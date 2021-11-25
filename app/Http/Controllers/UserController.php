<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpParser\Builder\Function_;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $input = $request->all();
        $input['password'] = Hash::make($request->password);
        User::create($input);

        return response()->json([
            'res'=> 'success',
            'message'=> 'Usuario creado correctamente'
        ], status:200);
    }

    public function login (Request $request){
        $user = User::whereEmail($request->email)->first();
        if(!is_null($user) && Hash::check($request->password, $user->password))
        {
            $user->api_token = Str::random(length:100);
            $user->save();

            return response()->json([
                'res'=> 'success',
                'token'=> $user->api_token,
                'message'=> 'Bienvenido al sistema'
            ], status:200);
        } else{
            return response()->json([
                'res'=> 'Error',
                'message'=> 'Email o Password incorrectos'
            ], status:400);
        }
    }

    public function logout (){
        $user = auth()->user();
        $user->api_token = null;
        $user->save();

        return response()->json([
            'res'=> 'success',
            'message'=> 'Adios'
        ], status:200);
    }
}
