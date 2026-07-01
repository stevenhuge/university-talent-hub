@extends('layouts.app')

@section('content')
<div class="mb-8 flex justify-between items-center" style="flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1>Administrator Dashboard</h1>
        <p class="text-muted">Platform overview and statistics.</p>
    </div>
</div>

<div class="grid grid-cols-1 md-grid-cols-4 gap-6 mb-8" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
    <a href="{{ route('admin.verification') }}" class="card text-center hover-shadow" style="text-decoration: none; border: 1px solid var(--warning); background: rgba(245, 158, 11, 0.05);">
        <h2 style="color: var(--warning);">{{ $stats['pending'] }}</h2>
        <p style="color: var(--warning); font-weight: 500;">Pending Verifications</p>
    </a>
    <a href="{{ route('admin.verification', ['filter' => 'verified_skill']) }}" class="card text-center hover-shadow" style="text-decoration: none;">
        <h2 style="color: var(--secondary);">{{ $stats['verified_skill'] }}</h2>
        <p class="text-muted">Verified Skills</p>
    </a>
    <a href="{{ route('admin.verification', ['filter' => 'verified_portfolio']) }}" class="card text-center hover-shadow" style="text-decoration: none;">
        <h2 style="color: var(--primary-hover);">{{ $stats['verified_portfolio'] }}</h2>
        <p class="text-muted">Verified Portfolios</p>
    </a>
    <a href="{{ route('admin.verification', ['filter' => 'rejected']) }}" class="card text-center hover-shadow" style="text-decoration: none; border: 1px solid var(--danger); background: rgba(239, 68, 68, 0.05);">
        <h2 style="color: var(--danger);">{{ $stats['rejected'] }}</h2>
        <p style="color: var(--danger); font-weight: 500;">Rejected Verifications</p>
    </a>
</div>

<style>
    .hover-shadow { transition: transform 0.2s, box-shadow 0.2s; }
    .hover-shadow:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
</style>

<div class="card mb-8">
    <h3 class="mb-4">Quick Actions</h3>
    <div class="flex gap-4" style="flex-wrap: wrap;">
        <a href="{{ route('admin.search') }}" class="btn btn-primary">Search Talents</a>
        <a href="{{ route('admin.verification') }}" class="btn btn-warning flex items-center gap-2">
            Verify Submissions 
            @if($stats['pending'] > 0)
                <span style="background: white; color: var(--warning); padding: 0.1rem 0.5rem; border-radius: var(--radius-full); font-size: 0.75rem;">{{ $stats['pending'] }}</span>
            @endif
        </a>
        <a href="{{ route('admin.rewards') }}" class="btn btn-outline">Manage Rewards & Opportunities</a>
    </div>
</div>

<div class="card">
    <h3 class="mb-4">Recent Reward Claims</h3>
    @if($recentClaims->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border); text-align: left;">
                        <th style="padding: 1rem;">Student</th>
                        <th style="padding: 1rem;">Reward</th>
                        <th style="padding: 1rem;">Status</th>
                        <th style="padding: 1rem;">Claimed At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentClaims as $claim)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 1rem; font-weight: 500;">{{ $claim->user->name }}</td>
                        <td style="padding: 1rem;">{{ $claim->reward->name }}</td>
                        <td style="padding: 1rem;">
                            <span class="badge badge-success">{{ ucfirst($claim->status) }}</span>
                        </td>
                        <td style="padding: 1rem; color: var(--text-muted); font-size: 0.875rem;">{{ $claim->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-muted text-center py-4">No recent claims.</p>
    @endif
</div>
@endsection
