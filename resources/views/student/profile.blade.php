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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('proof_file');
        if (!fileInput) return;

        const form = fileInput.form;
        const cloudinaryCloudName = "{{ env('CLOUDINARY_CLOUD_NAME') }}";
        // Default to 'ml_default' if not defined in .env
        const cloudinaryUploadPreset = "{{ env('CLOUDINARY_UPLOAD_PRESET', 'ml_default') }}";

        form.addEventListener('submit', async function (e) {
            // Check if file is selected and Cloudinary is configured
            if (fileInput.files.length > 0 && cloudinaryCloudName && cloudinaryCloudName !== 'YOUR_CLOUDINARY_CLOUD_NAME') {
                e.preventDefault(); // Stop native submission
                
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerText;
                
                // Show loading indicator
                submitBtn.disabled = true;
                submitBtn.innerText = 'Uploading directly to Cloudinary...';

                const file = fileInput.files[0];
                const formData = new FormData();
                formData.append('file', file);
                formData.append('upload_preset', cloudinaryUploadPreset);

                try {
                    const response = await fetch(`https://api.cloudinary.com/v1_1/${cloudinaryCloudName}/auto/upload`, {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) {
                        const err = await response.json();
                        throw new Error(err.error?.message || 'Failed to upload to Cloudinary');
                    }

                    const data = await response.json();
                    
                    // Create hidden input to pass the Cloudinary URL
                    const urlInput = document.createElement('input');
                    urlInput.type = 'hidden';
                    urlInput.name = 'proof_file_url';
                    urlInput.value = data.secure_url;
                    form.appendChild(urlInput);

                    // Clear the file input value so we don't upload the binary to our Laravel server
                    fileInput.value = '';

                    // Submit the form
                    form.submit();
                } catch (error) {
                    console.error('Cloudinary Client-side Error:', error);
                    alert('Cloudinary Upload Failed: ' + error.message + '\n\nFalling back to server-side upload...');
                    
                    // Restore submit button and continue with normal form submission
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;
                    form.submit();
                }
            }
        });
    });
</script>
@endsection
