@extends('layouts.app')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1>Reward Catalog</h1>
        <p class="text-muted">Redeem your hard-earned points for exclusive rewards.</p>
    </div>
    <div class="card text-center" style="padding: 1rem 2rem; background: rgba(16, 185, 129, 0.1); border-color: var(--secondary);">
        <p class="text-sm text-muted">Your Balance</p>
        <h2 style="color: var(--secondary);">{{ $userPoints }} Points</h2>
    </div>
</div>

<div class="grid grid-cols-3 gap-6">
    @foreach($rewards as $reward)
    <div class="card flex flex-col justify-between">
        <div>
            <div style="height: 150px; background: rgba(79, 70, 229, 0.1); border-radius: var(--radius-md) var(--radius-md) 0 0; margin: -1.5rem -1.5rem 1.5rem -1.5rem; display: flex; align-items: center; justify-content: center;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><polyline points="20 12 20 22 12 17 4 22 4 12"/><rect x="4" y="2" width="16" height="10" rx="2" ry="2"/></svg>
            </div>
            <h3 class="mb-2">{{ $reward->name }}</h3>
            <p class="text-sm text-muted mb-4">{{ $reward->description }}</p>
            
            <div class="flex justify-between items-center mb-6">
                <span class="badge badge-warning flex items-center gap-1">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                    {{ $reward->points_required }} Pts
                </span>
                <span class="text-sm text-muted">{{ $reward->stock }} left</span>
            </div>
        </div>
        
        <div>
            @if($reward->stock > 0)
                @if($userPoints >= $reward->points_required)
                    <form method="POST" action="{{ route('student.rewards.claim', $reward->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Redeem Reward</button>
                    </form>
                @else
                    <button class="btn btn-outline" style="width: 100%; opacity: 0.5; cursor: not-allowed;" disabled>Not Enough Points</button>
                @endif
            @else
                <button class="btn btn-danger" style="width: 100%; opacity: 0.5; cursor: not-allowed;" disabled>Out of Stock</button>
            @endif
        </div>
    </div>
    @endforeach
</div>

<div class="card mt-8">
    <h3 class="mb-4">Claim History</h3>
    @if($history->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border); text-align: left;">
                        <th style="padding: 1rem;">Reward</th>
                        <th style="padding: 1rem;">Points Spent</th>
                        <th style="padding: 1rem;">Status</th>
                        <th style="padding: 1rem;">Claimed At</th>
                        <th style="padding: 1rem; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $claim)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 1rem; font-weight: 500;">{{ $claim->reward->name }}</td>
                        <td style="padding: 1rem; color: var(--danger);">-{{ $claim->reward->points_required }}</td>
                        <td style="padding: 1rem;">
                            <span class="badge badge-success">{{ ucfirst($claim->status) }}</span>
                        </td>
                        <td style="padding: 1rem; color: var(--text-muted); font-size: 0.875rem;">{{ $claim->created_at->format('M d, Y H:i') }}</td>
                        <td style="padding: 1rem; text-align: right;">
                            <button onclick="showQrModal('CLAIM-{{ str_pad($claim->id, 5, '0', STR_PAD_LEFT) }}')" class="btn btn-outline btn-sm" style="padding: 0.25rem 0.75rem;">View Code</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-muted text-center py-4">You haven't claimed any rewards yet.</p>
    @endif
</div>

<!-- Smooth Animated QR Code Modal -->
<div id="qrModalOverlay" style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 100; opacity: 0; pointer-events: none; transition: opacity 0.3s ease; display: flex; align-items: center; justify-content: center; padding: 1rem;">
    
    <div id="qrModalBox" class="card text-center" style="width: 100%; max-width: 340px; background: var(--surface); transform: translateY(20px) scale(0.95); opacity: 0; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); padding: 2rem 1.5rem; border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        
        <div style="display: flex; justify-content: center; margin-bottom: 1.5rem;">
            <div style="background: rgba(99, 102, 241, 0.1); padding: 0.75rem; border-radius: 50%; color: var(--primary);">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect x="7" y="7" width="10" height="10" rx="1"/></svg>
            </div>
        </div>

        <h3 class="mb-2" style="font-size: 1.25rem;">Claim Your Reward</h3>
        <p class="text-sm text-muted mb-6">Scan this code at the administration desk to redeem.</p>
        
        <div style="background: white; padding: 1rem; border-radius: 1rem; display: inline-block; margin-bottom: 1.5rem; border: 4px solid rgba(99, 102, 241, 0.1); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <div id="qrLoading" style="width: 180px; height: 180px; display: flex; align-items: center; justify-content: center; color: var(--text-muted);">Loading...</div>
            <img id="qrImage" src="" alt="QR Code" style="width: 180px; height: 180px; display: none; object-fit: contain;">
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 0.5rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; display: inline-block;">
            <h2 id="qrText" style="letter-spacing: 3px; font-family: monospace; font-size: 1.125rem; color: var(--text); margin: 0;"></h2>
        </div>
        
        <button onclick="closeQrModal()" class="btn btn-outline" style="width: 100%; border-radius: var(--radius-full);">Done</button>
    </div>
</div>

<script>
    const overlay = document.getElementById('qrModalOverlay');
    const box = document.getElementById('qrModalBox');
    const img = document.getElementById('qrImage');
    const loading = document.getElementById('qrLoading');

    // Move modal out of any parent container to the root body 
    // This prevents position:fixed from breaking if a parent has CSS transforms/filters
    document.addEventListener("DOMContentLoaded", function() {
        document.body.appendChild(overlay);
    });

    function showQrModal(code) {
        document.getElementById('qrText').innerText = code;
        
        // Reset image state
        img.style.display = 'none';
        loading.style.display = 'flex';
        
        // Load new image
        img.onload = () => {
            loading.style.display = 'none';
            img.style.display = 'block';
        };
        img.src = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' + encodeURIComponent(code);
        
        // Prevent background scrolling
        document.body.style.overflow = 'hidden';
        
        // Animate In
        overlay.style.pointerEvents = 'auto';
        overlay.style.opacity = '1';
        
        setTimeout(() => {
            box.style.opacity = '1';
            box.style.transform = 'translateY(0) scale(1)';
        }, 50);
    }

    function closeQrModal() {
        // Animate Out
        box.style.opacity = '0';
        box.style.transform = 'translateY(20px) scale(0.95)';
        
        setTimeout(() => {
            overlay.style.opacity = '0';
            overlay.style.pointerEvents = 'none';
            // Restore background scrolling
            document.body.style.overflow = '';
        }, 200);
    }
</script>
@endsection
