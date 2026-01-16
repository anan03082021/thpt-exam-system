<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Topic;
use App\Models\CoreContent;
use App\Models\LearningObjective;
use Illuminate\Support\Facades\DB;

class CurriculumSeeder extends Seeder
{
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại để làm sạch dữ liệu cũ (nếu muốn)
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // LearningObjective::truncate();
        // CoreContent::truncate();
        // Topic::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $curriculum = [
            // ====================================================
            // LỚP 10
            // ====================================================
            10 => [
                'Chủ đề A. Máy tính và xã hội tri thức' => [
                    [
                        'core' => 'Dữ liệu, thông tin và xử lí thông tin',
                        'orientation' => 'chung',
                        'yccd' => [
                            'Phân biệt được thông tin và dữ liệu, nêu được ví dụ minh hoạ.',
                            'Chuyển đổi được giữa các đơn vị lưu trữ thông tin như B, KB, MB,...',
                            'Nêu được sự ưu việt của việc lưu trữ, xử lí và truyền thông tin bằng thiết bị số.'
                        ]
                    ],
                    [
                        'core' => 'Vai trò của máy tính và các thiết bị thông minh',
                        'orientation' => 'chung',
                        'yccd' => [
                            'Trình bày được những đóng góp cơ bản của tin học đối với xã hội.',
                            'Nêu được ví dụ cụ thể về thiết bị thông minh. Giải thích vai trò đối với CMCN 4.0.',
                            'Nhận biết được một vài thiết bị số thông dụng khác ngoài máy tính.',
                            'Giới thiệu được các thành tựu nổi bật ở một số mốc thời gian minh hoạ sự phát triển.'
                        ]
                    ],
                    [
                        'core' => 'Kĩ năng sử dụng thiết bị số',
                        'orientation' => 'chung',
                        'yccd' => [
                            'Khởi động được thiết bị số, sử dụng được các tệp dữ liệu và phần mềm cơ bản.'
                        ]
                    ],
                    [
                        'core' => 'Biểu diễn thông tin',
                        'orientation' => 'cs',
                        'yccd' => [
                            'Thực hiện được các phép tính cơ bản AND, OR, NOT, giải thích ứng dụng hệ nhị phân.',
                            'Giải thích được sơ lược việc số hoá văn bản, hình ảnh và âm thanh.',
                            'Giải thích được sơ lược về chức năng của bảng mã chuẩn quốc tế (Unicode).'
                        ]
                    ]
                ],
                'Chủ đề B. Mạng máy tính và Internet' => [
                    [
                        'core' => 'Khái niệm mạng máy tính, Internet, IoT',
                        'orientation' => 'chung',
                        'yccd' => [
                            'Trình bày được thay đổi chất lượng cuộc sống, học tập trong xã hội có mạng máy tính.',
                            'So sánh được mạng LAN và Internet.',
                            'Nêu được một số dịch vụ cụ thể mà Điện toán đám mây cung cấp.',
                            'Nêu được khái niệm Internet vạn vật (IoT) và ví dụ cụ thể.'
                        ]
                    ],
                    [
                        'core' => 'Sử dụng dịch vụ web. Tự bảo vệ khi tham gia mạng',
                        'orientation' => 'chung',
                        'yccd' => [
                            'Sử dụng được chức năng xử lí thông tin trên thiết bị số (dịch tự động, giọng nói).',
                            'Khai thác được nguồn học liệu mở trên Internet.',
                            'Nêu được nguy cơ, tác hại và cách phòng tránh khi tham gia Internet.',
                            'Nêu được cách phòng vệ khi bị bắt nạt trên mạng và bảo vệ dữ liệu cá nhân.',
                            'Trình bày sơ lược về phần mềm độc hại và cách phòng chống.'
                        ]
                    ]
                ],
                'Chủ đề D. Đạo đức, pháp luật và văn hóa' => [
                    [
                        'core' => 'Nghĩa vụ tuân thủ pháp lí trong môi trường số',
                        'orientation' => 'chung',
                        'yccd' => [
                            'Nêu được vấn đề nảy sinh về pháp luật, đạo đức, văn hóa khi giao tiếp qua mạng.',
                            'Nêu ví dụ minh họa sự vi phạm bản quyền thông tin và hậu quả.',
                            'Trình bày nội dung cơ bản của Luật CNTT, Luật An ninh mạng.',
                            'Giải thích khía cạnh pháp lí của bản quyền, sở hữu và trao đổi thông tin.',
                            'Vận dụng Luật để xác định tính hợp pháp của hành vi trong lĩnh vực CNTT.',
                            'Nêu biện pháp nâng cao tính an toàn và hợp pháp khi chia sẻ thông tin.'
                        ]
                    ]
                ],
                'Chủ đề F. Giải quyết vấn đề với máy tính' => [
                    [
                        'core' => 'Lập trình cơ bản',
                        'orientation' => 'cs',
                        'yccd' => [
                            'Viết và thực hiện được chương trình có sử dụng biến, cấu trúc điều khiển, mảng.',
                            'Phát triển năng lực giải quyết vấn đề và sáng tạo.'
                        ]
                    ],
                    [
                        'core' => 'Chương trình con',
                        'orientation' => 'cs',
                        'yccd' => [
                            'Viết được chương trình có sử dụng chương trình con thư viện chuẩn.',
                            'Viết được chương trình con biểu diễn thuật toán đơn giản.'
                        ]
                    ],
                    [
                        'core' => 'Giải quyết bài toán bằng lập trình',
                        'orientation' => 'cs',
                        'yccd' => [
                            'Đọc hiểu, kiểm thử và gỡ lỗi được chương trình đơn giản.',
                            'Viết chương trình giải quyết bài toán đơn giản có vận dụng kiến thức liên môn.'
                        ]
                    ]
                ]
            ],

