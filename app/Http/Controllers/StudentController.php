<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        
        $stats = [
            'pending' => Submission::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved' => Submission::where('user_id', $user->id)->where('status', 'approved')->count(),
            'rejected' => Submission::where('user_id', $user->id)->where('status', 'rejected')->count(),
        ];
        
        $query = Submission::where('user_id', $user->id);
        if ($request->has('filter')) {
            $query->where('status', $request->filter);
        }
        $submissions = $query->latest()->get();
        
        $recommendations = session('ai_recommendations', [
            'Learn Advanced Laravel for Hackathon',
            'Apply for Google Developer Student Club',
            'Participate in National UI/UX Competition'
        ]);
        
        $opportunities = \App\Models\Opportunity::latest()->get();
        return view('student.dashboard', compact('user', 'submissions', 'recommendations', 'opportunities', 'stats'));
    }

    public function askAI(Request $request)
    {
        $request->validate(['query' => 'required|string|max:500']);
        
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return back()->with('error', 'API Key not configured.');
        }

        $userQuery = $request->input('query');
        $prompt = "You are a career and talent advisor for university students. The student asks: '{$userQuery}'. Based on their question, give exactly 3 short, actionable recommendations (bullet points) to improve their skills or career. Output ONLY the 3 recommendations separated by a newline, no numbering, no introductory text.";

        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->timeout(5)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ]
                ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text');
                if ($text) {
                    $lines = array_filter(array_map('trim', explode("\n", str_replace(['*', '- '], '', $text))));
                    $recommendations = array_slice(array_values($lines), 0, 3);
                    if (count($recommendations) > 0) {
                        session(['ai_recommendations' => $recommendations]);
                        return back()->with('success', 'AI Recommendations updated via Gemini!');
                    }
                }
            }
        } catch (\Exception $e) {
            // Log error but proceed to fallback
            \Illuminate\Support\Facades\Log::error('Gemini Exception: ' . $e->getMessage());
        }

        // SMART FALLBACK AI FOR MVP
        $q = strtolower($userQuery);
        $recommendations = [];

        if (str_contains($q, 'ui') || str_contains($q, 'ux') || str_contains($q, 'design')) {
            $recommendations = [
                'Complete the Google UX Design Professional Certificate',
                'Build 3 case studies for your UI/UX portfolio',
                'Master Figma advanced prototyping skills'
            ];
        } elseif (str_contains($q, 'data') || str_contains($q, 'ai') || str_contains($q, 'machine learning')) {
            $recommendations = [
                'Learn Python Data Structures and Pandas',
                'Participate in a Kaggle beginner competition',
                'Build a basic predictive model and deploy it'
            ];
        } elseif (str_contains($q, 'web') || str_contains($q, 'laravel') || str_contains($q, 'code')) {
            $recommendations = [
                'Master Laravel MVC and Eloquent ORM',
                'Contribute to open-source PHP projects on GitHub',
                'Learn modern frontend frameworks like React or Vue'
            ];
        } else {
            $recommendations = [
                'Identify your core interests and join a relevant campus club',
                'Seek a mentorship session with a senior student or lecturer',
                'Complete one online course in your chosen field this month'
            ];
        }

        session(['ai_recommendations' => $recommendations]);
        return back()->with('success', 'AI Recommendations generated (Fallback Mode).');
    }

    public function profile()
    {
        $user = Auth::user();
        $submissions = Submission::where('user_id', $user->id)->latest()->get();
        return view('student.profile', compact('user', 'submissions'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'bio' => 'nullable|string',
            'major' => 'nullable|string',
        ]);
        
        $user->update([
            'bio' => $request->bio,
            'major' => $request->major,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'type' => 'required|in:skill,portfolio,certificate',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'proof_file_url' => 'nullable|string'
        ]);

        $path = null;
        if ($request->filled('proof_file_url')) {
            $path = $request->input('proof_file_url');
        } elseif ($request->hasFile('proof_file')) {
            if (env('CLOUDINARY_API_KEY') && env('CLOUDINARY_CLOUD_NAME')) {
                try {
                    $path = \App\Services\CloudinaryService::upload($request->file('proof_file'));
                } catch (\Exception $e) {
                    return back()->with('error', 'Cloudinary upload failed: ' . $e->getMessage());
                }
            } else {
                $path = $request->file('proof_file')->store('proofs', 'public');
            }
        }

        Submission::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'proof_file_path' => $path,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Submission sent for verification.');
    }

    public function leaderboard()
    {
        $students = User::where('role', 'student')->orderBy('points', 'desc')->get();
        return view('student.leaderboard', compact('students'));
    }

    public function rewards()
    {
        $rewards = Reward::all();
        $userPoints = Auth::user()->points;
        $history = \App\Models\RewardClaim::with('reward')
                        ->where('user_id', Auth::id())
                        ->latest()
                        ->get();
        return view('student.rewards', compact('rewards', 'userPoints', 'history'));
    }

    public function claimReward(Request $request, Reward $reward)
    {
        $user = Auth::user();
        
        if ($user->points >= $reward->points_required && $reward->stock > 0) {
            $user->points -= $reward->points_required;
            $user->save();
            
            $reward->stock -= 1;
            $reward->save();
            
            \App\Models\RewardClaim::create([
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'status' => 'claimed'
            ]);
            
            return back()->with('success', 'Reward claimed successfully!');
        }
        
        return back()->with('error', 'Not enough points or out of stock.');
    }

    public function applyOpportunity(Request $request, \App\Models\Opportunity $opportunity)
    {
        $request->validate([
            'contact_info' => 'required|string|max:500',
        ]);

        if ($opportunity->status !== 'open') {
            return back()->with('error', 'This opportunity is no longer open.');
        }

        // Check if already applied
        $existing = \App\Models\OpportunityApplication::where('user_id', Auth::id())
            ->where('opportunity_id', $opportunity->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already applied to this opportunity.');
        }

        \App\Models\OpportunityApplication::create([
            'user_id' => Auth::id(),
            'opportunity_id' => $opportunity->id,
            'contact_info' => $request->contact_info,
        ]);

        return back()->with('success', 'Successfully applied to the opportunity!');
    }
}
