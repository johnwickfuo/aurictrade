@php
if (Auth('admin')->User()->dashboard_style == 'light') {
    $text = 'dark';
} else {
    $text = 'light';
}
@endphp
@extends('layouts.app')
@section('content')
    @include('admin.topmenu')
    @include('admin.sidebar')
    <div class="main-panel ">
        <div class="content ">
            <div class="page-inner">
                <div class="mt-2 mb-4">
                    <h1 class="title1 d-inline">Currency Change Requests</h1>
                    <div class="d-inline">
                        <div class="float-right btn-group">
                            <a class="btn {{ $status === 'pending' ? 'btn-primary' : 'btn-secondary' }} btn-sm"
                               href="{{ route('currency.requests.index', ['status' => 'pending']) }}">
                                Pending
                                @if ($pendingCount > 0)
                                    <span class="badge badge-light">{{ $pendingCount }}</span>
                                @endif
                            </a>
                            <a class="btn {{ $status === 'resolved' ? 'btn-primary' : 'btn-secondary' }} btn-sm"
                               href="{{ route('currency.requests.index', ['status' => 'resolved']) }}">Resolved</a>
                            <a class="btn {{ $status === 'all' ? 'btn-primary' : 'btn-secondary' }} btn-sm"
                               href="{{ route('currency.requests.index', ['status' => 'all']) }}">All</a>
                        </div>
                    </div>
                </div>

                <x-danger-alert />
                <x-success-alert />
                <x-error-alert />

                <div class="card shadow">
                    <div class="card-body">
                        @if ($requests->count() === 0)
                            <div class="alert alert-info mb-0">
                                <i class="fa fa-info-circle"></i>
                                No currency change requests {{ $status === 'pending' ? 'pending' : 'on record' }}.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Email</th>
                                            <th>Current</th>
                                            <th>Requested</th>
                                            <th>Status</th>
                                            <th>Requested</th>
                                            <th>Resolved</th>
                                            <th class="text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($requests as $req)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('viewuser', $req->id) }}">{{ $req->name }}</a>
                                                    <br>
                                                    <small class="text-muted">{{ $req->username }}</small>
                                                </td>
                                                <td>{{ $req->email }}</td>
                                                <td>
                                                    <strong>{!! $req->currency !!}</strong>
                                                    <small class="text-muted">{{ $req->s_currency }}</small>
                                                </td>
                                                <td>
                                                    @if ($req->requested_currency)
                                                        <strong>{!! $req->requested_currency_symbol !!}</strong>
                                                        <small class="text-muted">{{ $req->requested_currency }}</small>
                                                    @else
                                                        <span class="text-muted">&mdash;</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($req->currency_change_status === 'pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @else
                                                        <span class="badge badge-success">Resolved</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $req->currency_change_requested_at ? \Carbon\Carbon::parse($req->currency_change_requested_at)->diffForHumans() : '-' }}
                                                </td>
                                                <td>
                                                    {{ $req->currency_change_resolved_at ? \Carbon\Carbon::parse($req->currency_change_resolved_at)->diffForHumans() : '-' }}
                                                </td>
                                                <td class="text-right">
                                                    @if ($req->currency_change_status === 'pending')
                                                        <button type="button" class="btn btn-success btn-sm"
                                                                data-toggle="modal" data-target="#approve-{{ $req->id }}">
                                                            <i class="fa fa-check"></i> Approve
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                                data-toggle="modal" data-target="#reject-{{ $req->id }}">
                                                            <i class="fa fa-times"></i> Reject
                                                        </button>
                                                    @else
                                                        @if ($req->currency_change_admin_note)
                                                            <small class="text-muted" title="{{ $req->currency_change_admin_note }}">
                                                                <i class="fa fa-comment"></i> Note
                                                            </small>
                                                        @else
                                                            <span class="text-muted">&mdash;</span>
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>

                                            @if ($req->currency_change_status === 'pending')
                                                <!-- Approve modal -->
                                                <div class="modal fade" id="approve-{{ $req->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <form action="{{ route('currency.requests.approve', $req->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Approve currency change</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>
                                                                        Change <strong>{{ $req->name }}</strong>'s currency from
                                                                        <strong>{!! $req->currency !!} {{ $req->s_currency }}</strong>
                                                                        to
                                                                        <strong>{!! $req->requested_currency_symbol !!} {{ $req->requested_currency }}</strong>?
                                                                    </p>
                                                                    <div class="alert alert-warning small">
                                                                        Balances and stored amounts are not converted &mdash; only the symbol shown changes.
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Optional note for the user</label>
                                                                        <textarea name="admin_note" class="form-control" rows="2" maxlength="1000"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-success btn-sm">
                                                                        <i class="fa fa-check"></i> Approve
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                                <!-- Reject modal -->
                                                <div class="modal fade" id="reject-{{ $req->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <form action="{{ route('currency.requests.reject', $req->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Reject currency change</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>
                                                                        Reject <strong>{{ $req->name }}</strong>'s request to switch to
                                                                        <strong>{{ $req->requested_currency }}</strong>?
                                                                    </p>
                                                                    <div class="form-group">
                                                                        <label>Reason (shown to the user)</label>
                                                                        <textarea name="admin_note" class="form-control" rows="2" maxlength="1000" required></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                                        <i class="fa fa-times"></i> Reject
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $requests->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
