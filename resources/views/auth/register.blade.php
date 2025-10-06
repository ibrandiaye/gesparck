<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Gestion de Flotte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .register-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .register-header i {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }
        .register-body {
            padding: 2rem;
        }
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .floating-label {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .floating-label label {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            color: #6c757d;
            transition: all 0.3s ease;
            pointer-events: none;
            background: white;
            padding: 0 0.5rem;
        }
        .floating-label .form-control:focus ~ label,
        .floating-label .form-control:not(:placeholder-shown) ~ label {
            top: 0;
            font-size: 0.8rem;
            color: #667eea;
        }
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
        }
        .strength-meter {
            height: 5px;
            border-radius: 5px;
            margin-top: 5px;
            background: #e9ecef;
            overflow: hidden;
        }
        .strength-meter::after {
            content: '';
            display: block;
            height: 100%;
            width: 0;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .strength-weak::after {
            width: 33%;
            background: #dc3545;
        }
        .strength-medium::after {
            width: 66%;
            background: #ffc107;
        }
        .strength-strong::after {
            width: 100%;
            background: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="register-container">
                    <div class="register-header">
                        <i class="fas fa-user-plus"></i>
                        <h2 class="mb-2">Créer un compte</h2>
                        <p class="mb-0">Rejoignez Gestion de Flotte</p>
                    </div>
                    <div class="register-body">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Nom -->
                            <div class="floating-label">
                                <input id="name" type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       name="name" value="{{ old('name') }}"
                                       placeholder=" "
                                       required autocomplete="name" autofocus>
                                <label for="name">
                                    <i class="fas fa-user me-1"></i>Nom complet
                                </label>
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="floating-label">
                                <input id="email" type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}"
                                       placeholder=" "
                                       required autocomplete="email">
                                <label for="email">
                                    <i class="fas fa-envelope me-1"></i>Adresse Email
                                </label>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Mot de passe -->
                            <div class="floating-label position-relative">
                                <input id="password" type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password"
                                       placeholder=" "
                                       required autocomplete="new-password"
                                       oninput="checkPasswordStrength(this.value)">
                                <label for="password">
                                    <i class="fas fa-lock me-1"></i>Mot de passe
                                </label>
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <div id="passwordStrength" class="strength-meter"></div>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Confirmation mot de passe -->
                            <div class="floating-label position-relative">
                                <input id="password_confirmation" type="password"
                                       class="form-control"
                                       name="password_confirmation"
                                       placeholder=" "
                                       required autocomplete="new-password">
                                <label for="password_confirmation">
                                    <i class="fas fa-lock me-1"></i>Confirmer le mot de passe
                                </label>
                                <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>

                            <!-- Conditions -->
                            <div class="mb-3 form-check">
                                <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    J'accepte les <a href="#" class="text-decoration-none">conditions d'utilisation</a>
                                </label>
                            </div>

                            <!-- Bouton d'inscription -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-register">
                                    <i class="fas fa-user-plus me-2"></i>Créer mon compte
                                </button>
                            </div>

                            <!-- Lien vers connexion -->
                            <div class="text-center mt-3">
                                <p class="mb-0">
                                    Déjà un compte ?
                                    <a href="{{ route('login') }}" class="text-decoration-none">Se connecter</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleIcon = passwordInput.parentElement.querySelector('.password-toggle i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        function checkPasswordStrength(password) {
            const strengthMeter = document.getElementById('passwordStrength');
            let strength = 0;

            // Reset
            strengthMeter.className = 'strength-meter';

            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/\d/)) strength++;
            if (password.match(/[^a-zA-Z\d]/)) strength++;

            if (password.length > 0) {
                if (strength <= 1) {
                    strengthMeter.classList.add('strength-weak');
                } else if (strength <= 2) {
                    strengthMeter.classList.add('strength-medium');
                } else {
                    strengthMeter.classList.add('strength-strong');
                }
            }
        }

        // Animation au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.parentElement.classList.remove('focused');
                    }
                });
            });
        });
    </script>
</body>
</html>
