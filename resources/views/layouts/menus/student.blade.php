@props(['component' => 'nav-link'])

{{-- 1. KỲ THI --}}
<x-dynamic-component :component="$component" :href="route('dashboard')" :active="request()->routeIs('dashboard')">
    {{ __('Kỳ thi') }}
</x-dynamic-component>

{{-- 2. ĐỀ THI --}}
<x-dynamic-component :component="$component" :href="route('student.practice')" :active="request()->routeIs('student.practice')">
    {{ __('Đề thi') }}
</x-dynamic-component>

{{-- 3. LỊCH SỬ LÀM BÀI --}}
<x-dynamic-component :component="$component" :href="route('student.history')" :active="request()->routeIs('student.history')">
    {{ __('Lịch sử') }}
</x-dynamic-component>

{{-- 4. TÀI LIỆU --}}
<x-dynamic-component :component="$component" :href="route('student.documents')" :active="request()->routeIs('student.documents')">
    {{ __('Tài liệu') }}
</x-dynamic-component>