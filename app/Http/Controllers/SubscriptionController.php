<?php

namespace App\Http\Controllers;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class SubscriptionController extends Controller
{
    public function subscribe($planId)
{
    $plan = Plan::findOrFail($planId);

    Subscription::create([
        'user_id' => Auth::id(),
        'plan_id' => $plan->id,
        'status' => 'active'
    ]);

    return redirect()->back()->with('success','Subscription berhasil');
}
}
