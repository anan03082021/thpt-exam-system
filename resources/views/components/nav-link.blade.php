@props(['active', 'mode' => 'student']) 
{{-- 'mode' mặc định là 'student' (Tailwind), nếu là giáo viên thì truyền 'teacher' --}}

@php
    // 1. CẤU HÌNH CSS CHO GIÁO VIÊN (BOOTSTRAP 5)
    if ($mode === 'teacher') {
        $classes = ($active ?? false)
                    ? 'nav-link active fw-bold text-warning' // Active: Màu vàng, đậm
                    : 'nav-link text-white';                 // Inactive: Màu trắng
    } 
    // 2. CẤU HÌNH CSS CHO HỌC SINH (TAILWIND / BREEZE)
    else {
        $classes = ($active ?? false)
                    ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
                    : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
    }
@endphp

@if($mode === 'teacher')
    {{-- Layout Giáo viên (Bootstrap) cần thẻ LI bao bọc thẻ A --}}
    <li class="nav-item">
        <a {{ $attributes->merge(['class' => $classes]) }}>
            {{ $slot }}
        </a>
    </li>
@else
    {{-- Layout Học sinh (Tailwind) dùng thẻ A trực tiếp --}}
    <a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@endif