<section class="mb-12">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $title ?: 'Our Work' }}</h2>
        <p class="text-lg text-gray-600">{{ $content ?: 'See the beautiful transformations we create' }}</p>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @forelse($galleries as $gallery)
            @if($gallery->thumbnail)
                <div class="aspect-w-1 aspect-h-1">
                    <img src="{{ asset('storage/' . $gallery->thumbnail) }}" 
                         alt="{{ $gallery->name }}" 
                         class="w-full h-full object-cover rounded-lg hover:opacity-90 transition-opacity cursor-pointer">
                </div>
            @endif
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500">No gallery images available at the moment.</p>
            </div>
        @endforelse
    </div>
</section>
