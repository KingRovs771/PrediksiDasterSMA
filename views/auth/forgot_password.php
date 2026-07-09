<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Tivayo Collection</title>
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

        @keyframes floatUp {
            0%, 100% {
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
            0%, 100% {
                opacity: 0;
                transform: scale(0);
            }
            50% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .forgot-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 480px;
            border-radius: 1.5rem;
            background: white;
            padding: 3rem 2.5rem;
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

        .brand-icon-box {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 50%, #a78bfa 100%);
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 25px rgba(124, 58, 237, 0.3);
        }

        .brand-icon-box i {
            font-size: 2.5rem;
            color: white;
        }

        .forgot-card h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1a1a2e;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .forgot-card .subtitle {
            font-size: 0.85rem;
            color: #6b7280;
            text-align: center;
            margin-bottom: 2rem;
            line-height: 1.5;
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

        .btn-reset {
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
            margin-top: 1rem;
        }

        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(124, 58, 237, 0.4);
        }

        .btn-reset.loading .btn-text {
            display: none;
        }

        .btn-reset .spinner-border-custom {
            display: none;
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            margin: 0 auto;
        }

        .btn-reset.loading .spinner-border-custom {
            display: inline-block;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
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
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .shake {
            animation: shake 0.5s ease;
        }

        .back-link {
            display: block;
            text-align: center;
            font-size: 0.85rem;
            color: #8b5cf6;
            text-decoration: none;
            font-weight: 500;
            margin-top: 1.5rem;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #7c3aed;
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <!-- Background Decoration -->
    <div class="bg-decoration">
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
        <div class="sparkle" style="top:15%; left:25%; animation-delay:0s;"></div>
        <div class="sparkle" style="top:75%; left:80%; animation-delay:0.5s;"></div>
        <div class="sparkle" style="top:45%; left:15%; animation-delay:1s;"></div>
        <div class="sparkle" style="top:10%; left:70%; animation-delay:2s;"></div>
    </div>

    <!-- Forgot Card -->
    <div class="forgot-card shadow" id="forgotContainer">
        <div class="brand-icon-box">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <h1>Lupa Password?</h1>
        <p class="subtitle">Masukkan data akun Anda yang terdaftar untuk menyetel ulang password secara langsung.</p>

        <!-- Alert Box -->
        <div class="alert alert-custom" id="alertBox" role="alert">
            <i class="bi bi-exclamation-circle-fill" id="alertIcon" style="font-size: 1.1rem;"></i>
            <span id="alertMessage"></span>
        </div>

        <!-- Forgot Form -->
        <form id="forgotForm" autocomplete="off">
            <div class="form-floating-custom">
                <label for="username">Username</label>
                <div class="input-group-custom">
                    <input type="text" class="form-control" id="username" name="username"
                        placeholder="Masukkan username Anda" required>
                    <i class="bi bi-person-fill input-icon"></i>
                </div>
            </div>

            <div class="form-floating-custom">
                <label for="email">Email Terdaftar</label>
                <div class="input-group-custom">
                    <input type="email" class="form-control" id="email" name="email"
                        placeholder="Masukkan email terdaftar" required>
                    <i class="bi bi-envelope-fill input-icon"></i>
                </div>
            </div>

            <div class="form-floating-custom">
                <label for="new_password">Password Baru</label>
                <div class="input-group-custom">
                    <input type="password" class="form-control" id="new_password" name="new_password"
                        placeholder="Masukkan password baru" required>
                    <i class="bi bi-key-fill input-icon"></i>
                </div>
            </div>

            <div class="form-floating-custom">
                <label for="new_password_confirm">Konfirmasi Password Baru</label>
                <div class="input-group-custom">
                    <input type="password" class="form-control" id="new_password_confirm" name="new_password_confirm"
                        placeholder="Ulangi password baru" required>
                    <i class="bi bi-check-all input-icon"></i>
                </div>
            </div>

            <button type="submit" class="btn-reset" id="btnReset">
                <span class="btn-text">Reset Password</span>
                <span class="spinner-border-custom"></span>
            </button>
        </form>

        <a href="login.php" class="back-link">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Login
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const forgotForm = document.getElementById('forgotForm');
            const usernameInput = document.getElementById('username');
            const emailInput = document.getElementById('email');
            const newPasswordInput = document.getElementById('new_password');
            const newPasswordConfirmInput = document.getElementById('new_password_confirm');
            const btnReset = document.getElementById('btnReset');
            const alertBox = document.getElementById('alertBox');
            const alertMessage = document.getElementById('alertMessage');
            const alertIcon = document.getElementById('alertIcon');
            const forgotContainer = document.getElementById('forgotContainer');

            function showAlert(message, type) {
                alertBox.className = 'alert-custom show alert-' + (type === 'error' ? 'danger' : 'success');
                alertMessage.textContent = message;
                alertIcon.className = type === 'error' ? 'bi bi-exclamation-circle-fill' : 'bi bi-check-circle-fill';
            }

            function hideAlert() {
                alertBox.className = 'alert-custom';
            }

            forgotForm.addEventListener('submit', function (e) {
                e.preventDefault();
                hideAlert();

                const username = usernameInput.value.trim();
                const email = emailInput.value.trim();
                const newPassword = newPasswordInput.value.trim();
                const newPasswordConfirm = newPasswordConfirmInput.value.trim();

                // Validasi Client
                if (newPassword !== newPasswordConfirm) {
                    showAlert('Password baru dan konfirmasi tidak cocok!', 'error');
                    forgotContainer.classList.add('shake');
                    setTimeout(() => forgotContainer.classList.remove('shake'), 500);
                    return;
                }

                if (newPassword.length < 5) {
                    showAlert('Password baru minimal harus 5 karakter!', 'error');
                    forgotContainer.classList.add('shake');
                    setTimeout(() => forgotContainer.classList.remove('shake'), 500);
                    return;
                }

                // Loading state
                btnReset.classList.add('loading');
                btnReset.disabled = true;

                // Send AJAX request
                fetch('../../public/process_forgot_password.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        username: username,
                        email: email,
                        new_password: newPassword
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showAlert(data.message, 'success');
                        setTimeout(function () {
                            window.location.href = 'login.php';
                        }, 2000);
                    } else {
                        showAlert(data.message, 'error');
                        forgotContainer.classList.add('shake');
                        setTimeout(() => forgotContainer.classList.remove('shake'), 500);
                        btnReset.classList.remove('loading');
                        btnReset.disabled = false;
                    }
                })
                .catch(error => {
                    showAlert('Terjadi kesalahan pada server saat mereset password.', 'error');
                    btnReset.classList.remove('loading');
                    btnReset.disabled = false;
                });
            });
        });
    </script>
</body>

</html>
