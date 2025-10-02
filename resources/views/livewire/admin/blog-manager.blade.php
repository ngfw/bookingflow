<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Blog Manager</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <button wire:click="openModal()" 
                            class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700">
                        New Post
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input wire:model="search" type="text" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                           placeholder="Search posts...">
                </div>
                <div class="sm:w-48">
                    <select wire:model="filter" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                        <option value="all">All Posts</option>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                        <option value="featured">Featured</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:w-48">
                    <select wire:model="sortBy" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                        <option value="created_at">Date Created</option>
                        <option value="published_at">Date Published</option>
                        <option value="title">Title</option>
                        <option value="views_count">Views</option>
                        <option value="likes_count">Likes</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Posts List -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Blog Posts</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($posts as $post)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <h4 class="text-lg font-medium text-gray-900">{{ $post->title }}</h4>
                                @if($post->is_published)
                                    <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Published</span>
                                @else
                                    <span class="ml-2 px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">Draft</span>
                                @endif
                                @if($post->is_featured)
                                    <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Featured</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 mt-1">{{ $post->excerpt ?: 'No excerpt' }}</p>
                            <div class="flex items-center mt-2 space-x-4 text-sm text-gray-500">
                                <span>By {{ $post->author_name ?: 'Unknown' }}</span>
                                <span>{{ $post->created_at->diffForHumans() }}</span>
                                @if($post->category)
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">{{ $post->category }}</span>
                                @endif
                                <span>{{ $post->views_count }} views</span>
                                <span>{{ $post->likes_count }} likes</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button wire:click="duplicatePost({{ $post->id }})" 
                                    class="p-1 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                            <button wire:click="openModal({{ $post->id }})" 
                                    class="p-1 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button wire:click="deletePost({{ $post->id }})" 
                                    class="p-1 text-red-400 hover:text-red-600"
                                    onclick="return confirm('Are you sure you want to delete this post?')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No blog posts found</h3>
                        <p class="text-gray-600 mb-4">Get started by creating your first blog post.</p>
                        <button wire:click="openModal()" 
                                class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700">
                            Create Post
                        </button>
                    </div>
                @endforelse
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $posts->links() }}
            </div>
        </div>
    </div>

    <!-- Blog Post Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $editingPost ? 'Edit Blog Post' : 'Create New Blog Post' }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="savePost" class="space-y-6">
                        <!-- Basic Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Post Title *</label>
                                <input wire:model="post_title" type="text" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                       placeholder="Enter post title">
                                @error('post_title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">URL Slug *</label>
                                <div class="flex">
                                    <input wire:model="post_slug" type="text" 
                                           class="flex-1 border-gray-300 rounded-l-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                           placeholder="post-url-slug">
                                    <button wire:click="generateSlug" type="button" 
                                            class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-sm text-gray-600 hover:bg-gray-200">
                                        Generate
                                    </button>
                                </div>
                                @error('post_slug') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Excerpt</label>
                            <textarea wire:model="post_excerpt" rows="3" 
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                      placeholder="Brief description of the post"></textarea>
                        </div>

                        <!-- Content Editor -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-700">Content *</label>
                                <button wire:click="togglePreview" type="button" 
                                        class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">
                                    {{ $showPreview ? 'Edit' : 'Preview' }}
                                </button>
                            </div>
                            
                            @if($showPreview)
                                <div class="border border-gray-300 rounded-lg p-4 bg-gray-50 min-h-64">
                                    <div class="prose max-w-none">
                                        {!! $post_content !!}
                                    </div>
                                </div>
                            @else
                                <textarea wire:model="post_content" rows="12" 
                                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                          placeholder="Write your blog post content here..."></textarea>
                            @endif
                            @error('post_content') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Featured Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                            <input wire:model="post_featured_image" type="file" accept="image/*" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                        </div>

                        <!-- Author Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Author Name</label>
                                <input wire:model="post_author_name" type="text" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                       placeholder="Author name">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Author Email</label>
                                <input wire:model="post_author_email" type="email" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                       placeholder="author@example.com">
                            </div>
                        </div>

                        <!-- Category and Tags -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <input wire:model="post_category" type="text" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                       placeholder="Post category">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                                <div class="flex">
                                    <input wire:model="new_tag" type="text" 
                                           class="flex-1 border-gray-300 rounded-l-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                           placeholder="Add tag"
                                           wire:keydown.enter.prevent="addTag">
                                    <button wire:click="addTag" type="button" 
                                            class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-sm text-gray-600 hover:bg-gray-200">
                                        Add
                                    </button>
                                </div>
                                @if(count($post_tags) > 0)
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach($post_tags as $index => $tag)
                                            <span class="inline-flex items-center px-2 py-1 bg-pink-100 text-pink-800 text-xs rounded-full">
                                                {{ $tag }}
                                                <button wire:click="removeTag({{ $index }})" 
                                                        class="ml-1 text-pink-600 hover:text-pink-800">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Settings -->
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input wire:model="post_is_published" type="checkbox" 
                                       class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Published</span>
                            </label>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" wire:click="closeModal" 
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700">
                                {{ $editingPost ? 'Update Post' : 'Create Post' }}
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
