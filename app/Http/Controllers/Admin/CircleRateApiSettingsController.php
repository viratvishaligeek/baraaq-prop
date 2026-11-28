<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CircleRateApiConnection;
use App\Models\CircleRateApiSyncLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

class CircleRateApiSettingsController extends Controller
{
    public function index()
    {
        return view('admin.circle-rates.api.index', [
            'connections' => CircleRateApiConnection::query()->withCount('syncLogs')->latest()->get(),
            'logs' => CircleRateApiSyncLog::query()->with('connection')->latest()->limit(20)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        if (! empty($data['credentials'])) {
            $data['encrypted_credentials'] = Crypt::encryptString($data['credentials']);
        }
        unset($data['credentials']);
        CircleRateApiConnection::query()->create($data);

        return back()->with('success', 'API connection settings saved. No external request has been made.');
    }

    public function update(Request $request, CircleRateApiConnection $connection)
    {
        $data = $this->validated($request);
        if (! empty($data['credentials'])) {
            $data['encrypted_credentials'] = Crypt::encryptString($data['credentials']);
        } else {
            unset($data['encrypted_credentials']);
        }
        unset($data['credentials']);
        $connection->update($data);

        return back()->with('success', 'API connection settings updated. Credentials remain encrypted.');
    }

    public function testUnavailable(Request $request, CircleRateApiConnection $connection)
    {
        $log = CircleRateApiSyncLog::query()->create([
            'connection_id' => $connection->id,
            'status' => 'not_configured',
            'endpoint' => null,
            'request_summary' => ['action' => 'manual_test'],
            'response_summary' => null,
            'error_message' => 'No documented government API endpoint or integration contract has been configured. No network request was attempted.',
            'started_at' => now(),
            'finished_at' => now(),
        ]);

        return back()->with('warning', 'No API request was sent. Configure a documented official endpoint and authentication contract before enabling synchronization.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'base_url' => ['nullable', 'url', 'max:1000'],
            'auth_type' => ['required', 'in:none,bearer,basic,api_key'],
            'credentials' => ['nullable', 'string', 'max:10000'],
            'configuration' => ['nullable', 'json'],
            'is_enabled' => ['nullable', 'boolean'],
        ]);

        if (! empty($data['is_enabled']) && empty($data['base_url'])) {
            throw ValidationException::withMessages(['base_url' => 'A documented base URL is required before enabling a connection.']);
        }

        if (isset($data['configuration'])) {
            $data['configuration'] = json_decode($data['configuration'], true, 512, JSON_THROW_ON_ERROR);
        }
        $data['is_enabled'] = $request->boolean('is_enabled');

        return $data;
    }
}
