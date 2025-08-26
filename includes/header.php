<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Regina Hotel Management System' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/css/style.css" rel="stylesheet">

    <!-- Prevent sidebar flicker by loading state early -->
    <script>
        // Check localStorage immediately to prevent flicker
        (function() {
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (sidebarCollapsed) {
                document.documentElement.classList.add('sidebar-preload-collapsed');
            }
        })();
    </script>

    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(180deg, #8b7355 0%, #6d5940 100%);
            z-index: 1000;
            transition: all 0.3s;
            box-shadow: 2px 0 10px rgba(139, 115, 85, 0.2);
        }

        .sidebar.collapsed {
            margin-left: -250px;
        }

        /* Preload collapsed state to prevent flicker */
        .sidebar-preload-collapsed .sidebar {
            margin-left: -250px !important;
            transition: none !important;
        }

        .sidebar-preload-collapsed .main-content {
            margin-left: 0 !important;
            transition: none !important;
        }

        .sidebar .brand {
            padding: 1.5rem;
            background: rgba(0, 0, 0, 0.1);
            color: white;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-item {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 1rem 1.5rem;
            border: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: #f8f9fa;
        }

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            background: #f8f9fa;
            transition: margin-left 0.3s;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        .top-header {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        #sidebarToggle {
            border: 2px solid #8b7355 !important;
            background: #8b7355 !important;
            color: white !important;
            font-size: 16px !important;
            padding: 10px 15px !important;
            border-radius: 6px !important;
            cursor: pointer !important;
            z-index: 9999 !important;
            position: relative !important;
            display: inline-block !important;
            margin-right: 15px !important;
            transition: all 0.3s ease !important;
        }

        #sidebarToggle:hover {
            background: #6d5940 !important;
            border-color: #6d5940 !important;
            color: white !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(139, 115, 85, 0.3) !important;
        }

        #sidebarToggle:focus {
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.3) !important;
        }

        #sidebarToggle:active {
            transform: translateY(0) scale(0.98) !important;
        }

        /* Booking Summary Sticky Position */
        .booking-summary-sticky {
            position: sticky;
            top: 90px;
            /* Height of the top header + some padding */
            z-index: 100;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #dee2e6;
        }

        .booking-summary-sticky .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
        }

        .content-area {
            padding: 2rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }

            .sidebar.show {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.expanded {
                margin-left: 0;
            }
        }

        /* Mobile backdrop */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        @media (max-width: 768px) {
            .sidebar.show~.sidebar-backdrop {
                display: block;
            }
        }
    </style>
</head>

<body>
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="brand">
                <i class="fas fa-hotel fa-2x"></i>
                <h5 class="mt-2 mb-0">Regina Hotel</h5>
            </div>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= $_SERVER['REQUEST_URI'] == '/reginahotel/public/dashboard' || $_SERVER['REQUEST_URI'] == '/reginahotel/public/' ? 'active' : '' ?>"
                        href="<?= BASE_URL ?>/dashboard">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/bookings') !== false ? 'active' : '' ?>"
                        href="<?= BASE_URL ?>/bookings">
                        <i class="fas fa-calendar-check me-2"></i> Bookings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/rooms') !== false ? 'active' : '' ?>"
                        href="<?= BASE_URL ?>/rooms">
                        <i class="fas fa-bed me-2"></i> Rooms
                    </a>
                </li>

                <?php if (hasPermission(['Owner', 'Admin'])): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/floors') !== false ? 'active' : '' ?>"
                            href="<?= BASE_URL ?>/floors">
                            <i class="fas fa-building me-2"></i> Floors
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/users') !== false ? 'active' : '' ?>"
                            href="<?= BASE_URL ?>/users">
                            <i class="fas fa-users me-2"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/revenue') !== false ? 'active' : '' ?>"
                            href="<?= BASE_URL ?>/revenue">
                            <i class="fas fa-chart-line me-2"></i> Revenue
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Sidebar Backdrop for Mobile -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Top Header -->
            <div class="top-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button type="button" id="sidebarToggle" title="Toggle Sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4 class="mb-0"><?= $title ?? 'Dashboard' ?></h4>
                </div>

                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle text-decoration-none" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle fa-lg me-2"></i>
                            <span><?= $_SESSION['name'] ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/profile">
                                    <i class="fas fa-user-cog me-2"></i> Profile
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/logout">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
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
            <?php else: ?>
                <div class="container-fluid">
                    <?php
                    // Display flash messages for non-logged users
                    $flash = getFlashMessage();
                    if ($flash):
                    ?>
                        <div class="alert alert-<?= $flash['type'] == 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($flash['message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <main>
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