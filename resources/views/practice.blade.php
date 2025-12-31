<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kho đề thi mẫu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(isset($practiceExams) && $practiceExams->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($practiceExams as $exam)
                            <div class="border rounded-lg p-5 hover:shadow-lg transition bg-green-50 border-green-200">
                                <h3 class="font-bold text-lg text-green-800 mb-2 truncate">{{ $exam->title }}</h3>
                                <p class="text-sm text-gray-600">⏱ Thời gian: {{ $exam->duration }} phút</p>
                                <p class="text-sm text-gray-600 mb-4">📚 Chủ đề: {{ $exam->topic->name ?? 'Tổng hợp' }}</p>
                                
                                <a href="{{ route('exam.practice', $exam->id) }}" class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded font-bold hover:bg-green-700 transition">
                                    🚀 Luyện ngay
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 text-gray-500">
                        Chưa có đề thi mẫu nào được cập nhật.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>