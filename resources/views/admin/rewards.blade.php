@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1>Reward & Opportunity Management</h1>
    <p class="text-muted">Manage the rewards catalog and post new opportunities.</p>
</div>

<div class="grid grid-cols-2 gap-6">
    <!-- Rewards Section -->
    <div class="card">
        <h3 class="mb-4">Current Rewards</h3>
        @if($rewards->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($rewards as $reward)
                    <div style="border: 1px solid var(--border); padding: 1rem; border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>{{ $reward->name }}</strong>
                            <p class="text-sm text-muted">Cost: {{ $reward->points_required }} pts | Stock: {{ $reward->stock }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="document.getElementById('edit-reward-{{ $reward->id }}').classList.toggle('hidden')" class="btn btn-outline btn-sm">Edit</button>
                            <form method="POST" action="{{ route('admin.rewards.delete', $reward) }}" onsubmit="return confirm('Are you sure you want to delete this reward?')">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Del</button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Edit Form (Hidden by default) -->
                    <div id="edit-reward-{{ $reward->id }}" class="hidden" style="background: var(--background); padding: 1rem; border-radius: var(--radius-sm); margin-top: -0.5rem; margin-bottom: 1rem;">
                        <form method="POST" action="{{ route('admin.rewards.update', $reward) }}">
                            @csrf
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="form-label text-sm">Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $reward->name }}" required>
                                </div>
                                <div>
                                    <label class="form-label text-sm">Points Required</label>
                                    <input type="number" name="points_required" class="form-control" value="{{ $reward->points_required }}" min="1" required>
                                </div>
                                <div>
                                    <label class="form-label text-sm">Stock</label>
                                    <input type="number" name="stock" class="form-control" value="{{ $reward->stock }}" min="0" required>
                                </div>
                                <div>
                                    <label class="form-label text-sm">Description</label>
                                    <input type="text" name="description" class="form-control" value="{{ $reward->description }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Update Reward</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted">No rewards available.</p>
        @endif
        
        <div class="mt-6 border-t pt-4" style="border-top: 1px solid var(--border); margin-top: 1.5rem; padding-top: 1rem;">
            <h4>Add New Reward</h4>
            <form method="POST" action="{{ route('admin.rewards.store') }}" class="mt-4">
                @csrf
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="form-label text-sm">Reward Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Free Coffee">
                    </div>
                    <div>
                        <label class="form-label text-sm">Points Required</label>
                        <input type="number" name="points_required" class="form-control" min="1" required placeholder="e.g. 50">
                    </div>
                    <div>
                        <label class="form-label text-sm">Stock</label>
                        <input type="number" name="stock" class="form-control" min="0" required placeholder="e.g. 10">
                    </div>
                    <div>
                        <label class="form-label text-sm">Description (Optional)</label>
                        <input type="text" name="description" class="form-control" placeholder="Short description">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Add Reward</button>
            </form>
        </div>
    </div>

    <!-- Opportunities Section -->
    <div class="card">
        <h3 class="mb-4">Active Opportunities</h3>
        @if($opportunities->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($opportunities as $opp)
                    <div style="border: 1px solid var(--border); padding: 1rem; border-radius: var(--radius-md);">
                        <div class="flex justify-between items-center mb-2">
                            <strong>{{ $opp->title }}</strong>
                            <div class="flex gap-2 items-center">
                                @if($opp->status === 'open')
                                    <span class="badge badge-success">Open</span>
                                @else
                                    <span class="badge badge-secondary">Closed</span>
                                @endif
                                
                                <form method="POST" action="{{ route('admin.opportunities.toggle', $opp) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline btn-sm" style="padding: 0.25rem 0.5rem;" title="Toggle Status">⭘</button>
                                </form>
                                <form method="POST" action="{{ route('admin.opportunities.delete', $opp) }}" onsubmit="return confirm('Delete opportunity?')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.25rem 0.5rem;">x</button>
                                </form>
                            </div>
                        </div>
                        <p class="text-sm text-muted mb-2">{{ Str::limit($opp->description, 60) }}</p>
                        <p class="text-sm mb-4"><strong>Skills:</strong> {{ $opp->required_skills }}</p>
                        
                        <div style="background: rgba(0,0,0,0.02); padding: 0.75rem; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                            <h4 class="text-sm mb-2 flex justify-between items-center">
                                Applicants ({{ $opp->applications->count() }})
                                <button type="button" class="btn btn-outline btn-sm" style="font-size: 0.7rem; padding: 0.1rem 0.4rem;" onclick="document.getElementById('apps-{{ $opp->id }}').classList.toggle('hidden')">View</button>
                            </h4>
                            <div id="apps-{{ $opp->id }}" class="hidden">
                                @if($opp->applications->count() > 0)
                                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.5rem;">
                                        @foreach($opp->applications as $app)
                                            <li style="font-size: 0.8rem; padding: 0.5rem; background: var(--surface); border-radius: var(--radius-sm); border: 1px solid var(--border);">
                                                <strong>{{ $app->user->name }}</strong><br>
                                                <span class="text-muted">{{ $app->contact_info }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-xs text-muted">No applicants yet.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted">No active opportunities.</p>
        @endif

        <div class="mt-6 border-t pt-4" style="border-top: 1px solid var(--border); margin-top: 1.5rem; padding-top: 1rem;">
            <h4>Post Opportunity</h4>
            <form method="POST" action="{{ route('admin.opportunities.store') }}" class="mt-4">
                @csrf
                <div class="form-group mb-4">
                    <label class="form-label text-sm">Title</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. Graphic Designer Intern">
                </div>
                <div class="form-group mb-4">
                    <label class="form-label text-sm">Required Skills</label>
                    <input type="text" name="required_skills" class="form-control" required placeholder="e.g. Adobe Illustrator, Corel">
                </div>
                <div class="form-group mb-4">
                    <label class="form-label text-sm">Description</label>
                    <textarea name="description" class="form-control" rows="3" required placeholder="Details about this opportunity"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Post Opportunity</button>
            </form>
        </div>
    </div>
</div>
@endsection
