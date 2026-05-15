<?php

namespace App\Actions\Fortify;

use App\Mail\WelcomeEmail;
use App\Models\User;
use App\Models\Settings;
use App\Models\Agent;
use App\Models\CryptoAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        $settings = Settings::where('id', '1')->first();
        $request = request();
        $currencies = config('currencies');
        $currencyRule = ['nullable', 'string', 'in:' . implode(',', array_keys($currencies))];

        if ($settings->captcha == "true") {
            Validator::make($input, [
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'unique:users,username'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => $this->passwordRules(),
                'currency' => $currencyRule,
                'g-recaptcha-response' => 'required|captcha',
                'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            ])->validate();
        } else {
            Validator::make($input, [
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'unique:users,username'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'captcha' => ['required', function ($attribute, $value, $fail) use ($input) {
        if ($value !== $input['captcha_confirmation']) {
            $fail('The CAPTCHA code does not match.');
        }
    }],

                'password' => $this->passwordRules(),
                'currency' => $currencyRule,
                'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            ])->validate();
        }

        // Resolve the requested currency code to its symbol. Fall back to the
        // platform default if the user didn't pick anything.
        $defaultCode = $settings->s_currency ?? 'USD';
        $defaultSymbol = $settings->currency ?? ($currencies[$defaultCode] ?? '&#36;');
        $currencyCode = !empty($input['currency']) && isset($currencies[$input['currency']])
            ? $input['currency']
            : $defaultCode;
        $currencySymbol = $currencies[$currencyCode] ?? $defaultSymbol;

        if (session('ref_by')) {
            $ref_by = session('ref_by');
            $user = User::where('username', $ref_by)->first();
            $ref_by_id = $user->id;
        } else {
            if (!empty($input['ref_by'])) {
                $sponsor = User::where('username', $input['ref_by'])->first();
                $ref_by_id = $sponsor->id;
            } else {
                $ref_by_id = NULL;
            }
        }

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'username' => $input['username'],
            'country' => $input['country'],
            'ref_by' => $ref_by_id,
            'status' => 'active',
            'currency' => $currencySymbol,
            's_currency' => $currencyCode,
            'password' => Hash::make($input['password']),
        ]);

        $cryptoaccnt = new CryptoAccount();
        $cryptoaccnt->user_id = $user->id;
        $cryptoaccnt->save();
        $request->session()->forget('ref_by');

        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome email to user: ' . $user->email . '. Error: ' . $e->getMessage());
        }

        return $user;
    }
}
