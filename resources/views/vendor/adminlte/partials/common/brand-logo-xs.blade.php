@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@php
    // Default dashboard URL if no specific role is found or user is not logged in
    $dashboard_base_target = config('adminlte.dashboard_url', 'home');

    // Check if a user is logged in and determine their role
    if (Auth::check()) {
        if (Auth::user()->role === 'admin') {
            $dashboard_base_target = 'admin.dashboard'; // Use the route name for admin dashboard
        } elseif (Auth::user()->role === 'guru') {
            $dashboard_base_target = 'teacher.dashboard'; // Use the route name for teacher/guru dashboard
        }
    }

    // Now, apply AdminLTE's configuration for URL generation (route() vs url())
    if (config('adminlte.use_route_url', false)) {
        // If 'use_route_url' is true, we expect a route name
        $dashboard_url = $dashboard_base_target ? route($dashboard_base_target) : '';
    } else {
        // If 'use_route_url' is false, we expect a URL path.
        // We need to convert route names (e.g., 'admin.dashboard') to paths (e.g., 'admin/dashboard')
        if (str_contains($dashboard_base_target, '.')) {
            $dashboard_base_target = str_replace('.', '/', $dashboard_base_target);
        }
        $dashboard_url = $dashboard_base_target ? url($dashboard_base_target) : '';
    }
@endphp

<a href="{{ $dashboard_url }}"
    @if($layoutHelper->isLayoutTopnavEnabled())
        class="navbar-brand {{ config('adminlte.classes_brand') }}"
    @else
        class="brand-link {{ config('adminlte.classes_brand') }}"
    @endif>

    {{-- Small brand logo --}}
<img src="{{ asset('img/smkn69jkt.jpg') }}"
            alt="{{ config('adminlte.logo_img_alt', 'AdminLTE') }}"
            class="{{ config('adminlte.logo_img_class', 'brand-image img-circle elevation-3') }}"
            style="opacity:.8">

    {{-- Brand text --}}
    <span class="brand-text font-weight-light {{ config('adminlte.classes_brand_text') }}">
        SMKN 69 Jakarta
    </span>

</a>