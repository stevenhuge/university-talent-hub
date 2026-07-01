@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1>Verification Center</h1>
    <p class="text-muted">Review student submissions and award points accordingly.</p>
</div>

<div class="card">
    @if($pending->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            @foreach($pending as $sub)
                <div style="border: 1px solid var(--border); padding: 1.5rem; border-radius: var(--radius-md);">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h3>{{ $sub->title }}</h3>
                                <span class="badge badge-warning">{{ ucfirst($sub->type) }}</span>
                            </div>
                            <p class="text-sm text-muted">Submitted by <strong>{{ $sub->user->name }}</strong> on {{ $sub->created_at->format('M d, Y') }}</p>
                        </div>
                        
                        @if($sub->proof_file_path)
                            <a href="{{ Storage::url($sub->proof_file_path) }}" target="_blank" class="btn btn-outline btn-sm">View Proof</a>
                        @else
                            <span class="text-sm text-muted italic">No proof file attached</span>
                        @endif
                    </div>
                    
                    @if($sub->description)
                        <div style="background: var(--background); padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem;">
                            <p class="text-sm">{{ $sub->description }}</p>
                        </div>
                    @endif

                    <div style="border-top: 1px solid var(--border); padding-top: 1rem;" class="flex items-center gap-4">
                        <form method="POST" action="{{ route('admin.verify', $sub->id) }}" class="flex items-center gap-4 w-full">
                            @csrf
                            <input type="hidden" name="action" value="approve">
                            
                            <div class="form-group mb-0" style="flex: 1;">
                                <label for="points_{{ $sub->id }}" class="form-label text-sm">Award Points</label>
                                <input type="number" id="points_{{ $sub->id }}" name="points" class="form-control" value="10" min="1" required style="max-width: 150px;">
                            </div>
                            
                            <button type="submit" class="btn btn-primary" style="background-color: var(--secondary); border-color: var(--secondary);">Approve & Award Points</button>
                        </form>
                        
                        <form method="POST" action="{{ route('admin.verify', $sub->id) }}">
                            @csrf
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-danger">Reject</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--secondary); margin: 0 auto 1rem auto;"><path d="M22 11.08V12a10 10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <h3>All Caught Up!</h3>
            <p class="text-muted mt-2">There are no pending verifications at the moment.</p>
        </div>
        </div>
    @endif
</div>

<div class="card mt-8">
    <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 1rem;">
        <h2 style="font-size: 1.25rem; font-weight: 600;">Verification History</h2>
        
        <div class="flex gap-2">
            <a href="{{ route('admin.verification') }}" class="btn btn-outline btn-sm {{ !request('filter') ? 'btn-primary' : '' }}" style="{{ !request('filter') ? 'color: white;' : '' }}">All</a>
            <a href="{{ route('admin.verification', ['filter' => 'verified_skill']) }}" class="btn btn-outline btn-sm {{ request('filter') === 'verified_skill' ? 'btn-primary' : '' }}" style="{{ request('filter') === 'verified_skill' ? 'color: white;' : '' }}">Skills</a>
            <a href="{{ route('admin.verification', ['filter' => 'verified_portfolio']) }}" class="btn btn-outline btn-sm {{ request('filter') === 'verified_portfolio' ? 'btn-primary' : '' }}" style="{{ request('filter') === 'verified_portfolio' ? 'color: white;' : '' }}">Portfolios</a>
            <a href="{{ route('admin.verification', ['filter' => 'rejected']) }}" class="btn btn-outline btn-sm {{ request('filter') === 'rejected' ? 'btn-danger' : '' }}" style="{{ request('filter') === 'rejected' ? 'color: white;' : '' }}">Rejected</a>
        </div>
    </div>
    
    @if(isset($history) && $history->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border);">
                        <th style="padding: 1rem;">Date</th>
                        <th style="padding: 1rem;">Student</th>
                        <th style="padding: 1rem;">Title</th>
                        <th style="padding: 1rem;">Type</th>
                        <th style="padding: 1rem;">Status</th>
                        <th style="padding: 1rem;">Points Awarded</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $item)
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 1rem; color: var(--text-muted); font-size: 0.875rem;">{{ $item->updated_at->format('M d, Y') }}</td>
                            <td style="padding: 1rem; font-weight: 500;">{{ $item->user->name }}</td>
                            <td style="padding: 1rem;">{{ $item->title }}</td>
                            <td style="padding: 1rem;"><span class="badge badge-warning">{{ ucfirst($item->type) }}</span></td>
                            <td style="padding: 1rem;">
                                @if($item->status === 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @else
                                    <span class="badge badge-secondary" style="background: var(--danger); color: white;">Rejected</span>
                                @endif
                            </td>
                            <td style="padding: 1rem;">
                                {{ $item->status === 'approved' ? '+'.$item->points_awarded : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-muted text-center py-4">No history records found for this filter.</p>
    @endif
</div>
@endsection
