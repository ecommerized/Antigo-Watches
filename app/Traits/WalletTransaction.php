<?php

namespace App\Traits;

use App\CentralLogics\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use function App\CentralLogics\translate;

trait WalletTransaction
{
    public function customerCreditWalletTransactionsForOrderComplete($customer, $order)
    {
        $customerSetupWalletEarningConfig = Helpers::get_business_settings('customer_setup_wallet_earning');
        if ($customerSetupWalletEarningConfig && array_key_exists('status', $customerSetupWalletEarningConfig) && (int)$customerSetupWalletEarningConfig['status'] == 1) {
            $customerFcmToken = $customer ? $customer->cm_firebase_token : null;
            $walletBalance = $customer?->userAccount?->wallet_balance ?? 0;
            $orderWiseEarningPercentage = (array_key_exists('order_wise_earning_percentage', $customerSetupWalletEarningConfig) && $customerSetupWalletEarningConfig['order_wise_earning_percentage']) ? $customerSetupWalletEarningConfig['order_wise_earning_percentage'] : 0;
            $calculateEarningAmount = ($order->order_amount * ($orderWiseEarningPercentage / 100));
            $this->walletTransactionCreate(model: $customer, walletBalance: $walletBalance, amount: $calculateEarningAmount, type: 'wallet_reward', reference: $order->id, description: 'Reward for order ' . $order->order_number, order: $order, customerFcmToken: $customerFcmToken);
        }
    }

    public function customerDebitWalletTransactionsForOrderPlace($customer, $order)
    {
        $walletBalance = $customer?->userAccount?->wallet_balance ?? 0;
        $this->walletTransactionCreate(model: $customer, walletBalance: $walletBalance, amount: $order->order_amount, type: 'wallet_payment', direction: 'debit', reference: $order->id, description: 'Wallet payment for order ' . $order->order_number);
    }


    protected function walletTransactionCreate($model, $walletBalance, $amount, $type, $direction = 'credit', $reference = null, $method = null, $description = null, $status = 'completed', $order = null, $customerFcmToken = null)
    {
        $opening = $walletBalance;
        if ($direction == 'credit') {
            $closing = $opening + $amount;
        } else {
            $closing = $opening - $amount;
        }

        // Update model's wallet_balance
        $model->userAccount->update(['wallet_balance' => $closing]);
        if ($customerFcmToken && $order) {
            try {
                $value = Helpers::get_business_settings('wallet_rewarded_message');
                if ($value != null && $value['status'] == 1 && $value['message'] != null && $value['message'] != ['']) {
                    $data = [
                        'title' => translate('Wallet Rewarded'),
                        'description' => $value['message'],
                        'order_id' => $order['id'],
                        'image' => '',
                        'type' => 'wallet_reward',
                    ];
                    Helpers::send_push_notif_to_device($customerFcmToken, $data);
                    sleep(1);
                }
            } catch (\Exception $e) {
            }
        }


        // Create the wallet transaction record
        return $model->walletTransactions()->create([
            'type' => $type,
            'direction' => $direction,
            'amount' => $amount,
            'opening_balance' => $opening,
            'closing_balance' => $closing,
            'reference' => $reference,
            'method' => $method,
            'description' => $description,
            'status' => $status,
        ]);
    }

}
