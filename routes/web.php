<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // Sửa thêm Auth để dùng trong closure
use App\Http\Controllers\ProfileController;

// 1. Controller chung (Điều hướng Dashboard)
use App\Http\Controllers\DashboardController;

// 2. Controller cho Học sinh
use App\Http\Controllers\ExamController; // Controller làm bài thi của HS

// 3. Controller cho Giáo viên (Admin)
use App\Http\Controllers\Teacher\QuestionController;
use App\Http\Controllers\Teacher\ExamController as TeacherExamController; // Đổi tên để không trùng với HS
use App\Http\Controllers\Teacher\ExamSessionController;

/*
|--------------------------------------------------------------------------
| TRANG CHỦ & AUTHENTICATION
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    // Nếu đã đăng nhập thì vào thẳng dashboard, chưa thì về trang login
    if (Auth::check()) {
        return Auth::user()->role === 'admin' 
            ? redirect()->route('teacher.dashboard') 
            : redirect()->route('dashboard');
    }
    return view('welcome'); 
});

require __DIR__.'/auth.php'; 

/*
|--------------------------------------------------------------------------
| KHU VỰC GIÁO VIÊN (ADMIN)
|--------------------------------------------------------------------------
*/
Route::prefix('teacher')
    ->name('teacher.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        
        // 1. Dashboard Giáo viên
        Route::get('/dashboard', [DashboardController::class, 'teacherDashboard'])->name('dashboard');

        // 2. Quản lý Ngân hàng câu hỏi
        Route::resource('questions', QuestionController::class);

        // 3. Quản lý Đề thi
        Route::get('/exams/create', [TeacherExamController::class, 'create'])->name('exams.create');
        Route::post('/exams/store', [TeacherExamController::class, 'store'])->name('exams.store');
        Route::get('/exams', [TeacherExamController::class, 'index'])->name('exams.index'); 
        Route::get('/exams/{id}/results', [TeacherExamController::class, 'results'])->name('exams.results');

        // 4. Tổ chức Kỳ thi
        Route::get('/sessions/create', [ExamSessionController::class, 'create'])->name('sessions.create');
        Route::post('/sessions/store', [ExamSessionController::class, 'store'])->name('sessions.store');
        Route::get('/sessions', [ExamSessionController::class, 'index'])->name('sessions.index'); 
    });

/*
|--------------------------------------------------------------------------
| KHU VỰC HỌC SINH
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // 1. Dashboard Học sinh
    Route::get('/dashboard', [DashboardController::class, 'studentDashboard'])->name('dashboard');
    Route::get('/practice', [DashboardController::class, 'practiceList'])->name('student.practice');
    Route::get('/history', [DashboardController::class, 'history'])->name('student.history');
    Route::get('/documents', [DashboardController::class, 'documents'])->name('student.documents');    

    // 2. Profile cá nhân
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 3. QUY TRÌNH LÀM BÀI THI
    
    // Vào làm bài (Check quyền + Mật khẩu)
    Route::get('/exam/take/{sessionId}', [ExamController::class, 'takeExam'])->name('exam.take');
    
    // [MỚI - QUAN TRỌNG] Route xử lý nhập mật khẩu (POST)
    // Route này nhận dữ liệu từ form nhập mật khẩu
    Route::post('/exam/join/{sessionId}', [ExamController::class, 'joinWithPassword'])->name('exam.join_password');
    
    // Nộp bài
    Route::post('/exam/submit/{sessionId}', [ExamController::class, 'submitExam'])->name('exam.submit');
    
    // Xem kết quả
    Route::get('/exam/result/{attemptId}', [ExamController::class, 'showResult'])->name('exam.result');

    // Làm bài luyện tập
    Route::get('/practice/{examId}', [ExamController::class, 'startPractice'])->name('exam.practice');
    
});