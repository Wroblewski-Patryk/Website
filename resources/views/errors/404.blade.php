<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - {{ app()->getLocale() === 'pl' ? 'Strona nie znaleziona' : 'Page Not Found' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            color: #e2e8f0;
            overflow: hidden;
        }

        .container {
            text-align: center;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .error-code {
            font-size: clamp(7rem, 20vw, 12rem);
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 30%, #a78bfa 60%, #6366f1 100%);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradient-shift 4s ease infinite;
            letter-spacing: -0.05em;
            margin-bottom: 1rem;
            filter: drop-shadow(0 0 40px rgba(99, 102, 241, 0.3));
        }

        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .message {
            font-size: clamp(1.25rem, 3vw, 1.75rem);
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #f1f5f9;
        }

        .description {
            font-size: 1rem;
            color: #94a3b8;
            margin-bottom: 2.5rem;
            max-width: 400px;
            margin-inline: auto;
            line-height: 1.6;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 2rem;
            font-size: 0.95rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            border-radius: 0.75rem;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.5);
        }

        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            pointer-events: none;
        }
        .bg-orb--1 { width: 500px; height: 500px; background: #6366f1; top: -150px; right: -100px; }
        .bg-orb--2 { width: 400px; height: 400px; background: #8b5cf6; bottom: -100px; left: -100px; }
    </style>
</head>
<body>
    <div class="bg-orb bg-orb--1"></div>
    <div class="bg-orb bg-orb--2"></div>

    <div class="container">
        <div class="error-code">404</div>
        <p class="message">
            {{ app()->getLocale() === 'pl' ? 'Strona nie znaleziona' : 'Page Not Found' }}
        </p>
        <p class="description">
            {{ app()->getLocale() === 'pl'
                ? 'Strona, której szukasz, nie istnieje lub została przeniesiona.'
                : 'The page you are looking for does not exist or has been moved.' }}
        </p>
        <a href="/" class="btn">
            ← {{ app()->getLocale() === 'pl' ? 'Wróć na stronę główną' : 'Back to Home' }}
        </a>
    </div>
</body>
</html>
