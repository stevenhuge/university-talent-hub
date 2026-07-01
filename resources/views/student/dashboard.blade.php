@extends('layouts.app')

@section('content')
<div class="mb-8 flex justify-between items-center" style="flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1>Welcome back, {{ $user->name }}!</h1>
        <p class="text-muted">Here is an overview of your talent profile and gamification points.</p>
    </div>
    <div style="background: {{ $user->rank_color }}; color: white; padding: 0.5rem 1.5rem; border-radius: var(--radius-full); font-weight: 600; font-size: 0.875rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 0.5rem;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15l-3.5 2 1-4-3-2.5 4-.5L12 6l1.5 4 4 .5-3 2.5 1 4z"/></svg>
        {{ $user->rank }} Tier
    </div>
</div>

<div class="grid grid-cols-2 md-grid-cols-4 gap-4 mb-8" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));">
    <a href="{{ route('student.dashboard') }}" class="card text-center hover-shadow" style="text-decoration: none; border: 1px solid var(--primary); background: rgba(79, 70, 229, 0.05);">
        <h3 style="color: var(--primary);">{{ $stats['approved'] + $stats['pending'] + $stats['rejected'] }}</h3>
        <p style="color: var(--primary); font-size: 0.875rem;">All Submissions</p>
    </a>
    <a href="{{ route('student.dashboard', ['filter' => 'approved']) }}" class="card text-center hover-shadow" style="text-decoration: none; border: 1px solid var(--secondary); background: rgba(16, 185, 129, 0.05);">
        <h3 style="color: var(--secondary);">{{ $stats['approved'] }}</h3>
        <p style="color: var(--secondary); font-size: 0.875rem;">Approved</p>
    </a>
    <a href="{{ route('student.dashboard', ['filter' => 'pending']) }}" class="card text-center hover-shadow" style="text-decoration: none; border: 1px solid var(--warning); background: rgba(245, 158, 11, 0.05);">
        <h3 style="color: var(--warning);">{{ $stats['pending'] }}</h3>
        <p style="color: var(--warning); font-size: 0.875rem;">Pending</p>
    </a>
    <a href="{{ route('student.dashboard', ['filter' => 'rejected']) }}" class="card text-center hover-shadow" style="text-decoration: none; border: 1px solid var(--danger); background: rgba(239, 68, 68, 0.05);">
        <h3 style="color: var(--danger);">{{ $stats['rejected'] }}</h3>
        <p style="color: var(--danger); font-size: 0.875rem;">Rejected</p>
    </a>
</div>

<style>
    .hover-shadow { transition: transform 0.2s, box-shadow 0.2s; }
    .hover-shadow:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
</style>

