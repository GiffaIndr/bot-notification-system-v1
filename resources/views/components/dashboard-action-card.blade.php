@props(['title', 'icon', 'iconDisabled' => false, 'column' => 'col-md-6', 'subtitle' => null, 'tone' => 'primary'])

@php
    $toneMap = [
        'primary' => ['accent' => '#0d6efd', 'surface' => '#e9f4ff', 'icon' => 'text-primary'],
        'success' => ['accent' => '#198754', 'surface' => '#e9fbf0', 'icon' => 'text-success'],
        'warning' => ['accent' => '#f59f00', 'surface' => '#fff6df', 'icon' => 'text-warning'],
        'info' => ['accent' => '#0dcaf0', 'surface' => '#e6fbff', 'icon' => 'text-info'],
        'dark' => ['accent' => '#1f2937', 'surface' => '#edf0f4', 'icon' => 'text-dark'],
    ][$tone] ?? ['accent' => '#0d6efd', 'surface' => '#e9f4ff', 'icon' => 'text-primary'];
@endphp

<div {{ $attributes->merge(['class' => $column . ' d-flex']) }}>
    <div class="card w-100 h-100 border-0 shadow-sm rounded-4 d-flex flex-column dashboard-action-card">
        <div class="dashboard-action-card__accent" style="background: {{ $toneMap['accent'] }};"></div>

        <div class="card-body p-4 d-flex flex-column flex-grow-1">
            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-4 {{ $iconDisabled ? 'opacity-50' : '' }}"
                    style="width: 56px; height: 56px; background: {{ $toneMap['surface'] }}; color: {{ $toneMap['accent'] }};"
                    aria-disabled="{{ $iconDisabled ? 'true' : 'false' }}">
                    <i class="{{ $icon }} fs-3 {{ $toneMap['icon'] }}"></i>
                </div>

                <span class="badge rounded-pill text-uppercase"
                    style="letter-spacing: 0.08em; background: {{ $toneMap['surface'] }}; color: {{ $toneMap['accent'] }};">
                    Quick Action
                </span>
            </div>

            <h5 class="fw-bold mb-2 text-dark">{{ $title }}</h5>

            @if ($subtitle)
                <p class="text-muted small mb-4">{{ $subtitle }}</p>
            @endif

            <div class="d-flex flex-column flex-grow-1 justify-content-end w-100">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
