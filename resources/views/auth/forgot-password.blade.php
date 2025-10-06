<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - Gestion de Flotte</title>
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
        .password-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
            max-width: 450px;
            width: 100%;
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
        .password-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .password-header i {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }
        .password-body {
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
        .btn-password {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-password:hover {
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
        .instructions {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #667eea;
        }
        .back-link {
            color: #667eea;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .back-link:hover {
            color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="password-container">
                    <div class="password-header">
                        <i class="fas fa-key"></i>
                        <h2 class="mb-2">Mot de passe oublié</h2>
                        <p class="mb-0">Réinitialisez votre mot de passe</p>
                    </div>
                    <div class="password-body">
                        <!-- Instructions -->
                        <div class="instructions">
                            <p class="mb-0 small">
                                <i class="fas fa-info-circle me-1"></i>
                                Saisissez votre adresse email. Vous recevrez un lien pour réinitialiser votre mot de passe.
                            </p>
                        </div>

                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <!-- Email Address -->
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
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-password">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer le lien de réinitialisation
                                </button>
                            </div>

                            <!-- Back to Login -->
                            <div class="text-center">
                                <a href="{{ route('login') }}" class="back-link">
                                    <i class="fas fa-arrow-left me-1"></i>Retour à la connexion
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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

            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.classList.remove('show');
                        setTimeout(() => {
                            if (alert.parentNode) {
                                alert.remove();
                            }
                        }, 150);
                    }
                }, 5000);
            });
        });
    </script>
</body>
</html>
