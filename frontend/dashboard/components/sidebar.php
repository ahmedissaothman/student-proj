<?php
// dashboard/components/sidebar.php  
// Smart Role-Based Sidebar Component - Clean & Attractive

// Determine the current path depth and set base URL accordingly
$current_file = $_SERVER['PHP_SELF'];

// Set base URL based on current location
if (strpos($current_file, '/modules/') !== false) {
    $base_url = '../../';
} else if (strpos($current_file, '/dashboard/') !== false) {
    $base_url = '../';
} else {
    $base_url = './';
}

// Get user role
$user_role = $_SESSION['user_role'] ?? 'Department';
?>

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- Smart Role-Based Sidebar -->
<aside class="sidebar" id="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <div class="sidebar-header-content">
            <div class="sidebar-logo">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="sidebar-title">
                <h2>MSICT</h2>
                <p><?php echo $user_role; ?> Panel</p>
            </div>
        </div>
        <button class="collapse-toggle" onclick="toggleSidebar()">
            <i class="fas fa-chevron-left" id="collapseIcon"></i>
        </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
        <!-- Dashboard - All Roles -->
        <div class="nav-section">
            <ul class="nav-menu">
                <li class="nav-item">
                    <?php 
                    $dashboard_url = $base_url . 'dashboard/';
                    switch($user_role) {
                        case 'Admin':
                            $dashboard_url .= 'admin-dashboard.php';
                            break;
                        case 'QuarterMaster':
                            $dashboard_url .= 'quartermaster-dashboard.php';
                            break;
                        case 'CO':
                            $dashboard_url .= 'co-dashboard.php';
                            break;
                        case 'Auditor':
                            $dashboard_url .= 'auditor-dashboard.php';
                            break;
                        default:
                            $dashboard_url .= 'department-dashboard.php';
                    }
                    ?>
                    <a href="<?php echo $dashboard_url; ?>" class="menu-item <?php echo (strpos(basename($_SERVER['PHP_SELF']), 'dashboard.php') !== false) ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt menu-icon"></i>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- ADMIN NAVIGATION -->
        <?php if ($user_role == 'Admin'): ?>
        <div class="nav-section">
            <div class="nav-section-title">Administration</div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/admin/user-management.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'user-management.php') ? 'active' : ''; ?>">
                        <i class="fas fa-users menu-icon"></i>
                        <span class="menu-text">User Management</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/admin/role-management.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'role-management.php') ? 'active' : ''; ?>">
                        <i class="fas fa-user-shield menu-icon"></i>
                        <span class="menu-text">Roles & Permissions</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/requests/all-requests.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'all-requests.php') ? 'active' : ''; ?>">
                        <i class="fas fa-clipboard-list menu-icon"></i>
                        <span class="menu-text">All Requests</span>
                        <span class="badge info">Monitor</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/inventory/items.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'items.php') ? 'active' : ''; ?>">
                        <i class="fas fa-boxes menu-icon"></i>
                        <span class="menu-text">Inventory Items</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">System</div>
            <ul class="nav-menu">
                <!-- <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/reports/analytics.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'analytics.php') ? 'active' : ''; ?>">
                        <i class="fas fa-chart-bar menu-icon"></i>
                        <span class="menu-text">Analytics</span>
                    </a>
                </li> -->
                
                 <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/reports/analytics.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'system-settings.php') ? 'active' : ''; ?>">
                        <i class="fas fa-cogs menu-icon"></i>
                        <span class="menu-text">Settings</span>
                    </a>
                </li> 
            </ul>
        </div>
        <?php endif; ?>

        <!-- QUARTERMASTER NAVIGATION -->
        <?php if ($user_role == 'QuarterMaster'): ?>
        <div class="nav-section">
            <div class="nav-section-title">Requests</div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/requests/new-request.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'new-request.php') ? 'active' : ''; ?>">
                        <i class="fas fa-plus-circle menu-icon"></i>
                        <span class="menu-text">New Request</span>
                        <span class="badge success">Create</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/requests/my-requests.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'my-requests.php') ? 'active' : ''; ?>">
                        <i class="fas fa-clipboard-list menu-icon"></i>
                        <span class="menu-text">My Requests</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/requests/all-requests.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'all-requests.php') ? 'active' : ''; ?>">
                        <i class="fas fa-list-alt menu-icon"></i>
                        <span class="menu-text">All Requests</span>
                        <span class="badge info">Monitor</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Inventory</div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/inventory/current-stock.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'current-stock.php') ? 'active' : ''; ?>">
                        <i class="fas fa-warehouse menu-icon"></i>
                        <span class="menu-text">Current Stock</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/inventory/stock-management.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'stock-management.php') ? 'active' : ''; ?>">
                        <i class="fas fa-boxes menu-icon"></i>
                        <span class="menu-text">Stock Management</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/approvals/pending-approvals.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'pending-approvals.php') ? 'active' : ''; ?>">
                        <i class="fas fa-hourglass-half menu-icon"></i>
                        <span class="menu-text">Pending Approvals</span>
                        <span class="badge warning">3</span>
                    </a>
                </li>
            </ul>
        </div>
        <?php endif; ?>

        <!-- COMMANDING OFFICER NAVIGATION -->
        <?php if ($user_role == 'CO'): ?>
        <div class="nav-section">
            <div class="nav-section-title">Command Functions</div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/approvals/pending-approvals.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'pending-approvals.php') ? 'active' : ''; ?>">
                        <i class="fas fa-hourglass-half menu-icon"></i>
                        <span class="menu-text">Pending Approvals</span>
                        <span class="badge warning">5</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/requests/co-all-requests.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'co-all-requests.php') ? 'active' : ''; ?>">
                        <i class="fas fa-list-alt menu-icon"></i>
                        <span class="menu-text">All Requests</span>
                        <span class="badge info">Overview</span>
                    </a>
                </li>

                
                <!-- <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/command/budget-control.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'budget-control.php') ? 'active' : ''; ?>">
                        <i class="fas fa-calculator menu-icon"></i>
                        <span class="menu-text">Budget Control</span>
                    </a>
                </li> -->
            </ul>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Reports</div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/reports/command-reports.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'command-reports.php') ? 'active' : ''; ?>">
                        <i class="fas fa-chart-bar menu-icon"></i>
                        <span class="menu-text">Command Reports</span>
                    </a>
                </li>
            </ul>
        </div>
        <?php endif; ?>

        <!-- AUDITOR NAVIGATION -->
        <?php if ($user_role == 'Auditor'): ?>
        <div class="nav-section">
            <div class="nav-section-title">Audit Functions</div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/audit/transaction-audit.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'transaction-audit.php') ? 'active' : ''; ?>">
                        <i class="fas fa-search menu-icon"></i>
                        <span class="menu-text">Transaction Audit</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/audit/compliance-check.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'compliance-check.php') ? 'active' : ''; ?>">
                        <i class="fas fa-clipboard-check menu-icon"></i>
                        <span class="menu-text">Compliance Check</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/audit/financial-audit.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'financial-audit.php') ? 'active' : ''; ?>">
                        <i class="fas fa-dollar-sign menu-icon"></i>
                        <span class="menu-text">Financial Audit</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/audit/system-logs.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'system-logs.php') ? 'active' : ''; ?>">
                        <i class="fas fa-file-alt menu-icon"></i>
                        <span class="menu-text">System Logs</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Reports</div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/audit/audit-reports.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'audit-reports.php') ? 'active' : ''; ?>">
                        <i class="fas fa-chart-line menu-icon"></i>
                        <span class="menu-text">Audit Reports</span>
                    </a>
                </li>
            </ul>
        </div>
        <?php endif; ?>

        <!-- DEPARTMENT NAVIGATION -->
        <?php if ($user_role == 'Department'): ?>
        <div class="nav-section">
            <div class="nav-section-title">My Requests</div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/requests/new-request.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'new-request.php') ? 'active' : ''; ?>">
                        <i class="fas fa-plus-circle menu-icon"></i>
                        <span class="menu-text">New Request</span>
                        <span class="badge success">Create</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/requests/my-requests.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'my-requests.php') ? 'active' : ''; ?>">
                        <i class="fas fa-clipboard-list menu-icon"></i>
                        <span class="menu-text">My Requests</span>
                        <span class="badge">5</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/requests/request-history.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'request-history.php') ? 'active' : ''; ?>">
                        <i class="fas fa-history menu-icon"></i>
                        <span class="menu-text">Request History</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- <div class="nav-section">
            <div class="nav-section-title">Information</div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/info/request-status.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'request-status.php') ? 'active' : ''; ?>">
                        <i class="fas fa-info-circle menu-icon"></i>
                        <span class="menu-text">Request Status</span>
                    </a>
                </li>
            </ul>
        </div>
        <?php endif; ?> -->

        <!-- Common Reports Section - For All Roles (except Department) -->
        <!-- <?php if ($user_role != 'Department'): ?>
        <div class="nav-section">
            <div class="nav-section-title">Analytics</div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/reports/analytics.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'analytics.php') ? 'active' : ''; ?>">
                        <i class="fas fa-chart-bar menu-icon"></i>
                        <span class="menu-text">Reports</span>
                    </a>
                </li>
            </ul>
        </div>
        <?php endif; ?> -->
    </nav>



    <!-- Sidebar Footer
    <div class="sidebar-footer">
        <div class="footer-content">
            <div class="footer-avatar"><?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 2)); ?></div>
            <div class="footer-text">
                <div class="footer-name"><?php echo $_SESSION['full_name'] ?? 'User Name'; ?></div>
                <div class="footer-role"><?php echo $user_role; ?></div>
            </div>
            <div class="footer-status online"></div>
        </div>
    </div> -->
