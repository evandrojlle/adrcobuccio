<?php

namespace App\Http\Controllers;

use App\Http\Requests\WalletRequest;
use App\Models\BankStatement;
use App\Models\User;
use App\Models\Wallet;
use App\Traits\Log;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
            $me = Auth::user(); // Get authenticated user.
            $validated = $request->validated(); // Validate request data.
            $validated['owner_id'] = $me->id; // Set owner id to authenticated user id.
            $wallet = $this->creditTransaction($validated); // Save transaction into wallet.
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
                    'owner' => $me->name,
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
     * Credit from outers users into my wallet.
     *
     * @param WalletRequest $request - Request form data
     * @return JsonResponse
     */
    public function outerCredit(WalletRequest $request): JsonResponse
    {
        try {
            $me = Auth::user(); // Get authenticated user.
            $validated = $request->validated(); // Validate request data.
            $validated['owner_id'] = $me->id; // Set owner id to authenticated user id.
            $outerWallet = Wallet::firstByFilters(['owner_id' => $validated['user_id']]);
            if (! $outerWallet) { // if wallet not found, return error response.
                return response()->json([
                    'success' => false,
                    'message' => __('The wallet not found.'),
                    'data' => []
                ], 400);
            }

            if ($outerWallet->amount < $validated['amount_transaction']) {
                return response()->json([
                    'success' => false,
                    'message' => __('The transaction amount is greater than the source wallet amount.'),
                    'data' => []
                ], 400);
            }

            // Data for debit transaction.
            $dataDebit = [
                'owner_id' => $validated['user_id'],
                'amount_transaction' => $validated['amount_transaction'],
            ];

            $debitWalletTransaction = $this->debitTransaction($dataDebit); // Save debit transaction from source wallet.
            if (! isset($debitWalletTransaction->success)) { // if failed to save wallet transaction, return error response.
                return response()->json([
                    'success' => false,
                    'message' => __('An error occurred while debit amount from source wallet.'),
                    'data' => []
                ], 400);
            }

            // Transform wallet data to array and add transaction type and amount transaction.
            $debitWalletTransaction = $debitWalletTransaction->toArray();
            $debitWalletTransaction['type_transaction'] = 'DEBIT';
            $debitWalletTransaction['amount_transaction'] = $dataDebit['amount_transaction'];
            $bankStatement = $this->bankStatement($debitWalletTransaction); // Save statement into bank statement.
            if (! $bankStatement->id) { // if failed to save bank statement, return error response.
                return response()->json([
                    'success' => false,
                    'message' => __('An error occurred while saving the debit transaction data.'),
                    'data' => []
                ], 400);
            }

            // Data for credit transaction.
            $dataCredit = [
                'owner_id' => $validated['owner_id'],
                'amount_transaction' => $validated['amount_transaction'],
            ];
            $creditWalletTransaction = $this->creditTransaction($dataCredit); // Save credit transaction into wallet.
            if (! $creditWalletTransaction->id) { // if failed to save wallet transaction, return error response.
                return response()->json([
                    'success' => false,
                    'message' => __('An error occurred while credit amount to wallet.'),
                    'data' => []
                ], 400);
            }

            // Transform wallet data to array and add transaction type and amount transaction.
            $creditWalletTransaction = $creditWalletTransaction->toArray();
            $creditWalletTransaction['type_transaction'] = 'CREDIT';
            $creditWalletTransaction['amount_transaction'] = $dataDebit['amount_transaction'];
            $bankStatement = $this->bankStatement($creditWalletTransaction); // Save statement into bank statement.
            if (! $bankStatement->id) { // if failed to save bank statement, return error response.
                return response()->json([
                    'success' => false,
                    'message' => __('An error occurred while saving the credit transaction data.'),
                    'data' => []
                ], 400);
            }

            // return success response with wallet data.
            return response()->json([
                'success' => true,
                'message' => __('Credit received successfully.'),
                'data' => [
                    'id' => $creditWalletTransaction['id'],
                    'owner' => $me->name,
                    'source' => User::find($outerWallet->owner_id)->name,
                    'amount_transaction' => $validated['amount_transaction'],
                    'amount' => $creditWalletTransaction['amount']
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
     * Transfer from my wallet into outer waller user.
     *
     * @param WalletRequest $request - Request form data
     * @return JsonResponse
     */
    public function transfer(WalletRequest $request): JsonResponse
    {
        try {
            $me = Auth::user(); // Get authenticated user.
            $validated = $request->validated(); // Validate request data.
            $validated['owner_id'] = $me->id; // Set owner id to authenticated user id.
            $myWallet = Wallet::firstByFilters(['owner_id' => $validated['owner_id']]);
            if (! $myWallet) { // if wallet not found, return error response.
                return response()->json([
                    'success' => false,
                    'message' => __('The wallet not found.'),
                    'data' => []
                ], 400);
            }

            if ($myWallet->amount < $validated['amount_transaction']) {
                return response()->json([
                    'success' => false,
                    'message' => __('The transaction amount is greater than the my wallet amount.'),
                    'data' => []
                ], 400);
            }

            // Data for credit transaction.
            $dataCredit = [
                'owner_id' => $validated['user_id'],
                'amount_transaction' => $validated['amount_transaction'],
            ];
            $outerCredit = $this->creditTransaction($dataCredit); // Save transaction into wallet.
            if (! $outerCredit->id) { // if failed to save wallet transaction, return error response.
                return response()->json([
                    'success' => false,
                    'message' => __('An error occurred while credit amount into wallet.'),
                    'data' => []
                ], 400);
            }

            // Transform wallet data to array and add transaction type and amount transaction.
            $outerWalletTransaction = $outerCredit->toArray();
            $outerWalletTransaction['type_transaction'] = 'CREDIT';
            $outerWalletTransaction['amount_transaction'] = $validated['amount_transaction'];
            $bankStatement = $this->bankStatement($outerWalletTransaction); // Save statement into bank statement.
            if (! $bankStatement->id) { // if failed to save bank statement, return error response.
                return response()->json([
                    'success' => false,
                    'message' => __('An error occurred while saving the credit transaction data.'),
                    'data' => []
                ], 400);
            }

            // Data for debit my wallet.
            $dataDebit = [
                'owner_id' => $validated['owner_id'],
                'amount_transaction' => $validated['amount_transaction'],
            ];

            $debitWalletTransaction = $this->debitTransaction($dataDebit); // Save debit transaction from my wallet.
            if (! isset($debitWalletTransaction->success)) { // if failed to save wallet transaction, return error response.
                return response()->json([
                    'success' => false,
                    'message' => __('An error occurred while debit amount from my wallet.'),
                    'data' => []
                ], 400);
            }

            // Transform wallet data to array and add transaction type and amount transaction.
            $debitWalletTransaction = $debitWalletTransaction->toArray();
            $debitWalletTransaction['type_transaction'] = 'DEBIT';
            $debitWalletTransaction['amount_transaction'] = $dataDebit['amount_transaction'];
            $bankStatement = $this->bankStatement($debitWalletTransaction); // Save statement into bank statement.
            if (! $bankStatement->id) { // if failed to save bank statement, return error response.
                return response()->json([
                    'success' => false,
                    'message' => __('An error occurred while transfering the amount.'),
                    'data' => []
                ], 400);
            }

            // return success response with wallet data.
            return response()->json([
                'success' => true,
                'message' => __('Transfer successfully.'),
                'data' => [
                    'id' => $outerWalletTransaction['id'],
                    'owner' => User::find($validated['user_id'])->name,
                    'source' => $me->name,
                    'amount_transaction' => $validated['amount_transaction'],
                    'amount' => $outerWalletTransaction['amount']
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
     * Credit transaction into wallet.
     *
     * @param array $dataWallet - Wallet data transaction.
     * @return Wallet
     */
    private function creditTransaction(array $dataWallet): Wallet
    {
        // Check if wallet exists. If exists, update the amount and update_at, else create a new data wallet.
        $wallet = Wallet::firstByFilters(['owner_id' => $dataWallet['owner_id']]);
        if ($wallet) {
            $amount = $wallet->amount + $dataWallet['amount_transaction'];
            $wallet->amount = $amount;
            $wallet->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $wallet->save();
        } else {
            $wallet = new Wallet();
            $wallet->created_at = Carbon::now()->format('Y-m-d H:i:s');
            $wallet->updated_at = null;
            foreach ($dataWallet as $key => $value) {
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

    /**
     * Debit transaction from wallet.
     *
     * @param array $dataWallet - Wallet data transaction.
     * @return Wallet
     */
    private function debitTransaction(array $dataWallet): Wallet
    {
        // Check if wallet exists. If exists, update the amount and update_at, else create a new data wallet.
        $wallet = Wallet::firstByFilters(['owner_id' => $dataWallet['owner_id']]);
        $amount = $wallet->amount - $dataWallet['amount_transaction'];
        $wallet->amount = $amount;
        $wallet->updated_at = Carbon::now()->format('Y-m-d H:i:s');
        if ($wallet->save()) {
            $wallet->success = true;
        }

        return $wallet;
    }
}
