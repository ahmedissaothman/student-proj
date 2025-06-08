<?php
// dashboard/components/header.php
// Fixed Header Component with Smart Path Detection

// Determine the current path depth and set base URL accordingly
$current_file = $_SERVER['PHP_SELF'];

// Set base URL based on current location
if (strpos($current_file, '/modules/') !== false) {
    // We're in modules directory, go up to root
    $header_base_url = '../../';
} else if (strpos($current_file, '/dashboard/') !== false) {
    // We're in dashboard directory, go up to root
    $header_base_url = '../';
} else {
    // We're in root or other directory
    $header_base_url = './';
}
?>
<header class="main-header">
    <!-- Header Left -->
    <div class="header-left">
        <!-- Mobile Menu Toggle -->
        <button class="menu-toggle" onclick="toggleMobileSidebar()">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Logo & System Info -->
        <div class="logo-section">
            <div class="logo">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="system-info">
                <h1>MSICT</h1>
                <p>Ordering System</p>
            </div>
        </div>
    </div>

    <!-- Header Right -->
    <div class="header-right">
        <!-- Search Bar (Desktop only) -->
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Search requests, users..." onkeypress="handleSearch(event)">
            <i class="fas fa-search search-icon"></i>
        </div>

        <!-- Status Indicators -->
        <div class="status-indicators">
            <div class="status-item">
                <div class="status-dot online"></div>
                <span>Online</span>
            </div>
        </div>

        <!-- Notifications -->
        <div class="notifications">
            <button class="notification-bell" onclick="toggleNotifications()">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">3</span>
            </button>

            <div class="notification-dropdown" id="notificationDropdown">
                <div class="notification-header">
                    <i class="fas fa-bell"></i> Notifications (3)
                </div>
                
                <div class="notification-item unread">
                    <div class="notification-content">
                        <div class="notification-icon success">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="notification-text">
                            <div class="notification-title">Request Approved</div>
                            <div class="notification-message">Your office supplies request #REQ-001 has been approved</div>
                            <div class="notification-time">5 minutes ago</div>
                        </div>
                    </div>
                </div>

                <div class="notification-item unread">
                    <div class="notification-content">
                        <div class="notification-icon warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="notification-text">
                            <div class="notification-title">Pending Approval</div>
                            <div class="notification-message">Request #REQ-002 requires your approval</div>
                            <div class="notification-time">1 hour ago</div>
                        </div>
                    </div>
                </div>

                <div class="notification-item">
                    <div class="notification-content">
                        <div class="notification-icon info">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="notification-text">
                            <div class="notification-title">System Update</div>
                            <div class="notification-message">System will be updated tonight at 2:00 AM</div>
                            <div class="notification-time">2 hours ago</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Menu -->
        <div class="user-menu" onclick="toggleUserMenu()">
            <div class="user-info">
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['username'] ?? 'PA', 0, 2)); ?></div>
                <div class="user-details">
                    <div class="user-name"><?php echo $_SESSION['full_name'] ?? 'Pte. Athuman'; ?></div>
                    <div class="user-role"><?php echo ucfirst($_SESSION['user_role'] ?? 'Clerk'); ?></div>
                </div>
                <i class="fas fa-chevron-down dropdown-arrow"></i>
            </div>

            <div class="user-dropdown" id="userDropdown">
                <div class="dropdown-header">
                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['username'] ?? 'PA', 0, 2)); ?></div>
                    <div class="user-name"><?php echo $_SESSION['full_name'] ?? 'Pte. Athuman JI'; ?></div>
                    <div class="user-role">Department <?php echo ucfirst($_SESSION['user_role'] ?? 'Clerk'); ?></div>
                </div>

                <div class="dropdown-menu">
                    <a href="<?php echo $header_base_url; ?>profile.php" class="dropdown-item">
                        <i class="fas fa-user"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="<?php echo $header_base_url; ?>settings.php" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    <a href="<?php echo $header_base_url; ?>notifications.php" class="dropdown-item">
                        <i class="fas fa-bell"></i>
                        <span>Notifications</span>
                    </a>
                    <a href="<?php echo $header_base_url; ?>help.php" class="dropdown-item">
                        <i class="fas fa-question-circle"></i>
                        <span>Help & Support</span>
                    </a>
                    
                    <div class="dropdown-divider"></div>
                    
                    <button class="dropdown-item logout-item" onclick="logout()">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Hidden logout URL for JavaScript -->
<script>
const LOGOUT_URL = '<?php echo $header_base_url; ?>auth/logout.php';
</script>

<script>
// Header JavaScript Functions
function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    const userDropdown = document.getElementById('userDropdown');
    
    // Close user dropdown if open
    userDropdown.classList.remove('show');
    document.querySelector('.user-menu').classList.remove('active');
    
    // Toggle notifications
    dropdown.classList.toggle('show');
}

function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const userMenu = document.querySelector('.user-menu');
    
    // Close notification dropdown if open
    notificationDropdown.classList.remove('show');
    
    // Toggle user menu
    dropdown.classList.toggle('show');
    userMenu.classList.toggle('active');
}

function handleSearch(event) {
    if (event.key === 'Enter') {
        const query = event.target.value.trim();
        if (query) {
            console.log('Searching for:', query);
            // Add your search functionality here
            alert('Searching for: ' + query);
        }
    }
}

