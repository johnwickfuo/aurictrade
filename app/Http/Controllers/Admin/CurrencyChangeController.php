<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CurrencyChangeController extends Controller
{
    // List all currency change requests (pending first), with quick filter
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $query = User::query()
            ->whereNotNull('currency_change_requested_at');

        if ($status === 'pending') {
            $query->where('currency_change_status', 'pending');
        } elseif ($status === 'resolved') {
            $query->whereNull('currency_change_status');
        }

        $requests = $query->orderByDesc('currency_change_requested_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.Users.currency-requests', [
            'title' => 'Currency Change Requests',
            'requests' => $requests,
            'status' => $status,
            'pendingCount' => User::where('currency_change_status', 'pending')->count(),
        ]);
    }

    // Approve a pending currency change: applies the new currency/s_currency
    // and clears the request fields.
    public function approve(Request $request, $id)
    {
        $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = User::findOrFail($id);

        if ($user->currency_change_status !== 'pending') {
            return redirect()->back()->with('error', 'This request is not pending.');
        }

        $currencies = config('currencies');
        $code = $user->requested_currency;

        if (!$code || !isset($currencies[$code])) {
            return redirect()->back()->with('error', 'Requested currency is invalid.');
        }

        $user->currency = html_entity_decode($currencies[$code], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $user->s_currency = $code;
        $user->requested_currency = null;
        $user->requested_currency_symbol = null;
        $user->currency_change_status = null;
        $user->currency_change_resolved_at = now();
        $user->currency_change_admin_note = $request->input('admin_note');
        $user->save();

        return redirect()->back()->with('success', "Currency change approved. {$user->name} is now using {$code}.");
    }

    // Reject a pending currency change: clears request fields, keeps history.
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = User::findOrFail($id);

        if ($user->currency_change_status !== 'pending') {
            return redirect()->back()->with('error', 'This request is not pending.');
        }

        $user->requested_currency = null;
        $user->requested_currency_symbol = null;
        $user->currency_change_status = null;
        $user->currency_change_resolved_at = now();
        $user->currency_change_admin_note = $request->input('admin_note');
        $user->save();

        return redirect()->back()->with('success', "Currency change request for {$user->name} was rejected.");
    }
}
