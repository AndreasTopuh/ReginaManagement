<?php
$title = "Login - Regina Hotel";
include INCLUDES_PATH . '/header.php';
?>

<div class="login-container">
    <div class="login-row">
        <!-- Left Side - Login Form -->
        <div class="login-left">
            <div class="login-form-wrapper">
                <div class="login-header">
                    <h3>Welcome to Regina Management Sistem</h3>
                    <p>Sign into your account</p>
                </div>

                <form method="POST" class="login-form">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username"
                            placeholder="Enter your username" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-input">
                            <input type="password" id="password" name="password"
                                placeholder="Enter your password" required>
                            <button type="button" class="password-toggle">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="login-btn">Log In</button>
                </form>

                <div class="demo-accounts">
                    <strong>Demo Accounts:</strong><br>
                    <span>Owner: owner / admin123</span><br>
                    <span>Admin: admin / admin123</span><br>
                    <span>Receptionist: receptionist / admin123</span>
                </div>
            </div>
        </div>

        <!-- Center Divider -->
        <div class="login-divider"></div>

        <!-- Right Side - Image Gallery -->
        <div class="login-right">
            <div class="image-gallery">


                <div class="gallery-grid">
                    <div class="gallery-item">
                        <img src="images/picture1.jpeg" alt="Hotel Lobby" class="gallery-img">
                    </div>
                    <div class="gallery-item">
                        <img src="images/picture2.jpeg" alt="Hotel Exterior" class="gallery-img">
                    </div>
                    <div class="gallery-item">
                        <img src="images/picture3.jpg" alt="Hotel Room" class="gallery-img">
                    </div>
                    <div class="gallery-item">
                        <img src="images/picture4.jpg" alt="Hotel Pool" class="gallery-img">
                    </div>
                    <div class="gallery-item">
                        <img src="images/picture5.jpg" alt="Hotel Restaurant" class="gallery-img">
                    </div>
                    <div class="gallery-item">
                        <img src="images/picutre6.jpg" alt="Hotel Pool Area" class="gallery-img">
                    </div>
                </div>

                <div class="welcome-text">
                    <h2>Welcome to Regina Hotel</h2>
                    <p>Log in to manage your bookings, access exclusive offers, and enjoy a personalized experience every time you stay with us.</p>
                </div>

                <!-- <div class="gallery-dots">
                    <span class="dot active"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </div> -->
            </div>
        </div>
    </div>

    <script>
        // Password toggle functionality
        document.querySelector('.password-toggle').addEventListener('click', function() {
            const passwordInput = document.querySelector('#password');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        });

        // Gallery dots functionality
        document.addEventListener('DOMContentLoaded', function() {
            const dots = document.querySelectorAll('.dot');
            const images = [
                'picture1.jpeg',
                'picture2.jpeg',
                'picture3.jpg',
                'picture4.jpg'
            ];

            dots.forEach((dot, index) => {
                dot.addEventListener('click', function() {
                    // Remove active class from all dots
                    dots.forEach(d => d.classList.remove('active'));
                    // Add active class to clicked dot
                    this.classList.add('active');

                    // Optional: Change main image based on dot clicked
                    const mainImg = document.querySelector('.gallery-item.large img');
                    if (mainImg && images[index]) {
                        mainImg.src = 'images/' + images[index];
                    }
                });
            });
        });
    </script>

    <?php include INCLUDES_PATH . '/footer.php'; ?>