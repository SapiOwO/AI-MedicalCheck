<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    /**
     * Export chat session as PDF
     */
    public function exportPDF(Request $request, $sessionId)
    {
        $session = $this->getSessionWithAuth($request, $sessionId);
        
        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found or unauthorized.',
            ], 404);
        }

        $messages = $session->messages()->orderBy('created_at', 'asc')->get();

        $pdf = PDF::loadView('exports.chat-pdf', [
            'session' => $session,
            'messages' => $messages,
            'exportDate' => now()->format('Y-m-d H:i:s'),
        ]);

        $filename = 'medisight_chat_' . $session->id . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export chat session as CSV
     */
    public function exportCSV(Request $request, $sessionId)
    {
        $session = $this->getSessionWithAuth($request, $sessionId);
        
        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found or unauthorized.',
            ], 404);
        }

        $messages = $session->messages()->orderBy('created_at', 'asc')->get();

        // Build CSV content
        $headers = ['Timestamp', 'Sender', 'Message'];
        $rows = [];
        
        foreach ($messages as $message) {
            $rows[] = [
                $message->created_at->format('Y-m-d H:i:s'),
                $message->sender,
                str_replace('"', '""', $message->message), // Escape quotes
            ];
        }

        // Create CSV string
        $csv = implode(',', $headers) . "\n";
        foreach ($rows as $row) {
            $csv .= '"' . implode('","', $row) . '"' . "\n";
        }

        $filename = 'medisight_chat_' . $session->id . '_' . now()->format('Ymd_His') . '.csv';

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export detection history as CSV
     */
    public function exportHistoryCSV(Request $request)
    {
        $user = $request->user();
        
        $sessions = ChatSession::where('user_id', $user->id)
            ->whereNotNull('initial_emotion')
            ->orderBy('created_at', 'desc')
            ->get();

        // Build CSV content
        $headers = ['ID', 'Date', 'Time', 'Day', 'Emotion', 'Confidence', 'Fatigue', 'Pain', 'Messages', 'Status'];
        $rows = [];
        
        foreach ($sessions as $session) {
            $rows[] = [
                $session->id,
                $session->created_at->format('Y-m-d'),
                $session->created_at->format('H:i:s'),
                $session->created_at->format('l'),
                $session->initial_emotion ?? 'N/A',
                ($session->emotion_confidence ?? 0) * 100 . '%',
                $session->initial_fatigue ?? 'N/A',
                $session->initial_pain ?? 'N/A',
                $session->messages()->count(),
                $session->status,
            ];
        }

        // Create CSV string
        $csv = implode(',', $headers) . "\n";
        foreach ($rows as $row) {
            $csv .= '"' . implode('","', $row) . '"' . "\n";
        }

        $filename = 'medisight_history_' . now()->format('Ymd_His') . '.csv';

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Get session with authorization check
     */
    private function getSessionWithAuth(Request $request, $sessionId)
    {
        $session = ChatSession::with('messages')->find($sessionId);
        
        if (!$session) {
            return null;
        }

        // Check authorization
        if ($request->user()) {
            // Logged-in user: must own the session
            if ($session->user_id !== $request->user()->id) {
                return null;
            }
        } else {
            // Guest: must have session_token
            $token = $request->header('X-Session-Token') ?? $request->query('session_token');
            if ($session->session_token !== $token) {
                return null;
            }
        }

        return $session;
    }
}
