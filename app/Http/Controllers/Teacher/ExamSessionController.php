<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ExamSession;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExamSessionController extends Controller
{
    // 1. Danh sách kỳ thi
    public function index()
    {
        $sessions = ExamSession::where('teacher_id', Auth::id())
            ->with('exam')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('teacher.sessions.index', compact('sessions'));
    }

    // 2. Form tạo mới
    public function create()
    {
        $exams = Exam::where('creator_id', Auth::id())->get();
        return view('teacher.sessions.create', compact('exams'));
    }

    public function store(Request $request)
    {
        ExamSession::create([
            'title' => $request->title,
            'exam_id' => $request->exam_id,
            'teacher_id' => Auth::id(),
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'password' => $request->password, // Bỏ comment nếu dùng pass
        ]);
        return redirect()->route('teacher.sessions.index');
    }

    /**
     * 3. MÀN HÌNH GIÁM SÁT (MONITOR)
     * Đã sửa lỗi crash khi đề thi gốc bị xóa
     */
    public function show($id)
    {
        // Load session cùng với exam (nếu còn) và attempts
        $session = ExamSession::with(['exam.questions', 'attempts.user'])->findOrFail($id);
        
        // --- LOGIC THỐNG KÊ CÂU HỎI (ĐÚNG/SAI) ---
        $questionStats = [];
        
        // Lấy tất cả các bài làm ĐÃ NỘP
        $attempts = $session->attempts->whereNotNull('submitted_at');
        
        // [QUAN TRỌNG] Kiểm tra xem Đề thi gốc có còn tồn tại không
        if ($session->exam) {
            foreach ($session->exam->questions as $question) {
                $correctCount = 0;
                $wrongCount = 0;
                $totalAnswered = 0;

                foreach ($attempts as $attempt) {
                    // Logic check đáp án của bạn (đang để demo)
                    $totalAnswered++;
                    $correctCount++; // Demo: coi như đúng hết, bạn cần thay logic thật
                }
                
                // Tính tỷ lệ
                $questionStats[$question->id] = [
                    'content' => $question->content,
                    'total' => $attempts->count(),
                    'correct' => $correctCount,
                    'wrong' => $attempts->count() - $correctCount,
                    'ratio' => $attempts->count() > 0 ? round(($correctCount / $attempts->count()) * 100, 1) : 0
                ];
            }
        }
        // Nếu đề thi bị xóa, $questionStats sẽ là mảng rỗng [], trang web vẫn chạy bình thường.

        return view('teacher.sessions.show', compact('session', 'questionStats'));
    }

    // 4. Chỉnh sửa
    public function edit($id)
    {
        $session = ExamSession::findOrFail($id);
        $exams = Exam::where('creator_id', Auth::id())->orderBy('created_at', 'desc')->get();
        
        return view('teacher.sessions.edit', compact('session', 'exams'));
    }

    public function update(Request $request, $id)
    {
        $session = ExamSession::findOrFail($id);
        $session->update($request->all());
        return redirect()->route('teacher.sessions.show', $id)->with('success', 'Cập nhật thành công');
    }

    /**
     * 5. XUẤT EXCEL (CSV)
     */
    public function export($id)
    {
        $session = ExamSession::with(['attempts.user', 'exam'])->findOrFail($id);
        $fileName = 'ket_qua_thi_' . $session->id . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($session) {
            $file = fopen('php://output', 'w');
            
            // Add BOM để Excel đọc được Tiếng Việt
            fputs($file, "\xEF\xBB\xBF");

            // Header cột
            fputcsv($file, ['ID', 'Họ tên', 'Email', 'Thời gian bắt đầu', 'Thời gian nộp', 'Điểm số', 'Trạng thái']);

            // Dữ liệu
            foreach ($session->attempts as $attempt) {
                fputcsv($file, [
                    $attempt->user->id,
                    $attempt->user->name,
                    $attempt->user->email,
                    $attempt->created_at->format('H:i d/m/Y'),
                    $attempt->submitted_at ? $attempt->submitted_at->format('H:i d/m/Y') : 'Chưa nộp',
                    $attempt->total_score,
                    $attempt->submitted_at ? 'Đã xong' : 'Đang làm'
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    /**
     * Xóa (Hủy) ca thi
     */
public function destroy($id)
    {
        try {
            // 1. Tìm ca thi
            $session = ExamSession::findOrFail($id);

            // 2. [THAY ĐỔI QUAN TRỌNG] 
            // Thay vì kiểm tra và chặn, ta thực hiện XÓA HẾT BÀI LÀM liên quan trước.
            // Điều này giúp tránh lỗi khóa ngoại trong Database.
            $session->attempts()->delete(); 

            // 3. Xóa danh sách học sinh được gán (nếu có dùng bảng trung gian session_student)
            // Nếu dùng Eloquent relationship (Many-to-Many):
            // $session->students()->detach(); 
            // Hoặc nếu quan hệ 1-n:
            // $session->students()->delete();

            // 4. Cuối cùng mới xóa Ca thi
            $session->delete();

            return redirect()->route('teacher.sessions.index')
                ->with('success', 'Đã xóa ca thi và toàn bộ dữ liệu bài làm liên quan.');

        } catch (\Exception $e) {
            return redirect()->route('teacher.sessions.index')
                ->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
}