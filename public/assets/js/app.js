// Regina Hotel Management System - JavaScript

document.addEventListener('DOMContentLoaded', function() {
    
    // Sidebar toggle functionality - Simplified and reliable
    function initSidebarToggle() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        
        console.log('Sidebar elements check:', {
            toggle: !!sidebarToggle,
            sidebar: !!sidebar,
            mainContent: !!mainContent,
            backdrop: !!sidebarBackdrop
        });
        
        if (sidebarToggle && sidebar && mainContent) {
            console.log('All sidebar elements found, setting up toggle...');
            
            // Remove preload class
            document.documentElement.classList.remove('sidebar-preload-collapsed');
            
            // Load saved state
            const savedState = localStorage.getItem('sidebarCollapsed') === 'true';
            if (savedState) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                console.log('Loaded collapsed state');
            }
            
            // Clear any existing onclick handlers
            sidebarToggle.onclick = null;
            
            // Add click event listener with simple approach
            sidebarToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Toggle button clicked!');
                
                // Simple toggle logic
                if (sidebar.classList.contains('collapsed')) {
                    // Show sidebar
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('expanded');
                    localStorage.setItem('sidebarCollapsed', 'false');
                    console.log('Sidebar shown');
                } else {
                    // Hide sidebar
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                    localStorage.setItem('sidebarCollapsed', 'true');
                    console.log('Sidebar hidden');
                }
            }, { passive: false });
            
            // Backdrop click handler for mobile
            if (sidebarBackdrop) {
                sidebarBackdrop.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    console.log('Backdrop clicked - closed sidebar');
                });
            }
            
            console.log('Sidebar toggle setup complete');
        } else {
            console.error('Missing sidebar elements:', {
                toggle: !!sidebarToggle,
                sidebar: !!sidebar,
                mainContent: !!mainContent
            });
            // Remove preload class anyway
            document.documentElement.classList.remove('sidebar-preload-collapsed');
        }
    }
    
    // Initialize immediately
    console.log('Starting sidebar initialization...');
    initSidebarToggle();
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
    
    // Room selection handler
    const roomCheckboxes = document.querySelectorAll('.room-checkbox');
    roomCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const card = this.closest('.room-card');
            if (this.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        });
    });
    
    // Date validation
    const checkinDate = document.getElementById('checkin_date');
    const checkoutDate = document.getElementById('checkout_date');
    
    if (checkinDate && checkoutDate) {
        checkinDate.addEventListener('change', function() {
            checkoutDate.min = this.value;
            if (checkoutDate.value <= this.value) {
                const nextDay = new Date(this.value);
                nextDay.setDate(nextDay.getDate() + 1);
                checkoutDate.value = nextDay.toISOString().split('T')[0];
            }
        });
        
        checkoutDate.addEventListener('change', function() {
            if (this.value <= checkinDate.value) {
                alert('Tanggal checkout harus setelah tanggal checkin');
                this.value = '';
            }
        });
    }
    
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
    
    // Confirmation dialogs
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            if (!confirm('Are you sure you want to delete this item?')) {
                event.preventDefault();
            }
        });
    });
    
    // Status change confirmations
    const statusButtons = document.querySelectorAll('.btn-status-change');
    statusButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            const action = this.dataset.action;
            let message = 'Are you sure?';
            
            switch(action) {
                case 'checkin':
                    message = 'Confirm check-in for this booking?';
                    break;
                case 'checkout':
                    message = 'Confirm check-out for this booking?';
                    break;
                case 'cancel':
                    message = 'Cancel this booking? This action cannot be undone.';
                    break;
            }
            
            if (!confirm(message)) {
                event.preventDefault();
            }
        });
    });
    
    // Search functionality with debounce
    const searchInput = document.getElementById('search');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                document.getElementById('search-form').submit();
            }, 500);
        });
    }
    
    // Auto-submit filter forms
    const filterSelects = document.querySelectorAll('.auto-submit');
    filterSelects.forEach(function(select) {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
    
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(function(tooltip) {
        new bootstrap.Tooltip(tooltip);
    });
    
    // Initialize popovers
    const popovers = document.querySelectorAll('[data-bs-toggle="popover"]');
    popovers.forEach(function(popover) {
        new bootstrap.Popover(popover);
    });
    
    // Room availability checker
    const checkAvailabilityBtn = document.getElementById('check-availability');
    if (checkAvailabilityBtn) {
        checkAvailabilityBtn.addEventListener('click', function() {
            const checkin = document.getElementById('checkin_date').value;
            const checkout = document.getElementById('checkout_date').value;
            
            if (!checkin || !checkout) {
                alert('Please select check-in and check-out dates first.');
                return;
            }
            
            if (new Date(checkout) <= new Date(checkin)) {
                alert('Check-out date must be after check-in date.');
                return;
            }
            
            // Add loading state
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Checking...';
            this.disabled = true;
            
            // Redirect to create booking page with dates
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('checkin_date', checkin);
            currentUrl.searchParams.set('checkout_date', checkout);
            window.location.href = currentUrl.toString();
        });
    }
    
    // Dynamic room price calculation
    function calculateTotalPrice() {
        const selectedRooms = document.querySelectorAll('.room-checkbox:checked');
        const checkinDate = document.getElementById('checkin_date');
        const checkoutDate = document.getElementById('checkout_date');
        
        if (!checkinDate || !checkoutDate || !checkinDate.value || !checkoutDate.value) {
            return;
        }
        
        const nights = Math.ceil(
            (new Date(checkoutDate.value) - new Date(checkinDate.value)) / (1000 * 60 * 60 * 24)
        );
        
        let totalRoomPrice = 0;
        selectedRooms.forEach(function(checkbox) {
            const price = parseFloat(checkbox.dataset.price || 0);
            totalRoomPrice += price * nights;
        });
        
        const tax = totalRoomPrice * 0.1; // 10% tax
        const service = totalRoomPrice * 0.05; // 5% service
        const grandTotal = totalRoomPrice + tax + service;
        
        // Update display
        const summaryElement = document.getElementById('booking-summary');
        if (summaryElement) {
            summaryElement.innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <h6>Booking Summary</h6>
                        <div class="d-flex justify-content-between">
                            <span>Room(s) × ${nights} night(s):</span>
                            <span>Rp ${totalRoomPrice.toLocaleString('id-ID')}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Tax (10%):</span>
                            <span>Rp ${tax.toLocaleString('id-ID')}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Service (5%):</span>
                            <span>Rp ${service.toLocaleString('id-ID')}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total:</span>
                            <span>Rp ${grandTotal.toLocaleString('id-ID')}</span>
                        </div>
                    </div>
                </div>
            `;
        }
    }
    
    // Attach price calculation to relevant events
    document.addEventListener('change', function(event) {
        if (event.target.classList.contains('room-checkbox') || 
            event.target.id === 'checkin_date' || 
            event.target.id === 'checkout_date') {
            calculateTotalPrice();
        }
    });
    
    // Initialize price calculation on page load
    calculateTotalPrice();
});
