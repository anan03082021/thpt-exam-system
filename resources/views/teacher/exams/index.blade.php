<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quản lý đề thi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="flex justify-between mb-4">
                    <h3 class="text-lg font-bold">Danh sách đề thi</h3>
                    <a href="{{ route('teacher.exams.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        + Tạo đề thi mới
                    </a>
                </div>

                <table class="min-w-full border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border p-2">ID</th>
                            <th class="border p-2">Tên đề thi</th>
                            <th class="border p-2">Thời lượng</th>
                            <th class="border p-2">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exams as $exam)
                            <tr>
                                <td class="border p-2 text-center">{{ $exam->id }}</td>
                                <td class="border p-2">{{ $exam->title }}</td>
                                <td class="border p-2 text-center">{{ $exam->duration }} phút</td>
                                <td class="border p-2 text-center">
                                    <a href="#" class="text-blue-600">Sửa</a> | 
                                    <a href="#" class="text-red-600">Xóa</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="mt-4">
                    {{ $exams->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>