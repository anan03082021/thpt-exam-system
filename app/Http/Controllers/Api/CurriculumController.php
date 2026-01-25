<?php

namespace App\Http\Controllers\Api; // <--- DÒNG NÀY PHẢI CHÍNH XÁC

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\CoreContent;
use App\Models\LearningObjective;

class CurriculumController extends Controller
{
    // 1. Lấy danh sách CHỦ ĐỀ
    public function getTopics(Request $request)
    {
        try {
            $grade = $request->grade; 
            $orientation = $request->orientation; 

            // Logic: Lấy Topic có chứa CoreContent thuộc grade đó
            $topics = Topic::whereHas('coreContents', function ($query) use ($grade, $orientation) {
                $query->where('grade', $grade);
                if ($orientation && $orientation !== 'chung') {
                    $query->whereIn('orientation', ['chung', $orientation]);
                }
            })->get(['id', 'name']); 

            return response()->json($topics);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 2. Lấy NỘI DUNG CỐT LÕI
    public function getCoreContents(Request $request)
    {
        try {
            $topicId = $request->topic_id;
            $grade = $request->grade;
            $orientation = $request->orientation;

            $contents = CoreContent::where('topic_id', $topicId)
                ->where('grade', $grade)
                ->when($orientation && $orientation !== 'chung', function ($q) use ($orientation) {
                    $q->whereIn('orientation', ['chung', $orientation]);
                })
                ->get(['id', 'name']);

            return response()->json($contents);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 3. Lấy YÊU CẦU CẦN ĐẠT
    public function getLearningObjectives(Request $request)
    {
        try {
            $coreContentId = $request->core_content_id;
            $objectives = LearningObjective::where('core_content_id', $coreContentId)
                                         ->get(['id', 'content']);
            return response()->json($objectives);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}