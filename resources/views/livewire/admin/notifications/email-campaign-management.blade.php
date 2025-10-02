<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Email Marketing Campaigns</h1>
                <p class="text-gray-600">Create and manage email marketing campaigns for your clients</p>
            </div>
            <div class="flex space-x-4">
                <button 
                    wire:click="createCampaign" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    New Campaign
                </button>
                <a href="{{ route('notifications.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    All Notifications
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Campaigns</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_campaigns'] }}</p>
                    <p class="text-xs text-gray-500">{{ $stats['active_campaigns'] }} active</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Emails Delivered</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['delivered_emails'] }}</p>
                    <p class="text-xs text-gray-500">{{ $stats['avg_open_rate'] }}% open rate</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Emails Opened</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['opened_emails'] }}</p>
                    <p class="text-xs text-gray-500">{{ $stats['avg_click_rate'] }}% click rate</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Recipients</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_recipients'] }}</p>
                    <p class="text-xs text-gray-500">{{ $stats['clicked_emails'] }} clicked</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Campaigns List -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Email Campaigns</h3>
                    <div class="flex space-x-4">
                        <select wire:model.live="statusFilter" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="sending">Sending</option>
                            <option value="sent">Sent</option>
                            <option value="paused">Paused</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <input 
                            type="text" 
                            wire:model.live="search" 
                            placeholder="Search campaigns..."
                            class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($campaigns as $campaign)
                        <div class="border rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <h4 class="font-medium text-gray-900">{{ $campaign->name }}</h4>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $campaign->getStatusBadgeClass() }}">
                                            {{ $campaign->getStatusIcon() }} {{ ucfirst($campaign->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">{{ $campaign->subject }}</p>
                                    <div class="text-xs text-gray-500 space-y-1">
                                        <p><strong>Recipients:</strong> {{ $campaign->total_recipients }}</p>
                                        @if($campaign->status === 'sent')
                                            <p><strong>Open Rate:</strong> {{ $campaign->open_rate }}% | <strong>Click Rate:</strong> {{ $campaign->click_rate }}%</p>
                                        @endif
                                        <p><strong>Created:</strong> {{ $campaign->created_at->format('M d, Y') }} by {{ $campaign->creator->name }}</p>
                                        @if($campaign->scheduled_at)
                                            <p><strong>Scheduled:</strong> {{ $campaign->scheduled_at->format('M d, Y g:i A') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex space-x-2 ml-4">
                                    @if($campaign->status === 'draft')
                                        <button 
                                            wire:click="sendCampaign({{ $campaign->id }})" 
                                            class="text-green-600 hover:text-green-900 text-sm"
                                        >
                                            Send
                                        </button>
                                        @if($campaign->scheduled_at)
                                            <button 
                                                wire:click="scheduleCampaign({{ $campaign->id }})" 
                                                class="text-blue-600 hover:text-blue-900 text-sm"
                                            >
                                                Schedule
                                            </button>
                                        @endif
                                    @endif
                                    @if($campaign->status === 'sending')
                                        <button 
                                            wire:click="pauseCampaign({{ $campaign->id }})" 
                                            class="text-yellow-600 hover:text-yellow-900 text-sm"
                                        >
                                            Pause
                                        </button>
                                    @endif
                                    @if(in_array($campaign->status, ['draft', 'scheduled', 'paused']))
                                        <button 
                                            wire:click="cancelCampaign({{ $campaign->id }})" 
                                            class="text-red-600 hover:text-red-900 text-sm"
                                        >
                                            Cancel
                                        </button>
                                    @endif
                                    <button 
                                        wire:click="previewCampaign({{ $campaign->id }})" 
                                        class="text-purple-600 hover:text-purple-900 text-sm"
                                    >
                                        Preview
                                    </button>
                                    <button 
                                        wire:click="viewStats({{ $campaign->id }})" 
                                        class="text-indigo-600 hover:text-indigo-900 text-sm"
                                    >
                                        Stats
                                    </button>
                                    <button 
                                        wire:click="viewRecipients({{ $campaign->id }})" 
                                        class="text-gray-600 hover:text-gray-900 text-sm"
                                    >
                                        Recipients
                                    </button>
                                    <button 
                                        wire:click="editCampaign({{ $campaign->id }})" 
                                        class="text-blue-600 hover:text-blue-900 text-sm"
                                    >
                                        Edit
                                    </button>
                                    <button 
                                        wire:click="duplicateCampaign({{ $campaign->id }})" 
                                        class="text-green-600 hover:text-green-900 text-sm"
                                    >
                                        Duplicate
                                    </button>
                                    @if($campaign->status !== 'sending')
                                        <button 
                                            wire:click="deleteCampaign({{ $campaign->id }})" 
                                            class="text-red-600 hover:text-red-900 text-sm"
                                            onclick="return confirm('Are you sure?')"
                                        >
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No email campaigns</h3>
                            <p class="mt-1 text-sm text-gray-500">Create your first email campaign to get started.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $campaigns->links() }}
                </div>
            </div>
        </div>

        <!-- Recent Campaigns -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Recent Campaigns</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($recentCampaigns as $campaign)
                        <div class="border rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900 text-sm">{{ $campaign->name }}</h4>
                                    <p class="text-xs text-gray-500">{{ $campaign->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $campaign->getStatusBadgeClass() }}">
                                    {{ $campaign->getStatusIcon() }}
                                </span>
                            </div>
                            @if($campaign->status === 'sent')
                                <div class="mt-2 text-xs text-gray-500">
                                    <p>Open Rate: {{ $campaign->open_rate }}%</p>
                                    <p>Click Rate: {{ $campaign->click_rate }}%</p>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500">
                            <p class="text-sm">No recent campaigns</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Modal -->
    @if($showCampaignModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $selectedCampaign ? 'Edit Campaign' : 'Create Campaign' }}
                        </h3>
                        <button wire:click="closeCampaignModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="storeCampaign">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Campaign Name *</label>
                                <input 
                                    type="text" 
                                    wire:model="name" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    required
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Template Type</label>
                                <select 
                                    wire:model="template_type" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="html">HTML</option>
                                    <option value="text">Plain Text</option>
                                    <option value="markdown">Markdown</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea 
                                wire:model="description" 
                                rows="2"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            ></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject Line *</label>
                            <input 
                                type="text" 
                                wire:model="subject" 
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Content *</label>
                            <textarea 
                                wire:model="content" 
                                rows="10"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter your email content here. Use placeholders like {{client_name}}, {{salon_name}}, etc."
                                required
                            ></textarea>
                            <p class="text-xs text-gray-500 mt-1">
                                Available placeholders: {{client_name}}, {{salon_name}}, {{salon_phone}}, {{salon_email}}, {{salon_address}}, {{current_date}}, {{current_year}}
                            </p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Target Criteria</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Client Types</label>
                                    <div class="space-y-1">
                                        <label class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                wire:model="target_criteria.client_types" 
                                                value="regular"
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">Regular</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                wire:model="target_criteria.client_types" 
                                                value="vip"
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">VIP</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                wire:model="target_criteria.client_types" 
                                                value="new"
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">New</span>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Services Used</label>
                                    <div class="max-h-32 overflow-y-auto space-y-1">
                                        @foreach($services as $service)
                                            <label class="flex items-center">
                                                <input 
                                                    type="checkbox" 
                                                    wire:model="target_criteria.services_used" 
                                                    value="{{ $service->id }}"
                                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                >
                                                <span class="ml-2 text-sm text-gray-700">{{ $service->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Schedule (Optional)</label>
                            <input 
                                type="datetime-local" 
                                wire:model="scheduled_at" 
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>

                        <div class="flex space-x-4">
                            <button 
                                type="button" 
                                wire:click="closeCampaignModal" 
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit" 
                                class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium"
                            >
                                {{ $selectedCampaign ? 'Update' : 'Create' }} Campaign
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Preview Modal -->
    @if($showPreviewModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Email Preview</h3>
                        <button wire:click="closePreviewModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <div class="bg-white rounded-lg p-6">
                            <h4 class="font-semibold text-lg mb-4">{{ $selectedCampaign->subject }}</h4>
                            <div class="prose max-w-none">
                                {!! nl2br(e($previewContent)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Stats Modal -->
    @if($showStatsModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Campaign Statistics</h3>
                        <button wire:click="closeStatsModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <p class="text-2xl font-bold text-blue-600">{{ $campaignStats['total_recipients'] }}</p>
                            <p class="text-sm text-gray-600">Total Recipients</p>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <p class="text-2xl font-bold text-green-600">{{ $campaignStats['delivered_count'] }}</p>
                            <p class="text-sm text-gray-600">Delivered</p>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <p class="text-2xl font-bold text-purple-600">{{ $campaignStats['open_rate'] }}%</p>
                            <p class="text-sm text-gray-600">Open Rate</p>
                        </div>
                        <div class="text-center p-4 bg-indigo-50 rounded-lg">
                            <p class="text-2xl font-bold text-indigo-600">{{ $campaignStats['click_rate'] }}%</p>
                            <p class="text-sm text-gray-600">Click Rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
