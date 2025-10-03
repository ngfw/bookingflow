<div class="p-6">
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Contact Submissions</h2>

        <div class="flex gap-4">
            <select wire:model.live="filterStatus" class="rounded-lg border-gray-300 text-sm">
                <option value="all">All Status</option>
                <option value="new">New</option>
                <option value="read">Read</option>
                <option value="replied">Replied</option>
                <option value="archived">Archived</option>
            </select>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($submissions as $submission)
                    <tr class="hover:bg-gray-50 {{ $submission->status === 'new' ? 'bg-blue-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $submission->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $submission->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ Str::limit($submission->subject, 40) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($submission->status === 'new') bg-blue-100 text-blue-800
                                @elseif($submission->status === 'read') bg-yellow-100 text-yellow-800
                                @elseif($submission->status === 'replied') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($submission->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $submission->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button wire:click="viewSubmission({{ $submission->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">View</button>
                            <button wire:click="deleteSubmission({{ $submission->id }})" wire:confirm="Are you sure you want to delete this submission?" class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No submissions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $submissions->links() }}
    </div>

    <!-- View Submission Modal -->
    @if($selectedSubmission)
        <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                    Contact Submission Details
                                </h3>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Name</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $selectedSubmission->name }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Email</label>
                                        <p class="mt-1 text-sm text-gray-900">
                                            <a href="mailto:{{ $selectedSubmission->email }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $selectedSubmission->email }}
                                            </a>
                                        </p>
                                    </div>

                                    @if($selectedSubmission->phone)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                                            <p class="mt-1 text-sm text-gray-900">
                                                <a href="tel:{{ $selectedSubmission->phone }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $selectedSubmission->phone }}
                                                </a>
                                            </p>
                                        </div>
                                    @endif

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Subject</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $selectedSubmission->subject }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Message</label>
                                        <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $selectedSubmission->message }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status</label>
                                        <p class="mt-1">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($selectedSubmission->status === 'new') bg-blue-100 text-blue-800
                                                @elseif($selectedSubmission->status === 'read') bg-yellow-100 text-yellow-800
                                                @elseif($selectedSubmission->status === 'replied') bg-green-100 text-green-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($selectedSubmission->status) }}
                                            </span>
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Submitted</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $selectedSubmission->created_at->format('F d, Y \a\t h:i A') }}</p>
                                    </div>

                                    @if($selectedSubmission->read_at)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Read At</label>
                                            <p class="mt-1 text-sm text-gray-900">{{ $selectedSubmission->read_at->format('F d, Y \a\t h:i A') }}</p>
                                        </div>
                                    @endif

                                    @if($selectedSubmission->replied_at)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Replied At</label>
                                            <p class="mt-1 text-sm text-gray-900">{{ $selectedSubmission->replied_at->format('F d, Y \a\t h:i A') }}</p>
                                        </div>
                                    @endif

                                    <div>
                                        <label for="admin-notes" class="block text-sm font-medium text-gray-700">Admin Notes</label>
                                        <textarea
                                            id="admin-notes"
                                            wire:model="adminNotes"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="Add internal notes about this submission..."
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        @if($selectedSubmission->status !== 'replied')
                            <button
                                type="button"
                                wire:click="markAsReplied"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Mark as Replied
                            </button>
                        @endif
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
