@extends('layouts.app')

@section('title', 'Chat — ' . ($citizenRequest->office->name ?? 'Office'))

@section('content')

@php
    $office   = $citizenRequest->office;
    $me       = Auth::user();
    $myInitial = strtoupper(substr($me->first_name ?? 'C', 0, 1));
    $isAr = app()->getLocale() === 'ar';
    $statusMap = $isAr ? [
        'pending'           => ['#f59e0b','قيد الانتظار'],
        'pending_payment'   => ['#60a5fa','بانتظار الدفع'],
        'in_review'         => ['#a78bfa','قيد المراجعة'],
        'missing_documents' => ['#fb923c','مستندات ناقصة'],
        'approved'          => ['#10b981','مقبول'],
        'rejected'          => ['#f87171','مرفوض'],
    ] : [
        'pending'           => ['#f59e0b','Pending'],
        'pending_payment'   => ['#60a5fa','Pending Payment'],
        'in_review'         => ['#a78bfa','In Review'],
        'missing_documents' => ['#fb923c','Missing Docs'],
        'approved'          => ['#10b981','Approved'],
        'rejected'          => ['#f87171','Rejected'],
    ];
    [$sColor,$sLabel] = $statusMap[$citizenRequest->status] ?? ['#94a3b8',ucfirst($citizenRequest->status)];
@endphp

<style>
    .content-area { padding: 0 !important; }
    .chat-nav { padding: .875rem 1.5rem .5rem; }
    .chat-shell {
        display:flex; height:calc(100vh - 104px); border-radius:16px; overflow:hidden;
        border:1px solid var(--border); background:var(--surface);
    }
    .chat-info-panel {
        width:280px; flex-shrink:0; border-right:1px solid var(--border);
        display:flex; flex-direction:column; overflow-y:auto;
    }
    .chat-main { flex:1; display:flex; flex-direction:column; min-width:0; }
    .chat-header {
        padding:.875rem 1.25rem; display:flex; align-items:center; gap:.875rem;
        background:#f8fafc; border-bottom:1px solid var(--border); flex-shrink:0;
    }
    .office-avatar {
        width:44px; height:44px; border-radius:50%; flex-shrink:0;
        background:linear-gradient(135deg,#4f46e5,#7c3aed);
        display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.1rem;
    }
    .chat-body {
        flex:1; overflow-y:auto; padding:1.25rem 1.5rem;
        display:flex; flex-direction:column; gap:.5rem;
        background:#ffffff;
    }
    .bubble-me   { align-self:flex-end;  max-width:65%; }
    .bubble-them { align-self:flex-start; max-width:65%; display:flex; gap:.5rem; align-items:flex-end; }
    .bubble-text-me {
        background:linear-gradient(135deg,var(--gold),var(--gold-light)); color:var(--navy);
        padding:.55rem 1rem; border-radius:18px 18px 4px 18px;
        font-size:.9rem; line-height:1.55; word-break:break-word; font-weight:500;
    }
    .bubble-text-them {
        background:#f1f5f9; color:var(--text); border:1px solid var(--border);
        padding:.55rem 1rem; border-radius:18px 18px 18px 4px;
        font-size:.9rem; line-height:1.55; word-break:break-word;
    }
    .bubble-meta  { font-size:.68rem; color:var(--muted); margin-top:.2rem; }
    .office-avatar-sm {
        width:30px; height:30px; border-radius:50%; flex-shrink:0;
        background:linear-gradient(135deg,#4f46e5,#7c3aed);
        display:flex; align-items:center; justify-content:center; color:#fff; font-size:.75rem;
    }
    .date-pill { text-align:center; margin:.75rem 0; }
    .date-pill span {
        background:#f1f5f9; color:var(--muted); border:1px solid var(--border);
        font-size:.7rem; padding:.25rem .9rem; border-radius:20px;
    }
    .chat-input-bar {
        padding:.875rem 1.25rem; display:flex; align-items:center; gap:.75rem;
        background:#ffffff; border-top:1px solid var(--border); flex-shrink:0;
    }
    #msg-input {
        flex:1; background:#f8fafc; border:1.5px solid var(--border);
        border-radius:24px; padding:.6rem 1.25rem; color:var(--text);
        font-size:.9rem; outline:none; transition:border-color .2s;
    }
    #msg-input:focus { border-color:var(--gold); }
    #send-btn {
        width:46px; height:46px; border-radius:50%; border:none; flex-shrink:0;
        background:linear-gradient(135deg,var(--gold),var(--gold-light)); color:var(--navy);
        display:flex; align-items:center; justify-content:center;
        cursor:pointer; font-size:1rem; transition:transform .15s,box-shadow .15s;
    }
    #send-btn:hover { transform:scale(1.08); box-shadow:0 4px 16px rgba(214,158,46,.4); }
    #send-btn:disabled { opacity:.5; cursor:not-allowed; transform:none; }

    @media (max-width: 768px) {
        .chat-info-panel { display: none; }
        .chat-shell { height: calc(100vh - 64px); border-radius: 0; border-left: none; border-right: none; }
        .bubble-me, .bubble-them { max-width: 84%; }
        .chat-nav { padding: .75rem 1rem .4rem; }
        .chat-header, .chat-input-bar { padding: .75rem 1rem; }
        .chat-body { padding: 1rem; }
    }
