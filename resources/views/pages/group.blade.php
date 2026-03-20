    @extends('layout.sidebar')

    @section('content')

        <style>
            .header-wrapper {
                background: white;
                padding: 1.5rem;
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
                margin-bottom: 2rem;
                border: 1px solid rgba(0, 0, 0, 0.02);
            }

            .btn-back {
                display: inline-flex;
                align-items: center;
                padding: 6px 12px;
                background: #f8fafc;
                border-radius: 10px;
                color: #64748b !important;
                font-size: 0.85rem;
                font-weight: 600;
                transition: all 0.2s;
                margin-bottom: 8px;
            }

            .btn-back:hover {
                background: #e2e8f0;
                color: #475569 !important;
                transform: translateX(-3px);
            }

            .group-title {
                font-size: 1.75rem;
                letter-spacing: -0.5px;
                color: #1e293b;
            }

            .role-badge-pill {
                display: inline-flex;
                align-items: center;
                padding: 8px 16px;
                border-radius: 12px;
                color: white;
                font-weight: 700;
                font-size: 0.9rem;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                border: 2px solid rgba(255, 255, 255, 0.2);
            }

            .announcement-container {
                border: none;
                border-radius: 20px;
                overflow: hidden;
            }

            .announcement-header {
                background: white;
                padding: 1.25rem 1.5rem;
                border-bottom: 1px solid #f1f5f9 !important;
            }

            /* Card per Item Announcement */
            .announcement-item {
                border: 1px solid #f1f5f9 !important;
                border-left: 5px solid #6366f1 !important;
                /* Aksen warna ungu/primary */
                border-radius: 15px;
                transition: all 0.3s ease;
                background: #ffffff;
            }

            .announcement-item:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
                border-color: #e2e8f0 !important;
            }

            /* Style Badge Custom */
            .badge-soft {
                font-weight: 600;
                padding: 6px 12px;
                border-radius: 8px;
                font-size: 0.75rem;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }

            .badge-user {
                background: #f1f5f9;
                color: #475569;
            }

            .badge-time {
                background: #f8fafc;
                color: #64748b;
            }

            .badge-scheduled {
                background: #eef2ff;
                color: #4338ca;
            }

            .badge-repeat {
                background: #ecfdf5;
                color: #059669;
            }

            /* Action Buttons */
            .btn-action-round {
                width: 35px;
                height: 35px;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 10px;
                transition: all 0.2s;
            }

            @media (max-width: 768px) {
                .header-wrapper {
                    flex-direction: column;
                    align-items: flex-start !important;
                    gap: 1rem;
                }
            }
        </style>

        <div class="header-wrapper d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="me-3 d-none d-md-flex align-items-center justify-content-center rounded-circle shadow-sm"
                    style="width: 60px; height: 60px; background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: white;">
                    <i class="fa fa-users fs-4"></i>
                </div>

                <div>
                    <a href="/dashboard" class="btn-back text-decoration-none">
                        <i class="fa fa-arrow-left me-2"></i> Dashboard
                    </a>
                    <h2 class="group-title mb-0 fw-bold">{{ $group->name }}</h2>
                </div>
            </div>

            <div class="text-end">
                <div class="role-badge-pill" style="background-color: {{ $role->color }}">
                    <i class="fa fa-shield-halved me-2"></i>
                    {{ $role->name }}
                </div>
                <div class="text-muted small mt-1 d-none d-md-block">
                    Your current permission level
                </div>
            </div>
        </div>
        <div class="row g-4">

            {{-- Daftar Announcement --}}
            <div class="col-md-8">
                <div class="card announcement-container shadow-sm">
                    <div
                        class="card-header announcement-header fw-bold d-flex justify-content-between align-items-center bg-white">
                        <span class="fs-5 text-dark">
                            <i class="fa fa-bullhorn text-primary me-2"></i>Daftar Announcement
                        </span>
                        <div class="d-flex gap-2">
                            <a href="/groups/{{ $group->id }}/logs"
                                class="btn btn-sm btn-light text-secondary fw-bold px-3">
                                <i class="fa fa-clock-rotate-left me-1"></i> Log
                            </a>
                            @if ($role->can_create_announcement)
                                <button class="btn btn-sm btn-primary fw-bold px-3 shadow-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalCreate">
                                    <i class="fa fa-plus-circle me-1"></i> Buat Baru
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="card-body p-4">
                        @forelse ($announcements as $announcement)
                            <div class="card announcement-item mb-3 shadow-none">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold text-dark mb-1" style="font-size: 1.1rem;">
                                                {{ $announcement->title }}</h6>
                                            <p class="mb-3 text-secondary" style="font-size: 0.95rem; line-height: 1.5;">
                                                {{ $announcement->content }}
                                            </p>

                                            <div class="d-flex gap-2 flex-wrap">
                                                <span class="badge-soft badge-user">
                                                    <i class="fa fa-user-circle"></i> {{ $announcement->user->name }}
                                                </span>
                                                <span class="badge-soft badge-time">
                                                    <i class="fa fa-calendar-alt"></i>
                                                    {{ $announcement->created_at->format('d M, H:i') }}
                                                </span>

                                                @if ($announcement->scheduled_at)
                                                    <span class="badge-soft badge-scheduled border border-primary-subtle">
                                                        <i class="fa fa-clock"></i>
                                                        {{ $announcement->scheduled_at->format('d M, H:i') }}
                                                    </span>
                                                @endif

                                                @if ($announcement->repeat !== 'none')
                                                    <span class="badge-soft badge-repeat border border-success-subtle">
                                                        <i class="fa fa-sync-alt"></i>
                                                        {{ match ($announcement->repeat) {
                                                            'daily' => 'Harian',
                                                            'weekly' => 'Mingguan',
                                                            'monthly' => 'Bulanan',
                                                        } }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        @if ($role->can_edit_announcement)
                                            <div class="d-flex flex-column gap-2 ms-3">
                                                <button class="btn btn-sm btn-outline-warning btn-action-round"
                                                    data-bs-toggle="modal" data-bs-target="#modalEdit"
                                                    data-id="{{ $announcement->id }}"
                                                    data-title="{{ $announcement->title }}"
                                                    data-content="{{ $announcement->content }}"
                                                    data-scheduled="{{ $announcement->scheduled_at?->format('Y-m-d\TH:i') }}"
                                                    data-repeat="{{ $announcement->repeat }}" title="Edit">
                                                    <i class="fa fa-pen"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger btn-action-round"
                                                    onclick="confirmDelete({{ $announcement->id }})" title="Hapus">
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
                                <div class="mb-3">
                                    <i class="fa fa-bullhorn fa-4x text-light"></i>
                                </div>
                                <h5 class="text-muted fw-normal">Belum ada pengumuman hari ini.</h5>
                                <p class="text-muted small">Semua pengumuman grup akan muncul di sini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                {{-- Poll Section --}}
                <div class="card announcement-container shadow-sm mt-4">
                    <div
                        class="card-header fw-bold d-flex justify-content-between align-items-center bg-white border-bottom">
                        <span><i class="fa fa-chart-bar text-primary me-2"></i>Poll & Voting</span>
                        @if ($role->can_create_poll)
                            <button class="btn btn-sm btn-primary fw-bold px-3 shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#modalCreatePoll">
                                <i class="fa fa-plus me-1"></i> Buat Poll
                            </button>
                        @endif
                    </div>
                    <div class="card-body">
                        @forelse ($polls as $poll)
                            <div class="card mb-3 border-0 shadow-sm">
                                <div class="card-body">

                                    {{-- Header poll --}}
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="fw-bold mb-1">{{ $poll->question }}</h6>
                                            <div class="d-flex gap-2">
                                                <small class="text-muted">
                                                    <i class="fa fa-user me-1"></i>{{ $poll->user->name }}
                                                </small>
                                                @if ($poll->is_anonymous)
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary"
                                                        style="font-size:10px">
                                                        <i class="fa fa-eye-slash me-1"></i>Anonymous
                                                    </span>
                                                @else
                                                    <span class="badge bg-info bg-opacity-10 text-info"
                                                        style="font-size:10px">
                                                        <i class="fa fa-eye me-1"></i>Publik
                                                    </span>
                                                @endif
                                                @if ($poll->is_closed || $poll->isExpired())
                                                    <span class="badge bg-danger bg-opacity-10 text-danger"
                                                        style="font-size:10px">
                                                        <i class="fa fa-lock me-1"></i>Ditutup
                                                    </span>
                                                @else
                                                    <span class="badge bg-success bg-opacity-10 text-success"
                                                        style="font-size:10px">
                                                        <i class="fa fa-circle me-1"></i>Aktif
                                                    </span>
                                                @endif
                                                @if ($poll->closes_at && !$poll->is_closed)
                                                    <small class="text-muted">
                                                        <i class="fa fa-clock me-1"></i>Tutup
                                                        {{ $poll->closes_at->format('d M Y, H:i') }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>

                                        @if ($role->can_create_poll)
                                            <div class="d-flex gap-1">
                                                @if (!$poll->is_closed && !$poll->isExpired())
                                                    <form method="POST"
                                                        action="/groups/{{ $group->id }}/polls/{{ $poll->id }}/close">
                                                        @csrf
                                                        <button class="btn btn-sm btn-outline-warning"
                                                            onclick="return confirm('Tutup poll ini?')"
                                                            title="Tutup Poll">
                                                            <i class="fa fa-lock"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form method="POST"
                                                    action="/groups/{{ $group->id }}/polls/{{ $poll->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Hapus poll ini?')" title="Hapus Poll">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Options & Vote --}}
                                    @php
                                        $totalVotes = $poll->votes->count();
                                        $userVote = $poll->votes->where('user_id', auth()->id())->first();
                                        $isClosed = $poll->is_closed || $poll->isExpired();
                                    @endphp

                                    <div class="d-flex flex-column gap-2">
                                        @foreach ($poll->options as $option)
                                            @php
                                                $optionVotes = $option->votes->count();
                                                $percent =
                                                    $totalVotes > 0 ? round(($optionVotes / $totalVotes) * 100) : 0;
                                                $isMyVote = $userVote?->poll_option_id === $option->id;
                                            @endphp

                                            <div>
                                                @if (!$userVote && !$isClosed)
                                                    {{-- Belum vote & poll aktif --}}
                                                    <form method="POST"
                                                        action="/groups/{{ $group->id }}/polls/{{ $poll->id }}/vote">
                                                        @csrf
                                                        <input type="hidden" name="option_id"
                                                            value="{{ $option->id }}">
                                                        <button type="submit"
                                                            class="btn btn-sm w-100 text-start {{ $isMyVote ? 'btn-primary' : 'btn-outline-secondary' }}"
                                                            style="border-radius: 8px;">
                                                            <i
                                                                class="fa fa-circle{{ $isMyVote ? '-check' : '' }} me-2"></i>
                                                            {{ $option->label }}
                                                        </button>
                                                    </form>
                                                @else
                                                    {{-- Sudah vote atau poll closed, tampilkan hasil --}}
                                                    <div class="position-relative mb-1">
                                                        <div class="progress" style="height: 28px; border-radius: 8px;">
                                                            <div class="progress-bar {{ $isMyVote ? 'bg-primary' : 'bg-secondary bg-opacity-25' }}"
                                                                style="width: {{ $percent }}%; border-radius: 8px;">
                                                            </div>
                                                        </div>
                                                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center px-3"
                                                            style="font-size: 12px;">
                                                            <span
                                                                class="{{ $isMyVote ? 'text-white fw-bold' : 'text-dark' }}">
                                                                @if ($isMyVote)
                                                                    <i class="fa fa-circle-check me-1"></i>
                                                                @endif
                                                                {{ $option->label }}
                                                            </span>
                                                            <span
                                                                class="ms-auto {{ $isMyVote ? 'text-white' : 'text-muted' }}">
                                                                {{ $percent }}%
                                                                @if (!$poll->is_anonymous || $isClosed)
                                                                    ({{ $optionVotes }})
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>

                                                    {{-- Tampilkan siapa yang vote (kalau publik & sudah vote/closed) --}}
                                                    @if (!$poll->is_anonymous && $optionVotes > 0)
                                                        <div class="d-flex flex-wrap gap-1 mb-1">
                                                            @foreach ($option->votes as $vote)
                                                                <span class="badge bg-light text-dark border"
                                                                    style="font-size: 9px;">
                                                                    {{ $vote->user->name }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    <small class="text-muted mt-2 d-block">
                                        <i class="fa fa-users me-1"></i>{{ $totalVotes }} vote
                                    </small>

                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fa fa-chart-bar fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada poll.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>


            {{-- Modal Create Poll --}}
            <div class="modal fade" id="modalCreatePoll" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fa fa-chart-bar me-2 text-primary"></i>Buat Poll
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="/groups/{{ $group->id }}/polls">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Pertanyaan</label>
                                    <input type="text" name="question" class="form-control"
                                        placeholder="Contoh: Setuju dengan jadwal meeting?" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tipe Poll</label>
                                    <select name="type" id="pollType" class="form-select"
                                        onchange="togglePollOptions()">
                                        <option value="yes_no">Ya / Tidak</option>
                                        <option value="multiple_choice">Pilihan Ganda</option>
                                    </select>
                                </div>

                                {{-- Options untuk multiple choice --}}
                                <div id="pollOptions" class="mb-3 d-none">
                                    <label class="form-label fw-semibold">Pilihan</label>
                                    <div id="optionList">
                                        <div class="input-group mb-2">
                                            <input type="text" name="options[]" class="form-control form-control-sm"
                                                placeholder="Pilihan 1">
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="removeOption(this)">
                                                <i class="fa fa-xmark"></i>
                                            </button>
                                        </div>
                                        <div class="input-group mb-2">
                                            <input type="text" name="options[]" class="form-control form-control-sm"
                                                placeholder="Pilihan 2">
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="removeOption(this)">
                                                <i class="fa fa-xmark"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOption()">
                                        <i class="fa fa-plus me-1"></i>Tambah Pilihan
                                    </button>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fa fa-clock me-1"></i>Tutup Otomatis (opsional)
                                    </label>
                                    <input type="datetime-local" name="closes_at" class="form-control">
                                    <small class="text-muted">Kosongkan jika tidak ada batas waktu.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Visibilitas Vote</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="is_anonymous"
                                                id="publik" value="0" checked>
                                            <label class="form-check-label small" for="publik">
                                                <i class="fa fa-eye me-1 text-info"></i>Publik
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="is_anonymous"
                                                id="anonymous" value="1">
                                            <label class="form-check-label small" for="anonymous">
                                                <i class="fa fa-eye-slash me-1 text-secondary"></i>Anonymous
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
                                    <i class="fa fa-paper-plane me-1"></i>Buat Poll
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-md-4">

                {{-- Bot Integration --}}
                @if ($role->can_manage_bot)
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
                        <div class="card-header bg-white py-3 border-bottom-0">
                            <div class="d-flex align-items-center">
                                <div class="p-2 bg-primary bg-opacity-10 rounded-3 me-3">
                                    <i class="fa fa-robot text-primary"></i>
                                </div>
                                <h6 class="fw-bold mb-0" style="color: #334155;">Bot Integration</h6>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            @forelse ($group->bots as $bot)
                                <div class="p-4 {{ !$loop->last ? 'border-bottom' : '' }}" style="background: #fcfcfd;">

                                    {{-- Header Row: Badge & Status --}}
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div>
                                            @if ($bot->type === 'whatsapp')
                                                <span class="badge rounded-pill px-3 py-2"
                                                    style="background: linear-gradient(45deg, #25D366, #128C7E); box-shadow: 0 4px 10px rgba(37, 211, 102, 0.2);">
                                                    <i class="fab fa-whatsapp me-1"></i> WhatsApp
                                                </span>
                                            @elseif ($bot->type === 'discord')
                                                <span class="badge rounded-pill px-3 py-2"
                                                    style="background: linear-gradient(45deg, #5865F2, #404EED); box-shadow: 0 4px 10px rgba(88, 101, 242, 0.2);">
                                                    <i class="fab fa-discord me-1"></i> Discord
                                                </span>
                                            @elseif ($bot->type === 'telegram')
                                                <span class="badge rounded-pill px-3 py-2"
                                                    style="background: linear-gradient(45deg, #0088cc, #0077b5); box-shadow: 0 4px 10px rgba(0, 136, 204, 0.2);">
                                                    <i class="fab fa-telegram me-1"></i> Telegram
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Status Pill --}}
                                        @php
                                            $isActive =
                                                $bot->type === 'whatsapp' ||
                                                ($bot->type === 'discord' && $bot->discord_channel_id) ||
                                                ($bot->type === 'telegram' && $bot->telegram_chat_id);
                                        @endphp
                                        <span
                                            class="badge {{ $isActive ? 'text-success' : 'text-warning' }} d-flex align-items-center fw-medium"
                                            style="background: {{ $isActive ? '#ecfdf5' : '#fffbeb' }}; font-size: 11px; border: 1px solid currentColor;">
                                            <i
                                                class="fa {{ $isActive ? 'fa-circle-check' : 'fa-triangle-exclamation' }} me-1"></i>
                                            {{ $isActive ? 'Aktif' : 'Belum Setup' }}
                                        </span>
                                    </div>

                                    {{-- Content Section --}}
                                    <div class="content-body">
                                        @if ($bot->type === 'whatsapp')
                                            <div class="text-center p-3 bg-white border rounded-3 border-dashed">
                                                <small class="text-muted d-block">Notifikasi otomatis terkirim ke semua
                                                    nomor member yang terdaftar.</small>
                                            </div>
                                        @elseif ($bot->type === 'discord')
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

                                            <div class="mb-3 p-3 bg-white border rounded-3 shadow-xs">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="small fw-bold text-dark flex-grow-1">
                                                        <i class="fa fa-hashtag text-muted me-1"></i>
                                                        {{ $discordChannelName ?? 'Channel belum diset' }}
                                                    </div>
                                                    <a href="{{ $inviteUrl }}" target="_blank"
                                                        onclick="showToast('Membuka halaman invite...', 'info')"
                                                        class="text-primary small text-decoration-none fw-bold">
                                                        Invite Bot <i class="fa fa-external-link ms-1"
                                                            style="font-size: 10px;"></i>
                                                    </a>
                                                </div>

                                                <form method="POST"
                                                    action="/groups/{{ $group->id }}/bots/{{ $bot->id }}/channel"
                                                    onsubmit="handleChannelSave(event, this)">
                                                    @csrf @method('PUT')
                                                    <div class="input-group">
                                                        <input type="text" name="discord_channel_id"
                                                            class="form-control form-control-sm border-end-0 bg-light"
                                                            style="font-size: 12px;"
                                                            value="{{ $bot->discord_channel_id }}"
                                                            placeholder="Channel ID (misal: 12345...)">
                                                        <button class="btn btn-sm btn-primary px-3 shadow-none"><i
                                                                class="fa fa-floppy-disk"></i></button>
                                                    </div>
                                                    <small class="text-muted mt-2 d-block"
                                                        style="font-size: 10px;">Gunakan Developer Mode di Discord untuk
                                                        menyalin ID Channel.</small>
                                                </form>
                                            </div>
                                        @elseif ($bot->type === 'telegram')
                                            <div class="p-3 bg-white border rounded-3">
                                                @if (!$bot->telegram_chat_id)
                                                    <div
                                                        class="bg-light p-2 rounded-2 mb-3 small border-start border-info border-4">
                                                        <ol class="mb-0 ps-3">
                                                            <li>Tambahkan
                                                                <strong>{{ config('services.telegram.username') }}</strong>
                                                                ke grup.
                                                            </li>
                                                            <li>Ketik <code>/start</code> di grup tersebut.</li>
                                                        </ol>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center mb-2 small fw-bold text-success">
                                                        <i class="fa fa-check-circle me-2"></i>
                                                        {{ $telegramGroupName ?? 'Terhubung' }}
                                                    </div>
                                                @endif

                                                <div class="d-grid mb-3">
                                                    <a href="/groups/{{ $group->id }}/bots/{{ $bot->id }}/fetch-telegram-chat"
                                                        class="btn btn-sm {{ !$bot->telegram_chat_id ? 'btn-info text-white' : 'btn-outline-info' }} fw-bold"
                                                        onclick="showToast('Mencari Chat ID...', 'info')">
                                                        <i class="fa fa-sync-alt me-1"></i>
                                                        {{ !$bot->telegram_chat_id ? 'Hubungkan Otomatis' : 'Perbarui Koneksi' }}
                                                    </a>
                                                </div>

                                                <form method="POST"
                                                    action="/groups/{{ $group->id }}/bots/{{ $bot->id }}/telegram-chat"
                                                    onsubmit="handleTelegramSave(event, this)">
                                                    @csrf @method('PUT')
                                                    <label class="small fw-bold text-muted mb-1"
                                                        style="font-size: 10px;">INPUT MANUAL ID</label>
                                                    <div class="input-group">
                                                        <input type="text" name="telegram_chat_id"
                                                            class="form-control form-control-sm bg-light"
                                                            value="{{ $bot->telegram_chat_id }}"
                                                            placeholder="-100xxxxxxx">
                                                        <button class="btn btn-sm btn-info text-white px-3 shadow-none"><i
                                                                class="fa fa-floppy-disk"></i></button>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" alt="No Bot"
                                        style="width: 60px; opacity: 0.3; filter: grayscale(1);">
                                    <p class="text-muted small mt-3 mb-0">Belum ada integrasi bot yang aktif.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif

                {{-- Invitation Code --}}
                @if ($role->can_generate_code)
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                        <div class="card-header bg-white py-3 border-bottom-0">
                            <div class="d-flex align-items-center">
                                <div class="p-2 bg-warning bg-opacity-10 rounded-3 me-3">
                                    <i class="fa fa-key text-warning"></i>
                                </div>
                                <h6 class="fw-bold mb-0" style="color: #334155;">Access Invitation</h6>
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            {{-- Slot Kode Editor --}}
                            <div class="p-3 mb-4 rounded-4"
                                style="background-color: #fffbeb; border: 1px dashed #fef3c7;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="fw-bold small text-warning text-uppercase"
                                        style="letter-spacing: 0.5px;">
                                        <i class="fa fa-user-tie me-1"></i> Editor Access
                                    </label>
                                    <span class="badge bg-warning text-dark shadow-xs" style="font-size: 10px;">High
                                        Privilege</span>
                                </div>

                                <div class="input-group mb-3 shadow-sm">
                                    <input type="text" class="form-control border-0 bg-white fw-bold text-center"
                                        style="font-family: 'Monaco', 'Consolas', monospace; letter-spacing: 2px; color: #92400e;"
                                        value="{{ $group->invitation_code_pj }}" id="code_pj" readonly>
                                    <button class="btn btn-white border-0 text-warning px-3" type="button"
                                        onclick="copyCode('code_pj')">
                                        <i class="fa fa-copy"></i>
                                    </button>
                                </div>

                                <form method="POST" action="/groups/{{ $group->id }}/generate-code"
                                    onsubmit="showToast('Kode Editor berhasil diperbarui!', 'success')">
                                    @csrf
                                    <input type="hidden" name="type" value="pj">
                                    <button class="btn btn-sm btn-warning w-100 fw-bold shadow-xs text-white"
                                        style="border-radius: 8px;">
                                        <i class="fa fa-rotate me-1"></i> Refresh Editor Code
                                    </button>
                                </form>
                            </div>

                            {{-- Slot Kode Member --}}
                            <div class="p-3 rounded-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="fw-bold small text-secondary text-uppercase"
                                        style="letter-spacing: 0.5px;">
                                        <i class="fa fa-user me-1"></i> Member Access
                                    </label>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary"
                                        style="font-size: 10px;">General</span>
                                </div>

                                <div class="input-group mb-3 shadow-sm">
                                    <input type="text"
                                        class="form-control border-0 bg-white fw-bold text-center text-secondary"
                                        style="font-family: 'Monaco', 'Consolas', monospace; letter-spacing: 2px;"
                                        value="{{ $group->invitation_code_member }}" id="code_member" readonly>
                                    <button class="btn btn-white border-0 text-secondary px-3" type="button"
                                        onclick="copyCode('code_member')">
                                        <i class="fa fa-copy"></i>
                                    </button>
                                </div>

                                <form method="POST" action="/groups/{{ $group->id }}/generate-code"
                                    onsubmit="showToast('Kode Member berhasil diperbarui!', 'success')">
                                    @csrf
                                    <input type="hidden" name="type" value="member">
                                    <button class="btn btn-sm btn-outline-secondary w-100 fw-bold"
                                        style="border-radius: 8px; border-style: dashed;">
                                        <i class="fa fa-rotate me-1"></i> Refresh Member Code
                                    </button>
                                </form>
                            </div>

                            <div class="mt-3 text-center">
                                <small class="text-muted" style="font-size: 11px;">
                                    <i class="fa fa-shield-alt me-1"></i> Bagikan kode ini hanya kepada orang yang
                                    dipercaya.
                                </small>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Manage Roles --}}
                @if ($role->can_manage_member)
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
                        <div
                            class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="p-2 bg-primary bg-opacity-10 rounded-3 me-3">
                                    <i class="fa fa-shield-halved text-primary"></i>
                                </div>
                                <h6 class="fw-bold mb-0" style="color: #334155;">Manage Roles</h6>
                            </div>
                            <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#modalCreateRole">
                                <i class="fa fa-plus me-1"></i> Role
                            </button>
                        </div>

                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach ($roles as $r)
                                    <div class="list-group-item d-flex align-items-center justify-content-between py-3 px-4 border-0 {{ !$loop->last ? 'border-bottom' : '' }}"
                                        style="transition: background 0.2s;">
                                        <div class="d-flex align-items-center gap-3">
                                            <div
                                                style="width: 14px; height: 14px; border-radius: 50%; background: {{ $r->color }}; box-shadow: 0 0 8px {{ $r->color }}80; border: 2px solid white;">
                                            </div>

                                            <div class="d-flex flex-column">
                                                <span class="small fw-bold text-dark">{{ $r->name }}</span>
                                                @if ($r->is_owner)
                                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-medium"
                                                        style="font-size: 9px; width: fit-content;">
                                                        <i class="fa fa-crown me-1" style="font-size: 8px;"></i>System
                                                        Owner
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        @if (!$r->is_owner)
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-sm btn-light text-warning border shadow-xs"
                                                    style="border-radius: 8px; padding: 4px 8px;" data-bs-toggle="modal"
                                                    data-bs-target="#modalEditRole" data-id="{{ $r->id }}"
                                                    data-name="{{ $r->name }}" data-color="{{ $r->color }}"
                                                    data-can_create="{{ $r->can_create_announcement ? '1' : '0' }}"
                                                    data-can_edit="{{ $r->can_edit_announcement ? '1' : '0' }}"
                                                    data-can_member="{{ $r->can_manage_member ? '1' : '0' }}"
                                                    data-can_code="{{ $r->can_generate_code ? '1' : '0' }}"
                                                    data-can_bot="{{ $r->can_manage_bot ? '1' : '0' }}">
                                                    <i class="fa fa-pen" style="font-size: 11px;"></i>
                                                </button>

                                                <form method="POST"
                                                    action="/groups/{{ $group->id }}/roles/{{ $r->id }}"
                                                    class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-light text-danger border shadow-xs"
                                                        style="border-radius: 8px; padding: 4px 8px;"
                                                        onclick="return confirm('Yakin hapus role ini?')">
                                                        <i class="fa fa-trash" style="font-size: 11px;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="text-muted small px-2">
                                                <i class="fa fa-lock" title="Fixed Role"></i>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="card-footer bg-light border-0 py-2 text-center">
                            <small class="text-muted" style="font-size: 10px; letter-spacing: 0.5px;">URUTAN ROLE
                                MENENTUKAN HIERARKI</small>
                        </div>
                    </div>
                @endif

                {{-- Daftar Member --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-white py-2 border-bottom-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-users text-primary me-2" style="font-size: 14px;"></i>
                                <span class="fw-bold mb-0" style="color: #334155; font-size: 13px;">Anggota Group</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                {{-- Tampilkan max member dari subscription --}}
                                @php
                                    $maxMembers = auth()->user()->activeSubscription()?->first()?->max_members ?? '∞';
                                @endphp
                                <span class="badge bg-light text-secondary border" style="font-size: 10px;">
                                    {{ $members->count() }}/{{ $maxMembers }} Member
                                </span>
                                @if (is_numeric($maxMembers) && $members->count() >= $maxMembers)
                                    <span class="badge bg-danger bg-opacity-10 text-danger" style="font-size: 10px;">
                                        <i class="fa fa-triangle-exclamation me-1"></i>Penuh
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0" style="font-size: 12px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3 py-2 border-0 text-secondary fw-bold text-uppercase"
                                            style="font-size: 10px; letter-spacing: 0.3px;">Nama</th>
                                        <th class="py-2 border-0 text-secondary fw-bold text-uppercase"
                                            style="font-size: 10px; letter-spacing: 0.3px;">Role</th>
                                        @if ($role->can_manage_member)
                                            <th class="py-2 border-0 text-secondary fw-bold text-uppercase text-end pe-3"
                                                style="font-size: 10px; letter-spacing: 0.3px;">Ubah Role</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($members as $m)
                                        <tr class="border-bottom-0" style="border-bottom: 1px solid #f1f5f9 !important;">
                                            <td class="ps-3 py-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2 d-flex align-items-center justify-content-center fw-bold text-white"
                                                        style="width: 28px; height: 28px; border-radius: 6px; background: linear-gradient(45deg, #6366f1, #a855f7); font-size: 10px;">
                                                        {{ strtoupper(substr($m->user->name, 0, 1)) }}
                                                    </div>
                                                    <span class="fw-medium text-dark">{{ $m->user->name }}</span>
                                                </div>
                                            </td>
                                            <td class="py-2">
                                                <span class="badge"
                                                    style="background-color: {{ $m->role->color }}; font-size: 9px; padding: 3px 8px;">
                                                    {{ $m->role->name }}
                                                </span>
                                            </td>
                                            @if ($role->can_manage_member)
                                                <td class="py-2 text-end pe-3">
                                                    @if ($m->role->is_owner)
                                                        <small class="text-muted" style="font-size: 10px;">
                                                            <i class="fa fa-lock me-1"></i>Owner
                                                        </small>
                                                    @else
                                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                                            {{-- Dropdown ubah role --}}
                                                            <form method="POST"
                                                                action="/groups/{{ $group->id }}/roles/assign"
                                                                class="d-inline-block">
                                                                @csrf
                                                                <input type="hidden" name="user_id"
                                                                    value="{{ $m->user_id }}">
                                                                <select name="role_id"
                                                                    class="form-select form-select-sm py-0"
                                                                    onchange="this.form.submit()"
                                                                    style="font-size: 10px; height: 26px; width: 110px; border-radius: 6px; background-color: #f8fafc;">
                                                                    @foreach ($roles->where('is_owner', false) as $r)
                                                                        <option value="{{ $r->id }}"
                                                                            {{ $m->role_id == $r->id ? 'selected' : '' }}>
                                                                            {{ $r->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </form>

                                                            {{-- Tombol kick --}}
                                                            <form method="POST"
                                                                action="/groups/{{ $group->id }}/members/{{ $m->id }}"
                                                                onsubmit="return confirm('Yakin kick {{ $m->user->name }}?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    style="font-size: 10px; padding: 2px 7px; border-radius: 6px;">
                                                                    <i class="fa fa-user-slash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-light border-0 py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted" style="font-size: 10px; letter-spacing: 0.5px;">URUTAN
                                MEMBER</small>
                            @if (is_numeric($maxMembers))
                                <div class="progress" style="width: 100px; height: 5px; border-radius: 10px;">
                                    <div class="progress-bar {{ $members->count() >= $maxMembers ? 'bg-danger' : 'bg-primary' }}"
                                        style="width: {{ min(100, ($members->count() / $maxMembers) * 100) }}%">
                                    </div>
                                </div>
                            @endif
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
                                    <input type="datetime-local" name="scheduled_at" id="editScheduled"
                                        class="form-control">
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
                                    <input type="text" name="name" class="form-control"
                                        placeholder="Contoh: Bendahara" required>
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
                                            <input class="form-check-input" type="checkbox"
                                                name="can_create_announcement" id="cr_create">
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
                                    <input type="text" name="name" id="editRoleName" class="form-control"
                                        required>
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
                                            <input class="form-check-input" type="checkbox"
                                                name="can_create_announcement" id="er_create">
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
                        <button type="button" class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast"></button>
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

                function togglePollOptions() {
                    const type = document.getElementById('pollType').value;
                    const options = document.getElementById('pollOptions');
                    const inputs = options.querySelectorAll('input');

                    if (type === 'multiple_choice') {
                        options.classList.remove('d-none');
                        inputs.forEach(input => input.disabled = false);
                    } else {
                        options.classList.add('d-none');
                        inputs.forEach(input => input.disabled = true); // ← disable saat yes_no
                    }
                }

                function addOption() {
                    const list = document.getElementById('optionList');
                    const count = list.children.length + 1;
                    const div = document.createElement('div');
                    div.className = 'input-group mb-2';
                    div.innerHTML = `
        <input type="text" name="options[]" class="form-control form-control-sm"
               placeholder="Pilihan ${count}">
        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeOption(this)">
            <i class="fa fa-xmark"></i>
        </button>
    `;
                    list.appendChild(div);
                }

                function removeOption(btn) {
                    const list = document.getElementById('optionList');
                    if (list.children.length > 2) {
                        btn.closest('.input-group').remove();
                    } else {
                        showToast('Minimal 2 pilihan!', 'warning');
                    }
                }
            </script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    calculatePrice();
                    checkPendingPayment();
                });

                function checkPendingPayment(attempt = 0) {
                    if (attempt > 10) return; // max 10x cek (50 detik)

                    fetch('/payment/check-pending', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.synced) {
                                window.location.href = '/payment/receipt/' + data.order_id;
                            } else if (data.has_pending) {
                                // Ada payment pending, cek lagi 5 detik kemudian
                                setTimeout(() => checkPendingPayment(attempt + 1), 5000);
                            }
                        })
                        .catch(() => {});
                }
            </script>
            <script>
                document.getElementById('modalCreatePoll').addEventListener('show.bs.modal', function() {
                    togglePollOptions();
                });
            </script>

        @endsection
