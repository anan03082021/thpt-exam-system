{{-- Menu 1: Dashboard --}}
<x-nav-link mode="teacher" :href="route('teacher.dashboard')" :active="request()->routeIs('teacher.dashboard')">
    {{ __('Dashboard GV') }}
</x-nav-link>

{{-- Menu 2: Ngân hàng câu hỏi --}}
<x-nav-link mode="teacher" :href="route('teacher.questions.index')" :active="request()->routeIs('teacher.questions.*')">
    {{ __('Ngân hàng câu hỏi') }}
</x-nav-link>

{{-- Menu 3: Tạo đề thi --}}
<x-nav-link mode="teacher" :href="route('teacher.exams.create')" :active="request()->routeIs('teacher.exams.*')">
    {{ __('Tạo đề thi') }}
</x-nav-link>

{{-- Menu 4: Tổ chức thi --}}
<x-nav-link mode="teacher" :href="route('teacher.sessions.create')" :active="request()->routeIs('teacher.sessions.*')">
    {{ __('Tổ chức thi') }}
</x-nav-link>