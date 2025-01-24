<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\LoginUserRequest;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Enums\RoleEnum;

class UserController extends Controller
{
    //Logs in the user and returns a token if credentials are valid.
    public function login(LoginUserRequest $request)
    {
        try {
            $loginUser = User::where('email', $request->email)->first();

            if ($loginUser && Hash::check($request->password, $loginUser->password)) {
                $token = $loginUser->createToken('user-token')->plainTextToken;
            } else {
                return response()->json([
                    'message' => 'Invalid Credentials',
                ], 401);
            }

            return response()->json([
                'message' => 'The user is logged in successfully',
                'token' => $token,
                'data' => new UserResource($loginUser),
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'message' => 'There was an error while logging the user',
                'code' => $e->getCode(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    // Logs out the authenticated user by deleting their current access token.
    public function logout()
    {
        try {
            $user = Auth::user();

            $user->currentAccessToken()->delete();

            return response()->json([
                'message' => 'The user is logged out successfully',
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'message' => 'There was an error while logging out the user',
                'code' => $e->getCode(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    // Returns a list of user roles with their labels and corresponding values.
    public function userRoles()
    {
        $roles = [
            [
                'label' => RoleEnum::getRoleLabel(RoleEnum::Admin->value),
                'role' => RoleEnum::Admin->value
            ],
            [
                'label' => RoleEnum::getRoleLabel(RoleEnum::Author->value),
                'role' => RoleEnum::Author->value
            ],
        ];

        return response()->json(['data' => $roles], 200);
    }
}