</style>

<div class="chat-nav">
    <a href="{{ route('citizen.my-requests') }}" style="color:var(--muted);font-size:.85rem;text-decoration:none">
        <i class="bi bi-arrow-left me-1"></i> {{ __('app.nav_my_requests') }}
    </a>
</div>

<div class="chat-shell">

    {{-- ── Left info panel ── --}}
    <div class="chat-info-panel">
        {{-- Office profile --}}
        <div class="p-4 text-center border-bottom" style="border-color:var(--border)!important">
            <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:#fff;margin:0 auto .75rem">
                <i class="bi bi-building"></i>
            </div>
            <div style="font-weight:700;color:var(--navy);font-size:1rem">{{ $office->name ?? 'Office' }}</div>
            @if($office?->city)
                <div style="font-size:.8rem;color:var(--muted);margin-top:.2rem">{{ $office->city }}</div>
            @endif
            @if($office?->phone)
                <div style="font-size:.78rem;color:var(--muted);margin-top:.15rem">
                    <i class="bi bi-telephone me-1"></i>{{ $office->phone }}
                </div>
            @endif
        </div>

        {{-- Request details --}}
        <div class="p-4">
            <div style="font-size:.7rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.875rem">{{ $isAr ? 'طلبك' : 'Your Request' }}</div>

            @foreach([
                [$isAr ? 'مرجع #' : 'Ref #',     $citizenRequest->reference_number ?? '—'],
                [__('pages.service_label'),   $citizenRequest->service->display_name ?? '—'],
                [__('pages.submitted'), $citizenRequest->created_at->format('d M Y')],
            ] as [$k,$v])
            <div class="mb-3">
                <div style="font-size:.72rem;color:var(--muted);margin-bottom:.2rem">{{ $k }}</div>
                <div style="font-size:.875rem;color:var(--text);font-weight:500">{{ $v }}</div>
            </div>
            @endforeach

            <div class="mb-3">
                <div style="font-size:.72rem;color:var(--muted);margin-bottom:.35rem">{{ __('app.status') }}</div>
                <span style="background:{{ $sColor }}22;border:1px solid {{ $sColor }}55;color:{{ $sColor }};font-size:.75rem;padding:.2rem .7rem;border-radius:20px;font-weight:600">
                    {{ $sLabel }}
                </span>
            </div>

            @if($citizenRequest->status === 'missing_documents')
            <div class="mt-3 p-3 rounded-3" style="background:rgba(249,115,22,.1);border:1px solid rgba(249,115,22,.3)">
                <div style="color:#9a3412;font-size:.78rem;font-weight:700;margin-bottom:.3rem">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $isAr ? 'إجراء مطلوب' : 'Action Required' }}
                </div>
                <p style="font-size:.78rem;color:var(--muted);margin:0">
                    {{ $isAr ? 'يرجى تحميل المستندات الناقصة. اسأل الدائرة عما هو مطلوب.' : "Please upload the missing documents. Ask the office what's needed." }}
                </p>
            </div>
            @endif

            {{-- Request Appointment --}}
            <div class="mt-4 pt-3" style="border-top:1px solid var(--border)">
                <div style="font-size:.7rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.75rem">
                    {{ $isAr ? 'موعد' : 'Appointment' }}
                </div>
                @if(session('success'))
                    <div style="background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.35);border-radius:8px;padding:.6rem .875rem;font-size:.78rem;color:#6ee7b7;margin-bottom:.75rem;">
                        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('citizen.appointments.request', $citizenRequest) }}">
                    @csrf
                    <div style="margin-bottom:.6rem;">
                        <label style="font-size:.72rem;color:var(--muted);display:block;margin-bottom:.3rem;">{{ $isAr ? 'التاريخ والوقت المفضل' : 'Preferred date & time' }}</label>
                        <input type="datetime-local" name="preferred_date"
                               class="form-control-custom" style="font-size:.82rem;color-scheme:dark;"
                               min="{{ now()->addHours(2)->format('Y-m-d\TH:i') }}" required>
                    </div>
                    <div style="margin-bottom:.75rem;">
                        <label style="font-size:.72rem;color:var(--muted);display:block;margin-bottom:.3rem;">{{ $isAr ? 'ملاحظات (اختياري)' : 'Notes (optional)' }}</label>
                        <textarea name="citizen_notes" rows="2" class="form-control-custom" style="font-size:.82rem;resize:none;"
                                  placeholder="{{ $isAr ? 'سبب الطلب...' : 'Reason for visit...' }}" maxlength="500"></textarea>
                    </div>
                    <button type="submit" class="btn-gold" style="width:100%;justify-content:center;font-size:.82rem;padding:.5rem;">
                        <i class="bi bi-calendar-plus"></i> {{ $isAr ? 'طلب موعد' : 'Request Appointment' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Office hours --}}
        @if($office?->opening_time && $office?->closing_time)
        <div class="px-4 pb-4 mt-auto">
            <div style="font-size:.7rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.5rem">{{ $isAr ? 'ساعات العمل' : 'Office Hours' }}</div>
            <div style="font-size:.82rem;color:var(--text)">
                <i class="bi bi-clock me-1" style="color:var(--gold)"></i>
                {{ \Carbon\Carbon::parse($office->opening_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($office->closing_time)->format('H:i') }}
            </div>
            @if($office->working_days)
                <div style="font-size:.78rem;color:var(--muted);margin-top:.25rem">{{ $office->working_days }}</div>
            @endif
        </div>
        @endif
    </div>

    {{-- ── Main chat ── --}}
    <div class="chat-main">

        {{-- Header --}}
        <div class="chat-header">
            <div class="office-avatar"><i class="bi bi-building"></i></div>
            <div style="flex:1">
                <div style="font-weight:700;color:var(--navy);font-size:.95rem;line-height:1.2">
                    {{ $office->name ?? ($isAr ? 'دعم الدائرة' : 'Office Support') }}
                </div>
                <div style="font-size:.75rem;color:#10b981">{{ $isAr ? '● فريق الدعم' : '● Support Team' }}</div>
            </div>
            <div style="font-size:.78rem;color:var(--muted)">
                <i class="bi bi-lock-fill me-1"></i> {{ $isAr ? 'آمن' : 'Secure' }}
            </div>
        </div>

        {{-- Messages --}}
        <div id="chat-messages" class="chat-body">
            @php $lastDate = null; @endphp
            @forelse($citizenRequest->messages as $msg)
                @php
                    $dl = $msg->created_at->isToday() ? ($isAr ? 'اليوم' : 'Today')
                        : ($msg->created_at->isYesterday() ? ($isAr ? 'أمس' : 'Yesterday')
                        : $msg->created_at->format('d M Y'));
                @endphp
                @if($dl !== $lastDate)
                    <div class="date-pill"><span>{{ $dl }}</span></div>
                    @php $lastDate = $dl; @endphp
                @endif

                @if(!$msg->is_office)
                {{-- Me (citizen) — right, gold --}}
                <div class="bubble-me">
                    <div class="bubble-text-me">{{ $msg->content }}</div>
                    <div class="bubble-meta text-end">
                        {{ $msg->created_at->format('H:i') }}
                        <i class="bi bi-check2-all ms-1" style="color:#34d399"></i>
                    </div>
                </div>
                @else
                {{-- Office — left, dark --}}
                <div class="bubble-them">
                    <div class="office-avatar-sm"><i class="bi bi-building" style="font-size:.65rem"></i></div>
                    <div>
                        <div class="bubble-text-them">{{ $msg->content }}</div>
                        <div class="bubble-meta">{{ $msg->created_at->format('H:i') }}</div>
                    </div>
                </div>
                @endif
            @empty
                <div id="empty-state" style="margin:auto;text-align:center">
                    <i class="bi bi-chat-dots" style="font-size:3rem;color:var(--muted);opacity:.25"></i>
                    <p style="color:var(--muted);font-size:.875rem;margin:.75rem 0 0">
                        {{ $isAr ? 'لا توجد رسائل بعد.' : 'No messages yet.' }}<br>{{ $isAr ? 'لا تتردد في سؤال الدائرة عن أي شيء.' : 'Feel free to ask the office anything.' }}
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Input bar --}}
        <div class="chat-input-bar">
            <input id="msg-input" type="text" placeholder="{{ $isAr ? 'اطرح سؤالاً أو أرسل رسالة…' : 'Ask a question or send a message…' }}" autocomplete="off" maxlength="2000">
            <button id="send-btn"><i class="bi bi-send-fill"></i></button>
        </div>
    </div>
