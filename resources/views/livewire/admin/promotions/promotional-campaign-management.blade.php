<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Promotional Campaigns</h2>
                            <p class="text-gray-600">Manage marketing campaigns and promotions</p>
                        </div>
                        <div class="flex space-x-3">
                            <button wire:click="processExpiredCampaigns" 
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                                Process Expired
                            </button>
                            <button wire:click="openCreateModal" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                Create Campaign
                            </button>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-10 0a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2M9 9h6m-6 4h6m-6 4h6"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Total Campaigns</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_campaigns'] }}</p>
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

                    <!-- Status Overview -->
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-600">{{ $stats['draft'] }}</div>
                            <div class="text-sm text-gray-600">Draft</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</div>
                            <div class="text-sm text-gray-600">Active</div>
                        </div>
                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">{{ $stats['paused'] }}</div>
                            <div class="text-sm text-gray-600">Paused</div>
                        </div>
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $stats['completed'] }}</div>
                            <div class="text-sm text-gray-600">Completed</div>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <div class="text-2xl font-bold text-red-600">{{ $stats['cancelled'] }}</div>
                            <div class="text-sm text-gray-600">Cancelled</div>
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
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                    <option value="paused">Paused</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                <select wire:model.live="typeFilter" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">All Types</option>
                                    <option value="discount">Discount</option>
                                    <option value="bonus_points">Bonus Points</option>
                                    <option value="free_service">Free Service</option>
                                    <option value="package_deal">Package Deal</option>
                                    <option value="seasonal">Seasonal</option>
                                    <option value="referral_bonus">Referral Bonus</option>
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

                    <!-- Campaigns Table -->
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($campaigns as $campaign)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $campaign->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $campaign->description }}</div>
                                                @if($campaign->promo_code)
                                                    <div class="text-xs text-blue-600">Code: {{ $campaign->promo_code }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $campaign->type_display }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                @if($campaign->status === 'draft') bg-gray-100 text-gray-800
                                                @elseif($campaign->status === 'active') bg-green-100 text-green-800
                                                @elseif($campaign->status === 'paused') bg-yellow-100 text-yellow-800
                                                @elseif($campaign->status === 'completed') bg-blue-100 text-blue-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ $campaign->status_display }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div>{{ $campaign->start_date->format('M j, Y') }}</div>
                                            @if($campaign->end_date)
                                                <div>{{ $campaign->end_date->format('M j, Y') }}</div>
                                            @else
                                                <div class="text-gray-500">Ongoing</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div>{{ $campaign->current_usage }}{{ $campaign->usage_limit ? ' / ' . $campaign->usage_limit : '' }}</div>
                                            @if($campaign->usage_limit)
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $campaign->usage_percentage }}%"></div>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                @if($campaign->status === 'draft')
                                                    <button wire:click="activateCampaign({{ $campaign->id }})" 
                                                            class="text-green-600 hover:text-green-900">Activate</button>
                                                @endif
                                                @if($campaign->status === 'active')
                                                    <button wire:click="pauseCampaign({{ $campaign->id }})" 
                                                            class="text-yellow-600 hover:text-yellow-900">Pause</button>
                                                @endif
                                                @if($campaign->status === 'paused')
                                                    <button wire:click="activateCampaign({{ $campaign->id }})" 
                                                            class="text-green-600 hover:text-green-900">Resume</button>
                                                @endif
                                                <button wire:click="openEditModal({{ $campaign->id }})" 
                                                        class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                                @if($campaign->status !== 'completed')
                                                    <button wire:click="cancelCampaign({{ $campaign->id }})" 
                                                            class="text-red-600 hover:text-red-900">Cancel</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No promotional campaigns found.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="px-6 py-3 border-t border-gray-200">
                            {{ $campaigns->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

