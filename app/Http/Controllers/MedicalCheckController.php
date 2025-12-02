<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MedicalCheckController extends Controller
{
    /**
     * Python API base URL
     */
    private const API_BASE_URL = 'http://localhost:8001';

    /**
     * Show medical check page
     */
    public function index()
    {
        return view('medical-check');
    }

    /**
     * Analyze emotion from uploaded image
     */
    public function analyze(Request $request)
    {
        // Validate request
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB
        ]);

        try {
            // Check if Python API is running
            $healthCheck = $this->checkApiHealth();
            
            if (!$healthCheck['healthy']) {
                return response()->json([
                    'success' => false,
                    'error' => 'AI Service is not running. Please contact administrator.',
                    'details' => $healthCheck
                ], 503);
            }

            // Get uploaded file
            $image = $request->file('image');

            // Send to Python API
            $response = Http::timeout(30)
                ->attach('file', file_get_contents($image->getRealPath()), $image->getClientOriginalName())
                ->post(self::API_BASE_URL . '/predict');

            // Check response
            if ($response->successful()) {
                $result = $response->json();

                // Log successful prediction
                Log::info('Emotion detection successful', [
                    'emotion' => $result['emotion'] ?? 'unknown',
                    'confidence' => $result['confidence'] ?? 0
                ]);

                return response()->json([
                    'success' => true,
                    'data' => $result
                ]);
            } else {
                // API returned error
                $error = $response->json();
                
                Log::error('Emotion detection failed', [
                    'status' => $response->status(),
                    'error' => $error
                ]);

                return response()->json([
                    'success' => false,
                    'error' => $error['detail'] ?? 'Failed to analyze image',
                ], $response->status());
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Connection error - Python service not running
            Log::error('Cannot connect to Python API', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Cannot connect to AI Service. Please ensure the service is running.',
                'details' => $e->getMessage()
            ], 503);

        } catch (\Exception $e) {
            // Other errors
            Log::error('Medical check error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An error occurred while processing your request.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check Python API health
     */
    private function checkApiHealth(): array
    {
        try {
            $response = Http::timeout(5)->get(self::API_BASE_URL . '/health');
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'healthy' => true,
                    'model_status' => $data['model_status'] ?? 'unknown',
                    'data' => $data
                ];
            }

            return [
                'healthy' => false,
                'reason' => 'API returned error status',
                'status' => $response->status()
            ];

        } catch (\Exception $e) {
            return [
                'healthy' => false,
                'reason' => 'Cannot connect to API',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get API status (for admin/debugging)
     */
    public function status()
    {
        $health = $this->checkApiHealth();
        
        return response()->json([
            'api_url' => self::API_BASE_URL,
            'health_check' => $health,
            'timestamp' => now()->toIso8601String()
        ]);
    }
}
