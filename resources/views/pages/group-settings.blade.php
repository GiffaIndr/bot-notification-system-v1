@extends('layout.sidebar')

@section('content')
    <div class="container-fluid pb-5 pt-3">
        <div class="row justify-content-center g-4">
            <div class="col-12 col-xxl-10">

                {{-- HEADER --}}
                <div
                    class="bg-white p-4 rounded-4 shadow-sm mb-4 border d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3 d-none d-md-flex align-items-center justify-content-center rounded-circle bg-primary text-white shadow-sm"
                            style="width: 50px; height: 50px;">
                            <i class="fa fa-cog fs-5"></i>
                        </div>
                        <div>
                            <a href="{{ route('groups.show', $group) }}"
                                class="btn btn-light btn-sm text-secondary fw-semibold rounded-3 mb-1 border">
                                <i class="fa fa-arrow-left me-2"></i> Kembali
                            </a>
                            <h2 class="fs-4 fw-bold mb-0 text-dark">Pengaturan {{ $group->name }}</h2>
                        </div>
                    </div>
                </div>

                @if ($role->is_owner)
                    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                        <div class="card-header bg-transparent border-bottom py-3 px-4">
                            <h6 class="fw-bold text-dark m-0">
                                <i class="fa fa-pen-to-square text-primary me-2"></i>Pengaturan Grup
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('groups.update', $group) }}"
                                class="row g-3 align-items-end">
                                @csrf
                                @method('PUT')
                                <div class="col-12 col-md-9">
                                    <label class="form-label small text-muted">Nama Grup</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $group->name) }}" maxlength="255" required>
                                </div>
                                <div class="col-12 col-md-3 d-grid">
                                    <button type="submit" class="btn btn-primary fw-bold">Simpan Nama</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <div class="row g-4">
                    {{-- NAV TABS --}}
                    <div class="col-12">
                        <ul class="nav nav-tabs border-bottom bg-white rounded-top-4 shadow-sm" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold" id="bots-tab" data-bs-toggle="tab"
                                    data-bs-target="#bots-content" type="button" role="tab">
                                    <i class="fa fa-robot me-2"></i> Bot Integrasi
                                </button>
                            </li>
                            @if ($role->can_manage_member)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold" id="roles-tab" data-bs-toggle="tab"
                                        data-bs-target="#roles-content" type="button" role="tab">
                                        <i class="fa fa-shield-halved me-2"></i> Roles & Permission
                                    </button>
                                </li>
                            @endif
                            @if ($role->can_generate_code)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold" id="codes-tab" data-bs-toggle="tab"
                                        data-bs-target="#codes-content" type="button" role="tab">
                                        <i class="fa fa-key me-2"></i> Kode Undangan
                                    </button>
                                </li>
                            @endif
                            @if ($role->can_edit_announcement)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold" id="categories-tab" data-bs-toggle="tab"
                                        data-bs-target="#categories-content" type="button" role="tab">
                                        <i class="fa fa-tags me-2"></i> Kategori Pengumuman
                                    </button>
                                </li>
                            @endif
                        </ul>
                    </div>

                    {{-- TAB CONTENT --}}
                    <div class="col-12">
                        <div class="tab-content">

                            {{-- BOT INTEGRATION TAB --}}
                            <div class="tab-pane fade show active" id="bots-content" role="tabpanel">
                                @if ($role->can_manage_bot)
                                    <div class="card border-0 shadow-sm rounded-4 bg-white">
                                        <div class="card-header bg-transparent border-bottom py-3 px-4">
                                            <h6 class="fw-bold text-dark m-0"><i
                                                    class="fa fa-robot text-primary me-2"></i>Integrasi
                                                Bot</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            @forelse ($group->bots as $bot)
                                                <div class="p-4 border-bottom">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="badge bg-light border text-dark text-uppercase"
                                                            style="font-size: 10px;">{{ $bot->type }}</span>
                                                        @php $isActive = $bot->type === 'whatsapp' || ($bot->type === 'discord' && $bot->discord_channel_id) || ($bot->type === 'telegram' && $bot->telegram_chat_id); @endphp
                                                        <span
                                                            class="badge {{ $isActive ? 'bg-success' : 'bg-warning' }} rounded-pill"
                                                            style="font-size: 9px;">{{ $isActive ? 'Aktif' : 'Setup' }}</span>
                                                    </div>

                                                    @if ($bot->type === 'whatsapp')
                                                        <p class="small text-muted mb-0">
                                                            Pengumuman akan dikirim ke semua nomor anggota
                                                            <strong>{{ $group->name }}</strong>.
                                                        </p>
                                                    @elseif($bot->type === 'discord')
                                                        @if ($bot->discord_channel_id)
                                                            <div class="small text-muted mb-3">
                                                                <div class="mb-2"><i class="fa fa-server me-1"></i>
                                                                    Server:
                                                                    <strong>{{ $bot->discord_server_name ?? ($discordServerName ?? '-') }}</strong>
                                                                </div>
                                                                <div><i class="fa fa-hashtag me-1"></i> Channel:
                                                                    <strong>{{ $bot->discord_channel_name ?? ($discordChannelName ?? '-') }}</strong>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <p class="small text-muted mb-3">Hubungkan Discord ke channel
                                                                target
                                                                untuk grup <strong>{{ $group->name }}</strong>.</p>
                                                        @endif

                                                        @if (session('discord_connect_command') && (int) session('discord_connect_bot_id') === (int) $bot->id)
                                                            <div class="bg-light p-3 rounded border small mb-3">
                                                                @php
                                                                    $discordCommand = session(
                                                                        'discord_connect_command',
                                                                    );
                                                                    $discordInviteLink =
                                                                        session('discord_invite_link') ?:
                                                                        $discordInviteUrl ?? null;
                                                                @endphp
                                                                <div class="fw-semibold mb-2">Langkah koneksi bot Discord
                                                                </div>
                                                                <ol class="mb-2 ps-3" style="font-size: 11px;">
                                                                    <li>Invite bot ke server Discord kamu.</li>
                                                                    <li>Jalankan command berikut di channel target:</li>
                                                                </ol>
                                                                <label class="small text-muted mb-1 d-block">Command</label>
                                                                <div class="input-group input-group-sm mb-2">
                                                                    <input type="text" class="form-control bg-white"
                                                                        id="discord_command_{{ $bot->id }}"
                                                                        value="{{ $discordCommand }}" readonly>
                                                                    <button type="button"
                                                                        class="btn btn-outline-secondary"
                                                                        onclick="copyCode('discord_command_{{ $bot->id }}')">Salin</button>
                                                                </div>

                                                                @if (!empty($discordInviteLink))
                                                                    <label class="small text-muted mb-1 d-block">Link
                                                                        invite
                                                                        bot</label>
                                                                    <div class="input-group input-group-sm mb-2">
                                                                        <input type="text"
                                                                            class="form-control bg-white"
                                                                            id="discord_invite_{{ $bot->id }}"
                                                                            value="{{ $discordInviteLink }}" readonly>
                                                                        <button type="button"
                                                                            class="btn btn-outline-secondary"
                                                                            onclick="copyCode('discord_invite_{{ $bot->id }}')">Salin</button>
                                                                        <a href="{{ $discordInviteLink }}"
                                                                            target="_blank"
                                                                            class="btn btn-outline-primary">Buka</a>
                                                                    </div>
                                                                @endif

                                                                <button type="button"
                                                                    class="btn btn-success btn-sm w-100 fw-semibold mt-2"
                                                                    onclick="claimDiscordConnect({{ $bot->id }})">Cek
                                                                    Ulang
                                                                    Koneksi Discord</button>
                                                            </div>
                                                        @endif

                                                        <form method="POST"
                                                            action="/groups/{{ $group->getRouteKey() }}/bots/{{ $bot->id }}/discord-connect">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn {{ $bot->discord_channel_id ? 'btn-outline-primary' : 'btn-primary' }} btn-sm w-100 fw-bold py-2">
                                                                {{ $bot->discord_channel_id ? 'Koneksikan Ulang Discord' : 'Mulai Koneksi Discord' }}
                                                            </button>
                                                        </form>
                                                    @elseif($bot->type === 'telegram')
                                                        @if ($bot->telegram_chat_id)
                                                            <div class="small text-muted mb-3">
                                                                <div><i class="fa fa-comments me-1"></i> Grup Telegram:
                                                                    <strong>{{ $telegramGroupName ?? $group->name }}</strong>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <p class="small text-muted mb-3">Hubungkan Telegram agar bot
                                                                bisa kirim
                                                                pengumuman ke grup <strong>{{ $group->name }}</strong>.
                                                            </p>
                                                        @endif

                                                        @if (session('telegram_connect_link') && (int) session('telegram_connect_bot_id') === (int) $bot->id)
                                                            <div class="bg-light p-3 rounded border small mb-3">
                                                                @php $telegramConnectLink = session('telegram_connect_link'); @endphp
                                                                <div class="fw-semibold mb-2">Langkah koneksi bot Telegram
                                                                </div>
                                                                <ol class="mb-2 ps-3" style="font-size: 11px;">
                                                                    <li>Buka link koneksi Telegram.</li>
                                                                    <li>Tambahkan bot ke grup target.</li>
                                                                    <li>Kembali ke halaman ini dan klik Cek Ulang Koneksi.
                                                                    </li>
                                                                </ol>
                                                                <label class="small text-muted mb-1 d-block">Link koneksi
                                                                    Telegram</label>
                                                                <div class="input-group input-group-sm mb-2">
                                                                    <input type="text" class="form-control bg-white"
                                                                        id="telegram_link_{{ $bot->id }}"
                                                                        value="{{ $telegramConnectLink }}" readonly>
                                                                    <button type="button"
                                                                        class="btn btn-outline-secondary"
                                                                        onclick="copyCode('telegram_link_{{ $bot->id }}')">Salin</button>
                                                                    <a href="{{ $telegramConnectLink }}" target="_blank"
                                                                        class="btn btn-outline-primary">Buka</a>
                                                                </div>

                                                                <button type="button"
                                                                    class="btn btn-success btn-sm w-100 fw-semibold mt-2"
                                                                    onclick="claimTelegramConnect({{ $bot->id }})">Cek
                                                                    Ulang
                                                                    Koneksi Telegram</button>
                                                            </div>
                                                        @endif

                                                        <form method="POST"
                                                            action="/groups/{{ $group->getRouteKey() }}/bots/{{ $bot->id }}/telegram-connect">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn {{ $bot->telegram_chat_id ? 'btn-outline-primary' : 'btn-primary' }} btn-sm w-100 fw-bold py-2">
                                                                {{ $bot->telegram_chat_id ? 'Koneksikan Ulang Telegram' : 'Mulai Koneksi Telegram' }}
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @empty
                                                <div class="p-4 text-center text-muted">
                                                    <p class="small mb-0">Belum ada bot terintegrasi.</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning rounded-4" role="alert">
                                        <i class="fa fa-lock me-2"></i> Anda tidak memiliki akses untuk mengelola bot.
                                    </div>
                                @endif
                            </div>

                            {{-- ROLES TAB --}}
                            @if ($role->can_manage_member)
                                <div class="tab-pane fade" id="roles-content" role="tabpanel">
                                    <div class="card border-0 shadow-sm rounded-4 bg-white">
                                        <div
                                            class="card-header bg-transparent border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                                            <h6 class="fw-bold text-dark m-0"><i
                                                    class="fa fa-shield-halved text-primary me-2"></i>Kelola Roles &
                                                Permissions</h6>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#modalCreateRole">
                                                <i class="fa fa-plus me-1"></i> Buat Role
                                            </button>
                                        </div>
                                        <div class="card-body p-4">
                                            @forelse ($group->roles as $groupRole)
                                                <div class="mb-4 p-3 bg-light rounded border">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <h6 class="fw-bold text-dark mb-1">{{ $groupRole->name }}</h6>
                                                            <span class="badge rounded-pill"
                                                                style="background-color: {{ $groupRole->color }}; font-size: 9px;">{{ $groupRole->color }}</span>
                                                            @if ($groupRole->is_owner)
                                                                <span class="badge bg-danger ms-1">Owner</span>
                                                            @endif
                                                        </div>
                                                        @if (!$groupRole->is_owner)
                                                            <button class="btn btn-sm btn-link text-danger p-0"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalDeleteRole{{ $groupRole->id }}">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>

                                                    {{-- Permissions Grid --}}
                                                    <form method="POST"
                                                        action="{{ route('groups.roles.update', [$group, $groupRole]) }}"
                                                        class="mt-3">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row g-3">
                                                            <div class="col-12 col-sm-8">
                                                                <label class="form-label small fw-semibold">Nama
                                                                    Role</label>
                                                                <input type="text" name="name"
                                                                    class="form-control form-control-sm"
                                                                    value="{{ $groupRole->name }}" required>
                                                            </div>
                                                            <div class="col-12 col-sm-4">
                                                                <label class="form-label small fw-semibold">Warna</label>
                                                                <input type="color" name="color"
                                                                    class="form-control form-control-color form-control-sm"
                                                                    value="{{ $groupRole->color }}" required>
                                                            </div>
                                                            <div class="col-12 col-sm-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="can_create_announcement"
                                                                        {{ $groupRole->can_create_announcement ? 'checked' : '' }}>
                                                                    <label class="form-check-label small">
                                                                        Buat Pengumuman
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 col-sm-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="can_edit_announcement"
                                                                        {{ $groupRole->can_edit_announcement ? 'checked' : '' }}>
                                                                    <label class="form-check-label small">
                                                                        Edit Kategori & Info
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 col-sm-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="can_manage_bot"
                                                                        {{ $groupRole->can_manage_bot ? 'checked' : '' }}>
                                                                    <label class="form-check-label small">
                                                                        Kelola Bot Integrasi
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 col-sm-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="can_manage_member"
                                                                        {{ $groupRole->can_manage_member ? 'checked' : '' }}>
                                                                    <label class="form-check-label small">
                                                                        Kelola Member & Role
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 col-sm-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="can_generate_code"
                                                                        {{ $groupRole->can_generate_code ? 'checked' : '' }}>
                                                                    <label class="form-check-label small">
                                                                        Generate Kode Undangan
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <button type="submit"
                                                            class="btn btn-sm btn-primary mt-3 w-100">Simpan
                                                            Perubahan</button>
                                                    </form>
                                                </div>
                                            @empty
                                                <div class="text-center text-muted py-4">
                                                    <p class="small mb-0">Belum ada role yang dibuat.</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Create Role --}}
                                <div class="modal fade" id="modalCreateRole" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 rounded-4">
                                            <div class="modal-header border-bottom">
                                                <h5 class="modal-title fw-bold">Buat Role Baru</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('groups.roles.store', $group) }}">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Nama Role</label>
                                                        <input type="text" name="name" class="form-control"
                                                            placeholder="Contoh: Moderator" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Warna Badge</label>
                                                        <input type="color" name="color"
                                                            class="form-control form-control-color" value="#6f42c1"
                                                            required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top">
                                                    <button type="button" class="btn btn-light"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Buat Role</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Delete Role --}}
                                @foreach ($group->roles as $groupRole)
                                    @if (!$groupRole->is_owner)
                                        <div class="modal fade" id="modalDeleteRole{{ $groupRole->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 rounded-4">
                                                    <div class="modal-header border-bottom">
                                                        <h5 class="modal-title fw-bold text-danger">Hapus Role</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="mb-0">Apakah Anda yakin ingin menghapus role
                                                            <strong>{{ $groupRole->name }}</strong>?
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer border-top">
                                                        <button type="button" class="btn btn-light"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <form method="POST"
                                                            action="{{ route('groups.roles.destroy', [$group, $groupRole]) }}"
                                                            style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif

                            {{-- INVITATION CODES TAB --}}
                            @if ($role->can_generate_code)
                                <div class="tab-pane fade" id="codes-content" role="tabpanel">
                                    <div class="card border-0 shadow-sm rounded-4 bg-white">
                                        <div class="card-header bg-transparent border-bottom py-3 px-4">
                                            <h6 class="fw-bold text-dark m-0"><i
                                                    class="fa fa-key text-primary me-2"></i>Kode
                                                Undangan</h6>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="mb-4">
                                                <label
                                                    class="small fw-bold text-warning-emphasis text-uppercase mb-2 d-block">Editor
                                                    Access (PJ)</label>
                                                <div class="input-group input-group-sm mb-2 shadow-xs">
                                                    <input type="text"
                                                        class="form-control text-center fw-bold bg-light"
                                                        value="{{ $group->invitation_code_pj }}" id="code_pj" readonly>
                                                    <button type="button" class="btn btn-warning text-white"
                                                        onclick="copyCode('code_pj')"><i class="fa fa-copy"></i></button>
                                                </div>
                                                <form method="POST"
                                                    action="/groups/{{ $group->getRouteKey() }}/generate-code">
                                                    @csrf <input type="hidden" name="type" value="pj">
                                                    <button
                                                        class="btn btn-link btn-sm text-warning p-0 fw-bold text-decoration-none"
                                                        style="font-size: 11px;">Perbarui Kode PJ</button>
                                                </form>
                                            </div>

                                            <div>
                                                <label
                                                    class="small fw-bold text-secondary text-uppercase mb-2 d-block">Member
                                                    Access</label>
                                                <div class="input-group input-group-sm mb-2 shadow-xs">
                                                    <input type="text"
                                                        class="form-control text-center fw-bold bg-light"
                                                        value="{{ $group->invitation_code_member }}" id="code_member"
                                                        readonly>
                                                    <button type="button" class="btn btn-secondary"
                                                        onclick="copyCode('code_member')"><i
                                                            class="fa fa-copy"></i></button>
                                                </div>
                                                <form method="POST"
                                                    action="/groups/{{ $group->getRouteKey() }}/generate-code">
                                                    @csrf <input type="hidden" name="type" value="member">
                                                    <button
                                                        class="btn btn-link btn-sm text-secondary p-0 fw-bold text-decoration-none"
                                                        style="font-size: 11px;">Perbarui Kode Member</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- ANNOUNCEMENT CATEGORIES TAB --}}
                            @if ($role->can_edit_announcement)
                                <div class="tab-pane fade" id="categories-content" role="tabpanel">
                                    <div class="card border-0 shadow-sm rounded-4 bg-white">
                                        <div
                                            class="card-header bg-transparent border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                                            <div class="d-flex justify-content-between align-items-center w-100">
                                                <h6 class="fw-bold text-dark m-0"><i
                                                        class="fa fa-tags text-primary me-2"></i>Kategori Pengumuman</h6>
                                                <span
                                                    class="badge bg-light text-dark border">{{ $categories->count() }}/5</span>
                                            </div>
                                        </div>
                                        <div class="card-body p-4">
                                            @if ($categories->count() < 5)
                                                <form method="POST"
                                                    action="{{ route('groups.categories.store', $group) }}"
                                                    class="mb-4">
                                                    @csrf
                                                    <label class="small fw-bold text-muted mb-2 d-block">Tambah kategori
                                                        baru</label>
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" name="name" class="form-control"
                                                            placeholder="Contoh: Perkuliahan" maxlength="50" required>
                                                        <button type="submit" class="btn btn-primary">Tambah</button>
                                                    </div>
                                                </form>
                                            @endif

                                            @forelse ($categories as $category)
                                                <div
                                                    class="d-flex align-items-center gap-2 mb-3 p-3 bg-light rounded border">
                                                    <form method="POST"
                                                        action="{{ route('groups.categories.update', [$group, $category]) }}"
                                                        class="d-flex align-items-center gap-2 flex-grow-1">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="text" name="name"
                                                            class="form-control form-control-sm"
                                                            value="{{ $category->name }}" maxlength="50" required>
                                                        <button type="submit"
                                                            class="btn btn-light btn-sm border">Simpan</button>
                                                    </form>
                                                    <form method="POST"
                                                        action="{{ route('groups.categories.destroy', [$group, $category]) }}"
                                                        onsubmit="return confirm('Hapus kategori ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-outline-danger btn-sm">Hapus</button>
                                                    </form>
                                                </div>
                                            @empty
                                                <p class="text-muted small mb-0">Belum ada kategori. Tambahkan untuk
                                                    mengelola
                                                    pengumuman lebih rapi.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function copyCode(id) {
            const el = document.getElementById(id);
            navigator.clipboard.writeText(el.value);
            notify('Kode disalin!', 'success');
        }

        async function claimDiscordConnect(botId) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const groupId = {{ $group->id }};

            try {
                const response = await fetch(`/groups/${groupId}/bots/${botId}/discord-connect/claim`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                const payload = await response.json();
                notify(payload.message || (response.ok ? 'Discord berhasil diklaim.' : 'Gagal cek claim Discord.'),
                    response.ok ? 'success' : 'warning');

                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                notify('Terjadi error saat cek claim Discord.', 'danger');
            }
        }

        async function claimTelegramConnect(botId) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const groupId = {{ $group->id }};

            try {
                const response = await fetch(`/groups/${groupId}/bots/${botId}/telegram-connect/claim`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                const payload = await response.json();
                notify(payload.message || (response.ok ? 'Telegram berhasil diklaim.' : 'Gagal cek claim Telegram.'),
                    response.ok ? 'success' : 'warning');

                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                notify('Terjadi error saat cek claim Telegram.', 'danger');
            }
        }

        function notify(message, type = 'info') {
            if (typeof window.showToast === 'function') {
                window.showToast(message, type);
                return;
            }
            alert(message);
        }
    </script>
@endsection
