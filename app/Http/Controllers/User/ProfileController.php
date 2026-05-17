<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    //Updating Profile Route
    public function updateprofile(Request $request)
    {
        User::where('id', Auth::user()->id)
            ->update([
                'name' => $request->name,
                'dob' => $request->dob,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
        return response()->json(['status' => 200, 'success' => 'Profile Information Updated Sucessfully!']);
    }

    //update account and contact info
    public function updateacct(Request $request)
    {
        User::where('id', Auth::user()->id)
            ->update([
                'bank_name' => $request['bank_name'],
                'account_name' => $request['account_name'],
                'account_number' => $request['account_no'],
                'swift_code' => $request['swiftcode'],
                'btc_address' => $request['btc_address'],
                'eth_address' => $request['eth_address'],
                'ltc_address' => $request['ltc_address'],
                'usdt_address' => $request['usdt_address'],
            ]);
        return response()->json(['status' => 200, 'success' => 'Withdrawal Info updated Sucessfully']);
    }

    //Update Password
    public function updatepass(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        $user = User::find(Auth::user()->id);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('message', 'Current password does not match!');
        }
        $user->password = Hash::make($request->password);
        $user->save();
        return back()->with('success', 'Password updated successfully');
    }

    // Update email preference logic
    public function updateemail(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $user->sendotpemail = $request->otpsend;
        $user->sendroiemail = $request->roiemail;
        $user->sendinvplanemail = $request->invplanemail;
        $user->save();
        return response()->json(['status' => 200, 'success' => 'Email Preference updated']);
    }

    // Submit a currency-change request. The change does not take effect until
    // an admin approves it; only one request can be pending per user.
    public function requestCurrencyChange(Request $request)
    {
        try {
            $currencies = config('currencies');

            $validated = $request->validate([
                'requested_currency' => ['required', 'string', 'in:' . implode(',', array_keys($currencies))],
            ]);

            $user = User::find(Auth::user()->id);

            if ($user->currency_change_status === 'pending') {
                return response()->json([
                    'status' => 422,
                    'error' => 'You already have a pending currency change request.',
                ], 422);
            }

            $newCode = $validated['requested_currency'];

            if ($user->s_currency === $newCode) {
                return response()->json([
                    'status' => 422,
                    'error' => 'That is already your current currency.',
                ], 422);
            }

            $user->requested_currency = $newCode;
            $user->requested_currency_symbol = html_entity_decode($currencies[$newCode], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $user->currency_change_status = 'pending';
            $user->currency_change_requested_at = now();
            $user->currency_change_resolved_at = null;
            $user->currency_change_admin_note = null;
            $user->save();

            return response()->json([
                'status' => 200,
                'success' => 'Currency change request submitted. An admin will review it shortly.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'error' => collect($e->errors())->flatten()->first() ?: 'Invalid currency selection.',
            ], 422);
        } catch (\Throwable $e) {
            \Log::error('Currency change request failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'status' => 500,
                'error' => 'Server error while submitting your request. Please try again or contact support.',
            ], 500);
        }
    }
}