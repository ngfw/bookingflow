<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">About Us Page Management</h2>
        <p class="text-gray-600 mt-2">Manage your About Us page content and team display settings</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="save">
        <div class="bg-white rounded-lg shadow-md p-6 space-y-6">
            <!-- Page Title -->
            <div>
                <label for="about_us_title" class="block text-sm font-medium text-gray-700 mb-2">
                    Page Title <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="about_us_title"
                    wire:model="about_us_title"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="About Us"
                >
                @error('about_us_title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Main Content -->
            <div>
                <label for="about_us_content" class="block text-sm font-medium text-gray-700 mb-2">
                    Main Content
                </label>
                <textarea
                    id="about_us_content"
                    wire:model="about_us_content"
                    rows="6"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Tell visitors about your salon, your story, and what makes you unique..."
                ></textarea>
                @error('about_us_content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Mission Statement -->
            <div>
                <label for="about_us_mission" class="block text-sm font-medium text-gray-700 mb-2">
                    Our Mission
                </label>
                <textarea
                    id="about_us_mission"
                    wire:model="about_us_mission"
                    rows="3"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Describe your mission and core values..."
                ></textarea>
                @error('about_us_mission') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Vision Statement -->
            <div>
                <label for="about_us_vision" class="block text-sm font-medium text-gray-700 mb-2">
                    Our Vision
                </label>
                <textarea
                    id="about_us_vision"
                    wire:model="about_us_vision"
                    rows="3"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Share your vision for the future..."
                ></textarea>
                @error('about_us_vision') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- About Us Image -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    About Us Image
                </label>
                @if($about_us_image && !$new_image)
                    <div class="mb-3">
                        <img src="{{ Storage::url($about_us_image) }}" alt="About Us" class="h-32 w-auto rounded-lg shadow-md">
                    </div>
                @endif
                @if($new_image)
                    <div class="mb-3">
                        <img src="{{ $new_image->temporaryUrl() }}" alt="Preview" class="h-32 w-auto rounded-lg shadow-md">
                    </div>
                @endif
                <input
                    type="file"
                    wire:model="new_image"
                    accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                >
                @error('new_image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                <p class="text-xs text-gray-500 mt-1">Max size: 2MB. Recommended: 1200x600px</p>
            </div>

            <!-- Show Team Toggle -->
            <div class="flex items-center space-x-3">
                <input
                    type="checkbox"
                    id="show_team_on_about"
                    wire:model="show_team_on_about"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                <label for="show_team_on_about" class="text-sm font-medium text-gray-700">
                    Display team members on About Us page
                </label>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4 border-t">
                <button
                    type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-semibold transition-colors"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Save Changes</span>
                    <span wire:loading>Saving...</span>
                </button>
            </div>
        </div>
    </form>

    <!-- Team Management Info -->
    @if($show_team_on_about)
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-blue-900">Team Member Settings</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        To manage which team members appear on the About Us page, go to
                        <a href="{{ route('admin.staff.index') }}" class="underline font-semibold">Staff Management</a>
                        and edit individual staff profiles to toggle "Display on Website" and upload profile images.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
