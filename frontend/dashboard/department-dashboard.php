<?php
// dashboard/department-dashboard.php - Department Dashboard
session_start();

// Check if user is logged in and is Department
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Department') {
    header('Location: ../auth/login.php');
    exit();
}

// Set user data for components
$_SESSION['username'] = $_SESSION['username'] ?? 'dept001';
$_SESSION['full_name'] = $_SESSION['full_name'] ?? 'Department User';
$_SESSION['user_role'] = 'Department';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Dashboard - MSICT Ordering System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Department Dashboard Styles */
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

        /* Page Layout */
        .dashboard-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
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
            flex-wrap: wrap;
            gap: 1rem;
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

        .stat-card.total-requests { --card-bg-1: #667eea; --card-bg-2: #764ba2; }
        .stat-card.pending-requests { --card-bg-1: #ff7675; --card-bg-2: #fd79a8; }
        .stat-card.approved-requests { --card-bg-1: #00b894; --card-bg-2: #00cec9; }
        .stat-card.denied-requests { --card-bg-1: #fdcb6e; --card-bg-2: #e17055; }

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
        .btn-warning { background: var(--warning); color: #212529; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-info { background: var(--info); color: white; }
        .btn-secondary { background: #6c757d; color: white; }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
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
        .status-badge.in_progress { background: #d1ecf1; color: #0c5460; }

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

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .action-card:hover {
            transform: translateY(-3px);
            border-color: var(--primary-color);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .action-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .action-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .action-description {
            font-size: 0.85rem;
            color: #666;
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

        /* Request Form */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--primary-color);
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(45, 80, 22, 0.1);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--light-gray);
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .close-modal:hover {
            background: var(--light-gray);
            color: var(--danger);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .page-actions {
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }

            .stat-card {
                padding: 1.5rem;
            }

            .table th,
            .table td {
                padding: 0.5rem 0.25rem;
                font-size: 0.8rem;
            }

            .quick-actions {
                grid-template-columns: 1fr;
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
        <div class="dashboard-container">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-building"></i>
                        Department Dashboard
                    </h1>
                    <p class="page-subtitle">Submit and track your department's item requests</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-info" onclick="refreshDashboard()">
                        <i class="fas fa-sync"></i>
                        Refresh
                    </button>
                    <button class="btn btn-success" onclick="showNewRequestModal()">
                        <i class="fas fa-plus"></i>
                        New Request
                    </button>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- Request Statistics -->
            <div class="stats-grid">
                <div class="stat-card total-requests" onclick="showAllRequests()">
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stat-value" id="totalRequests">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Total Requests</div>
                </div>
                
                <div class="stat-card pending-requests" onclick="filterRequests('pending')">
                    <div class="stat-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-value" id="pendingRequests">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Pending</div>
                </div>
                
                <div class="stat-card approved-requests" onclick="filterRequests('approved')">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value" id="approvedRequests">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Approved</div>
                </div>
                
                <div class="stat-card denied-requests" onclick="filterRequests('denied')">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-value" id="deniedRequests">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Denied</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <div class="action-card" onclick="showNewRequestModal()">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="action-title">Submit New Request</div>
                    <div class="action-description">Create a new item request for your department</div>
                </div>
                
                <div class="action-card" onclick="trackRequests()">
                    <div class="action-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="action-title">Track Requests</div>
                    <div class="action-description">Monitor the status of your submitted requests</div>
                </div>
                
                <div class="action-card" onclick="viewRequestHistory()">
                    <div class="action-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="action-title">Request History</div>
                    <div class="action-description">View all your past requests and their outcomes</div>
                </div>
                
                <div class="action-card" onclick="exportMyRequests()">
                    <div class="action-icon">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="action-title">Export Data</div>
                    <div class="action-description">Download your request history as CSV</div>
                </div>
            </div>

            <!-- Recent Requests -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock"></i>
                        Recent Requests
                    </h3>
                    <button class="btn btn-primary btn-sm" onclick="showAllRequests()">
                        View All
                    </button>
                </div>
                <div class="card-body">
                    <div id="recentRequestsContainer">
                        <div class="loading-skeleton" style="width: 100%; height: 200px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- New Request Modal -->
    <div class="modal" id="newRequestModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Submit New Request</h2>
                <button class="close-modal" onclick="closeNewRequestModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="newRequestForm">
                    <div class="form-group">
                        <label class="form-label" for="itemSelect">Select Item</label>
                        <select id="itemSelect" class="form-control" required>
                            <option value="">Loading items...</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="quantityInput">Quantity Requested</label>
                        <input type="number" id="quantityInput" class="form-control" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="prioritySelect">Priority</label>
                        <select id="prioritySelect" class="form-control" required>
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="purposeInput">Purpose/Justification</label>
                        <textarea id="purposeInput" class="form-control" rows="4" placeholder="Explain why you need these items..." required></textarea>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                        <button type="button" class="btn btn-secondary" onclick="closeNewRequestModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i> Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include Footer Component -->
    <?php include 'components/footer.php'; ?>

    <script>
        // API Configuration
        const API_BASE_URL = 'http://localhost/unfedZombie/Controllers';
        let dashboardData = {
            myRequests: [],
            availableItems: []
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
            console.log('🏢 Department Dashboard Loading...');
            loadDashboardData();
            setupEventListeners();
        });

        // Setup event listeners
        function setupEventListeners() {
            // New request form
            document.getElementById('newRequestForm').addEventListener('submit', submitNewRequest);
        }

        // Load all dashboard data
        async function loadDashboardData() {
            try {
                showAlert('🔄 Loading dashboard...', 'info');
                
                await Promise.all([
                    loadMyRequests(),
                    loadAvailableItems()
                ]);

                updateStatistics();
                displayRecentRequests();
                populateItemSelect();
                
                showAlert('✅ Dashboard loaded successfully!', 'success');
                
            } catch (error) {
                console.error('Error loading dashboard:', error);
                showAlert('❌ Error loading dashboard: ' + error.message, 'error');
            }
        }

        // Load my requests from Department API
        async function loadMyRequests() {
            try {
                const response = await fetch(`${API_BASE_URL}/Department/api/requests/my`, {
                    method: 'GET',
                    headers: headers
                });

                if (!response.ok) {
                    throw new Error(`My Requests API Error: ${response.status}`);
                }

                const requests = await response.json();
                dashboardData.myRequests = Array.isArray(requests) ? requests : [];
                
            } catch (error) {
                console.error('Error loading requests:', error);
                dashboardData.myRequests = [];
            }
        }

        // Load available items (from admin API for now)
        async function loadAvailableItems() {
            try {
                const response = await fetch(`${API_BASE_URL}/admin/api/admin/getitems`, {
                    method: 'GET',
                    headers: headers
                });

                if (!response.ok) {
                    throw new Error(`Items API Error: ${response.status}`);
                }

                const items = await response.json();
                dashboardData.availableItems = Array.isArray(items) ? items : [];
                
            } catch (error) {
                console.error('Error loading items:', error);
                dashboardData.availableItems = [];
            }
        }

        // Update statistics
        function updateStatistics() {
            const total = dashboardData.myRequests.length;
            const pending = dashboardData.myRequests.filter(req => req.status === 'pending').length;
            const approved = dashboardData.myRequests.filter(req => req.status === 'approved').length;
            const denied = dashboardData.myRequests.filter(req => req.status === 'denied').length;

            document.getElementById('totalRequests').textContent = total;
            document.getElementById('pendingRequests').textContent = pending;
            document.getElementById('approvedRequests').textContent = approved;
            document.getElementById('deniedRequests').textContent = denied;
        }

        // Display recent requests
        function displayRecentRequests() {
            const container = document.getElementById('recentRequestsContainer');
            
            if (!dashboardData.myRequests || dashboardData.myRequests.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>No Requests Yet</h3>
                        <p>You haven't submitted any requests. Click "New Request" to get started!</p>
                    </div>
                `;
                return;
            }

            // Show 5 most recent requests
            const recentRequests = dashboardData.myRequests
                .sort((a, b) => new Date(b.request_date) - new Date(a.request_date))
                .slice(0, 5);

            const tableHtml = `
                <table class="table">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${recentRequests.map(request => `
                            <tr>
                                <td><strong>#${request.id}</strong></td>
                                <td>${request.item_name || 'Unknown Item'}</td>
                                <td><strong>${request.quantity_requested}</strong></td>
                                <td><span class="priority-badge ${request.priority || 'medium'}">${request.priority || 'Medium'}</span></td>
                                <td><span class="status-badge ${request.status}">${request.status || 'Pending'}</span></td>
                                <td>${formatDate(request.request_date)}</td>
                                <td>
                                    <button class="btn btn-info btn-sm" onclick="viewRequest(${request.id})" title="View Details">
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

        // Populate item select dropdown
        function populateItemSelect() {
            const select = document.getElementById('itemSelect');
            
            if (!dashboardData.availableItems || dashboardData.availableItems.length === 0) {
                select.innerHTML = '<option value="">No items available</option>';
                return;
            }

            select.innerHTML = `
                <option value="">Select an item...</option>
                ${dashboardData.availableItems.map(item => `
                    <option value="${item.id}">${item.name} (${item.category_id || 'General'})</option>
                `).join('')}
            `;
        }

        // Submit new request
        async function submitNewRequest(e) {
            e.preventDefault();
            
            const formData = {
                item_id: parseInt(document.getElementById('itemSelect').value),
                quantity_requested: parseInt(document.getElementById('quantityInput').value),
                priority: document.getElementById('prioritySelect').value,
                purpose: document.getElementById('purposeInput').value
            };

            if (!formData.item_id || !formData.quantity_requested || !formData.purpose) {
                showAlert('❌ Please fill in all required fields', 'error');
                return;
            }

            try {
                showAlert('🔄 Submitting request...', 'info');
                
                const response = await fetch(`${API_BASE_URL}/Department/api/requests/add`, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify(formData)
                });

                if (!response.ok) {
                    throw new Error(`Request submission failed: ${response.status}`);
                }

                const result = await response.json();
                showAlert('✅ Request submitted successfully!', 'success');
                
                // Reset form and close modal
                document.getElementById('newRequestForm').reset();
                closeNewRequestModal();
                
                // Refresh dashboard data
                await loadDashboardData();
                
            } catch (error) {
                console.error('Error submitting request:', error);
                showAlert('❌ Error submitting request: ' + error.message, 'error');
            }
        }

        // View single request details
        async function viewRequest(requestId) {
            try {
                showAlert('🔄 Loading request details...', 'info');
                
                const response = await fetch(`${API_BASE_URL}/Department/api/requests/view?id=${requestId}`, {
                    method: 'GET',
                    headers: headers
                });

                if (!response.ok) {
                    throw new Error(`Request details failed: ${response.status}`);
                }

                const request = await response.json();
                
                // Show request details in alert or modal
                const statusBadge = `<span class="status-badge ${request.status}">${request.status}</span>`;
                const priorityBadge = `<span class="priority-badge ${request.priority}">${request.priority}</span>`;
                
                showAlert(`
                    📋 Request #${request.id}<br>
                    Item: ${request.item_name}<br>
                    Quantity: ${request.quantity_requested}<br>
                    Status: ${statusBadge}<br>
                    Priority: ${priorityBadge}<br>
                    ${request.remarks ? `Remarks: ${request.remarks}` : ''}
                `, 'info');
                
            } catch (error) {
                console.error('Error viewing request:', error);
                showAlert('❌ Error loading request details: ' + error.message, 'error');
            }
        }

        // Track request status
        async function trackRequests() {
            try {
                showAlert('🔄 Loading request status...', 'info');
                
                const response = await fetch(`${API_BASE_URL}/Department/api/requests/status`, {
                    method: 'GET',
                    headers: headers
                });

                if (!response.ok) {
                    throw new Error(`Request tracking failed: ${response.status}`);
                }

                const requests = await response.json();
                
                // Show tracking information
                if (requests.length === 0) {
                    showAlert('📭 No requests to track', 'info');
                } else {
                    const trackingInfo = requests.slice(0, 3).map(req => 
                        `#${req.id}: ${req.progress || req.status}`
                    ).join('<br>');
                    
                    showAlert(`📊 Recent Request Status:<br>${trackingInfo}`, 'info');
                }
                
            } catch (error) {
                console.error('Error tracking requests:', error);
                showAlert('❌ Error tracking requests: ' + error.message, 'error');
            }
        }

        // Filter requests by status
        function filterRequests(status) {
            const filteredRequests = dashboardData.myRequests.filter(req => req.status === status);
            showAlert(`📋 Found ${filteredRequests.length} ${status} requests`, 'info');
            
            // Could implement a filtered view here
            console.log(`Filtered ${status} requests:`, filteredRequests);
        }

        // Show all requests
        function showAllRequests() {
            // Navigate to all requests page or show modal with all requests
            showAlert(`📋 Total ${dashboardData.myRequests.length} requests found`, 'info');
            console.log('All requests:', dashboardData.myRequests);
        }

        // View request history
        function viewRequestHistory() {
            const completedRequests = dashboardData.myRequests.filter(req => 
                req.status === 'approved' || req.status === 'denied'
            );
            
            showAlert(`📚 You have ${completedRequests.length} completed requests in history`, 'info');
        }

        // Export my requests
        function exportMyRequests() {
            if (dashboardData.myRequests.length === 0) {
                showAlert('📭 No requests to export', 'warning');
                return;
            }

            const csvContent = [
                ['Request ID', 'Item', 'Quantity', 'Priority', 'Status', 'Purpose', 'Date', 'Remarks'],
                ...dashboardData.myRequests.map(req => [
                    req.id,
                    req.item_name || 'Unknown',
                    req.quantity_requested || 0,
                    req.priority || 'Medium',
                    req.status || 'Pending',
                    req.purpose || 'No purpose',
                    formatDate(req.request_date),
                    req.remarks || 'None'
                ])
            ].map(row => row.join(',')).join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `My_Requests_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            showAlert('📊 Requests exported successfully!', 'success');
        }

        // Show new request modal
        function showNewRequestModal() {
            document.getElementById('newRequestModal').classList.add('show');
        }

        // Close new request modal
        function closeNewRequestModal() {
            document.getElementById('newRequestModal').classList.remove('show');
            document.getElementById('newRequestForm').reset();
        }

        // Refresh dashboard
        function refreshDashboard() {
            console.log('🔄 Refreshing department dashboard...');
            
            // Reset loading states
            document.getElementById('totalRequests').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 40px;"></div>';
            document.getElementById('pendingRequests').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 40px;"></div>';
            document.getElementById('approvedRequests').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 40px;"></div>';
            document.getElementById('deniedRequests').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 40px;"></div>';
            
            document.getElementById('recentRequestsContainer').innerHTML = '<div class="loading-skeleton" style="width: 100%; height: 200px;"></div>';
            
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

        // Modal click outside to close
        document.getElementById('newRequestModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeNewRequestModal();
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeNewRequestModal();
            }
        });

        // Make functions globally accessible
        window.refreshDashboard = refreshDashboard;
        window.showNewRequestModal = showNewRequestModal;
        window.closeNewRequestModal = closeNewRequestModal;
        window.submitNewRequest = submitNewRequest;
        window.viewRequest = viewRequest;
        window.trackRequests = trackRequests;
        window.filterRequests = filterRequests;
        window.showAllRequests = showAllRequests;
        window.viewRequestHistory = viewRequestHistory;
        window.exportMyRequests = exportMyRequests;

        // Initialize
        console.log('🏢 MSICT Department Dashboard Initialized');
        console.log('🔗 API Base URL:', API_BASE_URL);
        console.log('🔑 JWT Token Status:', token ? 'Present' : 'Missing');
        console.log('👤 User Role: Department');
        console.log('📡 Available API Endpoints:');
        console.log('  - POST /Department/api/requests/add - Submit new request');
        console.log('  - GET /Department/api/requests/my - View my requests');
        console.log('  - GET /Department/api/requests/view?id=X - View single request');
        console.log('  - GET /Department/api/requests/status - Track request status');
    </script>
</body>
</html>