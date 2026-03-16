@extends('layout.cdn')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="/dashboard" class="text-muted text-decoration-none">← Dashboard</a>
            <h3 class="mt-1 mb-0">{{ $group->name }}</h3>
        </div>
        <span class="badge bg-primary fs-6">Role: {{ ucfirst($role) }}</span>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">

        {{-- Daftar Announcement --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                    <span>📋 Daftar Announcement</span>
                    @if (in_array($role, ['komti', 'pj']))
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
                            + Buat Announcement
                        </button>
                    @endif
                </div>
                <div class="card-body">

                    @forelse ($announcements as $announcement)
                        <div class="card mb-3 border">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">{{ $announcement->title }}</h6>
                                        <p class="mb-2">{{ $announcement->content }}</p>
                                        <div class="d-flex gap-3">
                                            <small class="text-muted">
                                                👤 {{ $announcement->user->name }} •
                                                {{ $announcement->created_at->format('d M Y, H:i') }}
                                            </small>
                                            @if ($announcement->scheduled_at)
                                                <small class="text-primary">
                                                    🕐 {{ $announcement->scheduled_at->format('d M Y, H:i') }}
                                                </small>
                                            @endif
                                            @if ($announcement->repeat !== 'none')
                                                <small class="text-success">
                                                    🔁
                                                    {{ match ($announcement->repeat) {
                                                        'daily' => 'Setiap Hari',
                                                        'weekly' => 'Setiap Minggu',
                                                        'monthly' => 'Setiap Bulan',
                                                    } }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>

                                    @if (in_array($role, ['komti', 'pj']))
                                        <div class="d-flex gap-2 ms-3">
                                            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                                                data-bs-target="#modalEdit" data-id="{{ $announcement->id }}"
                                                data-title="{{ $announcement->title }}"
                                                data-content="{{ $announcement->content }}"
                                                data-scheduled="{{ $announcement->scheduled_at?->format('Y-m-d\TH:i') }}"
                                                data-repeat="{{ $announcement->repeat }}">
                                                Edit
                                            </button>
                                            <form method="POST"
                                                action="/groups/{{ $group->id }}/announcements/{{ $announcement->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Yakin hapus?')">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Belum ada announcement.</p>
                    @endforelse

                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-md-4">

            @if ($role === 'komti')
                {{-- Invitation Code --}}
                <div class="card mb-4">
                    <div class="card-header fw-bold">🔑 Invitation Code</div>
                    <div class="card-body">

                        <label class="form-label fw-semibold small">Kode PJ</label>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control form-control-sm"
                                value="{{ $group->invitation_code_pj }}" id="code_pj" readonly>
                            <button class="btn btn-sm btn-outline-secondary" onclick="copyCode('code_pj')">Copy</button>
                        </div>
                        <form method="POST" action="/groups/{{ $group->id }}/generate-code" class="mb-3">
                            @csrf
                            <input type="hidden" name="type" value="pj">
                            <button class="btn btn-sm btn-warning w-100">🔄 Generate Ulang</button>
                        </form>

                        <label class="form-label fw-semibold small">Kode Member</label>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control form-control-sm"
                                value="{{ $group->invitation_code_member }}" id="code_member" readonly>
                            <button class="btn btn-sm btn-outline-secondary" onclick="copyCode('code_member')">Copy</button>
                        </div>
                        <form method="POST" action="/groups/{{ $group->id }}/generate-code">
                            @csrf
                            <input type="hidden" name="type" value="member">
                            <button class="btn btn-sm btn-warning w-100">🔄 Generate Ulang</button>
                        </form>

                    </div>
                </div>

                {{-- Bot Integration --}}
                <div class="card mb-4">
                    <div class="card-header fw-bold">🤖 Bot Integration</div>
                    <div class="card-body">
                        @forelse ($group->bots as $bot)
                            <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                                <div>
                                    @if ($bot->type === 'whatsapp')
                                        <span class="badge bg-success me-1">WhatsApp</span>
                                    @else
                                        <span class="badge bg-primary me-1">Discord</span>
                                    @endif
                                    <small id="bot_{{ $bot->id }}">{{ $bot->invitation_code }}</small>
                                </div>
                                <button class="btn btn-sm btn-outline-secondary"
                                    onclick="copyCode('bot_{{ $bot->id }}')">Copy</button>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">Tidak ada bot aktif.</p>
                        @endforelse
                    </div>
                </div>
            @endif

            {{-- Daftar Member --}}
            <div class="card">
                <div class="card-header fw-bold">👥 Anggota Group</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($members as $m)
                                <tr>
                                    <td>{{ $m->name }}</td>
                                    <td>
                                        @if ($m->pivot->role === 'komti')
                                            <span class="badge bg-primary">Komti</span>
                                        @elseif ($m->pivot->role === 'pj')
                                            <span class="badge bg-success">PJ</span>
                                        @else
                                            <span class="badge bg-secondary">Member</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

    {{-- Modal Create --}}
    <div class="modal fade" id="modalCreate" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">📢 Buat Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="/groups/{{ $group->id }}/announcements">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            <input type="text" name="title" class="form-control" placeholder="Judul Announcement"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Isi</label>
                            <textarea name="content" class="form-control" rows="3" placeholder="Isi Announcement" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jadwal Kirim</label>
                            <input type="datetime-local" name="scheduled_at" class="form-control">
                            <small class="text-muted">Kosongkan jika ingin langsung tampil.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pengulangan</label>
                            <select name="repeat" class="form-select">
                                <option value="none">Tidak Berulang</option>
                                <option value="daily">Setiap Hari</option>
                                <option value="weekly">Setiap Minggu</option>
                                <option value="monthly">Setiap Bulan</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">✏️ Edit Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formEdit">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            <input type="text" name="title" id="editTitle" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Isi</label>
                            <textarea name="content" id="editContent" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jadwal Kirim</label>
                            <input type="datetime-local" name="scheduled_at" id="editScheduled" class="form-control">
                            <small class="text-muted">Kosongkan jika ingin langsung tampil.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pengulangan</label>
                            <select name="repeat" id="editRepeat" class="form-select">
                                <option value="none">Tidak Berulang</option>
                                <option value="daily">Setiap Hari</option>
                                <option value="weekly">Setiap Minggu</option>
                                <option value="monthly">Setiap Bulan</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Isi form edit saat modal dibuka
        const modalEdit = document.getElementById('modalEdit');
        modalEdit.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            const id = btn.getAttribute('data-id');
            const title = btn.getAttribute('data-title');
            const content = btn.getAttribute('data-content');
            const scheduled = btn.getAttribute('data-scheduled');
            const repeat = btn.getAttribute('data-repeat');

            document.getElementById('editTitle').value = title;
            document.getElementById('editContent').value = content;
            document.getElementById('editScheduled').value = scheduled ?? '';
            document.getElementById('editRepeat').value = repeat ?? 'none';
            document.getElementById('formEdit').action = `/groups/{{ $group->id }}/announcements/${id}`;
        });

        function copyCode(id) {
            const el = document.getElementById(id);
            navigator.clipboard.writeText(el.value || el.innerText);
            alert('Kode berhasil disalin!');
        }
    </script>

@endsection
