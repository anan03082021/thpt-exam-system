<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan; // Thêm dòng này để sửa lỗi Artisan ở cuối file
use App\Http\Controllers\ProfileController;

// 1. Controller chung
use App\Http\Controllers\DashboardController;

// 2. Controller cho Học sinh
use App\Http\Controllers\ExamController as StudentExamController; 
use App\Http\Controllers\Student\HistoryController;

// 3. Controller cho Giáo viên (Admin)
use App\Http\Controllers\Teacher\QuestionController;
use App\Http\Controllers\Teacher\ExamController as TeacherExamController; 
use App\Http\Controllers\Teacher\ExamSessionController;
use App\Http\Controllers\Teacher\DocumentController; // [THÊM MỚI] Import Controller Tài liệu
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
        Route::post('/questions/upload-image', [QuestionController::class, 'uploadImage'])->name('questions.upload_image');

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

        // 5. Quản lý tài liệu [CẬP NHẬT: Dùng Resource cho gọn]
        // Tự động tạo các route: index, create, store, destroy...
        Route::resource('documents', DocumentController::class); 
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
    
    // [QUAN TRỌNG] Sửa route này trỏ về DocumentController method library
    Route::get('/documents', [DocumentController::class, 'library'])->name('student.documents');

    // Route Tiến độ học tập
    Route::get('/history', [HistoryController::class, 'index'])->name('student.history');

    // 2. Profile cá nhân
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 3. QUY TRÌNH LÀM BÀI THI
    Route::get('/exam/take/{sessionId}', [StudentExamController::class, 'takeExam'])->name('exam.take');
    Route::post('/exam/join/{sessionId}', [StudentExamController::class, 'joinWithPassword'])->name('exam.join_password');
    Route::post('/exam/submit/{sessionId}', [StudentExamController::class, 'submitExam'])->name('exam.submit');
    Route::post('/exam/save-elective/{sessionId}', [App\Http\Controllers\ExamController::class, 'saveElective'])->name('exam.saveElective');
    
    // Bắt đầu làm bài luyện tập
    Route::get('/practice/start/{examId}', [StudentExamController::class, 'startPractice'])->name('exam.practice');

    // --- KẾT QUẢ ---
    Route::get('/exam/result/official/{id}', [StudentExamController::class, 'showOfficialResult'])->name('student.exam.result.official');
    Route::get('/exam/result/practice/{id}', [StudentExamController::class, 'showResult'])->name('student.exam.result.practice');
    
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

/*
|--------------------------------------------------------------------------
| DIỄN ĐÀN & THẢO LUẬN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/forum', [App\Http\Controllers\ChatController::class, 'index'])->name('forum.index');
    Route::get('/forum/messages', [App\Http\Controllers\ChatController::class, 'fetchMessages'])->name('forum.fetch');
    Route::post('/forum/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('forum.send');
});

// Route sửa lỗi Avatar (Giữ nguyên nếu bạn cần debug)
Route::get('/fix-avatar', function () {
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    $user = Auth::user();
    echo "<h1>Đang sửa lỗi hiển thị...</h1>";
    echo "✅ Đã xóa Cache.<br>";
    if ($user->avatar) {
        echo "✅ DB có ảnh: <b>" . $user->avatar . "</b><br>";
        echo "<img src='/storage/" . str_replace('public/', '', $user->avatar) . "' style='width:100px; height:100px;'>";
    } else {
        echo "❌ Chưa có ảnh.<br>";
    }
    echo "<br><a href='/dashboard'>[ QUAY VỀ ]</a>";
});