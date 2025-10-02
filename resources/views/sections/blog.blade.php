<section class="mb-12">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $title ?: 'Latest News & Tips' }}</h2>
        <p class="text-lg text-gray-600">{{ $content ?: 'Stay updated with beauty tips, trends, and salon news' }}</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($posts as $post)
            <article class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                @if($post->featured_image)
                    <img src="{{ asset('storage/' . $post->featured_image) }}" 
                         alt="{{ $post->title }}" 
                         class="w-full h-48 object-cover">
                @endif
                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-500 mb-2">
                        <span>{{ $post->published_at->format('M j, Y') }}</span>
                        @if($post->category)
                            <span class="mx-2">•</span>
                            <span class="text-pink-600">{{ $post->category }}</span>
                        @endif
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        <a href="{{ $post->url }}" class="hover:text-pink-600">
                            {{ $post->title }}
                        </a>
                    </h3>
                    <p class="text-gray-600 mb-4">{{ $post->excerpt ?: Str::limit(strip_tags($post->content), 120) }}</p>
                    <div class="flex items-center justify-between">
                        <a href="{{ $post->url }}" class="text-pink-600 hover:text-pink-700 font-medium">
                            Read More →
                        </a>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            {{ $post->views_count }}
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500">No blog posts available at the moment.</p>
            </div>
        @endforelse
    </div>
    
    @if($posts->count() > 0)
        <div class="text-center mt-8">
            <a href="/blog" class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-3 rounded-lg font-medium">
                View All Posts
            </a>
        </div>
    @endif
</section>