</aside>

<style>
/* Smart Sidebar Styles - Role-Based & Clean */
.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    height: 100vh;
    width: 270px;
    background: linear-gradient(135deg, #2D5016, #1e3c72);
    color: white;
    transition: all 0.3s ease;
    z-index: 1000;
    overflow-y: auto;
    box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
}

.sidebar::-webkit-scrollbar {
    width: 4px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 2px;
}

.sidebar.collapsed {
    width: 70px;
}

/* Header */
.sidebar-header {
    padding: 1.5rem 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.sidebar-header-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.sidebar-logo {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.sidebar-title h2 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 700;
}

.sidebar-title p {
    margin: 0;
    font-size: 0.8rem;
    opacity: 0.8;
}

.collapse-toggle {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 5px;
    transition: background 0.3s ease;
}

.collapse-toggle:hover {
    background: rgba(255, 255, 255, 0.1);
}

/* Navigation */
.sidebar-nav {
    padding: 1rem 0;
}

.nav-section {
    margin-bottom: 1.5rem;
}

.nav-section-title {
    padding: 0 1rem;
    margin-bottom: 0.5rem;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.6;
    font-weight: 600;
}

.nav-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-item {
    margin-bottom: 0.2rem;
}

.menu-item {
    display: flex;
    align-items: center;
    padding: 0.8rem 1rem;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
    border-radius: 0 25px 25px 0;
    margin-right: 1rem;
}

.menu-item:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    transform: translateX(5px);
}

