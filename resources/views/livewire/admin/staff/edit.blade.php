<div class="p-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Staff Member</h1>
                <p class="text-gray-600">Update staff member information and settings</p>
            </div>
            <a href="{{ route('admin.staff.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                ← Back to Staff
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="updateStaff" class="space-y-8">
        <!-- Profile Image Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Profile Image</h3>
            
            <div class="flex items-center space-x-6">
                <div class="shrink-0">
                    @if($profile_image)
                        <img class="h-20 w-20 object-cover rounded-full" src="{{ Storage::url($profile_image) }}" alt="Profile">
                    @else
                        <div class="h-20 w-20 bg-gray-200 rounded-full flex items-center justify-center">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                
                <div class="flex-1">
                    <div class="flex items-center space-x-4">
                        <label class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md cursor-pointer">
                            <span>Upload new photo</span>
                            <input type="file" wire:model="new_profile_image" accept="image/*" class="hidden">
                        </label>
                        
                        @if($profile_image)
                            <button type="button" wire:click="deleteProfileImage" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
                                Remove photo
                            </button>
                        @endif
                    </div>
                    
                    @if($new_profile_image)
                        <div class="mt-2">
                            <p class="text-sm text-green-600">New image selected: {{ $new_profile_image->getClientOriginalName() }}</p>
                        </div>
                    @endif
                    
                    @error('new_profile_image')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                    <input type="text" wire:model="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                    <input type="email" wire:model="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="text" wire:model="phone" id="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee ID</label>
                    <input type="text" wire:model="employee_id" id="employee_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('employee_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="bio" class="block text-sm font-medium text-gray-700">Biography</label>
                <textarea wire:model="bio" id="bio" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Tell us about this staff member..."></textarea>
                @error('bio') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Professional Profile -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Professional Profile</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="experience_years" class="block text-sm font-medium text-gray-700">Years of Experience</label>
                    <input type="number" wire:model="experience_years" id="experience_years" min="0" max="50" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                           placeholder="5">
                    @error('experience_years') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="languages" class="block text-sm font-medium text-gray-700">Languages Spoken</label>
                    <input type="text" wire:model="languages" id="languages" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                           placeholder="English, Spanish, French">
                    <p class="mt-1 text-sm text-gray-500">Separate languages with commas</p>
                    @error('languages') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="education" class="block text-sm font-medium text-gray-700">Education & Training</label>
                <textarea wire:model="education" id="education" rows="3" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                          placeholder="Cosmetology School, Beauty Academy certification, etc."></textarea>
                @error('education') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mt-6">
                <label for="certifications" class="block text-sm font-medium text-gray-700">Certifications & Licenses</label>
                <textarea wire:model="certifications" id="certifications" rows="3" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                          placeholder="State Cosmetology License, Advanced Color Certification, etc."></textarea>
                @error('certifications') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mt-6">
                <label for="achievements" class="block text-sm font-medium text-gray-700">Awards & Achievements</label>
                <textarea wire:model="achievements" id="achievements" rows="3" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                          placeholder="Best Stylist Award 2023, Customer Service Excellence Award, etc."></textarea>
                @error('achievements') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Social Media Links -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-4">Social Media Profiles</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="instagram" class="block text-sm font-medium text-gray-600">Instagram</label>
                        <input type="url" wire:model="social_media.instagram" id="instagram" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               placeholder="https://instagram.com/username">
                    </div>
                    
                    <div>
                        <label for="facebook" class="block text-sm font-medium text-gray-600">Facebook</label>
                        <input type="url" wire:model="social_media.facebook" id="facebook" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               placeholder="https://facebook.com/username">
                    </div>
                    
                    <div>
                        <label for="linkedin" class="block text-sm font-medium text-gray-600">LinkedIn</label>
                        <input type="url" wire:model="social_media.linkedin" id="linkedin" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               placeholder="https://linkedin.com/in/username">
                    </div>
                    
                    <div>
                        <label for="tiktok" class="block text-sm font-medium text-gray-600">TikTok</label>
                        <input type="url" wire:model="social_media.tiktok" id="tiktok" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               placeholder="https://tiktok.com/@username">
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <label for="hobbies" class="block text-sm font-medium text-gray-700">Hobbies & Interests</label>
                <textarea wire:model="hobbies" id="hobbies" rows="2" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                          placeholder="Photography, traveling, fitness, cooking, etc."></textarea>
                <p class="mt-1 text-sm text-gray-500">Help clients connect with staff on a personal level</p>
                @error('hobbies') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Account Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Account Settings</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" wire:model="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-sm text-gray-500">Leave blank to keep current password</p>
                    @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" wire:model="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('password_confirmation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6">
                <label class="flex items-center">
                    <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Account is active</span>
                </label>
            </div>
        </div>

        <!-- Employment Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Employment Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700">Position *</label>
                    <input type="text" wire:model="position" id="position" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g., Hair Stylist, Nail Technician">
                    @error('position') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="employment_type" class="block text-sm font-medium text-gray-700">Employment Type *</label>
                    <select wire:model="employment_type" id="employment_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="full_time">Full Time</option>
                        <option value="part_time">Part Time</option>
                        <option value="contract">Contract</option>
                    </select>
                    @error('employment_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="hire_date" class="block text-sm font-medium text-gray-700">Hire Date *</label>
                    <input type="date" wire:model="hire_date" id="hire_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('hire_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="hourly_rate" class="block text-sm font-medium text-gray-700">Hourly Rate ($)</label>
                    <input type="number" wire:model="hourly_rate" id="hourly_rate" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('hourly_rate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="commission_rate" class="block text-sm font-medium text-gray-700">Commission Rate (%)</label>
                    <input type="number" wire:model="commission_rate" id="commission_rate" step="0.01" min="0" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('commission_rate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Specializations -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Specializations</h3>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @php
                    $specialOptions = ['Hair Cutting', 'Hair Coloring', 'Hair Styling', 'Manicures', 'Pedicures', 'Nail Art', 'Facials', 'Massages', 'Waxing', 'Eyebrow Threading', 'Makeup', 'Eyelash Extensions'];
                @endphp
                
                @foreach($specialOptions as $option)
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="specializations" value="{{ $option }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">{{ $option }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Working Hours & Days -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Working Schedule</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="default_start_time" class="block text-sm font-medium text-gray-700">Default Start Time</label>
                    <input type="time" wire:model="default_start_time" id="default_start_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('default_start_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="default_end_time" class="block text-sm font-medium text-gray-700">Default End Time</label>
                    <input type="time" wire:model="default_end_time" id="default_end_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('default_end_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Working Days</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @php
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    @endphp
                    
                    @foreach($days as $day)
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="working_days" value="{{ $day }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">{{ $day }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Services Assignment -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Services Assignment</h3>
            
            @if($availableServices->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($availableServices as $service)
                        <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50">
                            <input type="checkbox" wire:model="selectedServices" value="{{ $service->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <div class="ml-3 flex-1">
                                <span class="text-sm font-medium text-gray-900">{{ $service->name }}</span>
                                <p class="text-sm text-gray-500">${{ number_format($service->price, 2) }} • {{ $service->duration_minutes }} min</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No services available. Please create services first.</p>
            @endif
        </div>

        <!-- Online Booking Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Online Booking Settings</h3>
            
            <div>
                <label class="flex items-center">
                    <input type="checkbox" wire:model="can_book_online" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Allow online booking for this staff member</span>
                </label>
                <p class="mt-1 text-sm text-gray-500">When enabled, clients can book appointments with this staff member through the online booking system.</p>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.staff.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md">
                Update Staff Member
            </button>
        </div>
    </form>
</div>
