<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lịch sử làm bài') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            
            {{-- PHẦN 1: KỲ THI CHÍNH THỨC --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                <h3 class="text-lg font-bold text-blue-800 mb-4 flex items-center">
                    <span class="bg-blue-100 p-2 rounded-full mr-2">📅</span> 
                    Kỳ thi Chính thức
                </h3>

                @if($examAttempts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase">Tên Kỳ thi / Đề thi</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase">Ngày nộp</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase">Điểm số</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($examAttempts as $attempt)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900">{{ $attempt->examSession->title ?? 'Kỳ thi đã xóa' }}</div>
                                            <div class="text-sm text-gray-500">Đề: {{ $attempt->exam->title ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($attempt->submitted_at)->format('H:i d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-sm font-bold rounded-full {{ ($attempt->total_score ?? 0) >= 5 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ isset($attempt->total_score) ? number_format($attempt->total_score, 2) : '0.00' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('exam.result', $attempt->id) }}" class="text-blue-600 hover:text-blue-900 font-bold hover:underline">Xem kết quả</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-6 text-gray-400 italic">Bạn chưa tham gia kỳ thi nào.</div>
                @endif
            </div>

            {{-- PHẦN 2: LUYỆN TẬP TỰ DO --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                <h3 class="text-lg font-bold text-yellow-800 mb-4 flex items-center">
                    <span class="bg-yellow-100 p-2 rounded-full mr-2">🎯</span> 
                    Luyện tập Tự do
                </h3>

                @if($practiceAttempts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-yellow-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-yellow-700 uppercase">Tên Đề luyện tập</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-yellow-700 uppercase">Ngày nộp</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-yellow-700 uppercase">Điểm số</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-yellow-700 uppercase">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($practiceAttempts as $attempt)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900">
                                            {{ $attempt->exam->title ?? 'Đề thi đã xóa' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($attempt->submitted_at)->format('H:i d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-sm font-bold rounded-full {{ ($attempt->total_score ?? 0) >= 5 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ isset($attempt->total_score) ? number_format($attempt->total_score, 2) : '0.00' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('exam.result', $attempt->id) }}" class="text-yellow-600 hover:text-yellow-900 font-bold hover:underline">Xem chi tiết</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-6 text-gray-400 italic">Bạn chưa làm bài luyện tập nào.</div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>