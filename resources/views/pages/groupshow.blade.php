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
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border-radius: 24px;
        border: 1px solid rgba(226, 232, 240, 0.7) !important;
        background: #ffffff;
        display: flex;
        flex-direction: column;
    }

    .group-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06) !important;
        border-color: #6366f1 !important;
    }

    /* Avatar Inisial Ber-Style */
    .avatar-initial {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.2rem;
        color: white;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        /* Gradient Dinamis */
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        box-shadow: 0 8px 16px -4px rgba(99, 102, 241, 0.4);
    }

    /* Role Badge - Soft & Modern */
    .role-pill {
        font-size: 11px;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Manage Button Custom */
    .btn-manage {
        background: #f1f5f9;
        color: #1e293b;
        border: none;
        border-radius: 14px;
        font-weight: 700;
        padding: 12px;
        transition: 0.3s;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-manage:hover {
        background: #1e293b;
        color: #ffffff;
    }

    .btn-manage i {
        background: rgba(255, 255, 255, 0.2);
        padding: 5px;
        border-radius: 8px;
        font-size: 10px;
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

            <div class="d-flex justify-content-between align-items-end mb-5">
                <div>
                    <h2 class="fw-800 text-dark mb-1" style="letter-spacing: -1.5px;">Workspace Hub</h2>
                    <p class="text-muted mb-0">Kelola kolaborasi tim dalam satu pintu.</p>
                </div>
            </div>

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
                        <div class="card group-card h-100 p-4 border-0">

                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="avatar-initial">
                                    {{ $initials }}
                                </div>

                                @if ($memberRole?->role?->is_owner)
                                    <button class="btn btn-edit-tool"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditGroup"
                                            data-id="{{ $group->id }}"
                                            data-name="{{ $group->name }}">
                                        <i class="fas fa-pen-nib"></i>
                                    </button>
                                @endif
                            </div>

                            <div class="mb-4">
                                <h5 class="fw-bold text-dark mb-2 text-truncate">{{ $group->name }}</h5>

                                @if ($memberRole?->role)
                                    <span class="role-pill" style="background-color: {{ $memberRole->role->color }}15; color: {{ $memberRole->role->color }};">
                                        <i class="fas fa-shield-alt" style="font-size: 8px;"></i>
                                        {{ $memberRole->role->name }}
                                    </span>
                                @endif
                            </div>

                            <div class="mt-auto">
                                <a href="/groups/{{ $group->id }}" class="btn-manage text-decoration-none">
                                    <span>Manage Project</span>
                                    <i class="fas fa-chevron-right text-white bg-dark"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    @endforelse
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditGroup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 28px; overflow: hidden;">
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold m-0 text-dark">Rename Group</h6>
                    <button type="button" class="btn-close small" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formEditGroup">
                    @csrf
                    @method('PUT')
                    <input type="text" name="name" id="editGroupName"
                           class="form-control border-0 bg-light p-3 rounded-4 mb-3"
                           style="font-size: 0.9rem;" placeholder="Nama baru..." required>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">
                        Update Name
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
