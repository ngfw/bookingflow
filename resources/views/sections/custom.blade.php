<section class="mb-12">
    @if($title)
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $title }}</h2>
        </div>
    @endif
    
    @if($content)
        <div class="prose max-w-none">
            {!! $content !!}
        </div>
    @endif
    
    @if(empty($content) && empty($title))
        <div class="text-center py-12">
            <p class="text-gray-500">This section is empty. Please add content in the page builder.</p>
        </div>
    @endif
</section>
