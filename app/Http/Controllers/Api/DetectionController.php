<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetectionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DetectionController extends Controller
{
    private const PYTHON_API_URL = 'http://localhost:8001';

    /**
     * Run multi-model detection (emotion, fatigue, pain)
     */
    public function multiDetect(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // 5MB max
            'session_token' => 'nullable|string',
        ]);

        try {
            $image = $request->file('image');

            // Send to Python API
            $response = Http::timeout(30)
                ->attach('file', file_get_contents($image->getRealPath()), $image->getClientOriginalName())
                ->post(self::PYTHON_API_URL . '/predict');

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Detection failed: ' . $response->body(),
                ], 400);
            }

            $result = $response->json();

            // For now, only emotion detection (will add fatigue & pain later)
            $detectionData = [
                'emotion' => $result,
                'fatigue' => ['result' => 'low', 'confidence' => 0.0, 'probabilities' => []], // Placeholder
                'pain' => ['result' => 'none', 'confidence' => 0.0, 'probabilities' => []], // Placeholder
            ];

            // Optionally log detection
            if ($request->has('session_token') || auth()->check()) {
                DetectionLog::create([
                    'chat_session_id' => null, // Will be set when session is created
                    'detection_type' => 'emotion',
                    'result' => $result['emotion'] ?? 'unknown',
                    'confidence' => $result['confidence'] ?? 0,
                    'all_probabilities' => $result['all_probabilities'] ?? [],
                    'face_bbox' => $result['face_bbox'] ?? null,
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $detectionData,
            ]);

        } catch (\Exception $e) {
            Log::error('Detection error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'AI Service error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Individual emotion detection
     */
    public function detectEmotion(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        try {
            $image = $request->file('image');

            $response = Http::timeout(30)
                ->attach('file', file_get_contents($image->getRealPath()), $image->getClientOriginalName())
                ->post(self::PYTHON_API_URL . '/predict');

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Detection failed: ' . $response->body(),
                ], 400);
            }

            $result = $response->json();

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            Log::error('Emotion detection error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'AI Service error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fatigue detection (placeholder - to be implemented)
     */
    public function detectFatigue(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'result' => 'low',
                'confidence' => 0.0,
                'message' => 'Fatigue detection not yet implemented',
            ],
        ]);
    }

    /**
     * Pain detection (placeholder - to be implemented)
     */
    public function detectPain(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'result' => 'none',
                'confidence' => 0.0,
                'message' => 'Pain detection not yet implemented',
            ],
        ]);
    }

    /**
     * Check Python API health
     */
    public function checkHealth()
    {
        try {
            $response = Http::timeout(5)->get(self::PYTHON_API_URL . '/health');

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'AI Service is not responding',
            ], 503);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'AI Service is not running. Please contact administrator.',
            ], 503);
        }
    }
}
