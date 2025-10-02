@props(['mobile' => true])

@php
$cardClasses = $mobile ? 'card-mobile' : 'bg-white overflow-hidden shadow-sm sm:rounded-lg';
@endphp

<div {{ $attributes->merge(['class' => $cardClasses]) }}>
    {{ $slot }}
</div>

