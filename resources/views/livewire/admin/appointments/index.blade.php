<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Appointment Management</h1>
                <p class="text-gray-600">Manage appointments and bookings</p>
            </div>
                <div class="flex space-x-4">
                    <a href="{{ route('admin.appointments.calendar') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                        Calendar View
                    </a>
                    <a href="{{ route('admin.appointments.reminders') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                        Send Reminders
                    </a>
                    <a href="{{ route('admin.waitlist.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center">
                        Waitlist
                    </a>
                    <a href="{{ route('admin.appointments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                        Book Appointment
                    </a>
                </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by client name, email, or phone..." 
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <select wire:model.live="statusFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="no_show">No Show</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <select wire:model.live="dateFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Dates</option>
                        <option value="today">Today</option>
                        <option value="tomorrow">Tomorrow</option>
                        <option value="this_week">This Week</option>
                        <option value="next_week">Next Week</option>
                    </select>
                </div>

                <!-- Staff Filter -->
                <div>
                    <select wire:model.live="staffFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Staff</option>
                        @foreach($staff as $staffMember)
                            <option value="{{ $staffMember->id }}">{{ $staffMember->user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('appointment_date')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Date & Time
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                        <th wire:click="sortBy('status')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ Carbon\Carbon::parse($appointment->appointment_date)->format('M j, Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ Carbon\Carbon::parse($appointment->appointment_date)->format('g:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $appointment->client->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $appointment->client->user->phone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $appointment->service->name }}</div>
                                <div class="text-sm text-gray-500">${{ number_format($appointment->service->price, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $appointment->staff->user->name ?? 'Unassigned' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <select wire:change="updateAppointmentStatus({{ $appointment->id }}, $event.target.value)" 
                                        class="text-xs font-medium rounded-full px-2.5 py-0.5 border-0
                                        @if($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($appointment->status === 'confirmed') bg-green-100 text-green-800
                                        @elseif($appointment->status === 'completed') bg-blue-100 text-blue-800
                                        @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                        @elseif($appointment->status === 'no_show') bg-gray-100 text-gray-800
                                        @endif">
                                    <option value="pending" @if($appointment->status === 'pending') selected @endif>Pending</option>
                                    <option value="confirmed" @if($appointment->status === 'confirmed') selected @endif>Confirmed</option>
                                    <option value="completed" @if($appointment->status === 'completed') selected @endif>Completed</option>
                                    <option value="cancelled" @if($appointment->status === 'cancelled') selected @endif>Cancelled</option>
                                    <option value="no_show" @if($appointment->status === 'no_show') selected @endif>No Show</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.appointments.edit', $appointment->id) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                    <button wire:click="deleteAppointment({{ $appointment->id }})" 
                                            wire:confirm="Are you sure you want to delete this appointment?"
                                            class="text-red-600 hover:text-red-900">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No appointments found</h3>
                                    <p class="mt-1 text-sm text-gray-500">Get started by booking a new appointment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $appointments->links() }}
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
