<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CitizenRequest;
use App\Models\Office;
use App\Models\Revenue;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Only admin can access this controller
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        // ── KPI Cards ─────────────────────────────────────────────────────────

        // 1. Total registered users
        $totalUsers = User::count();

        // 2. Total users by role breakdown
        $usersByRole = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        // 3. New users this month
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // 4. Total offices
        $totalOffices = Office::count();
        $activeOffices = Office::where('is_active', true)->count();

        // 5. Total revenue (returns 0.00 if revenues table is empty)
        $totalRevenue = Revenue::sum('amount') ?? 0;

        // 6. Revenue this month
        $revenueThisMonth = Revenue::whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount') ?? 0;

        // ── Chart Data: Users per Municipality ────────────────────────────────
        $usersPerMunicipality = User::with('municipality')
            ->select('municipality_id', DB::raw('count(*) as total'))
            ->whereNotNull('municipality_id')
            ->groupBy('municipality_id')
            ->orderByDesc('total')
            ->get();

        $chartLabels = $usersPerMunicipality->map(fn($u) => $u->municipality?->name ?? 'Unknown')->toArray();
        $chartData   = $usersPerMunicipality->pluck('total')->toArray();

        // ── Recent Activity ───────────────────────────────────────────────────
        $recentUsers = User::latest()->take(5)->get();

        // ── Staff Members ─────────────────────────────────────────────────────
        $staffMembers = User::whereIn('role', ['admin', 'office'])->orderBy('role')->get();

        // ── Citizen Request Stats (for pie chart — uses the active system) ────
        $requestStats = [
            'pending'     => CitizenRequest::whereIn('status', ['pending', 'pending_payment'])->count(),
            'in_review'   => CitizenRequest::where('status', 'in_review')->count(),
            'approved'    => CitizenRequest::where('status', 'approved')->count(),
            'rejected'    => CitizenRequest::where('status', 'rejected')->count(),
        ];
        $totalRequests = array_sum($requestStats);

        // ── Monthly CitizenRequest trend (last 6 months) ──────────────────────
        $monthlyRaw = CitizenRequest::select(
                DB::raw('YEAR(created_at)  as yr'),
                DB::raw('MONTH(created_at) as mo'),
                DB::raw('COUNT(*)          as total')
            )
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('yr', 'mo')
            ->orderBy('yr')->orderBy('mo')
            ->get();

        // Build a complete 6-month series (fill gaps with 0)
        $monthlyLabels = [];
        $monthlyData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $dt  = now()->subMonths($i);
            $monthlyLabels[] = $dt->format('M Y');
            $row = $monthlyRaw->first(fn($r) => $r->yr == $dt->year && $r->mo == $dt->month);
            $monthlyData[] = $row ? $row->total : 0;
        }

        return view('admin.dashboard', compact(
            'totalUsers',
            'usersByRole',
            'newUsersThisMonth',
            'totalOffices',
            'activeOffices',
            'totalRevenue',
            'revenueThisMonth',
            'chartLabels',
            'chartData',
            'recentUsers',
            'staffMembers',
            'requestStats',
            'totalRequests',
            'monthlyLabels',
            'monthlyData'
        ));
    }
}
