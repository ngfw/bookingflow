<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Predictive Analytics</h1>
                <p class="text-gray-600">Advanced forecasting and predictive insights for business growth</p>
            </div>
            <div class="flex space-x-4">
                <select wire:model.live="forecastPeriod" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="1_month">1 Month</option>
                    <option value="3_months">3 Months</option>
                    <option value="6_months">6 Months</option>
                    <option value="12_months">12 Months</option>
                </select>
                <div class="flex items-center space-x-2">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model.live="includeSeasonality" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Seasonality</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model.live="includeTrends" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Trends</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Forecast -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Forecast</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="p-4 bg-blue-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-900">Forecast Accuracy</p>
                        <p class="text-xs text-blue-600">Model confidence</p>
                    </div>
                    <span class="text-sm font-semibold text-blue-700">{{ number_format($revenueForecast['accuracy'], 1) }}%</span>
                </div>
            </div>

            <div class="p-4 bg-green-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-900">Trend Direction</p>
                        <p class="text-xs text-green-600">Revenue trend</p>
                    </div>
                    <span class="text-sm font-semibold text-green-700 capitalize">{{ $revenueForecast['trend']['direction'] }}</span>
                </div>
            </div>

            <div class="p-4 bg-purple-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-900">Seasonality</p>
                        <p class="text-xs text-purple-600">Pattern strength</p>
                    </div>
                    <span class="text-sm font-semibold text-purple-700">{{ number_format($revenueForecast['seasonality']['strength'], 1) }}%</span>
                </div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="h-64 flex items-end space-x-2">
            @foreach($revenueForecast['historical'] as $index => $data)
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-gradient-to-t from-blue-500 to-blue-300 rounded-t" style="height: {{ $data['revenue'] > 0 ? ($data['revenue'] / max(collect($revenueForecast['historical'])->pluck('revenue')->toArray())) * 150 : 2 }}px;"></div>
                    <span class="text-xs text-gray-500 mt-2">{{ $data['month'] }}</span>
                    <span class="text-xs text-gray-700 font-semibold">${{ number_format($data['revenue']) }}</span>
                </div>
            @endforeach
            
            @foreach($revenueForecast['forecast'] as $index => $data)
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-gradient-to-t from-green-500 to-green-300 rounded-t opacity-75" style="height: {{ $data['revenue'] > 0 ? ($data['revenue'] / max(collect($revenueForecast['historical'])->pluck('revenue')->toArray())) * 150 : 2 }}px;"></div>
                    <span class="text-xs text-gray-500 mt-2">{{ $data['month'] }}</span>
                    <span class="text-xs text-gray-700 font-semibold">${{ number_format($data['revenue']) }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Appointment Forecast -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Appointment Demand Forecast</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Peak Periods -->
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-3">Peak Periods</h4>
                <div class="space-y-2">
                    @forelse($appointmentForecast['peak_periods'] as $peak)
                        <div class="p-3 bg-yellow-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-yellow-900">{{ $peak['month'] }}</span>
                                <span class="text-sm font-semibold text-yellow-700">{{ $peak['appointments'] }} appointments</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No peak periods identified</p>
                    @endforelse
                </div>
            </div>

            <!-- Demand Patterns -->
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-3">Demand Patterns</h4>
                <div class="space-y-2">
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-blue-900">Peak Days</span>
                            <span class="text-sm font-semibold text-blue-700">
                                @foreach($appointmentForecast['demand_patterns']['peak_days'] as $day)
                                    {{ ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][$day - 1] }}
                                    @if(!$loop->last), @endif
                                @endforeach
                            </span>
                        </div>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-green-900">Peak Hours</span>
                            <span class="text-sm font-semibold text-green-700">
                                @foreach($appointmentForecast['demand_patterns']['peak_hours'] as $hour)
                                    {{ Carbon\Carbon::createFromTime($hour)->format('g A') }}
                                    @if(!$loop->last), @endif
                                @endforeach
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Client Behavior Predictions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Client Churn Prediction -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Client Churn Prediction</h3>
            <div class="space-y-4">
                <div class="p-4 bg-red-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-900">At-Risk Clients</p>
                            <p class="text-xs text-red-600">Potential churn</p>
                        </div>
                        <span class="text-sm font-semibold text-red-700">{{ $clientPredictions['churn_prediction']['at_risk_count'] }}</span>
                    </div>
                </div>

                <div class="p-4 bg-yellow-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-yellow-900">At-Risk Percentage</p>
                            <p class="text-xs text-yellow-600">Of total clients</p>
                        </div>
                        <span class="text-sm font-semibold text-yellow-700">{{ number_format($clientPredictions['churn_prediction']['at_risk_percentage'], 1) }}%</span>
                    </div>
                </div>

                <div class="p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-900">Retention Rate</p>
                            <p class="text-xs text-green-600">Client loyalty</p>
                        </div>
                        <span class="text-sm font-semibold text-green-700">{{ number_format($clientPredictions['churn_prediction']['retention_rate'], 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Client Lifetime Value -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Client Lifetime Value</h3>
            <div class="space-y-4">
                <div class="p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-900">Average CLV</p>
                            <p class="text-xs text-blue-600">Per client</p>
                        </div>
                        <span class="text-sm font-semibold text-blue-700">${{ number_format($clientPredictions['lifetime_value_prediction']['average_clv'], 2) }}</span>
                    </div>
                </div>

                <div class="p-4 bg-purple-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-900">Average Appointments</p>
                            <p class="text-xs text-purple-600">Per client</p>
                        </div>
                        <span class="text-sm font-semibold text-purple-700">{{ number_format($clientPredictions['lifetime_value_prediction']['average_appointments'], 1) }}</span>
                    </div>
                </div>

                <div class="p-4 bg-indigo-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-indigo-900">CLV Trend</p>
                            <p class="text-xs text-indigo-600">Direction</p>
                        </div>
                        <span class="text-sm font-semibold text-indigo-700 capitalize">{{ $clientPredictions['lifetime_value_prediction']['clv_trend'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Performance Predictions -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Staff Performance Predictions</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="p-4 bg-blue-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-900">Current Capacity</p>
                        <p class="text-xs text-blue-600">Per staff member</p>
                    </div>
                    <span class="text-sm font-semibold text-blue-700">{{ number_format($staffPredictions['capacity_optimization']['current_capacity'], 1) }}</span>
                </div>
            </div>

            <div class="p-4 bg-green-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-900">Optimal Capacity</p>
                        <p class="text-xs text-green-600">Recommended</p>
                    </div>
                    <span class="text-sm font-semibold text-green-700">{{ number_format($staffPredictions['capacity_optimization']['optimal_capacity'], 1) }}</span>
                </div>
            </div>

            <div class="p-4 bg-purple-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-900">Utilization</p>
                        <p class="text-xs text-purple-600">Current usage</p>
                    </div>
                    <span class="text-sm font-semibold text-purple-700">{{ number_format($staffPredictions['capacity_optimization']['capacity_utilization'], 1) }}%</span>
                </div>
            </div>
        </div>

        <!-- Skill Gaps -->
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Skill Gaps & Training Needs</h4>
            <div class="space-y-2">
                @forelse($staffPredictions['skill_gaps'] as $gap)
                    <div class="p-3 bg-red-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-red-900">{{ $gap['service']->name }}</p>
                                <p class="text-xs text-red-600">{{ $gap['qualified_staff'] }} qualified staff for {{ $gap['demand'] }} bookings</p>
                            </div>
                            <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">{{ $gap['priority'] }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No significant skill gaps identified</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Business Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Growth Opportunities -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Growth Opportunities</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-medium text-gray-900 mb-2">Service Expansion</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($businessInsights['growth_opportunities']['service_expansion'] as $opportunity)
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">{{ $opportunity }}</span>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-900 mb-2">Time Expansion</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($businessInsights['growth_opportunities']['time_expansion'] as $opportunity)
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">{{ $opportunity }}</span>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-900 mb-2">Client Segments</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($businessInsights['growth_opportunities']['client_segments'] as $opportunity)
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">{{ $opportunity }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Factors -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Risk Factors</h3>
            <div class="space-y-4">
                <div class="p-4 bg-red-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-900">Client Churn Risk</p>
                            <p class="text-xs text-red-600">At-risk percentage</p>
                        </div>
                        <span class="text-sm font-semibold text-red-700">{{ number_format($businessInsights['risk_factors']['client_churn_risk'], 1) }}%</span>
                    </div>
                </div>

                <div class="p-4 bg-yellow-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-yellow-900">Staff Turnover Risk</p>
                            <p class="text-xs text-yellow-600">Potential turnover</p>
                        </div>
                        <span class="text-sm font-semibold text-yellow-700">{{ $businessInsights['risk_factors']['staff_turnover_risk'] }}%</span>
                    </div>
                </div>

                <div class="p-4 bg-orange-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-orange-900">Seasonal Downturn</p>
                            <p class="text-xs text-orange-600">Risk period</p>
                        </div>
                        <span class="text-sm font-semibold text-orange-700">{{ $businessInsights['risk_factors']['seasonal_downturn'] }}</span>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Competition Risk</p>
                            <p class="text-xs text-gray-600">Market threat</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">{{ $businessInsights['risk_factors']['competition_risk'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Market Analysis -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Market Analysis</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 bg-blue-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-900">Industry Growth</p>
                        <p class="text-xs text-blue-600">Annual rate</p>
                    </div>
                    <span class="text-sm font-semibold text-blue-700">{{ $businessInsights['market_trends']['industry_growth'] }}%</span>
                </div>
            </div>

            <div class="p-4 bg-green-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-900">Market Position</p>
                        <p class="text-xs text-green-600">Competitive stance</p>
                    </div>
                    <span class="text-sm font-semibold text-green-700">{{ $businessInsights['competitive_analysis']['market_position'] }}</span>
                </div>
            </div>

            <div class="p-4 bg-purple-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-900">Market Size</p>
                        <p class="text-xs text-purple-600">Growth trend</p>
                    </div>
                    <span class="text-sm font-semibold text-purple-700">{{ $businessInsights['market_trends']['market_size'] }}</span>
                </div>
            </div>

            <div class="p-4 bg-yellow-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-900">Competition Risk</p>
                        <p class="text-xs text-yellow-600">Threat level</p>
                    </div>
                    <span class="text-sm font-semibold text-yellow-700">{{ $businessInsights['risk_factors']['competition_risk'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>