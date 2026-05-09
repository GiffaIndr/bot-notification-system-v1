@extends('layout.sidebar')

@section('content')
    <div class="container-fluid pb-5 pt-3">
        <div class="row justify-content-center g-4">
            <div class="col-12 col-xxl-10">

                <div
                    class="bg-white p-4 rounded-4 shadow-sm mb-4 border d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3 d-none d-md-flex align-items-center justify-content-center rounded-circle bg-primary text-white shadow-sm"
                            style="width: 50px; height: 50px;">
                            <i class="fa fa-users fs-5"></i>
                        </div>
                        <div>
                            <a href="{{ route('groups.show', $group) }}"
                                class="btn btn-light btn-sm text-secondary fw-semibold rounded-3 mb-1 border">
                                <i class="fa fa-arrow-left me-2"></i> Kembali
                            </a>
                            <h2 class="fs-4 fw-bold mb-0 text-dark">Anggota {{ $group->name }}</h2>
                        </div>
                    </div>
                    <span class="badge bg-light text-dark border px-3 py-2">Total: {{ $members->total() }} anggota</span>
                </div>

                <div class="card border-0 shadow-sm rounded-4 bg-white">
                    <div class="card-header bg-transparent border-bottom p-4">
                        <form method="GET" action="{{ route('groups.members.index', $group) }}" class="row g-2">
                            <div class="col-12 col-md-10">
                                <input type="text" name="q" value="{{ $search }}" class="form-control"
                                    placeholder="Cari nama atau email anggota...">
                            </div>
                            <div class="col-12 col-md-2 d-grid">
                                <button type="submit" class="btn btn-primary">Cari</button>
                            </div>
                        </form>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-3">Nama</th>
                                        <th class="py-3">Email</th>
                                        <th class="py-3">Role</th>
                                        <th class="py-3">Bergabung</th>
                                        @if ($role->can_manage_member)
                                            <th class="py-3 text-end pe-4">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($members as $member)
                                        <tr>
                                            <td class="px-4 py-3 fw-semibold">{{ $member->user->name }}</td>
                                            <td class="py-3 text-muted">{{ $member->user->email }}</td>
                                            <td class="py-3">
                                                <span class="badge rounded-pill"
                                                    style="background-color: {{ $member->role->color }}; color: #fff;">
                                                    {{ $member->role->name }}
                                                </span>
                                            </td>
                                            <td class="py-3 text-muted">{{ $member->created_at?->format('d M Y, H:i') }}
                                            </td>
                                            @if ($role->can_manage_member)
                                                <td class="py-3 text-end pe-4">
                                                    @if (!$member->role->is_owner)
                                                        <form method="POST"
                                                            action="/groups/{{ $group->getRouteKey() }}/members/{{ $member->id }}"
                                                            onsubmit="return confirm('Kick member ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="fa fa-user-xmark me-1"></i> Kick
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-5">Tidak ada anggota
                                                ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if ($members->hasPages())
                        <div class="card-footer bg-transparent border-0 p-3">
                            {{ $members->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
