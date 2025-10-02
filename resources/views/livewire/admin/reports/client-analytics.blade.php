<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Client Analytics</h1>
                <p class="text-gray-600">Comprehensive client insights and behavior analysis</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                <input type="date" wire:model.live="dateFrom" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                <input type="date" wire:model.live="dateTo" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Client Segment</label>
                <select wire:model.live="clientSegment" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Clients</option>
                    <option value="vip">VIP Clients</option>
                    <option value="regular">Regular Clients</option>
                    <option value="new">New Clients</option>
                    <option value="at_risk">At-Risk Clients</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Client Retention Rate</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($retentionRate, 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Client Acquisition Rate</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($acquisitionRate, 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Avg Appointments/Client</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($behaviorInsights['avg_appointments_per_client'], 1) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Avg Spending/Client</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($behaviorInsights['avg_spending_per_client'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Client Segments -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Client Segments</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-900">VIP Clients</p>
                        <p class="text-xs text-purple-600">Top spenders</p>
                    </div>
                    <div class="text-2xl font-bold text-purple-900">{{ $segments['vip'] }}</div>
                </div>
            </div>

            <div class="p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-900">Regular Clients</p>
                        <p class="text-xs text-blue-600">Steady customers</p>
                    </div>
                    <div class="text-2xl font-bold text-blue-900">{{ $segments['regular'] }}</div>
                </div>
            </div>

            <div class="p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-900">New Clients</p>
                        <p class="text-xs text-green-600">First-time visitors</p>
                    </div>
                    <div class="text-2xl font-bold text-green-900">{{ $segments['new'] }}</div>
                </div>
            </div>

            <div class="p-4 bg-gradient-to-r from-red-50 to-red-100 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-900">At-Risk Clients</p>
                        <p class="text-xs text-red-600">Need attention</p>
                    </div>
                    <div class="text-2xl font-bold text-red-900">{{ $segments['at_risk'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Client Behavior Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Most Popular Services -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Most Popular Services</h3>
            <div class="space-y-3">
                @forelse($behaviorInsights['most_popular_services'] as $service)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $service['service_name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $service['count'] }} bookings</p>
                        </div>
                        <div class="w-20 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($service['count'] / max($behaviorInsights['most_popular_services']->pluck('count')->toArray())) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No data available</p>
                @endforelse
            </div>
        </div>

        <!-- Peak Booking Times -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Peak Booking Times</h3>
            <div class="space-y-3">
                @forelse($behaviorInsights['peak_booking_times'] as $time)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $time['time_label'] }}</p>
                            <p class="text-xs text-gray-500">{{ $time['count'] }} appointments</p>
                        </div>
                        <div class="w-20 bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($time['count'] / max($behaviorInsights['peak_booking_times']->pluck('count')->toArray())) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Client Lifetime Value Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Client Lifetime Value Analysis</h3>
                <div class="flex space-x-2">
                    <button wire:click="sortBy('total_spent')" class="px-3 py-1 text-xs font-medium {{ $sortBy === 'total_spent' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }} rounded-full">
                        Total Spent
                    </button>
                    <button wire:click="sortBy('appointment_count')" class="px-3 py-1 text-xs font-medium {{ $sortBy === 'appointment_count' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }} rounded-full">
                        Appointments
                    </button>
                    <button wire:click="sortBy('avg_appointment_value')" class="px-3 py-1 text-xs font-medium {{ $sortBy === 'avg_appointment_value' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }} rounded-full">
                        Avg Value
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appointments</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">First Visit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Visit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Since Last</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($clientLifetimeValue as $clientData)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ substr($clientData['client']->user->name, 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $clientData['client']->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $clientData['client']->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                ${{ number_format($clientData['total_spent'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $clientData['appointment_count'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($clientData['avg_appointment_value'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $clientData['first_appointment'] ? Carbon\Carbon::parse($clientData['first_appointment'])->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $clientData['last_appointment'] ? Carbon\Carbon::parse($clientData['last_appointment'])->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($clientData['days_since_last'])
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $clientData['days_since_last'] > 90 ? 'bg-red-100 text-red-800' : ($clientData['days_since_last'] > 30 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        {{ $clientData['days_since_last'] }} days
                                    </span>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No client data available for the selected period</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $clientLifetimeValue->links() }}
        </div>
    </div>
</div>