<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Analytics Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <select wire:model="selectedPeriod" 
                            class="border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                        <option value="7d">Last 7 days</option>
                        <option value="30d">Last 30 days</option>
                        <option value="90d">Last 90 days</option>
                        <option value="1y">Last year</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Page Views -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Page Views</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ number_format($summary['page_views']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Sessions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Sessions</h3>
                        <p class="text-3xl font-bold text-green-600">{{ number_format($summary['sessions']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Unique Visitors -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Unique Visitors</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ number_format($summary['unique_visitors']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Bounce Rate -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Bounce Rate</h3>
                        <p class="text-3xl font-bold text-red-600">{{ $summary['bounce_rate'] }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Top Pages -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Top Pages</h3>
                <div class="space-y-3">
                    @forelse($topPages as $page)
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $page->page_title ?: $page->page_url }}</p>
                                <p class="text-xs text-gray-500">{{ $page->page_url }}</p>
                            </div>
                            <div class="text-sm font-medium text-gray-900">{{ number_format($page->views) }}</div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No page views data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Traffic Sources -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Traffic Sources</h3>
                <div class="space-y-3">
                    @forelse($trafficSources as $source)
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $source->referrer ?: 'Direct' }}</p>
                            </div>
                            <div class="text-sm font-medium text-gray-900">{{ number_format($source->count) }}</div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No traffic source data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Device and Location Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Device Breakdown -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Device Breakdown</h3>
                <div class="space-y-3">
                    @forelse($deviceBreakdown as $device => $count)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-pink-600 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-900 capitalize">{{ $device }}</span>
                            </div>
                            <div class="text-sm font-medium text-gray-900">{{ number_format($count) }}</div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No device data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Location Breakdown -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Location Breakdown</h3>
                <div class="space-y-3">
                    @forelse($locationBreakdown as $location => $count)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-600 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-900">{{ $location }}</span>
                            </div>
                            <div class="text-sm font-medium text-gray-900">{{ number_format($count) }}</div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No location data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Real-time Visitors -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Real-time Visitors (Last 5 minutes)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($realTimeVisitors as $visitor)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-900">Session {{ substr($visitor->session_id, 0, 8) }}...</span>
                            <span class="text-xs text-green-600">● Active</span>
                        </div>
                        <div class="text-xs text-gray-500">
                            <p>Pages: {{ $visitor->page_views }}</p>
                            <p>Started: {{ $visitor->started_at->diffForHumans() }}</p>
                            @if($visitor->location_info)
                                <p>Location: {{ $visitor->location_info['country'] ?? 'Unknown' }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8">
                        <p class="text-gray-500">No active visitors</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Conversions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Conversions</h3>
            <div class="space-y-3">
                @forelse($conversions->take(10) as $conversion)
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ ucfirst($conversion->event_name) }}</p>
                            <p class="text-xs text-gray-500">{{ $conversion->page_url }}</p>
                            <p class="text-xs text-gray-400">{{ $conversion->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="text-sm font-medium text-green-600">
                            {{ $conversion->event_data['value'] ?? 'N/A' }}
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No conversions recorded</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
