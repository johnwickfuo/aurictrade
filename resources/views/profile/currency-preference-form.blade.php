@php
    $user = Auth::user();
    $currencies = $currencies ?? config('currencies');
    $pending = $user->currency_change_status === 'pending';
    $currentCode = $user->s_currency ?: 'USD';
    $currentSymbol = $user->currency ?: ($currencies[$currentCode] ?? '$');
@endphp
<div x-data="{ submitting: false }">
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-white">Account Currency</h3>
        <p class="text-sm text-gray-300 dark:text-gray-400 mt-1">
            This is the currency used to display your balances, deposits, withdrawals and transactions.
            Changing your currency does not convert any existing balance amounts &mdash; only the symbol shown changes.
        </p>
    </div>

    <!-- Current currency card -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="p-4 rounded-xl bg-gray-800 border border-gray-700">
            <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Current Currency</p>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-white">{!! $currentSymbol !!}</span>
                <span class="text-sm text-gray-300">{{ $currentCode }}</span>
            </div>
        </div>

        @if ($pending)
            <div class="p-4 rounded-xl bg-yellow-900/30 border border-yellow-700">
                <p class="text-xs uppercase tracking-wide text-yellow-300 mb-1">Pending Change Request</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-white">{!! $user->requested_currency_symbol !!}</span>
                    <span class="text-sm text-yellow-200">{{ $user->requested_currency }}</span>
                </div>
                <p class="text-xs text-yellow-300 mt-2">
                    Awaiting admin approval &mdash; requested {{ \Carbon\Carbon::parse($user->currency_change_requested_at)->diffForHumans() }}
                </p>
            </div>
        @endif
    </div>

    @if ($user->currency_change_admin_note && !$pending)
        <div class="mb-6 p-4 rounded-xl bg-blue-900/30 border border-blue-700">
            <p class="text-xs uppercase tracking-wide text-blue-300 mb-1">Note from admin on your last request</p>
            <p class="text-sm text-blue-100">{{ $user->currency_change_admin_note }}</p>
        </div>
    @endif

    <!-- Request currency change form -->
    <form method="POST" action="javascript:void(0)" id="requestCurrencyForm" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-center">
            <div class="md:col-span-1">
                <label for="requested_currency" class="block text-sm font-medium text-gray-200">
                    Request New Currency
                </label>
                <p class="text-xs text-gray-400 mt-1">An admin must approve the change before it takes effect.</p>
            </div>
            <div class="md:col-span-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="dollar-sign" class="h-5 w-5 text-gray-400"></i>
                    </div>
                    <select name="requested_currency" id="requested_currency"
                            @if ($pending) disabled @endif
                            class="pl-10 pr-10 block w-full rounded-xl border-gray-600 bg-gray-700 dark:bg-gray-700 text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm py-4 appearance-none @if ($pending) opacity-50 cursor-not-allowed @endif">
                        <option value="" selected disabled>Select a new currency</option>
                        @foreach ($currencies as $code => $symbol)
                            @if ($code !== $currentCode)
                                <option value="{{ $code }}">{{ $code }} ({!! $symbol !!})</option>
                            @endif
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <i data-lucide="chevron-down" class="h-5 w-5 text-gray-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-5 border-t border-gray-700">
            <div class="flex justify-end">
                <button type="submit"
                        @if ($pending) disabled @endif
                        x-on:click="submitting = true"
                        x-bind:disabled="submitting || {{ $pending ? 'true' : 'false' }}"
                        class="inline-flex items-center px-6 py-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 @if ($pending) opacity-50 cursor-not-allowed hover:bg-blue-600 @endif">
                    <span x-show="!submitting">
                        <i data-lucide="send" class="mr-2 h-5 w-5 inline"></i>
                        {{ $pending ? 'Request Pending' : 'Submit Change Request' }}
                    </span>
                    <span x-show="submitting" style="display:none;">
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Submitting...
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        const form = document.getElementById('requestCurrencyForm');
        if (!form) return;

        form.addEventListener('submit', function() {
            const select = document.getElementById('requested_currency');
            if (!select.value) {
                alert('Please choose a currency before submitting.');
                return;
            }

            $.ajax({
                url: "{{ route('currency.request') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    requested_currency: select.value
                },
                success: function(response) {
                    if (response.status === 200) {
                        const toast = document.createElement('div');
                        toast.className = 'fixed top-4 right-4 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-lg z-50';
                        toast.innerHTML = `<p class="text-sm font-medium">${response.success}</p>`;
                        document.body.appendChild(toast);
                        setTimeout(() => { window.location.reload(); }, 1500);
                    } else if (response.status === 422 || response.error) {
                        alert(response.error || 'Could not submit request.');
                        location.reload();
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON && xhr.responseJSON.error
                        ? xhr.responseJSON.error
                        : 'Failed to submit currency change request. Please try again.';
                    alert(msg);
                    location.reload();
                }
            });
        });
    });
</script>
