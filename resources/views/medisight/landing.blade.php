@extends('layouts.medisight')

@section('title', 'MediSight AI – Camera-based Health Insights')

@section('nav-links')
    <a href="#features">Features</a>
    <a href="#how-it-works">How it works</a>
    <a href="#security">Privacy</a>
    <a href="{{ url('/login') }}" class="nav-cta">Try now</a>
@endsection

@section('content')
<section class="hero">
    <div>
        <div class="badge">
            <div class="badge-dot"></div>
            Real-time health signals from your face
        </div>

        <h1 class="hero-title">
            Understand emotion, fatigue,<br>
            and <span class="hero-gradient">health risk in seconds.</span>
        </h1>

        <p class="hero-subtitle">
            MediSight AI uses your device camera and deep learning models to
            estimate emotion, fatigue level, and BMI-related risk — then passes
            those insights directly into an AI health assistant.
        </p>

        <div class="hero-actions">
            <a href="{{ url('/login') }}" class="btn-primary">Try MediSight AI</a>
            <a href="#features" class="btn-secondary">Explore features</a>
        </div>

        <div class="hero-metadata">
            <span>Emotion • Fatigue • Pain signals</span>
            <span>BMI estimation from facial landmarks</span>
            <span>Built for healthcare experiments</span>
        </div>
    </div>

    <aside class="hero-preview" aria-hidden="true">
        <div class="hero-preview-inner">
            <div class="hero-preview-header">
                <span class="text-muted">Live Camera · ImagoLab Models</span>
                <span class="badge-small">Inference & Chat</span>
            </div>

            <div class="hero-preview-cam">
                <div class="hero-preview-video" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); display: flex; align-items: center; justify-content: center;">
                    <span style="color: var(--muted); font-size: 12px;">📹 Camera Preview</span>
                </div>
                <div class="hero-preview-panel">
                    <div class="hero-chip-row">
                        <div class="hero-chip">
                            <span class="hero-indicator green"></span>
                            <div>
                                <div class="hero-chip-label">Emotion</div>
                                <div class="hero-chip-value">Calm · 0.82</div>
                            </div>
                        </div>
                        <div class="hero-chip">
                            <span class="hero-indicator yellow"></span>
                            <div>
                                <div class="hero-chip-label">Fatigue</div>
                                <div class="hero-chip-value">Mild · 0.41</div>
                            </div>
                        </div>
                    </div>

                    <div class="hero-chip-row">
                        <div class="hero-chip">
                            <span class="hero-indicator red"></span>
                            <div>
                                <div class="hero-chip-label">Pain</div>
                                <div class="hero-chip-value">Low · 0.18</div>
                            </div>
                        </div>
                        <div class="hero-chip">
                            <span class="hero-indicator green"></span>
                            <div>
                                <div class="hero-chip-label">BMI Risk</div>
                                <div class="hero-chip-value">Normal range</div>
                            </div>
                        </div>
                    </div>

                    <p class="text-muted" style="margin-top: 10px">
                        These readings are passed to the chatbot as your starting context.
                    </p>
                </div>
            </div>
        </div>
    </aside>
</section>

<section id="features" style="margin-top: 56px">
    <h2 class="section-title">What MediSight AI can detect</h2>
    <p class="section-subtitle">
        Under the hood, Laravel calls Python models like
        <code>emotion_best.pth</code>, <code>fatigue_best.pth</code>, and
        <code>pain_best.pth</code> to get structured JSON that feeds the chat.
    </p>

    <div class="dashboard-layout">
        <div class="card">
            <div class="card-header">
                <div class="badge-small">Camera detection</div>
                <h3 class="card-title">Emotion & fatigue</h3>
                <p class="card-subtitle">
                    Track high-level mood and tiredness from facial expressions to
                    help the assistant suggest breaks, lifestyle tips, or follow-up.
                </p>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="badge-small">BMI & pain signals</div>
                <h3 class="card-title">Risk-aware conversations</h3>
                <p class="card-subtitle">
                    Estimated BMI and pain indicators help personalize the first
                    messages the chatbot sends — without needing a long form.
                </p>
            </div>
        </div>
    </div>
</section>

<section id="how-it-works" style="margin-top: 48px">
    <h2 class="section-title">How it works</h2>
    <p class="section-subtitle">
        For logged-in users, chat history is saved. Guests can try MediSight
        once without account creation.
    </p>

    <div class="history-list">
        <div class="history-item" style="cursor: default;">
            <div class="history-meta">
                <strong>1. Landing → Login</strong>
                <span class="text-muted">
                    Click "Try MediSight AI" and choose to sign in or continue as guest.
                </span>
            </div>
        </div>
        <div class="history-item" style="cursor: default;">
            <div class="history-meta">
                <strong>2. Camera detection</strong>
                <span class="text-muted">
                    The browser opens the camera, Laravel calls the Python API, and receives JSON output.
                </span>
            </div>
        </div>
        <div class="history-item" style="cursor: default;">
            <div class="history-meta">
                <strong>3. AI Chat</strong>
                <span class="text-muted">
                    The chatbot uses those values as initial context for the conversation.
                </span>
            </div>
        </div>
    </div>
</section>

<section id="security" style="margin-top: 48px">
    <h2 class="section-title">Privacy & testing first</h2>
    <p class="section-subtitle">
        Your camera data is processed locally and sent only to our secure server.
        No images are stored permanently. All AI processing happens in real-time.
    </p>
</section>
@endsection
