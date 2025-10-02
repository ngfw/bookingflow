<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">User Information</h3>
                    <div class="space-y-2">
                        <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                        <p><strong>Role:</strong> {{ Auth::user()->role }}</p>
                        <p><strong>Phone:</strong> {{ Auth::user()->phone ?? 'Not provided' }}</p>
                        <p><strong>Address:</strong> {{ Auth::user()->address ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
