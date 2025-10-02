<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Social Media Manager</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <button wire:click="openModal('schedule')" 
                            class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700">
                        Schedule Post
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6">
                    <button wire:click="setActiveTab('settings')" 
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'settings' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Settings
                    </button>
                    <button wire:click="setActiveTab('analytics')" 
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'analytics' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Analytics
                    </button>
                    <button wire:click="setActiveTab('content')" 
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'content' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Content Ideas
                    </button>
                </nav>
            </div>
        </div>

        <!-- Content -->
        @if($activeTab === 'settings')
            <!-- Social Media Settings -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Social Media Links</h3>
                    <button wire:click="openModal('links')" 
                            class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700">
                        Update Links
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Facebook -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Facebook</h4>
                                <p class="text-xs text-gray-500">Connect your Facebook page</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            @if($facebook_url)
                                <a href="{{ $facebook_url }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                    {{ $facebook_url }}
                                </a>
                            @else
                                <span class="text-gray-400">Not connected</span>
                            @endif
                        </div>
                    </div>

                    <!-- Instagram -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987s11.987-5.367 11.987-11.987C24.014 5.367 18.647.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.418-3.323c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297c.928.875 1.418 2.026 1.418 3.323s-.49 2.448-1.418 3.244c-.875.807-2.026 1.297-3.323 1.297zm7.83-9.281c-.49 0-.928-.175-1.297-.49-.368-.315-.49-.753-.49-1.243 0-.49.122-.928.49-1.243.369-.315.807-.49 1.297-.49s.928.175 1.297.49c.368.315.49.753.49 1.243 0 .49-.122.928-.49 1.243-.369.315-.807.49-1.297.49z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Instagram</h4>
                                <p class="text-xs text-gray-500">Connect your Instagram account</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            @if($instagram_url)
                                <a href="{{ $instagram_url }}" target="_blank" class="text-pink-600 hover:text-pink-800">
                                    {{ $instagram_url }}
                                </a>
                            @else
                                <span class="text-gray-400">Not connected</span>
                            @endif
                        </div>
                    </div>

                    <!-- Twitter -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-blue-400 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Twitter</h4>
                                <p class="text-xs text-gray-500">Connect your Twitter account</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            @if($twitter_url)
                                <a href="{{ $twitter_url }}" target="_blank" class="text-blue-400 hover:text-blue-600">
                                    {{ $twitter_url }}
                                </a>
                            @else
                                <span class="text-gray-400">Not connected</span>
                            @endif
                        </div>
                    </div>

                    <!-- YouTube -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">YouTube</h4>
                                <p class="text-xs text-gray-500">Connect your YouTube channel</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            @if($youtube_url)
                                <a href="{{ $youtube_url }}" target="_blank" class="text-red-600 hover:text-red-800">
                                    {{ $youtube_url }}
                                </a>
                            @else
                                <span class="text-gray-400">Not connected</span>
                            @endif
                        </div>
                    </div>

                    <!-- TikTok -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-black rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">TikTok</h4>
                                <p class="text-xs text-gray-500">Connect your TikTok account</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            @if($tiktok_url)
                                <a href="{{ $tiktok_url }}" target="_blank" class="text-gray-800 hover:text-black">
                                    {{ $tiktok_url }}
                                </a>
                            @else
                                <span class="text-gray-400">Not connected</span>
                            @endif
                        </div>
                    </div>

                    <!-- LinkedIn -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-blue-700 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">LinkedIn</h4>
                                <p class="text-xs text-gray-500">Connect your LinkedIn page</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            @if($linkedin_url)
                                <a href="{{ $linkedin_url }}" target="_blank" class="text-blue-700 hover:text-blue-900">
                                    {{ $linkedin_url }}
                                </a>
                            @else
                                <span class="text-gray-400">Not connected</span>
                            @endif
                        </div>
                    </div>

                    <!-- Pinterest -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.746-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001 12.017.001z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Pinterest</h4>
                                <p class="text-xs text-gray-500">Connect your Pinterest account</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            @if($pinterest_url)
                                <a href="{{ $pinterest_url }}" target="_blank" class="text-red-600 hover:text-red-800">
                                    {{ $pinterest_url }}
                                </a>
                            @else
                                <span class="text-gray-400">Not connected</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'analytics')
            <!-- Social Media Analytics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($analyticsData as $platform => $data)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 capitalize">{{ $platform }}</h3>
                                <p class="text-sm text-gray-500">Social Media Analytics</p>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Followers</span>
                                <span class="text-sm font-medium text-gray-900">{{ number_format($data['followers']) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Engagement Rate</span>
                                <span class="text-sm font-medium text-gray-900">{{ number_format($data['engagement'] * 100, 1) }}%</span>
                            </div>
                            <div class="pt-2">
                                <a href="{{ $data['url'] }}" target="_blank" 
                                   class="text-sm text-pink-600 hover:text-pink-800">
                                    View Profile →
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Analytics Data</h3>
                        <p class="text-gray-600">Connect your social media accounts to see analytics.</p>
                    </div>
                @endforelse
            </div>

        @elseif($activeTab === 'content')
            <!-- Content Ideas -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Content Ideas</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- General Ideas -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">General Ideas</h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            @foreach($contentSuggestions as $idea)
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-pink-600 rounded-full mt-2 mr-2 flex-shrink-0"></span>
                                    {{ $idea }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Facebook Ideas -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Facebook Ideas</h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            @foreach($platformSuggestions['facebook'] as $idea)
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-blue-600 rounded-full mt-2 mr-2 flex-shrink-0"></span>
                                    {{ $idea }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Instagram Ideas -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Instagram Ideas</h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            @foreach($platformSuggestions['instagram'] as $idea)
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-pink-500 rounded-full mt-2 mr-2 flex-shrink-0"></span>
                                    {{ $idea }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Social Links Modal -->
    @if($showModal && $modalType === 'links')
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Update Social Media Links</h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveSocialLinks" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Facebook URL</label>
                            <input wire:model="facebook_url" type="url" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                   placeholder="https://facebook.com/yourpage">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Instagram URL</label>
                            <input wire:model="instagram_url" type="url" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                   placeholder="https://instagram.com/yourpage">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Twitter URL</label>
                            <input wire:model="twitter_url" type="url" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                   placeholder="https://twitter.com/yourpage">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">YouTube URL</label>
                            <input wire:model="youtube_url" type="url" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                   placeholder="https://youtube.com/yourchannel">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">TikTok URL</label>
                            <input wire:model="tiktok_url" type="url" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                   placeholder="https://tiktok.com/@yourpage">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">LinkedIn URL</label>
                            <input wire:model="linkedin_url" type="url" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                   placeholder="https://linkedin.com/company/yourpage">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pinterest URL</label>
                            <input wire:model="pinterest_url" type="url" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                   placeholder="https://pinterest.com/yourpage">
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" wire:click="closeModal" 
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700">
                                Save Links
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Schedule Post Modal -->
    @if($showModal && $modalType === 'schedule')
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Schedule Social Media Post</h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="schedulePost" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Platform *</label>
                            <select wire:model="post_platform" 
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                                <option value="facebook">Facebook</option>
                                <option value="instagram">Instagram</option>
                                <option value="twitter">Twitter</option>
                                <option value="linkedin">LinkedIn</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
                            <textarea wire:model="post_content" rows="4" 
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                      placeholder="Write your post content..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                            <input wire:model="post_image" type="file" accept="image/*" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Link</label>
                            <input wire:model="post_link" type="url" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                   placeholder="Optional link to include">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Schedule Date & Time *</label>
                            <input wire:model="post_scheduled_at" type="datetime-local" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" wire:click="closeModal" 
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700">
                                Schedule Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="fixed top-4 right-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif
</div>
