<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Page</h1>
                <p class="text-gray-600">Edit "{{ $page->title }}"</p>
            </div>
            <a href="{{ route('admin.pages.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
                Back to Pages
            </a>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-8">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                    <input wire:model.live="title" type="text" id="title" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('title') border-red-300 @enderror">
                    @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">URL Slug *</label>
                    <div class="flex">
                        <input wire:model="slug" type="text" id="slug" 
                               class="flex-1 border-gray-300 rounded-l-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('slug') border-red-300 @enderror">
                        <button type="button" wire:click="generateSlug" 
                                class="px-4 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-sm font-medium text-gray-700 hover:bg-gray-200">
                            Generate
                        </button>
                    </div>
                    @error('slug') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">Excerpt</label>
                    <textarea wire:model="excerpt" id="excerpt" rows="3" 
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('excerpt') border-red-300 @enderror"></textarea>
                    <p class="mt-1 text-sm text-gray-500">Brief description of the page (max 500 characters)</p>
                    @error('excerpt') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="template" class="block text-sm font-medium text-gray-700 mb-2">Template *</label>
                    <select wire:model="template" id="template" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('template') border-red-300 @enderror">
                        @foreach($templates as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('template') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                    <input wire:model="sort_order" type="number" id="sort_order" min="0" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('sort_order') border-red-300 @enderror">
                    @error('sort_order') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                    @if($current_featured_image)
                        <div class="mb-2">
                            <img src="{{ Storage::url($current_featured_image) }}" alt="Current featured image" class="w-32 h-32 object-cover rounded-lg">
                        </div>
                    @endif
                    <input wire:model="featured_image" type="file" id="featured_image" accept="image/*" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('featured_image') border-red-300 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Maximum file size: 2MB. Leave empty to keep current image.</p>
                    @error('featured_image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Content</h2>
            
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Page Content</label>
                <textarea wire:model="content" id="content" rows="15" 
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('content') border-red-300 @enderror"></textarea>
                <p class="mt-1 text-sm text-gray-500">You can use HTML tags for formatting</p>
                @error('content') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- SEO Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">SEO Settings</h2>
            
            <div class="space-y-6">
                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                    <input wire:model="meta_title" type="text" id="meta_title" maxlength="60" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('meta_title') border-red-300 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Recommended: 50-60 characters ({{ strlen($meta_title) }}/60)</p>
                    @error('meta_title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                    <textarea wire:model="meta_description" id="meta_description" rows="3" maxlength="160" 
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('meta_description') border-red-300 @enderror"></textarea>
                    <p class="mt-1 text-sm text-gray-500">Recommended: 150-160 characters ({{ strlen($meta_description) }}/160)</p>
                    @error('meta_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                    <input wire:model="meta_keywords" type="text" id="meta_keywords" 
                           placeholder="keyword1, keyword2, keyword3" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('meta_keywords') border-red-300 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Separate keywords with commas</p>
                    @error('meta_keywords') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Publishing Options -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Publishing Options</h2>
            
            <div class="space-y-4">
                <div class="flex items-center">
                    <input wire:model="is_published" type="checkbox" id="is_published" 
                           class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                    <label for="is_published" class="ml-2 block text-sm text-gray-900">
                        Publish this page
                    </label>
                </div>

                <div class="flex items-center">
                    <input wire:model="is_homepage" type="checkbox" id="is_homepage" 
                           class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                    <label for="is_homepage" class="ml-2 block text-sm text-gray-900">
                        Set as homepage
                    </label>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.pages.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-medium">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-2 rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed">
                <span wire:loading.remove>Update Page</span>
                <span wire:loading>Updating...</span>
            </button>
        </div>
    </form>
</div>