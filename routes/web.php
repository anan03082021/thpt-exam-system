<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;

// 1. Controller chung
use App\Http\Controllers\DashboardController;

// 2. Controller cho Học sinh (SỬA Ở ĐÂY: Dùng Alias để trỏ đúng vào thư mục Student)
use App\Http\Controllers\ExamController as StudentExamController; 
use App\Http\Controllers\Student\HistoryController;

// 3. Controller cho Giáo viên (Admin)
use App\Http\Controllers\Teacher\QuestionController;
use App\Http\Controllers\Teacher\ExamController as TeacherExamController; 
use App\Http\Controllers\Teacher\ExamSessionController;
use App\Http\Controllers\Admin\UserController;

/*
|--------------------------------------------------------------------------
| TRANG CHỦ & AUTHENTICATION
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
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
    ->middleware(['auth', 'role:teacher'])
    ->group(function () {
        
        // 1. Dashboard Giáo viên
        Route::get('/dashboard', [DashboardController::class, 'teacherDashboard'])->name('dashboard');

        // 2. Quản lý Ngân hàng câu hỏi
        Route::resource('questions', QuestionController::class);
        Route::post('/questions/store-quick', [QuestionController::class, 'storeQuick'])->name('questions.store_quick');
        Route::post('/questions/upload-image', [App\Http\Controllers\Teacher\QuestionController::class, 'uploadImage'])
            ->name('questions.upload_image');

        // 3. Quản lý Đề thi
        Route::get('/exams', [TeacherExamController::class, 'index'])->name('exams.index'); 
        Route::get('/exams/create', [TeacherExamController::class, 'create'])->name('exams.create');
        Route::post('/exams/store', [TeacherExamController::class, 'store'])->name('exams.store');
        
        Route::get('/exams/{id}/edit', [TeacherExamController::class, 'edit'])->name('exams.edit');
        Route::put('/exams/{id}', [TeacherExamController::class, 'update'])->name('exams.update');
        Route::delete('/exams/{id}', [TeacherExamController::class, 'destroy'])->name('exams.destroy');
        Route::get('/exams/{id}/results', [TeacherExamController::class, 'results'])->name('exams.results');

        // 4. Tổ chức Kỳ thi (Sessions)
        Route::get('/sessions', [ExamSessionController::class, 'index'])->name('sessions.index');
        Route::get('/sessions/create', [ExamSessionController::class, 'create'])->name('sessions.create');
        Route::post('/sessions/store', [ExamSessionController::class, 'store'])->name('sessions.store');

        Route::get('/sessions/{id}', [ExamSessionController::class, 'show'])->name('sessions.show');
        Route::get('/sessions/{id}/edit', [ExamSessionController::class, 'edit'])->name('sessions.edit');
        Route::put('/sessions/{id}', [ExamSessionController::class, 'update'])->name('sessions.update');
        Route::get('/sessions/{id}/export', [ExamSessionController::class, 'export'])->name('sessions.export');
        Route::delete('/sessions/{id}', [ExamSessionController::class, 'destroy'])->name('sessions.destroy');

        // 5. Quản lý tài liệu
        Route::get('/documents', [App\Http\Controllers\Teacher\DocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents', [App\Http\Controllers\Teacher\DocumentController::class, 'store'])->name('documents.store');
        Route::delete('/documents/{id}', [App\Http\Controllers\Teacher\DocumentController::class, 'destroy'])->name('documents.destroy');
        Route::get('/documents/{id}/download', [App\Http\Controllers\Teacher\DocumentController::class, 'download'])->name('documents.download');
    });

/*
|--------------------------------------------------------------------------
| KHU VỰC HỌC SINH
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:student'])->group(function () {
    
    // 1. Dashboard & Các trang chính
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/practice', [DashboardController::class, 'practiceList'])->name('student.practice');
    Route::get('/documents', [DashboardController::class, 'documents'])->name('student.documents');

    // Route Tiến độ học tập
    Route::get('/history', [HistoryController::class, 'index'])->name('student.history');

    // 2. Profile cá nhân
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 3. QUY TRÌNH LÀM BÀI THI (SỬA LẠI: Dùng StudentExamController)
    Route::get('/exam/take/{sessionId}', [StudentExamController::class, 'takeExam'])->name('exam.take');
    Route::post('/exam/join/{sessionId}', [StudentExamController::class, 'joinWithPassword'])->name('exam.join_password');
    Route::post('/exam/submit/{sessionId}', [StudentExamController::class, 'submitExam'])->name('exam.submit');
    
    // Bắt đầu làm bài luyện tập
    Route::get('/practice/start/{examId}', [StudentExamController::class, 'startPractice'])->name('exam.practice');

    // --- TÁCH ROUTE XEM KẾT QUẢ ---
    
    // 3.1. Kết quả KỲ THI CHÍNH THỨC
    Route::get('/exam/result/official/{id}', [StudentExamController::class, 'showOfficialResult'])
        ->name('student.exam.result.official');

    // 3.2. Kết quả LUYỆN TẬP
    Route::get('/exam/result/practice/{id}', [StudentExamController::class, 'showResult'])
        ->name('student.exam.result.practice');
    
});

/*
|--------------------------------------------------------------------------
| KHU VỰC ADMIN (QUẢN TRỊ VIÊN)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::resource('users', UserController::class);
    });