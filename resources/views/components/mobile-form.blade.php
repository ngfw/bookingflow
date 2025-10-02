@props(['mobile' => true])

@php
$formClasses = $mobile ? 'form-mobile' : 'space-y-6';
@endphp

<form {{ $attributes->merge(['class' => $formClasses]) }}>
    {{ $slot }}
</form>

