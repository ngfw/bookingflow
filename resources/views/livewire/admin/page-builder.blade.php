<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Page Builder</h1>
                    @if($page->id)
                        <span class="ml-4 px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">
                            {{ $page->title }}
                        </span>
                    @endif
                </div>
                <div class="flex items-center space-x-4">
                    @if($page->id)
                        <button wire:click="togglePreview" 
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                            {{ $previewMode ? 'Edit' : 'Preview' }}
                        </button>
                        <a href="{{ $page->url }}" target="_blank" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                            View Page
                        </a>
                    @endif
                    <button wire:click="$set('showPageModal', true)" 
                            class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700">
                        {{ $page->id ? 'Edit Page' : 'Create Page' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($previewMode)
            <!-- Preview Mode -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Page Preview</h2>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <p class="text-gray-500">Page preview will be rendered here</p>
                    <p class="text-sm text-gray-400 mt-2">Sections will be displayed in their final form</p>
                </div>
            </div>
        @else
            <!-- Page Builder Interface -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Left Sidebar - Section Library -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Section Library</h3>
                        <div class="space-y-3">
                            @foreach($availableSections as $key => $label)
                                <div class="p-3 border border-gray-200 rounded-lg cursor-move hover:bg-gray-50"
                                     draggable="true"
                                     wire:click="addSection"
                                     data-section-type="{{ $key }}">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-pink-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $label }}</p>
                                            <p class="text-xs text-gray-500">{{ ucfirst($key) }} section</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-lg shadow-sm">
                        <!-- Page Header -->
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-900">
                                        {{ $page->id ? 'Edit Page' : 'Create New Page' }}
                                    </h2>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $page->id ? 'Modify your page content and sections' : 'Build your page with drag-and-drop sections' }}
                                    </p>
                                </div>
                                <button wire:click="$set('showPageModal', true)" 
                                        class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700">
                                    {{ $page->id ? 'Edit Page Settings' : 'Page Settings' }}
                                </button>
                            </div>
                        </div>

                        <!-- Sections Area -->
                        <div class="p-6">
                            @if(count($sections) > 0)
                                <div class="space-y-4" id="sections-container">
                                    @foreach($sections as $index => $section)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:border-pink-300 transition-colors"
                                             data-section-id="{{ $section['id'] }}">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex items-center">
                                                    <div class="w-6 h-6 bg-pink-100 rounded-lg flex items-center justify-center mr-3">
                                                        <svg class="w-3 h-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-sm font-medium text-gray-900">
                                                            {{ $section['title'] ?: ucfirst($section['section_type']) }} Section
                                                        </h4>
                                                        <p class="text-xs text-gray-500">{{ $section['section_type'] }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <button wire:click="toggleSectionVisibility({{ $section['id'] }})" 
                                                            class="p-1 text-gray-400 hover:text-gray-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </button>
                                                    <button wire:click="duplicateSection({{ $section['id'] }})" 
                                                            class="p-1 text-gray-400 hover:text-gray-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </button>
                                                    <button wire:click="editSection({{ $section['id'] }})" 
                                                            class="p-1 text-gray-400 hover:text-gray-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </button>
                                                    <button wire:click="deleteSection({{ $section['id'] }})" 
                                                            class="p-1 text-red-400 hover:text-red-600"
                                                            onclick="return confirm('Are you sure you want to delete this section?')">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Section Preview -->
                                            <div class="bg-gray-50 rounded-lg p-4">
                                                <div class="text-sm text-gray-600">
                                                    @if($section['title'])
                                                        <p class="font-medium">{{ $section['title'] }}</p>
                                                    @endif
                                                    @if($section['content'])
                                                        <p class="mt-1">{{ Str::limit($section['content'], 100) }}</p>
                                                    @endif
                                                    <div class="mt-2 flex items-center">
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $section['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                            {{ $section['is_active'] ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No sections yet</h3>
                                    <p class="text-gray-600 mb-4">Start building your page by adding sections from the library.</p>
                                    <button wire:click="addSection" 
                                            class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700">
                                        Add First Section
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Page Settings Modal -->
    @if($showPageModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $page->id ? 'Edit Page Settings' : 'Create New Page' }}
                        </h3>
                        <button wire:click="$set('showPageModal', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="savePage" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Page Title *</label>
                                <input wire:model="title" type="text" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                       placeholder="Enter page title">
                                @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">URL Slug *</label>
                                <input wire:model="slug" type="text" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                       placeholder="page-url-slug">
                                @error('slug') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Page Excerpt</label>
                            <textarea wire:model="excerpt" rows="3" 
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                      placeholder="Brief description of the page"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Page Content</label>
                            <textarea wire:model="content" rows="6" 
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                      placeholder="Main page content (optional if using sections)"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Template</label>
                                <select wire:model="template" 
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                                    <option value="default">Default</option>
                                    <option value="landing">Landing Page</option>
                                    <option value="blog">Blog</option>
                                    <option value="gallery">Gallery</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                                <input wire:model="featured_image" type="file" accept="image/*" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input wire:model="is_published" type="checkbox" 
                                       class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Published</span>
                            </label>

                            <label class="flex items-center">
                                <input wire:model="is_homepage" type="checkbox" 
                                       class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Homepage</span>
                            </label>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" wire:click="$set('showPageModal', false)" 
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700">
                                {{ $page->id ? 'Update Page' : 'Create Page' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Section Editor Modal -->
    @if($showSectionModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $editingSection ? 'Edit Section' : 'Add New Section' }}
                        </h3>
                        <button wire:click="$set('showSectionModal', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveSection" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Section Type *</label>
                                <select wire:model="section_type" 
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                                    <option value="">Select section type</option>
                                    @foreach($availableSections as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('section_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Section Title</label>
                                <input wire:model="section_title" type="text" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                       placeholder="Section title (optional)">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Section Content</label>
                            <textarea wire:model="section_content" rows="6" 
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                      placeholder="Section content"></textarea>
                        </div>

                        @if($section_type)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Section Preview</h4>
                                <div class="text-sm text-gray-600">
                                    <p><strong>Type:</strong> {{ $availableSections[$section_type] ?? $section_type }}</p>
                                    @if($section_title)
                                        <p><strong>Title:</strong> {{ $section_title }}</p>
                                    @endif
                                    @if($section_content)
                                        <p><strong>Content:</strong> {{ Str::limit($section_content, 100) }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" wire:click="$set('showSectionModal', false)" 
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700">
                                {{ $editingSection ? 'Update Section' : 'Add Section' }}
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
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Drag and drop functionality
    document.addEventListener('DOMContentLoaded', function() {
        const sectionsContainer = document.getElementById('sections-container');
        if (sectionsContainer) {
            // Make sections sortable
            new Sortable(sectionsContainer, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function(evt) {
                    const orderedIds = Array.from(sectionsContainer.children).map(el => el.dataset.sectionId);
                    @this.call('reorderSections', orderedIds);
                }
            });
        }
    });
</script>
@endpush
