@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Main Dashboard')

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:#fff;margin:0;">
            {{ app()->getLocale() === 'ar' ? 'لوحة التحكم الرئيسية' : 'Main Dashboard' }}
        </h1>
        <p style="color:var(--muted);font-size:0.875rem;margin-top:4px;">
            {{ app()->getLocale() === 'ar' ? 'نظرة عامة على النظام' : 'System overview' }} — {{ now()->format('F d, Y') }}
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.offices.create') }}" class="btn-gold">
            <i class="bi bi-plus-lg"></i> {{ app()->getLocale() === 'ar' ? 'إنشاء دائرة' : 'Create Office' }}
        </a>
        <a href="{{ route('requests.index') }}" class="btn-ghost">
            <i class="bi bi-folder-check"></i> {{ __('app.nav_review_requests') }}
        </a>
    </div>
</div>

{{-- KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="admin-card d-flex align-items-center gap-3">
            <div style="width:48px;height:48px;background:rgba(37,99,235,0.15);border:1px solid rgba(37,99,235,0.25);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-people-fill" style="color:#93c5fd;font-size:1.2rem;"></i>
            </div>
            <div>
                <div style="font-size:1.6rem;font-weight:700;font-family:'Syne',sans-serif;color:#fff;">{{ number_format($totalUsers ?? 0) }}</div>
                <div style="font-size:0.78rem;color:var(--muted);">Total Users</div>
                <div style="font-size:0.72rem;color:#6ee7b7;margin-top:2px;">+{{ $newUsersThisMonth ?? 0 }} this month</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="admin-card d-flex align-items-center gap-3">
            <div style="width:48px;height:48px;background:rgba(4,120,87,0.15);border:1px solid rgba(4,120,87,0.3);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-building-fill" style="color:#6ee7b7;font-size:1.2rem;"></i>
            </div>
            <div>
                <div style="font-size:1.6rem;font-weight:700;font-family:'Syne',sans-serif;color:#fff;">{{ number_format($totalOffices ?? 0) }}</div>
                <div style="font-size:0.78rem;color:var(--muted);">Total Offices</div>
                <div style="font-size:0.72rem;color:#6ee7b7;margin-top:2px;">{{ $activeOffices ?? 0 }} active</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="admin-card d-flex align-items-center gap-3">
            <div style="width:48px;height:48px;background:rgba(214,158,46,0.15);border:1px solid rgba(214,158,46,0.25);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-currency-dollar" style="color:var(--gold);font-size:1.2rem;"></i>
            </div>
            <div>
                <div style="font-size:1.6rem;font-weight:700;font-family:'Syne',sans-serif;color:#fff;">${{ number_format($totalRevenue ?? 0, 0) }}</div>
                <div style="font-size:0.78rem;color:var(--muted);">Total Revenue</div>
                <div style="font-size:0.72rem;color:#6ee7b7;margin-top:2px;">${{ number_format($revenueThisMonth ?? 0, 0) }} this month</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="admin-card" style="padding:1.25rem 1.5rem;">
            <div style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.07em;margin-bottom:0.75rem;">Users by Role</div>
            @foreach(['admin'=>'#f87171','office'=>'var(--gold)','citizen'=>'#93c5fd'] as $role => $color)
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span style="font-size:0.8rem;color:var(--muted);text-transform:capitalize;">{{ $role }}</span>
                <span style="font-size:0.875rem;font-weight:600;color:{{ $color }};">{{ $usersByRole[$role] ?? 0 }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Staff Management Table --}}
<div class="admin-card mb-4" style="padding:0;overflow:hidden;">
    <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <h5 style="font-family:'Syne',sans-serif;font-weight:700;color:#fff;margin:0;">Government Office Staff</h5>
            <p style="color:var(--muted);font-size:0.8rem;margin:0;">Manage office clerks and system administrators</p>
        </div>
        <a href="{{ route('admin.staff.create') }}" class="btn-gold">
            <i class="bi bi-person-plus-fill"></i> Add New Staff
        </a>
    </div>
    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($staffMembers) && count($staffMembers) > 0)
                    @foreach($staffMembers as $staff)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div style="width:34px;height:34px;border-radius:50%;background:rgba(214,158,46,0.15);border:1px solid rgba(214,158,46,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:700;color:var(--gold);font-size:0.8rem;">
                                    {{ strtoupper(substr($staff->first_name ?? $staff->name ?? 'S', 0, 1)) }}
                                </div>
                                <span style="font-weight:600;color:#fff;">{{ $staff->first_name ?? $staff->name }} {{ $staff->last_name ?? '' }}</span>
                            </div>
                        </td>
                        <td style="color:var(--muted);font-size:0.85rem;">{{ $staff->email }}</td>
                        <td>
                            @if($staff->role === 'admin')
                                <span style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.25);color:#f87171;font-size:0.72rem;padding:0.2rem 0.65rem;border-radius:20px;font-weight:600;">Admin</span>
                            @else
                                <span style="background:rgba(214,158,46,0.12);border:1px solid rgba(214,158,46,0.2);color:var(--gold);font-size:0.72rem;padding:0.2rem 0.65rem;border-radius:20px;font-weight:600;">Office</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('admin.staff.edit', $staff->id) }}" class="btn-edit-soft">
                                    <i class="bi bi-pencil-fill"></i> Edit
                                </a>
                                <form action="{{ route('admin.staff.destroy', $staff->id) }}" method="POST"
                                      onsubmit="return confirm('Delete {{ $staff->first_name }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger-soft">
                                        <i class="bi bi-trash3-fill"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" style="text-align:center;padding:3rem;color:var(--muted);">
                            <i class="bi bi-people" style="font-size:2rem;display:block;margin-bottom:0.5rem;opacity:0.4;"></i>
                            No staff members found.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

