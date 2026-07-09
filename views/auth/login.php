<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tivayo Collection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 30%, #0f3460 60%, #533483 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .bg-decoration {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            z-index: 0;
        }

        .bg-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.03);
            animation: floatUp 15s infinite ease-in-out;
        }

        .bg-circle:nth-child(1) {
            width: 400px;
            height: 400px;
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }

        .bg-circle:nth-child(2) {
            width: 300px;
            height: 300px;
            bottom: -80px;
            right: -80px;
            animation-delay: 2s;
        }

        .bg-circle:nth-child(3) {
            width: 200px;
            height: 200px;
            top: 50%;
            left: 60%;
            animation-delay: 4s;
        }

        .bg-circle:nth-child(4) {
            width: 150px;
            height: 150px;
            top: 20%;
            right: 15%;
            animation-delay: 1s;
        }

        .bg-circle:nth-child(5) {
            width: 250px;
            height: 250px;
            bottom: 10%;
            left: 10%;
            animation-delay: 3s;
        }

        @keyframes floatUp {

            0%,
            100% {
                transform: translateY(0) scale(1) rotate(0deg);
                opacity: 0.3;
            }

            50% {
                transform: translateY(-30px) scale(1.1) rotate(5deg);
                opacity: 0.5;
            }
        }

        .sparkle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            animation: sparkleAnim 3s infinite ease-in-out;
        }

        @keyframes sparkleAnim {

            0%,
            100% {
                opacity: 0;
                transform: scale(0);
            }

            50% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .login-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 900px;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4), 0 0 40px rgba(83, 52, 131, 0.2);
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-left-panel {
            background: linear-gradient(160deg, #533483 0%, #6a42a0 30%, #8b5cf6 70%, #a78bfa 100%);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
            min-height: 580px;
        }

        .login-left-panel::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 60%);
            animation: rotateGlow 20s linear infinite;
        }

        @keyframes rotateGlow {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .login-left-panel .content {
            position: relative;
            z-index: 2;
        }

        .brand-icon-box {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: pulse 3s infinite ease-in-out;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.3);
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 0 20px 10px rgba(255, 255, 255, 0.1);
            }
        }

        .brand-icon-box i {
            font-size: 3rem;
            color: white;
        }

        .login-left-panel h2 {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .login-left-panel .subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.85rem;
            font-weight: 400;
            margin-bottom: 1.25rem;
            line-height: 1.6;
        }

        .method-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 50px;
            padding: 0.5rem 1.25rem;
            color: white;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }

        .features-list {
            list-style: none;
            text-align: left;
            padding: 0;
            margin: 0;
        }

        .features-list li {
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.8rem;
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .features-list li i {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .login-right-panel {
            background: white;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-right-panel h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 0.25rem;
        }

        .login-right-panel .text-muted {
            font-size: 0.85rem;
        }

        .form-floating-custom {
            margin-bottom: 1.25rem;
        }

        .form-floating-custom label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 0.5rem;
            display: block;
        }

        .input-group-custom {
            position: relative;
        }

        .input-group-custom .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1.1rem;
            z-index: 5;
            transition: color 0.3s ease;
        }

        .input-group-custom .form-control {
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.875rem;
            font-size: 0.9rem;
            background: #f9fafb;
            transition: all 0.3s ease;
        }

        .input-group-custom .form-control:focus {
            border-color: #8b5cf6;
            background: white;
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
        }

        .input-group-custom .form-control:focus~.input-icon {
            color: #8b5cf6;
        }

        .input-group-custom .btn-toggle-password {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            font-size: 1.1rem;
            z-index: 5;
            padding: 0.25rem;
            transition: color 0.3s ease;
        }

        .input-group-custom .btn-toggle-password:hover {
            color: #8b5cf6;
        }

        .btn-login {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 50%, #a78bfa 100%);
            color: white;
            border: none;
            border-radius: 0.875rem;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.5px;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(124, 58, 237, 0.4);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login.loading .btn-text {
            display: none;
        }

        .btn-login .spinner-border-custom {
            display: none;
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            margin: 0 auto;
        }

        .btn-login.loading .spinner-border-custom {
            display: inline-block;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .form-check-input:checked {
            background-color: #8b5cf6;
            border-color: #8b5cf6;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
            border-color: #8b5cf6;
        }

        .forgot-link {
            font-size: 0.8rem;
            color: #8b5cf6;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-link:hover {
            color: #7c3aed;
            text-decoration: underline;
        }

        .alert-custom {
            display: none;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.25rem;
            font-size: 0.8rem;
            align-items: center;
            gap: 0.5rem;
            animation: fadeInDown 0.4s ease;
        }

        .alert-custom.show {
            display: flex;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-5px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(5px);
            }
        }

        .shake {
            animation: shake 0.5s ease;
        }

        .footer-text {
            font-size: 0.75rem;
            color: #9ca3af;
        }

        .footer-text a {
            color: #8b5cf6;
            text-decoration: none;
            font-weight: 500;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }

        .divider-custom {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            gap: 1rem;
        }

        .divider-custom::before,
        .divider-custom::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .divider-custom span {
            font-size: 0.7rem;
            color: #9ca3af;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        @media (max-width: 768px) {
            .login-card {
                max-width: 100%;
                border-radius: 0;
                min-height: 100vh;
            }

            .login-left-panel {
                min-height: auto;
                padding: 2.5rem 2rem;
            }

            .login-left-panel .content .features-list {
                display: none;
            }

            .login-right-panel {
                padding: 2.5rem 2rem;
            }

            .brand-icon-box {
                width: 80px;
                height: 80px;
            }

            .brand-icon-box i {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 576px) {
            .login-left-panel {
                padding: 2rem 1.5rem;
            }

            .login-right-panel {
                padding: 2rem 1.5rem;
            }

            .login-right-panel h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>

    <!-- Background Decoration -->
    <div class="bg-decoration">
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
        <div class="sparkle" style="top:15%; left:25%; animation-delay:0s;"></div>
        <div class="sparkle" style="top:75%; left:80%; animation-delay:0.5s;"></div>
        <div class="sparkle" style="top:45%; left:90%; animation-delay:1s;"></div>
        <div class="sparkle" style="top:85%; left:15%; animation-delay:1.5s;"></div>
        <div class="sparkle" style="top:10%; left:70%; animation-delay:2s;"></div>
        <div class="sparkle" style="top:60%; left:40%; animation-delay:2.5s;"></div>
        <div class="sparkle" style="top:30%; left:50%; animation-delay:0.8s;"></div>
        <div class="sparkle" style="top:90%; left:60%; animation-delay:1.3s;"></div>
    </div>

    <!-- Login Card -->
    <div class="card login-card shadow" id="loginContainer">
        <div class="row g-0">
            <!-- Left Panel -->
            <div class="col-lg-6 d-none d-lg-flex login-left-panel">
                <div class="content">
                    <div class="brand-icon-box">
                        <i class="bi bi-bag-heart-fill"></i>
                    </div>
                    <h2>Prediksi Penjualan Daster</h2>
                    <p class="subtitle">Sistem Prediksi Penjualan Daster<br>dengan Metode Single Moving Average</p>
                    <div class="method-badge">
                        <i class="bi bi-bar-chart-line-fill me-2"></i>Single Moving Average
                    </div>
                    <ul class="features-list">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Prediksi penjualan akurat</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Analisis data historis</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Laporan & grafik interaktif</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Manajemen data produk</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right Panel -->
            <div class="col-lg-6 col-md-12 login-right-panel">
                <div class="mb-4">
                    <h1>Selamat Datang! <span>👋</span></h1>
                    <p class="text-muted">Silakan masuk ke akun Anda untuk melanjutkan</p>
                </div>

                <!-- Alert Box -->
                <div class="alert alert-custom" id="alertBox" role="alert">
                    <i class="bi bi-exclamation-circle-fill" id="alertIcon" style="font-size: 1.1rem;"></i>
                    <span id="alertMessage"></span>
                </div>

                <!-- Login Form -->
                <form id="loginForm" autocomplete="off">
                    <div class="form-floating-custom">
                        <label for="username">Username</label>
                        <div class="input-group-custom">
                            <input type="text" class="form-control" id="username" name="username"
                                placeholder="Masukkan username Anda" required>
                            <i class="bi bi-person-fill input-icon"></i>
                        </div>
                    </div>

                    <div class="form-floating-custom">
                        <label for="password">Password</label>
                        <div class="input-group-custom">
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Masukkan password Anda" required>
                            <i class="bi bi-lock-fill input-icon"></i>
                            <button type="button" class="btn-toggle-password" id="togglePassword"
                                aria-label="Toggle password visibility">
                                <i class="bi bi-eye-fill" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label" for="rememberMe" style="font-size: 0.8rem; color: #6b7280;">
                                Ingat saya
                            </label>
                        </div>
                        <a href="forgot_password.php" class="forgot-link">Lupa password?</a>
                    </div>

                    <button type="submit" class="btn-login" id="btnLogin">
                        <span class="btn-text">
                            <span>Masuk</span>
                            <i class="bi bi-arrow-right"></i>
                        </span>
                        <span class="spinner-border-custom"></span>
                    </button>
                </form>

                <div class="divider-custom">
                    <span>atau</span>
                </div>

                <p class="footer-text text-center">
                    Belum punya akun? <a href="#">Hubungi Admin</a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loginForm = document.getElementById('loginForm');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');
            const eyeIcon = document.getElementById('eyeIcon');
            const btnLogin = document.getElementById('btnLogin');
            const alertBox = document.getElementById('alertBox');
            const alertMessage = document.getElementById('alertMessage');
            const alertIcon = document.getElementById('alertIcon');
            const loginContainer = document.getElementById('loginContainer');

            // Toggle password visibility
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                if (type === 'text') {
                    eyeIcon.className = 'bi bi-eye-slash-fill';
                } else {
                    eyeIcon.className = 'bi bi-eye-fill';
                }
            });

            // Show alert
            function showAlert(message, type) {
                alertBox.className = 'alert-custom show alert-' + (type === 'error' ? 'danger' : 'success');
                alertMessage.textContent = message;

                if (type === 'error') {
                    alertIcon.className = 'bi bi-exclamation-circle-fill';
                    alertIcon.style.fontSize = '1.1rem';
                } else {
                    alertIcon.className = 'bi bi-check-circle-fill';
                    alertIcon.style.fontSize = '1.1rem';
                }
            }

            // Hide alert
            function hideAlert() {
                alertBox.className = 'alert-custom';
            }

            // Handle form submit
            loginForm.addEventListener('submit', function (e) {
                e.preventDefault();
                hideAlert();

                const username = usernameInput.value.trim();
                const password = passwordInput.value.trim();

                // Validation
                if (!username || !password) {
                    showAlert('Username dan password harus diisi!', 'error');
                    loginContainer.classList.add('shake');
                    setTimeout(() => loginContainer.classList.remove('shake'), 500);
                    return;
                }

                // Loading state
                btnLogin.classList.add('loading');
                btnLogin.disabled = true;

                // Send AJAX request to securely process login
                fetch('/public/process_login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username: username, password: password })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showAlert(data.message, 'success');
                            setTimeout(function () {
                                window.location.href = '/public/index.php';
                            }, 1500);
                        } else {
                            showAlert(data.message, 'error');
                            loginContainer.classList.add('shake');
                            setTimeout(() => loginContainer.classList.remove('shake'), 500);
                            btnLogin.classList.remove('loading');
                            btnLogin.disabled = false;
                        }
                    })
                    .catch(error => {
                        showAlert('Terjadi kesalahan pada server saat proses login.', 'error');
                        btnLogin.classList.remove('loading');
                        btnLogin.disabled = false;
                    });
            });

            // Create additional sparkles dynamically
            function createSparkles() {
                const decoration = document.querySelector('.bg-decoration');
                for (let i = 0; i < 5; i++) {
                    const sparkle = document.createElement('div');
                    sparkle.className = 'sparkle';
                    sparkle.style.top = Math.random() * 100 + '%';
                    sparkle.style.left = Math.random() * 100 + '%';
                    sparkle.style.animationDelay = (Math.random() * 3) + 's';
                    sparkle.style.animationDuration = (2 + Math.random() * 3) + 's';
                    decoration.appendChild(sparkle);
                }
            }
            createSparkles();
        });
    </script>
</body>

</html>