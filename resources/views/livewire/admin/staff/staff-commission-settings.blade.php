<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Staff Commission Settings</h1>
                <p class="text-gray-600">Configure commission structures for staff members</p>
            </div>
            <div class="flex space-x-4">
                <button wire:click="addCommissionSetting" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Commission Setting
                </button>
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
            </div>
        </div>
    </div>

    <!-- Commission Settings Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Commission Settings</h3>
            
            @if($commissionSettings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate/Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frequency</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($commissionSettings as $setting)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $setting->staff->user->name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $setting->service->name ?? 'All Services' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($setting->commission_type === 'percentage') bg-blue-100 text-blue-800
                                            @elseif($setting->commission_type === 'fixed') bg-green-100 text-green-800
                                            @else bg-purple-100 text-purple-800 @endif">
                                            {{ $setting->commission_type_display }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($setting->commission_type === 'percentage')
                                            {{ $setting->commission_rate }}%
                                        @elseif($setting->commission_type === 'fixed')
                                            ${{ number_format($setting->fixed_amount, 2) }}
                                        @else
                                            <span class="text-xs text-gray-500">Tiered Structure</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $setting->payment_frequency_display }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($setting->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                            {{ $setting->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button wire:click="editCommissionSetting({{ $setting->id }})" class="text-blue-600 hover:text-blue-900">
                                                Edit
                                            </button>
                                            <button wire:click="deleteCommissionSetting({{ $setting->id }})" 
                                                    wire:confirm="Are you sure you want to delete this commission setting?"
                                                    class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
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
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No commission settings</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new commission setting.</p>
                    <div class="mt-6">
                        <button wire:click="addCommissionSetting" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            Add Commission Setting
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Commission Setting Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $editingSetting ? 'Edit Commission Setting' : 'Add New Commission Setting' }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveCommissionSetting" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Staff Member *</label>
                                <select wire:model="formStaffId" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Staff</option>
                                    @foreach($staff as $staffMember)
                                        <option value="{{ $staffMember->id }}">{{ $staffMember->user->name }}</option>
                                    @endforeach
                                </select>
                                @error('formStaffId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Service (Optional)</label>
                                <select wire:model="formServiceId" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Services</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                                @error('formServiceId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Commission Type *</label>
                                <select wire:model.live="formCommissionType" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="percentage">Percentage</option>
                                    <option value="fixed">Fixed Amount</option>
                                    <option value="tiered">Tiered Structure</option>
                                </select>
                                @error('formCommissionType') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Calculation Basis *</label>
                                <select wire:model="formCalculationBasis" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="revenue">Revenue</option>
                                    <option value="profit">Profit</option>
                                    <option value="appointments">Appointments</option>
                                </select>
                                @error('formCalculationBasis') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            @if($formCommissionType === 'percentage')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Commission Rate (%) *</label>
                                    <input wire:model="formCommissionRate" type="number" step="0.01" min="0" max="100" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('formCommissionRate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            @endif

                            @if($formCommissionType === 'fixed')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fixed Amount ($) *</label>
                                    <input wire:model="formFixedAmount" type="number" step="0.01" min="0" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('formFixedAmount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Frequency *</label>
                                <select wire:model="formPaymentFrequency" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="bi_weekly">Bi-Weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                                @error('formPaymentFrequency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Threshold ($)</label>
                                <input wire:model="formMinimumThreshold" type="number" step="0.01" min="0" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('formMinimumThreshold') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Maximum Cap ($)</label>
                                <input wire:model="formMaximumCap" type="number" step="0.01" min="0" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('formMaximumCap') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Effective Date *</label>
                                <input wire:model="formEffectiveDate" type="date" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('formEffectiveDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                <input wire:model="formExpiryDate" type="date" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('formExpiryDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Tiered Rates Section -->
                        @if($showTieredRates)
                            <div class="border-t pt-4">
                                <h4 class="text-md font-medium text-gray-900 mb-3">Tiered Commission Structure</h4>
                                
                                @if(count($formTieredRates) > 0)
                                    <div class="mb-4">
                                        <div class="space-y-2">
                                            @foreach($formTieredRates as $index => $tier)
                                                <div class="flex items-center space-x-2 p-2 bg-gray-50 rounded">
                                                    <span class="text-sm text-gray-600">${{ number_format($tier['min'], 2) }} - ${{ number_format($tier['max'], 2) }}: {{ $tier['rate'] }}%</span>
                                                    <button type="button" wire:click="removeTieredRate({{ $index }})" class="text-red-600 hover:text-red-800 text-sm">
                                                        Remove
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Min Amount ($)</label>
                                        <input wire:model="newTierMin" type="number" step="0.01" min="0" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Max Amount ($)</label>
                                        <input wire:model="newTierMax" type="number" step="0.01" min="0" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Rate (%)</label>
                                        <input wire:model="newTierRate" type="number" step="0.01" min="0" max="100" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </div>
                                    <div class="flex items-end">
                                        <button type="button" wire:click="addTieredRate" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-md text-sm">
                                            Add Tier
                                        </button>
                                    </div>
                                </div>
                                @error('formTieredRates') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea wire:model="formNotes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Optional notes about this commission setting..."></textarea>
                            @error('formNotes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center">
                            <input wire:model="formIsActive" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <label class="ml-2 text-sm text-gray-700">Active</label>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" wire:click="closeModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">
                                Cancel
                            </button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                                {{ $editingSetting ? 'Update Setting' : 'Create Setting' }}
                            </button>
                        </div>
                    </form>
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
</div>