</div>

{{-- Sent toast --}}
<div id="sent-toast" style="position:fixed;bottom:2rem;right:2rem;z-index:9999;
     background:linear-gradient(135deg,var(--gold),var(--gold-light));color:var(--navy);
     padding:.6rem 1.1rem;border-radius:12px;display:none;
     align-items:center;gap:.5rem;font-size:.83rem;font-weight:700;
     box-shadow:0 8px 24px rgba(0,0,0,.4)">
    <i class="bi bi-check-circle-fill"></i> {{ app()->getLocale() === 'ar' ? 'تم الإرسال' : 'Sent' }}
</div>

@endsection

@push('scripts')
<script>
const chatEl   = document.getElementById('chat-messages');
const input    = document.getElementById('msg-input');
const sendBtn  = document.getElementById('send-btn');
const toast    = document.getElementById('sent-toast');
const sendUrl  = '{{ route('citizen.requests.chat.send', $citizenRequest) }}';
const pollUrl  = '{{ route('citizen.requests.messages', $citizenRequest) }}';
const csrf     = '{{ csrf_token() }}';
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
    const isMe = !msg.is_office;
    const wrap = document.createElement('div');
    if(isMe){
        wrap.className = 'bubble-me';
        wrap.innerHTML = `
            <div class="bubble-text-me">${escHtml(msg.content)}</div>
            <div class="bubble-meta text-end">${msg.time} <i class="bi bi-check2-all ms-1" style="color:#34d399"></i></div>`;
    } else {
        wrap.className = 'bubble-them';
        wrap.innerHTML = `
            <div class="office-avatar-sm"><i class="bi bi-building" style="font-size:.65rem"></i></div>
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
    finally{ sendBtn.disabled=false; input.focus(); }
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
        const res = await fetch(`${pollUrl}?after=${lastId}`,{
            headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}
        });
        const msgs = await res.json();
        if(msgs.length){
            removeEmpty();
            const atBottom = chatEl.scrollHeight - chatEl.scrollTop - chatEl.clientHeight < 80;
            msgs.forEach(m=>{ mkBubble(m); lastId = m.id; });
            if(atBottom) chatEl.scrollTop = chatEl.scrollHeight;
        }
    } catch {}
}
setInterval(poll, 5000);

sendBtn.addEventListener('click', sendMessage);
input.addEventListener('keydown', e=>{ if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();sendMessage();} });
</script>
@endpush