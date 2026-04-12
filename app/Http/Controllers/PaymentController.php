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
use App\Models\GroupMember;
use Illuminate\Support\Facades\Log;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private function resolveDurationMonths($value): int
    {
        $months = (int) $value;
        return max(1, min($months, 24));
    }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $pricing = PricingComponent::pluck('price', 'key');
        $subscription = $user->activeSubscription()->first();
        $groupCount = GroupMember::where('user_id', auth()->id())
            ->whereHas('role', fn($q) => $q->where('is_owner', true))
            ->count();

        return view('pages.payments', compact('pricing', 'subscription', 'groupCount'));
    }

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

            $durationMonths = $this->resolveDurationMonths($subscription->duration_months ?? 6);

            if ($existing) {
                $startsAt  = now();
                $expiresAt = $existing->expires_at->copy()->addMonths($durationMonths);
                $existing->delete();
            } else {
                $startsAt  = now();
                $expiresAt = now()->addMonths($durationMonths);
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

        $durationMonths = $this->resolveDurationMonths($subscription->duration_months ?? 6);

        if ($existing) {
            $startsAt  = now(); // langsung aktif
            $expiresAt = $existing->expires_at->copy()->addMonths($durationMonths);

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

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $pricing      = PricingComponent::pluck('price', 'key');
        $existing     = $user->activeSubscription()->first();
        $durationMonths = $this->resolveDurationMonths($request->duration_months ?? 6);

        // Hitung berdasarkan konfigurasi paket final agar total lebih konsisten dan mudah dipahami user.
        $hasWhatsapp = (bool) $request->has_whatsapp || ($existing?->has_whatsapp ?? false);
        $hasDiscord  = (bool) $request->has_discord  || ($existing?->has_discord ?? false);
        $hasTelegram = (bool) $request->has_telegram || ($existing?->has_telegram ?? false);

        if (!$hasWhatsapp && !$hasDiscord && !$hasTelegram) {
            return response()->json(['error' => 'Pilih minimal 1 bot notifikasi!'], 422);
        }

        $targetGroups  = max((int) $request->max_groups, (int) ($existing?->max_groups ?? 1));
        $targetMembers = max((int) $request->max_members, (int) ($existing?->max_members ?? 10));

        $packageCostFor6Months = 0;
        if ($hasWhatsapp) $packageCostFor6Months += $pricing['whatsapp'];
        if ($hasDiscord)  $packageCostFor6Months += $pricing['discord'];
        if ($hasTelegram) $packageCostFor6Months += $pricing['telegram'];
        $packageCostFor6Months += ($targetGroups * $pricing['per_group']);
        $packageCostFor6Months += ($targetMembers * $pricing['per_member']);

        $total = (int) round(($packageCostFor6Months / 6) * $durationMonths);

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
                'first_name' => $user->name,
                'email'      => $user->email,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        // Merge dengan subscription yang ada
        $subscription = Subscription::create([
            'user_id'      => $user->id,
            'has_whatsapp' => $hasWhatsapp,
            'has_discord'  => $hasDiscord,
            'has_telegram' => $hasTelegram,
            'max_groups'   => $targetGroups,
            'max_members'  => $targetMembers,
            'total_price'  => $total,
            'duration_months' => $durationMonths,
        ]);

        Payment::create([
            'user_id'         => $user->id,
            'subscription_id' => $subscription->id,
            'order_id'        => $orderId,
            'amount'          => $total,
            'duration_months' => $durationMonths,
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

                $durationMonths = $this->resolveDurationMonths($subscription->duration_months ?? 6);

                if ($existing) {
                    $startsAt  = now();
                    $expiresAt = $existing->expires_at->copy()->addMonths($durationMonths);
                    $existing->delete();
                } else {
                    $startsAt  = now();
                    $expiresAt = now()->addMonths($durationMonths);
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
