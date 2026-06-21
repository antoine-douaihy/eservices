<style>
    .chatbot-wrapper {
        position: fixed;
        bottom: 28px;
        right: 28px;
        z-index: 9999;
        font-family: 'DM Sans', system-ui, -apple-system, sans-serif;
    }

    /* In Arabic the sidebar moves to the right edge of the screen, so
       move the chat widget to the left edge instead to avoid covering
       the sidebar's Sign Out link. */
    html[dir="rtl"] .chatbot-wrapper {
        right: auto;
        left: 28px;
    }

    /* ── TOGGLE BUTTON ── */
    .chatbot-toggle {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        background: linear-gradient(135deg, #d97706, #f59e0b);
        color: #ffffff;
        border: none;
        border-radius: 50px;
        padding: 0.95rem 1.5rem;
        font-family: 'DM Sans', sans-serif;
        font-size: 1.05rem;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 8px 24px rgba(217,119,6,0.35);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .chatbot-toggle i { font-size: 1.2rem; }

    .chatbot-toggle:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(217,119,6,0.45);
    }

    /* ── WINDOW ── */
    .chatbot-window {
        display: none;
        width: 390px;
        height: 580px;
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 24px 60px rgba(15,23,42,0.25);
        flex-direction: column;
        overflow: hidden;
    }

    .chatbot-header {
        background: linear-gradient(135deg, #1e3a5f, #2d5a8e);
        color: #ffffff;
        padding: 1.1rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }

    .chatbot-header-info { display: flex; align-items: center; gap: 0.75rem; }

    .chatbot-avatar {
        width: 42px; height: 42px; border-radius: 50%;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.15rem; flex-shrink: 0;
    }

    .chatbot-header-title {
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: 1.05rem;
        line-height: 1.2;
    }

    .chatbot-header-status {
        font-size: 0.78rem;
        color: rgba(255,255,255,0.65);
        display: flex; align-items: center; gap: 0.35rem;
        margin-top: 2px;
    }
    .chatbot-header-status .dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: #34d399; flex-shrink: 0;
        box-shadow: 0 0 0 2px rgba(52,211,153,0.25);
    }

    .chatbot-close {
        background: rgba(255,255,255,0.12);
        border: none;
        color: #ffffff;
        font-size: 1rem;
        cursor: pointer;
        width: 36px; height: 36px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.2s;
        flex-shrink: 0;
    }
    .chatbot-close:hover { background: rgba(255,255,255,0.22); }

    /* ── MESSAGES ── */
    .chatbot-messages {
        flex: 1;
        padding: 1.25rem;
        overflow-y: auto;
        background-color: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 0.9rem;
    }

    .message-row { display: flex; align-items: flex-end; gap: 0.6rem; max-width: 88%; }
    .message-row.user-row { align-self: flex-end; flex-direction: row-reverse; }
    .message-row.bot-row { align-self: flex-start; }

    .message-avatar {
        width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0;
        background: linear-gradient(135deg, #1e3a5f, #2d5a8e);
        display: flex; align-items: center; justify-content: center;
        color: #ffffff; font-size: 0.78rem;
    }

    .message {
        padding: 0.8rem 1.05rem;
        border-radius: 16px;
        font-size: 1rem;
        line-height: 1.55;
        word-break: break-word;
        animation: chatbot-pop 0.18s ease;
    }

    @keyframes chatbot-pop {
        from { opacity: 0; transform: translateY(4px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .bot-message {
        background-color: #ffffff;
        color: #1e293b;
        border: 1px solid #e2e8f0;
        border-radius: 16px 16px 16px 4px;
    }
    html[dir="rtl"] .bot-message { border-radius: 16px 16px 4px 16px; }

    .user-message {
        background: linear-gradient(135deg, #d97706, #f59e0b);
        color: #ffffff;
        border-radius: 16px 16px 4px 16px;
        font-weight: 500;
    }
    html[dir="rtl"] .user-message { border-radius: 16px 16px 16px 4px; }

    /* Typing indicator */
    .typing-dots { display: flex; gap: 4px; padding: 0.3rem 0; }
    .typing-dots span {
        width: 7px; height: 7px; border-radius: 50%;
        background: #94a3b8; display: inline-block;
        animation: chatbot-bounce 1.2s infinite ease-in-out;
    }
    .typing-dots span:nth-child(2) { animation-delay: 0.15s; }
    .typing-dots span:nth-child(3) { animation-delay: 0.3s; }
    @keyframes chatbot-bounce {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.5; }
        30% { transform: translateY(-4px); opacity: 1; }
    }

    /* ── INPUT AREA ── */
    .chatbot-input-area {
        padding: 1rem;
        background-color: #ffffff;
        border-top: 1px solid #e2e8f0;
        display: flex;
        gap: 0.6rem;
        flex-shrink: 0;
    }

    .chatbot-input {
        flex: 1;
        padding: 0.8rem 1.1rem;
        font-size: 1rem;
        font-family: 'DM Sans', sans-serif;
        border: 1.5px solid #e2e8f0;
        border-radius: 24px;
        outline: none;
        background: #f8fafc;
        color: #1e293b;
        transition: border-color 0.2s, background 0.2s;
        min-width: 0;
    }
    .chatbot-input:focus { border-color: #d97706; background: #ffffff; }

    .chatbot-send {
        background: linear-gradient(135deg, #1e3a5f, #2d5a8e);
        color: #ffffff;
        border: none;
        width: 46px; height: 46px;
        border-radius: 50%;
        font-size: 1.05rem;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .chatbot-send:hover { trans