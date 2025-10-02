<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Content Manager</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <button wire:click="openModal('settings')" 
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg text-sm font-medium hover:bg-gray-700">
                        Salon Settings
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6">
                    <button wire:click="setActiveTab('pages')" 
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'pages' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Pages
                    </button>
                    <button wire:click="setActiveTab('posts')" 
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'posts' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Blog Posts
                    </button>
                    <button wire:click="setActiveTab('galleries')" 
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'galleries' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Galleries
                    </button>
                    <button wire:click="setActiveTab('testimonials')" 
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'testimonials' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Testimonials
                    </button>
                </nav>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input wire:model="search" type="text" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                           placeholder="Search {{ $activeTab }}...">
                </div>
                <div class="sm:w-48">
                    <select wire:model="filter" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                        <option value="all">All {{ $activeTab }}</option>
                        @if($activeTab === 'pages' || $activeTab === 'posts')
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                        @else
                            <option value="featured">Featured</option>
                            <option value="active">Active</option>
                        @endif
                    </select>
                </div>
                <button wire:click="openModal('{{ $activeTab }}')" 
                        class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700">
                    Add {{ ucfirst(rtrim($activeTab, 's')) }}
                </button>
            </div>
        </div>

        <!-- Content Lists -->
        @if($activeTab === 'pages')
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Pages</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($pages as $page)
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $page->title }}</h4>
                                    @if($page->is_homepage)
                                        <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Homepage</span>
                                    @endif
                                    @if($page->is_published)
                                        <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Published</span>
                                    @else
                                        <span class="ml-2 px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">Draft</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 mt-1">{{ $page->excerpt ?: 'No excerpt' }}</p>
                                <p class="text-xs text-gray-400 mt-1">Created {{ $page->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button wire:click="toggleStatus('page', {{ $page->id }}, 'is_published')" 
                                        class="p-1 text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                <button wire:click="openModal('page', {{ $page->id }})" 
                                        class="p-1 text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button wire:click="deleteItem('page', {{ $page->id }})" 
                                        class="p-1 text-red-400 hover:text-red-600"
                                        onclick="return confirm('Are you sure?')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <p class="text-gray-500">No pages found.</p>
                        </div>
                    @endforelse
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $pages->links() }}
                </div>
            </div>
        @endif

        <!-- Similar structure for other tabs... -->
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $editingItem ? 'Edit' : 'Add' }} {{ ucfirst($modalType) }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveItem" class="space-y-4">
                        @if($modalType === 'page')
                            <!-- Page form fields -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                                <input wire:model="page_title" type="text" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Slug *</label>
                                <input wire:model="page_slug" type="text" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                                <textarea wire:model="page_content" rows="6" 
                                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"></textarea>
                            </div>
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input wire:model="page_is_published" type="checkbox" 
                                           class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Published</span>
                                </label>
                                <label class="flex items-center">
                                    <input wire:model="page_is_homepage" type="checkbox" 
                                           class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Homepage</span>
                                </label>
                            </div>
                        @endif

                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" wire:click="closeModal" 
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700">
                                {{ $editingItem ? 'Update' : 'Create' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="fixed top-4 right-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif
</div>
