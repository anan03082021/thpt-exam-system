<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    // ==========================================
    // 1. KHU VỰC GIÁO VIÊN (QUẢN LÝ)
    // ==========================================

    /**
     * Hiển thị danh sách tài liệu quản lý
     */
    public function index(Request $request)
    {
        // 1. Lấy danh sách chủ đề để nạp vào bộ lọc (SỬA LỖI: Undefined variable $topics)
        $topics = Topic::all();

        // 2. Khởi tạo query
        $query = Document::with('topic')->latest();

        // 3. Xử lý bộ lọc (Nếu có request từ form lọc)
        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        if ($request->filled('topic_id')) {
            $query->where('topic_id', $request->topic_id);
        }

        // 4. Phân trang
        $documents = $query->paginate(10)->withQueryString();
        
        // [SỬA LẠI ĐƯỜNG DẪN VIEW]
        // Trỏ vào: resources/views/teacher/documents/index.blade.php
        return view('teacher.documents.index', compact('documents', 'topics'));
    }

    /**
     * Xử lý lưu file mới (Upload)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx', // Max 10MB
            'grade' => 'required|in:10,11,12',
            'topic_id' => 'required|exists:topics,id',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Lấy thông tin file
            $fileType = $file->getClientOriginalExtension();
            $fileSize = round($file->getSize() / 1024, 2) . ' KB';
            
            // Upload vào thư mục public/documents
            $path = $file->store('documents', 'public');

            // Lưu DB
            Document::create([
                'title' => $request->title,
                'file_path' => Storage::url($path),
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'grade' => $request->grade,
                'topic_id' => $request->topic_id,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('teacher.documents.index')->with('success', 'Đã tải lên tài liệu thành công!');
        }

        return back()->with('error', 'Lỗi khi tải file.');
    }

    /**
     * Xóa tài liệu
     */
    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        
        // Xóa file vật lý
        $relativePath = str_replace('/storage/', '', $document->file_path);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }

        $document->delete();
        return back()->with('success', 'Đã xóa tài liệu.');
    }

    // ==========================================
    // 2. KHU VỰC HỌC SINH (XEM THƯ VIỆN)
    // ==========================================

    /**
     * Hiển thị thư viện tài liệu cho học sinh
     * View: resources/views/documents.blade.php
     */
    public function library()
    {
        // 1. Lấy tất cả tài liệu
        $allDocs = Document::all();

        // 2. Nhóm tài liệu theo: Lớp -> Topic ID
        $documents = $allDocs->groupBy(['grade', 'topic_id']);

        // 3. Lấy danh sách Chủ đề
        $topics = Topic::all(); 

        // Trả về view cho học sinh
        return view('documents', compact('documents', 'topics'));
    }
}