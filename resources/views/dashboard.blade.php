<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Danh sách kỳ thi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(isset($officialSessions) && $officialSessions->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($officialSessions as $session)
                            {{-- Kiểm tra trạng thái thời gian --}}
                            @php
                                $now = \Carbon\Carbon::now();
                                $isUpcoming = $now < $session->start_at; // Chưa đến giờ
                                $isOngoing = $now >= $session->start_at && $now <= $session->end_at; // Đang diễn ra
                            @endphp

                            <div class="border {{ $isOngoing ? 'border-green-400 bg-green-50' : 'border-yellow-400 bg-yellow-50' }} rounded-lg p-5 shadow-sm relative">
                                
                                {{-- Badge trạng thái --}}
                                <span class="absolute top-4 right-4 text-xs font-bold px-2 py-1 rounded text-white {{ $isOngoing ? 'bg-green-600' : 'bg-yellow-500' }}">
                                    {{ $isOngoing ? 'ĐANG DIỄN RA' : 'SẮP DIỄN RA' }}
                                </span>

                                <h3 class="font-bold text-lg text-gray-800 mb-2 pr-20">{{ $session->title }}</h3>
                                <p class="text-sm text-gray-600">📝 Môn thi: {{ $session->exam->title ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600">⏳ Thời lượng: {{ $session->exam->duration }} phút</p>
                                
                                <div class="mt-2 text-sm">
                                    <p>🕒 Bắt đầu: <strong>{{ \Carbon\Carbon::parse($session->start_at)->format('H:i d/m/Y') }}</strong></p>
                                    <p class="text-red-600">🛑 Kết thúc: {{ \Carbon\Carbon::parse($session->end_at)->format('H:i d/m/Y') }}</p>
                                </div>

                                {{-- Nút hành động --}}
                                @if($isUpcoming)
                                    <button disabled class="block w-full text-center mt-4 bg-gray-400 text-white py-2 rounded cursor-not-allowed font-bold">
                                        🔒 Chưa đến giờ thi
                                    </button>
                                @else
                                    <a href="{{ route('exam.take', $session->id) }}" class="block w-full text-center mt-4 bg-green-600 text-white py-2 rounded hover:bg-green-700 transition font-bold shadow">
                                        ✍️ VÀO THI NGAY
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 text-gray-500 border-2 border-dashed rounded-lg">
                        Hiện không có kỳ thi nào (sắp tới hoặc đang diễn ra).
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>