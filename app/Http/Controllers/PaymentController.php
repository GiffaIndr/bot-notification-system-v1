<?php

namespace App\Http\Controllers;

use Midtrans\Snap;
use Midtrans\Config;
use Illuminate\Support\Str;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\GroupBot;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function callback(Request $request)
    {
        $orderId = $request->order_id;
        $status  = $request->transaction_status;

        $payment = Payment::where('order_id', $orderId)->with('plan')->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found']);
        }

        if ($status == 'settlement' || $status == 'capture') {

            $user     = $payment->user;
            $existing = $user->activeSubscription()->with('plan')->first();

            if ($existing) {
                // Perpanjang dari expired lama
                $startsAt  = $existing->expires_at;
                $expiresAt = $existing->expires_at->copy()->addMonths(6);
            } else {
                $startsAt  = now();
                $expiresAt = now()->addMonths(6);
            }

            $payment->update([
                'status'     => 'success',
                'starts_at'  => $startsAt,
                'expires_at' => $expiresAt,
            ]);

            // Sync bot untuk semua group milik user
            $this->syncGroupBots($user, $payment->plan);
        } elseif ($status == 'pending') {
            $payment->update(['status' => 'pending']);
        } elseif ($status == 'expire' || $status == 'cancel') {
            $payment->update(['status' => 'failed']);
        }

        return response()->json(['message' => 'ok']);
    }

    private function syncGroupBots($user, $plan)
    {
        // Ambil semua group milik user (role komti)
        $groups = $user->groups()
            ->wherePivot('role', 'komti')
            ->get();

        foreach ($groups as $group) {

            // Whatsapp
            if ($plan->whatsapp) {
                GroupBot::firstOrCreate(
                    ['group_id' => $group->id, 'type' => 'whatsapp'],
                    ['invitation_code' => Str::random(10), 'is_active' => true]
                );
            }

            // Discord
            if ($plan->discord) {
                GroupBot::firstOrCreate(
                    ['group_id' => $group->id, 'type' => 'discord'],
                    ['invitation_code' => Str::random(10), 'is_active' => true]
                );
            }
        }
    }
    public function syncBotsManual(Request $request)
    {
        $payment = Payment::where('order_id', $request->order_id)
            ->with('plan')
            ->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found']);
        }

        // Update status success DULU sebelum cek existing
        $payment->update([
            'status' => 'success',
            'starts_at' => now(),
            'expires_at' => now()->addMonths(6),
        ]);

        // Setelah update, baru cek apakah ada subscription lain yang aktif
        $user = $payment->user;
        $existing = $user->activeSubscription()
            ->where('id', '!=', $payment->id) // exclude payment yang baru
            ->with('plan')
            ->first();

        if ($existing) {
            // Ada subscription lain yang masih aktif, perpanjang dari expired lama
            $payment->update([
                'starts_at' => $existing->expires_at,
                'expires_at' => $existing->expires_at->copy()->addMonths(6),
            ]);
        }

        $this->syncGroupBots($user, $payment->plan);

        return response()->json(['message' => 'ok']);
    }
    public function snapToken(Request $request)
    {

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;
        Config::$isProduction = false;

        $orderId = 'ORDER-' . Str::random(8);

        $plan = Plan::findOrFail($request->plan_id);

        $params = [

            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $plan->price
            ],

            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email
            ]

        ];

        $snapToken = Snap::getSnapToken($params);

        Payment::create([
            'user_id' => auth()->id(),
            'plan_id' => $plan->id,
            'order_id' => $orderId,
            'amount' => $plan->price
        ]);

        return response()->json([
            'token' => $snapToken
        ]);
    }
}
