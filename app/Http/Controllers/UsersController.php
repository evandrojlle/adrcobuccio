<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Traits\Log;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class UsersController extends Controller
{
    use Log;

    /**
     * Register User
     *
     * @param UserRequest $request - Request form data
     * @return JsonResponse
     */
    public function store(UserRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $user = new User();
            $user->created_at = Carbon::now()->format('Y-m-d H:i:s');
            $user->updated_at = null;
            foreach ($validated as $key => $value) {
                $user->$key = $value;
            }

            $user->save();
            if (! $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => __('An error occurred while saving the user.'),
                    'data' => []
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => __('User created successfully.'),
                'data' => [
                    'id' => $user->id,
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::save('error', $e);

            return response()->json([
                'success' => false,
                'message' => __('Ops! An error occurred while performing this action.'),
                'data' => [],
            ], 500);
        }
    }
}