.menu-item.active {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.menu-item.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 4px;
    background: #4facfe;
    border-radius: 0 2px 2px 0;
}

.menu-icon {
    width: 20px;
    font-size: 1rem;
    margin-right: 1rem;
    text-align: center;
}

.menu-text {
    flex: 1;
    font-size: 0.9rem;
    font-weight: 500;
}

.badge {
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge.info {
    background: #17a2b8;
    color: white;
}

.badge.warning {
    background: #ffc107;
    color: #212529;
}

.badge.success {
    background: #28a745;
    color: white;
}

.badge.danger {
    background: #dc3545;
    color: white;
}

/* Quick Actions */
.quick-actions {
    padding: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    margin-top: auto;
}

.quick-actions-title {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.6;
    margin-bottom: 1rem;
    font-weight: 600;
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
}

.quick-action-btn {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    padding: 0.7rem 0.5rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.75rem;
}

.quick-action-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.quick-action-btn i {
    font-size: 1rem;
}

/* Footer */
.sidebar-footer {
    padding: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    margin-top: auto;
}

.footer-content {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.footer-avatar {
    width: 35px;
    height: 35px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
}

.footer-text {
    flex: 1;
}

.footer-name {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.1rem;
}

.footer-role {
    font-size: 0.7rem;
    opacity: 0.8;
}

.footer-status {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #28a745;
}

.footer-status.online {
    background: #28a745;
    box-shadow: 0 0 8px rgba(40, 167, 69, 0.6);
}

/* Collapsed State */
.sidebar.collapsed .sidebar-title,
.sidebar.collapsed .menu-text,
.sidebar.collapsed .nav-section-title,
.sidebar.collapsed .quick-actions-title,
.sidebar.collapsed .footer-text,
.sidebar.collapsed .badge,
.sidebar.collapsed .quick-actions {
    display: none;
}

.sidebar.collapsed .menu-item {
    justify-content: center;
    margin-right: 0;
    border-radius: 0;
}

.sidebar.collapsed .menu-icon {
    margin-right: 0;
}

.sidebar.collapsed .footer-content {
    justify-content: center;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
        width: 270px;
    }
    
    .sidebar.mobile-visible {
        transform: translateX(0);
    }
    
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .sidebar-overlay.active {
        opacity: 1;
        visibility: visible;
    }
}

/* Main content adjustment */
.main-content {
    margin-left: 270px;
    transition: margin-left 0.3s ease;
}

.sidebar.collapsed ~ .main-content {
    margin-left: 70px;
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
    }
}

