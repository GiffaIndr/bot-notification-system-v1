@extends('layout.sidebar')

@section('content')
    <div class="container-fluid pb-5 pt-3">
        <div class="row justify-content-center">
            <div class="col-12 col-xxl-10">

                {{-- 1. HEADER --}}
                <div
                    class="bg-white p-4 rounded-4 shadow-sm mb-4 border d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center">
                        <a href="/groups/{{ $group->getRouteKey() }}"
                            class="btn btn-light btn-sm rounded-circle border me-3 d-flex align-items-center justify-content-center shadow-xs"
                            style="width: 40px; height: 40px;">
                            <i class="fa fa-arrow-left text-secondary"></i>
                        </a>
                        <div>
                            <h2 class="fs-4 fw-bold mb-0 text-dark">Arsip Pengumuman</h2>
                            <p class="text-muted small mb-0">{{ $group->name }} • {{ $announcements->total() }} Informasi
                            </p>
                        </div>
                    </div>
                    @if ($role->can_create_announcement)
                        <button class="btn btn-primary fw-bold px-4 rounded-3 shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#modalCreate">
                            <i class="fa fa-plus-circle me-2"></i>Buat Baru
                        </button>
                    @endif
                </div>

                {{-- 2. TOOLBAR: Search, Filter, & Sort (Simetris & Profesional) --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white border">
                    <div class="card-body p-3 p-lg-4">
                        <form method="GET" action="{{ route('groups.announcements.index', $group) }}">
                            <div class="row g-3 align-items-end">
                                <div class="col-12 col-lg-4">
                                    <label class="small fw-bold text-muted mb-2 text-uppercase">Cari Kata Kunci</label>
                                    <div class="input-group border rounded-3 overflow-hidden shadow-xs">
                                        <span class="input-group-text bg-white border-0 ps-3 text-muted"><i
                                                class="fa-solid fa-search"></i></span>
                                        <input type="text" name="q" class="form-control border-0 py-2"
                                            placeholder="Judul atau isi pesan..." value="{{ $search }}">
                                    </div>
                                </div>
                                <div class="col-6 col-lg-2">
                                    <label class="small fw-bold text-muted mb-2 text-uppercase">Status</label>
                                    <select name="filter"
                                        class="form-select border rounded-3 py-2 shadow-xs fw-semibold text-secondary">
                                        <option value="all" @selected($filter === 'all')>Semua</option>
                                        <option value="pinned" @selected($filter === 'pinned')>Pinned</option>
                                        <option value="scheduled" @selected($filter === 'scheduled')>Terjadwal</option>
                                        <option value="repeat" @selected($filter === 'repeat')>Berulang</option>
                                        <option value="attachment" @selected($filter === 'attachment')>Ada Lampiran
                                        </option>
                                    </select>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label class="small fw-bold text-muted mb-2 text-uppercase">Kategori</label>
                                    <select name="category_id"
                                        class="form-select border rounded-3 py-2 shadow-xs fw-semibold text-secondary">
                                        <option value="">Semua kategori</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected((string) $categoryId === (string) $category->id)>
                                                {{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 col-lg-2">
                                    <label class="small fw-bold text-muted mb-2 text-uppercase">Urutkan</label>
                                    <select name="sort"
                                        class="form-select border rounded-3 py-2 shadow-xs fw-semibold text-secondary">
                                        <option value="latest" @selected($sort === 'latest')>Terbaru</option>
                                        <option value="oldest" @selected($sort === 'oldest')>Terlama</option>
                                        <option value="pinned" @selected($sort === 'pinned')>Prioritas Pin</option>
                                    </select>
                                </div>
                                <div class="col-6 col-lg-auto">
                                    <button type="submit" class="btn btn-primary w-100">Terapkan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- 3. FEED PENGUMUMAN --}}
                <div id="announcementContainer">
                    @forelse ($announcements as $announcement)
                        <div class="card border-0 shadow-sm rounded-4 mb-3 transition-all">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                            @if ($announcement->is_pinned)
                                                <span
                                                    class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1"
                                                    style="font-size: 10px;">
                                                    <i class="fa fa-thumbtack me-1"></i>DISEMATKAN
                                                </span>
                                            @endif
                                            @if ($announcement->category)
                                                <span class="badge bg-light text-primary border rounded-pill px-2 py-1"
                                                    style="font-size: 10px;">
                                                    <i class="fa fa-tag me-1"></i>{{ $announcement->category->name }}
                                                </span>
                                            @endif
                                            @if ($announcement->deadline_mode && $announcement->deadline_at)
                                                <span class="badge bg-light text-danger border rounded-pill px-2 py-1"
                                                    style="font-size: 10px;">
                                                    <i class="fa fa-hourglass-half me-1"></i>Tenggat:
                                                    {{ $announcement->deadline_at->format('d M Y, H:i') }}
                                                </span>
                                            @endif
                                            <span class="badge bg-light text-secondary border rounded-pill px-2 py-1"
                                                style="font-size: 10px;">
                                                <i class="fa fa-paper-plane me-1"></i>Kirim pertama:
                                                {{ $announcement->scheduled_at?->format('d M Y, H:i') }}
                                            </span>
                                            @if ($announcement->reminder_enabled && $announcement->reminder_at)
                                                <span class="badge bg-light text-warning border rounded-pill px-2 py-1"
                                                    style="font-size: 10px;">
                                                    <i class="fa fa-bell me-1"></i>Pengingat:
                                                    {{ $announcement->reminder_at->format('d M Y, H:i') }}
                                                </span>
                                            @endif
                                            <span class="text-muted" style="font-size: 11px;">
                                                <i
                                                    class="fa fa-clock me-1"></i>{{ $announcement->created_at->format('d M Y, H:i') }}
                                            </span>
                                        </div>

                                        <h5 class="fw-bold text-dark mb-2">{{ $announcement->title }}</h5>
                                        <p class="text-secondary mb-3 lh-base"
                                            style="display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; word-break: break-word;">
                                            {{ $announcement->content }}</p>

                                        @if ($announcement->attachments->isNotEmpty())
                                            <div class="d-flex flex-wrap gap-2 mb-3">
                                                @foreach ($announcement->attachments as $attachment)
                                                    <a href="{{ $attachment->url }}" target="_blank"
                                                        class="btn btn-sm btn-light border text-dark py-1 px-2 rounded-2 shadow-xs"
                                                        style="font-size: 11px;">
                                                        <i class="fa fa-paperclip me-1 text-primary"></i>
                                                        {{ Str::limit($attachment->filename, 20) }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-xs"
                                                style="width: 24px; height: 24px; font-size: 9px;">
                                                {{ strtoupper(substr($announcement->user->name, 0, 1)) }}
                                            </div>
                                            <span class="small fw-semibold text-secondary">Oleh
                                                {{ $announcement->user->name }}</span>
                                        </div>

                                        @if ($role->can_edit_announcement)
                                            <form method="POST"
                                                action="{{ route('groups.announcements.category.update', [$group, $announcement]) }}"
                                                class="mt-3 pt-2 border-top">
                                                @csrf
                                                @method('PUT')
                                                <div class="d-flex align-items-center gap-2">
                                                    <label class="small text-muted mb-0">Kategori</label>
                                                    <select name="category_id" class="form-select form-select-sm"
                                                        onchange="this.form.submit()">
                                                        <option value="">Tanpa kategori</option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}"
                                                                @selected((int) $announcement->category_id === (int) $category->id)>
                                                                {{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- Action Dropdown --}}
                                    @if ($role->can_edit_announcement)
                                        <div class="dropdown flex-shrink-0 position-relative">
                                            <button class="btn btn-light btn-sm rounded-3 border shadow-xs"
                                                data-bs-toggle="dropdown">
                                                <i class="fa fa-ellipsis-v text-muted"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                                <li>
                                                    <form method="POST"
                                                        action="/groups/{{ $group->getRouteKey() }}/announcements/{{ $announcement->id }}/pin">
                                                        @csrf
                                                        <button class="dropdown-item small">
                                                            <i class="fa fa-thumbtack me-2 text-warning"></i>
                                                            {{ $announcement->is_pinned ? 'Lepas Pin' : 'Sematkan' }}
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <button class="dropdown-item small text-danger"
                                                        onclick="confirmDelete({{ $announcement->id }})">
                                                        <i class="fa fa-trash me-2"></i>Hapus
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <form id="deleteForm{{ $announcement->id }}" method="POST"
                                action="/groups/{{ $group->getRouteKey() }}/announcements/{{ $announcement->id }}"
                                class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    @empty
                        <div class="text-center py-5 bg-white rounded-4 border border-dashed shadow-sm">
                            <i class="fa fa-bullhorn fa-3x text-muted opacity-25 mb-3"></i>
                            <h6 class="text-muted">Belum ada pengumuman apapun.</h6>
                        </div>
                    @endforelse
                </div>

                @if ($announcements->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $announcements->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    @if ($role->can_create_announcement)
        <div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <form method="POST" action="/groups/{{ $group->getRouteKey() }}/announcements"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header border-bottom-0 pb-0">
                            <h5 class="modal-title fw-bold">Buat Pengumuman Baru</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body pt-3">
                            <div class="mb-3">
                                <label class="form-label small text-muted">Judul</label>
                                <input type="text" name="title" class="form-control" maxlength="255" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted">Isi Pengumuman</label>
                                <textarea name="content" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Kategori</label>
                                    <select name="category_id" class="form-select">
                                        <option value="">Tanpa kategori</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Pengulangan</label>
                                    <select name="repeat" class="form-select" required>
                                        <option value="none">Tidak berulang</option>
                                        <option value="daily">Harian</option>
                                        <option value="weekly">Mingguan</option>
                                        <option value="monthly">Bulanan</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Waktu kirim pertama</label>
                                    <input type="datetime-local" name="scheduled_at" class="form-control"
                                        value="{{ old('scheduled_at', now()->format('Y-m-d\\TH:i')) }}" required>
                                    <small class="text-muted">Ini waktu kirim awal pengumuman, bukan tenggat.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Lampiran (maks. 3)</label>
                                    <input type="file" name="attachments[]" class="form-control" multiple>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input js-deadline-mode" type="checkbox" role="switch"
                                            id="deadline_mode_archive" name="deadline_mode" value="1">
                                        <label class="form-check-label" for="deadline_mode_archive">Aktifkan mode tenggat
                                            waktu</label>
                                    </div>
                                    <small class="text-muted d-none js-deadline-hint">Mode tenggat hanya tersedia jika
                                        pengulangan diatur ke "Tidak berulang".</small>
                                </div>
                                <div class="col-md-6 d-none js-deadline-fields">
                                    <label class="form-label small text-muted">Tenggat waktu</label>
                                    <input type="datetime-local" name="deadline_at" class="form-control"
                                        value="{{ old('deadline_at') }}">
                                </div>
                                <div class="col-12 d-none js-deadline-fields">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input js-reminder-enabled" type="checkbox"
                                            role="switch" id="reminder_enabled_archive" name="reminder_enabled"
                                            value="1">
                                        <label class="form-check-label" for="reminder_enabled_archive">Aktifkan pengingat
                                            sekali sebelum tenggat</label>
                                    </div>
                                </div>
                                <div class="col-md-6 d-none js-reminder-fields">
                                    <label class="form-label small text-muted">Waktu pengingat sebelum tenggat</label>
                                    <div class="input-group">
                                        <input type="number" name="reminder_offset_value" class="form-control"
                                            min="1" max="365" placeholder="1"
                                            value="{{ old('reminder_offset_value', 1) }}">
                                        <select name="reminder_offset_unit" class="form-select">
                                            <option value="day" @selected(old('reminder_offset_unit', 'day') === 'day')>Hari</option>
                                            <option value="hour" @selected(old('reminder_offset_unit') === 'hour')>Jam</option>
                                        </select>
                                    </div>
                                    <small class="text-muted">Contoh: isi 1 Hari berarti pengingat dikirim 1 hari sebelum
                                        tenggat.</small>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pt-0">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Pengumuman</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script>
        function bindDeadlineReminderForm(root) {
            const deadlineMode = root.querySelector('.js-deadline-mode');
            const reminderEnabled = root.querySelector('.js-reminder-enabled');
            const deadlineFields = root.querySelectorAll('.js-deadline-fields');
            const reminderFields = root.querySelectorAll('.js-reminder-fields');
            const repeatSelect = root.querySelector('select[name="repeat"]');
            const deadlineHint = root.querySelector('.js-deadline-hint');

            if (!deadlineMode) return;

            const toggle = () => {
                const isRepeatMode = repeatSelect && repeatSelect.value !== 'none';

                if (isRepeatMode) {
                    deadlineMode.checked = false;
                    deadlineMode.disabled = true;
                    if (reminderEnabled) {
                        reminderEnabled.checked = false;
                    }
                } else {
                    deadlineMode.disabled = false;
                }

                if (deadlineHint) {
                    deadlineHint.classList.toggle('d-none', !isRepeatMode);
                }

                deadlineFields.forEach(el => el.classList.toggle('d-none', !deadlineMode.checked));
                const reminderActive = deadlineMode.checked && reminderEnabled && reminderEnabled.checked;
                reminderFields.forEach(el => el.classList.toggle('d-none', !reminderActive));
            };

            deadlineMode.addEventListener('change', toggle);
            if (reminderEnabled) {
                reminderEnabled.addEventListener('change', toggle);
            }
            if (repeatSelect) {
                repeatSelect.addEventListener('change', toggle);
            }

            toggle();
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.modal form').forEach(bindDeadlineReminderForm);
        });

        function confirmDelete(id) {
            if (confirm('Hapus pengumuman ini?')) {
                document.getElementById('deleteForm' + id).submit();
            }
        }
    </script>
@endsection
