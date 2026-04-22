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
                border-radius: 0;
                transition: transform 0.2s ease;
                background: #ffffff;
            }

            .announcement-item:hover {
                transform: translateY(-2px);
            }

            /* Pinned Style */
            .announcement-item.is-pinned {
                background: linear-gradient(to right, #fffdf5, #ffffff);
                border-left: 5px solid #f59e0b !important;
            }

            /* Gaya Lampiran/Attachment */
            .attachment-card {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 8px 12px;
                transition: all 0.2s;
                min-width: 200px;
            }

            .attachment-card:hover {
                background: #f1f5f9;
                border-color: #cbd5e1;
            }

            /* Badge Info Pengumuman */
            .info-badge {
                font-size: 11px;
                background: #f1f5f9;
                color: #475569;
                padding: 4px 10px;
                border-radius: 6px;
                font-weight: 600;
            }

            /* Gaya Reaksi (Emoji) */
            .btn-reaction {
                border: 1px solid #f1f5f9;
                background: #ffffff;
                padding: 4px 10px;
                border-radius: 50px;
                font-size: 0.8rem;
                transition: all 0.2s;
            }

            .btn-reaction:hover {
                background: #f8fafc;
                border-color: #e2e8f0;
            }

            .btn-reaction.active {
                background: #eff6ff;
                border-color: #3b82f6;
                color: #1d4ed8;
            }

            /* Sidebar Actions (Pin/Edit/Delete) */
            .action-group .btn {
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 10px;
                margin-bottom: 5px;
                font-size: 12px;
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

            .picker-card {
                border: none;
                border-radius: 24px;
                overflow: hidden;
                transition: all 0.3s ease;
            }

            .picker-header {
                background: #ffffff;
                padding: 1.25rem 1.5rem;
                border-bottom: 1px solid #f1f5f9;
            }

            /* Radio Mode Toggle - Custom Look */
            .mode-selector {
                background: #f1f5f9;
                padding: 5px;
                border-radius: 12px;
                display: inline-flex;
                width: 100%;
            }

            .mode-option {
                flex: 1;
                text-align: center;
            }

            .mode-option input[type="radio"] {
                display: none;
            }

            .mode-option label {
                display: block;
                padding: 8px;
                border-radius: 10px;
                cursor: pointer;
                font-size: 13px;
                font-weight: 600;
                color: #64748b;
                transition: all 0.2s;
                margin-bottom: 0;
            }

            .mode-option input[type="radio"]:checked+label {
                background: white;
                color: #f59e0b;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }

            /* Picker Controls */
            .count-control {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 5px;
            }

            .count-control .btn-circle {
                width: 32px;
                height: 32px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0;
                background: white;
                border: 1px solid #e2e8f0;
                color: #64748b;
                transition: 0.2s;
            }

            .count-control .btn-circle:hover {
                background: #f1f5f9;
                color: #1e293b;
            }

            .count-control input {
                border: none;
                background: transparent;
                font-weight: 700;
                color: #1e293b;
                width: 50px;
            }

            /* Result Area - The "Glass" Look */
            #standaloneResult {
                background: linear-gradient(135deg, #fffbeb 0%, #fff7ed 100%);
                border: 2px dashed #fed7aa !important;
                border-radius: 20px;
                position: relative;
                overflow: hidden;
            }

            .picked-name {
                background: white;
                color: #92400e;
                padding: 10px 20px;
                border-radius: 12px;
                font-weight: 700;
                box-shadow: 0 4px 15px rgba(245, 158, 11, 0.15);
                border: 1px solid #fef3c7;
                animation: popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }

            @keyframes popIn {
                0% {
                    transform: scale(0.5);
                    opacity: 0;
                }

                100% {
                    transform: scale(1);
                    opacity: 1;
                }
            }

            .btn-pick-main {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                color: white;
                border: none;
                border-radius: 14px;
                padding: 12px;
                box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
                transition: all 0.2s;
            }

            .btn-pick-main:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 20px -3px rgba(245, 158, 11, 0.4);
                color: white;
            }

            .form-control:focus,
            .form-select:focus {
                border-color: #4e73df;
                box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.15);
            }

            .bg-soft-info {
                background-color: #e3f2fd;
                border: 1px solid #bbdefb;
            }

            .modal-content {
                border-radius: 15px;
                overflow: hidden;
            }

            .btn-check:checked+.btn-outline-primary {
                background-color: #4e73df;
                color: white;
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

                    <div class="card-body p-0">
                        @forelse ($announcements as $announcement)
                            <div
                                class="card mb-4 shadow-sm announcement-item {{ $announcement->is_pinned ? 'is-pinned' : 'border-0' }}">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start">

                                        <div class="flex-grow-1">
                                            {{-- Header: Title & Pin Icon --}}
                                            <div class="d-flex align-items-center mb-2">
                                                @if ($announcement->is_pinned)
                                                    <div class="bg-warning bg-opacity-10 p-2 rounded-circle me-3">
                                                        <i class="fa fa-thumbtack text-warning"
                                                            style="font-size: 14px;"></i>
                                                    </div>
                                                @endif
                                                <h5 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">
                                                    {{ $announcement->title }}</h5>
                                            </div>

                                            {{-- Content --}}
                                            <p class="text-secondary mb-4"
                                                style="font-size: 0.95rem; line-height: 1.7; white-space: pre-line; color: #4b5563 !important;">
                                                {{ $announcement->content }}
                                            </p>

                                            {{-- Attachments Section --}}
                                            @if ($announcement->attachments->isNotEmpty())
                                                <div class="d-flex flex-wrap gap-2 mb-4">
                                                    @foreach ($announcement->attachments as $attachment)
                                                        <a href="{{ $attachment->url }}" target="_blank"
                                                            class="attachment-card text-decoration-none text-dark d-flex align-items-center">
                                                            @if ($attachment->type === 'image')
                                                                <img src="{{ $attachment->url }}" class="rounded me-2"
                                                                    style="width:35px; height:35px; object-fit:cover;">
                                                            @else
                                                                @php
                                                                    $icon = match (true) {
                                                                        str_contains($attachment->mime_type, 'pdf')
                                                                            => 'fa-file-pdf text-danger',
                                                                        str_contains($attachment->mime_type, 'word')
                                                                            => 'fa-file-word text-primary',
                                                                        str_contains($attachment->mime_type, 'sheet')
                                                                            => 'fa-file-excel text-success',
                                                                        default => 'fa-file text-secondary',
                                                                    };
                                                                @endphp
                                                                <i class="fa {{ $icon }} fa-lg me-3"></i>
                                                            @endif
                                                            <div class="overflow-hidden">
                                                                <div class="small fw-bold text-truncate"
                                                                    style="max-width: 140px;">{{ $attachment->filename }}
                                                                </div>
                                                                <div class="text-muted" style="font-size: 9px;">
                                                                    {{ $attachment->formatted_size }}</div>
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif

                                            {{-- Footer Metadata --}}
                                            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                                <span class="info-badge">
                                                    <i class="fa fa-user-circle me-1 text-primary"></i>
                                                    {{ $announcement->user->name }}
                                                </span>
                                                <span class="info-badge">
                                                    <i class="fa fa-calendar-alt me-1"></i>
                                                    {{ $announcement->created_at->format('d M, H:i') }}
                                                </span>
                                                @if ($announcement->scheduled_at)
                                                    <span class="info-badge text-primary" style="background: #eff6ff;">
                                                        <i class="fa fa-clock me-1"></i> Terjadwal:
                                                        {{ $announcement->scheduled_at->format('d M, H:i') }}
                                                    </span>
                                                @endif
                                                @if ($announcement->repeat !== 'none')
                                                    <span class="info-badge text-success" style="background: #f0fdf4;">
                                                        <i class="fa fa-sync-alt me-1"></i>
                                                        {{ ucfirst($announcement->repeat) }}
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Reactions --}}
                                            <div class="d-flex flex-wrap gap-1" id="reactions-{{ $announcement->id }}">
                                                @php
                                                    $emojis = ['👍', '❤️', '😂', '😮', '😢', '😡'];
                                                    $reactionCounts = $announcement->reactions->groupBy('emoji');
                                                    $myReactions = $announcement->reactions
                                                        ->where('user_id', auth()->id())
                                                        ->pluck('emoji')
                                                        ->toArray();
                                                @endphp
                                                @foreach ($emojis as $emoji)
                                                    @php
                                                        $count = $reactionCounts->get($emoji)?->count() ?? 0;
                                                        $reacted = in_array($emoji, $myReactions);
                                                    @endphp
                                                    <button
                                                        onclick="react({{ $announcement->id }}, '{{ $emoji }}', this)"
                                                        class="btn-reaction {{ $reacted ? 'active' : '' }}">
                                                        {{ $emoji }} <span
                                                            class="ms-1 fw-bold">{{ $count > 0 ? $count : '' }}</span>
                                                    </button>
                                                @endforeach
                                            </div>

                                            {{-- Random Picker Result (Inner Card) --}}
                                            @if ($announcement->use_picker)
                                                <div class="mt-4 p-3 rounded-4 border border-warning border-opacity-25"
                                                    style="background: #fffcf0;">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                        <div class="small fw-bold text-warning text-uppercase">
                                                            <i class="fa fa-dice me-2"></i> RANDOM PICKER
                                                        </div>
                                                        <button
                                                            class="btn btn-sm {{ $announcement->picked_result ? 'btn-warning' : 'btn-outline-warning' }} rounded-pill px-3 fw-bold"
                                                            id="btnPick-{{ $announcement->id }}"
                                                            onclick="previewPick({{ $announcement->id }})">
                                                            <i
                                                                class="fa fa-{{ $announcement->picked_result ? 'rotate' : 'shuffle' }} me-1"></i>
                                                            {{ $announcement->picked_result ? 'Undi Ulang' : 'Undi Sekarang' }}
                                                        </button>
                                                    </div>
                                                    <div id="pickResult-{{ $announcement->id }}"
                                                        class="mt-3 {{ $announcement->picked_result ? '' : 'd-none' }}">
                                                        <div id="spinner-{{ $announcement->id }}"
                                                            class="fw-bold text-dark small mb-2 opacity-75">
                                                            {{ $announcement->picked_result ? '🎉 Hasil Undian:' : '' }}
                                                        </div>
                                                        <div id="names-{{ $announcement->id }}"
                                                            class="d-flex flex-wrap gap-2">
                                                            {{-- Hasil undian akan muncul di sini --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Admin Actions Column --}}
                                        @if ($role->can_edit_announcement)
                                            <div class="action-group d-flex flex-column ms-3">
                                                <form method="POST"
                                                    action="/groups/{{ $group->id }}/announcements/{{ $announcement->id }}/pin">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn {{ $announcement->is_pinned ? 'btn-warning shadow-sm' : 'btn-light border' }}"
                                                        title="Pin/Unpin">
                                                        <i class="fa fa-thumbtack"></i>
                                                    </button>
                                                </form>

                                                <button class="btn btn-light border text-primary" data-bs-toggle="modal"
                                                    data-bs-target="#modalEdit" data-id="{{ $announcement->id }}"
                                                    data-title="{{ $announcement->title }}"
                                                    data-content="{{ $announcement->content }}"
                                                    data-attachments="{{ json_encode($announcement->attachments) }}">
                                                    <i class="fa fa-pen"></i>
                                                </button>

                                                <button class="btn btn-light border text-danger"
                                                    onclick="confirmDelete({{ $announcement->id }})">
                                                    <i class="fa fa-trash"></i>
                                                </button>

                                                <form id="deleteForm{{ $announcement->id }}" method="POST"
                                                    action="/groups/{{ $group->id }}/announcements/{{ $announcement->id }}"
                                                    class="d-none">
                                                    @csrf @method('DELETE')
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="bg-light d-inline-block p-4 rounded-circle mb-3">
                                    <i class="fa fa-bullhorn fa-3x text-muted opacity-25"></i>
                                </div>
                                <h5 class="text-secondary fw-bold">Belum ada pengumuman</h5>
                                <p class="text-muted small">Cek kembali nanti untuk info terbaru.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                {{-- Random Picker --}}
                <div class="card picker-card shadow-sm mt-4">
                    <div class="picker-header d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold m-0"><i class="fa fa-shuffle text-warning me-2"></i>Random Picker</h6>
                        <span
                            class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill small fw-bold">Lucky
                            Draw</span>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-4">
                            {{-- Bagian Kontrol --}}
                            <div class="col-md-6 border-end border-light">
                                {{-- Mode Selector --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-muted mb-2 text-uppercase">Pilih
                                        Mode</label>
                                    <div class="mode-selector">
                                        <div class="mode-option">
                                            <input type="radio" name="picker_mode_standalone"
                                                id="standaloneModeMembers" value="members" checked
                                                onchange="toggleStandaloneMode()">
                                            <label for="standaloneModeMembers">
                                                <i class="fa fa-users me-1"></i> Members
                                            </label>
                                        </div>
                                        <div class="mode-option">
                                            <input type="radio" name="picker_mode_standalone" id="standaloneModeCustom"
                                                value="custom" onchange="toggleStandaloneMode()">
                                            <label for="standaloneModeCustom">
                                                <i class="fa fa-list me-1"></i> Custom List
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Mode Members Options --}}
                                <div id="standaloneMemberOptions" class="mb-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Filter Role</label>
                                    <select id="standaloneRoleId" class="form-select border-0 bg-light rounded-3 py-2">
                                        <option value="">Semua Member</option>
                                        @foreach ($roles as $r)
                                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Mode Custom Options --}}
                                <div id="standaloneCustomOptions" class="mb-3 d-none">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Daftar Nama</label>
                                    <textarea id="standaloneCustomList" class="form-control border-0 bg-light rounded-3" rows="5"
                                        placeholder="Contoh:&#10;Budi&#10;Ani&#10;Kelompok A"></textarea>
                                    <div class="mt-1 d-flex align-items-center text-muted" style="font-size: 11px;">
                                        <i class="fa fa-info-circle me-1"></i> Satu nama per baris
                                    </div>
                                </div>

                                {{-- Pick Count --}}
                                <div class="mb-4 pt-2">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Jumlah yang
                                        Dipick</label>
                                    <div class="count-control d-inline-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-circle"
                                            onclick="changeStandaloneCount(-1)"><i class="fa fa-minus small"></i></button>
                                        <input type="number" id="standalonePickCount" class="text-center"
                                            value="1" min="1" max="50" readonly>
                                        <button type="button" class="btn btn-circle"
                                            onclick="changeStandaloneCount(1)"><i class="fa fa-plus small"></i></button>
                                    </div>
                                </div>

                                <button class="btn btn-pick-main w-100 fw-bold py-3" id="btnStandalonePick"
                                    onclick="standalonePick()">
                                    <i class="fa fa-shuffle me-2"></i>UNDI SEKARANG
                                </button>
                            </div>

                            {{-- Bagian Hasil --}}
                            <div class="col-md-6">
                                <div class="h-100 d-flex flex-column">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-2">Hasil
                                        Undian</label>
                                    <div class="card border-0 h-100 p-4 text-center d-flex flex-column justify-content-center align-items-center"
                                        id="standaloneResult" style="min-height: 250px;">

                                        <div id="standaloneSpinner" class="text-center">
                                            <div class="mb-3">
                                                <i class="fa fa-dice fa-4x text-warning opacity-25"></i>
                                            </div>
                                            <h6 class="text-muted fw-bold mb-1">Siap Mengundi?</h6>
                                            <p class="text-muted small mb-0 px-4">Klik tombol undi untuk memilih pemenang
                                                secara acak.</p>
                                        </div>

                                        <div id="standaloneNames" class="d-flex flex-wrap gap-3 justify-content-center">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                    <div class="card-body p-0"> {{-- Menghapus padding agar card poll menempel rapi --}}
                        @forelse ($polls as $poll)
                            <div class="card mb-3 border-0 shadow-sm mx-3 mt-3"
                                style="border-radius: 15px; overflow: hidden;">
                                <div class="card-body p-4">

                                    {{-- Header Poll --}}
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold text-dark mb-2"
                                                style="font-size: 1.1rem; line-height: 1.4;">
                                                {{ $poll->question }}
                                            </h6>
                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                <span class="text-muted small">
                                                    <i class="fa fa-circle-user me-1"></i>{{ $poll->user->name }}
                                                </span>

                                                {{-- Badges Status --}}
                                                @if ($poll->is_anonymous)
                                                    <span
                                                        class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25"
                                                        style="font-size: 10px;">
                                                        <i class="fa fa-eye-slash me-1"></i>Anonymous
                                                    </span>
                                                @else
                                                    <span
                                                        class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info border-opacity-25"
                                                        style="font-size: 10px;">
                                                        <i class="fa fa-eye me-1"></i>Publik
                                                    </span>
                                                @endif

                                                @if ($poll->is_closed || $poll->isExpired())
                                                    <span
                                                        class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25"
                                                        style="font-size: 10px;">
                                                        <i class="fa fa-lock me-1"></i>Ditutup
                                                    </span>
                                                @else
                                                    <span
                                                        class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25"
                                                        style="font-size: 10px;">
                                                        <i class="fa fa-bolt me-1 text-success"></i>Aktif
                                                    </span>
                                                @endif

                                                @if ($poll->closes_at && !$poll->is_closed && !$poll->isExpired())
                                                    <span class="text-muted" style="font-size: 11px;">
                                                        <i class="fa fa-clock me-1 text-warning"></i>Tutup:
                                                        {{ $poll->closes_at->diffForHumans() }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Admin Actions --}}
                                        @if ($role->can_create_poll)
                                            <div class="dropdown ms-2">
                                                <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                    @if (!$poll->is_closed && !$poll->isExpired())
                                                        <li>
                                                            <form method="POST"
                                                                action="/groups/{{ $group->id }}/polls/{{ $poll->id }}/close">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="dropdown-item small text-warning"
                                                                    onclick="return confirm('Tutup poll ini?')">
                                                                    <i class="fa fa-lock me-2"></i>Tutup Poll
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <form method="POST"
                                                            action="/groups/{{ $group->id }}/polls/{{ $poll->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item small text-danger"
                                                                onclick="return confirm('Hapus poll ini?')">
                                                                <i class="fa fa-trash me-2"></i>Hapus Poll
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endif
                                    </div>

                                    <hr class="opacity-50 mb-3">

                                    {{-- Options & Vote Area --}}
                                    @php
                                        $totalVotes = $poll->votes->count();
                                        $userVote = $poll->votes->where('user_id', auth()->id())->first();
                                        $isClosed = $poll->is_closed || $poll->isExpired();
                                    @endphp

                                    <div class="d-flex flex-column gap-3">
                                        @foreach ($poll->options as $option)
                                            @php
                                                $optionVotes = $option->votes->count();
                                                $percent =
                                                    $totalVotes > 0 ? round(($optionVotes / $totalVotes) * 100) : 0;
                                                $isMyVote = $userVote?->poll_option_id === $option->id;
                                            @endphp

                                            <div class="option-container">
                                                @if (!$userVote && !$isClosed)
                                                    {{-- Mode: Belum Vote --}}
                                                    <form method="POST"
                                                        action="/groups/{{ $group->id }}/polls/{{ $poll->id }}/vote">
                                                        @csrf
                                                        <input type="hidden" name="option_id"
                                                            value="{{ $option->id }}">
                                                        <button type="submit"
                                                            class="btn btn-outline-primary w-100 text-start py-2 px-3 transition-all"
                                                            style="border-radius: 12px; border-width: 2px; font-weight: 500;">
                                                            {{ $option->label }}
                                                        </button>
                                                    </form>
                                                @else
                                                    {{-- Mode: Hasil Voting --}}
                                                    <div class="position-relative">
                                                        <div class="progress"
                                                            style="height: 40px; border-radius: 10px; background-color: #f0f2f5;">
                                                            <div class="progress-bar transition-all {{ $isMyVote ? 'bg-primary' : 'bg-secondary bg-opacity-25' }}"
                                                                role="progressbar" style="width: {{ $percent }}%;"
                                                                aria-valuenow="{{ $percent }}" aria-valuemin="0"
                                                                aria-valuemax="100">
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center px-3 justify-content-between">
                                                            <span
                                                                class="small {{ $isMyVote ? 'text-white fw-bold' : 'text-dark fw-medium' }}">
                                                                @if ($isMyVote)
                                                                    <i class="fa fa-circle-check me-2"></i>
                                                                @endif
                                                                {{ $option->label }}
                                                            </span>
                                                            <span
                                                                class="small {{ $isMyVote ? 'text-white fw-bold' : 'text-muted' }}">
                                                                {{ $percent }}%
                                                                @if (!$poll->is_anonymous || $isClosed)
                                                                    <span
                                                                        style="font-size: 0.75rem;">({{ $optionVotes }})</span>
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>

                                                    {{-- Daftar Voter (Jika Publik) --}}
                                                    @if (!$poll->is_anonymous && $optionVotes > 0)
                                                        <div class="d-flex flex-wrap gap-1 mt-1 ms-1">
                                                            @foreach ($option->votes as $vote)
                                                                <span class="badge bg-light text-muted border-0 p-0 me-1"
                                                                    style="font-size: 9px;">
                                                                    @if ($loop->first)
                                                                        <i class="fa fa-users me-1"></i>
                                                                    @endif
                                                                    {{ explode(' ', $vote->user->name)[0] }}{{ !$loop->last ? ',' : '' }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- Footer Info --}}
                                    <div class="mt-3 d-flex align-items-center justify-content-between">
                                        <span class="text-muted fw-semibold" style="font-size: 12px;">
                                            <i class="fa fa-poll me-1 text-primary"></i>Total: {{ $totalVotes }} suara
                                        </span>
                                        @if ($userVote)
                                            <span class="text-primary small fw-bold">
                                                <i class="fa fa-check-double me-1"></i>Sudah Memilih
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                    style="width: 80px; height: 80px;">
                                    <i class="fa fa-chart-simple fa-2x text-muted"></i>
                                </div>
                                <h6 class="fw-bold">Belum Ada Polling</h6>
                                <p class="text-muted small">Diskusi jadi lebih mudah dengan fitur voting.</p>
                            </div>
                        @endforelse
                    </div>

                </div>

            </div>


            {{-- Modal Create Poll --}}
            <div class="modal fade" id="modalCreatePoll" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered"> {{-- Tambah centered agar lebih fokus --}}
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold">
                                <span class="bg-primary bg-opacity-10 p-2 rounded-3 me-2">
                                    <i class="fa fa-chart-bar text-primary"></i>
                                </span>
                                Buat Poll Baru
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <form method="POST" action="/groups/{{ $group->id }}/polls">
                            @csrf
                            <div class="modal-body py-4">
                                {{-- Input Pertanyaan --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-uppercase text-muted">Pertanyaan</label>
                                    <textarea name="question" class="form-control border-2" rows="2" placeholder="Apa yang ingin Anda tanyakan?"
                                        style="border-radius: 12px; resize: none;" required></textarea>
                                </div>

                                {{-- Pilihan Tipe --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-uppercase text-muted">Tipe Voting</label>
                                    <select name="type" id="pollType" class="form-select border-2"
                                        style="border-radius: 10px; height: 45px;" onchange="togglePollOptions()">
                                        <option value="yes_no">👍 Ya / Tidak 👎</option>
                                        <option value="multiple_choice">📝 Pilihan Ganda</option>
                                    </select>
                                </div>

                                {{-- Container Pilihan Ganda (Hidden by default) --}}
                                <div id="pollOptions" class="mb-4 d-none p-3 bg-light rounded-4 border border-dashed">
                                    <label class="form-label fw-bold small text-uppercase text-muted d-block mb-3">Opsi
                                        Jawaban</label>
                                    <div id="optionList">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text border-0 bg-white"><i
                                                    class="fa fa-grip-lines text-muted"></i></span>
                                            <input type="text" name="options[]"
                                                class="form-control border-0 shadow-sm" placeholder="Pilihan 1"
                                                style="border-radius: 0 8px 8px 0;">
                                            <button type="button" class="btn btn-link text-danger ms-1 p-0"
                                                onclick="removeOption(this)">
                                                <i class="fa fa-circle-xmark fa-lg"></i>
                                            </button>
                                        </div>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text border-0 bg-white"><i
                                                    class="fa fa-grip-lines text-muted"></i></span>
                                            <input type="text" name="options[]"
                                                class="form-control border-0 shadow-sm" placeholder="Pilihan 2"
                                                style="border-radius: 0 8px 8px 0;">
                                            <button type="button" class="btn btn-link text-danger ms-1 p-0"
                                                onclick="removeOption(this)">
                                                <i class="fa fa-circle-xmark fa-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button"
                                        class="btn btn-sm btn-white border shadow-sm mt-2 w-100 fw-bold py-2"
                                        onclick="addOption()" style="border-radius: 10px;">
                                        <i class="fa fa-plus me-1 text-primary"></i> Tambah Opsi
                                    </button>
                                </div>

                                <div class="row">
                                    {{-- Tutup Otomatis --}}
                                    <div class="col-md-12 mb-4">
                                        <label class="form-label fw-bold small text-uppercase text-muted">
                                            <i class="fa fa-calendar-clock me-1"></i>Batas Waktu (Opsional)
                                        </label>
                                        <input type="datetime-local" name="closes_at" class="form-control border-2"
                                            style="border-radius: 10px;">
                                    </div>

                                    {{-- Visibilitas --}}
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold small text-uppercase text-muted">Privasi
                                            Hasil</label>
                                        <div class="d-flex gap-2">
                                            <input type="radio" class="btn-check" name="is_anonymous" id="publik"
                                                value="0" checked autocomplete="off">
                                            <label class="btn btn-outline-info flex-fill py-2" for="publik"
                                                style="border-radius: 10px; border-width: 2px;">
                                                <i class="fa fa-eye me-2"></i>Publik
                                            </label>

                                            <input type="radio" class="btn-check" name="is_anonymous" id="anonymous"
                                                value="1" autocomplete="off">
                                            <label class="btn btn-outline-secondary flex-fill py-2" for="anonymous"
                                                style="border-radius: 10px; border-width: 2px;">
                                                <i class="fa fa-eye-slash me-2"></i>Anonim
                                            </label>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted" style="font-size: 11px;">
                                                <i class="fa fa-info-circle me-1"></i>
                                                <b>Anonim:</b> Nama pemilih tidak akan terlihat oleh siapapun.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer border-0 pt-0 pb-4 justify-content-center">
                                <button type="button" class="btn btn-light px-4 fw-bold text-muted"
                                    data-bs-dismiss="modal" style="border-radius: 12px;">Batal</button>
                                <button type="submit" class="btn btn-primary px-5 fw-bold shadow"
                                    style="border-radius: 12px;">
                                    <i class="fa fa-rocket me-2"></i>Luncurkan Poll
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
                                            <div class="mb-3 p-3 bg-white border rounded-3 shadow-xs">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="small fw-bold text-dark flex-grow-1">
                                                        <i class="fa fa-hashtag text-muted me-1"></i>
                                                        {{ $discordChannelName ?? 'Channel belum diset' }}
                                                    </div>
                                                    <a href="{{ $discordInviteUrl ?? '#' }}" target="_blank"
                                                        @if (!$discordInviteUrl) onclick="event.preventDefault(); showToast('Link invite belum tersedia dari bot service.', 'warning')" @else onclick="showToast('Membuka halaman invite...', 'info')" @endif
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
                                                        <div class="fw-bold mb-1 text-dark">Hubungkan Telegram tanpa input ID.</div>
                                                        <div class="text-muted">Klik tombol di bawah, lalu tambahkan bot ke grup target. Sistem akan menangkap grup yang benar memakai token sekali pakai.</div>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center mb-2 small fw-bold text-success">
                                                        <i class="fa fa-check-circle me-2"></i>
                                                        {{ $telegramGroupName ?? 'Terhubung' }}
                                                    </div>
                                                @endif

                                                <form method="POST"
                                                    action="/groups/{{ $group->id }}/bots/{{ $bot->id }}/telegram-connect"
                                                    class="mb-3">
                                                    @csrf
                                                    <button class="btn btn-sm {{ !$bot->telegram_chat_id ? 'btn-info text-white' : 'btn-outline-info' }} fw-bold w-100">
                                                        <i class="fa fa-link me-1"></i>
                                                        {{ !$bot->telegram_chat_id ? 'Buat Link Koneksi Telegram' : 'Buat Ulang Koneksi' }}
                                                    </button>
                                                </form>

                                                @if (session('telegram_connect_link'))
                                                    @php $isCurrentTelegramBot = (int) session('telegram_connect_bot_id') === (int) $bot->id; @endphp
                                                @endif

                                                @if (session('telegram_connect_link') && $isCurrentTelegramBot)
                                                    <div class="border rounded-3 p-3 bg-light">
                                                        <div class="small fw-bold text-dark mb-2">Link koneksi siap dipakai</div>
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" class="form-control bg-white"
                                                                id="telegramConnectLink"
                                                                value="{{ session('telegram_connect_link') }}" readonly>
                                                            <button class="btn btn-outline-info" type="button"
                                                                onclick="copyTelegramConnectLink()">
                                                                <i class="fa fa-copy"></i>
                                                            </button>
                                                            <a class="btn btn-info text-white" href="{{ session('telegram_connect_link') }}" target="_blank" rel="noopener">
                                                                Buka Telegram
                                                            </a>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-info mt-2 w-100"
                                                            onclick="pollTelegramClaim({{ $group->id }}, {{ $bot->id }})">
                                                            <i class="fa fa-satellite-dish me-1"></i> Cek Status Koneksi
                                                        </button>
                                                        <small class="text-muted d-block mt-2" style="font-size: 10px;">
                                                            Token ini sekali pakai. Jika ada dua orang klik bersamaan, token terakhir yang aktif.
                                                        </small>
                                                    </div>
                                                @endif
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
            <div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered"> {{-- Tambah centered agar lebih modern --}}
                    <div class="modal-content border-0 shadow-lg">

                        {{-- Header dengan Gradien --}}
                        <div class="modal-header bg-primary text-white"
                            style="background: linear-gradient(45deg, #4e73df, #224abe);">
                            <h5 class="modal-title d-flex align-items-center">
                                <div class="bg-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                                    style="width: 35px; height: 35px;">
                                    <i class="fa fa-bullhorn text-primary small"></i>
                                </div>
                                <span class="fw-bold">Buat Announcement Baru</span>
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <form method="POST" action="/groups/{{ $group->id }}/announcements"
                            enctype="multipart/form-data" onsubmit="handleCreate(event, this)">
                            @csrf
                            <div class="modal-body p-4">

                                {{-- Section: Konten Utama --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark">Judul <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="title" id="createTitle"
                                        class="form-control border-2 shadow-sm" placeholder="Apa judul pengumumanmu?"
                                        required style="border-radius: 10px;">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark">Isi Pesan <span
                                            class="text-danger">*</span></label>
                                    <textarea name="content" id="createContent" class="form-control border-2 shadow-sm" rows="4"
                                        placeholder="Tuliskan detail pengumuman di sini..." required style="border-radius: 10px;"></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold small text-muted">
                                            <i class="fa fa-calendar-alt me-1 text-primary"></i>Jadwal Kirim
                                        </label>
                                        <input type="datetime-local" name="scheduled_at" id="createScheduled"
                                            class="form-control form-control-sm border-2 shadow-sm"
                                            style="border-radius: 8px;">
                                        <small class="text-muted">Kosong = kirim sekarang</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold small text-muted">
                                            <i class="fa fa-rotate me-1 text-success"></i>Pengulangan
                                        </label>
                                        <select name="repeat" class="form-select form-select-sm border-2 shadow-sm"
                                            style="border-radius: 8px;">
                                            <option value="none">Tidak Berulang</option>
                                            <option value="daily">Setiap Hari</option>
                                            <option value="weekly">Setiap Minggu</option>
                                            <option value="monthly">Setiap Bulan</option>
                                        </select>
                                    </div>
                                </div>

                                <hr class="my-4 opacity-50">

                                {{-- Section: Lampiran --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-dark d-flex justify-content-between">
                                        <span><i class="fa fa-paperclip me-2 text-info"></i>Lampiran</span>
                                        <span class="badge bg-soft-info text-info small"
                                            style="background-color: #e0f7fa;">Max 3</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="file" name="attachments[]" id="createAttachments"
                                            class="form-control border-2" multiple
                                            accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xlsx,.xls"
                                            onchange="previewAttachments(this, 'createPreview')"
                                            style="border-radius: 10px;">
                                    </div>
                                    <div id="createPreview" class="d-flex flex-wrap gap-2 mt-2"></div>
                                </div>

                                {{-- Section: Random Picker (Colorful Card) --}}
                                <div class="card border-0 shadow-sm"
                                    style="background-color: #fff9f0; border-radius: 15px;">
                                    <div class="card-body">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="use_picker"
                                                id="usePicker" onchange="togglePicker('create')"
                                                style="cursor: pointer; width: 40px; height: 20px;">
                                            <label class="form-check-label fw-bold text-warning ms-2" for="usePicker">
                                                <i class="fa fa-dice-d20 me-1"></i> Gunakan Random Picker
                                            </label>
                                        </div>

                                        <div id="pickerOptions" class="d-none mt-3">
                                            <div class="row g-2">
                                                <div class="col-12 mb-2">
                                                    <label class="form-label small fw-bold">Mode Picker</label>
                                                    <div class="btn-group w-100" role="group">
                                                        <input type="radio" class="btn-check" name="picker_mode"
                                                            id="modeMembers" value="members" checked
                                                            onchange="togglePickerMode('create')">
                                                        <label class="btn btn-outline-primary btn-sm" for="modeMembers">
                                                            <i class="fa fa-users me-1"></i> Member
                                                        </label>

                                                        <input type="radio" class="btn-check" name="picker_mode"
                                                            id="modeCustom" value="custom"
                                                            onchange="togglePickerMode('create')">
                                                        <label class="btn btn-outline-success btn-sm" for="modeCustom">
                                                            <i class="fa fa-list me-1"></i> Custom
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-6 mb-2">
                                                    <label class="form-label small fw-bold">Jumlah Pick</label>
                                                    <input type="number" name="pick_count"
                                                        class="form-control form-control-sm" value="1"
                                                        min="1" max="50">
                                                </div>

                                                <div id="pickerRoleFilter" class="col-12 mb-2">
                                                    <label class="form-label small fw-bold">Filter Role</label>
                                                    <select name="pick_role_id" class="form-select form-select-sm">
                                                        <option value="">Semua Member</option>
                                                        @foreach ($roles as $r)
                                                            <option value="{{ $r->id }}">{{ $r->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div id="pickerCustomList" class="col-12 mb-2 d-none">
                                                    <label class="form-label small fw-bold">Daftar Pilihan (Per
                                                        Baris)</label>
                                                    <textarea name="custom_pick_list" class="form-control form-control-sm" rows="3"
                                                        placeholder="Contoh:&#10;Nama A&#10;Nama B"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="modal-footer bg-light border-0 px-4 py-3" style="border-radius: 0 0 15px 15px;">
                                <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none"
                                    data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary px-4 shadow"
                                    style="border-radius: 10px; background: linear-gradient(45deg, #4e73df, #224abe);">
                                    <i class="fa fa-paper-plane me-2"></i>Kirim Pengumuman
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal Edit Announcement --}}
            <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">

                        {{-- Header dengan Gradien Kuning-Oranye --}}
                        <div class="modal-header text-white"
                            style="background: linear-gradient(45deg, #f6e05e, #ed8936);">
                            <h5 class="modal-title d-flex align-items-center">
                                <div class="bg-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                                    style="width: 35px; height: 35px;">
                                    <i class="fa fa-pen text-warning small"></i>
                                </div>
                                <span class="fw-bold text-dark">Perbarui Announcement</span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <form method="POST" id="formEdit" enctype="multipart/form-data"
                            onsubmit="handleEdit(event, this)">
                            @csrf
                            @method('PUT')

                            <div class="modal-body p-4">

                                {{-- Input Judul --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark small">Judul Announcement</label>
                                    <input type="text" name="title" id="editTitle"
                                        class="form-control border-2 shadow-sm" placeholder="Masukkan judul..." required
                                        style="border-radius: 10px;">
                                </div>

                                {{-- Input Konten --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark small">Isi Pesan</label>
                                    <textarea name="content" id="editContent" class="form-control border-2 shadow-sm" rows="4"
                                        placeholder="Edit isi pengumuman..." required style="border-radius: 10px;"></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold small text-muted">
                                            <i class="fa fa-calendar me-1 text-warning"></i>Jadwal Kirim
                                        </label>
                                        <input type="datetime-local" name="scheduled_at" id="editScheduled"
                                            class="form-control form-control-sm border-2 shadow-sm"
                                            style="border-radius: 8px;">
                                        <small class="text-muted">Kosong = kirim sekarang</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold small text-muted">
                                            <i class="fa fa-rotate me-1 text-success"></i>Pengulangan
                                        </label>
                                        <select name="repeat" id="editRepeat"
                                            class="form-select form-select-sm border-2 shadow-sm"
                                            style="border-radius: 8px;">
                                            <option value="none">Tidak Berulang</option>
                                            <option value="daily">Setiap Hari</option>
                                            <option value="weekly">Setiap Minggu</option>
                                            <option value="monthly">Setiap Bulan</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Area Lampiran yang Ada --}}
                                <div id="existingAttachmentsArea" class="mb-3 p-3 bg-light rounded-3 d-none">
                                    <label class="form-label fw-bold small text-muted mb-2">Lampiran Saat Ini:</label>
                                    <div id="existingAttachments" class="d-flex flex-wrap gap-2"></div>
                                </div>

                                {{-- Upload Lampiran Baru --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-dark small">
                                        <i class="fa fa-paperclip me-2 text-info"></i>Tambah Lampiran Baru
                                    </label>
                                    <input type="file" name="attachments[]" id="editAttachments"
                                        class="form-control form-control-sm border-2 shadow-sm" multiple
                                        accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xlsx,.xls"
                                        onchange="previewAttachments(this, 'editPreview')" style="border-radius: 10px;">
                                    <div id="editPreview" class="d-flex flex-wrap gap-2 mt-2"></div>
                                </div>

                                {{-- Section: Random Picker (Refined Card) --}}
                                <div class="card border-0 shadow-sm"
                                    style="background-color: #fff9f0; border-radius: 15px;">
                                    <div class="card-body">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input shadow-none" type="checkbox" name="use_picker"
                                                id="editUsePicker" onchange="toggleEditPicker()"
                                                style="cursor: pointer; width: 40px; height: 20px;">
                                            <label class="form-check-label fw-bold text-dark ms-2" for="editUsePicker">
                                                <i class="fa fa-dice-six me-1 text-warning"></i> Gunakan Random Picker
                                            </label>
                                        </div>

                                        <div id="editPickerOptions" class="d-none mt-3">
                                            <div class="row g-2">
                                                <div class="col-12 mb-2">
                                                    <div class="btn-group w-100 shadow-sm" role="group">
                                                        <input type="radio" class="btn-check" name="picker_mode"
                                                            id="editModeMembers" value="members"
                                                            onchange="toggleEditPickerMode()">
                                                        <label class="btn btn-outline-warning btn-sm fw-bold"
                                                            for="editModeMembers">Member</label>

                                                        <input type="radio" class="btn-check" name="picker_mode"
                                                            id="editModeCustom" value="custom"
                                                            onchange="toggleEditPickerMode()">
                                                        <label class="btn btn-outline-warning btn-sm fw-bold"
                                                            for="editModeCustom">Custom</label>
                                                    </div>
                                                </div>

                                                <div class="col-12 mb-2">
                                                    <label class="form-label small fw-bold text-muted">Jumlah yang
                                                        Dipick</label>
                                                    <input type="number" name="pick_count" id="editPickCount"
                                                        class="form-control form-control-sm" min="1"
                                                        max="50">
                                                </div>

                                                <div id="editPickerRoleFilter" class="col-12 mb-2">
                                                    <label class="form-label small fw-bold text-muted">Filter Berdasarkan
                                                        Role</label>
                                                    <select name="pick_role_id" id="editPickRoleId"
                                                        class="form-select form-select-sm">
                                                        <option value="">Semua Member</option>
                                                        @foreach ($roles as $r)
                                                            <option value="{{ $r->id }}">{{ $r->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div id="editPickerCustomList" class="col-12 mb-2 d-none">
                                                    <label class="form-label small fw-bold text-muted">Daftar Pilihan (Satu
                                                        Per Baris)</label>
                                                    <textarea name="custom_pick_list" id="editCustomPickList" class="form-control form-control-sm" rows="3"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="modal-footer border-0 px-4 pb-4 bg-white">
                                <button type="button" class="btn btn-light fw-semibold px-4 me-auto"
                                    data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                                <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm"
                                    style="border-radius: 10px; color: #5a4b00;">
                                    <i class="fa fa-floppy-disk me-2"></i>Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal Create Role --}}
            <div class="modal fade" id="modalCreateRole" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                    <i class="fa fa-shield-halved text-primary"></i>
                                </div>
                                <div>
                                    <span class="fw-bold d-block">Buat Role Baru</span>
                                    <small class="text-muted fw-normal" style="font-size: 0.75rem;">Tentukan nama dan hak
                                        akses grup</small>
                                </div>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <form method="POST" action="/groups/{{ $group->id }}/roles">
                            @csrf
                            <div class="modal-body p-4">
                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-uppercase text-secondary">Nama Role</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i
                                                class="fa fa-tag text-muted"></i></span>
                                        <input type="text" name="name" class="form-control border-start-0 ps-0"
                                            placeholder="Contoh: Bendahara, Moderator..." required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-uppercase text-secondary">Warna
                                        Identitas</label>
                                    <div class="d-flex align-items-center p-2 border rounded bg-light">
                                        <input type="color" name="color"
                                            class="form-control form-control-color border-0 bg-transparent"
                                            id="colorPicker" value="#6c757d" title="Pilih warna role">
                                        <label for="colorPicker" class="ms-2 small text-muted">Warna ini akan muncul pada
                                            lencana member</label>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label fw-bold small text-uppercase text-secondary">Hak Akses
                                        (Permissions)</label>
                                    <div class="card border-0 bg-light">
                                        <div class="list-group list-group-flush rounded border shadow-sm">

                                            <div
                                                class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-box me-3 text-center" style="width: 30px;"><i
                                                            class="fa fa-plus text-primary"></i></div>
                                                    <div>
                                                        <h6 class="mb-0 small fw-bold">Buat Announcement</h6>
                                                        <p class="text-muted mb-0" style="font-size: 0.7rem;">Member bisa
                                                            membuat pengumuman baru</p>
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="can_create_announcement" id="cr_create">
                                                </div>
                                            </div>

                                            <div
                                                class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-box me-3 text-center" style="width: 30px;"><i
                                                            class="fa fa-pen text-warning"></i></div>
                                                    <div>
                                                        <h6 class="mb-0 small fw-bold">Moderat Pengumuman</h6>
                                                        <p class="text-muted mb-0" style="font-size: 0.7rem;">Edit/Hapus
                                                            pengumuman milik siapa pun</p>
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="can_edit_announcement" id="cr_edit">
                                                </div>
                                            </div>

                                            <div
                                                class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-box me-3 text-center" style="width: 30px;"><i
                                                            class="fa fa-users text-success"></i></div>
                                                    <div>
                                                        <h6 class="mb-0 small fw-bold">Kelola Anggota</h6>
                                                        <p class="text-muted mb-0" style="font-size: 0.7rem;">Mengatur
                                                            member dan struktur role</p>
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="can_manage_member" id="cr_member">
                                                </div>
                                            </div>

                                            <div
                                                class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-box me-3 text-center" style="width: 30px;"><i
                                                            class="fa fa-key text-info"></i></div>
                                                    <div>
                                                        <h6 class="mb-0 small fw-bold">Kode Undangan</h6>
                                                        <p class="text-muted mb-0" style="font-size: 0.7rem;">Membuat
                                                            kode akses masuk grup</p>
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="can_generate_code" id="cr_code">
                                                </div>
                                            </div>

                                            <div
                                                class="list-group-item d-flex justify-content-between align-items-center py-3 border-0">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-box me-3 text-center" style="width: 30px;"><i
                                                            class="fa fa-robot text-danger"></i></div>
                                                    <div>
                                                        <h6 class="mb-0 small fw-bold">Kelola Bot</h6>
                                                        <p class="text-muted mb-0" style="font-size: 0.7rem;">Mengatur
                                                            integrasi bot otomatis</p>
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="can_manage_bot" id="cr_bot">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer bg-light border-top-0 p-3">
                                <button type="button"
                                    class="btn btn-link text-decoration-none text-secondary fw-semibold"
                                    data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                    <i class="fa fa-check me-2"></i>Simpan Role
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal Edit Role --}}
            <div class="modal fade" id="modalEditRole" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 p-2 rounded me-3">
                                    <i class="fa fa-pen-to-square text-warning"></i>
                                </div>
                                <div>
                                    <span class="fw-bold d-block">Edit Role</span>
                                    <small class="text-muted fw-normal" style="font-size: 0.75rem;">Perbarui nama atau
                                        hak akses role ini</small>
                                </div>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <form method="POST" id="formEditRole">
                            @csrf
                            @method('PUT')
                            <div class="modal-body p-4">
                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-uppercase text-secondary">Nama
                                        Role</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i
                                                class="fa fa-tag text-muted"></i></span>
                                        <input type="text" name="name" id="editRoleName"
                                            class="form-control border-start-0 ps-0" placeholder="Contoh: Moderator"
                                            required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-uppercase text-secondary">Warna
                                        Identitas</label>
                                    <div class="d-flex align-items-center p-2 border rounded bg-light">
                                        <input type="color" name="color" id="editRoleColor"
                                            class="form-control form-control-color border-0 bg-transparent"
                                            title="Pilih warna role">
                                        <label for="editRoleColor" class="ms-2 small text-muted">Sesuaikan warna lencana
                                            role</label>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label fw-bold small text-uppercase text-secondary">Hak Akses
                                        (Permissions)</label>
                                    <div class="card border-0 bg-light">
                                        <div class="list-group list-group-flush rounded border shadow-sm">

                                            <div
                                                class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-box me-3 text-center" style="width: 30px;"><i
                                                            class="fa fa-plus text-primary"></i></div>
                                                    <div>
                                                        <h6 class="mb-0 small fw-bold">Buat Announcement</h6>
                                                        <p class="text-muted mb-0" style="font-size: 0.7rem;">Izinkan
                                                            membuat pengumuman baru</p>
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="can_create_announcement" id="er_create">
                                                </div>
                                            </div>

                                            <div
                                                class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-box me-3 text-center" style="width: 30px;"><i
                                                            class="fa fa-pen text-warning"></i></div>
                                                    <div>
                                                        <h6 class="mb-0 small fw-bold">Moderat Pengumuman</h6>
                                                        <p class="text-muted mb-0" style="font-size: 0.7rem;">Edit atau
                                                            hapus semua pengumuman</p>
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="can_edit_announcement" id="er_edit">
                                                </div>
                                            </div>

                                            <div
                                                class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-box me-3 text-center" style="width: 30px;"><i
                                                            class="fa fa-users text-success"></i></div>
                                                    <div>
                                                        <h6 class="mb-0 small fw-bold">Kelola Anggota</h6>
                                                        <p class="text-muted mb-0" style="font-size: 0.7rem;">Tambah,
                                                            hapus, atau ubah role anggota</p>
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="can_manage_member" id="er_member">
                                                </div>
                                            </div>

                                            <div
                                                class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-box me-3 text-center" style="width: 30px;"><i
                                                            class="fa fa-key text-info"></i></div>
                                                    <div>
                                                        <h6 class="mb-0 small fw-bold">Kode Undangan</h6>
                                                        <p class="text-muted mb-0" style="font-size: 0.7rem;">Membuat
                                                            atau mereset kode grup</p>
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="can_generate_code" id="er_code">
                                                </div>
                                            </div>

                                            <div
                                                class="list-group-item d-flex justify-content-between align-items-center py-3 border-0">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-box me-3 text-center" style="width: 30px;"><i
                                                            class="fa fa-robot text-danger"></i></div>
                                                    <div>
                                                        <h6 class="mb-0 small fw-bold">Kelola Bot</h6>
                                                        <p class="text-muted mb-0" style="font-size: 0.7rem;">
                                                            Konfigurasi asisten otomatis (Bot)</p>
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="can_manage_bot" id="er_bot">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer bg-light border-top-0 p-3">
                                <button type="button"
                                    class="btn btn-link text-decoration-none text-secondary fw-semibold"
                                    data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-warning px-4 shadow-sm fw-bold text-dark">
                                    <i class="fa fa-floppy-disk me-2"></i>Update Role
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

                function copyTelegramConnectLink() {
                    const input = document.getElementById('telegramConnectLink');
                    if (!input) return;

                    input.select();
                    input.setSelectionRange(0, 99999);
                    navigator.clipboard.writeText(input.value).then(() => {
                        showToast('Link koneksi disalin.', 'success');
                    }).catch(() => {
                        showToast('Gagal menyalin link.', 'danger');
                    });
                }

                async function pollTelegramClaim(groupId, botId) {
                    showToast('Mengecek status koneksi Telegram...', 'info');

                    const maxAttempts = 12;
                    const intervalMs = 2500;

                    for (let attempt = 1; attempt <= maxAttempts; attempt++) {
                        try {
                            const response = await fetch(`/groups/${groupId}/bots/${botId}/telegram-connect/claim`, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                },
                            });

                            const payload = await response.json();

                            if (response.status === 202) {
                                if (attempt === maxAttempts) {
                                    showToast(payload.message || 'Belum ada group yang claim token. Coba lagi sebentar.', 'warning');
                                }
                                await new Promise(resolve => setTimeout(resolve, intervalMs));
                                continue;
                            }

                            if (response.ok && payload.success) {
                                showToast(payload.message || 'Telegram berhasil terhubung.', 'success');
                                window.location.reload();
                                return;
                            }

                            showToast(payload.message || 'Gagal memeriksa status koneksi.', 'danger');
                            return;
                        } catch (error) {
                            if (attempt === maxAttempts) {
                                showToast('Gagal polling koneksi Telegram.', 'danger');
                            }
                            await new Promise(resolve => setTimeout(resolve, intervalMs));
                        }
                    }
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
                    const usePicker = btn.getAttribute('data-use_picker') === '1';
                    const pickerMode = btn.getAttribute('data-picker_mode') ?? 'members';
                    const pickCount = btn.getAttribute('data-pick_count') ?? 1;
                    const pickRoleId = btn.getAttribute('data-pick_role_id') ?? '';
                    const customList = btn.getAttribute('data-custom_list') ?? '';

                    document.getElementById('editTitle').value = title;
                    document.getElementById('editContent').value = content;
                    document.getElementById('editScheduled').value = scheduled ?? '';
                    document.getElementById('editRepeat').value = repeat ?? 'none';
                    document.getElementById('formEdit').action = `/groups/{{ $group->id }}/announcements/${id}`;

                    // Picker
                    document.getElementById('editUsePicker').checked = usePicker;
                    document.getElementById('editPickerOptions').classList.toggle('d-none', !usePicker);
                    document.getElementById('editPickCount').value = pickCount;
                    document.getElementById('editPickRoleId').value = pickRoleId;
                    document.getElementById('editCustomPickList').value = customList;

                    if (pickerMode === 'custom') {
                        document.getElementById('editModeCustom').checked = true;
                        document.getElementById('editPickerRoleFilter').classList.add('d-none');
                        document.getElementById('editPickerCustomList').classList.remove('d-none');
                    } else {
                        document.getElementById('editModeMembers').checked = true;
                        document.getElementById('editPickerRoleFilter').classList.remove('d-none');
                        document.getElementById('editPickerCustomList').classList.add('d-none');
                    }

                    const attachments = btn.getAttribute('data-attachments');
                    const existingDiv = document.getElementById('existingAttachments');
                    existingDiv.innerHTML = '';
                    document.getElementById('editPreview').innerHTML = '';
                    document.getElementById('editAttachments').value = '';

                    if (attachments && attachments !== '[]') {
                        try {
                            const list = JSON.parse(attachments);
                            if (list.length > 0) {
                                existingDiv.innerHTML =
                                    '<label class="form-label fw-semibold small mb-2">Lampiran Saat Ini</label>';
                                list.forEach(att => {
                                    const icon = att.type === 'image' ? 'fa-image text-primary' :
                                        'fa-file text-secondary';
                                    existingDiv.innerHTML += `
                        <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-1" id="att-${att.id}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa ${icon}"></i>
                                <span class="small">${att.filename}</span>
                            </div>
                            <button type="button"
                                    class="btn btn-outline-danger"
                                    style="font-size:10px; padding: 1px 6px;"
                                    onclick="deleteAttachment(${att.id}, ${att.announcement_id})">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>`;
                                });
                            }
                        } catch (e) {
                            console.log('parse attachments error:', e);
                        }
                    }
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

                function previewAttachments(input, previewId) {
                    const preview = document.getElementById(previewId);
                    preview.innerHTML = '';

                    if (input.files.length > 3) {
                        showToast('Maksimal 3 file!', 'warning');
                        input.value = '';
                        return;
                    }

                    Array.from(input.files).forEach(file => {
                        const isImage = file.type.startsWith('image/');
                        const size = file.size >= 1048576 ?
                            (file.size / 1048576).toFixed(2) + ' MB' :
                            (file.size / 1024).toFixed(2) + ' KB';

                        const div = document.createElement('div');
                        div.className = 'border rounded p-2 d-flex align-items-center gap-2';
                        div.style.maxWidth = '180px';

                        if (isImage) {
                            const img = document.createElement('img');
                            img.style = 'width:36px;height:36px;object-fit:cover;border-radius:4px;';
                            img.src = URL.createObjectURL(file);
                            div.appendChild(img);
                        } else {
                            const icon = document.createElement('i');
                            icon.className = 'fa fa-file fa-lg text-secondary';
                            div.appendChild(icon);
                        }

                        const info = document.createElement('div');
                        info.className = 'overflow-hidden';
                        info.innerHTML = `<div class="small text-truncate fw-semibold">${file.name}</div>
                          <div style="font-size:10px" class="text-muted">${size}</div>`;
                        div.appendChild(info);
                        preview.appendChild(div);
                    });
                }

                function deleteAttachment(e, attachmentId, announcementId) {
                    e.preventDefault();
                    if (!confirm('Hapus lampiran ini?')) return;

                    fetch(`/groups/{{ $group->id }}/announcements/${announcementId}/attachments/${attachmentId}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                _method: 'DELETE'
                            })
                        })
                        .then(res => {
                            if (res.ok) {
                                showToast('Lampiran berhasil dihapus!', 'success');
                                // Hapus elemen dari DOM
                                e.target.closest('.d-flex').remove();
                            }
                        });
                }

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

                function react(announcementId, emoji, btn) {
                    fetch(`/groups/{{ $group->id }}/announcements/${announcementId}/react`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                emoji: emoji
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            // Update semua tombol reaction untuk announcement ini
                            const container = document.getElementById(`reactions-${announcementId}`);
                            const buttons = container.querySelectorAll('.reaction-btn');

                            buttons.forEach(button => {
                                const btnEmoji = button.getAttribute('data-emoji');
                                const count = data.reactions[btnEmoji] ?? 0;
                                const isMyReact = btnEmoji === emoji && data.reacted;

                                // Update style
                                button.classList.remove('btn-primary', 'btn-outline-secondary');
                                button.classList.add(isMyReact ? 'btn-primary' : 'btn-outline-secondary');

                                // Update count
                                button.querySelector('.reaction-count').innerText = count > 0 ? count : '';
                            });
                        });
                }

                function togglePicker(prefix) {
                    const checked = document.getElementById('usePicker').checked;
                    document.getElementById('pickerOptions').classList.toggle('d-none', !checked);
                }

                function togglePickerMode(prefix) {
                    const mode = document.querySelector('input[name="picker_mode"]:checked').value;
                    const roleFilter = document.getElementById('pickerRoleFilter');
                    const customList = document.getElementById('pickerCustomList');

                    if (mode === 'custom') {
                        roleFilter.classList.add('d-none');
                        customList.classList.remove('d-none');
                    } else {
                        roleFilter.classList.remove('d-none');
                        customList.classList.add('d-none');
                    }
                }

                function previewPick(announcementId) {
                    const resultDiv = document.getElementById(`pickResult-${announcementId}`);
                    const spinner = document.getElementById(`spinner-${announcementId}`);
                    const namesDiv = document.getElementById(`names-${announcementId}`);
                    const btn = document.getElementById(`btnPick-${announcementId}`);

                    resultDiv.classList.remove('d-none');
                    namesDiv.innerHTML = '';
                    namesDiv.className = "d-flex flex-wrap gap-2 mt-2 justify-content-start";
                    spinner.innerText = '🎰 Mengundi...';

                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>Mengundi...';

                    fetch(`/groups/{{ $group->id }}/announcements/${announcementId}/pick`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('HTTP error ' + res.status);
                            return res.json();
                        })
                        .then(data => {
                            console.log('pick result:', data); // ← cek di console

                            const spinEmojis = ['🎰', '🎲', '🎯', '🎱', '🎪'];
                            let i = 0;
                            const interval = setInterval(() => {
                                spinner.innerText = spinEmojis[i % spinEmojis.length] + ' Mengundi...';
                                i++;
                            }, 150);

                            setTimeout(() => {
                                clearInterval(interval);
                                renderPickResult(announcementId, data.picked);
                            }, 2000);
                        })
                        .catch(err => {
                            console.error('pick error:', err);
                            spinner.innerText = '❌ Error: ' + err.message;
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fa fa-shuffle me-1"></i>Undi';
                        });
                }

                function renderPickResult(announcementId, picked) {
                    const spinner = document.getElementById(`spinner-${announcementId}`);
                    const namesDiv = document.getElementById(`names-${announcementId}`);
                    const resultDiv = document.getElementById(`pickResult-${announcementId}`);
                    const btn = document.getElementById(`btnPick-${announcementId}`);

                    if (!resultDiv || !spinner || !namesDiv || !btn) return;

                    resultDiv.classList.remove('d-none');
                    namesDiv.className = "d-flex flex-wrap gap-2 mt-2 justify-content-start";
                    spinner.innerText = '🎉 Terpilih!';

                    const colors = ['primary', 'success', 'danger', 'info', 'dark', 'secondary'];
                    namesDiv.innerHTML = picked.map((name, index) => {
                        const color = colors[Math.floor(Math.random() * colors.length)];
                        return `
            <div class="badge bg-${color} text-white px-3 py-2 d-flex align-items-center shadow-sm"
                 style="font-size:14px; border-radius: 50px; min-height: 35px;">
                <span class="opacity-75 me-1">#${index + 1}</span> 🎯 ${name}
            </div>`;
                    }).join('');

                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa fa-rotate me-1"></i>Undi Ulang';
                    btn.classList.remove('btn-outline-warning');
                    btn.classList.add('btn-warning');
                }

                function restorePickResults() {
                    document.querySelectorAll('[data-picked-result]').forEach(el => {
                        const announcementId = el.getAttribute('data-announcement-id');
                        const saved = el.getAttribute('data-picked-result');

                        if (saved && saved !== 'null' && saved !== '[]') {
                            try {
                                const picked = JSON.parse(saved);
                                if (picked && picked.length > 0) {
                                    renderPickResult(announcementId, picked);
                                }
                            } catch (e) {
                                console.log('parse error:', e);
                            }
                        }
                    });
                }

                restorePickResults();

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

                function toggleEditPicker() {
                    const checked = document.getElementById('editUsePicker').checked;
                    document.getElementById('editPickerOptions').classList.toggle('d-none', !checked);
                }

                function toggleEditPickerMode() {
                    const mode = document.querySelector('input[name="picker_mode"]:checked')?.value;
                    const roleFilter = document.getElementById('editPickerRoleFilter');
                    const customList = document.getElementById('editPickerCustomList');

                    if (mode === 'custom') {
                        roleFilter.classList.add('d-none');
                        customList.classList.remove('d-none');
                    } else {
                        roleFilter.classList.remove('d-none');
                        customList.classList.add('d-none');
                    }
                }

                function toggleStandaloneMode() {
                    const mode = document.querySelector('input[name="picker_mode_standalone"]:checked').value;
                    const members = document.getElementById('standaloneMemberOptions');
                    const custom = document.getElementById('standaloneCustomOptions');

                    if (mode === 'custom') {
                        members.classList.add('d-none');
                        custom.classList.remove('d-none');
                    } else {
                        members.classList.remove('d-none');
                        custom.classList.add('d-none');
                    }
                }

                function changeStandaloneCount(delta) {
                    const input = document.getElementById('standalonePickCount');
                    const newVal = parseInt(input.value) + delta;
                    if (newVal >= 1 && newVal <= 50) input.value = newVal;
                }

                function standalonePick() {
                    const mode = document.querySelector('input[name="picker_mode_standalone"]:checked').value;
                    const count = parseInt(document.getElementById('standalonePickCount').value);
                    const roleId = document.getElementById('standaloneRoleId')?.value ?? '';
                    const spinner = document.getElementById('standaloneSpinner');
                    const names = document.getElementById('standaloneNames');
                    const btn = document.getElementById('btnStandalonePick');

                    names.innerHTML = '';
                    spinner.innerHTML = '🎰 Mengundi...';
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>Mengundi...';

                    let body = {
                        count,
                        role_id: roleId,
                        mode
                    };

                    if (mode === 'custom') {
                        const list = document.getElementById('standaloneCustomList').value
                            .split('\n')
                            .map(s => s.trim())
                            .filter(s => s !== '');

                        if (list.length === 0) {
                            spinner.innerHTML = '<p class="text-danger small">Daftar nama kosong!</p>';
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fa fa-shuffle me-1"></i>Undi Sekarang!';
                            return;
                        }

                        if (count > list.length) {
                            spinner.innerHTML =
                                `<p class="text-danger small">Jumlah pick (${count}) melebihi daftar (${list.length})!</p>`;
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fa fa-shuffle me-1"></i>Undi Sekarang!';
                            return;
                        }

                        body.custom_list = list;
                    }

                    fetch(`/groups/{{ $group->id }}/picker`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(body)
                        })
                        .then(res => res.json())
                        .then(data => {
                            const spinEmojis = ['🎰', '🎲', '🎯', '🎱', '🎪'];
                            let i = 0;
                            const interval = setInterval(() => {
                                spinner.innerText = spinEmojis[i % spinEmojis.length] + ' Mengundi...';
                                i++;
                            }, 150);

                            setTimeout(() => {
                                clearInterval(interval);
                                spinner.innerText = '🎉 Terpilih!';

                                const colors = ['primary', 'success', 'danger', 'info', 'dark', 'secondary'];
                                names.innerHTML = data.picked.map((name, index) => {
                                    const color = colors[Math.floor(Math.random() * colors.length)];
                                    return `
                    <div class="badge bg-${color} text-white px-3 py-2 d-flex align-items-center shadow-sm"
                         style="font-size:14px; border-radius: 50px; min-height: 35px;">
                        <span class="opacity-75 me-1">#${index + 1}</span> 🎯 ${name}
                    </div>`;
                                }).join('');

                                btn.disabled = false;
                                btn.innerHTML = '<i class="fa fa-rotate me-1"></i>Undi Ulang!';
                                btn.classList.remove('btn-warning');
                                btn.classList.add('btn-success');
                            }, 2000);
                        });
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
