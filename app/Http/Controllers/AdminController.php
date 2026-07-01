<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\User;
use App\Models\Reward;
use App\Models\Opportunity;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'pending' => Submission::where('status', 'pending')->count(),
            'verified_portfolio' => Submission::where('type', 'portfolio')->where('status', 'approved')->count(),
            'verified_skill' => Submission::where('type', 'skill')->where('status', 'approved')->count(),
            'rejected' => Submission::where('status', 'rejected')->count(),
        ];
        $recentClaims = \App\Models\RewardClaim::with(['user', 'reward'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentClaims'));
    }

    public function search(Request $request)
    {
        $query = User::where('role', 'student');
        
        if ($request->has('q')) {
            $q = $request->q;
            $query->where(function($subQuery) use ($q) {
                $subQuery->where('name', 'like', "%{$q}%")
                         ->orWhere('major', 'like', "%{$q}%")
                         ->orWhereHas('submissions', function($sq) use ($q) {
                             $sq->where('title', 'like', "%{$q}%")
                                ->where('status', 'approved');
                         });
            });
        }
        
        $students = $query->orderBy('points', 'desc')->get();
        return view('admin.search', compact('students'));
    }

    public function verification(Request $request)
    {
        $pending = Submission::with('user')->where('status', 'pending')->latest()->get();
        
        $historyQuery = Submission::with('user')->where('status', '!=', 'pending');
        
        if ($request->has('filter')) {
            $filter = $request->filter;
            if ($filter === 'rejected') {
                $historyQuery->where('status', 'rejected');
            } elseif ($filter === 'verified_portfolio') {
                $historyQuery->where('status', 'approved')->where('type', 'portfolio');
            } elseif ($filter === 'verified_skill') {
                $historyQuery->where('status', 'approved')->where('type', 'skill');
            }
        }
        
        $history = $historyQuery->latest()->get();

        return view('admin.verification', compact('pending', 'history'));
    }

    public function verify(Request $request, Submission $submission)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'points' => 'required_if:action,approve|numeric|min:0'
        ]);

        if ($request->action === 'approve') {
            $submission->status = 'approved';
            $submission->points_awarded = $request->points;
            
            $user = $submission->user;
            $user->points += $request->points;
            $user->save();
        } else {
            $submission->status = 'rejected';
        }
        
        $submission->save();

        return back()->with('success', 'Submission verified.');
    }

    public function rewards()
    {
        $rewards = \App\Models\Reward::all();
        $opportunities = \App\Models\Opportunity::with('applications.user')->get();
        return view('admin.rewards', compact('rewards', 'opportunities'));
    }

    public function storeReward(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'points_required' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
        ]);

        \App\Models\Reward::create($request->all());
        return back();
    }

    public function updateReward(Request $request, \App\Models\Reward $reward)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'points_required' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
        ]);

        $reward->update($request->all());
        return back();
    }

    public function deleteReward(Request $request, \App\Models\Reward $reward)
    {
        $reward->delete();
        return back();
    }

    public function storeOpportunity(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'required_skills' => 'required|string',
        ]);

        \App\Models\Opportunity::create($request->all());
        return back();
    }

    public function deleteOpportunity(Request $request, \App\Models\Opportunity $opportunity)
    {
        $opportunity->delete();
        return back();
    }

    public function toggleOpportunity(Request $request, \App\Models\Opportunity $opportunity)
    {
        $opportunity->status = $opportunity->status === 'open' ? 'closed' : 'open';
        $opportunity->save();
        return back()->with('success', 'Opportunity status updated.');
    }
}
