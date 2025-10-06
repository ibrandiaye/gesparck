<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email envoyé - Gestion de Flotte</title>
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
        .confirmation-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
            max-width: 500px;
            width: 100%;
            text-align: center;
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
        .confirmation-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
        }
        .confirmation-header i {
            font-size: 4rem;
            margin-bottom: 1rem;
            display: block;
        }
        .confirmation-body {
            padding: 2rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="confirmation-container">
                    <div class="confirmation-header">
                        <i class="fas fa-paper-plane"></i>
                        <h2 class="mb-2">Email envoyé !</h2>
                    </div>
                    <div class="confirmation-body">
                        <div class="mb-4">
                            <h4 class="text-success mb-3">
                                <i class="fas fa-check-circle me-2"></i>
                                Vérifiez votre boîte email
                            </h4>
                            <p class="text-muted">
                                Un lien de réinitialisation de mot de passe a été envoyé à votre adresse email.
                                Veuillez vérifier votre boîte de réception et suivre les instructions.
                            </p>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Conseil :</strong> Si vous ne voyez pas l'email, vérifiez votre dossier de spam.
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i>Retour à la connexion
                            </a>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted">
                                Vous n'avez pas reçu l'email ?
                                <a href="{{ route('password.request') }}" class="text-decoration-none">Renvoyer</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
