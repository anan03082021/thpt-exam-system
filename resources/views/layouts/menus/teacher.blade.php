@props(['component' => 'nav-link'])

{{-- Menu 1: Dashboard --}}
<x-dynamic-component :component="$component" :href="route('teacher.dashboard')" :active="request()->routeIs('teacher.dashboard')">
    {{ __('Dashboard GV') }}
</x-dynamic-component>

{{-- Menu 2: Ngân hàng câu hỏi --}}
<x-dynamic-component :component="$component" :href="route('teacher.questions.index')" :active="request()->routeIs('teacher.questions.*')">
    {{ __('Ngân hàng câu hỏi') }}
</x-dynamic-component>

{{-- Menu 3: Tạo đề thi --}}
<x-dynamic-component :component="$component" :href="route('teacher.exams.create')" :active="request()->routeIs('teacher.exams.*')">
    {{ __('Tạo đề thi') }}
</x-dynamic-component>

{{-- Menu 4: Tổ chức thi --}}
<x-dynamic-component :component="$component" :href="route('teacher.sessions.create')" :active="request()->routeIs('teacher.sessions.*')">
    {{ __('Tổ chức thi') }}
</x-dynamic-component>