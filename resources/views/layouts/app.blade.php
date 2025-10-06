<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Flotte - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand { font-weight: 600; }
        .card { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        .card-header { font-weight: 500; }
        .badge { font-size: 0.75em; }
        .navbar-nav .nav-link {
            border-radius: 0.375rem;
            margin: 0 0.125rem;
            transition: all 0.2s ease-in-out;
        }
        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }
        .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
                    <!-- Version mobile compacte -->
        <div class="d-lg-none">
            <div class="card">
                <div class="card-body py-2">
                    <div class="row text-center">
                        <div class="col-3">
                            <a href="{{ route('dashboard') }}" class="text-decoration-none">
                                <i class="fas fa-tachometer-alt fa-lg d-block mb-1"></i>
                                <small>Dashboard</small>
                            </a>
                        </div>
                        <div class="col-3">
                            <a href="{{ route('vehicles.index') }}" class="text-decoration-none">
                                <i class="fas fa-truck fa-lg d-block mb-1"></i>
                                <small>Véhicules</small>
                            </a>
                        </div>
                        <div class="col-3">
                            <a href="{{ route('fuel-entries.index') }}" class="text-decoration-none">
                                <i class="fas fa-gas-pump fa-lg d-block mb-1"></i>
                                <small>Carburant</small>
                            </a>
                        </div>
                        <div class="col-3">
                            <a href="{{ route('repair-logs.index') }}" class="text-decoration-none">
                                <i class="fas fa-tools fa-lg d-block mb-1"></i>
                                <small>Dépannages</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-car me-2"></i>Gestion de Flotte
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                           href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>

                    <!-- Véhicules -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}"
                           href="{{ route('vehicles.index') }}">
                            <i class="fas fa-truck me-1"></i>Véhicules
                        </a>
                    </li>

                    <!-- Carburant -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('fuel-entries.*') ? 'active' : '' }}"
                           href="{{ route('fuel-entries.index') }}">
                            <i class="fas fa-gas-pump me-1"></i>Carburant
                        </a>
                    </li>

                    <!-- Dépannages -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('repair-logs.*') ? 'active' : '' }}"
                           href="{{ route('repair-logs.index') }}">
                            <i class="fas fa-tools me-1"></i>Dépannages
                        </a>
                    </li>

                    <!-- Menu déroulant Rapports -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                           href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-chart-bar me-1"></i>Rapports
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-gas-pump me-2"></i>Rapport Carburant
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-tools me-2"></i>Rapport Entretien
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-file-export me-2"></i>Export Données
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>

                <!-- Menu utilisateur -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user-cog me-2"></i>Mon Profil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cog me-2"></i>Paramètres
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <footer class="bg-light mt-5 py-4">
        <div class="container text-center text-muted">
            <small>
                <i class="fas fa-car"></i> Gestion de Flotte &copy; {{ date('Y') }}
                - Développé avec Laravel & Bootstrap
            </small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
