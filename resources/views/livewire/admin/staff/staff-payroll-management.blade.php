<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Staff Payroll Management</h1>
                <p class="text-gray-600">Manage staff payroll calculations and payments</p>
            </div>
            <div class="flex space-x-4">
                <button wire:click="showGeneratePayrollModal" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Generate Payroll
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Payrolls</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $payrollCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Gross Pay</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($totalGrossPay, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m16 0l-4-4m4 4l-4 4M4 12l4-4m-4 4l4 4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Deductions</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($totalDeductions, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Net Pay</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($totalNetPay, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="w-full md:w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Staff</label>
                    <select wire:model.live="selectedStaff" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Staff</option>
                        @foreach($staff as $staffMember)
                            <option value="{{ $staffMember->id }}">{{ $staffMember->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="w-full md:w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Period</label>
                    <select wire:model.live="selectedPeriod" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Periods</option>
                        <option value="current_month">Current Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="current_quarter">Current Quarter</option>
                        <option value="current_year">Current Year</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payroll Records</h3>
            
            @if($payrolls->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pay Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Pay</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($payrolls as $payroll)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $payroll->staff->user->name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $payroll->pay_period_display }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($payroll->total_hours, 1) }}h
                                        @if($payroll->overtime_hours > 0)
                                            <span class="text-orange-600">(+{{ number_format($payroll->overtime_hours, 1) }}h OT)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($payroll->gross_pay, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($payroll->total_deductions, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        ${{ number_format($payroll->net_pay, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($payroll->status === 'draft') bg-gray-100 text-gray-800
                                            @elseif($payroll->status === 'calculated') bg-blue-100 text-blue-800
                                            @elseif($payroll->status === 'approved') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ $payroll->status_display }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button wire:click="viewPayroll({{ $payroll->id }})" class="text-blue-600 hover:text-blue-900">
                                                View
                                            </button>
                                            @if($payroll->status === 'calculated')
                                                <button wire:click="approvePayroll({{ $payroll->id }})" class="text-yellow-600 hover:text-yellow-900">
                                                    Approve
                                                </button>
                                            @endif
                                            @if($payroll->status === 'approved')
                                                <button wire:click="markAsPaid({{ $payroll->id }})" class="text-green-600 hover:text-green-900">
                                                    Mark Paid
                                                </button>
                                            @endif
                                            @if($payroll->status !== 'paid')
                                                <button wire:click="deletePayroll({{ $payroll->id }})" 
                                                        wire:confirm="Are you sure you want to delete this payroll record?"
                                                        class="text-red-600 hover:text-red-900">
                                                    Delete
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No payroll records</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by generating payroll for a period.</p>
                    <div class="mt-6">
                        <button wire:click="showGeneratePayrollModal" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                            Generate Payroll
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Generate Payroll Modal -->
    @if($showGenerateModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Generate Payroll</h3>
                        <button wire:click="closeGenerateModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="generatePayroll" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pay Period Start *</label>
                                <input wire:model="startDate" type="date" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('startDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pay Period End *</label>
                                <input wire:model="endDate" type="date" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('endDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pay Period Type *</label>
                                <select wire:model="payPeriodType" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="weekly">Weekly</option>
                                    <option value="bi_weekly">Bi-Weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                                @error('payPeriodType') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-blue-900 mb-2">Payroll Generation Info</h4>
                            <ul class="text-sm text-blue-800 space-y-1">
                                <li>• Payroll will be calculated based on staff performance data</li>
                                <li>• Commission rates will be applied from commission settings</li>
                                <li>• Standard tax deductions will be calculated</li>
                                <li>• Existing payroll records for the period will be skipped</li>
                            </ul>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" wire:click="closeGenerateModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">
                                Cancel
                            </button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                                Generate Payroll
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Payroll Detail Modal -->
    @if($showPayrollModal && $selectedPayroll)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Payroll Details</h3>
                        <button wire:click="closePayrollModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <!-- Staff Info -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-md font-medium text-gray-900 mb-2">Staff Information</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium">Name:</span> {{ $selectedPayroll->staff->user->name ?? 'Unknown' }}
                                </div>
                                <div>
                                    <span class="font-medium">Pay Period:</span> {{ $selectedPayroll->pay_period_display }}
                                </div>
                                <div>
                                    <span class="font-medium">Status:</span> 
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($selectedPayroll->status === 'draft') bg-gray-100 text-gray-800
                                        @elseif($selectedPayroll->status === 'calculated') bg-blue-100 text-blue-800
                                        @elseif($selectedPayroll->status === 'approved') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ $selectedPayroll->status_display }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium">Pay Date:</span> {{ $selectedPayroll->pay_date ? $selectedPayroll->pay_date->format('M j, Y') : 'Not paid' }}
                                </div>
                            </div>
                        </div>

                        <!-- Hours and Rates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white border rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 mb-3">Hours Worked</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span>Regular Hours:</span>
                                        <span>{{ number_format($selectedPayroll->regular_hours, 1) }}h</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Overtime Hours:</span>
                                        <span>{{ number_format($selectedPayroll->overtime_hours, 1) }}h</span>
                                    </div>
                                    <div class="flex justify-between font-medium">
                                        <span>Total Hours:</span>
                                        <span>{{ number_format($selectedPayroll->total_hours, 1) }}h</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white border rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 mb-3">Rates</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span>Hourly Rate:</span>
                                        <span>${{ number_format($selectedPayroll->hourly_rate, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Overtime Rate:</span>
                                        <span>${{ number_format($selectedPayroll->overtime_rate, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pay Breakdown -->
                        <div class="bg-white border rounded-lg p-4">
                            <h4 class="text-md font-medium text-gray-900 mb-3">Pay Breakdown</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>Regular Pay:</span>
                                    <span>${{ number_format($selectedPayroll->regular_pay, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Overtime Pay:</span>
                                    <span>${{ number_format($selectedPayroll->overtime_pay, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Commission:</span>
                                    <span>${{ number_format($selectedPayroll->commission_earned, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Bonus:</span>
                                    <span>${{ number_format($selectedPayroll->bonus_amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between font-medium border-t pt-2">
                                    <span>Gross Pay:</span>
                                    <span>${{ number_format($selectedPayroll->gross_pay, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Deductions -->
                        <div class="bg-white border rounded-lg p-4">
                            <h4 class="text-md font-medium text-gray-900 mb-3">Deductions</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>Tax Deduction:</span>
                                    <span>${{ number_format($selectedPayroll->tax_deduction, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Social Security:</span>
                                    <span>${{ number_format($selectedPayroll->social_security, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Medicare:</span>
                                    <span>${{ number_format($selectedPayroll->medicare, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Health Insurance:</span>
                                    <span>${{ number_format($selectedPayroll->health_insurance, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Retirement:</span>
                                    <span>${{ number_format($selectedPayroll->retirement_contribution, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Other Deductions:</span>
                                    <span>${{ number_format($selectedPayroll->other_deductions, 2) }}</span>
                                </div>
                                <div class="flex justify-between font-medium border-t pt-2">
                                    <span>Total Deductions:</span>
                                    <span>${{ number_format($selectedPayroll->total_deductions, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Net Pay -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <h4 class="text-md font-medium text-green-900">Net Pay</h4>
                                <span class="text-2xl font-bold text-green-900">${{ number_format($selectedPayroll->net_pay, 2) }}</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3 pt-4">
                            <button wire:click="closePayrollModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">
                                Close
                            </button>
                            @if($selectedPayroll->status === 'calculated')
                                <button wire:click="approvePayroll({{ $selectedPayroll->id }})" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md">
                                    Approve
                                </button>
                            @endif
                            @if($selectedPayroll->status === 'approved')
                                <button wire:click="markAsPaid({{ $selectedPayroll->id }})" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                                    Mark as Paid
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('info'))
        <div class="fixed bottom-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('info') }}
        </div>
    @endif
</div>