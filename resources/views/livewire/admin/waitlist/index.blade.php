<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Waitlist Management</h1>
                <p class="text-gray-600">Manage client waitlist requests and convert to appointments</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.appointments.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                    Back to Appointments
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by client name, email, or phone..." 
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <select wire:model.live="statusFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="contacted">Contacted</option>
                        <option value="booked">Booked</option>
                        <option value="expired">Expired</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <!-- Service Filter -->
                <div>
                    <select wire:model.live="serviceFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Services</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Waitlist Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('created_at')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Added
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preferred Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                        <th wire:click="sortBy('status')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($waitlist as $entry)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ Carbon\Carbon::parse($entry->created_at)->format('M j, Y g:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $entry->client->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $entry->client->user->email }}</div>
                                        <div class="text-sm text-gray-500">{{ $entry->client->user->phone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $entry->service->name }}</div>
                                <div class="text-sm text-gray-500">${{ number_format($entry->service->price, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ Carbon\Carbon::parse($entry->preferred_date)->format('M j, Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ Carbon\Carbon::parse($entry->preferred_time_start)->format('g:i A') }} - 
                                    {{ Carbon\Carbon::parse($entry->preferred_time_end)->format('g:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $entry->staff->user->name ?? 'Any Staff' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <select wire:change="updateStatus({{ $entry->id }}, $event.target.value)" 
                                        class="text-xs font-medium rounded-full px-2.5 py-0.5 border-0
                                        @if($entry->status === 'active') bg-blue-100 text-blue-800
                                        @elseif($entry->status === 'contacted') bg-yellow-100 text-yellow-800
                                        @elseif($entry->status === 'booked') bg-green-100 text-green-800
                                        @elseif($entry->status === 'expired') bg-gray-100 text-gray-800
                                        @elseif($entry->status === 'cancelled') bg-red-100 text-red-800
                                        @endif">
                                    <option value="active" @if($entry->status === 'active') selected @endif>Active</option>
                                    <option value="contacted" @if($entry->status === 'contacted') selected @endif>Contacted</option>
                                    <option value="booked" @if($entry->status === 'booked') selected @endif>Booked</option>
                                    <option value="expired" @if($entry->status === 'expired') selected @endif>Expired</option>
                                    <option value="cancelled" @if($entry->status === 'cancelled') selected @endif>Cancelled</option>
                                </select>
                                @if($entry->contacted_at)
                                    <div class="text-xs text-gray-500 mt-1">
                                        Contacted: {{ Carbon\Carbon::parse($entry->contacted_at)->format('M j, g:i A') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if($entry->status === 'active' || $entry->status === 'contacted')
                                        <button wire:click="convertToAppointment({{ $entry->id }})" 
                                                class="text-green-600 hover:text-green-900">
                                            Book Appointment
                                        </button>
                                    @endif
                                    <button wire:click="deleteWaitlist({{ $entry->id }})" 
                                            wire:confirm="Are you sure you want to delete this waitlist entry?"
                                            class="text-red-600 hover:text-red-900">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No waitlist entries found</h3>
                                    <p class="mt-1 text-sm text-gray-500">No clients are currently on the waitlist.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $waitlist->links() }}
        </div>
    </div>

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