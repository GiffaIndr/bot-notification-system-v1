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
        text-decoration: none;
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
            <i class="fa fa-bullhorn fs-4"></i>
        </div>

        <div>
            <a href="/groups/{{ $group->id }}" class="btn-back text-decoration-none">
                <i class="fa fa-arrow-left me-2"></i>Kembali ke Workspace
            </a>
            <h2 class="group-title mb-0 fw-bold">{{ $group->name }} - Semua Announcement</h2>
        </div>
    </div>

    <div class="text-end">
        <div class="text-muted small">
            <i class="fa fa-bullhorn me-1 text-primary"></i>
            <strong>{{ $announcements->count() }}</strong> Announcement
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card announcement-container shadow-sm">
            <div class="card-header announcement-header fw-bold d-flex justify-content-between align-items-center bg-white">
                <span class="fs-5 text-dark">
                    <i class="fa fa-bullhorn text-primary me-2"></i>Daftar Announcement
                </span>
                @if ($role->can_create_announcement)
                    <a href="/groups/{{ $group->id }}" class="btn btn-sm btn-primary fw-bold px-3 shadow-sm">
                        <i class="fa fa-plus-circle me-1"></i>Buat Baru
                    </a>
                @endif
            </div>

            <div class="card-body p-0">
                @forelse ($announcements as $announcement)
                    <div class="card mb-4 shadow-sm announcement-item {{ $announcement->is_pinned ? 'is-pinned' : 'border-0' }}">
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
    </div>

    {{-- Sidebar Info --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4" style="position: sticky; top: 90px;">
            <div class="card-body">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="fa fa-info-circle text-primary me-2"></i>Informasi Workspace
                </h6>
                <div class="list-group list-group-flush">
                    <div class="list-group-item px-0 py-2 border-0">
                        <small class="text-muted">Nama Workspace</small>
                        <div class="fw-bold text-dark">{{ $group->name }}</div>
                    </div>
                    <div class="list-group-item px-0 py-2 border-0">
                        <small class="text-muted">Total Announcement</small>
                        <div class="fw-bold text-dark">{{ $announcements->count() }}</div>
                    </div>
                    <div class="list-group-item px-0 py-2 border-0">
                        <small class="text-muted">Dibuat Pada</small>
                        <div class="fw-bold text-dark">{{ $group->created_at->format('d M Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
                const buttons = container.querySelectorAll('.btn-reaction');

                buttons.forEach(button => {
                    const buttonEmoji = button.textContent.trim().split(' ')[0];
                    const count = data.reactions[buttonEmoji] || 0;
                    const isReacted = data.myReactions.includes(buttonEmoji);

                    button.className = `btn-reaction ${isReacted ? 'active' : ''}`;
                    const countSpan = button.querySelector('span');
                    if (countSpan) {
                        countSpan.textContent = count > 0 ? count : '';
                    }
                });
            })
            .catch(() => {});
    }

    function confirmDelete(announcementId) {
        if (confirm('Yakin ingin menghapus announcement ini?')) {
            document.getElementById(`deleteForm${announcementId}`).submit();
        }
    }

    function previewPick(announcementId) {
        fetch(`/groups/{{ $group->id }}/announcements/${announcementId}/pick`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            const button = document.getElementById(`btnPick-${announcementId}`);
            const spinner = document.getElementById(`spinner-${announcementId}`);
            const names = document.getElementById(`names-${announcementId}`);
            const result = document.getElementById(`pickResult-${announcementId}`);

            button.textContent = '🔄 Undi Ulang';
            button.className = 'btn btn-sm btn-warning rounded-pill px-3 fw-bold';

            spinner.innerHTML = '🎉 Hasil Undian:';
            names.innerHTML = data.picked_users.map(user =>
                `<span class="badge bg-success">${user}</span>`
            ).join('');

            result.classList.remove('d-none');
        })
        .catch(() => alert('Gagal melakukan undian'));
    }
</script>

@endsection
