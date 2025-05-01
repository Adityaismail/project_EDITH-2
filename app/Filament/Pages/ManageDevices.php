<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Device;
use Carbon\Carbon;
use Filament\Pages\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
class ManageDevices extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static string $view = 'filament.pages.manage-devices';

    protected static ?string $navigationLabel = 'Devices';

    public $devices;
    public $error = null;
    public $stats = [
        'total' => 0,
        'connected' => 0,
        'messages' => 0
    ];

    public function mount(): void
    {
        $this->loadDevices();
    }

    public function loadDevices()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => env('FONNTE_API_TOKEN')
            ])->post('https://api.fonnte.com/get-devices');

            if ($response->successful()) {
                $data = $response->json();

                // Ambil devices dari response yang benar
                $devicesData = $data['data'] ?? []; // Sesuaikan dengan struktur response yang benar

                $this->devices = collect($devicesData)->map(function ($deviceData) {
                    // Debug setiap device
                    \Log::info('Device Data:', $deviceData);

                    return new Device([
                        'name' => $deviceData['name'] ?? $deviceData['device'] ?? 'Unknown',
                        'number' => $deviceData['number'] ?? $deviceData['phone'] ?? null,
                        'device' => $deviceData['device'] ?? null,
                        'status' => $deviceData['status'] ?? null,
                        'package' => $deviceData['package'] ?? null,
                        'quota' => $deviceData['quota'] ?? $deviceData['remaining_quota'] ?? 0,
                        'autoread' => $deviceData['autoread'] ?? null,
                        'expired' => $deviceData['expired'] ?? $deviceData['expiry'] ?? null,
                        'token' => $deviceData['token'] ?? null,
                        'last_active' => $deviceData['last_active'] ?? $deviceData['lastActive'] ?? null,
                    ]);
                });

                // Hitung statistik
                $this->stats = [
                    'total' => $this->devices->count(),
                    'connected' => $this->devices->where('status', 'connect')->count(),
                    'messages' => $data['messages'] ?? 0
                ];

                $this->error = null;
            } else {
                // Debug error response
                \Log::error('Fonnte API Error Response:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                $this->error = 'Failed to fetch devices: ' . $response->body();
                $this->devices = collect();
            }
        } catch (\Exception $e) {
            \Log::error('Exception in loadDevices:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->error = 'Error: ' . $e->getMessage();
            $this->devices = collect();
        }
    }

    public function getStatusColor($status)
    {
        return match (Str::lower($status)) {
            'connected' => 'green',
            'disconnected' => 'red',
            default => 'gray'
        };
    }

    public function formatExpired($timestamp)
    {
        if (!$timestamp) return '-';

        // Jika timestamp dalam bentuk string angka, konversi ke integer
        if (is_string($timestamp)) {
            $timestamp = intval($timestamp);
        }

        try {
            // Jika timestamp dalam format Unix (10 digit), kalikan 1000 untuk milliseconds
            if (strlen((string)$timestamp) === 10) {
                return Carbon::createFromTimestamp($timestamp)->format('d M Y');
            }
            // Jika timestamp dalam format Unix milliseconds (13 digit)
            else if (strlen((string)$timestamp) === 13) {
                return Carbon::createFromTimestampMs($timestamp)->format('d M Y');
            }
            // Jika format lain, coba parse sebagai string tanggal
            return Carbon::parse($timestamp)->format('d M Y');
        } catch (\Exception $e) {
            return '-';
        }
    }

    public function addDevice(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => env('FONNTE_API_TOKEN')
            ])->asForm()->post('https://api.fonnte.com/add-device', [
                'name' => $data['name'],
                'device' => $data['device'],
                'autoread' => $data['autoread'] ? 'true' : 'false',
                'personal' => $data['personal'] ? 'true' : 'false',
                'group' => $data['group'] ? 'true' : 'false',
            ]);

            if ($response->successful()) {
                Notification::make()
                    ->title('Device added successfully')
                    ->success()
                    ->send();

                // Refresh the devices list
                $this->loadDevices();
            } else {
                Notification::make()
                    ->title('Failed to add device')
                    ->body($response->body())
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error adding device')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    // Ubah dari static menjadi non-static
    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->icon('heroicon-o-arrow-path')
                ->action('loadDevices')
                ->label('Refresh'),
            Action::make('addDevice')
                ->icon('heroicon-o-plus')
                ->label('Tambah Device')
                ->color('success')
                ->slideOver()
                ->form([
                    TextInput::make('name')
                        ->label('Device Name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('device')
                        ->label('Phone Number')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Format: 08123456789'),

                    Toggle::make('autoread')
                        ->label('Auto Read')
                        ->default(false),

                    Toggle::make('personal')
                        ->label('Personal')
                        ->default(false),

                    Toggle::make('group')
                        ->label('Group')
                        ->default(false),
                ])
                ->action(function (array $data) {
                    $this->addDevice($data);
                })
        ];
    }
}
