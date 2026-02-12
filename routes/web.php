<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\CurriculumController;
use App\Models\ExamAttempt; // <--- THÊM DÒNG NÀY ĐỂ DÙNG TRONG TOOL DỌN DẸP

// 1. Controller chung
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChatController; // Diễn đàn chung

// 2. Controller cho Học sinh
use App\Http\Controllers\ExamController as StudentExamController; 
use App\Http\Controllers\Student\HistoryController;

// 3. Controller cho Giáo viên
use App\Http\Controllers\Teacher\QuestionController;
use App\Http\Controllers\Teacher\ExamController as TeacherExamController; 
use App\Http\Controllers\Teacher\ExamSessionController;
use App\Http\Controllers\Teacher\DocumentController;

// 4. Controller cho Admin
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ForumController as AdminForumController; // Thêm controller duyệt diễn đàn

/*
|--------------------------------------------------------------------------
| TRANG CHỦ & ĐIỀU HƯỚNG ĐĂNG NHẬP (QUAN TRỌNG)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role;

        // 1. Nếu là Admin -> Về Admin Dashboard
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        // 2. Nếu là Giáo viên -> Về Teacher Dashboard
        if ($role === 'teacher') {
            return redirect()->route('teacher.dashboard');
        }

        // 3. Còn lại (Học sinh) -> Về Student Dashboard
        return redirect()->route('dashboard');
    }
    return view('welcome'); 
});

require __DIR__.'/auth.php'; 

/*
|--------------------------------------------------------------------------
| KHU VỰC GIÁO VIÊN & ADMIN (ADMIN ĐƯỢC PHÉP TRUY CẬP)
|--------------------------------------------------------------------------
*/
Route::prefix('teacher')
    ->name('teacher.')
    ->middleware(['auth', 'role:teacher,admin']) // [QUAN TRỌNG] Cho phép cả Admin vào
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

        // 5. Quản lý tài liệu
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
        
        // Dashboard Admin
        // Nếu bạn đã có AdminDashboardController thì dùng controller, còn không thì return view như cũ
        Route::get('/dashboard', function () {
            // Giả lập số liệu thống kê nếu chưa có controller
            $stats = [
                'users' => \App\Models\User::count(),
                'teachers' => \App\Models\User::where('role', 'teacher')->count(),
                'students' => \App\Models\User::where('role', 'student')->count(),
                'exams' => \App\Models\Exam::count(),
                'sessions' => \App\Models\ExamSession::count(),
                'messages' => \App\Models\ChatMessage::count(),
            ];
            return view('admin.dashboard', compact('stats'));
        })->name('dashboard');

        // Quản lý Tài khoản
        Route::resource('users', UserController::class);

        // Quản lý Diễn đàn (Mới thêm)
        Route::get('/forum', [AdminForumController::class, 'index'])->name('forum.index');
        Route::delete('/forum/{id}', [AdminForumController::class, 'destroy'])->name('forum.destroy');
    });

/*
|--------------------------------------------------------------------------
| DIỄN ĐÀN & THẢO LUẬN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/forum', [ChatController::class, 'index'])->name('forum.index');
    Route::get('/forum/messages', [ChatController::class, 'fetchMessages'])->name('forum.fetch');
    Route::post('/forum/send', [ChatController::class, 'sendMessage'])->name('forum.send');
});

// Route sửa lỗi Avatar
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

/*
|--------------------------------------------------------------------------
| API ROUTES (Cho Dropdown 3 cấp)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->middleware(['auth'])->group(function () {
    Route::get('/topics', [CurriculumController::class, 'getTopics']);
    Route::get('/core-contents', [CurriculumController::class, 'getCoreContents']);
    Route::get('/learning-objectives', [CurriculumController::class, 'getLearningObjectives']);
});

Route::get('/student/exam/join/{code}', function ($code) {
    return "Trang tham gia thi của học sinh. Mã: " . $code;
})->name('student.exam.join');

