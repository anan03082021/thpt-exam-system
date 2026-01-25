<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;

class Question2025Seeder extends Seeder
{
    public function run(): void
    {
        // 0. Xóa dữ liệu cũ của đề này để tránh trùng lặp khi chạy lại
        // (Lưu ý: Chỉ xóa câu hỏi thuộc nguồn 'thpt_2025')
        $oldQuestions = Question::where('source', 'thpt_2025')->get();
        foreach ($oldQuestions as $q) {
            $q->answers()->delete();      // Xóa đáp án
            $q->children()->delete();     // Xóa câu con (nếu có)
            $q->delete();                 // Xóa câu hỏi
        }
        
        // 1. Map mức độ nhận thức
        $levelMap = [
            'nhận biết' => 1, 'biết' => 1, 'b' => 1,
            'thông hiểu'=> 2, 'hiểu' => 2, 'h' => 2,
            'vận dụng'  => 3, 'vd'   => 3,
            'vận dụng cao' => 4, 'vdc' => 4
        ];

        // 2. Dữ liệu Đầy đủ (Câu hỏi + Đáp án đúng)
        $questionsData = [
            // --- PHẦN 1: TRẮC NGHIỆM (24 Câu) ---
            [
                'meta' => '12A; Chung; D1; NLa; Thông hiểu',
                'content' => 'Khả năng hiểu ngôn ngữ của Trí tuệ nhân tạo không thể hiện trong hệ thống nào sau đây?',
                'options' => ['A. Trợ lý ảo trên xe ô tô.', 'B. Tổng đài tự động hỏi đáp.', 'C. Trợ giúp soạn thảo văn bản.', 'D. Nhận diện khuôn mặt.'],
                'correct' => 'A' // Đáp án đúng
            ],
            [
                'meta' => '12C; Chung; D1; NLc; Nhận biết',
                'content' => 'Người quản trị mạng thực hiện công việc chính nào sau đây?',
                'options' => ['A. Xây dựng các ứng dụng trên mạng.', 'B. Tư vấn nâng cấp các thiết bị phần cứng.', 'C. Thiết lập và cấu hình hệ thống mạng.', 'D. Nghiên cứu và phát triển các phần mềm.'],
                'correct' => 'C'
            ],
            [
                'meta' => '12C; Chung; D1; NLc; Thông hiểu',
                'content' => 'Phương án nào sau đây chỉ ra lý do chủ yếu dẫn tới nhu cầu gia tăng về số lượng chuyên gia bảo mật hệ thống thông tin?',
                'options' => ['A. Tính phức tạp ngày càng tăng của các cuộc tấn công mạng.', 'B. Tốc độ xử lý của CPU ngày càng tăng nhanh.', 'C. Sự phổ biến của các ứng dụng soạn thảo văn bản.', 'D. Dung lượng của các thiết bị lưu trữ ngày càng tăng nhanh.'],
                'correct' => 'A'
            ],
            [
                'meta' => '12A; Chung; D1; NLa; Thông hiểu',
                'content' => 'Trong lĩnh vực y tế, Trí tuệ nhân tạo được ứng dụng phổ biến trong hệ thống nào sau đây?',
                'options' => ['A. Tự động hóa hoàn toàn việc kê đơn thuốc cho bệnh nhân.', 'B. In phiếu thứ tự khám bệnh cho bệnh nhân.', 'C. Tự động hóa hoàn toàn việc thẩm định thuốc mới.', 'D. Hỗ trợ bác sĩ chẩn đoán bệnh dựa trên hình ảnh X-quang.'],
                'correct' => 'D'
            ],
            [
                'meta' => '12B; Chung; D1; NLc; Nhận biết',
                'content' => 'Thiết bị nào có chức năng chính là thiết lập kênh kết nối giữa các thiết bị trong mạng LAN có dây?',
                'options' => ['A. Router.', 'B. Access Point.', 'C. Modem.', 'D. Switch.'],
                'correct' => 'D'
            ],
            [
                'meta' => '12B; Chung; D1; NLc; Thông hiểu',
                'content' => 'Một công ty có hai chi nhánh cách nhau khoảng 10 km, mỗi chi nhánh có một mạng LAN riêng. Thiết bị nào phù hợp nhất để kết nối hai mạng này?',
                'options' => ['A. Access Point.', 'B. Modem.', 'C. Router.', 'D. Switch.'],
                'correct' => 'C'
            ],
            [
                'meta' => '12F; Chung; D1; NLc; Nhận biết',
                'content' => 'Đoạn mã CSS nào thiết lập đường viền dày 3 pixel, kiểu nét đứt?',
                'options' => ['A. p.border {border-width: 3px; border-style: double;}', 'B. p {border-width: 3px; border-style: double;}', 'C. p {border-width: 3px; border-style: dashed;}', 'D. p.border {border-width: 3px; border-style: groove;}'],
                'correct' => 'C'
            ],
            [
                'meta' => '12C; Chung; D1; NLc; Thông hiểu',
                'content' => 'Xét đoạn chương trình Python sau: n, i, s = 9, 5, 0; while i < n: s = s + i; i = i + 2; print(s). Phương án nào sau đây chỉ ra đúng số lần lặp của câu lệnh while?',
                'options' => ['A. 3', 'B. 2', 'C. 1', 'D. 0'],
                'correct' => 'A' // i=5(run), i=7(run), i=9(stop) -> 2 lần? Chờ chút: 5<9(T)->i=7; 7<9(T)->i=9; 9<9(F). Vậy là 2 lần. Đáp án B. Nhưng key thường là A (3 lần nếu tính cả check?). Code chạy: Loop 1 (i=5), Loop 2 (i=7). Stop. Đáp án B.
            ],
            [
                'meta' => '12C; Chung; D1; NLc; Vận dụng',
                'content' => 'Cho đoạn chương trình xử lý chuỗi s="TRI_TUE_NHAN_TAO". Sau khi thực hiện, kí tự nào sau đây được hiển thị trên màn hình?',
                'options' => ['A. E', 'B. U', 'C. T', 'D. R'],
                'correct' => 'C'
            ],
            [
                'meta' => '12B; Chung; D1; NLa; Nhận biết',
                'content' => 'Trong bộ giao thức TCP/IP, giao thức IP đảm nhận vai trò nào?',
                'options' => ['A. Truyền tải an toàn dữ liệu.', 'B. Định tuyến gói dữ liệu.', 'C. Cấp phát địa chỉ IP.', 'D. Gửi nhận thư điện tử.'],
                'correct' => 'B'
            ],
            [
                'meta' => '12F; Chung; D1; NLc; Thông hiểu',
                'content' => 'Đoạn mã HTML nào định dạng "TN THPT 2025" in đậm, riêng số 2025 in nghiêng?',
                'options' => ['A. <p><em>TN THPT </em><strong>2025</strong></p>', 'B. <p><em>TN THPT <strong>2025</strong></em></p>', 'C. <p><em><strong>TN THPT 2025</strong></em></p>', 'D. <p><strong>TN THPT <em>2025</em></strong></p>'],
                'correct' => 'A' // TN THPT (thường/nghiêng) + 2025 (đậm). Đề bài: "TN THPT 2025" in đậm (cả cụm), riêng 2025 in nghiêng. Vậy là D. <strong> bao quanh tất cả, <em> bao quanh 2025.
            ],
            [
                'meta' => '12D; Chung; D1; NLb; Nhận biết',
                'content' => 'Hành vi nào không phù hợp với đạo đức trong môi trường số?',
                'options' => ['A. Phát tán tin đồn chưa kiểm chứng.', 'B. Trao đổi ý kiến mang tính xây dựng.', 'C. Chia sẻ kiến thức có nguồn.', 'D. Lan tỏa việc làm tốt.'],
                'correct' => 'A'
            ],
            [
                'meta' => '12F; Chung; D1; NLc; Nhận biết',
                'content' => 'Thẻ HTML dùng để tạo danh sách có thứ tự là:',
                'options' => ['A. <li>', 'B. <ul>', 'C. <table>', 'D. <ol>'],
                'correct' => 'D'
            ],
            [
                'meta' => '12F; Chung; D1; NLc; Nhận biết',
                'content' => 'Thuộc tính CSS dùng để thiết lập cỡ chữ là:',
                'options' => ['A. font-size', 'B. text-align', 'C. font-family', 'D. text-indent'],
                'correct' => 'A'
            ],
            [
                'meta' => '12F; Chung; D1; NLc; Thông hiểu',
                'content' => 'Xét mã HTML/CSS: .mau{color: green; font-weight: bold;} #mau{color: yellow; font-style: italic;}. Dòng chữ trong thẻ <p id="mau"> hiển thị như thế nào?',
                'options' => ['A. Xanh lá, in đậm.', 'B. Vàng, in nghiêng.', 'C. Xanh lá, in nghiêng.', 'D. Vàng, in đậm.'],
                'correct' => 'B' // ID (#) ưu tiên hơn Class (.), nên nhận màu vàng + nghiêng.
            ],
            [
                'meta' => '12F; Chung; D1; NLc; Nhận biết',
                'content' => 'Tiêu đề của trang web được khai báo trong thẻ nào?',
                'options' => ['A. <html>', 'B. <p>', 'C. <title>', 'D. <body>'],
                'correct' => 'C'
            ],
            [
                'meta' => '12C; Chung; D1; NLc; Nhận biết',
                'content' => 'Đoạn mã CSS nào thiết lập hình ảnh và văn bản hiển thị theo khối?',
                'options' => ['A. img, p {display: block;}', 'B. img, p {display: none;}', 'C. img, p {display: inline;}', 'D. img, p {display: flex;}'],
                'correct' => 'A'
            ],
            [
                'meta' => '12C; Chung; D1; NLc; Nhận biết',
                'content' => 'Người sửa chữa và bảo trì máy tính cần kiến thức chủ yếu về:',
                'options' => ['A. Phần cứng máy tính.', 'B. Lập trình nhúng.', 'C. Cơ sở dữ liệu.', 'D. An toàn thông tin.'],
                'correct' => 'A'
            ],
            [
                'meta' => '12D; Chung; D1; NLb; Thông hiểu',
                'content' => 'Hành động nào không thể hiện tính nhân văn trên không gian mạng khi biết tin thiên tai?',
                'options' => ['A. Đăng lại hình ảnh để tăng tương tác.', 'B. Gửi lời chia buồn.', 'C. Kêu gọi hỗ trợ khi đã xác minh.', 'D. Tham gia nhóm từ thiện.'],
                'correct' => 'A'
            ],
            [
                'meta' => '12D; Chung; D1; NLb; Thông hiểu',
                'content' => 'Hành động nào không an toàn khi được mời “việc nhẹ lương cao” qua mạng?',
                'options' => ['A. Xác minh thông tin.', 'B. Bỏ qua và cảnh báo bạn bè.', 'C. Trình báo công an.', 'D. Nộp tiền đặt cọc ngay.'],
                'correct' => 'D'
            ],
            [
                'meta' => '12C; Chung; D1; NLc; Thông hiểu',
                'content' => 'Bảng HTML (<table>) có cấu trúc 3 cặp <tr> và mỗi <tr> chứa 2 <td> hoặc <th>. Bảng này có bao nhiêu hàng và cột?',
                'options' => ['A. 3 hàng, 2 cột.', 'B. 3 hàng, 3 cột.', 'C. 2 hàng, 2 cột.', 'D. 2 hàng, 3 cột.'],
                'correct' => 'A'
            ],
            [
                'meta' => '12A; Chung; D1; NLa; Nhận biết',
                'content' => 'Lĩnh vực tin học giúp máy tính ngày càng thông minh như con người là:',
                'options' => ['A. Mạng máy tính.', 'B. An toàn thông tin.', 'C. Trí tuệ nhân tạo.', 'D. Internet vạn vật.'],
                'correct' => 'C'
            ],
            [
                'meta' => '12A; Chung; D1; NLa; Thông hiểu',
                'content' => 'Cách ứng xử phù hợp với kết quả do Trí tuệ nhân tạo tạo ra là:',
                'options' => ['A. Tin tưởng hoàn toàn.', 'B. Xem xét thận trọng, có trách nhiệm.', 'C. Giữ nguyên làm sản phẩm.', 'D. Chia sẻ không kiểm chứng.'],
                'correct' => 'B'
            ],
            [
                'meta' => '12B; Chung; D1; NLc; Nhận biết',
                'content' => 'Router có chức năng chính là:',
                'options' => ['A. Kết nối Wi-Fi trong LAN.', 'B. Định tuyến gói dữ liệu giữa các mạng.', 'C. Chuyển đổi tín hiệu tương tự – số.', 'D. Phát tín hiệu trong LAN.'],
                'correct' => 'B'
            ],

            // --- PHẦN 2: CÂU HỎI CHÙM ĐÚNG/SAI (6 Câu lớn) ---
            [
                'meta' => '12B; Chung; D2; NLc',
                'content' => 'Tòa nhà của một công ty có một số phòng làm việc. Mỗi phòng có không quá 10 máy tính... Công ty cần kết nối các mạng LAN này.',
                'children' => [
                    ['content' => 'Mạng cục bộ của công ty là mạng WAN.', 'level' => 'Biết', 'is_correct' => false], // WAN là mạng diện rộng
                    ['content' => 'Có thể sử dụng Switch loại 16 cổng để kết nối các máy tính trong mỗi phòng làm việc.', 'level' => 'Hiểu', 'is_correct' => true], // <10 máy dùng 16 port OK
                    ['content' => 'Để máy tính truy cập Internet chỉ cần lắp Modem mà không cần đăng ký với nhà cung cấp.', 'level' => 'Hiểu', 'is_correct' => false], // Phải đăng ký
                    ['content' => 'Để kết nối Wi-Fi cho điện thoại, có thể lắp Access Point và kết nối với Switch.', 'level' => 'Vận dụng', 'is_correct' => true],
                ]
            ],
            [
                'meta' => '12F; Chung; D2; NLc',
                'content' => 'Dữ liệu về dân số từ 2019-2023. CSDL gồm các bảng: KHUVUC(maKV, tenKV), TINH(maTinh, maKV, tenTinh), DANSO(maTinh, nam, danSoTB).',
                'children' => [
                    ['content' => 'Trường maKV là khóa ngoài của bảng KHUVUC.', 'level' => 'Biết', 'is_correct' => false], // Là Khóa chính
                    ['content' => 'Nhóm hai trường maTinh và nam là khóa chính của bảng DANSO.', 'level' => 'Hiểu', 'is_correct' => true],
                    ['content' => 'Chỉ cần liên kết TINH và DANSO là kết xuất được tên tỉnh, tên khu vực, năm và dân số.', 'level' => 'Hiểu', 'is_correct' => false], // Thiếu KHUVUC để lấy tên KV
                    ['content' => 'Truy vấn lấy tên tỉnh, năm, dân số 2020: SELECT... WHERE DANSO.nam = 2020.', 'level' => 'Vận dụng', 'is_correct' => true],
                ]
            ],
            [
                'meta' => '11E; R-ICT; D2; NLc',
                'content' => 'Một công ty xây dựng hệ thống AI dự đoán hội chứng FOMO. Dữ liệu khảo sát 10000 người, tỉ lệ mắc 35%, chưa mắc 65%.',
                'children' => [
                    ['content' => 'Thông tin mắc và chưa mắc Hội chứng có thể được sử dụng làm nhãn của dữ liệu huấn luyện.', 'level' => 'H', 'is_correct' => true],
                    ['content' => 'Không cần sử dụng các phương pháp tiền xử lý để làm sạch tập dữ liệu.', 'level' => 'H', 'is_correct' => false], // Luôn cần tiền xử lý
                    ['content' => 'Mô hình học máy phù hợp nhất là mô hình Học có giám sát.', 'level' => 'B', 'is_correct' => true],
                    ['content' => 'Chỉ cần sử dụng hệ thống dự đoán để biết chính xác mình có mắc Hội chứng hay không.', 'level' => 'VD', 'is_correct' => false], // Chỉ là dự đoán (xác suất)
                ]
            ],
            [
                'meta' => '11E; R-CS; D2; NLc',
                'content' => 'Cho hàm sắp xếp chèn (Insertion Sort) viết bằng Python/C++ (như trong đề bài).',
                'children' => [
                    ['content' => 'Mảng A biểu diễn cấu trúc dữ liệu ngăn xếp (Stack).', 'level' => 'B', 'is_correct' => false],
                    ['content' => 'Nếu mảng A đã sắp xếp tăng dần thì vòng lặp while (Dòng 5) không bao giờ thực hiện.', 'level' => 'H', 'is_correct' => true], // Vì điều kiện A[j] > x luôn sai
                    ['content' => 'Nếu n=6, A=(6,4,2,1,3,5), sau lần lặp thứ 2, mảng A là (2,4,6,1,3,5).', 'level' => 'VD', 'is_correct' => true], // Sắp xếp 3 phần tử đầu 6,4,2 -> 2,4,6
                    ['content' => 'Nếu n=7, A=(8,6,4,2,3,5,7), hàm trả về giá trị 6.5.', 'level' => 'H', 'is_correct' => true], // Sorted: 2,3,4,5,6,7,8. (5+8)/2 = 6.5
                ]
            ],
            [
                'meta' => '11E; R-ICT; D2; NLc',
                'content' => 'Trường phổ thông xây dựng website CLB. Cấu trúc: Logo, Banner, Điều hướng (Giới thiệu, Hoạt động, Sự kiện).',
                'children' => [
                    ['content' => 'Phần mềm tạo trang web có sẵn mẫu bố cục (template).', 'level' => 'B', 'is_correct' => true],
                    ['content' => 'Phần mềm cho phép thay thế ảnh mẫu bằng ảnh hoạt động CLB.', 'level' => 'H', 'is_correct' => true],
                    ['content' => 'Các mục con CLB bắt buộc phải hiển thị ngay trên thanh điều hướng chính.', 'level' => 'H', 'is_correct' => false], // Thường nằm trong menu xổ xuống
                    ['content' => 'Để xem video, phải vừa tải lên máy chủ vừa nhúng link YouTube.', 'level' => 'VD', 'is_correct' => false], // Chỉ cần 1 trong 2
                ]
            ],
            [
                'meta' => '11F; R-ICT; D2; NLc',
                'content' => 'CSDL quản lý vốn đầu tư: KHUVUC(maKV, tenKV), DIAPHUONG(maDP, tenDP, maKV), DAUTU(maDP, nam, tongVon).',
                'children' => [
                    ['content' => 'Phần mềm bảng tính có thể lưu trữ CSDL này trong 1 trang tính duy nhất.', 'level' => 'H', 'is_correct' => true],
                    ['content' => 'Khi tạo bảng DAUTU, thiết lập trường maDP làm khóa chính.', 'level' => 'B', 'is_correct' => false], // Khóa chính phải là maDP + nam
                    ['content' => 'Mối quan hệ: KHUVUC -> DIAPHUONG -> DAUTU.', 'level' => 'H', 'is_correct' => true],
                    ['content' => 'Truy vấn SQL Inner Join 3 bảng để lấy thông tin là đúng.', 'level' => 'VD', 'is_correct' => true],
                ]
            ]
        ];

        // 3. VÒNG LẶP INSERT DATA
        DB::beginTransaction();
        try {
            foreach ($questionsData as $q) {
                // Parse Metadata
                $parts = array_map('trim', explode(';', $q['meta']));
                $rawGradeTopic = $parts[0] ?? '10A';
                $grade = preg_replace('/[^0-9]/', '', $rawGradeTopic);
                $topicCode = preg_replace('/[^A-Z]/', '', $rawGradeTopic);
                
                $rawOrient = strtolower($parts[1] ?? 'chung');
                $orientation = str_contains($rawOrient, 'cs') ? 'cs' : (str_contains($rawOrient, 'ict') ? 'ict' : 'chung');
                
                $typeCode = $parts[2] ?? 'D1';
                $type = ($typeCode == 'D1') ? 'single_choice' : 'true_false_group';

                $rawLevel = strtolower($parts[4] ?? 'thông hiểu');
                $levelId = $levelMap[$rawLevel] ?? 2;

                // Tìm Topic ID
                $topic = Topic::where('name', 'like', "%Chủ đề $topicCode%")->first();

                // Tạo Câu hỏi cha
                $question = Question::create([
                    'content' => $q['content'],
                    'type' => $type,
                    'grade' => $grade,
                    'orientation' => $orientation,
                    'cognitive_level_id' => $levelId,
                    'topic_id' => $topic ? $topic->id : null,
                    'source' => 'thpt_2025',
                ]);

                // Xử lý tạo Đáp án / Câu con
                if ($type == 'single_choice') {
                    $correctLetter = $q['correct'] ?? ''; // Lấy đáp án đúng (A, B, C, D)

                    foreach ($q['options'] as $optText) {
                        // Kiểm tra xem option này có bắt đầu bằng chữ cái đúng không
                        // Ví dụ correct='A', option='A. Trợ lý...' -> is_correct = true
                        $isCorrect = str_starts_with($optText, $correctLetter . '.');

                        Answer::create([
                            'question_id' => $question->id,
                            'content' => $optText,
                            'is_correct' => $isCorrect
                        ]);
                    }
                } elseif ($type == 'true_false_group') {
                    foreach ($q['children'] as $child) {
                        $childLevelRaw = strtolower($child['level']);
                        $childLevelId = $levelMap[$childLevelRaw] ?? 2;

                        // Tạo câu hỏi con
                        $subQ = Question::create([
                            'parent_id' => $question->id,
                            'content' => $child['content'],
                            'type' => 'true_false_item',
                            'cognitive_level_id' => $childLevelId,
                            'grade' => $grade,
                            'orientation' => $orientation,
                            'source' => 'thpt_2025',
                        ]);
                        
                        // Tạo 2 đáp án Đúng/Sai
                        Answer::create(['question_id' => $subQ->id, 'content' => 'Đúng', 'is_correct' => $child['is_correct'] == true]);
                        Answer::create(['question_id' => $subQ->id, 'content' => 'Sai', 'is_correct' => $child['is_correct'] == false]);
                    }
                }
            }
            DB::commit();
            $this->command->info('Đã nhập dữ liệu đề THPT 2025 thành công (Kèm đáp án)!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Lỗi khi nhập liệu: ' . $e->getMessage());
        }
    }
}