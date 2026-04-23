@extends('layout.sidebar')

@section('content')

<div class="container-fluid pb-5 pt-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="fw-bold fs-4 text-dark mb-4">Dashboard</h2>
                    <p class="text-muted mb-0">Kelola kolaborasi tim dalam satu pintu.</p>
                </div>
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
                                    <button class="btn btn-light text-secondary rounded-circle d-flex align-items-center justify-content-center border"
                                            style="width: 35px; height: 35px;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditGroup"
                                            data-id="{{ $group->id }}"
                                            data-name="{{ $group->name }}"
                                            title="Ubah Nama Grup">
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
                                <a href="/groups/{{ $group->id }}" class="btn btn-light border w-100 d-flex justify-content-between align-items-center rounded-4 py-2 fw-semibold text-dark text-decoration-none">
                                    <span>Kelola Grup</span>
                                    <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
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
                               class="form-control border-0 bg-light p-3 rounded-4"
                               style="font-size: 0.9rem;" placeholder="Masukkan nama grup..." required>
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
    document.addEventListener('DOMContentLoaded', function() {
        const modalEditGroup = document.getElementById('modalEditGroup');
        if(modalEditGroup) {
            modalEditGroup.addEventListener('show.bs.modal', function(e) {
                const btn = e.relatedTarget;
                document.getElementById('editGroupName').value = btn.getAttribute('data-name');
                document.getElementById('formEditGroup').action = `/groups/${btn.getAttribute('data-id')}`;
            });
        }
    });
</script>
@endsection
