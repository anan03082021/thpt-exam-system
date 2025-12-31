<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Topic;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chuDe1 = Topic::create(['name' => 'Chủ đề A. Máy tính và xã hội tri thức']);
        $chuDe2 = Topic::create(['name' => 'Chủ đề B. Mạng máy tính và Internet']);
        $chuDe3 = Topic::create(['name' => 'Chủ đề C. Ứng dụng công nghệ thông tin']);
        $chuDe4 = Topic::create(['name' => 'Chủ đề D. Đạo đức, pháp luật và văn hóa trong môi trường số']);
        $chuDe5 = Topic::create(['name' => 'Chủ đề E. Ứng dụng tin học']);
        $chuDe6 = Topic::create(['name' => 'Chủ đề F. Giải quyết vấn đề với sự trợ giúp của máy tính']);
        $chuDe7 = Topic::create(['name' => 'Chủ đề G. Hướng nghiệp với tin học']);
    }
}
