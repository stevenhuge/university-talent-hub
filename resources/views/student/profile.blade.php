@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1>Talent Profile</h1>
    <p class="text-muted">Manage your bio, major, skills, portfolios, and certificates.</p>
</div>

<div class="grid grid-cols-2 gap-6 mb-8">
    <!-- Profile Form -->
    <div class="card">
        <h3 class="mb-4">Basic Information</h3>
        <form method="POST" action="{{ route('student.profile.update') }}">
            @csrf
            
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" id="name" class="form-control" value="{{ $user->name }}" disabled>
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" class="form-control" value="{{ $user->email }}" disabled>
            </div>

            <div class="form-group">
                <label for="major" class="form-label">Major / Study Program</label>
                <input type="text" id="major" name="major" class="form-control" value="{{ $user->major }}" placeholder="e.g. Informatics Engineering">
            </div>

            <div class="form-group">
                <label for="bio" class="form-label">Bio (Professional Summary)</label>
                <textarea id="bio" name="bio" class="form-control" rows="4" placeholder="Tell us about yourself...">{{ $user->bio }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Save Profile</button>
        </form>
    </div>

    <!-- Submission Form -->
    <div class="card">
        <h3 class="mb-4">Add Achievement / Skill</h3>
        <p class="text-sm text-muted mb-4">Submit your certificates, skills, or portfolio to earn points and climb the leaderboard.</p>
        
        <form method="POST" action="{{ route('student.submit') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label for="type" class="form-label">Type</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="skill">Skill</option>
                    <option value="certificate">Certificate</option>
                    <option value="portfolio">Portfolio</option>
                </select>
            </div>

            <div class="form-group">
                <label for="title" class="form-label">Title</label>
                <input type="text" id="title" name="title" class="form-control" required placeholder="e.g. Laravel Developer, National Hackathon Winner">
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description / Link</label>
                <textarea id="description" name="description" class="form-control" rows="3" placeholder="Additional details or links..."></textarea>
            </div>

            <div class="form-group">
                <label for="proof_file" class="form-label">Proof (Image/PDF)</label>
                <input type="file" id="proof_file" name="proof_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                <small class="text-muted">Max 2MB. Important for verification.</small>
            </div>

            <button type="submit" class="btn btn-secondary">Submit for Verification</button>
        </form>
    </div>
</div>

<div class="card">
    <h3 class="mb-4">Submission History</h3>
    @if($submissions->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border); text-align: left;">
                        <th style="padding: 1rem;">Type</th>
                        <th style="padding: 1rem;">Title</th>
                        <th style="padding: 1rem;">Status</th>
                        <th style="padding: 1rem;">Points</th>
                        <th style="padding: 1rem;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submissions as $sub)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 1rem; text-transform: capitalize;">{{ $sub->type }}</td>
                        <td style="padding: 1rem; font-weight: 500;">{{ $sub->title }}</td>
                        <td style="padding: 1rem;">
                            @if($sub->status == 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif($sub->status == 'rejected')
                                <span class="badge badge-danger">Rejected</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </td>
                        <td style="padding: 1rem;">{{ $sub->points_awarded }}</td>
                        <td style="padding: 1rem; color: var(--text-muted); font-size: 0.875rem;">{{ $sub->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-muted text-center py-4">No submissions found.</p>
    @endif
</div>
@endsection
