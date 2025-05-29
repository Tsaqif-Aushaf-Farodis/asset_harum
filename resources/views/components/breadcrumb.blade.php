@props([
    'title' => null,
    'breadcrumbs' => [],
])

<div class="d-flex justify-content-between align-items-center">
    @if ($title)
        <h4 class="fw-bold">{{ $title }}</h4>
    @endif

    <nav aria-label="breadcrumb" class="d-none d-md-flex d-xl-flex">
        <ol class="breadcrumb breadcrumb-style1">
            @foreach ($breadcrumbs as $breadcrumb)
                <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}"
                    @if ($loop->last) aria-current="page" @endif>
                    @if (!$loop->last && isset($breadcrumb['url']))
                        <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
                    @else
                        {{ $breadcrumb['label'] }}
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
</div>
