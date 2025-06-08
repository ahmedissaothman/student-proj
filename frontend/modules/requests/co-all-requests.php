<?php
// modules/requests/co-all-requests.php
session_start();

// Check if user is logged in and is CO
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'CO') {
    header('Location: ../../auth/login.php');
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
    <title>All Requests - CO Dashboard - MSICT</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* CO All Requests Page Styles */
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
        .requests-container {
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

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Table Styles */
        .table-container {
            overflow-x: auto;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
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
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
            z-index: 10;
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

        /* Tabs */
        .tabs-container {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .tabs-header {
            display: flex;
            background: linear-gradient(135deg, var(--light-gray), #e9ecef);
            border-bottom: 1px solid #eee;
        }

        .tab-button {
            flex: 1;
            padding: 1rem 2rem;
            background: none;
            border: none;
            cursor: pointer;
            font-weight: 600;
            color: #666;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .tab-button.active {
            background: var(--primary-color);
            color: white;
        }

        .tab-button:hover:not(.active) {
            background: rgba(45, 80, 22, 0.1);
            color: var(--primary-color);
        }

        .tab-content {
            display: none;
            padding: 2rem;
        }

        .tab-content.active {
            display: block;
        }

        /* Search and Filter */
        .search-filter-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-input {
            flex: 1;
            min-width: 250px;
            padding: 0.75rem 1rem;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(45, 80, 22, 0.1);
        }

        .filter-select {
            padding: 0.75rem 1rem;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 0.9rem;
            background: white;
            cursor: pointer;
        }

        /* Stats Cards */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--card-bg-1), var(--card-bg-2));
            border-radius: 12px;
            padding: 2rem;
            color: white;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.pending-requests { --card-bg-1: #ff7675; --card-bg-2: #fd79a8; }
        .stat-card.my-approved { --card-bg-1: #00b894; --card-bg-2: #00cec9; }
        .stat-card.my-denied { --card-bg-1: #fdcb6e; --card-bg-2: #e17055; }
        .stat-card.total-processed { --card-bg-1: #6c5ce7; --card-bg-2: #a29bfe; }

        .stat-icon {
            font-size: 2.5rem;
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

        /* Bulk Actions */
        .bulk-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            margin-bottom: 1rem;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .requests-container {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .page-actions {
                justify-content: center;
            }

            .search-filter-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-input {
                min-width: 100%;
            }

            .table th,
            .table td {
                padding: 0.5rem 0.25rem;
                font-size: 0.8rem;
            }

            .tabs-header {
                flex-wrap: wrap;
            }

            .tab-button {
                min-width: 120px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .stats-row {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }

            .stat-card {
                padding: 1.5rem;
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
    <?php include '../../dashboard/components/sidebar.php'; ?>

    <!-- Include Header Component -->
    <?php include '../../dashboard/components/header.php'; ?>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="requests-container">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-clipboard-check"></i>
                        CO Request Management
                    </h1>
                    <p class="page-subtitle">Approve and deny requests as Chief Officer</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-info" onclick="refreshAllData()">
                        <i class="fas fa-sync"></i>
                        Refresh
                    </button>
                    <button class="btn btn-primary" onclick="exportCOReport()">
                        <i class="fas fa-download"></i>
                        Export Report
                    </button>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- Request Statistics -->
            <div class="stats-row">
                <div class="stat-card pending-requests" onclick="switchTab('pending')">
                    <div class="stat-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-value" id="pendingCount">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Pending Approval</div>
                </div>
                
                <div class="stat-card my-approved" onclick="switchTab('processed')">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value" id="approvedCount">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Approved by Me</div>
                </div>
                
                <div class="stat-card my-denied" onclick="switchTab('processed')">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-value" id="deniedCount">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Denied by Me</div>
                </div>
                
                <div class="stat-card total-processed" onclick="switchTab('processed')">
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stat-value" id="totalProcessed">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Total Processed</div>
                </div>
            </div>

            <!-- Tabs Container -->
            <div class="tabs-container">
                <div class="tabs-header">
                    <button class="tab-button active" onclick="switchTab('pending')">
                        <i class="fas fa-hourglass-half"></i>
                        Pending Requests
                    </button>
                    <button class="tab-button" onclick="switchTab('processed')">
                        <i class="fas fa-history"></i>
                        Processed Requests
                    </button>
                </div>

                <!-- Pending Requests Tab -->
                <div class="tab-content active" id="pendingTab">
                    <!-- Search and Filter for Pending -->
                    <div class="search-filter-bar">
                        <input type="text" id="pendingSearchInput" class="search-input" placeholder="🔍 Search pending requests...">
                        <select id="pendingPriorityFilter" class="filter-select">
                            <option value="">All Priorities</option>
                            <option value="high">High Priority</option>
                            <option value="medium">Medium Priority</option>
                            <option value="low">Low Priority</option>
                        </select>
                    </div>

                    <!-- Bulk Actions for Pending -->
                    <div class="bulk-actions">
                        <div class="checkbox-container">
                            <input type="checkbox" id="selectAllPending" onchange="toggleSelectAllPending()">
                            <label for="selectAllPending">Select All</label>
                        </div>
                        <button class="btn btn-success btn-sm" onclick="approveSelected()" id="approveSelectedBtn" disabled>
                            <i class="fas fa-check"></i>
                            Approve Selected
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="denySelected()" id="denySelectedBtn" disabled>
                            <i class="fas fa-times"></i>
                            Deny Selected
                        </button>
                    </div>

                    <!-- Pending Requests Table -->
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="50px">
                                        <input type="checkbox" id="selectAllPendingHeader" onchange="toggleSelectAllPending()">
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
                            <tbody id="pendingRequestsBody">
                                <tr>
                                    <td colspan="9" style="text-align: center; padding: 2rem;">
                                        <i class="fas fa-spinner fa-spin"></i> Loading pending requests...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Processed Requests Tab -->
                <div class="tab-content" id="processedTab">
                    <!-- Search and Filter for Processed -->
                    <div class="search-filter-bar">
                        <input type="text" id="processedSearchInput" class="search-input" placeholder="🔍 Search processed requests...">
                        <select id="processedStatusFilter" class="filter-select">
                            <option value="">All Status</option>
                            <option value="approved">Approved</option>
                            <option value="denied">Denied</option>
                        </select>
                        <select id="processedDateFilter" class="filter-select">
                            <option value="">All Dates</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>
                    </div>

                    <!-- Processed Requests Table -->
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Request ID</th>
                                    <th>Requested By</th>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Processed Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="processedRequestsBody">
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem;">
                                        <i class="fas fa-spinner fa-spin"></i> Loading processed requests...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Include Footer Component -->
    <?php include '../../dashboard/components/footer.php'; ?>

    <script>
        // API Configuration
        const API_BASE_URL = 'http://localhost/unfedZombie/Controllers';
        let allData = {
            pendingRequests: [],
            processedRequests: [],
            selectedRequests: []
        };

        // Get JWT token from session
        const token = '<?php echo $_SESSION["jwt_token"] ?? ""; ?>';

        // API Headers
        const headers = {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        };

        // Load data on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('👔 CO Request Management Loading...');
            loadAllData();
            setupEventListeners();
        });

        // Setup event listeners
        function setupEventListeners() {
            // Search and filter functionality
            document.getElementById('pendingSearchInput').addEventListener('input', filterPendingRequests);
            document.getElementById('pendingPriorityFilter').addEventListener('change', filterPendingRequests);
            
            document.getElementById('processedSearchInput').addEventListener('input', filterProcessedRequests);
            document.getElementById('processedStatusFilter').addEventListener('change', filterProcessedRequests);
            document.getElementById('processedDateFilter').addEventListener('change', filterProcessedRequests);
        }

        // Load all data
        async function loadAllData() {
            try {
                showAlert('🔄 Loading CO requests...', 'info');
                
                await Promise.all([
                    loadPendingRequests(),
                    loadProcessedRequests()
                ]);

                updateStatistics();
                showAlert('✅ Data loaded successfully!', 'success');
                
            } catch (error) {
                console.error('Error loading data:', error);
                showAlert('❌ Error loading data: ' + error.message, 'error');
            }
        }

        // Load pending requests
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
                allData.pendingRequests = Array.isArray(requests) ? requests : [];
                
                displayPendingRequests();
                
            } catch (error) {
                console.error('Error loading pending requests:', error);
                allData.pendingRequests = [];
                displayPendingRequests();
            }
        }

        // Load processed requests
        async function loadProcessedRequests() {
            try {
                const response = await fetch(`${API_BASE_URL}/CO/api/requests/approved`, {
                    method: 'GET',
                    headers: headers
                });

                if (!response.ok) {
                    throw new Error(`Processed Requests API Error: ${response.status}`);
                }

                const requests = await response.json();
                allData.processedRequests = Array.isArray(requests) ? requests : [];
                
                displayProcessedRequests();
                
            } catch (error) {
                console.error('Error loading processed requests:', error);
                allData.processedRequests = [];
                displayProcessedRequests();
            }
        }

        // Display pending requests
        function displayPendingRequests() {
            const tbody = document.getElementById('pendingRequestsBody');
            
            if (!allData.pendingRequests || allData.pendingRequests.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <h3>No Pending Requests</h3>
                            <p>All requests have been processed!</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = allData.pendingRequests.map(request => `
                <tr>
                    <td>
                        <input type="checkbox" class="request-select" value="${request.id}" onchange="updateSelectedRequests()">
                    </td>
                    <td><strong>#${request.id}</strong></td>
                    <td>${request.requested_by || 'Unknown User'}</td>
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
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Display processed requests
        function displayProcessedRequests() {
            const tbody = document.getElementById('processedRequestsBody');
            
            if (!allData.processedRequests || allData.processedRequests.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>No Processed Requests</h3>
                            <p>You haven't processed any requests yet.</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = allData.processedRequests.map(request => `
                <tr>
                    <td><strong>#${request.id}</strong></td>
                    <td>${request.requested_by || 'Unknown User'}</td>
                    <td>${request.item_name || 'N/A'}</td>
                    <td><strong>${request.quantity_requested || 0}</strong></td>
                    <td><span class="status-badge ${request.status}">${request.status || 'Unknown'}</span></td>
                    <td>${formatDate(request.approved_at)}</td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="viewRequestDetails(${request.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Update statistics
        function updateStatistics() {
            const pending = allData.pendingRequests.length;
            const approved = allData.processedRequests.filter(r => r.status === 'approved').length;
            const denied = allData.processedRequests.filter(r => r.status === 'denied').length;
            const total = allData.processedRequests.length;

            document.getElementById('pendingCount').textContent = pending;
            document.getElementById('approvedCount').textContent = approved;
            document.getElementById('deniedCount').textContent = denied;
            document.getElementById('totalProcessed').textContent = total;
        }

        // Switch tabs
        function switchTab(tabName) {
            // Remove active class from all tabs and buttons
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            // Add active class to selected tab
            document.querySelector(`[onclick="switchTab('${tabName}')"]`).classList.add('active');
            document.getElementById(tabName + 'Tab').classList.add('active');

            console.log(`🔄 Switched to ${tabName} tab`);
        }

        // Toggle select all pending requests
        function toggleSelectAllPending() {
            const selectAll = document.getElementById('selectAllPending');
            const selectAllHeader = document.getElementById('selectAllPendingHeader');
            const checkboxes = document.querySelectorAll('.request-select');
            
            // Sync both select all checkboxes
            selectAll.checked = selectAllHeader.checked;
            selectAllHeader.checked = selectAll.checked;
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            
            updateSelectedRequests();
        }

        // Update selected requests
        function updateSelectedRequests() {
            const checkboxes = document.querySelectorAll('.request-select:checked');
            allData.selectedRequests = Array.from(checkboxes).map(cb => parseInt(cb.value));
            
            // Update button states
            const approveBtn = document.getElementById('approveSelectedBtn');
            const denyBtn = document.getElementById('denySelectedBtn');
            
            if (allData.selectedRequests.length > 0) {
                approveBtn.disabled = false;
                denyBtn.disabled = false;
            } else {
                approveBtn.disabled = true;
                denyBtn.disabled = true;
            }
        }

        // Approve single request
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
                await loadAllData();
                
            } catch (error) {
                console.error('Error approving request:', error);
                showAlert('❌ Error approving request: ' + error.message, 'error');
            }
        }

        // Deny single request
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
                await loadAllData();
                
            } catch (error) {
                console.error('Error denying request:', error);
                showAlert('❌ Error denying request: ' + error.message, 'error');
            }
        }

        // Approve selected requests
        async function approveSelected() {
            if (allData.selectedRequests.length === 0) {
                showAlert('⚠️ No requests selected', 'warning');
                return;
            }

            if (!confirm(`Are you sure you want to approve ${allData.selectedRequests.length} selected requests?`)) {
                return;
            }

            try {
                showAlert('🔄 Approving selected requests...', 'info');
                
                const promises = allData.selectedRequests.map(requestId =>
                    fetch(`${API_BASE_URL}/CO/api/requests/approve?id=${requestId}`, {
                        method: 'PUT',
                        headers: headers
                    })
                );

                await Promise.all(promises);
                showAlert(`✅ ${allData.selectedRequests.length} requests approved successfully!`, 'success');
                
                // Reset selection and refresh
                allData.selectedRequests = [];
                await loadAllData();
                
            } catch (error) {
                console.error('Error approving requests:', error);
                showAlert('❌ Error approving some requests: ' + error.message, 'error');
            }
        }

        // Deny selected requests
        async function denySelected() {
            if (allData.selectedRequests.length === 0) {
                showAlert('⚠️ No requests selected', 'warning');
                return;
            }

            const remarks = prompt('Please provide a reason for denying these requests:');
            if (!remarks) {
                return;
            }

            try {
                showAlert('🔄 Denying selected requests...', 'info');
                
                const promises = allData.selectedRequests.map(requestId =>
                    fetch(`${API_BASE_URL}/CO/api/requests/deny?id=${requestId}`, {
                        method: 'PUT',
                        headers: headers,
                        body: JSON.stringify({ remarks: remarks })
                    })
                );

                await Promise.all(promises);
                showAlert(`✅ ${allData.selectedRequests.length} requests denied successfully!`, 'success');
                
                // Reset selection and refresh
                allData.selectedRequests = [];
                await loadAllData();
                
            } catch (error) {
                console.error('Error denying requests:', error);
                showAlert('❌ Error denying some requests: ' + error.message, 'error');
            }
        }

        // Filter pending requests
        function filterPendingRequests() {
            const searchTerm = document.getElementById('pendingSearchInput').value.toLowerCase();
            const priorityFilter = document.getElementById('pendingPriorityFilter').value;

            const filteredRequests = allData.pendingRequests.filter(request => {
                const matchesSearch = !searchTerm || 
                    request.id.toString().includes(searchTerm) ||
                    (request.requested_by || '').toLowerCase().includes(searchTerm) ||
                    (request.item_name || '').toLowerCase().includes(searchTerm) ||
                    (request.purpose || '').toLowerCase().includes(searchTerm);

                const matchesPriority = !priorityFilter || (request.priority || 'medium') === priorityFilter;

                return matchesSearch && matchesPriority;
            });

            // Update display with filtered results
            const tbody = document.getElementById('pendingRequestsBody');
            
            if (filteredRequests.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="empty-state">
                            <i class="fas fa-search"></i>
                            <h3>No Matching Requests</h3>
                            <p>Try adjusting your search criteria</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = filteredRequests.map(request => `
                <tr>
                    <td>
                        <input type="checkbox" class="request-select" value="${request.id}" onchange="updateSelectedRequests()">
                    </td>
                    <td><strong>#${request.id}</strong></td>
                    <td>${request.requested_by || 'Unknown User'}</td>
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
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Filter processed requests
        function filterProcessedRequests() {
            const searchTerm = document.getElementById('processedSearchInput').value.toLowerCase();
            const statusFilter = document.getElementById('processedStatusFilter').value;
            const dateFilter = document.getElementById('processedDateFilter').value;

            const filteredRequests = allData.processedRequests.filter(request => {
                const matchesSearch = !searchTerm || 
                    request.id.toString().includes(searchTerm) ||
                    (request.requested_by || '').toLowerCase().includes(searchTerm) ||
                    (request.item_name || '').toLowerCase().includes(searchTerm);

                const matchesStatus = !statusFilter || request.status === statusFilter;

                let matchesDate = true;
                if (dateFilter && request.approved_at) {
                    const requestDate = new Date(request.approved_at);
                    const today = new Date();
                    const todayStart = new Date(today.getFullYear(), today.getMonth(), today.getDate());
                    
                    switch(dateFilter) {
                        case 'today':
                            matchesDate = requestDate >= todayStart;
                            break;
                        case 'week':
                            const weekStart = new Date(todayStart);
                            weekStart.setDate(weekStart.getDate() - 7);
                            matchesDate = requestDate >= weekStart;
                            break;
                        case 'month':
                            const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
                            matchesDate = requestDate >= monthStart;
                            break;
                    }
                }

                return matchesSearch && matchesStatus && matchesDate;
            });

            // Update display with filtered results
            const tbody = document.getElementById('processedRequestsBody');
            
            if (filteredRequests.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="fas fa-search"></i>
                            <h3>No Matching Requests</h3>
                            <p>Try adjusting your search criteria</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = filteredRequests.map(request => `
                <tr>
                    <td><strong>#${request.id}</strong></td>
                    <td>${request.requested_by || 'Unknown User'}</td>
                    <td>${request.item_name || 'N/A'}</td>
                    <td><strong>${request.quantity_requested || 0}</strong></td>
                    <td><span class="status-badge ${request.status}">${request.status || 'Unknown'}</span></td>
                    <td>${formatDate(request.approved_at)}</td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="viewRequestDetails(${request.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // View request details
        function viewRequestDetails(requestId) {
            // Navigate to request details page
            window.location.href = `../requests/view-request.php?id=${requestId}`;
        }

        // Export CO report
        function exportCOReport() {
            const stats = {
                pending: allData.pendingRequests.length,
                approved: allData.processedRequests.filter(r => r.status === 'approved').length,
                denied: allData.processedRequests.filter(r => r.status === 'denied').length,
                total: allData.processedRequests.length,
                date: new Date().toLocaleDateString(),
                officer: '<?php echo $_SESSION["full_name"] ?? "Chief Officer"; ?>'
            };

            const csvContent = [
                ['MSICT CO Request Management Report'],
                ['Generated:', stats.date],
                ['Officer:', stats.officer],
                [''],
                ['Summary'],
                ['Metric', 'Count'],
                ['Pending Requests', stats.pending],
                ['Approved Requests', stats.approved],
                ['Denied Requests', stats.denied],
                ['Total Processed', stats.total],
                [''],
                ['Pending Requests'],
                ['Request ID', 'Requested By', 'Item', 'Quantity', 'Priority', 'Purpose', 'Date'],
                ...allData.pendingRequests.map(req => [
                    req.id,
                    req.requested_by || 'Unknown',
                    req.item_name || 'N/A',
                    req.quantity_requested || 0,
                    req.priority || 'Medium',
                    req.purpose || 'No purpose',
                    formatDate(req.request_date)
                ]),
                [''],
                ['Recent Processed Requests'],
                ['Request ID', 'Requested By', 'Item', 'Status', 'Date Processed'],
                ...allData.processedRequests.slice(0, 20).map(req => [
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
            a.download = `MSICT_CO_Request_Report_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            showAlert('📊 CO report exported successfully!', 'success');
        }

        // Refresh all data
        function refreshAllData() {
            console.log('🔄 Refreshing CO request data...');
            
            // Reset loading states
            document.getElementById('pendingCount').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 40px;"></div>';
            document.getElementById('approvedCount').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 40px;"></div>';
            document.getElementById('deniedCount').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 40px;"></div>';
            document.getElementById('totalProcessed').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 40px;"></div>';
            
            // Reset table loading states
            document.getElementById('pendingRequestsBody').innerHTML = `
                <tr>
                    <td colspan="9" style="text-align: center; padding: 2rem;">
                        <i class="fas fa-spinner fa-spin"></i> Loading pending requests...
                    </td>
                </tr>
            `;
            
            document.getElementById('processedRequestsBody').innerHTML = `
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem;">
                        <i class="fas fa-spinner fa-spin"></i> Loading processed requests...
                    </td>
                </tr>
            `;
            
            // Reload data
            loadAllData();
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
        window.refreshAllData = refreshAllData;
        window.exportCOReport = exportCOReport;
        window.switchTab = switchTab;
        window.toggleSelectAllPending = toggleSelectAllPending;
        window.updateSelectedRequests = updateSelectedRequests;
        window.approveRequest = approveRequest;
        window.denyRequest = denyRequest;
        window.approveSelected = approveSelected;
        window.denySelected = denySelected;
        window.viewRequestDetails = viewRequestDetails;

        // Initialize
        console.log('👔 MSICT CO Request Management Initialized');
        console.log('🔗 API Base URL:', API_BASE_URL);
        console.log('🔑 JWT Token Status:', token ? 'Present' : 'Missing');
        console.log('👤 User Role: CO (Chief Officer)');
    </script>
</body>
</html>