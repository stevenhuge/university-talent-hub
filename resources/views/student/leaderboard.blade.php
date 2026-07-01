@extends('layouts.app')

@section('content')
<div class="mb-8 text-center">
    <h1>Top Talents Leaderboard</h1>
    <p class="text-muted">Discover the most active and skilled students in the university.</p>
</div>

<div class="card max-w-4xl mx-auto" style="max-width: 800px; margin: 0 auto;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border); text-align: left;">
                    <th style="padding: 1rem; width: 60px;">Rank</th>
                    <th style="padding: 1rem;">Student</th>
                    <th class="hidden-mobile" style="padding: 1rem;">Major</th>
                    <th style="padding: 1rem; text-align: right;">Points</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                <tr style="border-bottom: 1px solid var(--border); transition: var(--transition);" class="hover-bg">
                    <td style="padding: 1rem; font-weight: bold; color: {{ $index < 3 ? 'var(--primary)' : 'var(--text-muted)' }};">
                        #{{ $index + 1 }}
                    </td>
                    <td style="padding: 1rem;">
                        <div class="flex items-center gap-4">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: {{ $student->rank_color }}; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                                {{ substr($student->name, 0, 1) }}
                            </div>
                            <div>
                                <strong style="display: block; font-size: 1.05rem;">{{ $student->name }}</strong>
                                <div style="display: flex; gap: 0.5rem; align-items: center; margin-top: 0.25rem;">
                                    <span style="font-size: 0.75rem; background: {{ $student->rank_color }}; color: white; padding: 0.1rem 0.5rem; border-radius: 12px; font-weight: 600;">{{ $student->rank }}</span>
                                    <span class="text-sm text-muted hidden-mobile">{{ $student->bio ? Str::limit($student->bio, 40) : 'No bio yet' }}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="hidden-mobile" style="padding: 1rem; color: var(--text-muted);">
                        <strong style="color: var(--text);">{{ $student->major ?? 'Undeclared' }}</strong><br>
                        <span style="font-size: 0.8rem;">{{ $student->skills ?? 'No specific skills listed' }}</span>
                    </td>
                    <td style="padding: 1rem; text-align: right; font-weight: bold; font-size: 1.25rem; color: var(--secondary);">
                        {{ $student->points }} <span style="font-size: 0.8rem; font-weight: normal; color: var(--text-muted);">pts</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    .hover-bg:hover {
        background-color: rgba(79, 70, 229, 0.03);
    }
</style>
@endsection
