<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Referral Management</h2>
                            <p class="text-gray-600">Manage client referrals and track referral performance</p>
                        </div>
                        <div class="flex space-x-3">
                            <button wire:click="processExpiredReferrals" 
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                                Process Expired
                            </button>
                            <button wire:click="openCreateModal" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                Create Referral
                            </button>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Total Referrals</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_referrals'] }}</p>
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
                                    <p class="text-sm font-medium text-gray-600">Completed</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $stats['completed'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-purple-50 p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-purple-100 rounded-lg">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Completion Rate</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $stats['completion_rate'] }}%</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-orange-50 p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-orange-100 rounded-lg">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Total Rewards</p>
                                    <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_referrer_rewards'] + $stats['total_referred_rewards'], 2) }}</p>
                        </div>
                    </div>

                    <!-- Status Overview -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
                            <div class="text-sm text-gray-600">Pending</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</div>
                            <div class="text-sm text-gray-600">Completed</div>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <div class="text-2xl font-bold text-red-600">{{ $stats['expired'] }}</div>
                            <div class="text-sm text-gray-600">Expired</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-600">{{ $stats['cancelled'] }}</div>
                            <div class="text-sm text-gray-600">Cancelled</div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input type="text" wire:model.live="search" 
                                       placeholder="Code, email, name..." 
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select wire:model.live="statusFilter" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="expired">Expired</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Method</label>
                                <select wire:model.live="methodFilter" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">All Methods</option>
                                    @foreach($referralMethods as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
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

                    <!-- Referrals Table -->
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referral Code</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referrer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referred</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rewards</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($referrals as $referral)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $referral->referral_code }}</div>
                                            <div class="text-sm text-gray-500">{{ $referral->created_at->format('M j, Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $referral->referrer->user->name ?? 'Unknown' }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $referral->referrer->user->email ?? 'No email' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($referral->referred)
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $referral->referred->user->name ?? 'Unknown' }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $referral->referred->user->email ?? 'No email' }}</div>
                                            @else
                                                <div class="text-sm font-medium text-gray-900">{{ $referral->referred_name ?? 'Unknown' }}</div>
                                                <div class="text-sm text-gray-500">{{ $referral->referred_email ?? 'No email' }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                @if($referral->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($referral->status === 'completed') bg-green-100 text-green-800
                                                @elseif($referral->status === 'expired') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $referral->status_display }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $referral->referral_method_display }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div>Referrer: ${{ number_format($referral->referrer_reward_amount, 2) }}</div>
                                            <div>Referred: ${{ number_format($referral->referred_reward_amount, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $referral->expiry_status }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                @if($referral->status === 'pending')
                                                    <button wire:click="openCompleteModal({{ $referral->id }})" 
                                                            class="text-green-600 hover:text-green-900">Complete</button>
                                                    <button wire:click="openCancelModal({{ $referral->id }})" 
                                                            class="text-red-600 hover:text-red-900">Cancel</button>
                                                @endif
                                                
                                                @if($referral->status === 'pending' && $referral->isExpiredByDate())
                                                    <button wire:click="openExpireModal({{ $referral->id }})" 
                                                            class="text-orange-600 hover:text-orange-900">Expire</button>
                                                @endif
                                                
                                                @if($referral->status === 'completed')
                                                    @if(!$referral->referrer_reward_claimed)
                                                        <button wire:click="openClaimModal({{ $referral->id }}, 'referrer')" 
                                                                class="text-blue-600 hover:text-blue-900">Claim Referrer</button>
                                                    @endif
                                                    @if(!$referral->referred_reward_claimed)
                                                        <button wire:click="openClaimModal({{ $referral->id }}, 'referred')" 
                                                                class="text-purple-600 hover:text-purple-900">Claim Referred</button>
                                                    @endif
                                                @endif
                                                
                                                <button wire:click="openEditModal({{ $referral->id }})" 
                                                        class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                            No referrals found.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
</div>

                        <div class="px-6 py-3 border-t border-gray-200">
                            {{ $referrals->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Referral Modal -->
@if($showCreateModal)
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Referral</h3>
            
            <form wire:submit.prevent="createReferral">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Referrer</label>
                        <select wire:model="referrerId" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Referrer</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->user->name ?? 'Unknown' }} ({{ $client->user->email ?? 'No email' }})</option>
                            @endforeach
                        </select>
                        @error('referrerId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Referred Email</label>
                        <input type="email" wire:model="referredEmail" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('referredEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Referred Name</label>
                        <input type="text" wire:model="referredName" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('referredName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Referred Phone</label>
                        <input type="text" wire:model="referredPhone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('referredPhone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Referral Method</label>
                        <select wire:model="referralMethod" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach($referralMethods as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('referralMethod') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Expiry Date</label>
                        <input type="date" wire:model="expiryDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('expiryDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Referrer Points</label>
                            <input type="number" wire:model="referrerPoints" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('referrerPoints') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Referred Points</label>
                            <input type="number" wire:model="referredPoints" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('referredPoints') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Referrer Reward ($)</label>
                            <input type="number" step="0.01" wire:model="referrerRewardAmount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('referrerRewardAmount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Referred Reward ($)</label>
                            <input type="number" step="0.01" wire:model="referredRewardAmount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('referredRewardAmount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea wire:model="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" wire:click="$set('showCreateModal', false)" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Create Referral
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Complete Referral Modal -->
@if($showCompleteModal)
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Complete Referral</h3>
            
            <form wire:submit.prevent="completeReferral">
                <div class="mb-4">
                    <p class="text-sm text-gray-600">
                        Completing referral: <strong>{{ $selectedReferral->referral_code ?? '' }}</strong>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Completion Reason</label>
                    <input type="text" wire:model="completionReason" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g., New client signed up">
                    @error('completionReason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" wire:click="$set('showCompleteModal', false)" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Complete Referral
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Cancel Referral Modal -->
@if($showCancelModal)
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Cancel Referral</h3>
            
            <form wire:submit.prevent="cancelReferral">
                <div class="mb-4">
                    <p class="text-sm text-gray-600">
                        Cancelling referral: <strong>{{ $selectedReferral->referral_code ?? '' }}</strong>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Cancellation Reason</label>
                    <input type="text" wire:model="cancellationReason" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g., Client declined">
                    @error('cancellationReason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" wire:click="$set('showCancelModal', false)" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Cancel Referral
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Expire Referral Modal -->
@if($showExpireModal)
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Expire Referral</h3>
            
            <form wire:submit.prevent="expireReferral">
                <div class="mb-4">
                    <p class="text-sm text-gray-600">
                        Expiring referral: <strong>{{ $selectedReferral->referral_code ?? '' }}</strong>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Expiry Reason</label>
                    <input type="text" wire:model="expiryReason" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g., Expired by date">
                    @error('expiryReason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" wire:click="$set('showExpireModal', false)" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                        Expire Referral
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Claim Reward Modal -->
@if($showClaimModal)
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Claim Reward</h3>
            
            <form wire:submit.prevent="claimReward">
                <div class="mb-4">
                    <p class="text-sm text-gray-600">
                        Claiming {{ $claimType }} reward for referral: <strong>{{ $selectedReferral->referral_code ?? '' }}</strong>
                    </p>
                    <p class="text-sm text-gray-600">
                        Reward amount: <strong>${{ number_format($claimType === 'referrer' ? $selectedReferral->referrer_reward_amount : $selectedReferral->referred_reward_amount, 2) }}</strong>
                    </p>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" wire:click="$set('showClaimModal', false)" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                        Claim Reward
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif