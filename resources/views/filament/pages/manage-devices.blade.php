<x-filament-panels::page>
    <div class="space-y-6">
        {{-- <!-- Header with Refresh Button -->
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-medium">Device Management</h2>
            <x-filament::button wire:click="loadDevices" icon="heroicon-o-arrow-path">
                Refresh
            </x-filament::button>
        </div> --}}

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-filament::card class="border-l-4 border-blue-500">
                <h3 class="text-sm font-medium text-gray-500">Total Devices</h3>
                <p class="text-2xl font-semibold text-gray-800">{{ $stats['total'] }}</p>
            </x-filament::card>

            <x-filament::card class="border-l-4 border-green-500">
                <h3 class="text-sm font-medium text-gray-500">Connected</h3>
                <p class="text-2xl font-semibold text-gray-800">{{ $stats['connected'] }}</p>
            </x-filament::card>

            <x-filament::card class="border-l-4 border-purple-500">
                <h3 class="text-sm font-medium text-gray-500">Messages Used</h3>
                <p class="text-2xl font-semibold text-gray-800">{{ $stats['messages'] }}</p>
            </x-filament::card>
        </div>

        <!-- Error Alert -->
        @if($error)
            <x-filament::card class="bg-red-50 border-l-4 border-red-400">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-x-circle class="h-5 w-5 text-red-400"/>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ $error }}</p>
                    </div>
                </div>
            </x-filament::card>
        @endif

        <!-- Devices Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($devices as $device)
                <x-filament::card class="border-l-4 border-{{ $this->getStatusColor($device->status ?? '') }}-500">
                    <div class="space-y-3">
                        <!-- Device Header -->
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">{{ $device->name ?? 'Unnamed Device' }}</h3>
                                <p class="text-sm text-gray-500">{{ $device->device ?? '-' }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                text-{{ $this->getStatusColor($device->status ?? '') }}-800">
                                {{ $device->status ?? 'unknown' }}
                            </span>
                        </div>

                        <!-- Device Details -->
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <p class="text-gray-500">Package</p>
                                <p class="font-medium">{{ $device->package ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Quota</p>
                                <p class="font-medium">{{ $device->quota ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Auto Read</p>
                                <p class="font-medium">{{ $device->autoread ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Expired</p>
                                <p class="font-medium">{{ $this->formatExpired($device->expired ?? null) }}</p>
                            </div>
                        </div>

                        <!-- Token (hidden by default) -->
                        <div x-data="{ showToken: false }" class="pt-2 border-t border-gray-200">
                            <button @click="showToken = !showToken"
                                    class="text-xs text-primary-600 hover:text-primary-800">
                                <span x-show="!showToken">Show Token</span>
                                <span x-show="showToken">Hide Token</span>
                            </button>
                            <div x-show="showToken" class="mt-1 p-2 bg-gray-50 rounded text-xs font-mono break-all">
                                {{ $device->token ?? 'No token available' }}
                            </div>
                        </div>
                    </div>
                </x-filament::card>
            @empty
                <x-filament::card class="col-span-full">
                    <p class="text-gray-500 text-center py-4">No devices found</p>
                </x-filament::card>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>
