<div class="question-card" id="question-{{ $question->id }}">
    <div class="d-flex justify-content-between align-items-center">
        <div class="question-number">Câu {{ $index }}</div>
        @if($question->type == 'true_false_group')
            <span class="badge bg-light text-secondary border">Đúng / Sai</span>
        @endif
    </div>
    
    <div class="question-text">{!! $question->content !!}</div>

    @if($question->image)
        <div class="mb-4 text-center">
            <img src="{{ asset('storage/' . $question->image) }}" class="img-fluid rounded-3 border" style="max-height: 400px;">
        </div>
    @endif

    {{-- True/False Group --}}
    @if($question->type == 'true_false_group')
        <div class="tf-container">
            @foreach($question->children as $childIndex => $child)
                <div class="tf-row">
                    <div class="tf-content">
                        <span class="fw-bold me-2 text-primary">{{ $childIndex + 1 }}.</span>
                        {!! $child->content !!}
                    </div>
                    <div class="tf-options">
                        @foreach($child->answers as $ans)
                            @php 
                                $isTrue = stripos($ans->content, 'Đúng') !== false || stripos($ans->content, 'True') !== false;
                                $btnType = $isTrue ? 'true' : 'false';
                                $label = $isTrue ? 'Đúng' : 'Sai';
                                $icon = $isTrue ? 'bi-check-lg' : 'bi-x-lg';
                            @endphp
                            <div class="btn-tf" id="tf-btn-{{ $child->id }}-{{ $ans->id }}"
                                 onclick="selectTFAnswer({{ $question->id }}, {{ $child->id }}, {{ $ans->id }}, '{{ $btnType }}')">
                                <i class="bi {{ $icon }}"></i> {{ $label }}
                            </div>
                            <input type="radio" name="answers[{{ $child->id }}]" value="{{ $ans->id }}" id="radio-{{ $child->id }}-{{ $ans->id }}" class="d-none">
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Single Choice --}}
        <div class="answer-grid">
            @foreach($question->answers as $ansKey => $answer)
                @php $char = ['A','B','C','D'][$ansKey] ?? '?'; @endphp
                <div class="answer-option" id="option-card-{{ $question->id }}-{{ $answer->id }}"
                     onclick="selectAnswer({{ $question->id }}, {{ $answer->id }})">
                    <div class="option-marker">{{ $char }}</div>
                    <div class="answer-content">{{ $answer->content }}</div>
                </div>
                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->id }}" id="radio-{{ $question->id }}-{{ $answer->id }}" class="d-none">
            @endforeach
        </div>
    @endif
</div>