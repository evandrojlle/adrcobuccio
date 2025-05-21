<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\User;
use App\Traits\Log;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use Log;

    /**
     * User Authentication
     *
     * @param AuthRequest $request
     * @return JsonResponse
     */
     public function auth(AuthRequest $request): JsonResponse
     {
         try {
             $credentials = $request->validated();
             $dataUser = User::userByEmail($credentials['email'], true);
             if (! $dataUser) {
                return response()->json([
                    'success' => false,
                    'message' => __('No user found with this email. Register to continue.'),
                    'data' => [],
                ], 200);
            }

            if (!$dataUser || !Hash::check($credentials['password'], $dataUser['password'])) {
                return response()->json([
                    'success' => false,
                    'message' => __('Invalid credentials!'),
                    'data' => [],
                ], 200);
            }

            $token = $dataUser->createToken('token', ['*'], Carbon::now()->addMinutes(config('sanctum.expiration')))->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $dataUser->only(['id', 'name', 'email']),
                'message' => __('Authentication successfully!'),
            ]);
        } catch (\Exception $e) {
            Log::save('error', $e);
    
            return response()->json([
                'success' => false,
                'message' => __('Ops! An error occurred while performing this action.'),
                'data' => [],
            ], 200);
        }
     }
}
