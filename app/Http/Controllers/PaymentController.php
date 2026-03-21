<?php

namespace App\Http\Controllers;

use Midtrans\Snap;
use Midtrans\Config;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Models\PricingComponent;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\GroupBot;
use Illuminate\Support\Facades\Log;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function callback(Request $request)
    {
        $orderId = $request->order_id;
        $status  = $request->transaction_status;

        $payment = Payment::where('order_id', $orderId)
            ->with('subscription')
            ->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found']);
        }

        if ($status == 'settlement' || $status == 'capture') {

            $user         = $payment->user;
            $subscription = $payment->subscription;

            if (!$subscription) {
                return response()->json(['message' => 'Subscription not found']);
            }

            $existing = Subscription::where('user_id', $user->id)
                ->where('id', '!=', $subscription->id)
                ->whereNotNull('starts_at')
                ->where('expires_at', '>', now())
                ->latest('expires_at')
                ->first();

            if ($existing) {
                $startsAt  = now();
                $expiresAt = $existing->expires_at->copy()->addMonths(6);
                $existing->delete();
            } else {
                $startsAt  = now();
                $expiresAt = now()->addMonths(6);
            }

            $payment->update([
                'status'     => 'success',
                'starts_at'  => $startsAt,
                'expires_at' => $expiresAt,
            ]);

            $subscription->update([
                'starts_at'  => $startsAt,
                'expires_at' => $expiresAt,
            ]);

            $this->syncGroupBots($user, $subscription);

            Log::info('Callback payment success', ['order_id' => $orderId]);
        } elseif ($status == 'pending') {
            $payment->update(['status' => 'pending']);
        } elseif ($status == 'expire' || $status == 'cancel') {
            $payment->update(['status' => 'failed']);
        }

        return response()->json(['message' => 'ok']);
    }

    private function syncGroupBots($user, $subscription)
    {
        $groupIds = \App\Models\GroupMember::where('user_id', $user->id)
            ->whereHas('role', fn($q) => $q->where('is_owner', true))
            ->pluck('group_id');

        $groups = \App\Models\Group::whereIn('id', $groupIds)->get();

        foreach ($groups as $group) {
            if ($subscription->has_whatsapp) {
                GroupBot::firstOrCreate(
                    ['group_id' => $group->id, 'type' => 'whatsapp'],
                    ['invitation_code' => Str::random(10), 'is_active' => true]
                );
            }
            if ($subscription->has_discord) {
                GroupBot::firstOrCreate(
                    ['group_id' => $group->id, 'type' => 'discord'],
                    ['invitation_code' => Str::random(10), 'is_active' => true]
                );
            }
            if ($subscription->has_telegram) {
                GroupBot::firstOrCreate(
                    ['group_id' => $group->id, 'type' => 'telegram'],
                    ['invitation_code' => Str::random(10), 'is_active' => true]
                );
            }
        }
    }

    public function syncBotsManual(Request $request)
    {
        Log::info('syncBotsManual called', ['order_id' => $request->order_id]);

        $payment = Payment::where('order_id', $request->order_id)
            ->with('subscription')
            ->first();

        Log::info('payment found', ['payment' => $payment?->toArray()]);
        Log::info('subscription', ['sub' => $payment?->subscription?->toArray()]);

        // ... rest of code
        $payment = Payment::where('order_id', $request->order_id)
            ->with('subscription')
            ->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found']);
        }

        $user         = $payment->user;
        $subscription = $payment->subscription;

        // Cek existing subscription aktif selain yang ini
        $existing = Subscription::where('user_id', $user->id)
            ->where('id', '!=', $subscription->id)
            ->whereNotNull('starts_at')
            ->where('expires_at', '>', now())
            ->latest('expires_at')
            ->first();

        if ($existing) {
            $startsAt  = now(); // langsung aktif
            $expiresAt = $existing->expires_at->copy()->addMonths(6);

            // Hapus subscription lama, merge ke yang baru
            $existing->delete();
        } else {
            $startsAt  = now();
            $expiresAt = now()->addMonths(6);
        }

        $payment->update([
            'status'     => 'success',
            'starts_at'  => $startsAt,
            'expires_at' => $expiresAt,
        ]);

        $subscription->update([
            'starts_at'  => $startsAt,
            'expires_at' => $expiresAt,
        ]);

        $this->syncGroupBots($user, $subscription);

        return response()->json(['message' => 'ok']);
    }

    public function logs()
    {
        $logs = Payment::where('user_id', auth()->id())
            ->with('subscription')
            ->latest()
            ->paginate(10);

        return view('pages.paymentLogs', compact('logs'));
    }
    public function receipt(string $orderId)
    {
        $payment = Payment::where('order_id', $orderId)
            ->where('user_id', auth()->id())
            ->with('subscription')
            ->firstOrFail();

        // Ambil expires_at dari subscription kalau payment null
        $expiresAt = $payment->expires_at ?? $payment->subscription->expires_at;

        return view('pages.receipt', compact('payment', 'expiresAt'));
    }

    public function printReceipt(string $orderId)
    {
        $payment = Payment::where('order_id', $orderId)
            ->where('user_id', auth()->id())
            ->with('subscription')
            ->firstOrFail();

        $pdf = Pdf::loadView('pages.receipt-pdf', compact('payment'));

        return $pdf->download("receipt-{$orderId}.pdf");
    }
    public function snapToken(Request $request)
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = false;
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        $pricing      = PricingComponent::pluck('price', 'key');
        $existing     = auth()->user()->activeSubscription()->first();

        $total = 0;

        // Bot — hanya yang baru
        if ($request->has_whatsapp && !($existing?->has_whatsapp)) $total += $pricing['whatsapp'];
        if ($request->has_discord  && !($existing?->has_discord))  $total += $pricing['discord'];
        if ($request->has_telegram && !($existing?->has_telegram)) $total += $pricing['telegram'];

        // Group & member — hanya tambahan
        $currentGroups  = $existing?->max_groups  ?? 0;
        $currentMembers = $existing?->max_members ?? 0;
        $extraGroups    = max(0, $request->max_groups  - $currentGroups);
        $extraMembers   = max(0, $request->max_members - $currentMembers);

        $total += $extraGroups  * $pricing['per_group'];
        $total += $extraMembers * $pricing['per_member'];

        if ($total <= 0) {
            return response()->json(['error' => 'Tidak ada perubahan dari langganan saat ini!'], 422);
        }

        $orderId = 'ORDER-' . Str::random(8);

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email'      => auth()->user()->email,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        // Merge dengan subscription yang ada
        $subscription = Subscription::create([
            'user_id'      => auth()->id(),
            'has_whatsapp' => $request->has_whatsapp || ($existing?->has_whatsapp ?? false),
            'has_discord'  => $request->has_discord  || ($existing?->has_discord  ?? false),
            'has_telegram' => $request->has_telegram || ($existing?->has_telegram ?? false),
            'max_groups'   => max($request->max_groups,  $existing?->max_groups  ?? 0),
            'max_members'  => max($request->max_members, $existing?->max_members ?? 0),
            'total_price'  => $total,
        ]);

        Payment::create([
            'user_id'         => auth()->id(),
            'subscription_id' => $subscription->id,
            'order_id'        => $orderId,
            'amount'          => $total,
        ]);

        return response()->json(['token' => $snapToken]);
    }

    public function checkPending(Request $request)
    {
        $payment = Payment::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->latest()
            ->with('subscription')
            ->first();

        if (!$payment) {
            return response()->json(['synced' => false, 'has_pending' => false]);
        }
        if (!$payment->subscription) {
            return response()->json(['synced' => false, 'has_pending' => false]);
        }

        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = false;

        try {
            /** @var object $status */
            $status = \Midtrans\Transaction::status($payment->order_id);

            Log::info('Midtrans status check', [
                'order_id' => $payment->order_id,
                'status'   => $status->transaction_status
            ]);

            if ($status->transaction_status === 'settlement' || $status->transaction_status === 'capture') {

                $user         = $payment->user;
                $subscription = $payment->subscription;

                $existing = Subscription::where('user_id', $user->id)
                    ->where('id', '!=', $subscription->id)
                    ->whereNotNull('starts_at')
                    ->where('expires_at', '>', now())
                    ->latest('expires_at')
                    ->first();

                if ($existing) {
                    $startsAt  = now();
                    $expiresAt = $existing->expires_at->copy()->addMonths(6);
                    $existing->delete();
                } else {
                    $startsAt  = now();
                    $expiresAt = now()->addMonths(6);
                }

                $payment->update([
                    'status'     => 'success',
                    'starts_at'  => $startsAt,
                    'expires_at' => $expiresAt,
                ]);

                $subscription->update([
                    'starts_at'  => $startsAt,
                    'expires_at' => $expiresAt,
                ]);

                $this->syncGroupBots($user, $subscription);

                return response()->json([
                    'synced'      => true,
                    'has_pending' => false,
                    'order_id'    => $payment->order_id,
                ]);
            }

            // Status masih pending di Midtrans
            return response()->json([
                'synced'      => false,
                'has_pending' => true, // ← kasih tau JS untuk cek lagi
            ]);
        } catch (\Exception $e) {
            Log::error('checkPending error: ' . $e->getMessage());
            return response()->json(['synced' => false, 'has_pending' => false]);
        }
    }
}
