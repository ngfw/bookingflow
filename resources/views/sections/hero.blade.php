<section class="bg-gradient-to-r from-pink-100 to-purple-100 py-20 rounded-lg mb-12">
    <div class="text-center">
        <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
            {{ $title ?: 'Welcome to Our Salon' }}
        </h1>
        <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
            {{ $content ?: 'Experience luxury beauty services in a relaxing environment.' }}
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/book" class="bg-pink-600 hover:bg-pink-700 text-white px-8 py-3 rounded-lg text-lg font-medium">
                Book Appointment
            </a>
            <a href="/services" class="bg-white hover:bg-gray-50 text-pink-600 px-8 py-3 rounded-lg text-lg font-medium border-2 border-pink-600">
                View Services
            </a>
        </div>
    </div>
</section>
