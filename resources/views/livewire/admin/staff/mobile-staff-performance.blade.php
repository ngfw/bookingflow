<div class="min-h-screen bg-gray-50">
    <!-- Mobile Header -->
    <div class="bg-white shadow-sm sticky top-0 z-10">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold text-gray-900">Staff Performance</h1>
                    <p class="text-sm text-gray-600">Track staff metrics</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Content -->
    <div class="px-4 py-6">
        <!-- Mobile Filters -->
        <div class="bg-white rounded-xl shadow-sm mb-6">
            <div class="p-4">
                <!-- Staff Selector -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Staff Member</label>
                    <div class="relative">
                        <button wire:click="toggleStaffSelector" 
                                class="w-full text-left px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @if($selectedStaff)
                                {{ $staff->find($selectedStaff)->user->name ?? 'Select Staff' }}
                            @else
                                Select Staff Member
                            @endif
                        </button>
                        @if($showStaffSelector)
                            <div class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-20">
                                @foreach($staff as $staffMember)
                                    <button wire:click="$set('selectedStaff', '{{ $staffMember->id }}')" 
                                            class="w-full text-left px-4 py-2 hover:bg-gray-50 {{ $selectedStaff == $staffMember->id ? 'bg-blue-50' : '' }}">
                                        {{ $staffMember->user->name }}
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Period Selector -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Time Period</label>
                    <div class="relative">
                        <button wire:click="togglePeriodSelector" 
                                class="w-full text-left px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            {{ collect($periodOptions)->firstWhere('value', $selectedPeriod)['label'] ?? 'Select Period' }}
                        </button>
                        @if($showPeriodSelector)
                            <div class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-20">
                                @foreach($periodOptions as $option)
                                    <button wire:click="$set('selectedPeriod', '{{ $option['value'] }}')" 
                                            class="w-full text-left px-4 py-2 hover:bg-gray-50 {{ $selectedPeriod == $option['value'] ? 'bg-blue-50' : '' }}">
                                        {{ $option['label'] }}
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if(empty($performanceData))
            <!-- No Data State -->
            <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Select a Staff Member</h3>
                <p class="text-gray-600">Choose a staff member to view their performance metrics.</p>
            </div>
        @else
            <!-- Performance Overview -->
            <div class="bg-white rounded-xl shadow-sm mb-6">
                <div class="p-4">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">{{ $performanceData['staff_name'] }}</h2>
                        <p class="text-sm text-gray-600">{{ $performanceData['period'] }}</p>
                    </div>

                    <!-- Key Metrics -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $performanceData['total_appointments'] }}</div>
                            <div class="text-sm text-gray-600">Total Appointments</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">${{ number_format($performanceData['total_revenue'], 0) }}</div>
                            <div class="text-sm text-gray-600">Total Revenue</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ number_format($performanceData['completion_rate'], 1) }}%</div>
                            <div class="text-sm text-gray-600">Completion Rate</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ number_format($performanceData['satisfaction_score'], 1) }}%</div>
                            <div class="text-sm text-gray-600">Satisfaction</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Metrics -->
            <div class="space-y-4">
                <!-- Appointment Breakdown -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Appointment Breakdown</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                    <span class="text-sm text-gray-700">Completed</span>
                                </div>
                                <span class="font-semibold">{{ $performanceData['completed_appointments'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                                    <span class="text-sm text-gray-700">Cancelled</span>
                                </div>
                                <span class="font-semibold">{{ $performanceData['cancelled_appointments'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                                    <span class="text-sm text-gray-700">No Show</span>
                                </div>
                                <span class="font-semibold">{{ $performanceData['no_show_appointments'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Rates -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Rates</h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-700">Completion Rate</span>
                                    <span class="text-sm font-semibold">{{ number_format($performanceData['completion_rate'], 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $performanceData['completion_rate'] }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-700">Cancellation Rate</span>
                                    <span class="text-sm font-semibold">{{ number_format($performanceData['cancellation_rate'], 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-red-500 h-2 rounded-full" style="width: {{ $performanceData['cancellation_rate'] }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-700">No Show Rate</span>
                                    <span class="text-sm font-semibold">{{ number_format($performanceData['no_show_rate'], 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $performanceData['no_show_rate'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Metrics -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Financial Performance</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-700">Total Revenue</span>
                                <span class="font-semibold text-green-600">${{ number_format($performanceData['total_revenue'], 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-700">Average Appointment Value</span>
                                <span class="font-semibold">${{ number_format($performanceData['average_appointment_value'], 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-700">Completed Appointments</span>
                                <span class="font-semibold">{{ $performanceData['completed_appointments'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Satisfaction Score -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Client Satisfaction</h3>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-purple-600 mb-2">{{ number_format($performanceData['satisfaction_score'], 1) }}%</div>
                            <div class="text-sm text-gray-600">Overall Satisfaction Score</div>
                            <div class="mt-4">
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-purple-500 h-3 rounded-full" style="width: {{ $performanceData['satisfaction_score'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

