@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@php
    $dashboard_base_url = 'dashboard'; // Default fallback path

    // Determine the base dashboard URL based on user role
    if (Auth::check()) {
        if (Auth::user()->role === 'admin') {
            $dashboard_base_url = 'admin.dashboard'; // Use route name for admin
        } elseif (Auth::user()->role === 'guru') {
            $dashboard_base_url = 'teacher.dashboard'; // Use route name for guru/teacher
        }
    }

    // Now, apply AdminLTE's config for use_route_url
    if (config('adminlte.use_route_url', false)) {
        $dashboard_url = $dashboard_base_url ? route($dashboard_base_url) : '';
    } else {
        // If not using route names, then we expect a URL path like 'admin/dashboard'
        // For consistency, let's adjust dashboard_base_url if it was a route name
        if (str_contains($dashboard_base_url, '.')) { // Check if it's a route name
            // Convert route name to URL path if use_route_url is false
            // This is a common conversion, but ensure your route names match
            // a simple path (e.g., 'admin.dashboard' -> 'admin/dashboard')
            $dashboard_base_url = str_replace('.', '/', $dashboard_base_url);
        }
        $dashboard_url = $dashboard_base_url ? url($dashboard_base_url) : '';
    }
@endphp

<a href="{{ $dashboard_url }}"
    @if($layoutHelper->isLayoutTopnavEnabled())
        class="navbar-brand logo-switch {{ config('adminlte.classes_brand') }}"
    @else
        class="brand-link logo-switch {{ config('adminlte.classes_brand') }}"
    @endif>

    {{-- Small brand logo --}}
<img src="{{ asset('img/smkn69jkt.jpg') }}"
            alt="{{ config('adminlte.logo_img_alt', 'AdminLTE') }}"
            class="{{ config('adminlte.logo_img_class', 'brand-image-xl') }} logo-xs">

    {{-- Large brand logo --}}
  <img src="{{ asset('img/smkn69jkt.jpg') }}"
            alt="{{ config('adminlte.logo_img_alt', 'AdminLTE') }}"
            class="{{ config('adminlte.logo_img_xl_class', 'brand-image-xs') }} logo-xl">

</a>