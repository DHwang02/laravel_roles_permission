<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            "name"=>"required | string",
            "email"=>"required | string | email | unique:users",
            "password"=>"required | confirmed" //Password Comfirmation
        ]);

        if ($validator->fails()){
            $errorMessage = $validator->errors()->first();
            $response = [
                'status' => false,
                'message' => $errorMessage,
            ];
            return response()->json($response, 401);
        }

        User::create([
            "name"=>$request->name,
            "email"=>$request->email,
            "password"=> Hash::make($request->password)
        ]);

        //Response
        return response()->json([
            "status"=>true,
            "message"=>"User registered successfully"
        ]);
    }

    public function login(Request $request){

        $validator = Validator::make($request->all(),[
            "email"=>"required",
            "password"=>"required"
        ]);

        if ($validator->fails()){
            $errorMessage = $validator->errors()->first();
            $response = [
                'status' => false,
                'message' => $errorMessage,
            ];
            return response()->json($response, 401);
        }

        //check user by email
        $user = User::where("email", $request->email)->first();

        //check user by password
        if(!empty($user)){

            if(Hash::check($request->password, $user->password)){
                
                //Login is ok
                $tokenInfo = $user->createToken("token");

                $token = $tokenInfo->plainTextToken; //Token value
                
                return response()->json([
                    "status"=>true,
                    "message"=>"Login successfully",
                    "token"=>$token
                ]);
            }else{

                return response()->json([
                    "status"=>false,
                    "message"=>"Password didn't match."
                ]);
            }
        }else{

            return response()->json([
                "status"=>false,
                "message"=>"Invalid credentials"
            ]);
        }
    }

    //Profile(GET, Auth Token)
    public function profile(){

        $userData = auth()->user();

        return response()->json([
            "status"=>true,
            "message"=>"Profile Information",
            "data"=>$userData
        ]);
    }

    //Logout(GET, Auth Token)
    public function logout(){
        
        //To get all tokens of logged in user and delete that
        request()->user()->tokens()->delete();

        return response()->json([
            "status"=>true,
            "message"=>"User logged out"
        ]);
    }

    public function refreshToken(){

        $tokenInfo = request()->user()->createToken("newtoken");

        $newToken = $tokenInfo->plainTextToken; //Token value

        return response()->json([
            "status"=> true,
            "message"=> "Refresh token",
            "access_token"=> $newToken
        ]);
    }

}
