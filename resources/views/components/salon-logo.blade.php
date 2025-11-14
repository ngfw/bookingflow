@php
    $settings = \App\Models\SalonSetting::getDefault();
    $logoPath = $settings->logo_path ?? null;
@endphp

@if($logoPath && \Storage::disk('public')->exists($logoPath))
    <img src="{{ asset('storage/' . $logoPath) }}" 
         alt="{{ $settings->salon_name ?? 'BookingFlow' }}" 
         {{ $attributes->merge(['class' => 'h-auto object-contain']) }}>
@else
    <x-application-logo {{ $attributes }} />
@endif