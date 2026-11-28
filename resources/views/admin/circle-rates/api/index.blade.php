@extends('admin.layouts.app')
@section('title', 'Government API Integration Settings')
@section('content')
    <div class="mb-4"><h1 class="h3 mb-1">API Integration Settings</h1><p class="text-muted mb-0">Prepare secure settings for a future official integration. No endpoint is assumed or called by this module.</p></div>
    <div class="alert alert-warning">Do not enable synchronization until the relevant government authority provides a documented API endpoint, authentication method, terms and data contract. The calculator continues to use MySQL as its source of published rates.</div>
    <div class="row g-4">
        <div class="col-lg-4"><div class="card border-0 shadow-sm"><div class="card-header bg-white"><h2 class="h5 mb-0">Add connection</h2></div><div class="card-body">
            <form method="POST" action="{{ route('admin.circle-rates.api.store') }}">@csrf
                <div class="mb-3"><label class="form-label">Connection name *</label><input class="form-control" name="name" required></div>
                <div class="mb-3"><label class="form-label">Documented base URL</label><input class="form-control" type="url" name="base_url" placeholder="https://"></div>
                <div class="mb-3"><label class="form-label">Authentication</label><select class="form-select" name="auth_type"><option value="none">None</option><option value="bearer">Bearer token</option><option value="basic">Basic authentication</option><option value="api_key">API key</option></select></div>
                <div class="mb-3"><label class="form-label">Credentials</label><textarea class="form-control" name="credentials" rows="3" autocomplete="off"></textarea><small class="text-muted">Stored encrypted. Never paste credentials into notes or source code.</small></div>
                <div class="mb-3"><label class="form-label">Additional configuration (JSON)</label><textarea class="form-control font-monospace" name="configuration" rows="3">{}</textarea></div>
                <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="is_enabled" value="1" id="api-enabled"><label class="form-check-label" for="api-enabled">Enable configuration</label></div>
                <button class="btn btn-primary">Save connection</button>
            </form>
        </div></div></div>
        <div class="col-lg-8"><div class="card border-0 shadow-sm"><div class="card-header bg-white"><h2 class="h5 mb-0">Configured connections</h2></div><div class="card-body">
            @forelse($connections as $connection)
                <form method="POST" action="{{ route('admin.circle-rates.api.update', $connection) }}" class="border rounded p-3 mb-3">@csrf @method('PUT')
                    <div class="d-flex justify-content-between"><strong>{{ $connection->name }}</strong><span class="badge {{ $connection->is_enabled ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $connection->is_enabled ? 'Enabled configuration' : 'Disabled' }}</span></div>
                    <div class="small text-muted mb-3">{{ $connection->base_url ?: 'No endpoint configured' }} · {{ $connection->auth_type }} · {{ $connection->sync_logs_count }} sync log(s)</div>
                    <div class="row g-2">
                        <div class="col-md-6"><label class="form-label">Name</label><input class="form-control" name="name" value="{{ $connection->name }}" required></div>
                        <div class="col-md-6"><label class="form-label">Base URL</label><input class="form-control" type="url" name="base_url" value="{{ $connection->base_url }}"></div>
                        <div class="col-md-6"><label class="form-label">Authentication</label><select class="form-select" name="auth_type">@foreach(['none','bearer','basic','api_key'] as $type)<option value="{{ $type }}" @selected($connection->auth_type === $type)>{{ ucfirst($type) }}</option>@endforeach</select></div>
                        <div class="col-md-6"><label class="form-label">Replace credentials (leave blank to keep current)</label><input class="form-control" type="password" name="credentials" autocomplete="new-password"></div>
                        <div class="col-12"><label class="form-label">Configuration JSON</label><textarea class="form-control font-monospace" name="configuration" rows="2">{{ json_encode($connection->configuration ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</textarea></div>
                        <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_enabled" value="1" @checked($connection->is_enabled) id="connection-{{ $connection->id }}"><label class="form-check-label" for="connection-{{ $connection->id }}">Enable configuration</label></div></div>
                    </div>
                    <div class="mt-3 d-flex gap-2"><button class="btn btn-sm btn-primary">Save settings</button></div>
                </form>
            @empty<p class="text-muted mb-0">No API connections configured.</p>@endforelse
        </div></div></div>
    </div>
    <div class="card border-0 shadow-sm mt-4"><div class="card-header bg-white"><h2 class="h5 mb-0">Recent synchronization logs</h2></div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Connection</th><th>Status</th><th>Endpoint</th><th>Message</th><th>Time</th></tr></thead><tbody>@forelse($logs as $log)<tr><td>{{ $log->connection?->name ?? '—' }}</td><td>{{ $log->status }}</td><td>{{ $log->endpoint ?? '—' }}</td><td>{{ $log->error_message ?? '—' }}</td><td>{{ $log->started_at?->format('d M Y H:i') }}</td></tr>@empty<tr><td colspan="5" class="text-center text-muted py-3">No API activity recorded.</td></tr>@endforelse</tbody></table></div></div>
@endsection
