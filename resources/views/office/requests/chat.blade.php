@extends('admin.layouts.app')

@section('title', 'Chat — ' . ($citizenRequest->user->first_name ?? ''))
@section('page-title', 'Chat')

@section('content')

@php
    $citizen = $citizenRequest->user;
    $initial = strtoupper(substr($citizen->first_name ?? 'C', 0, 1));
    $statusMap = [
        'pending'           => ['#f59e0b','Pending'],
        'pending_payment'   => ['#60a5fa','Pending Payment'],
        'in_review'         => ['#a78bfa','In Review'],
        'missing_documents' => ['#fb923c','Missing Docs'],
        'approved'          => ['#10b981','Approved'],
        'rejected'          => ['#f87171','Rejected'],
    ];
    [$sColor,$sLabel] = $statusMap[$citizenRequest->status] ?? ['#94a3b8', ucfirst($citizenRequest->status)];
@endphp

<style>
    .content-area { padding: 0 !important; }
    .chat-nav { padding: .875rem 1.5rem .5rem; }
    /* Full-height chat wrapper */
    .chat-shell {
        display:flex; height:calc(100vh - 104px); border-radius:16px; overflow:hidden;
        border:1px solid var(--border); background:var(--surface);
    }
    /* Info panel */
    .chat-info-panel {
        width:300px; flex-shrink:0; border-right:1px solid var(--border);
        display:flex; flex-direction:column; overflow-y:auto;
    }
    /* Main chat column */
    .chat-main {
        flex:1; display:flex; flex-direction:column; min-width:0;
    }
    /* Chat header */
    .chat-header {
        padding:.875rem 1.25rem; display:flex; align-items:center; gap:.875rem;
        background:rgba(255,255,255,.04); border-bottom:1px solid var(--border); flex-shrink:0;
    }
    .chat-avatar {
        width:44px; height:44px; border-radius:50%; flex-shrink:0;
        background:linear-gradient(135deg,#0d9488,#047857);
        display:flex; align-items:center; justify-content:center;
        font-weight:800; color:#fff; font-size:1rem;
    }
    /* Messages scroll area */
    .chat-body {
        flex:1; overflow-y:auto; padding:1.25rem 1.5rem;
        display:flex; flex-direction:column; gap:.5rem;
        background:repeating-linear-gradient(
            0deg, rgba(255,255,255,.012) 0px, rgba(255,255,255,.012) 1px,
            transparent 1px, transparent 40px);
    }
    /* Bubble */
    .bubble-me   { align-self:flex-end;  max-width:65%; }
    .bubble-them { align-self:flex-start; max-width:65%; display:flex; gap:.5rem; align-items:flex-end; }
    .bubble-text-me {
        background:linear-gradient(135deg,#047857,#059669); color:#fff;
        padding:.55rem 1rem; border-radius:18px 18px 4px 18px;
        font-size:.9rem; line-height:1.55; word-break:break-word;
    }
    .bubble-text-them {
        background:rgba(255,255,255,.09); color:var(--text);
        padding:.55rem 1rem; border-radius:18px 18px 18px 4px;
        font-size:.9rem; line-height:1.55; word-break:break-word;
    }
    .bubble-meta { font-size:.68rem; color:var(--muted); margin-top:.2rem; }
    .bubble-avatar-sm {
        width:30px; height:30px; border-radius:50%; flex-shrink:0;
        background:linear-gradient(135deg,#0d9488,#047857);
        display:flex; align-items:center; justify-content:center;
        font-size:.7rem; font-weight:700; color:#fff;
    }
    /* Date pill */
    .date-pill {
        text-align:center; margin:.75rem 0;
    }
    .date-pill span {
        background:rgba(255,255,255,.07); color:var(--muted);
        font-size:.7rem; padding:.25rem .9rem; border-radius:20px;
    }
    /* Input bar */
    .chat-input-bar {
        padding:.875rem 1.25rem; display:flex; align-items:center; gap:.75rem;
        background:rgba(255,255,255,.04); border-top:1px solid var(--border); flex-shrink:0;
    }
    #msg-input {
        flex:1; background:rgba(255,255,255,.07); border:1.5px solid var(--border);
        border-radius:24px; padding:.6rem 1.25rem; color:var(--text);
        font-size:.9rem; outline:none; transition:border-color .2s;
    }
    #msg-input:focus { border-color:var(--emerald); }
    #send-btn {
        width:46px; height:46px; border-radius:50%; border:none; flex-shrink:0;
        background:linear-gradient(135deg,#047857,#059669); color:#fff;
        display:flex; align-items:center; justify-content:center;
        cursor:pointer; font-size:1rem; transition:transform .15s, box-shadow .15s;
    }
    #send-btn:hover { transform:scale(1.08); box-shadow:0 4px 16px rgba(4,120,87,.4); }
    #send-btn:disabled { opacity:.5; cursor:not-allowed; transform:none; }
</style>

<div class="chat-nav">
    <a href="{{ route('office.dashboard') }}" style="color:var(--muted);font-size:.85rem;text-decoration:none">
        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
    </a>
</div>

<div class="chat-shell">

    {{-- ── Left info panel ── --}}
    <div class="chat-info-panel">
        {{-- Citizen profile --}}
        <div class="p-4 text-center border-bottom" style="border-color:var(--border)!important">
            <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#0d9488,#047857);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:800;color:#fff;margin:0 auto .75rem">
                {{ $initial }}
            </div>
            <div style="font-weight:700;color:#fff;font-size:1rem">{{ $citizen->first_name }} {{ $citizen->last_name }}</div>
            <div style="font-size:.8rem;color:var(--muted);margin-top:.2rem">{{ $citizen->email }}</div>
        </div>

        {{-- Request details --}}
        <div class="p-4">
            <div style="font-size:.7rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.875rem">Request Details</div>

            @foreach([
                ['Ref #',      $citizenRequest->reference_number],
                ['Service',    $citizenRequest->service->name ?? '—'],
                ['Office',     $citizenRequest->office->name ?? '—'],
                ['Submitted',  $citizenRequest->created_at->format('d M Y')],
            ] as [$k,$v])
            <div class="mb-3">
                <div style="font-size:.72rem;color:var(--muted);margin-bottom:.2rem">{{ $k }}</div>
                <div style="font-size:.875rem;color:var(--text);font-weight:500">{{ $v }}</div>
            </div>
            @endforeach

            <div class="mb-3">
                <div style="font-size:.72rem;color:var(--muted);margin-bottom:.35rem">Status</div>
                <span style="background:{{ $sColor }}22;border:1px solid {{ $sColor }}55;color:{{ $sColor }};font-size:.75rem;padding:.2rem .7rem;border-radius:20px;font-weight:600">
                    {{ $sLabel }}
                </span>
            </div>
        </div>

        {{-- Quick status change --}}
        <div class="p-4 mt-auto border-top" style="border-color:var(--border)!important">
            <div style="font-size:.7rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.75rem">Update Status</div>
            <form method="POST" action="{{ route('office.requests.status', $citizenRequest) }}">
                @csrf @method('PATCH')
                <select name="status" class="form-select-custom mb-2" style="font-size:.8rem">
                    @foreach(['in_review'=>'In Review','missing_documents'=>'Missing Documents','approved'=>'Approved','rejected'=>'Rejected'] as $val=>$lbl)
                        <option value="{{ $val }}" {{ $citizenRequest->status===$val?'selected':'' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-ghost btn-sm w-100">Update</button>
            </form>
        </div>
    </div>

    {{-- ── Main chat ── --}}
    <div class="chat-main">

        {{-- Header --}}
        <div class="chat-header">
            <div class="chat-avatar">{{ $initial }}</div>
            <div style="flex:1">
                <div style="font-weight:700;color:#fff;font-size:.95rem;line-height:1.2">
                    {{ $citizen->first_name }} {{ $citizen->last_name }}
                </div>
                <div id="status-line" style="font-size:.75rem;color:#10b981">● Online</div>
            </div>
            <div style="font-size:.8rem;color:var(--muted)">
                <i class="bi bi-shield-lock me-1"></i> Secure channel
            </div>
        </div>

        {{-- Messages --}}
        <div id="chat-messages" class="chat-body">
            @php $lastDate = null; @endphp
            @forelse($citizenRequest->messages as $msg)
                @php
                    $dl = $msg->created_at->isToday() ? 'Today'
                        : ($msg->created_at->isYesterday() ? 'Yesterday'
                        : $msg->created_at->format('d M Y'));
                @endphp
                @if($dl !== $lastDate)
                    <div class="date-pill"><span>{{ $dl }}</span></div>
                    @php $lastDate = $dl; @endphp
                @endif

                @if($msg->is_office)
                <div class="bubble-me">
                    <div class="bubble-text-me">{{ $msg->content }}</div>
                    <div class="bubble-meta text-end">
                        {{ $msg->created_at->format('H:i') }}
                        <i class="bi bi-check2-all ms-1" style="color:#34d399"></i>
                    </div>
                </div>
                @else
                <div class="bubble-them">
                    <div class="bubble-avatar-sm">{{ $initial }}</div>
                    <div>
                        <div class="bubble-text-them">{{ $msg->content }}</div>
                        <div class="bubble-meta">{{ $msg->created_at->format('H:i') }}</div>
                    </div>
                </div>
                @endif
            @empty
                <div id="empty-state" style="margin:auto;text-align:center">
                    <i class="bi bi-chat-dots" style="font-size:3rem;color:var(--muted);opacity:.25"></i>
                    <p style="color:var(--muted);font-size:.875rem;margin:.75rem 0 0">No messages yet.<br>Start the conversation.</p>
                </div>
            @endforelse
        </div>

        {{-- Input bar --}}
        <div class="chat-input-bar">
            <input id="msg-input" type="text" placeholder="Type a message…" autocomplete="off" maxlength="2000">
            <button id="send-btn"><i class="bi bi-send-fill"></i></button>
        </div>
    </div>
</div>

{{-- Sent toast --}}
<div id="sent-toast" style="position:fixed;bottom:2rem;right:2rem;z-index:9999;
     background:linear-gradient(135deg,#047857,#059669);color:#fff;
     padding:.6rem 1.1rem;border-radius:12px;display:none;
     align-items:center;gap:.5rem;font-size:.83rem;font-weight:600;
     box-shadow:0 8px 24px rgba(0,0,0,.4)">
    <i class="bi bi-check-circle-fill"></i> Sent
</div>

@endsection

@push('scripts')
<script>
const chatEl   = document.getElementById('chat-messages');
const input    = document.getElementById('msg-input');
const sendBtn  = document.getElementById('send-btn');
const toast    = document.getElementById('sent-toast');
const sendUrl  = '{{ route('office.requests.chat.send', $citizenRequest) }}';
const pollUrl  = '{{ route('office.requests.messages', $citizenRequest) }}';
const csrf     = '{{ csrf_token() }}';
const myInitial = '{{ $initial }}';
let lastId     = {{ $citizenRequest->messages->last()?->id ?? 0 }};
let lastDate   = '';
const seenIds  = new Set([{{ $citizenRequest->messages->pluck('id')->join(',') }}]);

chatEl.scrollTop = chatEl.scrollHeight;

function escHtml(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

function removeEmpty(){ const e=document.getElementById('empty-state'); if(e) e.remove(); }

function appendDatePill(date){
    const d = document.createElement('div');
    d.className = 'date-pill';
    d.innerHTML = `<span>${escHtml(date)}</span>`;
    chatEl.appendChild(d);
    lastDate = date;
}

function mkBubble(msg){
    if(msg.id && seenIds.has(msg.id)) return;
    if(msg.id) seenIds.add(msg.id);
    if(msg.date && msg.date !== lastDate) appendDatePill(msg.date);

    const wrap = document.createElement('div');
    if(msg.is_office){
        wrap.className = 'bubble-me';
        wrap.innerHTML = `
            <div class="bubble-text-me">${escHtml(msg.content)}</div>
            <div class="bubble-meta text-end">${msg.time} <i class="bi bi-check2-all ms-1" style="color:#34d399"></i></div>`;
    } else {
        wrap.className = 'bubble-them';
        wrap.innerHTML = `
            <div class="bubble-avatar-sm">${myInitial}</div>
            <div>
                <div class="bubble-text-them">${escHtml(msg.content)}</div>
                <div class="bubble-meta">${msg.time}</div>
            </div>`;
    }
    chatEl.appendChild(wrap);
}

function showToast(){
    toast.style.display = 'flex';
    clearTimeout(toast._t);
    toast._t = setTimeout(()=>{
        toast.style.transition='opacity .4s'; toast.style.opacity='0';
        setTimeout(()=>{ toast.style.display='none'; toast.style.opacity='1'; },400);
    },2000);
}

async function sendMessage(){
    const content = input.value.trim();
    if(!content) return;
    input.value = '';
    sendBtn.disabled = true;
    try {
        const fd = new FormData();
        fd.append('content', content);
        const res = await fetch(sendUrl,{
            method:'POST',
            headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
            body:fd
        });
        if(res.ok){
            const data = await res.json();
            removeEmpty();
            mkBubble(data);
            if(data.id) lastId = data.id;
            chatEl.scrollTop = chatEl.scrollHeight;
            showToast();
        } else {
            console.error('Send failed:', res.status);
        }
    } catch(e){ console.error('Send error:', e); }
    finally { sendBtn.disabled=false; input.focus(); }
}

// Real-time via Pusher WebSocket
if(window.Echo){
    window.Echo.private('citizen-request.{{ $citizenRequest->id }}')
        .listen('.message.sent', (msg) => {
            removeEmpty();
            const atBottom = chatEl.scrollHeight - chatEl.scrollTop - chatEl.clientHeight < 80;
            mkBubble(msg);
            lastId = msg.id;
            if(atBottom) chatEl.scrollTop = chatEl.scrollHeight;
        });
}

// Fallback polling (in case WebSocket is unavailable)
async function poll(){
    try {
        const res = await fetch(`${pollUrl}?after=${lastId}`,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
        const msgs = await res.json();
        if(msgs.length){
            removeEmpty();
            const atBottom = chatEl.scrollHeight - chatEl.scrollTop - chatEl.clientHeight < 80;
            msgs.forEach(m=>{ mkBubble(m); lastId=m.id; });
            if(atBottom) chatEl.scrollTop = chatEl.scrollHeight;
        }
    } catch {}
}
setInterval(poll, 5000);

sendBtn.addEventListener('click', sendMessage);
input.addEventListener('keydown', e=>{ if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();sendMessage();} });
</script>
@endpush
