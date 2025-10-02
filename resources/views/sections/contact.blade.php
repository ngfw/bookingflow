<section class="mb-12">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $title ?: 'Contact Us' }}</h2>
        <p class="text-lg text-gray-600">{{ $content ?: 'Get in touch with us to book your appointment or ask any questions' }}</p>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Contact Information -->
        <div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-6">Get In Touch</h3>
            
            <div class="space-y-6">
                <!-- Phone -->
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-gray-900">Phone</h4>
                        <p class="text-gray-600">+1 (555) 123-4567</p>
                    </div>
                </div>

                <!-- Email -->
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-gray-900">Email</h4>
                        <p class="text-gray-600">info@beautysalon.com</p>
                    </div>
                </div>

                <!-- Address -->
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-gray-900">Address</h4>
                        <p class="text-gray-600">123 Beauty Street<br>City, State 12345</p>
                    </div>
                </div>

                <!-- Hours -->
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-gray-900">Hours</h4>
                        <div class="text-gray-600 space-y-1">
                            <p>Monday - Friday: 9:00 AM - 7:00 PM</p>
                            <p>Saturday: 9:00 AM - 6:00 PM</p>
                            <p>Sunday: 10:00 AM - 5:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-6">Send us a Message</h3>
            
            <form class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" id="name" name="name" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                           placeholder="Your full name">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                           placeholder="your@email.com">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="tel" id="phone" name="phone" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                           placeholder="(555) 123-4567">
                </div>

                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                    <input type="text" id="subject" name="subject" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                           placeholder="What's this about?">
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea id="message" name="message" rows="4" 
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                              placeholder="Tell us how we can help you..."></textarea>
                </div>

                <button type="submit" 
                        class="w-full bg-pink-600 hover:bg-pink-700 text-white py-3 px-4 rounded-lg font-medium">
                    Send Message
                </button>
            </form>
        </div>
    </div>
</section>