// FIXED LOGOUT FUNCTION with Smart Path Detection
function logout() {
    if (confirm('🔐 Are you sure you want to logout?\n\nThis will end your current session and redirect you to the login page.')) {
        // Show loading state
        const logoutBtn = document.querySelector('.logout-item');
        if (logoutBtn) {
            logoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Logging out...</span>';
            logoutBtn.disabled = true;
        }
        
        // Clear browser storage
        try {
            localStorage.clear();
            sessionStorage.clear();
            
            // Clear any cached data
            if ('caches' in window) {
                caches.keys().then(names => {
                    names.forEach(name => {
                        caches.delete(name);
                    });
                });
            }
        } catch (e) {
            console.log('Storage clear error:', e);
        }
        
        // Show logout message
        showLogoutAlert();
        
        // Use the correct logout URL based on current location
        setTimeout(() => {
            console.log('🔄 Redirecting to logout:', LOGOUT_URL);
            
            // Try multiple logout strategies
            try {
                // First try: Use the detected logout URL
                window.location.replace(LOGOUT_URL);
            } catch (e) {
                console.log('Primary logout failed, trying alternatives...');
                
                // Fallback strategies
                const alternativeUrls = [
                    '../../auth/logout.php',  // For modules
                    '../auth/logout.php',     // For dashboard
                    './auth/logout.php',      // For root
                    '/auth/logout.php',       // Absolute
                    'logout.php'              // Same directory
                ];
                
                // Try each alternative
                for (let i = 0; i < alternativeUrls.length; i++) {
                    setTimeout(() => {
                        try {
                            window.location.replace(alternativeUrls[i]);
                        } catch (e2) {
                            console.log(`Logout attempt ${i+1} failed:`, e2);
                            
                            // Last resort: Manual session destruction
                            if (i === alternativeUrls.length - 1) {
                                // Create a form to submit logout request
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = LOGOUT_URL || '../auth/logout.php';
                                
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'logout';
                                input.value = '1';
                                form.appendChild(input);
                                
                                document.body.appendChild(form);
                                form.submit();
                            }
                        }
                    }, i * 500);
                }
            }
        }, 1500);
    }
}

// Show logout alert
function showLogoutAlert() {
    const alert = document.createElement('div');
    alert.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: linear-gradient(135deg, #2D5016, #1e3c72);
        color: white;
        padding: 2rem 3rem;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        z-index: 9999;
        text-align: center;
        animation: fadeInScale 0.5s ease;
        min-width: 300px;
    `;
    
    alert.innerHTML = `
        <div style="margin-bottom: 1rem;">
            <i class="fas fa-sign-out-alt" style="font-size: 2rem; color: #FFD700;"></i>
        </div>
        <div style="font-size: 1.2rem; font-weight: 600; margin-bottom: 0.5rem;">
            Logging Out...
        </div>
        <div style="font-size: 0.9rem; opacity: 0.8;">
            Thank you for using MSICT System
        </div>
        <div style="margin-top: 1rem;">
            <div style="width: 200px; height: 4px; background: rgba(255,255,255,0.2); border-radius: 2px; margin: 0 auto; overflow: hidden;">
                <div style="width: 100%; height: 100%; background: #FFD700; border-radius: 2px; animation: loading 1.5s ease;"></div>
            </div>
        </div>
    `;
    
    document.body.appendChild(alert);
    
    // Add overlay
    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9998;
        backdrop-filter: blur(3px);
    `;
    document.body.appendChild(overlay);
}

// Alternative logout function if regular logout fails
function forceLogout() {
    // Clear all possible session data
    document.cookie.split(";").forEach(function(c) { 
        document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
    });
    
    // Clear storage
    localStorage.clear();
    sessionStorage.clear();
    
    // Redirect to login page
    const loginUrls = [
        '../../auth/login.php',
        '../auth/login.php', 
        './auth/login.php',
        '/auth/login.php',
        'login.php'
    ];
    
    // Try to redirect to login
    for (let url of loginUrls) {
        try {
            window.location.replace(url);
            break;
        } catch (e) {
            continue;
        }
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const notificationDropdown = document.getElementById('notificationDropdown');
    const userDropdown = document.getElementById('userDropdown');
    const userMenu = document.querySelector('.user-menu');
    
    // Check if click is outside notification area
    if (!event.target.closest('.notifications')) {
        notificationDropdown?.classList.remove('show');
    }
    
    // Check if click is outside user menu area
    if (!event.target.closest('.user-menu')) {
        userDropdown?.classList.remove('show');
        userMenu?.classList.remove('active');
    }
});

// Debug function to check current paths
function debugPaths() {
    console.log('🔍 Debug Info:');
    console.log('Current URL:', window.location.href);
    console.log('Current pathname:', window.location.pathname);
    console.log('Logout URL:', LOGOUT_URL);
    console.log('Base URL from PHP:', '<?php echo $header_base_url; ?>');
}

// Add logout animations CSS
const logoutStyles = document.createElement('style');
logoutStyles.textContent = `
    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: translate(-50%, -50%) scale(0.8);
        }
        to {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
    }
    
    @keyframes loading {
        from {
            transform: translateX(-100%);
        }
        to {
            transform: translateX(0);
        }
    }
`;
document.head.appendChild(logoutStyles);

// Initialize header
document.addEventListener('DOMContentLoaded', function() {
    console.log('📋 Header initialized with logout URL:', LOGOUT_URL);
    
    // Optional: Add keyboard shortcut for logout (Ctrl+Shift+L)
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.shiftKey && e.key === 'L') {
            e.preventDefault();
            logout();
        }
    });
});
</script>