<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">SEO Manager</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <button wire:click="generateSitemap" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                        Generate Sitemap
                    </button>
                    <button wire:click="generateRobotsTxt" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">
                        Generate Robots.txt
                    </button>
                    <button wire:click="openModal('settings')" 
                            class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700">
                        Global SEO Settings
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
                    <button wire:click="setActiveTab('overview')" 
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'overview' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Overview
                    </button>
                    <button wire:click="setActiveTab('pages')" 
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'pages' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Pages
                    </button>
                    <button wire:click="setActiveTab('posts')" 
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'posts' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Blog Posts
                    </button>
                    <button wire:click="setActiveTab('analysis')" 
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'analysis' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        SEO Analysis
                    </button>
                </nav>
            </div>
        </div>

        <!-- Content -->
        @if($activeTab === 'overview')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- SEO Score Card -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Overall SEO Score</h3>
                            <p class="text-3xl font-bold text-green-600">85</p>
                            <p class="text-sm text-gray-500">Grade: B</p>
                        </div>
                    </div>
                </div>

                <!-- Pages Count -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Total Pages</h3>
                            <p class="text-3xl font-bold text-blue-600">{{ $pages->total() }}</p>
                            <p class="text-sm text-gray-500">Published pages</p>
                        </div>
                    </div>
                </div>

                <!-- Blog Posts Count -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Blog Posts</h3>
                            <p class="text-3xl font-bold text-purple-600">{{ $posts->total() }}</p>
                            <p class="text-sm text-gray-500">Published posts</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO Recommendations -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Recommendations</h3>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-900">Improve Meta Descriptions</h4>
                            <p class="text-sm text-gray-600">Some pages have missing or incomplete meta descriptions. Add compelling descriptions to improve click-through rates.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-900">Add Alt Text to Images</h4>
                            <p class="text-sm text-gray-600">Several images are missing alt text. Add descriptive alt text to improve accessibility and SEO.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-900">Optimize Page Speed</h4>
                            <p class="text-sm text-gray-600">Your site loads quickly. Consider adding more internal links to improve page authority.</p>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'pages')
            <!-- Pages SEO Management -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Pages SEO</h3>
                        <div class="flex items-center space-x-4">
                            <input wire:model="search" type="text" 
                                   class="border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                   placeholder="Search pages...">
                        </div>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($pages as $page)
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $page->title }}</h4>
                                    @if($page->is_published)
                                        <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Published</span>
                                    @else
                                        <span class="ml-2 px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">Draft</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 mt-1">{{ $page->seo_description ?: 'No meta description' }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $page->url }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button wire:click="analyzeSEO('page', {{ $page->id }})" 
                                        class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full hover:bg-blue-200">
                                    Analyze
                                </button>
                                <button wire:click="openModal('page', {{ $page->id }})" 
                                        class="p-1 text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
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

        @elseif($activeTab === 'posts')
            <!-- Blog Posts SEO Management -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Blog Posts SEO</h3>
                        <div class="flex items-center space-x-4">
                            <input wire:model="search" type="text" 
                                   class="border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                   placeholder="Search posts...">
                        </div>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($posts as $post)
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $post->title }}</h4>
                                    @if($post->is_published)
                                        <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Published</span>
                                    @else
                                        <span class="ml-2 px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">Draft</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 mt-1">{{ $post->seo_description ?: 'No meta description' }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $post->url }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button wire:click="analyzeSEO('post', {{ $post->id }})" 
                                        class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full hover:bg-blue-200">
                                    Analyze
                                </button>
                                <button wire:click="openModal('post', {{ $post->id }})" 
                                        class="p-1 text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <p class="text-gray-500">No blog posts found.</p>
                        </div>
                    @endforelse
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $posts->links() }}
                </div>
            </div>

        @elseif($activeTab === 'analysis')
            <!-- SEO Analysis Results -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Analysis</h3>
                
                @if($seo_score > 0)
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">SEO Score</span>
                            <span class="text-sm font-medium text-gray-900">{{ $seo_score }}/100 (Grade: {{ $seo_grade }})</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-pink-600 h-2 rounded-full" style="width: {{ $seo_score }}%"></div>
                        </div>
                    </div>

                    @if(count($seo_issues) > 0)
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Issues Found</h4>
                            <div class="space-y-2">
                                @foreach($seo_issues as $issue)
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div class="w-5 h-5 bg-red-100 rounded-full flex items-center justify-center">
                                                <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-gray-700">{{ $issue }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Great SEO!</h3>
                            <p class="text-gray-600">No issues found. Your content is well optimized for search engines.</p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Analysis Yet</h3>
                        <p class="text-gray-600">Select a page or post to analyze its SEO performance.</p>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- SEO Settings Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $editingItem ? 'Edit SEO Settings' : 'Global SEO Settings' }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="{{ $editingItem ? 'saveItemSEO' : 'saveSEOSettings' }}" class="space-y-4">
                        <!-- Basic SEO -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title *</label>
                            <input wire:model="meta_title" type="text" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                   placeholder="Page title (max 60 characters)">
                            <p class="text-xs text-gray-500 mt-1">{{ strlen($meta_title) }}/60 characters</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description *</label>
                            <textarea wire:model="meta_description" rows="3" 
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                      placeholder="Page description (max 160 characters)"></textarea>
                            <p class="text-xs text-gray-500 mt-1">{{ strlen($meta_description) }}/160 characters</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keywords</label>
                            <input wire:model="meta_keywords" type="text" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                   placeholder="keyword1, keyword2, keyword3">
                        </div>

                        <!-- Open Graph -->
                        <div class="border-t pt-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Open Graph (Social Media)</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">OG Title</label>
                                    <input wire:model="og_title" type="text" 
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">OG Description</label>
                                    <textarea wire:model="og_description" rows="2" 
                                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">OG Image</label>
                                    <input wire:model="og_image" type="text" 
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                           placeholder="Image URL">
                                </div>
                            </div>
                        </div>

                        <!-- Twitter Card -->
                        <div class="border-t pt-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Twitter Card</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Twitter Title</label>
                                    <input wire:model="twitter_title" type="text" 
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Twitter Description</label>
                                    <textarea wire:model="twitter_description" rows="2" 
                                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Twitter Image</label>
                                    <input wire:model="twitter_image" type="text" 
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                           placeholder="Image URL">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" wire:click="closeModal" 
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700">
                                {{ $editingItem ? 'Update SEO' : 'Save Settings' }}
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
