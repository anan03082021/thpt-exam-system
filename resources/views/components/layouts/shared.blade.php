@props(['title' => 'Hệ thống thi trắc nghiệm'])

@if(Auth::user()->role === 'admin')
    {{-- Nếu là Admin thì dùng khung Admin --}}
    <x-layouts.admin :title="$title">
        {{ $slot }}
    </x-layouts.admin>
@else
    {{-- Nếu là Giáo viên thì dùng khung Giáo viên --}}
    <x-layouts.teacher :title="$title">
        {{ $slot }}
    </x-layouts.teacher>
@endif