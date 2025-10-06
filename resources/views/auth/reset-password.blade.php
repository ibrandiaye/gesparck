<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation - Gestion de Flotte</title>
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
        .reset-container {
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
        .reset-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .reset-header i {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }
        .reset-body {
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
        .btn-reset {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-reset:hover {
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
        .requirements {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #28a745;
        }
        .requirement-list {
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 0.875rem;
        }
        .requirement-list li {
            padding: 0.25rem 0;
            color: #6c757d;
        }
        .requirement-list li.valid {
            color: #28a745;
        }
        .requirement-list li.valid::before {
            content: '✓ ';
            font-weight: bold;
        }
        .requirement-list li.invalid::before {
            content: '✗ ';
            font-weight: bold;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="reset-container">
                    <div class="reset-header">
                        <i class="fas fa-lock"></i>
                        <h2 class="mb-2">Nouveau mot de passe</h2>
                        <p class="mb-0">Créez votre nouveau mot de passe</p>
                    </div>
                    <div class="reset-body">
                        <!-- Exigences du mot de passe -->
                        <div class="requirements">
                            <h6 class="mb-2">Exigences de sécurité :</h6>
                            <ul class="requirement-list">
                                <li id="req-length" class="invalid">Minimum 8 caractères</li>
                                <li id="req-lowercase" class="invalid">Une lettre minuscule</li>
                                <li id="req-uppercase" class="invalid">Une lettre majuscule</li>
                                <li id="req-number" class="invalid">Un chiffre</li>
                                <li id="req-special" class="invalid">Un caractère spécial</li>
                            </ul>
                        </div>

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <!-- Token Hidden -->
                            <input type="hidden" name="token" value="{{ $token }}">

                            <!-- Email Address Hidden -->
                            <input type="hidden" name="email" value="{{ $email }}">

                            <!-- Password -->
                            <div class="floating-label position-relative">
                                <input id="password" type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password"
                                       placeholder=" "
                                       required autocomplete="new-password"
                                       oninput="validatePassword(this.value)">
                                <label for="password">
                                    <i class="fas fa-lock me-1"></i>Nouveau mot de passe
                                </label>
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <div id="passwordStrength" class="strength-meter"></div>
                                @error('password')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="floating-label position-relative">
                                <input id="password_confirmation" type="password"
                                       class="form-control"
                                       name="password_confirmation"
                                       placeholder=" "
                                       required autocomplete="new-password"
                                       oninput="checkPasswordMatch()">
                                <label for="password_confirmation">
                                    <i class="fas fa-lock me-1"></i>Confirmer le mot de passe
                                </label>
                                <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <div id="passwordMatch" class="mt-1"></div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-reset" id="submitButton" disabled>
                                    <i class="fas fa-save me-2"></i>Réinitialiser le mot de passe
                                </button>
                            </div>

                            <!-- Back to Login -->
                            <div class="text-center">
                                <a href="{{ route('login') }}" class="text-decoration-none">
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

        function validatePassword(password) {
            const requirements = {
                length: password.length >= 8,
                lowercase: /[a-z]/.test(password),
                uppercase: /[A-Z]/.test(password),
                number: /\d/.test(password),
                special: /[^a-zA-Z\d]/.test(password)
            };

            // Update requirement list
            Object.keys(requirements).forEach(key => {
                const element = document.getElementById(`req-${key}`);
                if (requirements[key]) {
                    element.classList.remove('invalid');
                    element.classList.add('valid');
                } else {
                    element.classList.remove('valid');
                    element.classList.add('invalid');
                }
            });

            // Update strength meter
            const strengthMeter = document.getElementById('passwordStrength');
            let strength = Object.values(requirements).filter(Boolean).length;

            strengthMeter.className = 'strength-meter';
            if (password.length > 0) {
                if (strength <= 2) {
                    strengthMeter.classList.add('strength-weak');
                } else if (strength <= 4) {
                    strengthMeter.classList.add('strength-medium');
                } else {
                    strengthMeter.classList.add('strength-strong');
                }
            }

            checkPasswordMatch();
            updateSubmitButton();
        }

        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            const matchElement = document.getElementById('passwordMatch');

            if (confirmPassword.length === 0) {
                matchElement.innerHTML = '';
                return;
            }

            if (password === confirmPassword) {
                matchElement.innerHTML = '<small class="text-success"><i class="fas fa-check me-1"></i>Les mots de passe correspondent</small>';
            } else {
                matchElement.innerHTML = '<small class="text-danger"><i class="fas fa-times me-1"></i>Les mots de passe ne correspondent pas</small>';
            }

            updateSubmitButton();
        }

        function updateSubmitButton() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            const submitButton = document.getElementById('submitButton');
            const requirements = {
                length: password.length >= 8,
                lowercase: /[a-z]/.test(password),
                uppercase: /[A-Z]/.test(password),
                number: /\d/.test(password),
                special: /[^a-zA-Z\d]/.test(password)
            };

            const allRequirementsMet = Object.values(requirements).every(Boolean);
            const passwordsMatch = password === confirmPassword && password.length > 0;

            submitButton.disabled = !(allRequirementsMet && passwordsMatch);
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

            // Focus on password field if empty
            const passwordField = document.getElementById('password');
            if (passwordField && !passwordField.value) {
                passwordField.focus();
            }
        });
    </script>
</body>
</html>
