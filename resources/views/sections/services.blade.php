<section class="mb-12">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $title ?: 'Our Services' }}</h2>
        <p class="text-lg text-gray-600">{{ $content ?: 'Professional beauty services tailored to your needs' }}</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($services as $service)
            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $service->name }}</h3>
                <p class="text-gray-600 mb-4">{{ Str::limit($service->description, 100) }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold text-pink-600">${{ number_format($service->price, 2) }}</span>
                    <span class="text-sm text-gray-500">{{ $service->duration_minutes }} min</span>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500">No services available at the moment.</p>
            </div>
        @endforelse
    </div>
</section>
