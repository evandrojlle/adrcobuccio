<?php

namespace App\Http\Controllers;

use App\Http\Requests\WalletRequest;
use App\Models\BankStatement;
use App\Models\Wallet;
use App\Traits\Log;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class WalletsController extends Controller
{
    use Log;

    /**
     * Self deposite money to wallet
     *
     * @param WalletRequest $request - Request form data
     * @return JsonResponse
     */
    public function selfDeposite(WalletRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
dd($validated);
            $wallet = new Wallet();
            $wallet->created_at = Carbon::now()->format('Y-m-d H:i:s');
            $wallet->updated_at = null;
            foreach ($validated as $key => $value) {
                $wallet->$key = $value;
            }

            $wallet->save();
            if (! $wallet->id) {
                return response()->json([
                    'success' => false,
                    'message' => __('An error occurred while deposite money to wallet.'),
                    'data' => []
                ], 400);
            }

            $bankStatement = new BankStatement();
            $bankStatement->wallet_id = $wallet->id;
            $bankStatement->transfer_id = $wallet->owner_id;
            $bankStatement->type_transaction = 'CREDIT';
            $bankStatement->amount_transaction = $amount;
            $bankStatement->created_at = Carbon::now()->format('Y-m-d H:i:s');
            $bankStatement->updated_at = null;
            $bankStatement->save();
            if (! $bankStatement->id) {
                return response()->json([
                    'success' => false,
                    'message' => __('An error occurred while saving the transaction data.'),
                    'data' => []
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => __('Money deposite successfully.'),
                'data' => [
                    'id' => $wallet->id,
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
