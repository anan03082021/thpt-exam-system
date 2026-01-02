<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🔒 Xác thực tham gia
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 text-center">
                
                <div class="mb-4 text-yellow-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-gray-900 mb-2">Kỳ thi yêu cầu mật khẩu</h3>
                <p class="text-gray-600 mb-6">
                    Kỳ thi <strong>"{{ $session->title }}"</strong> được bảo vệ.<br>
                    Vui lòng nhập mật khẩu do giáo viên cung cấp để vào thi.
                </p>

                <form action="{{ route('exam.join_password', $session->id) }}" method="POST">
                    @csrf
                    
                    <div class="mb-5">
                        <input type="text" name="password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg text-center text-2xl tracking-widest font-bold focus:ring-blue-500 focus:border-blue-500 shadow-sm" 
                               placeholder="******" required autofocus autocomplete="off">
                    </div>

                    <div class="flex justify-center gap-4">
                        <a href="{{ route('dashboard') }}" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition">
                            Quay lại
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-lg transition transform hover:-translate-y-0.5">
                            🔓 Mở khóa & Vào thi
                        </button>
                    </div>
                </form>

                @if(session('error'))
                    <div class="mt-4 p-3 bg-red-50 text-red-600 rounded-lg border border-red-100 text-sm font-bold">
                        ❌ {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>