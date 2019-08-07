<?php

namespace App\Http\Controllers\API;

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
            $user->password = bcrypt($request->password); //password through bcrypt
            $user->save();


            $response = $this->get_token($request->email, $request->password);
            return response(
                [
                    'success'=>true,
                    'data' => json_decode((string)$response->getBody(), true),
                    'message'=>'User registered successfully'
                ],
                200);
        }
        else
            return response()->json(
                [
                    'success'=>false,
                    'message'=>'User with such e-mail already exists'
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
                    'success'=>false,
                    'status' => 'error',
                    'message' => 'User not found. Check credentials'
                ],
                404);
        }
        if (Hash::check($request->password, $user->password)) { //checks passwords

            $response = $this->get_token($request->email, $request->password);
            $name = '';
            if(!empty($user->name) || !is_null($user->name)){
                $name = $user->name;
            }
            return response([
                'success'=>true,
                'data' => json_decode((string)$response->getBody(), true),
                'name'=> $name
            ],
                201);
        }
        return response()->json(
            [
                'success'=>false,
                'status' => 'error',
                'message' => 'E-mail or password is wrong. Check credentials'
            ],
            404);

    }

    public function change_password(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
            'newpassword' => 'required'
        ]);

        $user = User::where('email', $request->email)->first(); //gets user with email
        if (!$user) {
            //returns error if user does not exists
            return response()->json(
                [
                    'success'=>false,
                    'status' => 'error',
                    'message' => 'User not found. Check credentials'
                ],
                404);
        }
        if (Hash::check($request->password, $user->password)) { //checks passwords

            $user->password = bcrypt($request->newpassword); //password through bcrypt
            $user->save();

            $response = $this->get_token($request->email, $request->newpassword);

            $name = '';
            if(!empty($user->name) || !is_null($user->name)){
                $name = $user->name;
            }
            return response([
                'success'=>true,
                'data' => json_decode((string)$response->getBody(), true),
//                'role_id' => $user->role_id,
                'name'=> $name
            ],
                201);
        }
        return response()->json(
            [
                'success'=>false,
                'status' => 'error',
                'message' => 'E-mail or password is wrong. Check credentials'
            ],
            404);

    }

    public function user_show()
    {
        $user = auth('api')->user();
//        $user = User::find($uid);
        if ($user) {
            return response()->json(
                [
                    'success'=>true,
                    'data' => $user,
                    'message' => 'User data retrieved successfully'
                ],
                200);
        }

        return response()->json(
            [
                'success'=>false,
                'data' => [],
                'message' => 'User is not found'
            ],
            404);
    }

    public function users()
    {
        $user = User::where('id', '<>', auth('api')->user()->id)->get();
        if ($user) {
            return response()->json(
                [
                    'success'=>true,
                    'data' => $user,
                    'message' => 'Users data retrieved successfully'
                ],
                200);
        }

        return response()->json(
            [
                'success'=>false,
                'data' => [],
                'message' => 'There is no any user'
            ],
            404);
    }











}
