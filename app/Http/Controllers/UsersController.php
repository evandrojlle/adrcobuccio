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
     * Get User by id
     *
     * @param int $id - user id
     * @return JsonResponse
     */
    public function get(int $id)
    {
        try {
            $user = User::filters(['id' => $id])->first();
            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => __('User not found.'),
                    'data' => []
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => __('Show item found.'),
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            Log::save('error', $e);

            return response()->json([
                'success' => false,
                'message' => 'error',
                'error' => __('Ops! An error occurred while performing this action.')
            ], 500);
        }
    }

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

    /**
     * Update User
     *
     * @param UserRequest $request - Request form data
     * @return JsonResponse
     */
    public function update(UserRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $id = $validated['user_id'];
            $user = User::getById($id);
            if (! $user) {
                return response()->json([
                    'message' => __('User not found.'),
                    'data' => []
                ], 403);
            }

            $user->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            foreach ($validated as $key => $value) {
                if ($key !== 'user_id') {
                    $user->$key = $value;
                }
            }

            if (! $user->save()) {
                return response()->json([
                    'success' => false,
                    'message' => __('An error occurred while saving the user.'),
                    'data' => []
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => __('User updated successfully.'),
                'data' => [
                    'id' => $id,
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
