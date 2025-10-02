<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Business Intelligence Dashboard</h1>
                <p class="text-gray-600">Advanced analytics and strategic insights for business growth</p>
            </div>
            <div class="flex space-x-4">
                <select wire:model.live="timeframe" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Revenue Growth</p>
                    <p class="text-2xl font-semibold text-gray-900 {{ $kpis['revenue_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $kpis['revenue_growth'] >= 0 ? '+' : '' }}{{ number_format($kpis['revenue_growth'], 1) }}%
                    </p>
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
                    <p class="text-sm font-medium text-gray-600">Appointment Growth</p>
                    <p class="text-2xl font-semibold text-gray-900 {{ $kpis['appointment_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $kpis['appointment_growth'] >= 0 ? '+' : '' }}{{ number_format($kpis['appointment_growth'], 1) }}%
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Client Growth</p>
                    <p class="text-2xl font-semibold text-gray-900 {{ $kpis['client_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $kpis['client_growth'] >= 0 ? '+' : '' }}{{ number_format($kpis['client_growth'], 1) }}%
                    </p>
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
                    <p class="text-sm font-medium text-gray-600">Avg Appointment Value</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($kpis['avg_appointment_value'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-indigo-100 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Client Retention</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($kpis['client_retention_rate'], 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Staff Utilization</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($kpis['staff_utilization'], 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-teal-100 rounded-lg">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Peak Hours Efficiency</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($kpis['peak_hours_efficiency'], 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-pink-100 rounded-lg">
                    <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Service Popularity</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($kpis['service_popularity_index'], 1) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Trends Chart -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Trends (Last 12 Months)</h3>
        <div class="h-64 flex items-end space-x-2">
            @foreach($trends['revenue'] as $index => $amount)
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-gradient-to-t from-blue-500 to-blue-300 rounded-t" style="height: {{ $amount > 0 ? ($amount / max($trends['revenue'])) * 200 : 2 }}px;"></div>
                    <span class="text-xs text-gray-500 mt-2">{{ $trends['months'][$index] }}</span>
                    <span class="text-xs text-gray-700 font-semibold">${{ number_format($amount) }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Business Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Top Performers -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performers</h3>
            <div class="space-y-4">
                @if($insights['best_performing_service'])
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Best Service</p>
                                <p class="text-xs text-gray-500">{{ $insights['best_performing_service']->service->name ?? 'Unknown' }}</p>
                            </div>
                            <span class="text-sm font-semibold text-gray-700">{{ $insights['best_performing_service']->count }} bookings</span>
                        </div>
                    </div>
                @endif

                @if($insights['top_revenue_staff'])
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Top Revenue Staff</p>
                                <p class="text-xs text-gray-500">{{ $insights['top_revenue_staff']['staff']->user->name ?? 'Unknown' }}</p>
                            </div>
                            <span class="text-sm font-semibold text-gray-700">${{ number_format($insights['top_revenue_staff']['revenue'], 2) }}</span>
                        </div>
                    </div>
                @endif

                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Peak Booking Day</p>
                            <p class="text-xs text-gray-500">Most popular day</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">{{ $insights['peak_booking_day'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Forecast -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Forecast</h3>
            <div class="space-y-4">
                <div class="p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-900">Next Month Forecast</p>
                            <p class="text-xs text-blue-600">Predicted revenue</p>
                        </div>
                        <span class="text-sm font-semibold text-blue-700">${{ number_format($insights['revenue_forecast']['next_month'], 2) }}</span>
                    </div>
                </div>

                <div class="p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-900">Growth Rate</p>
                            <p class="text-xs text-green-600">Monthly trend</p>
                        </div>
                        <span class="text-sm font-semibold text-green-700">${{ number_format($insights['revenue_forecast']['growth_rate'], 2) }}</span>
                    </div>
                </div>

                <div class="p-4 bg-purple-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-900">Confidence Level</p>
                            <p class="text-xs text-purple-600">Forecast accuracy</p>
                        </div>
                        <span class="text-sm font-semibold text-purple-700">{{ number_format($insights['revenue_forecast']['confidence'], 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Operational Metrics -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Operational Metrics</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Completion Rate</p>
                        <p class="text-xs text-gray-500">Appointments completed</p>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">{{ number_format($operationalMetrics['appointment_completion_rate'], 1) }}%</span>
                </div>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Staff Efficiency</p>
                        <p class="text-xs text-gray-500">Performance score</p>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">{{ number_format($operationalMetrics['staff_efficiency_score'], 1) }}%</span>
                </div>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Client Satisfaction</p>
                        <p class="text-xs text-gray-500">Proxy score</p>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">{{ number_format($operationalMetrics['client_satisfaction_proxy'], 1) }}%</span>
                </div>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Capacity Utilization</p>
                        <p class="text-xs text-gray-500">Space efficiency</p>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">{{ number_format($insights['capacity_utilization'], 1) }}%</span>
                </div>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Revenue/Sq Ft</p>
                        <p class="text-xs text-gray-500">Space productivity</p>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">${{ number_format($operationalMetrics['revenue_per_square_foot'], 2) }}</span>
                </div>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Inventory Turnover</p>
                        <p class="text-xs text-gray-500">Stock efficiency</p>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">{{ number_format($operationalMetrics['inventory_turnover'], 1) }}x</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Market Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Service Mix Analysis -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Service Mix Analysis</h3>
            <div class="space-y-3">
                @forelse($marketAnalysis['service_mix_analysis']->take(5) as $service)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $service['service']->name }}</p>
                            <p class="text-xs text-gray-500">{{ $service['appointment_count'] }} bookings</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">${{ number_format($service['revenue_potential'], 2) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No service data available</p>
                @endforelse
            </div>
        </div>

        <!-- Growth Opportunities -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Growth Opportunities</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-medium text-gray-900 mb-2">Service Expansion</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($marketAnalysis['growth_opportunities']['service_expansion'] as $opportunity)
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">{{ $opportunity }}</span>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-900 mb-2">Time Expansion</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($marketAnalysis['growth_opportunities']['time_expansion'] as $opportunity)
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">{{ $opportunity }}</span>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-900 mb-2">Technology Upgrades</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($marketAnalysis['growth_opportunities']['technology_upgrades'] as $opportunity)
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">{{ $opportunity }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Competitive Positioning -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Competitive Positioning</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 bg-blue-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-900">Market Share</p>
                        <p class="text-xs text-blue-600">Local market</p>
                    </div>
                    <span class="text-sm font-semibold text-blue-700">{{ $marketAnalysis['competitive_positioning']['market_share'] }}%</span>
                </div>
            </div>

            <div class="p-4 bg-green-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-900">Price Positioning</p>
                        <p class="text-xs text-green-600">Market position</p>
                    </div>
                    <span class="text-sm font-semibold text-green-700">{{ $marketAnalysis['competitive_positioning']['price_positioning'] }}</span>
                </div>
            </div>

            <div class="p-4 bg-purple-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-900">Service Differentiation</p>
                        <p class="text-xs text-purple-600">Competitive advantage</p>
                    </div>
                    <span class="text-sm font-semibold text-purple-700">{{ $marketAnalysis['competitive_positioning']['service_differentiation'] }}</span>
                </div>
            </div>

            <div class="p-4 bg-yellow-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-900">Client Retention</p>
                        <p class="text-xs text-yellow-600">Customer loyalty</p>
                    </div>
                    <span class="text-sm font-semibold text-yellow-700">{{ number_format($marketAnalysis['competitive_positioning']['client_retention'], 1) }}%</span>
                </div>
            </div>
        </div>
    </div>
</div>