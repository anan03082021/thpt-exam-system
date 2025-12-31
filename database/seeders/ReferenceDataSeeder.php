<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // <--- QUAN TRỌNG: Thêm dòng này
use App\Models\Competency;
use App\Models\CognitiveLevel;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tắt kiểm tra khóa ngoại
        Schema::disableForeignKeyConstraints();

        // 2. Làm rỗng bảng (Lúc này sẽ không bị lỗi nữa)
        DB::table('cognitive_levels')->truncate();
        DB::table('competencies')->truncate();

        // 3. Bật lại kiểm tra khóa ngoại
        Schema::enableForeignKeyConstraints();

        // --- BẮT ĐẦU NẠP DỮ LIỆU ---

        // 4. MỨC ĐỘ NHẬN THỨC
        $levels = [
            ['id' => 1, 'name' => 'Nhận biết'],
            ['id' => 2, 'name' => 'Thông hiểu'],
            ['id' => 3, 'name' => 'Vận dụng'],
        ];

        foreach ($levels as $level) {
            CognitiveLevel::create($level);
        }

        // 5. NĂNG LỰC TIN HỌC
$competencies = [
            [
                'id' => 1, 
                'code' => 'NLa', 
                'description' => 'Sử dụng và quản lý các phương tiện CNTT và truyền thông'
            ],
            [
                'id' => 2, 
                'code' => 'NLb', 
                'description' => 'Ứng xử phù hợp trong môi trường số'
            ],
            [
                'id' => 3, 
                'code' => 'NLc', 
                'description' => 'Giải quyết vấn đề với sự hỗ trợ của CNTT và truyền thông'
            ],
            [
                'id' => 4, 
                'code' => 'NLd', 
                'description' => 'Ứng dụng CNTT và truyền thông trong học tập và tự học'
            ],
            [
                'id' => 5, 
                'code' => 'NLe', 
                'description' => 'Hợp tác trong môi trường số'
            ],
        ];

        foreach ($competencies as $comp) {
            Competency::create($comp);
        }
    }
}