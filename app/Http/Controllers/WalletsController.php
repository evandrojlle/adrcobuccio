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
     * Self Credit to wallet
     *
     * @param WalletRequest $request - Request form data
     * @return JsonResponse
     */
    public function selfCredit(WalletRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated(); // Validate request data.
            $wallet = $this->walletTransaction($validated); // Save transaction into wallet.
            if (! $wallet->id) { // if failed to save wallet transaction, return error response.
                return response()->json([
                    'success' => false,
                    'message' => __('An error occurred while deposite money to wallet.'),
                    'data' => []
                ], 400);
            }

            // Transform wallet data to array and add transaction type and amount transaction.
            $walletTransaction = $wallet->toArray();
            $walletTransaction['type_transaction'] = 'CREDIT';
            $walletTransaction['amount_transaction'] = $validated['amount_transaction'];
            $bankStatement = $this->bankStatement($walletTransaction); // Save statement into bank statement.
            if (! $bankStatement->id) { // if failed to save bank statement, return error response.
                return response()->json([
                    'success' => false,
                    'message' => __('An error occurred while saving the transaction data.'),
                    'data' => []
                ], 400);
            }

            // return success response with wallet data.
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
            Log::save('error', $e); // Save error log.

            return response()->json([
                'success' => false,
                'message' => __('Ops! An error occurred while performing this action.'),
                'data' => [],
            ], 500);
        }
    }

    /**
     * Save transaction into wallet.
     *
     * @param array $wallet - Wallet data transaction.
     * @return Wallet
     */
    private function walletTransaction(array $wallet): Wallet
    {
        // Check if wallet exists. If exists, update the amount and update_at, else create a new data wallet.
        $wallet = Wallet::firstByFilters(['owner_id' => $wallet['owner_id']]);
        if ($wallet) {
            $amount = $wallet->amount + $wallet['amount_transaction'];
            $wallet->amount = $amount;
            $wallet->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $wallet->save();
        } else {
            $wallet = new Wallet();
            $wallet->created_at = Carbon::now()->format('Y-m-d H:i:s');
            $wallet->updated_at = null;
            
            foreach ($wallet as $key => $value) {
                $key = ($key == 'amount_transaction') ? 'amount' : $key;
                $wallet->$key = $value;
            }

            $wallet->save();
        }

        return $wallet;
    }

    /**
     * Save transaction into bank statement.
     *
     * @param array $wallet - Wallet data transaction.
     * @return BankStatement
     */
    private function bankStatement(array $wallet): BankStatement
    {
        // Save statement into bank statement.
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
