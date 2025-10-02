<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Client Communication History</h1>
                <p class="text-gray-600">Track and manage all client communications</p>
            </div>
            <div class="flex space-x-4">
                <button wire:click="showCreateCommunicationModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Log Communication
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Communications</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalCommunications }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Emails</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $emailCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">SMS</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $smsCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Phone Calls</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $phoneCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Important</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $importantCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Follow-up Required</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $followUpCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-indigo-100 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-6H4v6zM4 13h6V7H4v6zM4 5h6V1H4v4z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">In Person</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $inPersonCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Client</label>
                    <select wire:model.live="selectedClient" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Staff</label>
                    <select wire:model.live="selectedStaff" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Staff</option>
                        @foreach($staff as $staffMember)
                            <option value="{{ $staffMember->id }}">{{ $staffMember->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Type</label>
                    <select wire:model.live="selectedType" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        <option value="email">Email</option>
                        <option value="sms">SMS</option>
                        <option value="phone">Phone</option>
                        <option value="in_person">In Person</option>
                        <option value="push_notification">Push Notification</option>
                        <option value="system_generated">System Generated</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
                    <select wire:model.live="selectedStatus" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="sent">Sent</option>
                        <option value="delivered">Delivered</option>
                        <option value="read">Read</option>
                        <option value="failed">Failed</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input wire:model.live="startDate" type="date" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input wire:model.live="endDate" type="date" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>
    </div>

    <!-- Communications Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Communication History</h3>
            
            @if($communications->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject/Message</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($communications as $communication)
                                <tr class="@if($communication->is_important) bg-yellow-50 @endif">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $communication->client->user->name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($communication->communication_type === 'email') bg-blue-100 text-blue-800
                                                @elseif($communication->communication_type === 'sms') bg-yellow-100 text-yellow-800
                                                @elseif($communication->communication_type === 'phone') bg-purple-100 text-purple-800
                                                @elseif($communication->communication_type === 'in_person') bg-green-100 text-green-800
                                                @elseif($communication->communication_type === 'push_notification') bg-indigo-100 text-indigo-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $communication->communication_type_display }}
                                            </span>
                                            @if($communication->direction === 'inbound')
                                                <span class="ml-2 text-xs text-gray-500">(Inbound)</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs">
                                            @if($communication->subject)
                                                <div class="font-medium truncate" title="{{ $communication->subject }}">
                                                    {{ $communication->subject }}
                                                </div>
                                            @endif
                                            <div class="text-gray-600 truncate" title="{{ $communication->message }}">
                                                {{ $communication->message }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $communication->staff->user->name ?? 'System' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($communication->status === 'sent') bg-blue-100 text-blue-800
                                            @elseif($communication->status === 'delivered') bg-green-100 text-green-800
                                            @elseif($communication->status === 'read') bg-gray-100 text-gray-800
                                            @elseif($communication->status === 'failed') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ $communication->status_display }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>
                                            {{ $communication->created_at->format('M j, Y') }}
                                            <div class="text-xs text-gray-500">{{ $communication->created_at->format('g:i A') }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button wire:click="viewCommunication({{ $communication->id }})" class="text-blue-600 hover:text-blue-900">
                                                View
                                            </button>
                                            @if($communication->is_important)
                                                <button wire:click="markAsUnimportant({{ $communication->id }})" class="text-yellow-600 hover:text-yellow-900">
                                                    Unmark
                                                </button>
                                            @else
                                                <button wire:click="markAsImportant({{ $communication->id }})" class="text-yellow-600 hover:text-yellow-900">
                                                    Mark Important
                                                </button>
                                            @endif
                                            @if($communication->requires_follow_up)
                                                <button wire:click="clearFollowUp({{ $communication->id }})" class="text-orange-600 hover:text-orange-900">
                                                    Clear Follow-up
                                                </button>
                                            @else
                                                <button wire:click="setFollowUp({{ $communication->id }})" class="text-orange-600 hover:text-orange-900">
                                                    Set Follow-up
                                                </button>
                                            @endif
                                            <button wire:click="deleteCommunication({{ $communication->id }})" 
                                                    wire:confirm="Are you sure you want to delete this communication?"
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No communications found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by logging a new communication.</p>
                    <div class="mt-6">
                        <button wire:click="showCreateCommunicationModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            Log Communication
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Create Communication Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Log Communication</h3>
                        <button wire:click="closeCreateModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="createCommunication" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Client *</label>
                                <select wire:model="formClientId" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Client</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->user->name }}</option>
                                    @endforeach
                                </select>
                                @error('formClientId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Staff</label>
                                <select wire:model="formStaffId" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Staff</option>
                                    @foreach($staff as $staffMember)
                                        <option value="{{ $staffMember->id }}">{{ $staffMember->user->name }}</option>
                                    @endforeach
                                </select>
                                @error('formStaffId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Communication Type *</label>
                                <select wire:model="formCommunicationType" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="email">Email</option>
                                    <option value="sms">SMS</option>
                                    <option value="phone">Phone Call</option>
                                    <option value="in_person">In Person</option>
                                    <option value="push_notification">Push Notification</option>
                                    <option value="system_generated">System Generated</option>
                                </select>
                                @error('formCommunicationType') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Direction *</label>
                                <select wire:model="formDirection" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="outbound">Outbound</option>
                                    <option value="inbound">Inbound</option>
                                </select>
                                @error('formDirection') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Channel</label>
                                <input wire:model="formChannel" type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., email, phone, etc.">
                                @error('formChannel') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Recipient</label>
                                <input wire:model="formRecipient" type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Email or phone number">
                                @error('formRecipient') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                            <input wire:model="formSubject" type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Communication subject">
                            @error('formSubject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                            <textarea wire:model="formMessage" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Communication message"></textarea>
                            @error('formMessage') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sender</label>
                            <input wire:model="formSender" type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Staff name or system">
                            @error('formSender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea wire:model="formNotes" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Additional notes"></textarea>
                            @error('formNotes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center">
                                <input wire:model="formIsImportant" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <label class="ml-2 text-sm text-gray-700">Mark as Important</label>
                            </div>

                            <div class="flex items-center">
                                <input wire:model="formRequiresFollowUp" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <label class="ml-2 text-sm text-gray-700">Requires Follow-up</label>
                            </div>
                        </div>

                        @if($formRequiresFollowUp)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Follow-up Date</label>
                                    <input wire:model="formFollowUpDate" type="date" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('formFollowUpDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Follow-up Notes</label>
                                    <input wire:model="formFollowUpNotes" type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Follow-up details">
                                    @error('formFollowUpNotes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" wire:click="closeCreateModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">
                                Cancel
                            </button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                                Log Communication
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Communication Detail Modal -->
    @if($showDetailModal && $selectedCommunication)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Communication Details</h3>
                        <button wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <!-- Communication Info -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-md font-medium text-gray-900 mb-2">Communication Information</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium">Client:</span> {{ $selectedCommunication->client->user->name ?? 'Unknown' }}
                                </div>
                                <div>
                                    <span class="font-medium">Staff:</span> {{ $selectedCommunication->staff->user->name ?? 'System' }}
                                </div>
                                <div>
                                    <span class="font-medium">Type:</span> {{ $selectedCommunication->communication_type_display }}
                                </div>
                                <div>
                                    <span class="font-medium">Direction:</span> {{ $selectedCommunication->direction_display }}
                                </div>
                                <div>
                                    <span class="font-medium">Status:</span> 
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($selectedCommunication->status === 'sent') bg-blue-100 text-blue-800
                                        @elseif($selectedCommunication->status === 'delivered') bg-green-100 text-green-800
                                        @elseif($selectedCommunication->status === 'read') bg-gray-100 text-gray-800
                                        @elseif($selectedCommunication->status === 'failed') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ $selectedCommunication->status_display }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium">Date:</span> {{ $selectedCommunication->created_at->format('M j, Y g:i A') }}
                                </div>
                            </div>
                        </div>

                        <!-- Message Content -->
                        <div class="bg-white border rounded-lg p-4">
                            <h4 class="text-md font-medium text-gray-900 mb-3">Message Content</h4>
                            @if($selectedCommunication->subject)
                                <div class="mb-3">
                                    <span class="font-medium">Subject:</span>
                                    <p class="mt-1">{{ $selectedCommunication->subject }}</p>
                                </div>
                            @endif
                            <div>
                                <span class="font-medium">Message:</span>
                                <p class="mt-1 whitespace-pre-wrap">{{ $selectedCommunication->message }}</p>
                            </div>
                        </div>

                        <!-- Additional Details -->
                        @if($selectedCommunication->channel || $selectedCommunication->recipient || $selectedCommunication->sender)
                            <div class="bg-white border rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 mb-3">Additional Details</h4>
                                <div class="space-y-2 text-sm">
                                    @if($selectedCommunication->channel)
                                        <div><span class="font-medium">Channel:</span> {{ $selectedCommunication->channel }}</div>
                                    @endif
                                    @if($selectedCommunication->recipient)
                                        <div><span class="font-medium">Recipient:</span> {{ $selectedCommunication->recipient }}</div>
                                    @endif
                                    @if($selectedCommunication->sender)
                                        <div><span class="font-medium">Sender:</span> {{ $selectedCommunication->sender }}</div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Notes and Follow-up -->
                        @if($selectedCommunication->notes || $selectedCommunication->requires_follow_up)
                            <div class="bg-white border rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 mb-3">Notes & Follow-up</h4>
                                @if($selectedCommunication->notes)
                                    <div class="mb-3">
                                        <span class="font-medium">Notes:</span>
                                        <p class="mt-1">{{ $selectedCommunication->notes }}</p>
                                    </div>
                                @endif
                                @if($selectedCommunication->requires_follow_up)
                                    <div>
                                        <span class="font-medium">Follow-up Required:</span>
                                        <p class="mt-1">
                                            @if($selectedCommunication->follow_up_date)
                                                Due: {{ $selectedCommunication->follow_up_date->format('M j, Y') }}
                                            @endif
                                            @if($selectedCommunication->follow_up_notes)
                                                <br>Notes: {{ $selectedCommunication->follow_up_notes }}
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3 pt-4">
                            <button wire:click="closeDetailModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">
                                Close
                            </button>
                            @if($selectedCommunication->is_important)
                                <button wire:click="markAsUnimportant({{ $selectedCommunication->id }})" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md">
                                    Unmark Important
                                </button>
                            @else
                                <button wire:click="markAsImportant({{ $selectedCommunication->id }})" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md">
                                    Mark Important
                                </button>
                            @endif
                            @if($selectedCommunication->requires_follow_up)
                                <button wire:click="clearFollowUp({{ $selectedCommunication->id }})" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md">
                                    Clear Follow-up
                                </button>
                            @else
                                <button wire:click="setFollowUp({{ $selectedCommunication->id }})" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md">
                                    Set Follow-up
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
</div>