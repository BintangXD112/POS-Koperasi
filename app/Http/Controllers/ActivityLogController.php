<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user.role')
            ->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(20);

        // Get filter options
        $actions = ActivityLog::distinct()->pluck('action')->sort();
        $users = \App\Models\User::with('role')->get(['id', 'name', 'role_id']);

        return view('admin.activity-logs.index', compact('logs', 'actions', 'users'));
    }

    /**
     * Show specific activity log
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user.role');
        
        return view('admin.activity-logs.show', compact('activityLog'));
    }

    /**
     * Export activity logs
     */
    public function export(Request $request)
    {
        $query = ActivityLog::with('user.role')
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->get();

        $filename = 'activity_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'User',
                'Role',
                'Action',
                'Description',
                'IP Address',
                'User Agent',
                'Created At'
            ]);

            // CSV data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user->name ?? 'N/A',
                    $log->user->role->display_name ?? 'N/A',
                    $log->action_display_name,
                    $log->description ?? '',
                    $log->ip_address ?? '',
                    $log->user_agent ?? '',
                    $log->formatted_time
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get activity statistics
     */
    public function statistics()
    {
        $stats = [
            'total_logs' => ActivityLog::count(),
            'login_count' => ActivityLog::where('action', 'login')->count(),
            'logout_count' => ActivityLog::where('action', 'logout')->count(),
            'profile_updates' => ActivityLog::where('action', 'profile_update')->count(),
            'password_changes' => ActivityLog::where('action', 'password_change')->count(),
            'failed_logins' => ActivityLog::where('action', 'failed_login')->count(),
            'failed_logins_unknown_user' => ActivityLog::where('action', 'failed_login')
                ->whereJsonContains('metadata->user_exists', false)
                ->count(),
            'failed_logins_wrong_password' => ActivityLog::where('action', 'failed_login')
                ->whereNotNull('user_id')
                ->count(),
        ];

        // Recent activity (last 7 days)
        $recentActivity = ActivityLog::with('user')
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'stats' => $stats,
            'recent_activity' => $recentActivity
        ]);
    }
}