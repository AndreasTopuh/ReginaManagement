<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Regina Hotel Management System' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/css/style.css" rel="stylesheet">
</head>

<body>

    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?= BASE_URL ?>/dashboard">
                    <i class="fas fa-hotel"></i> Regina Hotel
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?= $_SERVER['REQUEST_URI'] == '/reginahotel/public/dashboard' || $_SERVER['REQUEST_URI'] == '/reginahotel/public/' ? 'active' : '' ?>"
                                href="<?= BASE_URL ?>/dashboard">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/bookings') !== false ? 'active' : '' ?>"
                                href="<?= BASE_URL ?>/bookings">
                                <i class="fas fa-calendar-check"></i> Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/rooms') !== false ? 'active' : '' ?>"
                                href="<?= BASE_URL ?>/rooms">
                                <i class="fas fa-bed"></i> Rooms
                            </a>
                        </li>

                        <?php if (hasPermission(['Owner', 'Admin'])): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/floors') !== false ? 'active' : '' ?>"
                                    href="<?= BASE_URL ?>/floors">
                                    <i class="fas fa-building"></i> Floors
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/users') !== false ? 'active' : '' ?>"
                                    href="<?= BASE_URL ?>/users">
                                    <i class="fas fa-users"></i> Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/revenue') !== false ? 'active' : '' ?>"
                                    href="<?= BASE_URL ?>/revenue">
                                    <i class="fas fa-chart-line"></i> Revenue
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?= $_SESSION['name'] ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/profile">
                                        <i class="fas fa-user-cog"></i> Profile
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/logout">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <main class="<?= isset($_SESSION['user_id']) ? 'container-fluid mt-4' : '' ?>">
        <?php
        // Display flash messages
        $flash = getFlashMessage();
        if ($flash):
        ?>
            <div class="alert alert-<?= $flash['type'] == 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>