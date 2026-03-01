<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Belajar Kanji JLPT N5</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .navbar h2 {
            margin: 0;
            font-size: 24px;
        }

        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info span {
            font-size: 14px;
            opacity: 0.9;
        }

        .btn-logout {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid white;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: 0.2s;
        }

        .btn-logout:hover {
            background: rgba(255,255,255,0.3);
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .welcome-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .welcome-card h1 {
            color: #667eea;
            margin-top: 0;
        }

        .welcome-card p {
            color: #666;
            line-height: 1.6;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
        }

        .card h3 {
            color: #667eea;
            margin-top: 0;
        }

        .card p {
            color: #666;
            font-size: 14px;
        }

        .btn-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
            transition: 0.2s;
        }

        .btn-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }

        .stat-box h4 {
            margin: 0 0 10px 0;
            color: #667eea;
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>漢字 Belajar Kanji</h2>
        <div class="user-info">
            <span>Welcome, <strong>{{ Auth::user()->name }}</strong></span>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="welcome-card">
            <h1>Welcome to Your Dashboard!</h1>
            <p>Ini adalah dashboard area untuk pengguna. Anda dapat melihat progres pembelajaran Kanji Anda di sini dan mengakses fitur-fitur pembelajaran.</p>
        </div>

        <div class="stats">
            <div class="stat-box">
                <h4>Kanji Learned</h4>
                <div class="stat-number">0</div>
            </div>
            <div class="stat-box">
                <h4>Kanji Mastered</h4>
                <div class="stat-number">0</div>
            </div>
            <div class="stat-box">
                <h4>Daily Streak</h4>
                <div class="stat-number">0</div>
            </div>
        </div>

        <h2 style="margin-top: 40px; color: #333;">Learning Features</h2>
        <div class="grid-container">
            <div class="card">
                <h3>📚 Kanji List</h3>
                <p>Browse and learn Kanji characters for JLPT N5 level.</p>
                <a href="/list" class="btn-card">Mulai Belajar</a>
            </div>

            <div class="card">
                <h3>✍️ Practice Writing</h3>
                <p>Practice writing Kanji characters to improve your skills.</p>
                <a href="#" class="btn-card">Practice</a>
            </div>

            <div class="card">
                <h3>🎯 Quiz</h3>
                <p>Test your knowledge with interactive quizzes.</p>
                <a href="#" class="btn-card">Take Quiz</a>
            </div>

            <div class="card">
                <h3>📊 Progress</h3>
                <p>Track your learning progress and achievements.</p>
                <a href="#" class="btn-card">View Progress</a>
            </div>
        </div>
    </div>
</body>
</html>
