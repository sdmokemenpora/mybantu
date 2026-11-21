<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Midtrans\Config;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;

class MidtransCallbackController extends Controller
{
    /**
     * Handle webhook HTTP notification dari Midtrans
     */
    public function notification(Request $request)
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');

        try {
            $notif = new Notification();
            $transactionStatus = $notif->transaction_status;
            $orderId = $notif->order_id;
            $paymentType = $notif->payment_type ?? null;
            $fraudStatus = $notif->fraud_status ?? '';

            // Bersihkan suffix timestamp jika order_id memakai format TRX-xxx-timestamp
            $cleanOrderId = $orderId;
            $parts = explode('-', $orderId);
            if (count($parts) >= 3 && $parts[0] === 'TRX') {
                $cleanOrderId = $parts[0] . '-' . $parts[1];
            } elseif (preg_match('/^(TRX-[A-Za-z0-9]+)/', $orderId, $matches)) {
                $cleanOrderId = $matches[1];
            }

            Log::info("Midtrans Webhook: order_id={$orderId}, status={$transactionStatus}, payment_type={$paymentType}");

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    // Pembayaran kredit berstatus challenge
                    Transaction::where('id_transaksi', $cleanOrderId)->update([
                        'payment_type' => $paymentType,
                    ]);
                } else if ($fraudStatus == 'accept') {
                    Transaction::where('id_transaksi', $cleanOrderId)->update([
                        'status_pembayaran' => 'Sudah Bayar',
                        'payment_type'      => $paymentType,
                        'konfirmasi'        => 'No',
                    ]);
                }
            } else if ($transactionStatus == 'settlement') {
                // Pembayaran sukses lunas (VA, QRIS, GoPay, dll)
                Transaction::where('id_transaksi', $cleanOrderId)->update([
                    'status_pembayaran' => 'Sudah Bayar',
                    'payment_type'      => $paymentType,
                    'konfirmasi'        => 'No',
                ]);
            } else if ($transactionStatus == 'pending') {
                // User baru generate VA / QRIS, belum transfer
                Transaction::where('id_transaksi', $cleanOrderId)->update([
                    'payment_type' => $paymentType,
                ]);
            } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                Transaction::where('id_transaksi', $cleanOrderId)->update([
                    'status_pembayaran' => 'Batal',
                ]);
            }

            return response()->json(['status' => 'success', 'message' => 'Notification handled']);
        } catch (\Exception $e) {
            Log::error('Midtrans Webhook Exception: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

