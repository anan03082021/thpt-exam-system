<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with('topic')->where('user_id', Auth::id());

        // Lọc theo chủ đề
        if ($request->filled('topic_id')) {
            $query->where('topic_id', $request->topic_id);
        }

        $documents = $query->latest()->paginate(10);
        $topics = Topic::all();

        return view('teacher.documents.index', compact('documents', 'topics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'topic_id' => 'required|exists:topics,id',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip|max:10240', // Max 10MB
        ]);

        try {
            // 1. Upload file
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
                $size = $file->getSize(); // Bytes
                $extension = $file->getClientOriginalExtension();

                // Lưu vào thư mục public/documents
                $path = $file->storeAs('documents', time() . '_' . $originalName, 'public');

                // 2. Lưu Database
                Document::create([
                    'title' => $request->title,
                    'file_path' => $path,
                    'file_type' => $extension,
                    'file_size' => $size,
                    'topic_id' => $request->topic_id,
                    'user_id' => Auth::id(),
                ]);

                return redirect()->back()->with('success', 'Upload tài liệu thành công!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi upload: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $document = Document::where('user_id', Auth::id())->findOrFail($id);

        // Xóa file vật lý
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();
        return redirect()->back()->with('success', 'Đã xóa tài liệu.');
    }
    
    // Hàm tải xuống (Optional)
    public function download($id)
    {
        $document = Document::findOrFail($id);
        return Storage::disk('public')->download($document->file_path, $document->title . '.' . $document->file_type);
    }
}