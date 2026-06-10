<?php

namespace App\Http\Controllers\PhysicalPossession;

use App\Http\Controllers\Controller;
use App\Models\ApplicationStatusLog;
use App\Models\OfficerApplicationAction;
use App\Models\PhysicalPossessionApplication;
use App\Models\PhysicalPossessionDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PpOfficerController extends Controller
{
    // Officer dashboard with stats
    public function dashboard()
    {
        $officer = Auth::user();
        $query = $this->districtApplicationsQuery($officer);

        $stats = [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
            'today' => (clone $query)->whereDate('created_at', today())->count(),
        ];

        // Chart ke liye last 7 days data
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('d M');
            $chartData[] = (clone $query)->whereDate('created_at', $date)->count();
        }

        $recentApplications = (clone $query)->latest()->take(6)->get();
        $pendingApplications = (clone $query)->where('status', 'pending')->latest()->take(4)->get();
        $userCount = (clone $query)->distinct()->count('user_id');
        $approvalRate = $stats['total'] > 0
            ? (int) round(($stats['approved'] / $stats['total']) * 100)
            : 0;
        $weekTotal = array_sum($chartData);

        return view('physical-possession.officer.dashboard', compact(
            'officer',
            'stats',
            'chartLabels',
            'chartData',
            'recentApplications',
            'pendingApplications',
            'userCount',
            'approvalRate',
            'weekTotal',
        ));
    }

    // Saari applications - sirf apne district ki
    public function applications(Request $request)
    {
        $officer = Auth::user();
        $status = $request->get('status', 'all');

        $query = $this->districtApplicationsQuery($officer);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $applications = $query->latest()->get();

        return view('physical-possession.officer.applications.index', compact('applications', 'status'));
    }

    // Approved applications
    public function approvedApplications()
    {
        return $this->applicationsByStatus('approved');
    }

    // Rejected applications
    public function rejectedApplications()
    {
        return $this->applicationsByStatus('rejected');
    }

    private function applicationsByStatus(string $status)
    {
        $officer = Auth::user();
        $applications = $this->districtApplicationsQuery($officer)
            ->where('status', $status)
            ->latest()
            ->get();

        return view('physical-possession.officer.applications.index', [
            'applications' => $applications,
            'status' => $status,
        ]);
    }

    // Application detail dekhna
    public function showApplication(PhysicalPossessionApplication $application)
    {
        $officer = Auth::user();
        $application = $this->findOfficerApplication($officer, $application);
        $application->load(['documents', 'statusLogs', 'user', 'officerAction.officer']);

        return view('physical-possession.officer.applications.show', compact('application', 'officer'));
    }

    // Document download
    public function downloadDocument(PhysicalPossessionApplication $application, PhysicalPossessionDocument $document)
    {
        $officer = Auth::user();
        $application = $this->findOfficerApplication($officer, $application);

        if ($document->application_id !== $application->id) {
            abort(404);
        }

        if (! Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File not found.');
        }

        return Storage::disk('public')->download($document->file_path, $document->original_name);
    }

    // Approve ya reject — single form (radio + remarks)
    public function decide(Request $request, PhysicalPossessionApplication $application)
    {
        $request->validate([
            'decision' => 'required|in:approved,rejected',
            'remarks' => 'required|string|max:1000',
        ], [
            'decision.required' => 'Please select Approve or Reject.',
            'decision.in' => 'Invalid decision selected.',
            'remarks.required' => 'Remarks are required.',
        ]);

        return $this->updateStatus($application, $request->decision, $request->remarks);
    }

    // Approve karna (legacy route)
    public function approve(Request $request, PhysicalPossessionApplication $application)
    {
        $request->validate([
            'remarks' => 'required|string|max:1000',
        ], [
            'remarks.required' => 'Remarks are required.',
        ]);

        return $this->updateStatus($application, 'approved', $request->remarks);
    }

    // Reject karna (legacy route)
    public function reject(Request $request, PhysicalPossessionApplication $application)
    {
        $request->validate([
            'remarks' => 'required|string|max:1000',
        ], [
            'remarks.required' => 'Remarks are required.',
        ]);

        return $this->updateStatus($application, 'rejected', $request->remarks);
    }

    // Status update - approve ya reject (single action per application, audit table)
    private function updateStatus(PhysicalPossessionApplication $application, string $newStatus, ?string $remarks)
    {
        $officer = Auth::user();
        $this->findOfficerApplication($officer, $application);

        try {
            DB::transaction(function () use ($officer, $application, $newStatus, $remarks) {
                $locked = PhysicalPossessionApplication::query()
                    ->where('id', $application->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($locked->status !== 'pending') {
                    throw new \RuntimeException('Only pending applications can be updated.');
                }

                if (OfficerApplicationAction::where('application_id', $locked->id)->exists()) {
                    throw new \RuntimeException('An officer action has already been recorded for this application.');
                }

                $oldStatus = $locked->status;

                $locked->update([
                    'status' => $newStatus,
                    'remarks' => $remarks,
                    'approved_by' => $officer->id,
                    'approved_at' => now(),
                ]);

                OfficerApplicationAction::create([
                    'application_id' => $locked->id,
                    'officer_id' => $officer->id,
                    'action' => $newStatus,
                    'remarks' => $remarks,
                    'previous_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'application_number' => $locked->application_number,
                    'district_id' => $locked->district_id,
                    'district_name' => $locked->district_name,
                ]);

                ApplicationStatusLog::create([
                    'application_id' => $locked->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'remarks' => $remarks,
                    'changed_by_type' => 'officer',
                    'changed_by_id' => $officer->id,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->with('error', 'An officer action has already been recorded for this application.');
            }

            throw $e;
        }

        $message = $newStatus === 'approved'
            ? 'Application has been approved successfully.'
            : 'Application has been rejected.';

        return back()->with('success', $message);
    }

    // Users list - jo users ne apply kiya apne district me
    public function users()
    {
        $officer = Auth::user();

        $userIds = $this->districtApplicationsQuery($officer)
            ->pluck('user_id')
            ->unique();

        $users = User::whereIn('id', $userIds)->get();

        return view('physical-possession.officer.users', compact('users', 'officer'));
    }

    // Reports page
    public function reports()
    {
        $officer = Auth::user();
        $query = $this->districtApplicationsQuery($officer);

        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyStats[] = [
                'label' => $month->format('M Y'),
                'total' => (clone $query)->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)->count(),
                'approved' => (clone $query)->where('status', 'approved')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)->count(),
                'rejected' => (clone $query)->where('status', 'rejected')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)->count(),
            ];
        }

        return view('physical-possession.officer.reports', compact('officer', 'monthlyStats'));
    }

    // Sirf apne district ki applications - yahi main filtering hai
    private function districtApplicationsQuery(User $officer)
    {
        $query = PhysicalPossessionApplication::query();

        if ($officer->district_id) {
            $query->where('district_id', $officer->district_id);
        } elseif ($officer->district_name) {
            $query->where('district_name', 'like', '%'.$officer->district_name.'%');
        }

        return $query;
    }

    private function findOfficerApplication(User $officer, PhysicalPossessionApplication $application): PhysicalPossessionApplication
    {
        return $this->districtApplicationsQuery($officer)
            ->where('secure_id', $application->secure_id)
            ->firstOrFail();
    }
}
