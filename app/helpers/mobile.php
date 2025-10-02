<?php

if (!function_exists('isMobile')) {
    /**
     * Check if the current request is from a mobile device
     */
    function isMobile(): bool
    {
        return request()->attributes->get('isMobile', false);
    }
}

if (!function_exists('mobileClass')) {
    /**
     * Return mobile-specific CSS classes
     */
    function mobileClass(string $mobileClass, string $desktopClass = ''): string
    {
        return isMobile() ? $mobileClass : $desktopClass;
    }
}

if (!function_exists('mobileBreakpoint')) {
    /**
     * Return responsive CSS classes for mobile/desktop
     */
    function mobileBreakpoint(string $mobileClass, string $desktopClass = ''): string
    {
        if (empty($desktopClass)) {
            return $mobileClass;
        }
        
        return $mobileClass . ' ' . $desktopClass;
    }
}