{{-- Charts Row --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="admin-card">
            <div style="margin-bottom:1.25rem;">
                <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:1rem;color:#fff;">Users per Municipality</div>
                <div style="font-size:0.78rem;color:var(--muted);">Registered citizen distribution by location</div>
            </div>
            @if(isset($chartLabels) && count($chartLabels) > 0)
                <canvas id="municipalityChart" style="max-height:280px;"></canvas>
            @else
                <div style="text-align:center;padding:3rem;color:var(--muted);">
                    <i class="bi bi-bar-chart" style="font-size:2.5rem;opacity:0.3;display:block;margin-bottom:0.75rem;"></i>
                    No municipality data yet.
                </div>
            @endif
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-card" style="height:100%;">
            <div style="margin-bottom:1.25rem;">
                <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:1rem;color:#fff;">Recent Registrations</div>
                <div style="font-size:0.78rem;color:var(--muted);">Last 5 users to join</div>
            </div>
            @if(isset($recentUsers) && count($recentUsers) > 0)
                @foreach($recentUsers as $user)
                <div style="display:flex;align-items:center;gap:0.75rem;padding:0.6rem 0;{{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}">
                    <div style="width:36px;height:36px;border-radius:50%;background:rgba(37,99,235,0.2);border:1px solid rgba(37,99,235,0.25);display:flex;align-items:center;justify-content:center;font-weight:700;color:#93c5fd;font-size:13px;flex-shrink:0;">
                        {{ strtoupper(substr($user->first_name, 0, 1)) }}
                    </div>
                    <div style="flex:1;overflow:hidden;">
                        <div style="font-size:0.85rem;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $user->first_name }} {{ $user->last_name }}
                        </div>
                        <div style="font-size:0.75rem;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $user->email }}</div>
                    </div>
                    <span style="font-size:0.65rem;padding:0.15rem 0.5rem;border-radius:20px;font-weight:600;flex-shrink:0;
                        {{ $user->role === 'admin' ? 'background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.25);' : ($user->role === 'office' ? 'background:rgba(214,158,46,0.15);color:var(--gold);border:1px solid rgba(214,158,46,0.25);' : 'background:rgba(37,99,235,0.15);color:#93c5fd;border:1px solid rgba(37,99,235,0.25);') }}">
                        {{ $user->role }}
                    </span>
                </div>
                @endforeach
            @else
                <p style="color:var(--muted);text-align:center;padding:2rem 0;">No users yet.</p>
            @endif
        </div>
    </div>
</div>

{{-- Request Status Row --}}
<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="admin-card">
            <div style="margin-bottom:1.25rem;">
                <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:1rem;color:#fff;">Requests by Status</div>
                <div style="font-size:0.78rem;color:var(--muted);">Total: {{ $totalRequests ?? 0 }} citizen requests</div>
            </div>
            @if(($totalRequests ?? 0) > 0)
                <canvas id="requestsPieChart" style="max-height:220px;"></canvas>
            @else
                <div style="text-align:center;padding:2rem;color:var(--muted);">
                    <i class="bi bi-pie-chart" style="font-size:2rem;opacity:0.3;display:block;margin-bottom:0.5rem;"></i>
                    No citizen requests yet.
                </div>
            @endif
        </div>
    </div>
    <div class="col-lg-7">
        <div class="admin-card" style="height:100%;">
            <div style="margin-bottom:1.25rem;">
                <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:1rem;color:#fff;">Request Status Breakdown</div>
                <div style="font-size:0.78rem;color:var(--muted);">Current distribution of all citizen requests</div>
            </div>
            @php $totalRequests = $totalRequests ?? 0; @endphp
            @foreach([
                ['Pending',   $requestStats['pending']   ?? 0, 'rgba(245,158,11,0.3)',  '#fcd34d'],
                ['In Review', $requestStats['in_review'] ?? 0, 'rgba(37,99,235,0.3)',   '#93c5fd'],
                ['Approved',  $requestStats['approved']  ?? 0, 'rgba(4,120,87,0.3)',    '#6ee7b7'],
                ['Rejected',  $requestStats['rejected']  ?? 0, 'rgba(239,68,68,0.2)',   '#f87171'],
            ] as [$name, $count, $barColor, $textColor])
                @php $pct = $totalRequests > 0 ? round(($count / $totalRequests) * 100) : 0; @endphp
                <div style="margin-bottom:1.1rem;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:0.35rem;">
                        <span style="font-size:0.82rem;color:var(--text);">{{ $name }}</span>
                        <span style="font-size:0.82rem;color:{{ $textColor }};">{{ $count }} ({{ $pct }}%)</span>
                    </div>
                    <div style="height:6px;background:rgba(255,255,255,0.06);border-radius:4px;overflow:hidden;">
                        <div style="height:100%;width:{{ $pct }}%;background:{{ $barColor }};border-radius:4px;transition:width 0.6s ease;"></div>
                    </div>
                </div>
            @endforeach

            <div style="border-top:1px solid var(--border);padding-top:1rem;margin-top:0.5rem;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:0.82rem;color:var(--muted);">Total Requests</span>
                <span style="font-family:'Syne',sans-serif;font-weight:700;color:#fff;">{{ $totalRequests }}</span>
            </div>
            <div style="margin-top:1rem;">
                <a href="{{ route('requests.index') }}" class="btn-ghost" style="font-size:0.8rem;padding:0.45rem 1rem;">
                    <i class="bi bi-arrow-right"></i> Manage Requests
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Monthly Requests Chart --}}
<div class="admin-card">
    <div style="margin-bottom:1.25rem;">
        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:1rem;color:#fff;">Monthly Requests — Last 6 Months</div>
        <div style="font-size:0.78rem;color:var(--muted);">Submitted citizen service requests per month</div>
    </div>
    <canvas id="monthlyChart" style="max-height:240px;"></canvas>
