<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\ExamSessionStudent;
use App\Models\User;
use Illuminate\Support\Facades\Auth; // Thêm Auth để lấy ID giáo viên
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExamSessionController extends Controller
{
    // 1. Hiển thị form tạo ca thi
    public function create()
    {
        // Lấy danh sách đề thi (đã public) để giáo viên chọn
        $exams = Exam::orderBy('created_at', 'desc')->get();
        return view('teacher.sessions.create', compact('exams'));
    }

    // 2. Xử lý lưu
    public function store(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'title' => 'required|string|max:255',
            'exam_id' => 'required|exists:exams,id',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'password' => 'nullable|string|max:50', // Mật khẩu là tùy chọn
            'student_file' => 'nullable|mimes:xlsx,xls,csv' // File excel là tùy chọn
        ]);

        DB::beginTransaction();
        try {
            // 2. Tạo Ca thi
            $session = ExamSession::create([
                'title' => $request->title,
                'exam_id' => $request->exam_id,
                'teacher_id' => Auth::id(), // Gán ID giáo viên tạo
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
                'password' => $request->password, // Lưu mật khẩu (nếu có)
            ]);

            // 3. Xử lý file Excel (Nếu có upload)
            if ($request->hasFile('student_file')) {
                // Đọc dữ liệu từ file
                $data = Excel::toArray([], $request->file('student_file'));

                if (!empty($data) && count($data[0]) > 0) {
                    $rows = $data[0];

                    // Mảng chứa các ID đã thêm để tránh trùng lặp trong file excel
                    $addedUserIds = [];

                    foreach ($rows as $key => $row) {
                        // Giả sử: Cột A (0) là Tên, Cột B (1) là Email
                        // Chúng ta chỉ quan tâm Cột Email để đối chiếu
                        $email = trim($row[1] ?? '');

                        // Bỏ qua dòng tiêu đề hoặc dòng trống
                        if ($key == 0 && strtolower($email) == 'email') continue;
                        if (empty($email)) continue;

                        // --- LOGIC MỚI: CHỈ THÊM NGƯỜI ĐÃ CÓ TÀI KHOẢN ---
                        
                        // Tìm User trong DB
                        $user = User::where('email', $email)->first();

                        // Nếu User tồn tại VÀ chưa được thêm vào danh sách lần này
                        if ($user && !in_array($user->id, $addedUserIds)) {
                            
                            ExamSessionStudent::create([
                                'exam_session_id' => $session->id,
                                'user_id' => $user->id,
                                'student_name' => $user->name, // Lấy tên chuẩn từ DB luôn
                                'student_email' => $user->email
                            ]);

                            $addedUserIds[] = $user->id; // Đánh dấu đã thêm
                        }
                        // Nếu không có User -> BỎ QUA (Không làm gì cả)
                    }
                }
            }

            DB::commit();
            return redirect()->route('teacher.dashboard')->with('success', 'Đã tạo kỳ thi thành công!');
        
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }
}