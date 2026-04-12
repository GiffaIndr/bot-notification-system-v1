@extends('layout.sidebar')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');

    .content-wrapper {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fafc;
    }

    /* Group Card Modern */
    .group-card {
        transition: all 0.3s ease;
        border-radius: 18px;
        border: 1px solid #e7edf5;
        background: #fff;
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.04);
        display: flex;
        flex-direction: column;
        padding: 20px;
    }

    .group-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
        border-color: #0d6efd;
    }

    /* Avatar Inisial */
    .avatar-initial {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.1rem;
        color: white;
        background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);
        flex-shrink: 0;
    }

    /* Role Badge - Soft & Modern */
    .role-pill {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-edit-tool {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        color: #94a3b8;
        border-radius: 12px;
        width: 34px;
        height: 34px;
        transition: 0.2s;
    }

    .btn-edit-tool:hover {
        color: #6366f1;
        background: #eef2ff;
    }
</style>

<div class="container-fluid py-5 content-wrapper">
    <div class="row justify-content-center">
        <div class="col-lg-11">

            <!-- Header -->
            <div class="mb-5">
                <h2 class="fw-bold text-dark mb-2" style="font-size: 2rem;">Semua Workspace</h2>
                <p class="text-muted mb-0">Kelola dan akses semua group atau workspace kamu dalam satu tempat.</p>
            </div>

            <!-- Groups Grid -->
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

                    <div class="col-md-6 col-lg-4">
                        <div class="group-card">
                            <!-- Header dengan Avatar & Edit Button -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="avatar-initial">
                                    {{ $initials }}
                                </div>
                                @if ($memberRole?->role?->is_owner)
                                    <button class="btn btn-sm btn-light border" 
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditGroup"
                                            data-id="{{ $group->id }}"
                                            data-name="{{ $group->name }}">
                                        <i class="fas fa-pen text-primary" style="font-size: 0.8rem;"></i>
                                    </button>
                                @endif
                            </div>

                            <!-- Group Info -->
                            <div class="mb-4">
                                <h5 class="fw-bold text-dark mb-2">{{ $group->name }}</h5>
                                @if ($memberRole?->role)
                                    <span class="role-pill" style="background-color: {{ $memberRole->role->color }}15; color: {{ $memberRole->role->color }};">
                                        {{ $memberRole->role->name }}
                                    </span>
                                @endif
                            </div>

                            <!-- Action Button -->
                            <div class="mt-auto">
                                <a href="/groups/{{ $group->id }}" class="btn btn-primary w-100 rounded-pill fw-bold btn-sm">
                                    Buka Workspace
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <h6 class="fw-bold mb-2">Belum ada workspace</h6>
                            <p class="text-muted small mb-3">Mulai dengan membuat group baru atau join dengan invitation code.</p>
                            <a href="/dashboard" class="btn btn-primary rounded-pill px-4 fw-bold">
                                Ke Dashboard
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditGroup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
            <div class="modal-header border-0 p-4">
                <h6 class="modal-title fw-bold">Edit Nama Workspace</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <form method="POST" id="formEditGroup">
                    @csrf
                    @method('PUT')
                    <input type="text" name="name" id="editGroupName"
                           class="form-control rounded-3 p-3 mb-4"
                           placeholder="Nama workspace baru..." required>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">
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
