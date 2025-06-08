<?php
// dashboard/admin-dashboard.php - Dynamic Version with Real Data
session_start();

// Check if user is logged in and is Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Set user data for components
$_SESSION['username'] = $_SESSION['username'] ?? 'admin001';
$_SESSION['full_name'] = $_SESSION['full_name'] ?? 'Administrator';
$_SESSION['user_role'] = 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MSICT Ordering System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Essential Dashboard Styles Only */
        :root {
            --primary-color: #2D5016;
            --secondary-color: #1e3c72;
            --white: #ffffff;
            --light-gray: #f8f9fa;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--card-bg-1), var(--card-bg-2));
            border-radius: 15px;
            padding: 1.5rem;
            color: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.users { --card-bg-1: #667eea; --card-bg-2: #764ba2; }
        .stat-card.requests { --card-bg-1: #f093fb; --card-bg-2: #f5576c; }
        .stat-card.pending { --card-bg-1: #4facfe; --card-bg-2: #00f2fe; }
        .stat-card.items { --card-bg-1: #43e97b; --card-bg-2: #38f9d7; }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Cards */
        .card {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            background: var(--light-gray);
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Table */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
        }

        .table th {
            background: var(--light-gray);
            font-weight: 600;
            color: var(--primary-color);
        }

        /* Status Badges */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.pending { background: #fff3cd; color: #856404; }
        .status-badge.approved { background: #d4edda; color: #155724; }
        .status-badge.rejected { background: #f8d7da; color: #721c24; }
        .status-badge.authorized { background: #cce5ff; color: #0056b3; }

        /* Buttons */
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .btn-info {
            background: #17a2b8;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
        }

        .page-actions {
            display: flex;
            gap: 0.75rem;
        }

        /* Loading States */
        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Alert */
        .alert {
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 10px;
            border: none;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .alert.success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }

        .alert.error {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }

        .alert.info {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            color: #0c5460;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
            .page-header {
                flex-direction: column;
                gap: 1rem;
            }
            .table th,
            .table td {
                padding: 0.5rem;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Include Sidebar Component -->
    <?php include 'components/sidebar.php'; ?>

    <!-- Include Header Component -->
    <?php include 'components/header.php'; ?>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="content-container">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-tachometer-alt"></i>
                        Admin Dashboard
                    </h1>
                </div>
                <div class="page-actions">
                    <button class="btn btn-success" onclick="refreshDashboard()">
                        <i class="fas fa-sync"></i>
                        Refresh
                    </button>
                    <button class="btn btn-primary" onclick="exportReport()">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- Main Stats -->
            <div class="stats-grid">
                <div class="stat-card users" onclick="navigateTo('../modules/admin/user-management.php')">
                    <div class="stat-value" id="totalUsers">
                        <div class="loading-skeleton" style="width: 60px; height: 32px; border-radius: 4px;"></div>
                    </div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-card requests" onclick="navigateTo('../modules/requests/all-requests.php')">
                    <div class="stat-value" id="totalRequests">
                        <div class="loading-skeleton" style="width: 80px; height: 32px; border-radius: 4px;"></div>
                    </div>
                    <div class="stat-label">Total Requests</div>
                </div>
                <div class="stat-card pending" onclick="navigateTo('../modules/requests/pending-requests.php')">
                    <div class="stat-value" id="pendingRequests">
                        <div class="loading-skeleton" style="width: 40px; height: 32px; border-radius: 4px;"></div>
                    </div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card items" onclick="navigateTo('../modules/inventory/items.php')">
                    <div class="stat-value" id="totalItems">
                        <div class="loading-skeleton" style="width: 70px; height: 32px; border-radius: 4px;"></div>
                    </div>
                    <div class="stat-label">Total Items</div>
                </div>
            </div>

            <!-- Recent Requests -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-list"></i>
                        Recent Requests
                    </h3>
                    <button class="btn btn-primary btn-sm" onclick="navigateTo('../modules/requests/all-requests.php')">
                        View All
                    </button>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Items</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="recentRequestsTable">
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 2rem;">
                                    <div class="loading-skeleton" style="width: 100%; height: 20px; border-radius: 4px; margin-bottom: 10px;"></div>
                                    <div class="loading-skeleton" style="width: 80%; height: 20px; border-radius: 4px; margin-bottom: 10px;"></div>
                                    <div class="loading-skeleton" style="width: 90%; height: 20px; border-radius: 4px;"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <button class="btn btn-primary" onclick="navigateTo('../modules/admin/user-management.php')">
                            <i class="fas fa-users"></i>
                            Manage Users
                        </button>
                        <button class="btn btn-info" onclick="navigateTo('../modules/inventory/items.php')">
                            <i class="fas fa-boxes"></i>
                            Manage Items
                        </button>
                        <button class="btn btn-success" onclick="navigateTo('../modules/admin/role-management.php')">
                            <i class="fas fa-user-shield"></i>
                            Manage Roles
                        </button>
                        <button class="btn btn-primary" onclick="navigateTo('../modules/requests/all-requests.php')">
                            <i class="fas fa-clipboard-list"></i>
                            View Requests
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Include Footer Component -->
    <?php include 'components/footer.php'; ?>

    <script>
        // API Configuration
        const API_BASE_URL = 'http://localhost/students-proj/unfedZombie/Controllers';
        let dashboardData = {
            users: [],
            requests: [],
            items: []
        };

        // Get JWT token from session
        const token = '<?php echo $_SESSION["jwt_token"] ?? ""; ?>';

        // API Headers
        const headers = {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        };

        // Load dashboard data on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🛡️ MSICT Admin Dashboard Loading...');
            loadDashboardData();
        });

        // Load all dashboard data
        async function loadDashboardData() {
            try {
                showAlert('🔄 Loading dashboard data...', 'info');
                
                // Load data in parallel
                const [usersData, requestsData, itemsData] = await Promise.allSettled([
                    loadUsers(),
                    loadRequests(),
                    loadItems()
                ]);

                // Process results
                if (usersData.status === 'fulfilled') {
                    dashboardData.users = usersData.value;
                    updateUserStats(usersData.value);
                } else {
                    console.error('Failed to load users:', usersData.reason);
                    document.getElementById('totalUsers').textContent = 'Error';
                }

                if (requestsData.status === 'fulfilled') {
                    dashboardData.requests = requestsData.value;
                    updateRequestStats(requestsData.value);
                    displayRecentRequests(requestsData.value);
                } else {
                    console.error('Failed to load requests:', requestsData.reason);
                    document.getElementById('totalRequests').textContent = 'Error';
                    document.getElementById('pendingRequests').textContent = 'Error';
                }

                if (itemsData.status === 'fulfilled') {
                    dashboardData.items = itemsData.value;
                    updateItemStats(itemsData.value);
                } else {
                    console.error('Failed to load items:', itemsData.reason);
                    document.getElementById('totalItems').textContent = 'Error';
                }

                showAlert('✅ Dashboard loaded successfully!', 'success');
                
            } catch (error) {
                console.error('Error loading dashboard:', error);
                showAlert('❌ Error loading dashboard: ' + error.message, 'error');
            }
        }

        // Load users from API
        async function loadUsers() {
            const response = await fetch(`${API_BASE_URL}/admin/api/admin/users`, {
                method: 'GET',
                headers: headers
            });

            if (!response.ok) {
                throw new Error(`Users API Error: ${response.status}`);
            }

            const users = await response.json();
            return Array.isArray(users) ? users : [];
        }

        // Load requests from API
        async function loadRequests() {
            const response = await fetch(`${API_BASE_URL}/admin/api/admin/requests`, {
                method: 'GET',
                headers: headers
            });

            if (!response.ok) {
                throw new Error(`Requests API Error: ${response.status}`);
            }

            const requests = await response.json();
            return Array.isArray(requests) ? requests : [];
        }

        // Load items from API
        async function loadItems() {
            const response = await fetch(`${API_BASE_URL}/admin/api/admin/getitems`, {
                method: 'GET',
                headers: headers
            });

            if (!response.ok) {
                throw new Error(`Items API Error: ${response.status}`);
            }

            const items = await response.json();
            return Array.isArray(items) ? items : [];
        }

        // Update user statistics
        function updateUserStats(users) {
            document.getElementById('totalUsers').textContent = users.length;
        }

        // Update request statistics
        function updateRequestStats(requests) {
            document.getElementById('totalRequests').textContent = requests.length;
            
            // Count pending requests (not approved and not authorized)
            const pendingCount = requests.filter(req => !req.approved && !req.authorized).length;
            document.getElementById('pendingRequests').textContent = pendingCount;
        }

        // Update item statistics
        function updateItemStats(items) {
            document.getElementById('totalItems').textContent = items.length;
        }

        // Display recent requests in table
        function displayRecentRequests(requests) {
            const tbody = document.getElementById('recentRequestsTable');
            
            if (!requests || requests.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: #666;">
                            <i class="fas fa-inbox"></i>
                            <br>No requests found
                        </td>
                    </tr>
                `;
                return;
            }

            // Get the 5 most recent requests
            const recentRequests = requests
                .sort((a, b) => new Date(b.created_at || b.request_date) - new Date(a.created_at || a.request_date))
                .slice(0, 5);

            tbody.innerHTML = recentRequests.map(request => {
                const status = getRequestStatus(request);
                const date = formatDate(request.created_at || request.request_date);
                
                return `
                    <tr>
                        <td><strong>#${request.id}</strong></td>
                        <td>${request.user_name || request.requester || 'Unknown User'}</td>
                        <td>${request.item_name || request.description || 'Multiple Items'}</td>
                        <td><span class="status-badge ${status.class}">${status.text}</span></td>
                        <td>${date}</td>
                        <td>
                            <button class="btn btn-info btn-sm" onclick="viewRequest(${request.id})" title="View Request">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Get request status
        function getRequestStatus(request) {
            if (request.authorized) {
                return { class: 'authorized', text: 'Authorized' };
            } else if (request.approved) {
                return { class: 'approved', text: 'Approved' };
            } else if (request.rejected) {
                return { class: 'rejected', text: 'Rejected' };
            } else {
                return { class: 'pending', text: 'Pending' };
            }
        }

        // Format date
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric',
                    year: date.getFullYear() !== new Date().getFullYear() ? 'numeric' : undefined
                });
            } catch (error) {
                return 'Invalid Date';
            }
        }

        // Navigation helper
        function navigateTo(url) {
            window.location.href = url;
        }

        // View specific request
        function viewRequest(requestId) {
            // Navigate to request details page
            window.location.href = `../modules/requests/view-request.php?id=${requestId}`;
        }

        // Export report
        function exportReport() {
            const stats = {
                totalUsers: dashboardData.users.length,
                totalRequests: dashboardData.requests.length,
                pendingRequests: dashboardData.requests.filter(req => !req.approved && !req.authorized).length,
                totalItems: dashboardData.items.length,
                date: new Date().toLocaleDateString()
            };

            const csvContent = [
                ['MSICT Admin Dashboard Report'],
                ['Generated:', stats.date],
                [''],
                ['Metric', 'Value'],
                ['Total Users', stats.totalUsers],
                ['Total Requests', stats.totalRequests],
                ['Pending Requests', stats.pendingRequests],
                ['Total Items', stats.totalItems],
                [''],
                ['Recent Requests'],
                ['ID', 'User', 'Status', 'Date'],
                ...dashboardData.requests.slice(0, 10).map(req => [
                    req.id,
                    req.user_name || req.requester || 'Unknown',
                    getRequestStatus(req).text,
                    formatDate(req.created_at || req.request_date)
                ])
            ].map(row => row.join(',')).join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `MSICT_Dashboard_Report_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            showAlert('📊 Dashboard report exported successfully!', 'success');
        }

        // Refresh dashboard
        function refreshDashboard() {
            console.log('🔄 Refreshing dashboard...');
            
            // Reset loading states
            document.getElementById('totalUsers').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 32px; border-radius: 4px;"></div>';
            document.getElementById('totalRequests').innerHTML = '<div class="loading-skeleton" style="width: 80px; height: 32px; border-radius: 4px;"></div>';
            document.getElementById('pendingRequests').innerHTML = '<div class="loading-skeleton" style="width: 40px; height: 32px; border-radius: 4px;"></div>';
            document.getElementById('totalItems').innerHTML = '<div class="loading-skeleton" style="width: 70px; height: 32px; border-radius: 4px;"></div>';
            
            document.getElementById('recentRequestsTable').innerHTML = `
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem;">
                        <i class="fas fa-spinner fa-spin"></i> Loading recent requests...
                    </td>
                </tr>
            `;
            
            // Reload data
            loadDashboardData();
        }

        // Show alert notification
        function showAlert(message, type = 'info') {
            const alertContainer = document.getElementById('alertContainer');
            
            const alert = document.createElement('div');
            alert.className = `alert ${type}`;
            
            const icon = type === 'success' ? 'fa-check-circle' : 
                        type === 'error' ? 'fa-exclamation-circle' : 
                        type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
            
            alert.innerHTML = `
                <i class="fas ${icon}"></i>
                <span>${message}</span>
            `;
            
            alertContainer.appendChild(alert);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.style.animation = 'slideOutRight 0.4s ease';
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 400);
                }
            }, 3000);
        }

        // Make functions globally accessible
        window.refreshDashboard = refreshDashboard;
        window.exportReport = exportReport;
        window.viewRequest = viewRequest;
        window.navigateTo = navigateTo;

        // Initialize
        console.log('🛡️ MSICT Admin Dashboard Initialized');
        console.log('🔗 API Base URL:', API_BASE_URL);
        console.log('🔑 JWT Token Status:', token ? 'Present' : 'Missing');
    </script>
</body>
</html>