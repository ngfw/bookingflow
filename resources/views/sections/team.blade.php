<section class="mb-12">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $title ?: 'Meet Our Team' }}</h2>
        <p class="text-lg text-gray-600">{{ $content ?: 'Our skilled professionals are here to help you look and feel your best' }}</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($staff as $member)
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                @if($member->profile_image)
                    <img src="{{ asset('storage/' . $member->profile_image) }}" 
                         alt="{{ $member->name }}" 
                         class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                @else
                    <div class="w-24 h-24 bg-pink-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="text-pink-600 font-semibold text-xl">{{ substr($member->name, 0, 1) }}</span>
                    </div>
                @endif
                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $member->name }}</h3>
                <p class="text-pink-600 font-medium mb-2">{{ $member->position }}</p>
                @if($member->bio)
                    <p class="text-gray-600 text-sm">{{ Str::limit($member->bio, 100) }}</p>
                @endif
                @if($member->specialties)
                    <div class="mt-4">
                        @foreach($member->specialties as $specialty)
                            <span class="inline-block bg-pink-100 text-pink-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">
                                {{ $specialty }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500">No team members available at the moment.</p>
            </div>
        @endforelse
    </div>
</section>
