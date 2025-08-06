<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Deposit;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StatisticsController extends Controller
{
    /**
     * Get statistics for a specific time period.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getStatistics(Request $request)
    {
        $period = $request->input('period', 'month'); // Default to month
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');

        // Determine date range based on period
        $startDate = null;
        $endDate = Carbon::now();

        switch ($period) {
            case 'week':
                $startDate = Carbon::now()->subWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->subMonth();
                break;
            case 'quarter':
                $startDate = Carbon::now()->subMonths(3);
                break;
            case 'year':
                $startDate = Carbon::now()->subYear();
                break;
            case 'custom':
                if ($customStart && $customEnd) {
                    $startDate = Carbon::parse($customStart);
                    $endDate = Carbon::parse($customEnd);
                } else {
                    return response()->json(['error' => 'Custom period requires start_date and end_date parameters'], 400);
                }
                break;
            default:
                $startDate = Carbon::now()->subMonth();
        }

        // Get user ID
        $userId = Auth::id();

        // Get total expenses
        $totalExpenses = Expense::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        // Get total deposits
        $totalDeposits = Deposit::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        // Get expenses by category
        $expensesByCategory = Expense::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category:id,name')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category ? $item->category->name : 'Uncategorized',
                    'total' => $item->total
                ];
            });

        // Get deposits by category
        $depositsByCategory = Deposit::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category:id,name')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category ? $item->category->name : 'Uncategorized',
                    'total' => $item->total
                ];
            });

        // Get expenses by day for the period
        $expensesByDay = Expense::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->select(DB::raw('DATE(date) as day'), DB::raw('SUM(amount) as total'))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Get deposits by day for the period
        $depositsByDay = Deposit::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->select(DB::raw('DATE(date) as day'), DB::raw('SUM(amount) as total'))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return response()->json([
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
                'type' => $period
            ],
            'summary' => [
                'total_expenses' => $totalExpenses,
                'total_deposits' => $totalDeposits,
                'balance' => $totalDeposits - $totalExpenses
            ],
            'expenses_by_category' => $expensesByCategory,
            'deposits_by_category' => $depositsByCategory,
            'expenses_by_day' => $expensesByDay,
            'deposits_by_day' => $depositsByDay
        ]);
    }
}