</div>

{{-- Hidden data for charts --}}
<div id="dashboardData"
     data-chart-labels='@json($chartLabels ?? [])'
     data-chart-data='@json($chartData ?? [])'
     data-total-requests="{{ $totalRequests ?? 0 }}"
     data-pending="{{ $requestStats['pending'] ?? 0 }}"
     data-in-review="{{ $requestStats['in_review'] ?? 0 }}"
     data-approved="{{ $requestStats['approved'] ?? 0 }}"
     data-rejected="{{ $requestStats['rejected'] ?? 0 }}"
     data-monthly-labels='@json($monthlyLabels ?? [])'
     data-monthly-data='@json($monthlyData ?? [])'
     style="display:none;"></div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const dd     = document.getElementById('dashboardData');
    const labels = JSON.parse(dd.dataset.chartLabels || '[]');
    const data   = JSON.parse(dd.dataset.chartData   || '[]');
    if (!labels.length) return;

    new Chart(document.getElementById('municipalityChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Registered Users',
                data: data,
                backgroundColor: 'rgba(214,158,46,0.2)',
                borderColor: 'rgba(214,158,46,0.7)',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y} user${ctx.parsed.y !== 1 ? 's' : ''}` } }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0, color: '#94a3b8' },
                    grid: { color: 'rgba(255,255,255,0.06)' },
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8', maxRotation: 30 }
                }
            }
        }
    });
})();

(function() {
    const dd    = document.getElementById('dashboardData');
    const total = parseInt(dd.dataset.totalRequests || '0');
    if (!total) return;
    const ctx = document.getElementById('requestsPieChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'In Review', 'Approved', 'Rejected'],
            datasets: [{
                data: [
                    parseInt(dd.dataset.pending   || '0'),
                    parseInt(dd.dataset.inReview  || '0'),
                    parseInt(dd.dataset.approved  || '0'),
                    parseInt(dd.dataset.rejected  || '0'),
                ],
                backgroundColor: ['rgba(245,158,11,0.25)', 'rgba(37,99,235,0.25)', 'rgba(4,120,87,0.25)', 'rgba(239,68,68,0.2)'],
                borderColor:     ['#fcd34d', '#93c5fd', '#6ee7b7', '#f87171'],
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#94a3b8', font: { size: 12 } } },
                tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} requests` } }
            },
            cutout: '60%',
        }
    });
})();

(function() {
    const dd = document.getElementById('dashboardData');
    const labels = JSON.parse(dd.dataset.monthlyLabels || '[]');
    const data   = JSON.parse(dd.dataset.monthlyData   || '[]');
    const ctx    = document.getElementById('monthlyChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Requests',
                data: data,
                borderColor: 'rgba(214,158,46,0.9)',
                backgroundColor: 'rgba(214,158,46,0.08)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(214,158,46,1)',
                pointRadius: 4,
                fill: true,
                tension: 0.35,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y} request${ctx.parsed.y !== 1 ? 's' : ''}` } }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0, color: '#94a3b8' },
                    grid: { color: 'rgba(255,255,255,0.06)' },
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8' }
                }
            }
        }
    });
})();
</script>
@endpush
