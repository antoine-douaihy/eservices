<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4 portrait; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            background: #fff;
            color: #0d1f3c;
            width: 210mm;
            height: 297mm;
        }

        .page {
            width: 210mm;
            height: 297mm;
            padding: 11mm;
        }

        /* ── OUTER FRAME ── */
        .outer-frame {
            border: 3.5px solid #0d1f3c;
            height: 100%;
            width: 100%;
        }
        .gold-frame {
            border: 1.5px solid #d69e2e;
            height: 100%;
            margin: 4px;
            display: table;
            width: calc(100% - 8px);
        }

        /* ── HEADER BANNER ── */
        .header-banner {
            background: #0d1f3c;
            padding: 26px 40px 22px;
            text-align: center;
        }
        .header-eyebrow {
            font-size: 8px; letter-spacing: 6px; text-transform: uppercase;
            color: #d69e2e; margin-bottom: 6px;
        }
        .header-title {
            font-size: 27px; font-weight: bold; color: #ffffff; letter-spacing: 2px;
        }
        .header-sub {
            font-size: 8px; color: rgba(255,255,255,0.4); letter-spacing: 4px;
            text-transform: uppercase; margin-top: 5px;
        }

        /* ── GOLD STRIPE ── */
        .gold-stripe { background: #d69e2e; height: 5px; }

        /* ── TITLE BAND ── */
        .title-band {
            background: #f7f3ea;
            text-align: center;
            padding: 20px 0 17px;
        }
        .cert-title {
            font-size: 19px; font-weight: bold; color: #0d1f3c;
            letter-spacing: 8px; text-transform: uppercase;
        }
        .cert-subtitle {
            font-size: 8.5px; color: #d69e2e; letter-spacing: 3px;
            text-transform: uppercase; margin-top: 5px;
        }

        /* ── BODY ── */
        .body-section {
            padding: 32px 52px 28px;
            text-align: center;
        }
        .certifies-text {
            font-size: 10px; color: #999; letter-spacing: 3px;
            text-transform: uppercase; margin-bottom: 14px;
        }
        .citizen-name {
            font-size: 30px; font-weight: bold; color: #0d1f3c;
            letter-spacing: 1px; padding: 5px 30px 11px;
            border-bottom: 3px solid #d69e2e; display: inline-block;
        }
        .completed-text {
            font-size: 11.5px; color: #555; margin: 20px 0 12px; line-height: 1.8;
        }
        .service-badge {
            font-size: 15px; font-weight: bold; color: #047857;
            background: rgba(4,120,87,0.08); border: 1.5px solid rgba(4,120,87,0.3);
            border-radius: 5px; display: inline-block; padding: 7px 28px; margin: 4px 0 14px;
        }
        .office-ref { font-size: 10.5px; color: #aaa; }

        /* ── DETAILS TABLE ── */
        .details-wrap { padding: 24px 48px 20px; }
        .details { width: 100%; border-collapse: collapse; font-size: 11.5px; }
        .details td {
            padding: 11px 14px;
            color: #374151;
        }
        .details tr:nth-child(even) td { background: #f9fafb; }
        .details td.lbl {
            font-weight: bold; color: #0d1f3c; width: 38%;
            border-right: 2px solid #d69e2e;
        }

        /* ── SIGNATURES ── */
        .footer-section { padding: 24px 48px 28px; }
        .sig-table { width: 100%; border-collapse: collapse; }
        .sig-table td { width: 33%; text-align: center; vertical-align: bottom; padding: 0 8px; }
        .sig-line {
            border-top: 1px solid #0d1f3c; margin: 0 auto;
            width: 120px; margin-top: 50px;
        }
        .sig-label { font-size: 9.5px; color: #999; margin-top: 6px; }
        .sig-name { font-size: 10.5px; font-weight: bold; color: #0d1f3c; margin-top: 3px; }

        /* ── STAMP ── */
        .stamp-outer {
            width: 86px; height: 86px;
            border: 3px solid #0d1f3c; border-radius: 50%;
            display: inline-block; text-align: center; padding-top: 10px;
        }
        .stamp-inner {
            width: 72px; height: 72px;
            border: 1px dashed #d69e2e; border-radius: 50%;
            display: inline-block; padding-top: 12px;
        }
        .stamp-text {
            font-size: 7.5px; font-weight: bold; color: #0d1f3c;
            letter-spacing: 0.8px; text-transform: uppercase; line-height: 1.8;
        }

        /* ── FOOTER BAR ── */
        .footer-bar {
            background: #0d1f3c; padding: 10px 20px; text-align: center;
        }
        .cert-hash {
            font-size: 8px; color: rgba(214,158,46,0.7); letter-spacing: 0.8px;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="outer-frame">
        <div class="gold-frame">

            {{-- ── HEADER ── --}}
            <div class="header-banner">
                <div class="header-eyebrow">Lebanese Republic</div>
                <div class="header-title">E-Services Platform</div>
                <div class="header-sub">Digital Government Services</div>
            </div>

            {{-- ── GOLD STRIPE ── --}}
            <div class="gold-stripe"></div>

            {{-- ── CERT TITLE ── --}}
            <div class="title-band">
                <div class="cert-title">Official Certificate</div>
                <div class="cert-subtitle">Certificate of Service Completion</div>
            </div>

            {{-- ── BODY ── --}}
            <div class="body-section">
                <div class="certifies-text">This is to certify that</div>
                <div class="citizen-name">
                    {{ $request->full_name ?? ($request->user->first_name . ' ' . $request->user->last_name) }}
                </div>
                <div class="completed-text">
                    has successfully submitted and received approval for the government service of
                </div>
                <div class="service-badge">{{ $request->service->name }}</div>
                <div class="office-ref">
                    through the E-Services Platform &mdash; {{ $request->office->name }}
                </div>
            </div>

            {{-- ── DETAILS ── --}}
            <div class="details-wrap">
                <table class="details">
                    <tr>
                        <td class="lbl">Certificate Number</td>
                        <td>#CERT-{{ str_pad($request->id, 6, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Citizen Name</td>
                        <td>{{ $request->full_name ?? ($request->user->first_name . ' ' . $request->user->last_name) }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Citizen Email</td>
                        <td>{{ $request->email ?? $request->user->email }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Service</td>
                        <td>{{ $request->service->name }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Issuing Office</td>
                        <td>{{ $request->office->name }}{{ $request->office->city ? ', '.$request->office->city : '' }}</td>
                    </tr>
                    @if($request->service->price > 0)
                    <tr>
                        <td class="lbl">Amount Paid</td>
                        <td>{{ number_format($request->service->price, 2) }} {{ $request->service->currency ?? 'USD' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="lbl">Date Approved</td>
                        <td>{{ $request->approved_at->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Issued On</td>
                        <td>{{ now()->format('d F Y \a\t H:i') }} UTC</td>
                    </tr>
                </table>
            </div>

            {{-- ── SIGNATURES ── --}}
            <div class="footer-section">
                <table class="sig-table">
                    <tr>
                        <td>
                            <div class="sig-line"></div>
                            <div class="sig-label">Authorized Officer</div>
                            <div class="sig-name">{{ $request->office->name }}</div>
                        </td>
                        <td style="text-align:center;">
                            <div class="stamp-outer">
                                <div class="stamp-inner">
                                    <div class="stamp-text">E-Services<br>Platform<br>OFFICIAL</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="sig-line"></div>
                            <div class="sig-label">Platform Administrator</div>
                            <div class="sig-name">E-Services Platform</div>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- ── FOOTER BAR ── --}}
            <div class="footer-bar">
                <div class="cert-hash">
                    VERIFICATION CODE: CERT-{{ strtoupper(substr(md5($request->id . $request->approved_at), 0, 16)) }}
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    e-services-platform-production.up.railway.app
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>
