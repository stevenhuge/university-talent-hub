@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1>Talent Search</h1>
    <p class="text-muted">Find the right students for your industry partners or internal campus projects.</p>
</div>

<div class="card mb-8">
    <form method="GET" action="{{ route('admin.search') }}" class="flex gap-4">
        <input type="text" name="q" class="form-control" placeholder="Search by name, major, or skill (e.g. Laravel, UI/UX)..." value="{{ request('q') }}" style="flex: 1;">
        <button type="submit" class="btn btn-primary">Search</button>
        @if(request('q'))
            <a href="{{ route('admin.search') }}" class="btn btn-outline">Clear</a>
        @endif
    </form>
</div>

<div class="grid grid-cols-2 gap-6">
    @forelse($students as $student)
    <div class="card flex flex-col justify-between">
        <div>
            <div class="flex items-center gap-4 mb-4">
                <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: bold;">
                    {{ substr($student->name, 0, 1) }}
                </div>
                <div>
                    <h3 style="margin-bottom: 0;">{{ $student->name }}</h3>
                    <p class="text-sm text-muted">{{ $student->major ?? 'No Major Set' }}</p>
                </div>
            </div>
            
            <p class="text-sm mb-4">{{ $student->bio ?? 'No professional summary provided.' }}</p>
            
            <div class="mb-4">
                <p class="text-sm font-semibold mb-2">Verified Skills/Achievements:</p>
                <div class="flex" style="flex-wrap: wrap; gap: 0.5rem;">
                    @foreach($student->submissions->where('status', 'approved')->take(5) as $sub)
                        <span class="badge badge-primary">{{ $sub->title }}</span>
                    @endforeach
                    @if($student->submissions->where('status', 'approved')->count() > 5)
                        <span class="badge" style="background: var(--border);">+{{ $student->submissions->where('status', 'approved')->count() - 5 }} more</span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="flex justify-between items-center" style="border-top: 1px solid var(--border); padding-top: 1rem; margin-top: 1rem;">
            <div>
                <span class="text-muted text-sm">Total Points:</span>
                <strong style="color: var(--secondary); font-size: 1.125rem; margin-left: 0.25rem;">{{ $student->points }}</strong>
            </div>
            <a href="mailto:{{ $student->email }}" class="btn btn-outline btn-sm">Contact</a>
        </div>
    </div>
    @empty
    <div class="col-span-2 text-center py-8">
        <p class="text-muted">No students found matching your criteria.</p>
    </div>
    @endforelse
</div>
@endsection
