@extends('layout.cdn')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="/dashboard" class="text-muted text-decoration-none">
                <i class="fa fa-arrow-left me-1"></i> Dashboard
            </a>
            <h3 class="mt-1 mb-0 fw-bold">{{ $group->name }}</h3>
        </div>
        <span class="badge fs-6 px-3 py-2" style="background-color: {{ $role->color }}">
            <i class="fa fa-shield-halved me-1"></i> {{ $role->name }}
        </span>
    </div>

    <div class="row g-4">

        {{-- Daftar Announcement --}}
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header fw-bold d-flex justify-content-between align-items-center bg-white border-bottom">
                    <span><i class="fa fa-bullhorn text-primary me-2"></i>Daftar Announcement</span>
                    <div class="d-flex gap-2">
                        <a href="/groups/{{ $group->id }}/logs" class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-clock-rotate-left me-1"></i> Log
                        </a>
                        @if ($role->can_create_announcement)
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
                                <i class="fa fa-plus me-1"></i> Buat
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @forelse ($announcements as $announcement)
                        <div class="card mb-3 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">{{ $announcement->title }}</h6>
                                        <p class="mb-2 text-muted">{{ $announcement->content }}</p>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <span class="badge bg-light text-dark">
                                                <i class="fa fa-user me-1"></i>{{ $announcement->user->name }}
                                            </span>
                                            <span class="badge bg-light text-dark">
                                                <i
                                                    class="fa fa-clock me-1"></i>{{ $announcement->created_at->format('d M Y, H:i') }}
                                            </span>
                                            @if ($announcement->scheduled_at)
                                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                                    <i
                                                        class="fa fa-calendar me-1"></i>{{ $announcement->scheduled_at->format('d M Y, H:i') }}
                                                </span>
                                            @endif
                                            @if ($announcement->repeat !== 'none')
                                                <span class="badge bg-success bg-opacity-10 text-success">
                                                    <i class="fa fa-rotate me-1"></i>
                                                    {{ match ($announcement->repeat) {
                                                        'daily' => 'Setiap Hari',
                                                        'weekly' => 'Setiap Minggu',
                                                        'monthly' => 'Setiap Bulan',
                                                    } }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($role->can_edit_announcement)
                                        <div class="d-flex gap-2 ms-3">
                                            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                                                data-bs-target="#modalEdit" data-id="{{ $announcement->id }}"
                                                data-title="{{ $announcement->title }}"
                                                data-content="{{ $announcement->content }}"
                                                data-scheduled="{{ $announcement->scheduled_at?->format('Y-m-d\TH:i') }}"
                                                data-repeat="{{ $announcement->repeat }}">
                                                <i class="fa fa-pen"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete({{ $announcement->id }})">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                            <form id="deleteForm{{ $announcement->id }}" method="POST"
                                                action="/groups/{{ $group->id }}/announcements/{{ $announcement->id }}"
                                                class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada announcement.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>


        {{-- Sidebar --}}
        <div class="col-md-4">

            {{-- Bot Integration --}}
            @if ($role->can_manage_bot)
                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-bold bg-white border-bottom">
                        <i class="fa fa-robot text-primary me-2"></i>Bot Integration
                    </div>
                    <div class="card-body p-0">
                        @forelse ($group->bots as $bot)
                            <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">

                                {{-- WhatsApp --}}
                                @if ($bot->type === 'whatsapp')
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="badge bg-success me-2">
                                                <i class="fab fa-whatsapp me-1"></i>WhatsApp
                                            </span>
                                            <small class="text-muted">Otomatis ke semua member</small>
                                        </div>
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            <i class="fa fa-circle-check me-1"></i>Aktif
                                        </span>
                                    </div>

                                    {{-- Discord --}}
                                @elseif ($bot->type === 'discord')
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div>
                                            <span class="badge bg-primary me-2">
                                                <i class="fab fa-discord me-1"></i>Discord
                                            </span>
                                            @if ($bot->discord_channel_id)
                                                <small class="text-muted">#{{ $discordChannelName }}</small>
                                            @else
                                                <small class="text-muted">Belum terhubung</small>
                                            @endif
                                        </div>
                                        @if ($bot->discord_channel_id)
                                            <span class="badge bg-success bg-opacity-10 text-success">
                                                <i class="fa fa-circle-check me-1"></i>Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning">
                                                <i class="fa fa-triangle-exclamation me-1"></i>Belum Setup
                                            </span>
                                        @endif
                                    </div>

                                    @php
                                        $inviteUrl =
                                            'https://discord.com/oauth2/authorize?client_id=' .
                                            config('services.discord.client_id') .
                                            '&permissions=3072&scope=bot&state=' .
                                            $group->id .
                                            '&redirect_uri=' .
                                            urlencode(config('services.discord.redirect_uri')) .
                                            '&response_type=code';
                                    @endphp

                                    <a href="{{ $inviteUrl }}" target="_blank"
                                        onclick="showToast('Membuka halaman invite Discord...', 'info')"
                                        class="btn btn-sm btn-outline-primary w-100 mb-3">
                                        <i class="fab fa-discord me-1"></i> Invite Bot ke Server Discord
                                    </a>

                                    <form method="POST"
                                        action="/groups/{{ $group->id }}/bots/{{ $bot->id }}/channel"
                                        onsubmit="handleChannelSave(event, this)">
                                        @csrf
                                        @method('PUT')
                                        <label class="form-label small fw-semibold">Discord Channel ID</label>
                                        <div class="input-group">
                                            <input type="text" name="discord_channel_id" id="discordChannelInput"
                                                class="form-control form-control-sm" value="{{ $bot->discord_channel_id }}"
                                                placeholder="1234567890123456">
                                            <button class="btn btn-sm btn-primary">
                                                <i class="fa fa-floppy-disk"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fa fa-circle-info me-1"></i>
                                            Klik kanan channel → Copy Channel ID
                                        </small>
                                    </form>

                                    {{-- Telegram --}}
                                @elseif ($bot->type === 'telegram')
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div>
                                            <span class="badge bg-info me-2">
                                                <i class="fab fa-telegram me-1"></i>Telegram
                                            </span>
                                            @if ($bot->telegram_chat_id)
                                                <small class="text-muted">{{ $telegramGroupName }}</small>
                                            @else
                                                <small class="text-muted">Belum terhubung</small>
                                            @endif
                                        </div>
                                        @if ($bot->telegram_chat_id)
                                            <span class="badge bg-success bg-opacity-10 text-success">
                                                <i class="fa fa-circle-check me-1"></i>Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning">
                                                <i class="fa fa-triangle-exclamation me-1"></i>Belum Setup
                                            </span>
                                        @endif
                                    </div>

                                    @if (!$bot->telegram_chat_id)
                                        <div class="alert alert-info py-2 small mb-3">
                                            <strong><i class="fa fa-list-ol me-1"></i>Langkah-langkah:</strong><br>
                                            1. Buat group di Telegram<br>
                                            2. Add bot <strong>{{ config('services.telegram.username') }}</strong><br>
                                            3. Ketik <code>/start</code> di group<br>
                                            4. Klik tombol di bawah
                                        </div>
                                        <a href="/groups/{{ $group->id }}/bots/{{ $bot->id }}/fetch-telegram-chat"
                                            class="btn btn-sm btn-info text-white w-100 mb-3"
                                            onclick="showToast('Mencari Chat ID...', 'info')">
                                            <i class="fab fa-telegram me-1"></i> Dapatkan Chat ID Otomatis
                                        </a>
                                    @else
                                        <div class="alert alert-success py-2 small mb-3">
                                            <i class="fa fa-circle-check me-1"></i>
                                            Bot sudah terhubung ke group Telegram!
                                        </div>
                                        <a href="/groups/{{ $group->id }}/bots/{{ $bot->id }}/fetch-telegram-chat"
                                            class="btn btn-sm btn-outline-info w-100 mb-3"
                                            onclick="showToast('Memperbarui Chat ID...', 'info')">
                                            <i class="fa fa-rotate me-1"></i> Perbarui Chat ID
                                        </a>
                                    @endif

                                    <form method="POST"
                                        action="/groups/{{ $group->id }}/bots/{{ $bot->id }}/telegram-chat"
                                        onsubmit="handleTelegramSave(event, this)">
                                        @csrf
                                        @method('PUT')
                                        <label class="form-label small fw-semibold">Atau input manual</label>
                                        <div class="input-group">
                                            <input type="text" name="telegram_chat_id" id="telegramChatInput"
                                                class="form-control form-control-sm" value="{{ $bot->telegram_chat_id }}"
                                                placeholder="-1001234567890">
                                            <button class="btn btn-sm btn-info text-white">
                                                <i class="fa fa-floppy-disk"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fa fa-circle-info me-1"></i>
                                            Chat ID group diawali minus (-)
                                        </small>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fa fa-robot fa-2x text-muted mb-2"></i>
                                <p class="text-muted small mb-0">Tidak ada bot aktif.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            {{-- Invitation Code --}}
            @if ($role->can_generate_code)
                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-bold bg-white border-bottom">
                        <i class="fa fa-key text-warning me-2"></i>Invitation Code
                    </div>
                    <div class="card-body">
                        <label class="form-label fw-semibold small">
                            <i class="fa fa-user-tie me-1 text-success"></i>Kode PJ
                        </label>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control form-control-sm"
                                value="{{ $group->invitation_code_pj }}" id="code_pj" readonly>
                            <button class="btn btn-sm btn-outline-secondary" onclick="copyCode('code_pj')">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                        <form method="POST" action="/groups/{{ $group->id }}/generate-code" class="mb-4"
                            onsubmit="showToast('Kode PJ berhasil diperbarui!', 'success')">
                            @csrf
                            <input type="hidden" name="type" value="pj">
                            <button class="btn btn-sm btn-warning w-100">
                                <i class="fa fa-rotate me-1"></i> Generate Ulang
                            </button>
                        </form>

                        <label class="form-label fw-semibold small">
                            <i class="fa fa-user me-1 text-secondary"></i>Kode Member
                        </label>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control form-control-sm"
                                value="{{ $group->invitation_code_member }}" id="code_member" readonly>
                            <button class="btn btn-sm btn-outline-secondary" onclick="copyCode('code_member')">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                        <form method="POST" action="/groups/{{ $group->id }}/generate-code"
                            onsubmit="showToast('Kode Member berhasil diperbarui!', 'success')">
                            @csrf
                            <input type="hidden" name="type" value="member">
                            <button class="btn btn-sm btn-warning w-100">
                                <i class="fa fa-rotate me-1"></i> Generate Ulang
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Manage Roles --}}
            @if ($role->can_manage_member)
                <div class="card shadow-sm mb-4">
                    <div
                        class="card-header fw-bold bg-white border-bottom d-flex justify-content-between align-items-center">
                        <span><i class="fa fa-shield-halved text-primary me-2"></i>Manage Roles</span>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateRole">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                    <div class="card-body p-0">
                        @foreach ($roles as $r)
                            <div
                                class="d-flex align-items-center justify-content-between p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex align-items-center gap-2">
                                    <span
                                        style="width:12px; height:12px; border-radius:50%; background:{{ $r->color }}; display:inline-block;"></span>
                                    <span class="small fw-semibold">{{ $r->name }}</span>
                                    @if ($r->is_owner)
                                        <span class="badge bg-primary bg-opacity-10 text-primary"
                                            style="font-size:10px">Owner</span>
                                    @endif
                                </div>
                                @if (!$r->is_owner)
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-outline-warning"
                                            style="padding: 2px 7px; font-size: 11px" data-bs-toggle="modal"
                                            data-bs-target="#modalEditRole" data-id="{{ $r->id }}"
                                            data-name="{{ $r->name }}" data-color="{{ $r->color }}"
                                            data-can_create="{{ $r->can_create_announcement ? '1' : '0' }}"
                                            data-can_edit="{{ $r->can_edit_announcement ? '1' : '0' }}"
                                            data-can_member="{{ $r->can_manage_member ? '1' : '0' }}"
                                            data-can_code="{{ $r->can_generate_code ? '1' : '0' }}"
                                            data-can_bot="{{ $r->can_manage_bot ? '1' : '0' }}">
                                            <i class="fa fa-pen"></i>
                                        </button>
                                        <form method="POST"
                                            action="/groups/{{ $group->id }}/roles/{{ $r->id }}"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"
                                                style="padding: 2px 7px; font-size: 11px"
                                                onclick="return confirm('Yakin hapus role ini?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Daftar Member --}}
            <div class="card shadow-sm">
                <div class="card-header fw-bold bg-white border-bottom">
                    <i class="fa fa-users text-primary me-2"></i>Anggota Group
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th>Role</th>
                                @if ($role->can_manage_member)
                                    <th>Ubah Role</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($members as $m)
                                <tr>
                                    <td class="small">{{ $m->user->name }}</td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $m->role->color }}">
                                            {{ $m->role->name }}
                                        </span>
                                    </td>
                                    @if ($role->can_manage_member)
                                        <td>
                                            @if ($m->role->is_owner)
                                                <span class="text-muted small">
                                                    <i class="fa fa-lock me-1"></i>Owner
                                                </span>
                                            @else
                                                <form method="POST" action="/groups/{{ $group->id }}/roles/assign">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $m->user_id }}">
                                                    <select name="role_id" class="form-select form-select-sm"
                                                        onchange="this.form.submit()"
                                                        style="font-size: 11px; min-width: 100px; border-color: {{ $m->role->color }}">
                                                        @foreach ($roles->where('is_owner', false) as $r)
                                                            <option value="{{ $r->id }}"
                                                                {{ $m->role_id == $r->id ? 'selected' : '' }}>
                                                                {{ $r->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Modal Create Announcement --}}
    <div class="modal fade" id="modalCreate" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-bullhorn me-2 text-primary"></i>Buat Announcement
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="/groups/{{ $group->id }}/announcements"
                    onsubmit="handleCreate(event, this)">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul</label>
                            <input type="text" name="title" id="createTitle" class="form-control"
                                placeholder="Judul Announcement" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Isi</label>
                            <textarea name="content" id="createContent" class="form-control" rows="3" placeholder="Isi Announcement"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-calendar me-1"></i>Jadwal Kirim
                            </label>
                            <input type="datetime-local" name="scheduled_at" class="form-control">
                            <small class="text-muted">Kosongkan jika ingin langsung tampil.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-rotate me-1"></i>Pengulangan
                            </label>
                            <select name="repeat" class="form-select">
                                <option value="none">Tidak Berulang</option>
                                <option value="daily">Setiap Hari</option>
                                <option value="weekly">Setiap Minggu</option>
                                <option value="monthly">Setiap Bulan</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa fa-xmark me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-paper-plane me-1"></i>Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Announcement --}}
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-pen me-2 text-warning"></i>Edit Announcement
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formEdit" onsubmit="handleEdit(event, this)">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul</label>
                            <input type="text" name="title" id="editTitle" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Isi</label>
                            <textarea name="content" id="editContent" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-calendar me-1"></i>Jadwal Kirim
                            </label>
                            <input type="datetime-local" name="scheduled_at" id="editScheduled" class="form-control">
                            <small class="text-muted">Kosongkan jika ingin langsung tampil.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-rotate me-1"></i>Pengulangan
                            </label>
                            <select name="repeat" id="editRepeat" class="form-select">
                                <option value="none">Tidak Berulang</option>
                                <option value="daily">Setiap Hari</option>
                                <option value="weekly">Setiap Minggu</option>
                                <option value="monthly">Setiap Bulan</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa fa-xmark me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fa fa-floppy-disk me-1"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Create Role --}}
    <div class="modal fade" id="modalCreateRole" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-shield-halved me-2 text-primary"></i>Buat Role Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="/groups/{{ $group->id }}/roles">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Role</label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Bendahara"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Warna Role</label>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="color" name="color" class="form-control form-control-color"
                                    value="#6c757d">
                                <small class="text-muted">Pilih warna untuk role ini</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Permission</label>
                            <div class="d-flex flex-column gap-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="can_create_announcement"
                                        id="cr_create">
                                    <label class="form-check-label small" for="cr_create">
                                        <i class="fa fa-plus me-1 text-primary"></i>Buat Announcement
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="can_edit_announcement"
                                        id="cr_edit">
                                    <label class="form-check-label small" for="cr_edit">
                                        <i class="fa fa-pen me-1 text-warning"></i>Edit/Delete Announcement
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="can_manage_member"
                                        id="cr_member">
                                    <label class="form-check-label small" for="cr_member">
                                        <i class="fa fa-users me-1 text-success"></i>Manage Member & Role
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="can_generate_code"
                                        id="cr_code">
                                    <label class="form-check-label small" for="cr_code">
                                        <i class="fa fa-key me-1 text-warning"></i>Generate Invitation Code
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="can_manage_bot"
                                        id="cr_bot">
                                    <label class="form-check-label small" for="cr_bot">
                                        <i class="fa fa-robot me-1 text-info"></i>Manage Bot
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa fa-xmark me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-plus me-1"></i>Buat Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Role --}}
    <div class="modal fade" id="modalEditRole" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-pen me-2 text-warning"></i>Edit Role
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formEditRole">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Role</label>
                            <input type="text" name="name" id="editRoleName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Warna Role</label>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="color" name="color" id="editRoleColor"
                                    class="form-control form-control-color">
                                <small class="text-muted">Pilih warna untuk role ini</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Permission</label>
                            <div class="d-flex flex-column gap-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="can_create_announcement"
                                        id="er_create">
                                    <label class="form-check-label small" for="er_create">
                                        <i class="fa fa-plus me-1 text-primary"></i>Buat Announcement
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="can_edit_announcement"
                                        id="er_edit">
                                    <label class="form-check-label small" for="er_edit">
                                        <i class="fa fa-pen me-1 text-warning"></i>Edit/Delete Announcement
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="can_manage_member"
                                        id="er_member">
                                    <label class="form-check-label small" for="er_member">
                                        <i class="fa fa-users me-1 text-success"></i>Manage Member & Role
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="can_generate_code"
                                        id="er_code">
                                    <label class="form-check-label small" for="er_code">
                                        <i class="fa fa-key me-1 text-warning"></i>Generate Invitation Code
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="can_manage_bot"
                                        id="er_bot">
                                    <label class="form-check-label small" for="er_bot">
                                        <i class="fa fa-robot me-1 text-info"></i>Manage Bot
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa fa-xmark me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fa fa-floppy-disk me-1"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
        <div id="toast" class="toast align-items-center text-white border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body fw-semibold" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => showToast('{{ session('success') }}', 'success'));
        </script>
    @endif
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => showToast('{{ session('error') }}', 'danger'));
        </script>
    @endif

    <script>
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toastMessage');
            toast.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'bg-primary');
            toast.classList.add(`bg-${type}`);
            toastMsg.innerText = message;
            new bootstrap.Toast(toast, {
                delay: 3000
            }).show();
        }

        function copyCode(id) {
            const el = document.getElementById(id);
            navigator.clipboard.writeText(el.value || el.innerText);
            showToast('Kode berhasil disalin!', 'success');
        }

        function confirmDelete(id) {
            if (confirm('Yakin hapus announcement ini?')) {
                showToast('Menghapus announcement...', 'danger');
                setTimeout(() => document.getElementById('deleteForm' + id).submit(), 500);
            }
        }

        function handleCreate(event, form) {
            const title = document.getElementById('createTitle').value.trim();
            const content = document.getElementById('createContent').value.trim();
            if (!title || !content) {
                event.preventDefault();
                showToast('Judul dan isi wajib diisi!', 'warning');
                return;
            }
            showToast('Membuat announcement...', 'primary');
        }

        function handleEdit(event, form) {
            const title = document.getElementById('editTitle').value.trim();
            const content = document.getElementById('editContent').value.trim();
            if (!title || !content) {
                event.preventDefault();
                showToast('Judul dan isi wajib diisi!', 'warning');
                return;
            }
            showToast('Menyimpan perubahan...', 'primary');
        }

        function handleChannelSave(event, form) {
            const channelId = document.getElementById('discordChannelInput').value.trim();
            if (!channelId) {
                event.preventDefault();
                showToast('Channel ID wajib diisi!', 'warning');
                return;
            }
            showToast('Menyimpan Channel ID...', 'primary');
        }

        function handleTelegramSave(event, form) {
            const chatId = document.getElementById('telegramChatInput').value.trim();
            if (!chatId) {
                event.preventDefault();
                showToast('Chat ID wajib diisi!', 'warning');
                return;
            }
            showToast('Menyimpan Chat ID...', 'primary');
        }

        // Modal Edit Announcement
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

        // Modal Edit Role
        const modalEditRole = document.getElementById('modalEditRole');
        modalEditRole.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            document.getElementById('editRoleName').value = btn.getAttribute('data-name');
            document.getElementById('editRoleColor').value = btn.getAttribute('data-color');
            document.getElementById('er_create').checked = btn.getAttribute('data-can_create') === '1';
            document.getElementById('er_edit').checked = btn.getAttribute('data-can_edit') === '1';
            document.getElementById('er_member').checked = btn.getAttribute('data-can_member') === '1';
            document.getElementById('er_code').checked = btn.getAttribute('data-can_code') === '1';
            document.getElementById('er_bot').checked = btn.getAttribute('data-can_bot') === '1';
            document.getElementById('formEditRole').action =
                `/groups/{{ $group->id }}/roles/${btn.getAttribute('data-id')}`;
        });
    </script>

@endsection