            // ====================================================
            // LỚP 11 (Cập nhật đầy đủ từ trang 3,4,5)
            // ====================================================
            11 => [
                'Chủ đề A. Máy tính và xã hội tri thức' => [
                    [
                        'core' => 'Hệ điều hành và phần mềm ứng dụng',
                        'orientation' => 'chung',
                        'yccd' => [
                            'Trình bày sơ lược lịch sử phát triển HĐH thương mại và nguồn mở cho PC.',
                            'Sử dụng được chức năng cơ bản của một trong hai loại HĐH đó.',
                            'Trình bày được nét chính về HĐH cho thiết bị di động.',
                            'Trình bày mối quan hệ giữa phần cứng, HĐH và phần mềm ứng dụng.'
                        ]
                    ],
                    [
                        'core' => 'Phần mềm nguồn mở',
                        'orientation' => 'chung',
                        'yccd' => [
                            'Trình bày khái niệm phần mềm nguồn mở, giấy phép công cộng.',
                            'So sánh phần mềm nguồn mở và phần mềm thương mại.',
                            'Sử dụng được các tiện ích có sẵn của HĐH để nâng cao hiệu suất.',
                            'Sử dụng được phần mềm văn phòng chạy trên Internet (Google Docs).'
                        ]
                    ],
                    [
                        'core' => 'Bên trong máy tính',
                        'orientation' => 'chung',
                        'yccd' => [
                            'Nhận diện các bộ phận chính: CPU, RAM, thiết bị lưu trữ.',
                            'Giải thích đơn vị đo GB, TB. Nhận biết mạch logic AND, OR, NOT.',
                        ]
                    ],
                    [
                        'core' => 'Thế giới thiết bị số',
                        'orientation' => 'chung',
                        'yccd' => [
                            'Tuỳ chỉnh chức năng cơ bản của máy tính phù hợp nhu cầu.',
                            'Đọc hiểu tài liệu hướng dẫn thiết bị số thông dụng.',
                            'Đọc hiểu thông số kỹ thuật (CPU, RAM, độ phân giải) của thiết bị số.',
                            'Biết cách kết nối PC với các thiết bị số thông dụng.'
                        ]
                    ]
                ],
                'Chủ đề C. Tổ chức lưu trữ, tìm kiếm thông tin' => [
                    [
                        'core' => 'Tìm kiếm và trao đổi thông tin trên mạng',
                        'orientation' => 'chung',
                        'yccd' => [
                            'Sử dụng công cụ lưu trữ trực tuyến (Google Drive, Dropbox).',
                            'Sử dụng máy tìm kiếm nâng cao để khai thác thông tin hiệu quả.',
                            'Sử dụng chức năng nâng cao của mạng xã hội và phân loại email.'
                        ]
                    ]
                ],
                'Chủ đề D. Đạo đức, pháp luật và văn hóa' => [
                    [
                        'core' => 'Ứng xử văn hoá và an toàn trên mạng',
                        'orientation' => 'chung',
                        'yccd' => [
                            'Nêu được các dạng lừa đảo phổ biến trên mạng và cách phòng tránh.',
                            'Giao tiếp trên mạng văn minh, phù hợp quy tắc ứng xử.'
                        ]
                    ]
                ],
                'Chủ đề F. Giải quyết vấn đề với máy tính' => [
                    [
                        'core' => 'Giới thiệu các hệ Cơ sở dữ liệu',
                        'orientation' => 'chung',
                        'yccd' => [
                            'Nhận biết nhu cầu lưu trữ dữ liệu cho bài toán quản lí.',
                            'Diễn đạt khái niệm hệ CSDL, mô hình quan hệ (bảng, khóa, truy vấn).',
                            'Phân biệt kiến trúc CSDL tập trung và phân tán.',
                            'Nêu tầm quan trọng của bảo mật hệ CSDL.'
                        ]
                    ],
                    [
                        'core' => 'Thực hành tạo và khai thác CSDL',
                        'orientation' => 'ict',
                        'yccd' => [
                            'Tạo bảng, chỉ định khóa chính, khóa ngoài.',
                            'Thực hiện cập nhật và truy vấn tìm kiếm thông tin từ CSDL.',
                            'Giải thích tính ưu việt của việc quản lí dữ liệu bằng CSDL.',
                            'Nêu được một vài tổ chức cần ứng dụng CSDL.'
                        ]
                    ],
                    [
                        'core' => 'Kĩ thuật lập trình (Sắp xếp, Tìm kiếm)',
                        'orientation' => 'cs',
                        'yccd' => [
                            'Viết chương trình cho thuật toán sắp xếp và tìm kiếm.',
                            'Vận dụng thuật toán đã học giải quyết bài toán cụ thể.'
                        ]
                    ],
                    [
                        'core' => 'Kiểm thử và đánh giá hiệu quả chương trình',
                        'orientation' => 'cs',
                        'yccd' => [
                            'Biết được vai trò của kiểm thử trong phát hiện lỗi.',
                            'Trình bày sơ lược độ phức tạp thời gian của thuật toán.',
                            'Vận dụng quy tắc xác định độ phức tạp thời gian.'
                        ]
                    ],
                    [
                        'core' => 'Làm mịn dần và mô đun hóa',
                        'orientation' => 'cs',
                        'yccd' => [
                            'Giải thích và vận dụng phương pháp làm mịn dần.',
                            'Thiết kế chương trình thành các mô đun.',
                            'Nhận biết lợi ích của mô đun hóa trong làm việc nhóm.'
                        ]
                    ],
                    [
                        'core' => 'Tổ chức dữ liệu trong chương trình',
                        'orientation' => 'cs',
                        'yccd' => [
                            'Trình bày cấu trúc mảng (1-2 chiều) và danh sách liên kết.',
                            'Tạo thư viện nhỏ và viết chương trình sử dụng thư viện.',
                            'Viết chương trình giải quyết vấn đề liên môn.'
                        ]
                    ]
                ]
            ],

