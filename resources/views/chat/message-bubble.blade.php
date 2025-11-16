@php
    $align = $self ? 'text-end' : 'text-start';
    $bubble = $self ? 'bg-primary text-white' : 'bg-light';
@endphp

<div class="{{ $align }} mb-2">
    @if($m->message)
        <p class="{{ $bubble }} p-2 rounded d-inline-block">{{ $m->message }}</p>
    @endif

    @if($m->file_path)
        @php $url = "/storage/" . $m->file_path; @endphp

        @if(preg_match('/\.(jpg|jpeg|png|gif)$/i', $m->file_path))
            <img src="{{ $url }}" class="img-fluid rounded mt-2" style="max-width:200px;">
        @else
            <a href="{{ $url }}" download class="btn btn-sm btn-secondary mt-2">
                Download File
            </a>
        @endif
    @endif
</div>
