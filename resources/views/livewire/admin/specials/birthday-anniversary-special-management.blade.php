<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Birthday & Anniversary Specials</h2>
                            <p class="text-gray-600">Manage special offers for client birthdays and anniversaries</p>
                        </div>
                        <div class="flex space-x-3">
                            <button wire:click="processUpcomingEvents" 
                                    class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg">
                                Process Upcoming Events
                            </button>
                            <button wire:click="processExpiredSpecials" 
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                                Process Expired
                            </button>
                            <button wire:click="openCreateModal" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                Create Special
                            </button>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Total Specials</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_specials'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Active</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-purple-50 p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-purple-100 rounded-lg">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Total Usage</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_usage'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-orange-50 p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-orange-100 rounded-lg">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Discount Given</p>
                                    <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_discount_given'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Type Overview -->
                    <div class="grid grid-cols-3 gap-4 mb-8">
                        <div class="text-center p-4 bg-pink-50 rounded-lg">
                            <div class="text-2xl font-bold text-pink-600">{{ $stats['birthday_specials'] }}</div>
                            <div class="text-sm text-gray-600">Birthday Specials</div>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <div class="text-2xl font-bold text-red-600">{{ $stats['anniversary_specials'] }}</div>
                            <div class="text-sm text-gray-600">Anniversary Specials</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">{{ $stats['both_specials'] }}</div>
                            <div class="text-sm text-gray-600">Both Types</div>
                        </div>
                    </div>

                    <!-- Upcoming Events -->
                    <div class="bg-gray-50 p-6 rounded-lg mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Events (Next 30 Days)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @forelse($upcomingEvents as $event)
                            <div class="bg-white p-4 rounded-lg border">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $event->user->name }}</div>
                                        <div class="text-sm text-gray-600">{{ $event->user->email }}</div>
                                        <div class="text-sm text-blue-600">{{ $event->user->date_of_birth ? \Carbon\Carbon::parse($event->user->date_of_birth)->format('M j') : 'No birthday set' }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-500">{{ $event->user->date_of_birth ? \Carbon\Carbon::parse($event->user->date_of_birth)->diffForHumans() : '' }}</div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-span-full text-center text-gray-500 py-8">
                                No upcoming events found.
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input type="text" wire:model.live="search" 
                                       placeholder="Name, description..." 
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select wire:model.live="statusFilter" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">All Statuses</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                <select wire:model.live="typeFilter" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">All Types</option>
                                    <option value="birthday">Birthday</option>
                                    <option value="anniversary">Anniversary</option>
                                    <option value="both">Both</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                                <div class="flex space-x-2">
                                    <input type="date" wire:model.live="dateFrom" class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <input type="date" wire:model.live="dateTo" class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button wire:click="clearFilters" class="text-blue-600 hover:text-blue-800 text-sm">
                                Clear Filters
                            </button>
                        </div>
                    </div>

                    <!-- Specials Table -->
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Special</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Validity Window</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($specials as $special)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $special->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $special->description }}</div>
                                                @if($special->auto_apply)
                                                    <div class="text-xs text-green-600">Auto-apply enabled</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $special->type_display }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                @if($special->status === 'active') bg-green-100 text-green-800
                                                @elseif($special->status === 'inactive') bg-gray-100 text-gray-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ $special->status_display }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $special->validity_window }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div>{{ $special->clientSpecialUsage()->where('status', 'used')->count() }} uses</div>
                                            <div class="text-xs text-gray-500">Limit: {{ $special->usage_limit_per_client }}/year</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                @if($special->status === 'inactive')
                                                    <button wire:click="activateSpecial({{ $special->id }})" 
                                                            class="text-green-600 hover:text-green-900">Activate</button>
                                                @endif
                                                @if($special->status === 'active')
                                                    <button wire:click="deactivateSpecial({{ $special->id }})" 
                                                            class="text-yellow-600 hover:text-yellow-900">Deactivate</button>
                                                @endif
                                                <button wire:click="openEditModal({{ $special->id }})" 
                                                        class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                                @if($special->status !== 'expired')
                                                    <button wire:click="expireSpecial({{ $special->id }})" 
                                                            class="text-red-600 hover:text-red-900">Expire</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No birthday/anniversary specials found.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="px-6 py-3 border-t border-gray-200">
                            {{ $specials->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

