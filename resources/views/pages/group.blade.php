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
                            <a href="/dashboard"
                                class="btn btn-light btn-sm text-secondary fw-semibold rounded-3 mb-1 border">
                                <i class="fa fa-arrow-left me-2"></i> Dashboard
                            </a>
                            <h2 class="fs-4 fw-bold mb-0 text-dark">{{ $group->name }}</h2>
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
                    {{-- KOLOM KIRI: Daftar Pengumuman (Limit 5) --}}
                    <div class="col-12 col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 bg-white">
                            <div
                                class="card-header bg-transparent border-bottom p-4 d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold text-dark m-0"><i class="fa fa-bullhorn text-primary me-2"></i>Pengumuman
                                </h5>
                                <div class="d-flex gap-2">
                                    <a href="/groups/{{ $group->id }}/logs"
                                        class="btn btn-sm btn-light border fw-bold px-3">Log</a>
                                    @if ($role->can_create_announcement)
                                        <button class="btn btn-sm btn-primary fw-bold px-3 shadow-sm" data-bs-toggle="modal"
                                            data-bs-target="#modalCreate">
                                            <i class="fa fa-plus-circle me-1"></i> Buat Baru
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body p-4">
                                @forelse ($announcements->take(5) as $announcement)
                                    <div
                                        class="card mb-3 border shadow-none rounded-4 {{ $announcement->is_pinned ? 'border-warning bg-warning-subtle bg-opacity-10' : '' }}">
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
                                                    @if ($announcement->deadline_mode && $announcement->deadline_at)
                                                        <span class="badge bg-light text-danger border mb-2 ms-1">
                                                            <i class="fa fa-hourglass-half me-1"></i>Tenggat:
                                                            {{ $announcement->deadline_at->format('d M Y, H:i') }}
                                                        </span>
                                                    @endif
                                                    <span class="badge bg-light text-secondary border mb-2 ms-1">
                                                        <i class="fa fa-paper-plane me-1"></i>Kirim pertama:
                                                        {{ $announcement->scheduled_at?->format('d M Y, H:i') }}
                                                    </span>
                                                    @if ($announcement->reminder_enabled && $announcement->reminder_at)
                                                        <span class="badge bg-light text-warning border mb-2 ms-1">
                                                            <i class="fa fa-bell me-1"></i>Pengingat:
                                                            {{ $announcement->reminder_at->format('d M Y, H:i') }}
                                                        </span>
                                                    @endif
                                                    <p class="text-secondary small mb-2 text-truncate">
                                                        {{ $announcement->content }}</p>
                                                    <div class="d-flex gap-2 text-muted" style="font-size: 10px;">
                                                        <span><i
                                                                class="fa fa-user me-1"></i>{{ $announcement->user->name }}</span>
                                                        <span><i
                                                                class="fa fa-clock me-1"></i>{{ $announcement->created_at->format('d M, H:i') }}</span>
                                                    </div>
                                                </div>
                                                @if ($role->can_edit_announcement)
                                                    <div class="dropdown">
                                                        <button class="btn btn-light btn-sm border"
                                                            data-bs-toggle="dropdown"><i
                                                                class="fa fa-ellipsis-v"></i></button>
                                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                            <li>
                                                                <form method="POST"
                                                                    action="/groups/{{ $group->id }}/announcements/{{ $announcement->id }}/pin">
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
                                        action="/groups/{{ $group->id }}/announcements/{{ $announcement->id }}"
                                        class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @empty
                                    <div class="text-center py-5 text-muted small">Belum ada pengumuman.</div>
                                @endforelse
                            </div>
                            @if ($announcements->count() > 5)
                                <div class="card-footer bg-light border-0 p-3 text-center rounded-bottom-4">
                                    <a href="{{ route('groups.announcements.index', $group) }}"
                                        class="fw-bold text-primary text-decoration-none small">
                                        Lihat Semua Pengumuman ({{ $announcements->count() }}) <i
                                            class="fa fa-chevron-right ms-1"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- KOLOM KANAN: Integrasi & Kode --}}
                    <div class="col-12 col-lg-4">

                        {{-- BOT INTEGRATION --}}
                        @if ($role->can_manage_bot)
                            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                                <div class="card-header bg-transparent border-bottom py-3 px-4">
                                    <h6 class="fw-bold text-dark m-0"><i class="fa fa-robot text-primary me-2"></i>Integrasi
                                        Bot</h6>
                                </div>
                                <div class="card-body p-0">
                                    @foreach ($group->bots as $bot)
                                        <div class="p-3 border-bottom">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-light border text-dark text-uppercase"
                                                    style="font-size: 10px;">{{ $bot->type }}</span>
                                                @php $isActive = $bot->type === 'whatsapp' || ($bot->type === 'discord' && $bot->discord_channel_id) || ($bot->type === 'telegram' && $bot->telegram_chat_id); @endphp
                                                <span
                                                    class="badge {{ $isActive ? 'bg-success' : 'bg-warning' }} rounded-pill"
                                                    style="font-size: 9px;">{{ $isActive ? 'Aktif' : 'Setup' }}</span>
                                            </div>

                                            @if ($bot->type === 'whatsapp')
                                                <p class="small text-muted mb-2">
                                                    Pengumuman akan dikirim ke semua nomor anggota
                                                    <strong>{{ $group->name }}</strong>.
                                                </p>
                                            @elseif($bot->type === 'discord')
                                                @if ($bot->discord_channel_id)
                                                    <div class="small text-muted mb-2">
                                                        <div><i class="fa fa-server me-1"></i> Server:
                                                            <strong>{{ $bot->discord_server_name ?? ($discordServerName ?? '-') }}</strong>
                                                        </div>
                                                        <div><i class="fa fa-hashtag me-1"></i> Channel:
                                                            <strong>{{ $bot->discord_channel_name ?? ($discordChannelName ?? '-') }}</strong>
                                                        </div>
                                                    </div>
                                                @else
                                                    <p class="small text-muted mb-2">Hubungkan Discord ke channel target
                                                        untuk grup <strong>{{ $group->name }}</strong>.</p>
                                                @endif

                                                @if (session('discord_connect_command') && (int) session('discord_connect_bot_id') === (int) $bot->id)
                                                    <div class="bg-light p-3 rounded border small mb-2">
                                                        @php
                                                            $discordCommand = session('discord_connect_command');
                                                            $discordInviteLink =
                                                                session('discord_invite_link') ?:
                                                                $discordInviteUrl ?? null;
                                                        @endphp
                                                        <div class="fw-semibold mb-2">Langkah koneksi bot Discord</div>
                                                        <ol class="mb-2 ps-3" style="font-size: 11px;">
                                                            <li>Invite bot ke server Discord kamu.</li>
                                                            <li>Jalankan command berikut di channel target:</li>
                                                        </ol>
                                                        <label class="small text-muted mb-1 d-block">Command</label>
                                                        <div class="input-group input-group-sm mb-2">
                                                            <input type="text" class="form-control bg-white"
                                                                id="discord_command_{{ $bot->id }}"
                                                                value="{{ $discordCommand }}" readonly>
                                                            <button type="button" class="btn btn-outline-secondary"
                                                                onclick="copyCode('discord_command_{{ $bot->id }}')">Salin</button>
                                                        </div>

                                                        @if (!empty($discordInviteLink))
                                                            <label class="small text-muted mb-1 d-block">Link invite
                                                                bot</label>
                                                            <div class="input-group input-group-sm mb-2">
                                                                <input type="text" class="form-control bg-white"
                                                                    id="discord_invite_{{ $bot->id }}"
                                                                    value="{{ $discordInviteLink }}" readonly>
                                                                <button type="button" class="btn btn-outline-secondary"
                                                                    onclick="copyCode('discord_invite_{{ $bot->id }}')">Salin</button>
                                                                <a href="{{ $discordInviteLink }}" target="_blank"
                                                                    class="btn btn-outline-primary">Buka</a>
                                                            </div>
                                                        @endif

                                                        <button type="button"
                                                            class="btn btn-success btn-sm w-100 fw-semibold mt-1"
                                                            onclick="claimDiscordConnect({{ $bot->id }})">Cek Ulang
                                                            Koneksi Discord</button>
                                                    </div>
                                                @endif

                                                <form method="POST"
                                                    action="/groups/{{ $group->id }}/bots/{{ $bot->id }}/discord-connect">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn {{ $bot->discord_channel_id ? 'btn-outline-primary' : 'btn-primary' }} btn-sm w-100 fw-bold py-2">
                                                        {{ $bot->discord_channel_id ? 'Koneksikan Ulang Discord' : 'Mulai Koneksi Discord' }}
                                                    </button>
                                                </form>
                                            @elseif($bot->type === 'telegram')
                                                @if ($bot->telegram_chat_id)
                                                    <div class="small text-muted mb-2">
                                                        <div><i class="fa fa-comments me-1"></i> Grup Telegram:
                                                            <strong>{{ $telegramGroupName ?? $group->name }}</strong>
                                                        </div>
                                                    </div>
                                                @else
                                                    <p class="small text-muted mb-2">Hubungkan Telegram agar bot bisa kirim
                                                        pengumuman ke grup <strong>{{ $group->name }}</strong>.</p>
                                                @endif

                                                @if (session('telegram_connect_link') && (int) session('telegram_connect_bot_id') === (int) $bot->id)
                                                    <div class="bg-light p-3 rounded border small mb-2">
                                                        @php $telegramConnectLink = session('telegram_connect_link'); @endphp
                                                        <div class="fw-semibold mb-2">Langkah koneksi bot Telegram</div>
                                                        <ol class="mb-2 ps-3" style="font-size: 11px;">
                                                            <li>Buka link koneksi Telegram.</li>
                                                            <li>Tambahkan bot ke grup target.</li>
                                                            <li>Kembali ke halaman ini dan klik Cek Ulang Koneksi.</li>
                                                        </ol>
                                                        <label class="small text-muted mb-1 d-block">Link koneksi
                                                            Telegram</label>
                                                        <div class="input-group input-group-sm mb-2">
                                                            <input type="text" class="form-control bg-white"
                                                                id="telegram_link_{{ $bot->id }}"
                                                                value="{{ $telegramConnectLink }}" readonly>
                                                            <button type="button" class="btn btn-outline-secondary"
                                                                onclick="copyCode('telegram_link_{{ $bot->id }}')">Salin</button>
                                                            <a href="{{ $telegramConnectLink }}" target="_blank"
                                                                class="btn btn-outline-primary">Buka</a>
                                                        </div>

                                                        <button type="button"
                                                            class="btn btn-success btn-sm w-100 fw-semibold mt-1"
                                                            onclick="claimTelegramConnect({{ $bot->id }})">Cek Ulang
                                                            Koneksi Telegram</button>
                                                    </div>
                                                @endif

                                                <form method="POST"
                                                    action="/groups/{{ $group->id }}/bots/{{ $bot->id }}/telegram-connect">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn {{ $bot->telegram_chat_id ? 'btn-outline-primary' : 'btn-primary' }} btn-sm w-100 fw-bold py-2">
                                                        {{ $bot->telegram_chat_id ? 'Koneksikan Ulang Telegram' : 'Mulai Koneksi Telegram' }}
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- INVITATION CODES --}}
                        @if ($role->can_generate_code)
                            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                                <div class="card-header bg-transparent border-bottom py-3 px-4">
                                    <h6 class="fw-bold text-dark m-0"><i class="fa fa-key text-muted me-2"></i>Kode
                                        Undangan</h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="mb-4">
                                        <label
                                            class="small fw-bold text-warning-emphasis text-uppercase mb-2 d-block">Editor
                                            Access (PJ)</label>
                                        <div class="input-group input-group-sm mb-2 shadow-xs">
                                            <input type="text" class="form-control text-center fw-bold bg-light"
                                                value="{{ $group->invitation_code_pj }}" id="code_pj" readonly>
                                            <button class="btn btn-warning text-white" onclick="copyCode('code_pj')"><i
                                                    class="fa fa-copy"></i></button>
                                        </div>
                                        <form method="POST" action="/groups/{{ $group->id }}/generate-code">
                                            @csrf <input type="hidden" name="type" value="pj">
                                            <button
                                                class="btn btn-link btn-sm text-warning p-0 fw-bold text-decoration-none"
                                                style="font-size: 11px;">Perbarui Kode PJ</button>
                                        </form>
                                    </div>

                                    <div>
                                        <label class="small fw-bold text-secondary text-uppercase mb-2 d-block">Member
                                            Access</label>
                                        <div class="input-group input-group-sm mb-2 shadow-xs">
                                            <input type="text" class="form-control text-center fw-bold bg-light"
                                                value="{{ $group->invitation_code_member }}" id="code_member" readonly>
                                            <button class="btn btn-secondary" onclick="copyCode('code_member')"><i
                                                    class="fa fa-copy"></i></button>
                                        </div>
                                        <form method="POST" action="/groups/{{ $group->id }}/generate-code">
                                            @csrf <input type="hidden" name="type" value="member">
                                            <button
                                                class="btn btn-link btn-sm text-secondary p-0 fw-bold text-decoration-none"
                                                style="font-size: 11px;">Perbarui Kode Member</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($role->can_edit_announcement)
                            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                                <div class="card-header bg-transparent border-bottom py-3 px-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="fw-bold text-dark m-0"><i
                                                class="fa fa-tags text-muted me-2"></i>Kategori
                                            Pengumuman</h6>
                                        <span class="badge bg-light text-dark border">{{ $categories->count() }}/5</span>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    @if ($categories->count() < 5)
                                        <form method="POST" action="{{ route('groups.categories.store', $group) }}"
                                            class="mb-3">
                                            @csrf
                                            <label class="small text-muted mb-2">Tambah kategori baru</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" name="name" class="form-control"
                                                    placeholder="Contoh: Perkuliahan" maxlength="50" required>
                                                <button type="submit" class="btn btn-primary">Tambah</button>
                                            </div>
                                        </form>
                                    @endif

                                    @forelse ($categories as $category)
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <form method="POST"
                                                action="{{ route('groups.categories.update', [$group, $category]) }}"
                                                class="d-flex align-items-center gap-2 flex-grow-1">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="name" class="form-control form-control-sm"
                                                    value="{{ $category->name }}" maxlength="50" required>
                                                <button type="submit" class="btn btn-light btn-sm border">Simpan</button>
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
                                        <p class="text-muted small mb-0">Belum ada kategori. Tambahkan untuk mengelola
                                            pengumuman lebih rapi.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                        {{-- Daftar Anggota (Kualitas Enterprise) --}}
                        <div class="card border-0 shadow-sm rounded-4 bg-white">
                            <div class="card-header bg-transparent border-bottom p-4">
                                <h6 class="fw-bold text-dark m-0"><i class="fa fa-users text-muted me-2"></i>Anggota
                                    ({{ $members->count() }})</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    @foreach ($members->take(10) as $m)
                                        <div
                                            class="list-group-item px-4 py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                                    style="width: 32px; height: 32px; font-size: 10px;">
                                                    {{ strtoupper(substr($m->user->name, 0, 1)) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="fw-bold text-dark small text-truncate"
                                                        style="max-width: 120px;">{{ $m->user->name }}</div>
                                                    <span class="badge rounded-pill p-0 text-primary"
                                                        style="font-size: 9px;">{{ $m->role->name }}</span>
                                                </div>
                                            </div>
                                            @if ($role->can_manage_member && !$m->role->is_owner)
                                                <form method="POST"
                                                    action="/groups/{{ $group->id }}/members/{{ $m->id }}"
                                                    onsubmit="return confirm('Kick member ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger p-0"><i
                                                            class="fa fa-user-xmark small"></i></button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 p-3 text-center">
                                <small class="text-muted fw-semibold">Kelola Anggota Secara Penuh di Dashboard</small>
                            </div>
                        </div>

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
        });

        function notify(message, type = 'info') {
            if (typeof window.showToast === 'function') {
                window.showToast(message, type);
                return;
            }
            alert(message);
        }

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
                    <form method="POST" action="/groups/{{ $group->id }}/announcements"
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
