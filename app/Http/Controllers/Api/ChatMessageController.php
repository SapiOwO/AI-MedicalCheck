<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatMessageController extends Controller
{
    /**
     * Send message and get bot response
     */
    public function send(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:chat_sessions,id',
            'session_token' => 'nullable|string',
            'message' => 'required|string|max:1000',
        ]);

        // Verify session access
        $query = ChatSession::query();

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            if (!$request->session_token) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session token required for guest',
                ], 401);
            }
            $query->where('session_token', $request->session_token);
        }

        $session = $query->find($request->session_id);

        if (!$session) {
            return response()->json([
                'success' => false,
                'error' => 'Session not found or access denied',
            ], 404);
        }

        // Save user message
        $userMessage = ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender' => 'user',
            'message' => $request->message,
        ]);

        // Generate bot response (using simple logic for now)
        $botResponse = $this->generateBotResponse($session, $request->message);

        // Save bot message
        $botMessage = ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender' => 'bot',
            'message' => $botResponse,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'user_message' => [
                    'id' => $userMessage->id,
                    'sender' => 'user',
                    'message' => $userMessage->message,
                    'created_at' => $userMessage->created_at,
                ],
                'bot_response' => [
                    'id' => $botMessage->id,
                    'sender' => 'bot',
                    'message' => $botMessage->message,
                    'created_at' => $botMessage->created_at,
                ],
            ],
        ]);
    }

    /**
     * Get messages for a session
     */
    public function index(Request $request, $sessionId)
    {
        $query = ChatSession::query();

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

        $session = $query->find($sessionId);

        if (!$session) {
            return response()->json([
                'success' => false,
                'error' => 'Session not found',
            ], 404);
        }

        $messages = $session->messages()->orderBy('created_at', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'messages' => $messages->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'sender' => $message->sender,
                        'message' => $message->message,
                        'created_at' => $message->created_at,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Generate bot response (simple rule-based for now)
     * TODO: Replace with actual chatbot API (Gemini/OpenAI)
     */
    private function generateBotResponse(ChatSession $session, string $userMessage): string
    {
        // Get initial detection data
        $emotion = $session->initial_emotion ?? 'neutral';
        $fatigue = $session->initial_fatigue ?? 'normal';
        $pain = $session->initial_pain ?? 'none';

        // Simple context-aware response based on detection
        $greetingMessages = [
            "Hello! I'm your AI medical assistant. I've detected that you're feeling {$emotion}. How can I help you today?",
            "Hi there! Based on our initial assessment, I notice you're feeling {$emotion}. What brings you here?",
            "Welcome! I can see you're feeling {$emotion}. I'm here to listen and help. What's on your mind?",
        ];

        // If it's the first message, use greeting
        $messageCount = ChatMessage::where('chat_session_id', $session->id)->count();
        
        if ($messageCount === 1) {
            return $greetingMessages[array_rand($greetingMessages)];
        }

        // Simple response based on keywords
        $lowerMessage = strtolower($userMessage);

        if (str_contains($lowerMessage, 'headache') || str_contains($lowerMessage, 'head hurt')) {
            return "I understand you're experiencing a headache. Based on your emotional state ({$emotion}), this could be related to stress or tension. Have you been resting enough? Would you like some suggestions for relief?";
        }

        if (str_contains($lowerMessage, 'stress') || str_contains($lowerMessage, 'anxious')) {
            return "Stress and anxiety can significantly impact your health. I noticed you're feeling {$emotion}. It's important to take care of your mental health. Would you like to discuss some stress management techniques?";
        }

        if (str_contains($lowerMessage, 'tired') || str_contains($lowerMessage, 'fatigue')) {
            return "Feeling tired is common, especially with your current emotional state ({$emotion}). Make sure you're getting adequate rest. Have you noticed any patterns in your fatigue?";
        }

        if (str_contains($lowerMessage, 'thank')) {
            return "You're welcome! I'm here to help. Is there anything else you'd like to discuss about your health?";
        }

        // Default response
        return "I understand. Can you tell me more about that? Your initial assessment showed you were feeling {$emotion}, which might be related to what you're experiencing.";
    }
}
