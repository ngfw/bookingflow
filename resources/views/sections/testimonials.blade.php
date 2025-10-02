<section class="mb-12">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $title ?: 'What Our Clients Say' }}</h2>
        <p class="text-lg text-gray-600">{{ $content ?: 'Real experiences from our satisfied customers' }}</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($testimonials as $testimonial)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center mr-4">
                        <span class="text-pink-600 font-semibold">{{ $testimonial->client_initials }}</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">{{ $testimonial->client_name }}</h4>
                        <div class="flex items-center">
                            {!! $testimonial->star_rating !!}
                        </div>
                    </div>
                </div>
                <p class="text-gray-600">"{{ $testimonial->content }}"</p>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500">No testimonials available at the moment.</p>
            </div>
        @endforelse
    </div>
</section>
