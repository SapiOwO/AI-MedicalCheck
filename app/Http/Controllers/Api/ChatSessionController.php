<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatSessionController extends Controller
{
    /**
     * Start a new chat session (after detection)
     */
    public function start(Request $request)
    {
        $request->validate([
            'session_token' => 'nullable|string',
            'emotion' => 'nullable|string',
            'fatigue' => 'nullable|string',
            'pain' => 'nullable|string',
            'emotion_confidence' => 'nullable|numeric',
            'fatigue_confidence' => 'nullable|numeric',
            'pain_confidence' => 'nullable|numeric',
            'emotion_probabilities' => 'nullable|array',
            'fatigue_probabilities' => 'nullable|array',
            'pain_probabilities' => 'nullable|array',
        ]);

        $session = ChatSession::create([
            'user_id' => auth()->id(), // Null for guest
            'session_token' => $request->session_token ?? Str::random(32),
            'initial_emotion' => $request->emotion,
            'initial_fatigue' => $request->fatigue,
            'initial_pain' => $request->pain,
            'emotion_confidence' => $request->emotion_confidence,
            'fatigue_confidence' => $request->fatigue_confidence,
            'pain_confidence' => $request->pain_confidence,
            'emotion_probabilities' => $request->emotion_probabilities,
            'fatigue_probabilities' => $request->fatigue_probabilities,
            'pain_probabilities' => $request->pain_probabilities,
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Chat session started',
            'data' => [
                'session' => [
                    'id' => $session->id,
                    'session_token' => $session->session_token,
                    'initial_emotion' => $session->initial_emotion,
                    'initial_fatigue' => $session->initial_fatigue,
                    'initial_pain' => $session->initial_pain,
                    'status' => $session->status,
                    'started_at' => $session->started_at,
                ],
            ],
        ], 201);
    }

    /**
     * Get user's chat history (authenticated users only)
     */
    public function index(Request $request)
    {
        $sessions = ChatSession::where('user_id', auth()->id())
            ->withCount('messages')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'sessions' => $sessions->map(function ($session) {
                    return [
                        'id' => $session->id,
                        'initial_emotion' => $session->initial_emotion,
                        'initial_fatigue' => $session->initial_fatigue,
                        'initial_pain' => $session->initial_pain,
                        'status' => $session->status,
                        'message_count' => $session->messages_count,
                        'started_at' => $session->started_at,
                        'ended_at' => $session->ended_at,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Get specific chat session with messages
     */
    public function show(Request $request, $id)
    {
        $query = ChatSession::with('messages');

        // Check if user is authenticated
        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            // For guest, check session_token
            $sessionToken = $request->query('session_token');
            if (!$sessionToken) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session token required for guest access',
                ], 401);
            }
            $query->where('session_token', $sessionToken);
        }

        $session = $query->find($id);

        if (!$session) {
            return response()->json([
                'success' => false,
                'error' => 'Session not found or access denied',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'session' => [
                    'id' => $session->id,
                    'initial_emotion' => $session->initial_emotion,
                    'initial_fatigue' => $session->initial_fatigue,
                    'initial_pain' => $session->initial_pain,
                    'emotion_confidence' => $session->emotion_confidence,
                    'fatigue_confidence' => $session->fatigue_confidence,
                    'pain_confidence' => $session->pain_confidence,
                    'status' => $session->status,
                    'started_at' => $session->started_at,
                    'ended_at' => $session->ended_at,
                    'messages' => $session->messages->map(function ($message) {
                        return [
                            'id' => $message->id,
                            'sender' => $message->sender,
                            'message' => $message->message,
                            'metadata' => $message->metadata,
                            'created_at' => $message->created_at,
                        ];
                    }),
                ],
            ],
        ]);
    }

    /**
     * End chat session
     */
    public function end(Request $request, $id)
    {
        $query = ChatSession::query();

        // Check access
        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            $sessionToken = $request->query('session_token');
            if (!$sessionToken) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session token required',
                ], 401);
            }
            $query->where('session_token', $sessionToken);
        }

        $session = $query->find($id);

        if (!$session) {
            return response()->json([
                'success' => false,
                'error' => 'Session not found or access denied',
            ], 404);
        }

        $session->markCompleted();

        return response()->json([
            'success' => true,
            'message' => 'Session ended successfully',
        ]);
    }

    /**
     * Update session profile data (for profile page)
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'session_id' => 'required|integer',
            'session_token' => 'nullable|string',
            'age' => 'nullable|integer|min:1|max:120',
            'gender' => 'nullable|string|in:male,female,other',
            'symptom_data' => 'nullable|array',
            'ai_detection_data' => 'nullable|array',
        ]);

        $query = ChatSession::query();

        // Check access
        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            $sessionToken = $request->session_token;
            if (!$sessionToken) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session token required for guest access',
                ], 401);
            }
            $query->where('session_token', $sessionToken);
        }

        $session = $query->find($request->session_id);

        if (!$session) {
            return response()->json([
                'success' => false,
                'error' => 'Session not found or access denied',
            ], 404);
        }

        // Update profile data
        $session->update([
            'age' => $request->age,
            'gender' => $request->gender,
            'symptom_data' => $request->symptom_data,
            'ai_detection_data' => $request->ai_detection_data,
            'current_step' => 'profile',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'session' => [
                    'id' => $session->id,
                    'age' => $session->age,
                    'gender' => $session->gender,
                    'symptom_data' => $session->symptom_data,
                    'ai_detection_data' => $session->ai_detection_data,
                ],
            ],
        ]);
    }
}

