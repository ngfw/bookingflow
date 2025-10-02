<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Page Manager</h2>
        <a href="{{ route('admin.pages.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            Create New Page
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" wire:model.live="search" placeholder="Search pages..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex gap-2">
                <select wire:model.live="filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Pages</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                    <option value="homepage">Homepage</option>
                </select>
                <select wire:model.live="sortBy" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="created_at">Created Date</option>
                    <option value="title">Title</option>
                    <option value="updated_at">Last Updated</option>
                </select>
                <button wire:click="setSortBy('{{ $sortBy }}')" 
                        class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    @if($sortDirection === 'asc')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                        </svg>
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    @endif
                </button>
            </div>
        </div>
    </div>

    <!-- Pages Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($pages as $page)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                <!-- Page Header -->
                <div class="p-4 border-b border-gray-100">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 truncate">{{ $page->title }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $page->slug }}</p>
                        </div>
                        <div class="flex gap-1 ml-2">
                            @if($page->is_homepage)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Homepage
                                </span>
                            @endif
                            @if($page->is_published)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Published
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Draft
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Page Content Preview -->
                <div class="p-4">
                    @if($page->excerpt)
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $page->excerpt }}</p>
                    @endif
                    
                    <div class="text-xs text-gray-500 space-y-1">
                        <div>Created: {{ $page->created_at->format('M j, Y') }}</div>
                        <div>Updated: {{ $page->updated_at->format('M j, Y') }}</div>
                        @if($page->published_at)
                            <div>Published: {{ $page->published_at->format('M j, Y') }}</div>
                        @endif
                    </div>
                </div>

                <!-- Page Actions -->
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                    <div class="flex justify-between items-center">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.pages.edit', $page->id) }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Edit
                            </a>
                            <a href="{{ route('pages.show', $page->slug) }}" target="_blank"
                               class="text-green-600 hover:text-green-800 text-sm font-medium">
                                View
                            </a>
                        </div>
                        
                        <div class="flex gap-1">
                            <!-- Toggle Publish Status -->
                            <button wire:click="toggleStatus({{ $page->id }})" 
                                    class="p-1 text-gray-400 hover:text-gray-600" 
                                    title="{{ $page->is_published ? 'Unpublish' : 'Publish' }}">
                                @if($page->is_published)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                    </svg>
                                @endif
                            </button>

                            <!-- Set as Homepage -->
                            @if(!$page->is_homepage)
                                <button wire:click="setHomepage({{ $page->id }})" 
                                        class="p-1 text-gray-400 hover:text-yellow-600" 
                                        title="Set as Homepage">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                </button>
                            @endif

                            <!-- Duplicate Page -->
                            <button wire:click="duplicatePage({{ $page->id }})" 
                                    class="p-1 text-gray-400 hover:text-blue-600" 
                                    title="Duplicate Page">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>

                            <!-- Delete Page -->
                            <button wire:click="deletePage({{ $page->id }})" 
                                    wire:confirm="Are you sure you want to delete this page?"
                                    class="p-1 text-gray-400 hover:text-red-600" 
                                    title="Delete Page">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No pages found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new page.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.pages.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Create New Page
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $pages->links() }}
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
</div>
