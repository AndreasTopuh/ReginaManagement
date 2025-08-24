    </main>
    
    <?php if (isset($_SESSION['user_id'])): ?>
    <!-- <footer class="bg-dark text-light text-center py-3 mt-5">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Regina Hotel Management System. All rights reserved.</p>
        </div>
    </footer> -->
    <?php endif; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= ASSETS_URL ?>/js/app.js"></script>
</body>
</html>