            // ====================================================
            // LỚP 12 (Cập nhật đầy đủ từ trang 6,7,8)
            // ====================================================
            12 => [
                'Chủ đề A. Máy tính và xã hội tri thức' => [
                    [
                        'core' => 'Giới thiệu Trí tuệ nhân tạo (AI)',
                        'orientation' => 'chung',
                        'yccd' => [
                            'Giải thích sơ lược khái niệm AI.',
                            'Nêu ví dụ ứng dụng AI (nhận dạng, chẩn đoán, trợ lí ảo).',
                            'Nêu ví dụ hệ thống AI có tri thức, khả năng suy luận và học.',
                            'Nêu cảnh báo về sự phát triển của AI trong tương lai.'
                        ]
                    ],
                    [
                        'core' => 'Thực hành kết nối thiết bị số',
                        'orientation' => 'ict',
                        'yccd' => [
                            'Kết nối PC với điện thoại di động, tivi thông minh, thiết bị thực tế ảo.'
                        ]
                    ]
                ],
                'Chủ đề B. Mạng máy tính và Internet' => [
                    [
                        'core' => 'Thiết bị và giao thức mạng',
                        'orientation' => 'chung',
                        'yccd' => [
                            'Nêu chức năng thiết bị mạng (Switch, Modem, AP).',
                            'Mô tả sơ lược giao thức TCP/IP.'
                        ]
                    ],
                    [
                        'core' => 'Các chức năng mạng của HĐH',
                        'orientation' => 'chung',
                        'yccd' => [
                            'Sử dụng các chức năng mạng của hệ điều hành để chia sẻ tài nguyên.'
                        ]
                    ],
                    [
                        'core' => 'Kết nối mạng trên thiết bị di động',
                        'orientation' => 'chung',
                        'yccd' => [
                            'Kết nối được thiết bị di động vào mạng máy tính.'
                        ]
                    ],
                    [
                        'core' => 'Phác thảo thiết kế mạng máy tính',
                        'orientation' => 'cs',
                        'yccd' => [
                            'Phân biệt chức năng Server, Switch, cáp mạng.',
                            'Nêu khái niệm đường truyền hữu tuyến và vô tuyến.',
                            'Trình bày sơ lược thiết kế mạng LAN cho một tổ chức nhỏ.'
                        ]
                    ]
                ],
                'Chủ đề D. Đạo đức, pháp luật' => [
                    [
                        'core' => 'Giữ gìn tính nhân văn trong thế giới ảo',
                        'orientation' => 'chung',
                        'yccd' => [
                            'Phân tích ưu nhược điểm về giao tiếp ảo.',
                            'Phân tích tính nhân văn trong ứng xử ở thế giới ảo.'
                        ]
                    ]
                ],
                'Chủ đề F. Giải quyết vấn đề với máy tính' => [
                    [
                        'core' => 'Cấu trúc trang web HTML',
                        'orientation' => 'ict',
                        'yccd' => [
                            'Hiểu cấu trúc trang web HTML.',
                            'Sử dụng thẻ HTML định dạng văn bản, liên kết, đa phương tiện.',
                            'Tạo bảng, khung và biểu mẫu (form).'
                        ]
                    ],
                    [
                        'core' => 'Sử dụng CSS trong tạo trang web',
                        'orientation' => 'ict',
                        'yccd' => [
                            'Hiểu và sử dụng thuộc tính cơ bản của CSS (màu, phông, nền).',
                            'Sử dụng các vùng chọn (selector).',
                            'Sử dụng CSS làm trang web đẹp và sinh động hơn.'
                        ]
                    ],
                    [
                        'core' => 'Giới thiệu Khoa học dữ liệu',
                        'orientation' => 'cs',
                        'yccd' => [
                            'Nêu mục tiêu, thành tựu của Khoa học dữ liệu.',
                            'Biết vai trò của máy tính với sự phát triển KHDL.',
                            'Trải nghiệm trích rút tri thức từ dữ liệu.'
                        ]
                    ],
                    [
                        'core' => 'Giới thiệu Học máy',
                        'orientation' => 'cs',
                        'yccd' => [
                            'Giải thích sơ lược khái niệm Học máy.',
                            'Nêu vai trò của Học máy (lọc thư rác, chẩn đoán bệnh...).'
                        ]
                    ],
                    [
                        'core' => 'Mô phỏng trong giải quyết vấn đề',
                        'orientation' => 'cs',
                        'yccd' => [
                            'Nêu lĩnh vực và vấn đề thực tế cần dùng kĩ thuật mô phỏng.',
                            'Sử dụng và giải thích các bước cơ bản của phần mềm mô phỏng.'
                        ]
                    ]
                ]
            ]
        ];

        // LOGIC LƯU VÀO DATABASE
        foreach ($curriculum as $grade => $topics) {
            foreach ($topics as $topicName => $contents) {
                // 1. Tạo Chủ đề
                $topic = Topic::firstOrCreate(['name' => $topicName]);

                foreach ($contents as $contentData) {
                    // 2. Tạo Nội dung cốt lõi
                    $core = CoreContent::firstOrCreate([
                        'name' => $contentData['core'],
                        'topic_id' => $topic->id,
                        'grade' => $grade,
                        'orientation' => $contentData['orientation']
                    ]);

                    // 3. Tạo Yêu cầu cần đạt
                    foreach ($contentData['yccd'] as $objText) {
                        LearningObjective::firstOrCreate([
                            'content' => $objText,
                            'core_content_id' => $core->id,
                            
                            // Dòng này để tránh lỗi "topic_id doesn't have default value" 
                            // nếu bảng learning_objectives của bạn vẫn còn cột topic_id NOT NULL
                            'topic_id' => $topic->id 
                        ]);
                    }
                }
            }
        }
    }
}