<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
class DevicesList extends Widget
{
    protected static string $view = 'filament.widgets.devices-list';

    protected int|string|array $columnSpan = 'full';

    public $devices = [];
    public $isLoading = true;
    public $error = null;
    public $stats = [
        'connected' => 0,
        'total' => 0,
        'messages' => 0
    ];

    public function mount()
    {
        $this->fetchDevices();
    }

    public function fetchDevices()
    {
        $this->isLoading = true;
        $this->error = null;

        try {
            $response = Http::withHeaders([
                'Authorization' => env('FONNTE_API_TOKEN')
            ])->post('https://api.fonnte.com/get-devices');

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] ?? false) {
                    $this->devices = $data['data'] ?? [];
                    $this->stats = [
                        'connected' => $data['connected'] ?? 0,
                        'total' => $data['devices'] ?? 0,
                        'messages' => $data['messages'] ?? 0
                    ];
                } else {
                    $this->error = 'API returned unsuccessful status';
                }
            } else {
                $this->error = 'Failed to fetch devices: ' . $response->status();
                Log::error('Fonnte API Error', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            $this->error = 'API request failed: ' . $e->getMessage();
            Log::error('Fonnte API Exception', ['error' => $e->getMessage()]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function formatExpired($timestamp)
    {
        if (empty($timestamp)) return '-';

        try {
            return Carbon::createFromTimestamp($timestamp)->format('d M Y H:i');
        } catch (\Exception $e) {
            return $timestamp;
        }
    }

    public function getStatusColor($status)
    {
        return match($status) {
            'connected' => 'green',
            'disconnect' => 'red',
            default => 'gray'
        };
    }
}
