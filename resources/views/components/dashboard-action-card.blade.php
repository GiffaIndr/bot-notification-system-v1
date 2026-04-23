@props(['title', 'icon', 'iconDisabled' => false, 'column' => 'col-md-6'])

{{-- 1. Outer wrapper kita jadikan d-flex agar card di dalamnya otomatis sejajar (stretch) --}}
<div {{ $attributes->merge(['class' => $column . ' d-flex']) }}>

    {{-- 2. Card full Bootstrap 5: hapus border, tambah shadow, lengkungan, dan jadikan flex-column --}}
    <div class="card w-100 h-100 border-0 shadow-sm rounded-4 d-flex flex-column">

        {{-- 3. Card body jadi flex container yang memaksa ruang membesar (flex-grow-1) --}}
        <div class="card-body p-4 text-center d-flex flex-column flex-grow-1">

            {{-- Pengganti .icon-box menggunakan Bootstrap murni --}}
            <div class="mx-auto mb-3 d-inline-flex align-items-center justify-content-center bg-secondary-subtle text-secondary rounded-4 {{ $iconDisabled ? 'opacity-50' : '' }}"
                style="width: 56px; height: 56px;"
                aria-disabled="{{ $iconDisabled ? 'true' : 'false' }}">
                <i class="{{ $icon }} fs-3"></i>
            </div>

            <h5 class="fw-bold mb-3 text-dark">{{ $title }}</h5>

            {{-- 4. KUNCI UTAMA: Wrapper khusus untuk slot agar isinya memanjang dan tombol terdorong ke bawah --}}
            <div class="d-flex flex-column flex-grow-1 justify-content-end w-100">
                {{ $slot }}
            </div>

        </div>
    </div>
</div>
