<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with expense metrics.
     */
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // 1. Total Expenses
        $totalExpenses = Expense::sum('amount');

        // 2. Today's Expenses
        $todayExpenses = Expense::whereDate('expense_date', $today)->sum('amount');

        // 3. This Month's Expenses
        $monthExpenses = Expense::where('expense_date', '>=', $startOfMonth)->sum('amount');

        // 4. Number of Expenses
        $expenseCount = Expense::count();

        // 5. Recent 10 Expenses
        $recentExpenses = Expense::with('category')
            ->orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // 6. Monthly Expense Chart (last 12 months)
        $monthlyData = Expense::selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month, SUM(amount) as total")
            ->where('expense_date', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->get();

        $chartLabels = [];
        $chartData = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $chartLabels[] = $date->format('M Y');
            
            $monthSum = $monthlyData->firstWhere('month', $monthKey);
            $chartData[] = $monthSum ? (float) $monthSum->total : 0.0;
        }

        return view('dashboard', compact(
            'totalExpenses',
            'todayExpenses',
            'monthExpenses',
            'expenseCount',
            'recentExpenses',
            'chartLabels',
            'chartData'
        ));
    }
}
