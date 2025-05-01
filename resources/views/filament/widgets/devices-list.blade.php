<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-4">
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

            <!-- Error State -->
            @if($error)
                <x-filament::card class="bg-red-50 border-l-4 border-red-400">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ $error }}</p>
                        </div>
                    </div>
                </x-filament::card>

            <!-- Device Cards -->
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($devices as $device)
                        <x-filament::card class="border-l-4 border-{{ $this->getStatusColor($device['status'] ?? '') }}-500">
                            <div class="space-y-3">
                                <!-- Device Header -->
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800">{{ $device['name'] ?? 'Unnamed Device' }}</h3>
                                        <p class="text-sm text-gray-500">{{ $device['device'] ?? '-' }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        text-{{ $this->getStatusColor($device['status'] ?? '') }}-800">
                                        {{ $device['status'] ?? 'unknown' }}
                                    </span>
                                </div>

                                <!-- Device Details -->
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <p class="text-gray-500">Package</p>
                                        <p class="font-medium">{{ $device['package'] ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Quota</p>
                                        <p class="font-medium">{{ $device['quota'] ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Auto Read</p>
                                        <p class="font-medium">{{ $device['autoread'] ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Expired</p>
                                        <p class="font-medium">{{ $this->formatExpired($device['expired'] ?? null) }}</p>
                                    </div>
                                </div>

                                <!-- Token (hidden by default, can be toggled) -->
                                <div x-data="{ showToken: false }" class="pt-2 border-t border-gray-200">
                                    <button @click="showToken = !showToken" class="text-xs text-primary-600 hover:text-primary-800">
                                        <span x-show="!showToken">Show Token</span>
                                        <span x-show="showToken">Hide Token</span>
                                    </button>
                                    <div x-show="showToken" class="mt-1 p-2 bg-gray-50 rounded text-xs font-mono break-all">
                                        {{ $device['token'] ?? 'No token available' }}
                                    </div>
                                </div>
                            </div>
                        </x-filament::card>
                    @empty
                        <x-filament::card>
                            <p class="text-gray-500 text-center py-4">No devices found</p>
                        </x-filament::card>
                    @endforelse
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
