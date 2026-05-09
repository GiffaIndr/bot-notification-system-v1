@extends('layout.sidebar')

@section('content')
    <div class="container-fluid pb-5 pt-4">
        <div class="row justify-content-center">
            <div class="col-lg-11">

                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
                    <div>
                        <h2 class="fw-bold fs-4 text-dark mb-1">Daftar Grup</h2>
                        <p class="text-muted mb-0">Buat grup baru atau gabung ke grup yang sudah ada.</p>
                    </div>
                </div>

                <div id="group-actions" class="row g-3 mb-4">
                    <div class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                            <div class="card-body p-4 d-flex flex-column h-100">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-success bg-opacity-10 text-success p-2 px-3 rounded-3 border"><i class="fa-solid fa-plus"></i></div>
                                    <h5 class="fw-bold mb-0 text-dark">Buat Grup</h5>
                                </div>
                                <p class="text-muted small mb-4">Buat grup baru langsung dari halaman groups, tanpa dashboard terpisah.</p>

                                @if (!auth()->user()->isSubscribed())
                                    <div class="alert alert-warning small mb-3">
                                        Kamu belum punya akses grup. Beli akses dulu untuk membuat grup baru.
                                    </div>
                                    <a href="/payments" class="btn btn-success w-100 py-2 fw-semibold rounded-pill mt-auto">Beli Akses Grup</a>
                                @else
                                    <form id="formCreateGroup" method="POST" action="/groups" class="mt-auto d-flex flex-column gap-2">
                                        @csrf
                                        <input type="text" id="inputGroupName" name="name" class="form-control py-2 rounded-pill" placeholder="Nama grup baru">
                                        <button type="button" class="btn btn-success w-100 py-2 fw-semibold rounded-pill" onclick="submitCreateGroup()">Buat Sekarang</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                            <div class="card-body p-4 d-flex flex-column h-100">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-primary bg-opacity-10 text-primary p-2 px-3 rounded-3 border"><i class="fa-solid fa-link"></i></div>
                                    <h5 class="fw-bold mb-0 text-dark">Join Grup</h5>
                                </div>
                                <p class="text-muted small mb-4">Masukkan kode undangan untuk masuk ke grup yang sudah ada.</p>
                                <form id="formJoinGroup" method="POST" action="/join" class="mt-auto d-flex flex-column gap-2">
                                    @csrf
                                    <input type="text" id="inputJoinCode" name="code" class="form-control py-2 rounded-pill text-center" placeholder="Contoh: ABC-123">
                                    <button type="button" class="btn btn-primary w-100 py-2 fw-semibold rounded-pill" onclick="submitJoinGroup()">Gabung Grup</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Search, Filter, Sort Bar --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 p-3">
                    <form method="GET" action="{{ route('groups.index') }}" class="row g-2 align-items-end">
                        {{-- Search --}}
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-muted mb-2">Cari Grup</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0"
                                    placeholder="Nama grup..." value="{{ $search }}">
                            </div>
                        </div>

                        {{-- Filter --}}
                        <div class="col-12 col-sm-6 col-md-2">
                            <label class="form-label small fw-bold text-muted mb-2">Filter</label>
                            <select name="filter" class="form-select">
                                <option value="all" @selected($filter === 'all')>Semua</option>
                                <option value="owner" @selected($filter === 'owner')>Pemilik</option>
                                <option value="member" @selected($filter === 'member')>Anggota</option>
                            </select>
                        </div>

                        {{-- Sort --}}
                        <div class="col-12 col-sm-6 col-md-2">
                            <label class="form-label small fw-bold text-muted mb-2">Urutkan</label>
                            <select name="sort" class="form-select">
                                <option value="newest" @selected($sort === 'newest')>Terbaru</option>
                                <option value="oldest" @selected($sort === 'oldest')>Terlama</option>
                                <option value="name" @selected($sort === 'name')>Nama</option>
                            </select>
                        </div>

                        {{-- Buttons --}}
                        <div class="col-12 col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm fw-bold flex-grow-1">
                                <i class="fa fa-filter me-1"></i> Terapkan
                            </button>
                            <a href="{{ route('groups.index') }}" class="btn btn-light border btn-sm fw-bold">
                                <i class="fa fa-redo me-1"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Grid Kartu Grup --}}
                <div class="row g-4">
                    @forelse ($groups as $group)
                        @php
                            $memberRole = \App\Models\GroupMember::where('group_id', $group->id)
                                ->where('user_id', auth()->id())
                                ->with('role')
                                ->first();

                            // Ambil 2 huruf awal untuk inisial
                            $initials = strtoupper(substr($group->name, 0, 2));
                        @endphp

                        <div class="col-md-6 col-xl-4">
                            <div class="card h-100 p-4 border-0 shadow-sm rounded-4">

                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    {{-- Avatar Inisial --}}
                                    <div class="d-flex align-items-center justify-content-center text-white rounded-4 fw-bold shadow-sm"
                                        style="width: 56px; height: 56px; font-size: 1.25rem; background: linear-gradient(135deg, var(--tasku-primary) 0%, var(--tasku-deep) 100%);">
                                        {{ $initials }}
                                    </div>

                                    {{-- Tombol Edit (Hanya untuk Owner) --}}
                                    @if ($memberRole?->role?->is_owner)
                                        <button
                                            class="btn btn-light text-secondary rounded-circle d-flex align-items-center justify-content-center border"
                                            style="width: 35px; height: 35px;" data-bs-toggle="modal"
                                            data-bs-target="#modalEditGroup" data-id="{{ $group->id }}"
                                            data-name="{{ $group->name }}" title="Ubah Nama Grup">
                                            <i class="fas fa-pen" style="font-size: 12px;"></i>
                                        </button>
                                    @endif
                                </div>

                                <div class="mb-4">
                                    <h5 class="fw-bold text-dark mb-3 text-truncate">{{ $group->name }}</h5>

                                    {{-- Lencana Peran (Role Badge) --}}
                                    @if ($memberRole?->role)
                                        <span class="badge rounded-pill fw-bold border"
                                            style="background-color: {{ $memberRole->role->color }}15; color: {{ $memberRole->role->color }}; font-size: 11px; padding: 6px 12px; border-color: {{ $memberRole->role->color }}30 !important;">
                                            <i class="fas fa-shield-alt me-1"></i>
                                            {{ strtoupper($memberRole->role->name === 'OWNER' ? 'Pemilik' : $memberRole->role->name) }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-auto">
                                    {{-- Tombol Kelola --}}
                                    <a href="{{ route('groups.show', $group) }}"
                                        class="btn btn-light border w-100 d-flex justify-content-between align-items-center rounded-4 py-2 fw-semibold text-dark text-decoration-none">
                                        <span>Buka Grup</span>
                                        <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 26px; height: 26px;">
                                            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                                        </div>
                                    </a>
                                </div>

                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="bg-white d-inline-block p-4 rounded-circle mb-3 shadow-sm border">
                                <i class="fa fa-folder-open fa-3x text-muted opacity-25"></i>
                            </div>
                            <h5 class="text-secondary fw-bold">Belum Ada Grup</h5>
                            <p class="text-muted small">Kamu belum memiliki atau bergabung dengan grup manapun.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if ($groups->count() > 0)
                    <div class="d-flex justify-content-center mt-5 mb-4">
                        {{ $groups->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Ubah Nama Grup --}}
    <div class="modal fade" id="modalEditGroup" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold m-0 text-dark">Ubah Nama Grup</h6>
                        <button type="button" class="btn-close small" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" id="formEditGroup">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">NAMA BARU</label>
                            <input type="text" name="name" id="editGroupName"
                                class="form-control border-0 bg-light p-3 rounded-4" style="font-size: 0.9rem;"
                                placeholder="Masukkan nama grup..." required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showToast(message, type = 'danger') {
            alert(message);
        }

        function submitCreateGroup() {
            const name = document.getElementById('inputGroupName').value.trim();
            if (!name) return showToast('Isi nama grup dulu.', 'warning');
            document.getElementById('formCreateGroup').submit();
        }

        function submitJoinGroup() {
            const code = document.getElementById('inputJoinCode').value.trim();
            if (!code) return showToast('Masukkan kode undangan dulu.', 'warning');
            document.getElementById('formJoinGroup').submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modalEditGroup = document.getElementById('modalEditGroup');
            if (modalEditGroup) {
                modalEditGroup.addEventListener('show.bs.modal', function(e) {
                    const btn = e.relatedTarget;
                    document.getElementById('editGroupName').value = btn.getAttribute('data-name');
                    document.getElementById('formEditGroup').action =
                        `/groups/${btn.getAttribute('data-id')}`;
                });
            }
        });
    </script>
@endsection
