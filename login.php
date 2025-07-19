<?php
session_start();
include 'koneksi.php';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

if (isset($_POST['username']) && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $stmt = $koneksi->prepare("SELECT username, password FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        if (password_verify($password, $data['password'])) {
            $_SESSION['user'] = $data['username'];
            session_regenerate_id(true);
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('Username atau password tidak valid! Silakan coba lagi.');</script>";
        }
    } else {
        echo "<script>alert('Username tidak ditemukan! Silakan daftar jika belum memiliki akun.');</script>";
    }
    $stmt->close();
} elseif (isset($_POST['username'])) {
    echo "<script>alert('Token CSRF tidak valid! Silakan coba lagi.');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login ke Web</title>
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
            max-width: 400px;
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
        h3 {
            color: #ffffff;
            font-size: 1.8rem;
            font-weight: 800;
            text-align: center;
            text-shadow: 0 0 15px rgba(220, 38, 38, 0.9);
            margin-bottom: 0.5rem;
        }
        .tagline {
            color: #fee2e2;
            font-size: 1.1rem;
            text-align: center;
            margin-bottom: 1.5rem;
            opacity: 0;
            animation: fadeIn 1s ease forwards 0.5s;
        }
        table {
            width: 100%;
        }
        td {
            padding: 0.75rem;
            color: #fee2e2;
        }
        .input-group {
            position: relative;
            margin-bottom: 1rem;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 0.5rem;
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        input:focus {
            outline: none;
            border-color: #dc2626;
            box-shadow: 0 0 10px rgba(220, 38, 38, 0.5);
        }
        label {
            position: absolute;
            top: 0.75rem;
            left: 0.75rem;
            color: #fee2e2;
            font-size: 1rem;
            transition: all 0.3s ease;
            pointer-events: none;
        }
        input:focus + label,
        input:not(:placeholder-shown) + label {
            top: -0.5rem;
            left: 0.5rem;
            font-size: 0.75rem;
            color: #f87171;
        }
        button {
            background: linear-gradient(45deg, #dc2626, #f87171);
            color: #ffffff;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: pulse 2s infinite;
        }
        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.4s ease;
        }
        button:hover::before {
            left: 100%;
        }
        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 20px rgba(220, 38, 38, 0.8);
        }
        a {
            text-decoration: none;
            color: #f87171;
            font-weight: 500;
            margin-left: 1rem;
            transition: color 0.3s ease, text-shadow 0.3s ease;
        }
        a:hover {
            color: #dc2626;
            text-shadow: 0 0 10px rgba(220, 38, 38, 0.7);
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
            h3 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="custom-cursor" id="cursor"></div>
    <canvas id="particle-canvas"></canvas>
    <div class="container" id="tilt-container">
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <h3>Login User</h3>
            <p class="tagline">Masuk dan Mulai Petualangan Anda!</p>
            <table align="center">
                <tr>
                    <td>Username</td>
                    <td class="input-group">
                        <input type="text" name="username" id="username" placeholder=" " required aria-label="Username">
                        <label for="username">Username</label>
                    </td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td class="input-group">
                        <input type="password" name="password" id="password" placeholder=" " required aria-label="Password">
                        <label for="password">Password</label>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <button type="submit" onclick="playClickSound()">Login</button>
                        <a href="daftar.php" onclick="playClickSound()">Daftar</a>
                    </td>
                </tr>
            </table>
        </form>
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