@props(['type' => 'text', 'mobile' => true])

@php
$inputClasses = 'block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500';
$mobileClasses = $mobile ? 'text-base min-h-[44px] px-3 py-2' : 'text-sm px-3 py-2';
@endphp

<input 
    type="{{ $type }}" 
    {{ $attributes->merge(['class' => $inputClasses . ' ' . $mobileClasses]) }}
>

