<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function updateUser($user_id,Request $request){
        $request['user_id']= $user_id;
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
            ]);
            if($validator->fails()){
                return response()->json(["error"=>$validator->errors()], 400);
            }
            $user = User::find($user_id)->update($request->all());
            return response()->json([
                'status' => true,
                'message' => "success updated",
            ], 200);
        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 401);
        }
    }
    public function login(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);
            if ($validator->fails()) {
                return response()->json(["error"=>$validator->errors()], 400);
            }
            $user = User::whereEmail($request->email)->first();
            if(isset($user)){
                $token = $user->createToken('furniture')->plainTextToken;
            }

            if (!isset($user)) {
                // If authentication fails
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to authenticate after registration',
                ], 401);
            }
            return response()->json([
                'user' => $user,
                'token' => $token
            ]);
        }catch (\Exception $exception){
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 401);
        }
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
//        $token = $user->createToken('furniture')->plainTextToken;
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
//            'token' => $token
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth()->user());
    }
}
