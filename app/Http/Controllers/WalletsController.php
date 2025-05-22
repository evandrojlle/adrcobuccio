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
    public function selfCredit(WalletRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $wallet = $this->walletTransaction($validated);
            if (! $wallet->id) {
                return response()->json([
                    'success' => false,
                    'message' => __('An error occurred while deposite money to wallet.'),
                    'data' => []
                ], 400);
            }

            $walletTransaction = $wallet->toArray();
            $walletTransaction['type_transaction'] = 'CREDIT';
            $walletTransaction['amount_transaction'] = $validated['amount_transaction'];
            $bankStatement = $this->bankStatement($walletTransaction);
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
                    'amount_transaction' => $validated['amount_transaction'],
                    'amount' => $wallet->amount
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

    private function walletTransaction(array $data): Wallet
    {
        $wallet = Wallet::firstByFilters(['owner_id' => $data['owner_id']]);
        if ($wallet) {
            $amount = $wallet->amount + $data['amount_transaction'];
            $wallet->amount = $amount;
            $wallet->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $wallet->save();
        } else {
            $wallet = new Wallet();
            $wallet->created_at = Carbon::now()->format('Y-m-d H:i:s');
            $wallet->updated_at = null;
            
            foreach ($data as $key => $value) {
                $key = ($key == 'amount_transaction') ? 'amount' : $key;
                $wallet->$key = $value;
            }

            $wallet->save();
        }

        return $wallet;
    }

    private function bankStatement(array $wallet): BankStatement
    {
        $bankStatement = new BankStatement();
        $bankStatement->wallet_id = $wallet['id'];
        $bankStatement->transfer_id = $wallet['owner_id'];
        $bankStatement->type_transaction = $wallet['type_transaction'];
        $bankStatement->amount_transaction = $wallet['amount_transaction'];
        $bankStatement->created_at = Carbon::now()->format('Y-m-d H:i:s');
        $bankStatement->updated_at = null;
        $bankStatement->save();

        return $bankStatement;
    }
}
