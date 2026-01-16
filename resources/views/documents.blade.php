<x-app-layout>
    {{-- Thêm thư viện Icon Bootstrap nếu chưa có --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* --- RESET CSS CƠ BẢN ĐỂ ĐẢM BẢO GIAO DIỆN ĐẸP --- */
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; margin: 0; }
        .custom-container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        
        /* HEADER */
        .glass-header {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
            position: sticky; top: 0; z-index: 100;
            padding: 15px 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header-content { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        
        /* TITLE */
        .page-title { font-size: 24px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 10px; margin: 0; }
        .title-icon { 
            width: 40px; height: 40px; background: #4f46e5; color: white; 
            border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;
        }

        /* SEARCH BAR */
        .search-box { position: relative; width: 300px; }
        .search-input {
            width: 100%; padding: 10px 15px 10px 40px; border-radius: 50px;
            border: 1px solid #cbd5e1; background: #f1f5f9; outline: none;
            transition: all 0.3s;
        }
        .search-input:focus { background: white; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        .search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; }

        /* TABS LỚP HỌC */
        .tabs-container { display: flex; gap: 10px; margin-top: 15px; overflow-x: auto; padding-bottom: 5px; }
        .grade-tab {
            padding: 10px 20px; border-radius: 50px; border: 1px solid #e2e8f0;
            background: white; color: #64748b; font-weight: 700; cursor: pointer;
            transition: all 0.3s; display: flex; align-items: center; gap: 8px;
            white-space: nowrap;
        }
        .grade-tab:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        
        /* Active States */
        .grade-tab.active-10 { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border: none; }
        .grade-tab.active-11 { background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; }
        .grade-tab.active-12 { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border: none; }

        /* CONTENT GRID */
        .content-area { padding: 40px 0; }
        .section-header { margin-bottom: 30px; border-left: 5px solid #3b82f6; padding-left: 15px; }
        .section-title { font-size: 20px; font-weight: 700; color: #334155; margin: 0; }
        
        .topic-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); /* Tự động chia cột responsive */
            gap: 25px;
        }

        /* CARD DESIGN */
        .topic-card {
            background: white; border-radius: 16px; overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #f1f5f9;
            transition: transform 0.3s ease; position: relative;
            display: flex; flex-direction: column; height: 100%;
        }
        .topic-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
        .accent-bar { height: 6px; width: 100%; }
        .bar-10 { background: #3b82f6; } .bar-11 { background: #10b981; } .bar-12 { background: #f59e0b; }

        .card-body { padding: 20px; flex: 1; display: flex; flex-direction: column; }
        
        .topic-meta { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .topic-badge { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8; background: #f8fafc; padding: 4px 8px; border-radius: 4px; }
        .file-count { font-size: 11px; font-weight: 700; color: #4f46e5; background: #eef2ff; padding: 4px 10px; border-radius: 20px; }

        .topic-name { font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 20px 0; line-height: 1.4; min-height: 50px; }

        /* FILE LIST */
        .file-list { display: flex; flex-direction: column; gap: 8px; flex: 1; }
        .doc-item {
            display: flex; align-items: center; padding: 10px; background: #f8fafc;
            border-radius: 10px; text-decoration: none; color: #334155;
            transition: 0.2s; border: 1px solid transparent;
        }
        .doc-item:hover { background: white; border-color: #cbd5e1; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        
        .file-icon { 
            width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; 
            font-size: 18px; margin-right: 12px; flex-shrink: 0;
        }
        .pdf { background: #fef2f2; color: #ef4444; }
        .word { background: #eff6ff; color: #3b82f6; }
        .ppt { background: #fff7ed; color: #f97316; }

        .file-info { flex: 1; overflow: hidden; }
        .file-name { font-size: 14px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .file-date { font-size: 11px; color: #94a3b8; display: block; margin-top: 2px; }
        
        .download-icon { color: #cbd5e1; transition: 0.2s; }
        .doc-item:hover .download-icon { color: #4f46e5; }

        .view-more { 
            text-align: center; margin-top: 15px; padding-top: 15px; border-top: 1px solid #f1f5f9;
            font-size: 13px; font-weight: 600; color: #4f46e5; text-decoration: none; display: block;
        }
        .view-more:hover { text-decoration: underline; }

        /* TRẠNG THÁI TRỐNG */
        .empty-state { text-align: center; padding: 30px 0; color: #94a3b8; font-style: italic; font-size: 13px; }
    </style>
    @endpush

    <div x-data="{ activeGrade: 10 }" style="min-height: 100vh; padding-bottom: 50px;">
        
        {{-- HEADER --}}
        <div class="glass-header">
            <div class="custom-container">
                <div class="header-content">
                    {{-- Title --}}
                    <h1 class="page-title">
                        <div class="title-icon"><i class="bi bi-collection-fill"></i></div>
                        Thư viện tài liệu
                    </h1>
                    
                    {{-- Search --}}
                    <div class="search-box">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="search-input" placeholder="Tìm tài liệu, đề cương...">
                    </div>
                </div>

                {{-- Tabs --}}
                <div class="tabs-container">
                    @foreach([10, 11, 12] as $grade)
                        <div @click="activeGrade = {{ $grade }}"
                             class="grade-tab"
                             :class="{ 'active-{{ $grade }}': activeGrade === {{ $grade }} }">
                            <i class="bi bi-mortarboard{{ $grade == 12 ? '-fill' : '' }}"></i> 
                            Lớp {{ $grade }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- BODY CONTENT --}}
        <div class="custom-container content-area">
            @foreach([10, 11, 12] as $grade)
                <div x-show="activeGrade === {{ $grade }}" style="display: none;">
                    
                    {{-- Section Header --}}
                    <div class="section-header" style="border-color: {{ $grade==10?'#3b82f6':($grade==11?'#10b981':'#f59e0b') }}">
                        <h3 class="section-title">Chương trình Tin học {{ $grade }}</h3>
                        <p style="margin: 5px 0 0; color: #64748b; font-size: 14px;">Tài liệu bám sát chương trình GDPT 2018</p>
                    </div>

                    {{-- GRID --}}
                    <div class="topic-grid">
                        @foreach($topics as $topic)
                            @php
                                // Nếu chưa có cột grade, code này sẽ lỗi, hãy chắc chắn đã chạy migration
                                $docsInTopic = $documents[$grade][$topic->id] ?? collect([]);
                            @endphp

                            <div class="topic-card">
                                <div class="accent-bar bar-{{ $grade }}"></div>
                                <div class="card-body">
                                    <div class="topic-meta">
                                        <span class="topic-badge">Chủ đề {{ $topic->id }}</span>
                                        <span class="file-count">{{ $docsInTopic->count() }} Files</span>
                                    </div>
                                    
                                    <h4 class="topic-name">{{ $topic->name }}</h4>

                                    <div class="file-list">
                                        @if($docsInTopic->count() > 0)
                                            @foreach($docsInTopic->take(3) as $doc)
                                                <a href="{{ $doc->file_path }}" target="_blank" class="doc-item">
                                                    <div class="file-icon {{ str_contains($doc->file_type, 'pdf') ? 'pdf' : (str_contains($doc->file_type, 'ppt') ? 'ppt' : 'word') }}">
                                                        <i class="bi {{ $doc->icon_html }}"></i>
                                                    </div>
                                                    <div class="file-info">
                                                        <div class="file-name" title="{{ $doc->title }}">{{ $doc->title }}</div>
                                                        <span class="file-date">{{ $doc->file_size }} • {{ $doc->created_at->format('d/m/Y') }}</span>
                                                    </div>
                                                    <i class="bi bi-download download-icon"></i>
                                                </a>
                                            @endforeach
                                        @else
                                            <div class="empty-state">
                                                <i class="bi bi-folder-x" style="font-size: 24px; display: block; margin-bottom: 5px;"></i>
                                                Chưa có tài liệu nào
                                            </div>
                                        @endif
                                    </div>

                                    @if($docsInTopic->count() > 3)
                                        <a href="#" class="view-more">Xem thêm {{ $docsInTopic->count() - 3 }} tài liệu khác <i class="bi bi-arrow-right"></i></a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>