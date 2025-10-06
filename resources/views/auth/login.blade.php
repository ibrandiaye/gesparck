<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion de Flotte</title>
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
        .login-container {
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
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-header i {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }
        .login-body {
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
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .login-footer {
            text-align: center;
            padding: 1rem 2rem;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 0.5rem 0;
            color: #6c757d;
        }
        .feature-list li i {
            color: #28a745;
            margin-right: 0.5rem;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="login-container">
                    <div class="row g-0">
                        <!-- Section de connexion -->
                        <div class="col-lg-6">
                            <div class="login-header">
                                <i class="fas fa-car"></i>
                                <h2 class="mb-2">Gestion de Flotte</h2>
                                <p class="mb-0">Connectez-vous à votre espace</p>
                            </div>
                            <div class="login-body">
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <!-- Email -->
                                    <div class="floating-label">
                                        <input id="email" type="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               name="email" value="{{ old('email') }}"
                                               placeholder=" "
                                               required autocomplete="email" autofocus>
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
                                               required autocomplete="current-password">
                                        <label for="password">
                                            <i class="fas fa-lock me-1"></i>Mot de passe
                                        </label>
                                        <button type="button" class="password-toggle" onclick="togglePassword()">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @error('password')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Remember Me -->
                                    <div class="mb-3 form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">
                                            Se souvenir de moi
                                        </label>
                                    </div>

                                    <!-- Bouton de connexion -->
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-login">
                                            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                                        </button>
                                    </div>

                                    <!-- Liens supplémentaires -->
                                    @if (Route::has('password.request'))
                                    <div class="text-center mt-3">
                                        <a href="{{ route('password.request') }}" class="text-decoration-none">
                                            Mot de passe oublié ?
                                        </a>
                                    </div>
                                    @endif
                                </form>
                            </div>
                        </div>

                        <!-- Section présentation -->
                        <div class="col-lg-6 d-none d-lg-block">
                            <div class="h-100 d-flex align-items-center justify-content-center p-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                <div class="text-center">
                                    <i class="fas fa-chart-line fa-4x text-primary mb-4"></i>
                                    <h4 class="mb-3">Gestion Complète de Votre Flotte</h4>
                                    <ul class="feature-list text-start">
                                        <li><i class="fas fa-check-circle"></i> Suivi en temps réel</li>
                                        <li><i class="fas fa-check-circle"></i> Analyse de consommation</li>
                                        <li><i class="fas fa-check-circle"></i> Gestion des entretiens</li>
                                        <li><i class="fas fa-check-circle"></i> Rapports détaillés</li>
                                        <li><i class="fas fa-check-circle"></i> Alertes automatiques</li>
                                    </ul>
                                    <div class="mt-4">
                                        <small class="text-muted">
                                            <i class="fas fa-shield-alt me-1"></i>
                                            Connexion sécurisée SSL
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="login-footer">
                        <small class="text-muted">
                            &copy; {{ date('Y') }} Gestion de Flotte - Tous droits réservés
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle i');

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

        // Gestion des erreurs avec animation
        @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            const errorElements = document.querySelectorAll('.is-invalid');
            errorElements.forEach(element => {
                element.addEventListener('input', function() {
                    if (this.value) {
                        this.classList.remove('is-invalid');
                    }
                });
            });
        });
        @endif
    </script>
</body>
</html>
