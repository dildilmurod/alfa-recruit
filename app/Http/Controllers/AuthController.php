<?php

namespace App\Http\Controllers\Api;

use App\SmsService;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct()
    {

        //$this->middleware('api-auth', ['except' => []]);

    }

    protected function get_token($email, $password){
        $http = new Client();
        $response = $http->post(url('oauth/token'), [ //forms token
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => 2,
                'client_secret' => 'oCJGvGmedqxhMoLP8VrQrmzWJv456l9H5wCMgRvC',
                'username' => $email,
                'password' => $password,
                'scope' => '',
            ],
        ]);
        return $response;
    }

    //current function registers users
    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'name' => 'required',
            'password' => 'required',
        ]);

        if (empty(User::where('email', $request->email)->first())){
            $user = User::firstOrNew(['email' => $request->email]); //checks whether it is new user with this email
            $user->name = $request->name;
            $user->email = $request->email;
//            $user->status = 1;
            $user->password = bcrypt($request->password); //password through bcrypt
            $user->save();

            $response = $this->get_token($request->email, $request->password);

            return response(
                [
                    'data' => json_decode((string)$response->getBody(), true)
                ],
                200);
        }
        else
            return response()->json(
                [
                    'error'=>'User with such e-mail already exists'
                ],
                401);


    }
    //logins user
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first(); //gets user with email
        if (!$user) {
            //returns error if user does not exists
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'User not found. Check credentials'
                ],
                404);
        }
        if (Hash::check($request->password, $user->password)) { //checks passwords

            $response = $this->get_token($request->email, $request->password);

            return response([
                'data' => json_decode((string)$response->getBody(), true),
//                'role_id' => $user->role_id,
                'name'=> $user->name,
            ],
                201);
        }
        return response()->json(
            [
                'status' => 'error',
                'message' => 'E-mail or password is wrong. Check credentials'
            ],
            404);

    }










}
