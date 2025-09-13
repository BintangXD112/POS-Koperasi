@extends('layouts.app')

@section('title', 'Group Chat')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">{{ $room->name }}</h1>
        <p class="mt-1 text-sm text-gray-600">Semua user dapat membaca dan mengirim pesan</p>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            @if(auth()->user()->isAdmin())
                <form action="{{ route('chat.clear') }}" method="POST" class="mb-4 js-confirm" data-title="Bersihkan chat?" data-text="Semua pesan akan dihapus." data-icon="warning" data-confirm="Ya, bersihkan">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 text-sm bg-red-600 text-white rounded hover:bg-red-700">Clear Chat</button>
                </form>
            @endif
            <div id="messages" class="h-96 overflow-y-auto space-y-4 border-b pb-4">
                @forelse($messages as $message)
                    <div class="flex items-start gap-3" data-id="{{ $message->id }}">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-900">{{ $message->user->name }}</span>
                                @if($message->user && $message->user->role)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-100 text-blue-700">{{ $message->user->role->display_name }}</span>
                                @endif
                                <span class="text-xs text-gray-500">{{ $message->created_at->format('H:i') }}</span>
                                @if(auth()->user()->isAdmin())
                                    <button class="ml-auto text-xs text-red-600 hover:text-red-800 chat-delete" data-url="{{ route('chat.delete', $message) }}" title="Hapus">Hapus</button>
                                @endif
                            </div>
                            <div class="text-sm text-gray-800 whitespace-pre-line">{{ $message->content }}</div>
                            @if($message->attachment_path)
                                @php 
                                    $url = asset('storage/'.$message->attachment_path);
                                    $isImage = Str::startsWith($message->attachment_type, 'image/');
                                @endphp
                                @if($isImage)
                                    <div class="mt-2">
                                        <div class="text-xs text-gray-500 mb-1">[Gambar]</div>
                                        <a href="{{ $url }}" class="chat-image" data-full="{{ $url }}"><img src="{{ $url }}" alt="attachment" class="max-h-48 rounded"></a>
                                    </div>
                                @else
                                    <div class="mt-2">
                                        <div class="text-xs text-gray-500 mb-1">[Lampiran]</div>
                                        <a href="{{ $url }}" target="_blank" class="text-blue-600 hover:underline">Lihat lampiran</a>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center">Belum ada pesan</p>
                @endforelse
            </div>

            <form id="chatForm" action="{{ route('chat.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 flex items-end gap-2">
                @csrf
                <textarea id="chatInput" name="content" rows="2"
                    class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Tulis pesan..."></textarea>
                <input type="file" id="chatFile" name="attachment" class="hidden" accept="image/*,.pdf">
                <label for="chatFile" class="px-3 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50 cursor-pointer">Lampirkan</label>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Kirim</button>
            </form>
            <div id="attachInfo" class="mt-2 hidden">
                <div class="inline-flex items-center gap-2 px-2 py-1 rounded bg-gray-100 text-xs text-gray-700">
                    <span id="attachText"></span>
                    <button id="attachClear" type="button" class="text-gray-500 hover:text-gray-700">&times;</button>
                </div>
                <div id="attachThumb" class="mt-2"></div>
            </div>

            <!-- Image Lightbox -->
            <div id="imgLightbox" class="hidden fixed inset-0 z-50 bg-black/80 items-center justify-center p-4">
                <img id="imgLightboxEl" src="" alt="preview" class="max-h-full max-w-full rounded shadow" />
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-scroll to bottom on load
    const messages = document.getElementById('messages');
    messages.scrollTop = messages.scrollHeight;
    // AJAX submit to avoid reload
    const form = document.getElementById('chatForm');
    const input = document.getElementById('chatInput');
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const content = input.value.trim();
        const fileEl = document.getElementById('chatFile');
        if (!content && (!fileEl.files || fileEl.files.length === 0)) return;
        const fd = new FormData();
        fd.append('content', content);
        if (fileEl.files && fileEl.files[0]) fd.append('attachment', fileEl.files[0]);
        // Optimistically clear UI immediately
        input.value = '';
        if (fileEl) fileEl.value = '';
        if (attachInfo) { attachInfo.classList.add('hidden'); }
        if (attachThumb) { attachThumb.innerHTML = ''; }
        const res = await fetch(form.action, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: fd });
        if (res.ok) {
            await poll(true);
        }
    });

    // Shift+Enter to send
    input.addEventListener('keydown', function(e){
        if (e.key === 'Enter' && e.shiftKey) {
            e.preventDefault();
            form.requestSubmit();
        }
    });

    // Polling for latest messages
    let lastId = {{ $messages->last()?->id ?? 0 }};
    let isPolling = false;
    let lastRoomUpdate = 0;
    async function poll(forceScroll) {
        if (isPolling) return; // prevent overlapping polls that can cause duplicates
        isPolling = true;
        try {
            const url = `{{ route('chat.latest', [], false) }}` + `?since=${lastId}`;
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();
            if (data.room_updated_at && data.room_updated_at > lastRoomUpdate) {
                // If room had any update (e.g., deletion), reconcile snapshot once
                lastRoomUpdate = data.room_updated_at;
                await reconcileSnapshot();
                return;
            }
            if (!data.messages || data.messages.length === 0) return;
            const frag = document.createDocumentFragment();
            data.messages.forEach(m => {
                lastId = Math.max(lastId, m.id);
                const wrap = document.createElement('div');
                wrap.className = 'flex items-start gap-3';
                wrap.setAttribute('data-id', m.id);
                const roleBadge = m.role ? `<span class=\"text-[10px] px-1.5 py-0.5 rounded bg-blue-100 text-blue-700\">${escapeHtml(m.role)}</span>` : '';
                const attach = m.attachment_url ? (m.attachment_type && m.attachment_type.startsWith('image/')
                    ? `<div class=\"mt-2\"><div class=\"text-xs text-gray-500 mb-1\">[Gambar]</div><a href=\"${m.attachment_url}\" class=\"chat-image\" data-full=\"${m.attachment_url}\"><img src=\"${m.attachment_url}\" alt=\"attachment\" class=\"max-h-48 rounded\"></a></div>`
                    : `<div class=\"mt-2\"><div class=\"text-xs text-gray-500 mb-1\">[Lampiran]</div><a href=\"${m.attachment_url}\" target=\"_blank\" class=\"text-blue-600 hover:underline\">Lihat lampiran</a></div>`) : '';
                const delBtn = {{ auth()->user()->isAdmin() ? 'true' : 'false' }} ? `<button class=\"ml-auto text-xs text-red-600 hover:text-red-800 chat-delete\" data-url=\"${`{{ url('/chat/messages') }}`}/${m.id}\" title=\"Hapus\">Hapus</button>` : '';
                wrap.innerHTML = `<div class=\"flex-1\"><div class=\"flex items-center gap-2\"><span class=\"text-sm font-medium text-gray-900\">${escapeHtml(m.user)}</span>${roleBadge}<span class=\"text-xs text-gray-500\">${m.time}</span>${delBtn}</div><div class=\"text-sm text-gray-800 whitespace-pre-line\">${escapeHtml(m.content)}</div>${attach}</div>`;
                frag.appendChild(wrap);
            });
            messages.appendChild(frag);
            if (forceScroll || messages.scrollTop + messages.clientHeight >= messages.scrollHeight - 40) {
                messages.scrollTop = messages.scrollHeight;
            }
        } catch (e) { /* silent */ }
        finally { isPolling = false; }
    }

    function escapeHtml(str) {
        return str.replace(/[&<>"]+/g, function (c) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' })[c] || c;
        });
    }

    setInterval(poll, 3000);

    // Realtime via Echo (if available)
    try {
        if (window.Echo) {
            window.Echo.channel('chat.global')
                .listen('ChatMessageCreated', (e) => {
                    // show toast if not on chat page handled in layout; here just append
                    const m = e.message;
                    lastId = Math.max(lastId, m.id);
                    const wrap = document.createElement('div');
                    wrap.className = 'flex items-start gap-3';
                    wrap.setAttribute('data-id', m.id);
                    const roleBadge = m.role ? `<span class=\"text-[10px] px-1.5 py-0.5 rounded bg-blue-100 text-blue-700\">${escapeHtml(m.role)}</span>` : '';
                    const attach = m.attachment_url ? (m.attachment_type && m.attachment_type.startsWith('image/')
                        ? `<div class=\"mt-2\"><div class=\"text-xs text-gray-500 mb-1\">[Gambar]</div><a href=\"${m.attachment_url}\" class=\"chat-image\" data-full=\"${m.attachment_url}\"><img src=\"${m.attachment_url}\" alt=\"attachment\" class=\"max-h-48 rounded\"></a></div>`
                        : `<div class=\"mt-2\"><div class=\"text-xs text-gray-500 mb-1\">[Lampiran]</div><a href=\"${m.attachment_url}\" target=\"_blank\" class=\"text-blue-600 hover:underline\">Lihat lampiran</a></div>`) : '';
                    wrap.innerHTML = `<div class=\"flex-1\"><div class=\"flex items-center gap-2\"><span class=\"text-sm font-medium text-gray-900\">${escapeHtml(m.user)}</span>${roleBadge}<span class=\"text-xs text-gray-500\">${m.time}</span></div><div class=\"text-sm text-gray-800 whitespace-pre-line\">${escapeHtml(m.content || '')}</div>${attach}</div>`;
                    messages.appendChild(wrap);
                    messages.scrollTop = messages.scrollHeight;
                })
                .listen('ChatMessageDeleted', (e) => {
                    const el = messages.querySelector(`[data-id="${e.id}"]`);
                    if (el) el.remove();
                })
                .listen('ChatCleared', () => {
                    messages.innerHTML = '';
                    lastId = 0;
                });
        }
    } catch (err) { /* ignore */ }

    // Attachment selection UI (like WhatsApp)
    const fileEl = document.getElementById('chatFile');
    const attachInfo = document.getElementById('attachInfo');
    const attachText = document.getElementById('attachText');
    const attachThumb = document.getElementById('attachThumb');
    const attachClear = document.getElementById('attachClear');

    function humanSize(bytes){
        if (!bytes) return '0 B';
        const units = ['B','KB','MB','GB'];
        let i = 0; let v = bytes;
        while (v >= 1024 && i < units.length-1){ v /= 1024; i++; }
        return `${v.toFixed(v >= 10 ? 0 : 1)} ${units[i]}`;
    }

    fileEl.addEventListener('change', function(){
        attachThumb.innerHTML = '';
        if (!fileEl.files || !fileEl.files[0]) { attachInfo.classList.add('hidden'); return; }
        const f = fileEl.files[0];
        attachInfo.classList.remove('hidden');
        attachText.textContent = `${f.name} • ${humanSize(f.size)}`;
        if (f.type && f.type.startsWith('image/')){
            const img = document.createElement('img');
            img.className = 'mt-2 max-h-32 rounded';
            img.src = URL.createObjectURL(f);
            attachThumb.appendChild(img);
        }
    });

    attachClear.addEventListener('click', function(){
        fileEl.value = '';
        attachInfo.classList.add('hidden');
        attachThumb.innerHTML = '';
    });

    // Clickable images (lightbox)
    const lightbox = document.getElementById('imgLightbox');
    const lightboxImg = document.getElementById('imgLightboxEl');
    messages.addEventListener('click', function(e){
        const a = e.target.closest('a.chat-image');
        if (!a) return;
        e.preventDefault();
        lightboxImg.src = a.getAttribute('data-full') || a.getAttribute('href');
        lightbox.classList.remove('hidden');
        lightbox.classList.add('flex');
    });
    lightbox.addEventListener('click', function(){
        lightbox.classList.add('hidden');
        lightbox.classList.remove('flex');
        lightboxImg.src = '';
    });

    // Delete message (admin)
    messages.addEventListener('click', async function(e){
        const btn = e.target.closest('.chat-delete');
        if (!btn) return;
        e.preventDefault();
        const url = btn.getAttribute('data-url');
        try {
            const res = await fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } });
            if (res.ok) {
                // Remove from DOM immediately for all tabs via polling rebuild below
                const node = btn.closest('[data-id]');
                const removedId = node?.getAttribute('data-id');
                node?.remove();
                // Trigger a reconciliation snapshot to reflect deletions in other tabs quickly
                // We fetch a compact snapshot and rebuild the list to remove any gaps
                await reconcileSnapshot();
            }
        } catch {}
    });

    async function reconcileSnapshot(){
        try {
            const url = `{{ route('chat.latest', [], false) }}` + `?snapshot=1`;
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();
            if (!data.messages) return;
            // Rebuild DOM from snapshot
            messages.innerHTML = '';
            lastId = 0;
            const frag = document.createDocumentFragment();
            data.messages.forEach(m => {
                lastId = Math.max(lastId, m.id);
                const wrap = document.createElement('div');
                wrap.className = 'flex items-start gap-3';
                wrap.setAttribute('data-id', m.id);
                const roleBadge = m.role ? `<span class=\"text-[10px] px-1.5 py-0.5 rounded bg-blue-100 text-blue-700\">${escapeHtml(m.role)}</span>` : '';
                const attach = m.attachment_url ? (m.attachment_type && m.attachment_type.startsWith('image/')
                    ? `<div class=\"mt-2\"><div class=\"text-xs text-gray-500 mb-1\">[Gambar]</div><a href=\"${m.attachment_url}\" class=\"chat-image\" data-full=\"${m.attachment_url}\"><img src=\"${m.attachment_url}\" alt=\"attachment\" class=\"max-h-48 rounded\"></a></div>`
                    : `<div class=\"mt-2\"><div class=\"text-xs text-gray-500 mb-1\">[Lampiran]</div><a href=\"${m.attachment_url}\" target=\"_blank\" class=\"text-blue-600 hover:underline\">Lihat lampiran</a></div>`) : '';
                const delBtn = {{ auth()->user()->isAdmin() ? 'true' : 'false' }} ? `<button class=\"ml-auto text-xs text-red-600 hover:text-red-800 chat-delete\" data-url=\"${`{{ url('/chat/messages') }}`}/${m.id}\" title=\"Hapus\">Hapus</button>` : '';
                wrap.innerHTML = `<div class=\"flex-1\"><div class=\"flex items-center gap-2\"><span class=\"text-sm font-medium text-gray-900\">${escapeHtml(m.user)}</span>${roleBadge}<span class=\"text-xs text-gray-500\">${m.time}</span>${delBtn}</div><div class=\"text-sm text-gray-800 whitespace-pre-line\">${escapeHtml(m.content)}</div>${attach}</div>`;
                frag.appendChild(wrap);
            });
            messages.appendChild(frag);
            messages.scrollTop = messages.scrollHeight;
        } catch {}
    }
</script>
@endsection