<div class="grid grid-cols-2 gap-6">
    <div class="card">
        <div class="flex justify-between items-center mb-4">
            <h3 style="margin: 0;">
                @if(request('filter') === 'approved') Approved Submissions
                @elseif(request('filter') === 'pending') Pending Verifications
                @elseif(request('filter') === 'rejected') Rejected Verifications
                @else Recent Submissions
                @endif
            </h3>
            @if(request('filter'))
                <a href="{{ route('student.dashboard') }}" class="btn btn-outline btn-sm" style="padding: 0.1rem 0.5rem; font-size: 0.75rem;">Clear Filter</a>
            @endif
        </div>
        
        @if($submissions->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 1rem; max-height: 400px; overflow-y: auto; padding-right: 0.5rem;">
                @foreach($submissions as $sub)
                    <div style="border: 1px solid var(--border); padding: 1rem; border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                        <div>
                            <strong>{{ $sub->title }}</strong>
                            <p class="text-sm text-muted">{{ ucfirst($sub->type) }}</p>
                        </div>
                        <div>
                            @if($sub->status == 'approved')
                                <span class="badge badge-success">Approved (+{{ $sub->points_awarded }})</span>
                            @elseif($sub->status == 'rejected')
                                <span class="badge badge-secondary" style="background: var(--danger); color: white;">Rejected</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if(!request('filter'))
                <div class="mt-4 text-center">
                    <a href="{{ route('student.profile') }}" class="btn btn-outline btn-sm">View All & Add New</a>
                </div>
            @endif
        @else
            <p class="text-muted">No records found.</p>
            <div class="mt-4">
                <a href="{{ route('student.profile') }}" class="btn btn-primary">Add New Submission</a>
            </div>
        @endif
    </div>

    <div class="card">
        <h3 class="mb-4 flex items-center gap-2">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            AI Recommendations
        </h3>
        <p class="text-sm text-muted mb-4">Ask our AI for career advice or skills to improve:</p>
        
        <form method="POST" action="{{ route('student.ai.ask') }}" class="mb-4 flex gap-2" style="flex-wrap: wrap;">
            @csrf
            <input type="text" name="query" class="form-control text-sm" placeholder="e.g. How to be a better UI Designer?" required style="flex: 1; min-width: 200px;">
            <button type="submit" class="btn btn-primary btn-sm">Ask AI</button>
        </form>

        <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.75rem;">
            @foreach($recommendations as $rec)
                <li style="background: rgba(79, 70, 229, 0.05); padding: 1rem; border-radius: var(--radius-md); border-left: 4px solid var(--primary);">
                    {{ $rec }}
                </li>
            @endforeach
        </ul>
    </div>
    </div>
</div>

<div class="card mt-8">
    <h3 class="mb-4">Open Opportunities</h3>
    @if(isset($opportunities) && $opportunities->count() > 0)
        <div class="grid grid-cols-2 gap-6">
            @foreach($opportunities as $opp)
                <div style="border: 1px solid var(--border); padding: 1.5rem; border-radius: var(--radius-md); transition: var(--transition);" class="hover:shadow-md">
                    <div class="flex justify-between items-start mb-2">
                        <h4 style="color: var(--primary);">{{ $opp->title }}</h4>
                        <span class="badge badge-success">Open</span>
                    </div>
                    <p class="text-sm text-muted mb-4">{{ $opp->description }}</p>
                    <div class="mb-4">
                        <strong>Required Skills:</strong> <span class="text-sm">{{ $opp->required_skills }}</span>
                    </div>
                    @if(\App\Models\OpportunityApplication::where('user_id', Auth::id())->where('opportunity_id', $opp->id)->exists())
                        <button class="btn btn-secondary btn-sm" style="width: 100%; opacity: 0.7;" disabled>Applied</button>
                    @elseif($opp->status !== 'open')
                        <button class="btn btn-secondary btn-sm" style="width: 100%; opacity: 0.7;" disabled>Closed</button>
                    @else
                        <button class="btn btn-primary btn-sm" style="width: 100%;" onclick="openApplyModal({{ $opp->id }}, '{{ addslashes($opp->title) }}')">Apply Now</button>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted text-center py-4">There are no open opportunities at the moment. Keep building your skills!</p>
    @endif
</div>

<!-- Apply Modal -->
<div id="applyModalOverlay" style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 100; opacity: 0; pointer-events: none; transition: opacity 0.3s ease; display: flex; align-items: center; justify-content: center; padding: 1rem;">
    <div id="applyModalBox" class="card" style="width: 100%; max-width: 400px; background: var(--surface); transform: translateY(20px) scale(0.95); opacity: 0; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); padding: 2rem;">
        <h3 class="mb-2" id="applyModalTitle">Apply</h3>
        <p class="text-sm text-muted mb-4">Please provide your contact info and a brief pitch.</p>
        
        <form method="POST" id="applyForm" action="">
            @csrf
            <div class="form-group mb-4">
                <label class="form-label text-sm">Contact Info & Pitch</label>
                <textarea name="contact_info" class="form-control" rows="4" required placeholder="Email, phone number, and a short message why you're a good fit..."></textarea>
            </div>
            
            <div class="flex gap-2">
                <button type="button" onclick="closeApplyModal()" class="btn btn-outline" style="flex: 1;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1;">Submit Application</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const overlay = document.getElementById('applyModalOverlay');
        if (overlay) document.body.appendChild(overlay);
    });

    function openApplyModal(id, title) {
        const overlay = document.getElementById('applyModalOverlay');
        const box = document.getElementById('applyModalBox');
        
        document.getElementById('applyModalTitle').innerText = 'Apply: ' + title;
        document.getElementById('applyForm').action = '/student/opportunities/' + id + '/apply';
        
        document.body.style.overflow = 'hidden';
        overlay.style.pointerEvents = 'auto';
        overlay.style.opacity = '1';
        
        setTimeout(() => {
            box.style.opacity = '1';
            box.style.transform = 'translateY(0) scale(1)';
        }, 50);
    }

    function closeApplyModal() {
        const overlay = document.getElementById('applyModalOverlay');
        const box = document.getElementById('applyModalBox');
        
        box.style.opacity = '0';
        box.style.transform = 'translateY(20px) scale(0.95)';
        
        setTimeout(() => {
            overlay.style.opacity = '0';
            overlay.style.pointerEvents = 'none';
            document.body.style.overflow = '';
        }, 200);
    }
</script>
@endsection
