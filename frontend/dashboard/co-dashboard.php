<?php
// dashboard/co-dashboard.php - CO Dashboard with Real Data
session_start();

// Check if user is logged in and is CO
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'CO') {
    header('Location: ../auth/login.php');
    exit();
}

// Set user data for components
$_SESSION['username'] = $_SESSION['username'] ?? 'co001';
$_SESSION['full_name'] = $_SESSION['full_name'] ?? 'Chief Officer';
$_SESSION['user_role'] = 'CO';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CO Dashboard - MSICT Ordering System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Essential Dashboard Styles */
        :root {
            --primary-color: #2D5016;
            --secondary-color: #1e3c72;
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f6fa;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--card-bg-1), var(--card-bg-2));
            border-radius: 15px;
            padding: 2rem;
            color: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
            text-align: center;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.pending { --card-bg-1: #ff7675; --card-bg-2: #fd79a8; }
        .stat-card.approved { --card-bg-1: #00b894; --card-bg-2: #00cec9; }
        .stat-card.denied { --card-bg-1: #fdcb6e; --card-bg-2: #e17055; }
        .stat-card.total { --card-bg-1: #6c5ce7; --card-bg-2: #a29bfe; }

        .stat-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.8;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 500;
        }

        /* Cards */
        .card {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, var(--light-gray), #e9ecef);
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.2rem;
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
            padding: 1rem 0.75rem;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .table th {
            background: var(--light-gray);
            font-weight: 600;
            color: var(--primary-color);
            position: sticky;
            top: 0;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        /* Status Badges */
        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.pending { background: #fff3cd; color: #856404; }
        .status-badge.approved { background: #d4edda; color: #155724; }
        .status-badge.denied { background: #f8d7da; color: #721c24; }

        /* Priority Badges */
        .priority-badge {
            padding: 0.3rem 0.6rem;
            border-radius: 15px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .priority-badge.high { background: #ffebee; color: #c62828; }
        .priority-badge.medium { background: #fff3e0; color: #ef6c00; }
        .priority-badge.low { background: #e8f5e8; color: #2e7d32; }

        /* Buttons */
        .btn {
            padding: 0.6rem 1.2rem;
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

        .btn-primary { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; }
        .btn-success { background: var(--success); color: white; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-info { background: var(--info); color: white; }
        .btn-warning { background: var(--warning); color: #212529; }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
        }

        .page-subtitle {
            font-size: 1rem;
            color: #666;
            margin-top: 0.25rem;
        }

        .page-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        /* Loading States */
        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: 4px;
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

        .alert.success { background: linear-gradient(135deg, #d4edda, #c3e6cb); color: #155724; }
        .alert.error { background: linear-gradient(135deg, #f8d7da, #f5c6cb); color: #721c24; }
        .alert.info { background: linear-gradient(135deg, #d1ecf1, #bee5eb); color: #0c5460; }
        .alert.warning { background: linear-gradient(135deg, #fff3cd, #ffeaa7); color: #856404; }

        /* Request Details */
        .request-details {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin: 0.5rem 0;
        }

        .request-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
        }

        .meta-label {
            font-size: 0.75rem;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .meta-value {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--primary-color);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }
            
            .stat-card {
                padding: 1.5rem;
            }
            
            .page-header {
                flex-direction: column;
                align-items: stretch;
            }
            
            .page-actions {
                justify-content: center;
            }
            
            .table {
                font-size: 0.8rem;
            }
            
            .table th,
            .table td {
                padding: 0.5rem 0.25rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #666;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .empty-state h3 {
            margin-bottom: 0.5rem;
            color: var(--primary-color);
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
                        <i class="fas fa-user-tie"></i>
                        CO Dashboard
                    </h1>
                    <p class="page-subtitle">Chief Officer - Request Approval Management</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-info" onclick="refreshDashboard()">
                        <i class="fas fa-sync"></i>
                        Refresh
                    </button>
                    <button class="btn btn-primary" onclick="exportApprovalReport()">
                        <i class="fas fa-download"></i>
                        Export Report
                    </button>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card pending" onclick="filterRequests('pending')">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value" id="pendingCount">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Pending Requests</div>
                </div>
                
                <div class="stat-card approved" onclick="filterRequests('approved')">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value" id="approvedCount">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Approved by Me</div>
                </div>
                
                <div class="stat-card denied" onclick="filterRequests('denied')">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-value" id="deniedCount">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Denied by Me</div>
                </div>
                
                <div class="stat-card total" onclick="filterRequests('all')">
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stat-value" id="totalCount">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Total Processed</div>
                </div>
            </div>

            <!-- Pending Requests for Approval -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-hourglass-half"></i>
                        Pending Requests Requiring Approval
                    </h3>
                    <div>
                        <button class="btn btn-success btn-sm" onclick="approveAllSelected()" id="approveAllBtn" disabled>
                            <i class="fas fa-check"></i>
                            Approve Selected
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="denyAllSelected()" id="denyAllBtn" disabled>
                            <i class="fas fa-times"></i>
                            Deny Selected
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="pendingRequestsContainer">
                        <div class="loading-skeleton" style="width: 100%; height: 200px;"></div>
                    </div>
                </div>
            </div>

            <!-- Recently Processed Requests -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i>
                        Recently Processed Requests
                    </h3>
                    <button class="btn btn-info btn-sm" onclick="loadApprovedRequests()">
                        <i class="fas fa-refresh"></i>
                        Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div id="approvedRequestsContainer">
                        <div class="loading-skeleton" style="width: 100%; height: 150px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Include Footer Component -->
    <?php include 'components/footer.php'; ?>

    <script>
        // API Configuration
        const API_BASE_URL = 'http://localhost/unfedZombie/Controllers';
        let dashboardData = {
            pendingRequests: [],
            approvedRequests: [],
            selectedRequests: []
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
            console.log('👔 MSICT CO Dashboard Loading...');
            loadDashboardData();
        });

        // Load all dashboard data
        async function loadDashboardData() {
            try {
                showAlert('🔄 Loading CO dashboard...', 'info');
                
                // Load pending and approved requests
                await Promise.all([
                    loadPendingRequests(),
                    loadApprovedRequests()
                ]);

                showAlert('✅ Dashboard loaded successfully!', 'success');
                
            } catch (error) {
                console.error('Error loading CO dashboard:', error);
                showAlert('❌ Error loading dashboard: ' + error.message, 'error');
            }
        }

        // Load pending requests from CO API
        async function loadPendingRequests() {
            try {
                const response = await fetch(`${API_BASE_URL}/CO/api/requests/pending`, {
                    method: 'GET',
                    headers: headers
                });

                if (!response.ok) {
                    throw new Error(`Pending Requests API Error: ${response.status}`);
                }

                const requests = await response.json();
                dashboardData.pendingRequests = Array.isArray(requests) ? requests : [];
                
                updatePendingStats();
                displayPendingRequests();
                
            } catch (error) {
                console.error('Error loading pending requests:', error);
                document.getElementById('pendingCount').textContent = 'Error';
                document.getElementById('pendingRequestsContainer').innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h3>Error Loading Requests</h3>
                        <p>${error.message}</p>
                    </div>
                `;
            }
        }

        // Load approved requests from CO API
        async function loadApprovedRequests() {
            try {
                const response = await fetch(`${API_BASE_URL}/CO/api/requests/approved`, {
                    method: 'GET',
                    headers: headers
                });

                if (!response.ok) {
                    throw new Error(`Approved Requests API Error: ${response.status}`);
                }

                const requests = await response.json();
                dashboardData.approvedRequests = Array.isArray(requests) ? requests : [];
                
                updateApprovedStats();
                displayApprovedRequests();
                
            } catch (error) {
                console.error('Error loading approved requests:', error);
                document.getElementById('approvedCount').textContent = 'Error';
                document.getElementById('deniedCount').textContent = 'Error';
                document.getElementById('totalCount').textContent = 'Error';
            }
        }

        // Update pending statistics
        function updatePendingStats() {
            document.getElementById('pendingCount').textContent = dashboardData.pendingRequests.length;
        }

        // Update approved statistics
        function updateApprovedStats() {
            const approved = dashboardData.approvedRequests.filter(req => req.status === 'approved').length;
            const denied = dashboardData.approvedRequests.filter(req => req.status === 'denied').length;
            
            document.getElementById('approvedCount').textContent = approved;
            document.getElementById('deniedCount').textContent = denied;
            document.getElementById('totalCount').textContent = dashboardData.approvedRequests.length;
        }

        // Display pending requests
        function displayPendingRequests() {
            const container = document.getElementById('pendingRequestsContainer');
            
            if (!dashboardData.pendingRequests || dashboardData.pendingRequests.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <h3>No Pending Requests</h3>
                        <p>All requests have been processed. Great job!</p>
                    </div>
                `;
                return;
            }

            const tableHtml = `
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>Request ID</th>
                            <th>Requested By</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Priority</th>
                            <th>Purpose</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${dashboardData.pendingRequests.map(request => `
                            <tr>
                                <td>
                                    <input type="checkbox" class="request-select" value="${request.id}" onchange="updateSelectedRequests()">
                                </td>
                                <td><strong>#${request.id}</strong></td>
                                <td>${request.requested_by || 'Unknown'}</td>
                                <td>${request.item_name || 'N/A'}</td>
                                <td><strong>${request.quantity_requested || 0}</strong></td>
                                <td><span class="priority-badge ${request.priority || 'medium'}">${request.priority || 'Medium'}</span></td>
                                <td title="${request.purpose || 'No purpose specified'}">${truncateText(request.purpose || 'No purpose specified', 30)}</td>
                                <td>${formatDate(request.request_date)}</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-success btn-sm" onclick="approveRequest(${request.id})" title="Approve Request">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="denyRequest(${request.id})" title="Deny Request">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button class="btn btn-info btn-sm" onclick="viewRequestDetails(${request.id})" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;

            container.innerHTML = tableHtml;
        }

        // Display approved requests
        function displayApprovedRequests() {
            const container = document.getElementById('approvedRequestsContainer');
            
            if (!dashboardData.approvedRequests || dashboardData.approvedRequests.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>No Processed Requests</h3>
                        <p>You haven't processed any requests yet.</p>
                    </div>
                `;
                return;
            }

            // Show only the 10 most recent
            const recentRequests = dashboardData.approvedRequests
                .sort((a, b) => new Date(b.approved_at) - new Date(a.approved_at))
                .slice(0, 10);

            const tableHtml = `
                <table class="table">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Requested By</th>
                            <th>Item</th>
                            <th>Status</th>
                            <th>Processed Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${recentRequests.map(request => `
                            <tr>
                                <td><strong>#${request.id}</strong></td>
                                <td>${request.requested_by || 'Unknown'}</td>
                                <td>${request.item_name || 'N/A'}</td>
                                <td><span class="status-badge ${request.status}">${request.status || 'Unknown'}</span></td>
                                <td>${formatDate(request.approved_at)}</td>
                                <td>
                                    <button class="btn btn-info btn-sm" onclick="viewRequestDetails(${request.id})" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;

            container.innerHTML = tableHtml;
        }

        // Approve a single request
        async function approveRequest(requestId) {
            if (!confirm('Are you sure you want to approve this request?')) {
                return;
            }

            try {
                showAlert('🔄 Approving request...', 'info');
                
                const response = await fetch(`${API_BASE_URL}/CO/api/requests/approve?id=${requestId}`, {
                    method: 'PUT',
                    headers: headers
                });

                if (!response.ok) {
                    throw new Error(`Approval failed: ${response.status}`);
                }

                const result = await response.json();
                showAlert('✅ Request approved successfully!', 'success');
                
                // Refresh data
                await loadDashboardData();
                
            } catch (error) {
                console.error('Error approving request:', error);
                showAlert('❌ Error approving request: ' + error.message, 'error');
            }
        }

        // Deny a single request
        async function denyRequest(requestId) {
            const remarks = prompt('Please provide a reason for denying this request:');
            if (!remarks) {
                return;
            }

            try {
                showAlert('🔄 Denying request...', 'info');
                
                const response = await fetch(`${API_BASE_URL}/CO/api/requests/deny?id=${requestId}`, {
                    method: 'PUT',
                    headers: headers,
                    body: JSON.stringify({ remarks: remarks })
                });

                if (!response.ok) {
                    throw new Error(`Denial failed: ${response.status}`);
                }

                const result = await response.json();
                showAlert('✅ Request denied successfully!', 'success');
                
                // Refresh data
                await loadDashboardData();
                
            } catch (error) {
                console.error('Error denying request:', error);
                showAlert('❌ Error denying request: ' + error.message, 'error');
            }
        }

        // Toggle select all checkboxes
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.request-select');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            
            updateSelectedRequests();
        }

        // Update selected requests array
        function updateSelectedRequests() {
            const checkboxes = document.querySelectorAll('.request-select:checked');
            dashboardData.selectedRequests = Array.from(checkboxes).map(cb => parseInt(cb.value));
            
            // Update button states
            const approveBtn = document.getElementById('approveAllBtn');
            const denyBtn = document.getElementById('denyAllBtn');
            
            if (dashboardData.selectedRequests.length > 0) {
                approveBtn.disabled = false;
                denyBtn.disabled = false;
            } else {
                approveBtn.disabled = true;
                denyBtn.disabled = true;
            }
        }

        // Approve all selected requests
        async function approveAllSelected() {
            if (dashboardData.selectedRequests.length === 0) {
                showAlert('⚠️ No requests selected', 'warning');
                return;
            }

            if (!confirm(`Are you sure you want to approve ${dashboardData.selectedRequests.length} selected requests?`)) {
                return;
            }

            try {
                showAlert('🔄 Approving selected requests...', 'info');
                
                const promises = dashboardData.selectedRequests.map(requestId =>
                    fetch(`${API_BASE_URL}/CO/api/requests/approve?id=${requestId}`, {
                        method: 'PUT',
                        headers: headers
                    })
                );

                await Promise.all(promises);
                showAlert(`✅ ${dashboardData.selectedRequests.length} requests approved successfully!`, 'success');
                
                // Reset selection and refresh
                dashboardData.selectedRequests = [];
                await loadDashboardData();
                
            } catch (error) {
                console.error('Error approving requests:', error);
                showAlert('❌ Error approving some requests: ' + error.message, 'error');
            }
        }

        // Deny all selected requests
        async function denyAllSelected() {
            if (dashboardData.selectedRequests.length === 0) {
                showAlert('⚠️ No requests selected', 'warning');
                return;
            }

            const remarks = prompt('Please provide a reason for denying these requests:');
            if (!remarks) {
                return;
            }

            try {
                showAlert('🔄 Denying selected requests...', 'info');
                
                const promises = dashboardData.selectedRequests.map(requestId =>
                    fetch(`${API_BASE_URL}/CO/api/requests/deny?id=${requestId}`, {
                        method: 'PUT',
                        headers: headers,
                        body: JSON.stringify({ remarks: remarks })
                    })
                );

                await Promise.all(promises);
                showAlert(`✅ ${dashboardData.selectedRequests.length} requests denied successfully!`, 'success');
                
                // Reset selection and refresh
                dashboardData.selectedRequests = [];
                await loadDashboardData();
                
            } catch (error) {
                console.error('Error denying requests:', error);
                showAlert('❌ Error denying some requests: ' + error.message, 'error');
            }
        }

        // View request details
        function viewRequestDetails(requestId) {
            // Navigate to request details page or show modal
            window.location.href = `../modules/requests/view-request.php?id=${requestId}`;
        }

        // Filter requests by status
        function filterRequests(status) {
            console.log(`Filtering requests by: ${status}`);
            // This could be implemented to filter the display
            // For now, just show an alert
            showAlert(`📋 Filtering by ${status} requests`, 'info');
        }

        // Export approval report
        function exportApprovalReport() {
            const stats = {
                pending: dashboardData.pendingRequests.length,
                approved: dashboardData.approvedRequests.filter(req => req.status === 'approved').length,
                denied: dashboardData.approvedRequests.filter(req => req.status === 'denied').length,
                total: dashboardData.approvedRequests.length,
                date: new Date().toLocaleDateString()
            };

            const csvContent = [
                ['MSICT CO Approval Report'],
                ['Generated:', stats.date],
                ['Officer:', '<?php echo $_SESSION["full_name"] ?? "Chief Officer"; ?>'],
                [''],
                ['Summary'],
                ['Metric', 'Count'],
                ['Pending Requests', stats.pending],
                ['Approved Requests', stats.approved],
                ['Denied Requests', stats.denied],
                ['Total Processed', stats.total],
                [''],
                ['Recent Approvals'],
                ['Request ID', 'Requested By', 'Item', 'Status', 'Date'],
                ...dashboardData.approvedRequests.slice(0, 20).map(req => [
                    req.id,
                    req.requested_by || 'Unknown',
                    req.item_name || 'N/A',
                    req.status || 'Unknown',
                    formatDate(req.approved_at)
                ])
            ].map(row => row.join(',')).join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `MSICT_CO_Approval_Report_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            showAlert('📊 Approval report exported successfully!', 'success');
        }

        // Refresh dashboard
        function refreshDashboard() {
            console.log('🔄 Refreshing CO dashboard...');
            
            // Reset loading states
            document.getElementById('pendingCount').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 40px;"></div>';
            document.getElementById('approvedCount').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 40px;"></div>';
            document.getElementById('deniedCount').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 40px;"></div>';
            document.getElementById('totalCount').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 40px;"></div>';
            
            document.getElementById('pendingRequestsContainer').innerHTML = '<div class="loading-skeleton" style="width: 100%; height: 200px;"></div>';
            document.getElementById('approvedRequestsContainer').innerHTML = '<div class="loading-skeleton" style="width: 100%; height: 150px;"></div>';
            
            // Reload data
            loadDashboardData();
        }

        // Utility Functions
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric',
                    year: date.getFullYear() !== new Date().getFullYear() ? 'numeric' : undefined,
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (error) {
                return 'Invalid Date';
            }
        }

        function truncateText(text, maxLength) {
            if (!text) return '';
            return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
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
            
            // Auto remove after timeout
            const timeout = ['warning', 'error'].includes(type) ? 6000 : 4000;
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.style.animation = 'slideOutRight 0.4s ease';
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 400);
                }
            }, timeout);
        }

        // Make functions globally accessible
        window.refreshDashboard = refreshDashboard;
        window.exportApprovalReport = exportApprovalReport;
        window.approveRequest = approveRequest;
        window.denyRequest = denyRequest;
        window.viewRequestDetails = viewRequestDetails;
        window.filterRequests = filterRequests;
        window.toggleSelectAll = toggleSelectAll;
        window.updateSelectedRequests = updateSelectedRequests;
        window.approveAllSelected = approveAllSelected;
        window.denyAllSelected = denyAllSelected;

        // Initialize dashboard
        console.log('👔 MSICT CO Dashboard Initialized');
        console.log('🔗 API Base URL:', API_BASE_URL);
        console.log('🔑 JWT Token Status:', token ? 'Present' : 'Missing');
        console.log('👤 User Role: CO (Chief Officer)');
    </script>
</body>
</html>