/* Role-specific color themes - All roles use original green gradient */
.sidebar[data-role="Admin"] {
    background: linear-gradient(135deg, #2D5016, #1e3c72);
}

.sidebar[data-role="QuarterMaster"] {
    background: linear-gradient(135deg, #2D5016, #1e3c72);
}

.sidebar[data-role="CO"] {
    background: linear-gradient(135deg, #2D5016, #1e3c72);
}

.sidebar[data-role="Auditor"] {
    background: linear-gradient(135deg, #2D5016, #1e3c72);
}

.sidebar[data-role="Department"] {
    background: linear-gradient(135deg, #2D5016, #1e3c72);
}
</style>

<script>
// Smart Sidebar JavaScript Functions
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const collapseIcon = document.getElementById('collapseIcon');
    
    sidebar.classList.toggle('collapsed');
    
    // Update icon
    if (sidebar.classList.contains('collapsed')) {
        collapseIcon.classList.remove('fa-chevron-left');
        collapseIcon.classList.add('fa-chevron-right');
    } else {
        collapseIcon.classList.remove('fa-chevron-right');
        collapseIcon.classList.add('fa-chevron-left');
    }
    
    // Save sidebar state
    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
}

function toggleMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.add('mobile-visible');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.remove('mobile-visible');
    overlay.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Handle mobile responsiveness
function handleResize() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (window.innerWidth > 768) {
        sidebar.classList.remove('mobile-visible');
        overlay.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}

// Initialize sidebar state on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set role-specific theme
    const userRole = '<?php echo $user_role; ?>';
    const sidebar = document.getElementById('sidebar');
    sidebar.setAttribute('data-role', userRole);
    
    // Restore sidebar collapsed state
    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed');
    if (sidebarCollapsed === 'true') {
        toggleSidebar();
    }
    
    // Add smooth hover effects
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            if (!this.classList.contains('active')) {
                this.style.transform = 'translateX(8px)';
            }
        });
        
        item.addEventListener('mouseleave', function() {
            if (!this.classList.contains('active')) {
                this.style.transform = 'translateX(0)';
            }
        });
    });
    
    // Update role-specific notifications
    updateRoleNotifications();
});

// Update role-specific notifications and badges
function updateRoleNotifications() {
    const userRole = '<?php echo $user_role; ?>';
    
    // This would be connected to your real-time notification system
    // For now, we'll simulate role-specific badge updates
    switch(userRole) {
        case 'Admin':
            // Update admin-specific badges
            console.log('🛡️ Admin notifications loaded');
            break;
        case 'QuarterMaster':
            // Update quartermaster-specific badges
            console.log('📦 QuarterMaster notifications loaded');
            break;
        case 'CO':
            // Update CO-specific badges
            console.log('⭐ CO notifications loaded');
            break;
        case 'Auditor':
            // Update auditor-specific badges
            console.log('🔍 Auditor notifications loaded');
            break;
        case 'Department':
            // Update department-specific badges
            console.log('🏢 Department notifications loaded');
            break;
    }
}

// Role-based quick access shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl + Shift + shortcuts for quick navigation
    if (e.ctrlKey && e.shiftKey) {
        const userRole = '<?php echo $user_role; ?>';
        
        switch(e.key) {
            case 'D': // Dashboard
                e.preventDefault();
                const dashboardUrl = getDashboardUrl(userRole);
                window.location.href = dashboardUrl;
                break;
            case 'N': // New (role-specific)
                e.preventDefault();
                if (userRole === 'Admin') {
                    window.location.href = '<?php echo $base_url; ?>modules/admin/user-management.php';
                } else if (['Department', 'QuarterMaster'].includes(userRole)) {
                    window.location.href = '<?php echo $base_url; ?>modules/requests/new-request.php';
                }
                break;
            case 'A': // Approvals (for CO, QuarterMaster, Admin)
                e.preventDefault();
                if (['CO', 'QuarterMaster', 'Admin'].includes(userRole)) {
                    window.location.href = '<?php echo $base_url; ?>modules/approvals/pending-approvals.php';
                }
                break;
        }
    }
});

function getDashboardUrl(role) {
    const baseUrl = '<?php echo $base_url; ?>dashboard/';
    switch(role) {
        case 'Admin':
            return baseUrl + 'admin-dashboard.php';
        case 'QuarterMaster':
            return baseUrl + 'quartermaster-dashboard.php';
        case 'CO':
            return baseUrl + 'co-dashboard.php';
        case 'Auditor':
            return baseUrl + 'auditor-dashboard.php';
        default:
            return baseUrl + 'department-dashboard.php';
    }
}

window.addEventListener('resize', handleResize);

// Make functions globally accessible
window.toggleSidebar = toggleSidebar;
window.toggleMobileSidebar = toggleMobileSidebar;
window.closeSidebar = closeSidebar;

// Role-based welcome message
console.log(`🎯 MSICT ${userRole} Panel Initialized`);
console.log('⌨️  Keyboard shortcuts: Ctrl+Shift+D (Dashboard), Ctrl+Shift+N (New), Ctrl+Shift+A (Approvals)');
</script>