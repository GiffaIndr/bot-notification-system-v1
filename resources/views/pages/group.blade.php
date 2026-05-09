@extends('layout.sidebar')

@section('content')
    <div class="container-fluid pb-5 pt-3">
        <div class="row justify-content-center g-4">
            <div class="col-12 col-xxl-11">

                {{-- 1. HEADER: Nama Grup & Role --}}
                <div
                    class="bg-white p-4 rounded-4 shadow-sm mb-4 border d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3 d-none d-md-flex align-items-center justify-content-center rounded-circle bg-primary text-white shadow-sm"
                            style="width: 50px; height: 50px;">
                            <i class="fa fa-users fs-5"></i>
                        </div>
                        <div>
                            <h2 class="fs-4 fw-bold mb-0 text-dark">{{ $group->name }}</h2>
                            <p class="small mb-0 mt-1 {{ $isGroupActive ? 'text-success' : 'text-muted' }}">
                                <i class="fa fa-calendar-check me-1"></i>
                                Masa aktif grup:
                                @if ($groupActiveUntil)
                                    {{ $groupActiveUntil->format('d M Y, H:i') }}
                                    @if (!$isGroupActive)
                                        <span class="text-danger">(sudah berakhir)</span>
                                    @endif
                                @else
                                    Tidak tersedia
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="text-md-end">
                        <span class="badge rounded-pill py-2 px-3 text-white border shadow-sm"
                            style="background-color: {{ $role->color }}; font-size: 0.85rem;">
                            <i class="fa fa-shield-halved me-2"></i>{{ $role->name }}
                        </span>
                    </div>
                </div>

                <div class="row g-4">
                    @php
                        $canAccessManagement =
                            $role->can_manage_bot ||
                            $role->can_manage_member ||
                            $role->can_generate_code ||
                            $role->can_edit_announcement;
                    @endphp
                    {{-- KOLOM KIRI: Daftar Pengumuman --}}
                    <div class="col-12 col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 bg-white">
                            <div
                                class="card-header bg-transparent border-bottom p-4 d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold text-dark m-0"><i class="fa fa-bullhorn text-primary me-2"></i>Pengumuman
                                </h5>
                                <div class="d-flex gap-2">
                                    @if ($canAccessManagement)
                                        <a href="/groups/{{ $group->getRouteKey() }}/logs"
                                            class="btn btn-sm btn-light border fw-bold px-3">Log</a>
                                    @endif
                                    @if ($role->can_create_announcement)
                                        <button class="btn btn-sm btn-primary fw-bold px-3 shadow-sm" data-bs-toggle="modal"
                                            data-bs-target="#modalCreate">
                                            <i class="fa fa-plus-circle me-1"></i> Buat Baru
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" data-category-tab="" class="btn btn-sm btn-primary">
                                            Semua
                                        </button>
                                        @foreach ($categories as $category)
                                            <button type="button" data-category-tab="{{ $category->id }}"
                                                class="btn btn-sm btn-light border">
                                                {{ $category->name }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <form method="GET" action="{{ route('groups.show', $group) }}" class="row g-2 mb-4">
                                    <div class="col-12 col-md-5">
                                        <input type="text" name="q" value="{{ $search }}"
                                            class="form-control" placeholder="Cari judul, konten, atau pembuat...">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <select name="filter" class="form-select">
                                            <option value="all" @selected($filter === 'all')>Semua status</option>
                                            <option value="pinned" @selected($filter === 'pinned')>Hanya pinned</option>
                                            <option value="scheduled" @selected($filter === 'scheduled')>Terjadwal</option>
                                            <option value="repeat" @selected($filter === 'repeat')>Berulang</option>
                                            <option value="deadline_upcoming" @selected($filter === 'deadline_upcoming')>Masih dalam
                                                tenggat</option>
                                            <option value="deadline_passed" @selected($filter === 'deadline_passed')>Sudah lewat tenggat
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <select name="sort" class="form-select">
                                            <option value="latest" @selected($sort === 'latest')>Terbaru</option>
                                            <option value="oldest" @selected($sort === 'oldest')>Terlama</option>
                                            <option value="pinned" @selected($sort === 'pinned')>Pinned dulu</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-2 d-grid">
                                        <button type="submit" class="btn btn-primary">Terapkan</button>
                                    </div>
                                </form>

                                @forelse ($announcements as $announcement)
                                    <div class="card mb-3 border shadow-none rounded-4 js-announcement-item {{ $announcement->is_pinned ? 'border-warning bg-warning-subtle bg-opacity-10' : '' }}"
                                        data-category-id="{{ $announcement->category_id ?? '' }}">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start gap-3">
                                                <div class="flex-grow-1 min-w-0">
                                                    <h6 class="fw-bold text-dark mb-1">
                                                        @if ($announcement->is_pinned)
                                                            <i class="fa fa-thumbtack text-warning me-1"></i>
                                                        @endif
                                                        {{ $announcement->title }}
                                                    </h6>
                                                    @if ($announcement->category)
                                                        <span class="badge bg-light text-primary border mb-2">
                                                            <i
                                                                class="fa fa-tag me-1"></i>{{ $announcement->category->name }}
                                                        </span>
                                                    @endif
                                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                                        @if ($announcement->deadline_mode && $announcement->deadline_at)
                                                            <span class="badge bg-light text-danger border">
                                                                <i class="fa fa-hourglass-half me-1"></i>Tenggat:
                                                                {{ $announcement->deadline_at->format('d M Y, H:i') }}
                                                            </span>
                                                        @endif
                                                        <span class="badge bg-light text-secondary border">
                                                            <i class="fa fa-paper-plane me-1"></i>Kirim pertama:
                                                            {{ $announcement->scheduled_at?->format('d M Y, H:i') }}
                                                        </span>
                                                        @if ($announcement->reminder_enabled && $announcement->reminder_at)
                                                            <span class="badge bg-light text-warning border">
                                                                <i class="fa fa-bell me-1"></i>Pengingat:
                                                                {{ $announcement->reminder_at->format('d M Y, H:i') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="text-secondary small mb-2"
                                                        style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; word-break: break-word;">
                                                        {{ $announcement->content }}</p>
                                                    <div class="d-flex gap-2 text-muted" style="font-size: 10px;">
                                                        <span><i
                                                                class="fa fa-user me-1"></i>{{ $announcement->user->name }}</span>
                                                        <span><i
                                                                class="fa fa-clock me-1"></i>{{ $announcement->created_at->format('d M, H:i') }}</span>
                                                    </div>
                                                </div>
                                                @if ($role->can_edit_announcement)
                                                    <div class="dropdown flex-shrink-0 position-relative">
                                                        <button class="btn btn-light btn-sm border"
                                                            data-bs-toggle="dropdown"><i
                                                                class="fa fa-ellipsis-v"></i></button>
                                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                            <li>
                                                                <form method="POST"
                                                                    action="/groups/{{ $group->getRouteKey() }}/announcements/{{ $announcement->id }}/pin">
                                                                    @csrf<button
                                                                        class="dropdown-item small">{{ $announcement->is_pinned ? 'Lepas Pin' : 'Sematkan' }}</button>
                                                                </form>
                                                            </li>
                                                            <li><button class="dropdown-item small text-danger"
                                                                    onclick="confirmDelete({{ $announcement->id }})">Hapus</button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                            @if ($role->can_edit_announcement)
                                                <form method="POST"
                                                    action="{{ route('groups.announcements.category.update', [$group, $announcement]) }}"
                                                    class="mt-3">
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
                                    </div>
                                    <form id="deleteForm{{ $announcement->id }}" method="POST"
                                        action="/groups/{{ $group->getRouteKey() }}/announcements/{{ $announcement->id }}"
                                        class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @empty
                                    <div class="text-center py-5 text-muted small">Belum ada pengumuman.</div>
                                @endforelse

                                <div id="categoryEmptyState" class="text-center py-4 text-muted small d-none">
                                    Tidak ada pengumuman pada kategori ini di halaman saat ini.
                                </div>

                                @if ($announcements->hasPages())
                                    <div class="mt-4">
                                        {{ $announcements->onEachSide(1)->links('pagination::bootstrap-5') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- KOLOM KANAN: Integrasi & Kode --}}
                    <div class="col-12 col-lg-4">

                        @if ($canAccessManagement)
                            {{-- SETTINGS BUTTON --}}
                            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                                <div class="card-body p-4 text-center">
                                    <i class="fa fa-cog text-primary mb-3" style="font-size: 32px;"></i>
                                    <h6 class="fw-bold text-dark mb-2">Pengaturan Grup</h6>
                                    <p class="small text-muted mb-3">Kelola bot integrasi, roles, kode undangan, dan
                                        kategori pengumuman.</p>
                                    <a href="{{ route('groups.settings', $group) }}"
                                        class="btn btn-primary btn-sm w-100 fw-bold px-3">
                                        <i class="fa fa-gear me-2"></i> Buka Pengaturan
                                    </a>
                                </div>
                            </div>
                        @endif

                        {{-- Daftar anggota terpisah --}}
                        <div class="card border-0 shadow-sm rounded-4 bg-white">
                            <div class="card-header bg-transparent border-bottom p-4 d-flex align-items-center gap-2">
                                <i class="fa fa-users text-muted"></i>
                                <h6 class="fw-bold text-dark m-0">Daftar Anggota</h6>
                            </div>
                            <div class="card-body p-4">
                                <p class="small text-muted mb-3">
                                    Lihat seluruh anggota grup pada halaman khusus daftar anggota.
                                </p>
                                <a href="{{ route('groups.members.index', $group) }}"
                                    class="btn btn-light border btn-sm w-100 fw-bold">
                                    <i class="fa fa-arrow-right me-2"></i>Lihat Daftar Anggota
                                </a>
                            </div>
                            <div class="card-footer bg-transparent border-0 p-3 text-center">
                                <small class="text-muted fw-semibold">Total anggota saat ini:
                                    {{ $memberCount }}</small>
                            </div>
                        </div>

                        @if ($role->is_owner)
                            {{-- Upgrade Card --}}
                            <div class="card border-0 shadow-sm rounded-4 mt-4 bg-white">
                                <div class="card-body p-4 text-center">
                                    <i class="fa fa-rocket text-warning mb-3" style="font-size: 32px;"></i>
                                    <h6 class="fw-bold text-dark mb-2">Upgrade Grup</h6>
                                    <p class="small text-muted mb-3">Tambah masa aktif, kuota member, atau bot integrasi
                                    </p>
                                    <a href="{{ route('groups.upgrade.cart', $group) }}"
                                        class="btn btn-primary btn-sm w-100 fw-bold">
                                        <i class="fa fa-shopping-cart me-2"></i> Upgrade Sekarang
                                    </a>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

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

            const categoryTabs = document.querySelectorAll('[data-category-tab]');
            const announcementItems = document.querySelectorAll('.js-announcement-item');
            const categoryEmptyState = document.getElementById('categoryEmptyState');

            const setActiveCategory = (categoryId) => {
                let visibleCount = 0;

                announcementItems.forEach((item) => {
                    const itemCategoryId = item.dataset.categoryId || '';
                    const shouldShow = categoryId === '' || itemCategoryId === categoryId;
                    item.classList.toggle('d-none', !shouldShow);
                    if (shouldShow) {
                        visibleCount++;
                    }
                });

                categoryTabs.forEach((tab) => {
                    const isActive = (tab.dataset.categoryTab || '') === categoryId;
                    tab.classList.toggle('btn-primary', isActive);
                    tab.classList.toggle('btn-light', !isActive);
                    tab.classList.toggle('border', !isActive);
                });

                if (categoryEmptyState) {
                    categoryEmptyState.classList.toggle('d-none', visibleCount > 0);
                }
            };

            categoryTabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    setActiveCategory(tab.dataset.categoryTab || '');
                });
            });

            setActiveCategory('');
        });

        function notify(message, type = 'info') {
            if (typeof window.showToast === 'function') {
                window.showToast(message, type);
                return;
            }
            alert(message);
        }

        function confirmDelete(id) {
            if (confirm('Hapus pengumuman ini?')) {
                document.getElementById('deleteForm' + id).submit();
            }
        }
    </script>

    @if ($role->can_create_announcement)
        <div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <form method="POST" action="/groups/{{ $group->getRouteKey() }}/announcements">
                        @csrf
                        <div class="modal-header border-bottom-0 pb-0">
                            <h5 class="modal-title fw-bold">Buat Pengumuman Baru</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body pt-3">
                            <div class="mb-3">
                                <label class="form-label small text-muted">Judul <span class="text-muted"
                                        style="font-size: 0.8rem;">(Max 100 karakter)</span></label>
                                <input type="text" name="title" class="form-control" maxlength="100"
                                    placeholder="Contoh: Pertemuan Minggu Depan" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted">Isi Pengumuman <span class="text-muted"
                                        style="font-size: 0.8rem;">(Max 1000 karakter)</span></label>
                                <textarea name="content" class="form-control" rows="4" maxlength="1000"
                                    placeholder="Tuliskan isi pengumuman di sini..." required></textarea>
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
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input js-deadline-mode" type="checkbox" role="switch"
                                            id="deadline_mode_group" name="deadline_mode" value="1">
                                        <label class="form-check-label" for="deadline_mode_group">Aktifkan mode tenggat
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
                                            role="switch" id="reminder_enabled_group" name="reminder_enabled"
                                            value="1">
                                        <label class="form-check-label" for="reminder_enabled_group">Aktifkan pengingat
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

    <style>
        .shadow-xs {
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
    </style>
@endsection
