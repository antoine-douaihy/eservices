<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4 portrait; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            background: #ffffff;
            color: #0d1f3c;
            width: 210mm;
        }

        .page { width: 210mm; }

        /* ── HEADER ── */
        .header { background: #0d1f3c; padding: 24px 44px 20px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .brand-name { font-size: 22px; font-weight: bold; color: #ffffff; letter-spacing: 1.5px; }
        .brand-dash { color: #d69e2e; }
        .brand-sub {
            font-size: 8.5px; color: rgba(255,255,255,0.45);
            letter-spacing: 3px; text-transform: uppercase; margin-top: 4px;
        }
        .receipt-badge {
            font-size: 8.5px; font-weight: bold; letter-spacing: 5px;
            text-transform: uppercase; color: #d69e2e; margin-bottom: 4px;
        }
        .receipt-title {
            font-size: 20px; font-weight: bold; color: #ffffff;
            letter-spacing: 2px; text-transform: uppercase;
        }
        .receipt-meta {
            font-size: 9px; color: rgba(255,255,255,0.45); margin-top: 4px; letter-spacing: 1px;
        }

        /* ── GOLD STRIPE ── */
        .gold-stripe { background: #d69e2e; height: 4px; }

        /* ── META INFO ── */
        .meta-section { padding: 24px 44px 20px; }
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td { padding: 7px 10px 7px 0; vertical-align: top; }
        .meta-label {
            font-size: 8.5px; color: #9ca3af;
            text-transform: uppercase; letter-spacing: 0.08em; width: 22%;
        }
        .meta-value { font-size: 12px; font-weight: bold; color: #0d1f3c; width: 28%; }

        .badge-paid {
            display: inline-block; background: #dcfce7; color: #166534;
            border: 1px solid #86efac; padding: 2px 12px; border-radius: 20px;
            font-size: 9.5px; font-weight: bold; letter-spacing: 0.5px;
        }

        /* ── GOLD ACCENT LINE ── */
        .accent-line { height: 1px; background: #e8d5a3; margin: 0 44px; }

        /* ── PAYMENT DETAILS ── */
        .payment-section { padding: 20px 44px 0; }
        .section-heading {
            font-size: 9px; font-weight: bold; text-transform: uppercase;
            letter-spacing: 0.1em; color: #d69e2e; margin-bottom: 14px;
        }

        .detail-table { width: 100%; border-collapse: collapse; }
        .detail-table td { padding: 9px 0; vertical-align: top; }
        .detail-label { font-size: 11.5px; color: #6b7280; width: 50%; }
        .detail-value { font-size: 11.5px; font-weight: bold; color: #0d1f3c; text-align: right; }

        /* ── TOTAL ── */
        .total-wrap { padding: 20px 44px; }
        .total-box {
            background: #0d1f3c; border-radius: 6px; padding: 14px 20px;
        }
        .total-table { width: 100%; border-collapse: collapse; }
        .total-label { font-size: 13px; font-weight: bold; color: #ffffff; }
        .total-amount { font-size: 17px; font-weight: bold; color: #d69e2e; text-align: right; }

        /* ── NOTE ── */
        .note {
            font-size: 10px; color: #9ca3af; font-style: italic;
            padding: 0 44px; margin-bottom: 24px;
        }

        /* ── FOOTER ── */
        .footer { background: #0d1f3c; padding: 10px 44px; text-align: center; }
        .footer-text {
            font-size: 8.5px; color: rgba(214,158,46,0.7); letter-spacing: 0.8px;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ── HEADER ── --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="vertical-align:middle;">
                    <div class="brand-name">E<span class="brand-dash">-</span>Services Platform</div>
                    <div class="brand-sub">Lebanese Republic &nbsp;&bull;&nbsp; Digital Government Services</div>
                </td>
                <td style="vertical-align:middle;text-align:right;">
                    <div class="receipt-badge">Official Document</div>
                    <div class="receipt-title">Payment Receipt</div>
                    <div class="receipt-meta">Receipt #{{ $cr->id }} &nbsp;&bull;&nbsp; {{ now()->format('d M Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ── GOLD STRIPE ── --}}
    <div class="gold-stripe"></div>

    {{-- ── META INFO ── --}}
    <div class="meta-section">
        <table class="meta-table">
            <tr>
                <td class="meta-label">Citizen</td>
                <td class="meta-value">{{ $cr->full_name ?? ($cr->user->first_name . ' ' . $cr->user->last_name) }}</td>
                <td class="meta-label">Request ID</td>
                <td class="meta-value">#{{ $cr->id }}</td>
            </tr>
            <tr>
                <td class="meta-label">Email</td>
                <td class="meta-value" style="font-size:10.5px;">{{ $cr->email ?? $cr->user->email }}</td>
                <td class="meta-label">Submitted</td>
                <td class="meta-value" style="font-size:10.5px;">{{ $cr->created_at->format('d M Y H:i') }}</td>
            </tr>
            <tr>
                <td class="meta-label">Service</td>
                <td class="meta-value">{{ $cr->service->name ?? '—' }}</td>
                <td class="meta-label">Status</td>
                <td class="meta-value"><span class="badge-paid">PAID</span></td>
            </tr>
            <tr>
                <td class="meta-label">Office</td>
                <td class="meta-value" style="font-size:10.5px;">{{ $cr->office->name ?? '—' }}</td>
                <td></td><td></td>
            </tr>
        </table>
    </div>

    <div class="accent-line"></div>

    {{-- ── PAYMENT DETAILS ── --}}
    <div class="payment-section">
        <div class="section-heading">Payment Details</div>
        @php
            $local  = $cr->localPayments->where('status', 'confirmed')->first();
            $crypto = $cr->cryptoTransactions->where('status', 'confirmed')->first();
        @endphp
        <table class="detail-table">
            @if($local)
            <tr>
                <td class="detail-label">Payment Method</td>
                <td class="detail-value">{{ strtoupper($local->method) }} (Local Transfer)</td>
            </tr>
            <tr>
                <td class="detail-label">Reference Number</td>
                <td class="detail-value">{{ $local->reference_number }}</td>
            </tr>
            <tr>
                <td class="detail-label">Amount Paid</td>
                <td class="detail-value">${{ number_format($local->amount_usd, 2) }} USD</td>
            </tr>
            @if($local->confirmed_at)
            <tr>
                <td class="detail-label">Confirmed At</td>
                <td class="detail-value">{{ \Carbon\Carbon::parse($local->confirmed_at)->format('d M Y H:i') }}</td>
            </tr>
            @endif

            @elseif($crypto)
            <tr>
                <td class="detail-label">Payment Method</td>
                <td class="detail-value">Cryptocurrency ({{ $crypto->currency }})</td>
            </tr>
            <tr>
                <td class="detail-label">Transaction Hash</td>
                <td class="detail-value" style="font-size:9px;word-break:break-all;">{{ $crypto->tx_hash }}</td>
            </tr>
            <tr>
                <td class="detail-label">Amount</td>
                <td class="detail-value">{{ $crypto->amount_crypto }} {{ $crypto->currency }}</td>
            </tr>
            @if($crypto->amount_usd)
            <tr>
                <td class="detail-label">USD Equivalent</td>
                <td class="detail-value">${{ number_format($crypto->amount_usd, 2) }}</td>
            </tr>
            @endif

            @else
            <tr>
                <td class="detail-label">Payment Method</td>
                <td class="detail-value">Card (Stripe)</td>
            </tr>
            @endif

            <tr>
                <td class="detail-label">Service Fee</td>
                <td class="detail-value">
                    @if($cr->service && $cr->service->price > 0)
                        {{ $cr->service->currency ?? 'USD' }} {{ number_format($cr->service->price, 2) }}
                    @else
                        Free
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- ── TOTAL ── --}}
    <div class="total-wrap">
        <div class="total-box">
            <table class="total-table">
                <tr>
                    <td class="total-label">Total Paid</td>
                    <td class="total-amount">
                        @if($local)
                            ${{ number_format($local->amount_usd, 2) }} USD
                        @elseif($crypto && $crypto->amount_usd)
                            ${{ number_format($crypto->amount_usd, 2) }} USD
                        @elseif($cr->service && $cr->service->price > 0)
                            {{ $cr->service->currency ?? 'USD' }} {{ number_format($cr->service->price, 2) }}
                        @else
                            Free
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ── NOTE ── --}}
    <div class="note">
        This receipt is auto-generated and does not require a signature. Keep this document for your records.
    </div>

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <div class="footer-text">
            E-Services Government Platform &nbsp;&bull;&nbsp; Generated {{ now()->format('d M Y \a\t H:i') }} UTC
            &nbsp;&nbsp;|&nbsp;&nbsp;
            e-services-platform-production.up.railway.app
        </div>