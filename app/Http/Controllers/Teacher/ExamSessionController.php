<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\ExamSessionStudent;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel; // Import thư viện Excel
use Illuminate\Support\Facades\DB;

class ExamSessionController extends Controller
{
    // 1. Hiển thị form tạo ca thi
    public function create()
    {
        // Lấy danh sách đề thi để giáo viên chọn
        $exams = Exam::orderBy('created_at', 'desc')->get();
        return view('teacher.sessions.create', compact('exams'));
    }

    // 2. Xử lý lưu
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'exam_id' => 'required',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'student_file' => 'required|mimes:xlsx,xls,csv' // Bắt buộc file Excel
        ]);

        DB::beginTransaction();
        try {
            // 1. Tạo Ca thi
            $session = ExamSession::create([
                'title' => $request->title,
                'exam_id' => $request->exam_id,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
            ]);

            // 2. Đọc file Excel
            // toArray trả về mảng dữ liệu từ file. 
            // Giả sử sheet 1 chứa dữ liệu.
            $data = Excel::toArray([], $request->file('student_file')); 
            
            if (!empty($data) && count($data[0]) > 0) {
                $rows = $data[0]; 
                
                // Bỏ qua dòng tiêu đề (nếu có). 
                // Cách đơn giản: Nếu dòng 1 chứa chữ "Email" thì bỏ qua.
                foreach ($rows as $key => $row) {
                    // Giả sử file Excel: Cột A (0) là Tên, Cột B (1) là Email
                    $name = $row[0];
                    $email = $row[1];

                    // Bỏ qua dòng tiêu đề hoặc dòng trống
                    if ($key == 0 && (strtolower($email) == 'email' || strtolower($name) == 'họ tên')) continue;
                    if (empty($email)) continue;

                    // Kiểm tra xem email này đã có tài khoản User chưa để link luôn
                    $user = User::where('email', $email)->first();

                    ExamSessionStudent::create([
                        'exam_session_id' => $session->id,
                        'student_name' => $name,
                        'student_email' => $email,
                        'user_id' => $user ? $user->id : null
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('teacher.exams.index')->with('success', 'Đã tạo kỳ thi và import danh sách học sinh!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}