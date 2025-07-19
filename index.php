<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Administrator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap');
        body {
            background: linear-gradient(135deg, #1c1917 0%, #7f1d1d 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
            cursor: none;
        }
        .custom-cursor {
            position: fixed;
            width: 20px;
            height: 20px;
            background: rgba(220, 38, 38, 0.5);
            border-radius: 50%;
            pointer-events: none;
            transform: translate(-50%, -50%);
            z-index: 9999;
            transition: transform 0.1s ease;
        }
        .container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            border-radius: 1.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            padding: 3rem;
            max-width: 600px;
            width: 100%;
            margin: 1rem;
            border: 2px solid rgba(220, 38, 38, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            z-index: 10;
        }
        .container:hover {
            box-shadow: 0 15px 50px rgba(220, 38, 38, 0.5);
        }
        h1 {
            color: #ffffff;
            font-size: 3rem;
            font-weight: 800;
            text-align: center;
            text-shadow: 0 0 15px rgba(220, 38, 38, 0.9);
            margin-bottom: 0.5rem;
        }
        .tagline {
            color: #fee2e2;
            font-size: 1.2rem;
            text-align: center;
            margin-bottom: 1.5rem;
            opacity: 0;
            animation: fadeIn 1s ease forwards 0.5s;
        }
        h3 {
            color: #fee2e2;
            font-size: 1.8rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1rem;
        }
        .nav-links {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .nav-links a {
            text-decoration: none;
            color: #ffffff;
            background: linear-gradient(45deg, #dc2626, #f87171);
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: pulse 2s infinite;
        }
        .nav-links a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.4s ease;
        }
        .nav-links a:hover::before {
            left: 100%;
        }
        .nav-links a:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 20px rgba(220, 38, 38, 0.8);
        }
        hr {
            border: 0;
            height: 2px;
            background: linear-gradient(to right, transparent, #dc2626, transparent);
            margin: 1.5rem 0;
        }
        p {
            color: #d1d5db;
            font-size: 1.2rem;
            text-align: center;
        }
        canvas {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        @keyframes fadeIn {
            to { opacity: 1; }
        }
        @media (max-width: 640px) {
            .container {
                padding: 2rem;
                margin: 0.5rem;
            }
            h1 {
                font-size: 2.2rem;
            }
            h3 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="custom-cursor" id="cursor"></div>
    <canvas id="particle-canvas"></canvas>
    <div class="container" id="tilt-container">
        <h1>Halaman Administrator</h1>
        <p class="tagline">Kelola dengan Kekuatan dan Gaya!</p>
        <div class="nav-links">
            <a href="index.html" onclick="playClickSound()">Home</a> 
            <a href="logout.php" onclick="playClickSound()">Logout</a>
        </div>
        <hr>
        <h3>Selamat Datang, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h3>
        <p>Halaman ini akan tampil setelah user login</p>
    </div>
    <script>
        // Custom Cursor
        const cursor = document.getElementById('cursor');
        document.addEventListener('mousemove', (e) => {
            cursor.style.left = e.clientX + 'px';
            cursor.style.top = e.clientY + 'px';
        });

        // Tilt Effect
        const container = document.getElementById('tilt-container');
        container.addEventListener('mousemove', (e) => {
            const rect = container.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            const tiltX = (y / rect.height) * 20;
            const tiltY = -(x / rect.width) * 20;
            container.style.transform = `perspective(1000px) rotateX(${tiltX}deg) rotateY(${tiltY}deg)`;
        });
        container.addEventListener('mouseleave', () => {
            container.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
        });

        // Particle Effect
        const canvas = document.getElementById('particle-canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        const particles = [];
        class Particle {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 5 + 1;
                this.speedX = Math.random() * 2 - 1;
                this.speedY = Math.random() * 2 - 1;
            }
            update() {
                this.x += this.speedX;
                this.y += this.speedY;
                if (this.size > 0.2) this.size -= 0.1;
            }
            draw() {
                ctx.fillStyle = 'rgba(220, 38, 38, 0.5)';
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }
        function handleParticles() {
            for (let i = 0; i < particles.length; i++) {
                particles[i].update();
                particles[i].draw();
                if (particles[i].size <= 0.2) {
                    particles.splice(i, 1);
                    i--;
                }
            }
        }
        function animateParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            if (Math.random() < 0.1) particles.push(new Particle());
            handleParticles();
            requestAnimationFrame(animateParticles);
        }
        animateParticles();
        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });

        // Click Sound
        function playClickSound() {
            const audio = new Audio('data:audio/wav;base64,UklGRigAAABXQVZFZm10IBIAAAABAAEARKwAAIhYAQACABAAAABkYXRhAgAAAAEA');
            audio.play();
        }
    </script>
</body>
</html>