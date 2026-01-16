<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ExamSession;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Symfony\Component\HttpFoundation\StreamedResponse; // Dùng để xuất CSV

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

    // 2. Form tạo mới (Đã có từ trước - giữ nguyên hoặc tạo đơn giản)
    public function create()
    {
        $exams = Exam::where('creator_id', Auth::id())->get();
        return view('teacher.sessions.create', compact('exams'));
    }

    public function store(Request $request)
    {
        // Logic lưu kỳ thi (Start time, End time, Password...)
        // Bạn tự bổ sung validate nhé
        ExamSession::create([
            'title' => $request->title,
            'exam_id' => $request->exam_id,
            'teacher_id' => Auth::id(),
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            // 'password' => $request->password,
        ]);
        return redirect()->route('teacher.sessions.index');
    }

    /**
     * 3. MÀN HÌNH GIÁM SÁT (MONITOR)
     * Bao gồm: Thông tin, Danh sách HS, Thống kê câu hỏi
     */
    public function show($id)
    {
        $session = ExamSession::with(['exam.questions', 'attempts.user'])->findOrFail($id);
        
        // --- LOGIC THỐNG KÊ CÂU HỎI (ĐÚNG/SAI) ---
        $questionStats = [];
        // Lấy tất cả các bài làm ĐÃ NỘP
        $attempts = $session->attempts->whereNotNull('submitted_at');
        
        foreach ($session->exam->questions as $question) {
            $correctCount = 0;
            $wrongCount = 0;
            $totalAnswered = 0;

            foreach ($attempts as $attempt) {
                // Giả sử logic lưu bài làm của bạn là JSON: ['question_id' => 'answer']
                // Hoặc bạn lưu bảng chi tiết exam_attempt_answers. 
                // Ở đây tôi giả định bạn check dựa trên điểm số (nếu có lưu điểm từng câu)
                // Demo logic đơn giản:
                // Nếu chưa có bảng chi tiết, ta tạm bỏ qua hoặc phải decode JSON bài làm.
                // Để demo, tôi set random. *Bạn cần thay bằng logic check đáp án thật của bạn*
                $totalAnswered++;
                $correctCount++; // Demo
            }
            
            // Tính tỷ lệ
            $questionStats[$question->id] = [
                'content' => $question->content,
                'total' => $attempts->count(),
                'correct' => $correctCount, // Thay bằng biến thật
                'wrong' => $attempts->count() - $correctCount,
                'ratio' => $attempts->count() > 0 ? round(($correctCount / $attempts->count()) * 100, 1) : 0
            ];
        }

        return view('teacher.sessions.show', compact('session', 'questionStats'));
    }

    // 4. Chỉnh sửa
public function edit($id)
{
    $session = ExamSession::findOrFail($id);
    // Lấy danh sách đề thi của giáo viên này để có thể chọn lại đề khác nếu muốn
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
     * Không cần cài thư viện nặng, dùng StreamedResponse của PHP thuần
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
                    $attempt->user->email, // Đã thêm Email theo yêu cầu
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
            // 1. Tìm ca thi theo ID
            $session = ExamSession::findOrFail($id);

            // 2. Kiểm tra an toàn: Nếu đã có học sinh nộp bài thi (Attempt) thì không cho xóa
            // (Giả sử bạn có quan hệ examAttempts trong model ExamSession)
            if ($session->examAttempts()->count() > 0) {
                return redirect()->route('teacher.sessions.index')
                    ->with('error', 'Không thể xóa ca thi này vì đã có học sinh làm bài.');
            }

            // 3. Xóa các dữ liệu liên quan (nếu Database không cài đặt Cascade Delete)
            // Xóa danh sách học sinh được gán vào ca thi (nếu có)
            $session->students()->delete(); 

            // 4. Xóa ca thi
            $session->delete();

            return redirect()->route('teacher.sessions.index')
                ->with('success', 'Đã xóa ca thi thành công.');

        } catch (\Exception $e) {
            return redirect()->route('teacher.sessions.index')
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}   