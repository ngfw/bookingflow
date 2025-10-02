@props(['variant' => 'primary', 'size' => 'md', 'mobile' => true])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
$variantClasses = match($variant) {
    'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
    'secondary' => 'bg-gray-200 text-gray-900 hover:bg-gray-300 focus:ring-gray-500',
    'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    'warning' => 'bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500',
    'info' => 'bg-blue-500 text-white hover:bg-blue-600 focus:ring-blue-500',
    default => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500'
};
$sizeClasses = match($size) {
    'sm' => 'px-3 py-2 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
    'xl' => 'px-8 py-4 text-lg',
    default => 'px-4 py-2 text-sm'
};
$mobileClasses = $mobile ? 'min-h-[44px] min-w-[44px]' : '';
@endphp

<button {{ $attributes->merge(['class' => $baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses . ' ' . $mobileClasses]) }}>
    {{ $slot }}
</button>

