<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupSubscription;
use App\Models\GroupBot;
use App\Models\PricingComponent;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class GroupUpgradeController extends Controller
{
    /**
     * Tampilkan cart-based upgrade page (1 halaman, customizable)
     */
    public function cart(Group $group)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')
            ->first();

        if (!$member || !$member->role->is_owner) {
            abort(403, 'Hanya owner yang bisa upgrade grup.');
        }

        $group->load('subscription', 'groupMembers', 'bots');

        $subscription = $group->subscription ?? GroupSubscription::create([
            'group_id' => $group->id,
            'max_members' => 50,
            'active_bots_count' => $group->bots->count(),
            'max_bots' => 1,
            'expires_at' => now()->addMonth(),
        ]);

        $currentMembers = $group->groupMembers()->count();
        $activeBots = $group->bots->count();

        // Get pricing dari database
        $pricing = PricingComponent::cachedPrices();
        $extendPrice = $pricing['upgrade_extend_month'] ?? 50000;
        $memberPrice = $pricing['upgrade_member_slot'] ?? 5000; // per 5 orang
        $botPrice = $pricing['upgrade_bot'] ?? 75000;

        // Available bot types untuk ditambah
        $availableBots = [];
        $botTypes = ['discord' => 'Discord', 'telegram' => 'Telegram', 'whatsapp' => 'WhatsApp'];
        $activeBotTypes = $group->bots->pluck('type')->toArray();

        foreach ($botTypes as $type => $label) {
            if (!in_array($type, $activeBotTypes)) {
                $availableBots[] = ['type' => $type, 'label' => $label, 'price' => $botPrice];
            }
        }

        // Pricing for preset options
        $extensionOptions = [
            ['months' => 1, 'price' => $extendPrice, 'label' => "1 Bulan (Rp" . number_format($extendPrice, 0, ',', '.') . ")"],
            ['months' => 3, 'price' => $extendPrice * 3, 'label' => "3 Bulan (Rp" . number_format($extendPrice * 3, 0, ',', '.') . ")"],
        ];

        $memberOptions = [
            ['slots' => 5, 'price' => $memberPrice, 'label' => "+5 Member (Rp" . number_format($memberPrice, 0, ',', '.') . ")"],
            ['slots' => 10, 'price' => $memberPrice * 2, 'label' => "+10 Member (Rp" . number_format($memberPrice * 2, 0, ',', '.') . ")"],
        ];

        return view('pages.group-upgrade-cart', compact(
            'group',
            'subscription',
            'currentMembers',
            'activeBots',
            'availableBots',
            'extensionOptions',
            'memberOptions',
            'extendPrice',
            'memberPrice',
            'botPrice',
        ));
    }

    /**
     * Process cart checkout (handle multiple upgrades at once)
     */
    public function checkout(Request $request, Group $group)
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->with('role')
            ->first();

        if (!$member || !$member->role->is_owner) {
            abort(403);
        }

        $request->validate([
            'extend_duration' => 'nullable|numeric|min:0',
            'extend_preset' => 'nullable|in:1,3',
            'add_members' => 'nullable|numeric|min:0',
            'add_members_preset' => 'nullable|in:5,10',
            'add_bots' => 'nullable|array',
            'add_bots.*' => 'in:discord,telegram,whatsapp',
        ]);

        $cart = [];
        $totalPrice = 0;
        $pricing = $this->getPricing();

        // Validate at least one upgrade selected
        $hasExtend = $request->filled('extend_preset') || $request->filled('extend_duration');
        $hasMembers = $request->filled('add_members_preset') || $request->filled('add_members');
        $hasBots = $request->filled('add_bots');

        if (!$hasExtend && !$hasMembers && !$hasBots) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Pilih minimal 1 upgrade yang ingin dibeli.'], 422);
            }

            return back()->with('error', 'Pilih minimal 1 upgrade yang ingin dibeli.');
        }

        // Handle extension
        if ($request->filled('extend_preset')) {
            $months = (int) $request->extend_preset;
            $price = $pricing['extension'][$months] ?? 0;
            if ($price > 0) {
                $cart['extend_duration'] = ['months' => $months, 'price' => $price];
                $totalPrice += $price;
            }
        } elseif ($request->filled('extend_duration')) {
            $months = (int) $request->extend_duration;
            if ($months > 0 && $months <= 60) { // Max 5 years
                $price = $months * $pricing['extend_month'];
                $cart['extend_duration'] = ['months' => $months, 'price' => $price];
                $totalPrice += $price;
            }
        }

        // Handle add members
        if ($request->filled('add_members_preset')) {
            $slots = (int) $request->add_members_preset;
            $price = $pricing['members'][$slots] ?? 0;
            if ($price > 0) {
                $cart['add_members'] = ['slots' => $slots, 'price' => $price];
                $totalPrice += $price;
            }
        } elseif ($request->filled('add_members')) {
            $slots = (int) $request->add_members;
            if ($slots > 0 && $slots <= 1000) { // Max 1000 slots
                $price = ceil($slots / 5) * $pricing['member_slot'];
                $cart['add_members'] = ['slots' => $slots, 'price' => $price];
                $totalPrice += $price;
            }
        }

        // Handle add bots
        if ($request->filled('add_bots')) {
            $bots = $request->add_bots;
            foreach ($bots as $botType) {
                if ($group->bots()->where('type', $botType)->doesntExist()) {
                    if (!isset($cart['add_bots'])) {
                        $cart['add_bots'] = ['bots' => [], 'price' => 0];
                    }
                    $cart['add_bots']['bots'][] = $botType;
                    $cart['add_bots']['price'] += $pricing['bot'];
                    $totalPrice += $pricing['bot'];
                }
            }
        }

        if ($totalPrice <= 0) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Total pembayaran tidak valid.'], 422);
            }

            return back()->with('error', 'Total pembayaran tidak valid.');
        }

        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');

        $orderId = 'GROUP-UPGRADE-' . Str::random(8);

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $totalPrice,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
        } catch (\Throwable $e) {
            Log::error('Group upgrade Midtrans snap token generation failed', [
                'message' => $e->getMessage(),
                'group_id' => $group->id,
                'order_id' => $orderId,
            ]);

            return response()->json([
                'error' => 'Gagal membuat token Midtrans. Cek konfigurasi MIDTRANS_SERVER_KEY dan client key.',
            ], 422);
        }

        // Store cart di session untuk payment callback
        session(['group_upgrade_cart' => [
            'group_id' => $group->id,
            'order_id' => $orderId,
            'cart' => $cart,
            'total_price' => $totalPrice,
        ]]);

        return response()->json([
            'token' => $snapToken,
            'order_id' => $orderId,
        ]);
    }

    /**
     * Handle payment callback after Midtrans success.
     */
    public function callback(Request $request, Group $group)
    {
        $upgradeCart = session('group_upgrade_cart');

        if (!$upgradeCart || (int) $upgradeCart['group_id'] !== (int) $group->id) {
            return response()->json([
                'error' => 'Data upgrade tidak valid atau sudah kadaluarsa.',
            ], 422);
        }

        $requestOrderId = $request->query('order_id') ?? $request->input('order_id');
        if ($requestOrderId && isset($upgradeCart['order_id']) && $requestOrderId !== $upgradeCart['order_id']) {
            return response()->json([
                'error' => 'Order ID tidak cocok.',
            ], 422);
        }

        $this->processCart($group, $upgradeCart['cart']);

        session()->forget('group_upgrade_cart');

        if ($request->expectsJson()) {
            return response()->json(['message' => 'ok']);
        }

        return redirect()->route('groups.show', $group)
            ->with('success', 'Upgrade grup berhasil! Fitur baru sudah aktif.');
    }

    /**
     * Process cart upgrades
     */
    private function processCart(Group $group, array $cart)
    {
        $subscription = $group->subscription ?? GroupSubscription::create([
            'group_id' => $group->id,
            'max_members' => 50,
            'active_bots_count' => $group->bots->count(),
            'max_bots' => 1,
            'expires_at' => now()->addMonth(),
        ]);

        // Process extend duration
        if (isset($cart['extend_duration'])) {
            $months = $cart['extend_duration']['months'];
            $newExpiry = $subscription->expires_at && $subscription->expires_at->isFuture()
                ? $subscription->expires_at->addMonths($months)
                : now()->addMonths($months);

            $subscription->update(['expires_at' => $newExpiry]);
        }

        // Process add members
        if (isset($cart['add_members'])) {
            $slots = $cart['add_members']['slots'];
            $subscription->update([
                'max_members' => $subscription->max_members + $slots,
            ]);
        }

        // Process add bots
        if (isset($cart['add_bots'])) {
            foreach ($cart['add_bots']['bots'] as $botType) {
                if ($group->bots()->where('type', $botType)->doesntExist()) {
                    GroupBot::create([
                        'group_id' => $group->id,
                        'type' => $botType,
                        'invitation_code' => Str::random(10),
                        'is_active' => false,
                    ]);
                }
            }
            // Update bot count
            $subscription->update([
                'active_bots_count' => $group->bots->count(),
                'max_bots' => $subscription->max_bots + 1,
            ]);
        }
    }

    /**
     * Get pricing reference dari PricingComponent
     */
    private function getPricing(): array
    {
        $prices = PricingComponent::cachedPrices();
        $extendMonth = $prices['upgrade_extend_month'] ?? 50000;
        $memberSlot = $prices['upgrade_member_slot'] ?? 5000; // per 5 orang
        $bot = $prices['upgrade_bot'] ?? 75000;

        return [
            'extension' => [
                1 => $extendMonth,
                3 => $extendMonth * 3,
            ],
            'members' => [
                5 => $memberSlot,
                10 => $memberSlot * 2,
            ],
            'extend_month' => $extendMonth,
            'member_slot' => $memberSlot,
            'bot' => $bot,
        ];
    }
}
