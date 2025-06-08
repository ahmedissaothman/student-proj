<?php
// modules/quartermaster/dispatch-center.php - Dispatch Management
session_start();

// Check if user is logged in and is QuarterMaster
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'QuarterMaster') {
    header('Location: ../../auth/login.php');
    exit();
}

// Set user data for components
$_SESSION['username'] = $_SESSION['username'] ?? 'quartermaster001';
$_SESSION['full_name'] = $_SESSION['full_name'] ?? 'QuarterMaster';
$_SESSION['user_role'] = 'QuarterMaster';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispatch Center - MSICT Ordering System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* Essential Styles */
        :root {
            --primary-color: #2D5016;
            --secondary-color: #1e3c72;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --border-color: #dee2e6;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
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

        /* Stats Cards */
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
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-card.pending { --card-bg-1: #667eea; --card-bg-2: #764ba2; }
        .stat-card.today { --card-bg-1: #43e97b; --card-bg-2: #38f9d7; }
        .stat-card.total { --card-bg-1: #4facfe; --card-bg-2: #00f2fe; }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Tabs */
        .tabs {
            display: flex;
            background: var(--light-gray);
            border-radius: 10px;
            padding: 0.25rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .tab {
            flex: 1;
            padding: 0.75rem 1.5rem;
            background: transparent;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #666;
        }

        .tab.active {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 2px 8px rgba(45, 80, 22, 0.3);
        }

        .tab:hover:not(.active) {
            background: rgba(45, 80, 22, 0.1);
        }

        /* Tab Content */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
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
            background: var(--light-gray);
            border-bottom: 1px solid var(--border-color);
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

        /* Search and Filter */
        .search-filter-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            font-size: 0.9rem;
            transition: border-color 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        /* Table */
        .table-container {
            overflow-x: auto;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .table th {
            background: var(--light-gray);
            font-weight: 600;
            color: var(--primary-color);
            position: sticky;
            top: 0;
        }

        .table tbody tr:hover {
            background: rgba(45, 80, 22, 0.05);
        }

        /* Status Badges */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.authorized { background: #cce5ff; color: #0056b3; }
        .status-badge.dispatched { background: #d4edda; color: #155724; }
        .status-badge.pending { background: #fff3cd; color: #856404; }

        /* Priority Badges */
        .priority-badge {
            padding: 0.2rem 0.6rem;
            border-radius: 15px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .priority-badge.high { background: #f8d7da; color: #721c24; }
        .priority-badge.medium { background: #fff3cd; color: #856404; }
        .priority-badge.low { background: #d4edda; color: #155724; }

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

        .btn-primary { background: var(--primary-color); color: white; }
        .btn-success { background: var(--success-color); color: white; }
        .btn-info { background: var(--info-color); color: white; }
        .btn-warning { background: var(--warning-color); color: #212529; }
        .btn-danger { background: var(--danger-color); color: white; }

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

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
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
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }

        /* Request Details */
        .request-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .detail-item {
            padding: 1rem;
            background: var(--light-gray);
            border-radius: 8px;
        }

        .detail-label {
            font-size: 0.8rem;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .detail-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary-color);
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

        /* Loading */
        .loading {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        .spinner {
            display: inline-block;
            width: 2rem;
            height: 2rem;
            border: 3px solid var(--border-color);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 1rem;
            }
            .search-filter-bar {
                flex-direction: column;
            }
            .tabs {
                flex-direction: column;
            }
            .tab {
                text-align: center;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
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
        <div class="content-container">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-shipping-fast"></i>
                        Dispatch Center
                    </h1>
                </div>
                <div class="page-actions">
                    <button class="btn btn-info" onclick="refreshData()">
                        <i class="fas fa-sync"></i>
                        Refresh
                    </button>
                    <button class="btn btn-success" onclick="bulkDispatch()">
                        <i class="fas fa-truck"></i>
                        Bulk Dispatch
                    </button>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card pending">
                    <div class="stat-value" id="pendingDispatch">0</div>
                    <div class="stat-label">Ready to Dispatch</div>
                </div>
                <div class="stat-card today">
                    <div class="stat-value" id="dispatchedToday">0</div>
                    <div class="stat-label">Dispatched Today</div>
                </div>
                <div class="stat-card total">
                    <div class="stat-value" id="totalDispatched">0</div>
                    <div class="stat-label">Total Dispatched</div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab active" onclick="switchTab('ready')">
                    <i class="fas fa-clock"></i>
                    Ready to Dispatch
                </button>
                <button class="tab" onclick="switchTab('history')">
                    <i class="fas fa-history"></i>
                    Dispatch History
                </button>
                <button class="tab" onclick="switchTab('authorize')">
                    <i class="fas fa-check-circle"></i>
                    Authorize Requests
                </button>
            </div>

            <!-- Ready to Dispatch Tab -->
            <div id="ready" class="tab-content active">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-shipping-fast"></i>
                            Ready for Dispatch
                        </h3>
                        <div style="display: flex; gap: 0.5rem;">
                            <button class="btn btn-warning btn-sm" onclick="selectAll()">
                                <i class="fas fa-check-square"></i>
                                Select All
                            </button>
                            <button class="btn btn-success btn-sm" onclick="dispatchSelected()" id="dispatchSelectedBtn" disabled>
                                <i class="fas fa-truck"></i>
                                Dispatch Selected
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="search-filter-bar">
                            <div class="search-box">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" class="search-input" id="readySearch" placeholder="Search requests...">
                            </div>
                        </div>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                                        </th>
                                        <th>Request ID</th>
                                        <th>Item Name</th>
                                        <th>Quantity</th>
                                        <th>Requested By</th>
                                        <th>Request Date</th>
                                        <th>Priority</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="readyTable">
                                    <tr>
                                        <td colspan="8" class="loading">
                                            <div class="spinner"></div>
                                            <br>Loading ready requests...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dispatch History Tab -->
            <div id="history" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history"></i>
                            Dispatch History
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="search-filter-bar">
                            <div class="search-box">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" class="search-input" id="historySearch" placeholder="Search dispatch history...">
                            </div>
                        </div>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Item Name</th>
                                        <th>Quantity</th>
                                        <th>Dispatched By</th>
                                        <th>Dispatch Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="historyTable">
                                    <tr>
                                        <td colspan="6" class="loading">
                                            <div class="spinner"></div>
                                            <br>Loading dispatch history...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Authorize Requests Tab -->
            <div id="authorize" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-check-circle"></i>
                            Authorize Approved Requests
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert info">
                            <i class="fas fa-info-circle"></i>
                            <span>Only approved requests can be authorized for dispatch.</span>
                        </div>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Item Name</th>
                                        <th>Quantity</th>
                                        <th>Requested By</th>
                                        <th>Approved Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="authorizeTable">
                                    <tr>
                                        <td colspan="6" class="loading">
                                            <div class="spinner"></div>
                                            <br>Loading pending authorization...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Dispatch Confirmation Modal -->
    <div id="dispatchModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Dispatch</h3>
                <button class="close-btn" onclick="closeDispatchModal()">&times;</button>
            </div>
            <div id="dispatchDetails">
                <!-- Dispatch details will be populated here -->
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <button class="btn btn-secondary" onclick="closeDispatchModal()">Cancel</button>
                <button class="btn btn-success" onclick="confirmDispatch()" id="confirmDispatchBtn">
                    <i class="fas fa-shipping-fast"></i>
                    Confirm Dispatch
                </button>
            </div>
        </div>
    </div>

    <!-- Include Footer Component -->
    <?php include '../../dashboard/components/footer.php'; ?>

    <script>
        // API Configuration
        const API_BASE_URL = 'http://localhost/unfedZombie/Controllers/QuarterMaster/api';
        let readyRequests = [];
        let dispatchHistory = [];
        let authorizeRequests = [];
        let selectedRequests = [];

        // Get JWT token from session
        const token = '<?php echo $_SESSION["jwt_token"] ?? ""; ?>';

        // API Headers
        const headers = {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        };

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚚 Dispatch Center Loading...');
            loadAllData();
            setupEventListeners();
        });

        // Load all data
        async function loadAllData() {
            try {
                showAlert('🔄 Loading dispatch data...', 'info');
                
                const [ready, history, authorize] = await Promise.allSettled([
                    loadReadyRequests(),
                    loadDispatchHistory(),
                    loadAuthorizeRequests()
                ]);

                if (ready.status === 'fulfilled') {
                    readyRequests = ready.value;
                    displayReadyRequests();
                    updateStats();
                }

                if (history.status === 'fulfilled') {
                    dispatchHistory = history.value;
                    displayDispatchHistory();
                }

                if (authorize.status === 'fulfilled') {
                    authorizeRequests = authorize.value;
                    displayAuthorizeRequests();
                }

                showAlert('✅ Data loaded successfully!', 'success');
                
            } catch (error) {
                console.error('Error loading data:', error);
                showAlert('❌ Error loading data: ' + error.message, 'error');
            }
        }

        // Load ready requests
        async function loadReadyRequests() {
            const response = await fetch(`${API_BASE_URL}/requests/ready`, {
                method: 'GET',
                headers: headers
            });

            if (!response.ok) {
                throw new Error(`Ready requests API Error: ${response.status}`);
            }

            return await response.json();
        }

        // Load dispatch history (placeholder - you might need a new endpoint)
        async function loadDispatchHistory() {
            // This would need a new endpoint like /dispatch/history
            // For now, return empty array
            return [];
        }

        // Load authorize requests (placeholder - approved but not authorized)
        async function loadAuthorizeRequests() {
            // This would need filtering approved but not authorized requests
            // For now, return empty array
            return [];
        }

        // Update statistics
        function updateStats() {
            document.getElementById('pendingDispatch').textContent = readyRequests.length;
            document.getElementById('dispatchedToday').textContent = '0'; // Placeholder
            document.getElementById('totalDispatched').textContent = dispatchHistory.length;
        }

        // Display ready requests
        function displayReadyRequests() {
            const tbody = document.getElementById('readyTable');
            
            if (readyRequests.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 2rem; color: green;"><i class="fas fa-check-circle"></i><br>No requests ready for dispatch!</td></tr>';
                return;
            }

            tbody.innerHTML = readyRequests.map(request => `
                <tr>
                    <td>
                        <input type="checkbox" class="request-checkbox" value="${request.id}" onchange="updateSelectedRequests()">
                    </td>
                    <td><strong>#${request.id}</strong></td>
                    <td>${request.item_name}</td>
                    <td>${request.quantity_requested}</td>
                    <td>User #${request.requested_by}</td>
                    <td>${formatDate(request.created_at || new Date())}</td>
                    <td><span class="priority-badge medium">Medium</span></td>
                    <td>
                        <button class="btn btn-success btn-sm" onclick="dispatchSingle(${request.id})" title="Dispatch Now">
                            <i class="fas fa-shipping-fast"></i>
                            Dispatch
                        </button>
                        <button class="btn btn-info btn-sm" onclick="viewRequestDetails(${request.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Display dispatch history
        function displayDispatchHistory() {
            const tbody = document.getElementById('historyTable');
            
            if (dispatchHistory.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem;">No dispatch history available</td></tr>';
                return;
            }

            tbody.innerHTML = dispatchHistory.map(dispatch => `
                <tr>
                    <td><strong>#${dispatch.request_id}</strong></td>
                    <td>${dispatch.item_name}</td>
                    <td>${dispatch.quantity}</td>
                    <td>User #${dispatch.dispatched_by}</td>
                    <td>${formatDate(dispatch.dispatched_at)}</td>
                    <td><span class="status-badge dispatched">Dispatched</span></td>
                </tr>
            `).join('');
        }

        // Display authorize requests
        function displayAuthorizeRequests() {
            const tbody = document.getElementById('authorizeTable');
            
            if (authorizeRequests.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem;">No requests pending authorization</td></tr>';
                return;
            }

            tbody.innerHTML = authorizeRequests.map(request => `
                <tr>
                    <td><strong>#${request.id}</strong></td>
                    <td>${request.item_name}</td>
                    <td>${request.quantity_requested}</td>
                    <td>User #${request.requested_by}</td>
                    <td>${formatDate(request.approved_at)}</td>
                    <td>
                        <button class="btn btn-success btn-sm" onclick="authorizeRequest(${request.id})" title="Authorize">
                            <i class="fas fa-check"></i>
                            Authorize
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Tab switching
        function switchTab(tabName) {
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById(tabName).classList.add('active');
        }

        // Setup event listeners
        function setupEventListeners() {
            document.getElementById('readySearch').addEventListener('input', filterReadyRequests);
            document.getElementById('historySearch').addEventListener('input', filterDispatchHistory);
        }

        // Filter ready requests
        function filterReadyRequests() {
            const searchTerm = document.getElementById('readySearch').value.toLowerCase();
            const filteredData = readyRequests.filter(request => 
                request.item_name.toLowerCase().includes(searchTerm) ||
                request.id.toString().includes(searchTerm)
            );
            displayFilteredReadyRequests(filteredData);
        }

        // Display filtered ready requests
        function displayFilteredReadyRequests(data) {
            const tbody = document.getElementById('readyTable');
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 2rem;">No requests match your search</td></tr>';
                return;
            }

            tbody.innerHTML = data.map(request => `
                <tr>
                    <td>
                        <input type="checkbox" class="request-checkbox" value="${request.id}" onchange="updateSelectedRequests()">
                    </td>
                    <td><strong>#${request.id}</strong></td>
                    <td>${request.item_name}</td>
                    <td>${request.quantity_requested}</td>
                    <td>User #${request.requested_by}</td>
                    <td>${formatDate(request.created_at || new Date())}</td>
                    <td><span class="priority-badge medium">Medium</span></td>
                    <td>
                        <button class="btn btn-success btn-sm" onclick="dispatchSingle(${request.id})" title="Dispatch Now">
                            <i class="fas fa-shipping-fast"></i>
                            Dispatch
                        </button>
                        <button class="btn btn-info btn-sm" onclick="viewRequestDetails(${request.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Filter dispatch history
        function filterDispatchHistory() {
            const searchTerm = document.getElementById('historySearch').value.toLowerCase();
            const filteredData = dispatchHistory.filter(dispatch => 
                dispatch.item_name.toLowerCase().includes(searchTerm) ||
                dispatch.request_id.toString().includes(searchTerm)
            );
            displayFilteredDispatchHistory(filteredData);
        }

        // Display filtered dispatch history
        function displayFilteredDispatchHistory(data) {
            const tbody = document.getElementById('historyTable');
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem;">No history matches your search</td></tr>';
                return;
            }

            tbody.innerHTML = data.map(dispatch => `
                <tr>
                    <td><strong>#${dispatch.request_id}</strong></td>
                    <td>${dispatch.item_name}</td>
                    <td>${dispatch.quantity}</td>
                    <td>User #${dispatch.dispatched_by}</td>
                    <td>${formatDate(dispatch.dispatched_at)}</td>
                    <td><span class="status-badge dispatched">Dispatched</span></td>
                </tr>
            `).join('');
        }

        // Selection functions
        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const checkboxes = document.querySelectorAll('.request-checkbox');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            
            updateSelectedRequests();
        }

        function selectAll() {
            document.getElementById('selectAllCheckbox').checked = true;
            toggleSelectAll();
        }

        function updateSelectedRequests() {
            const checkboxes = document.querySelectorAll('.request-checkbox:checked');
            selectedRequests = Array.from(checkboxes).map(cb => parseInt(cb.value));
            
            const dispatchSelectedBtn = document.getElementById('dispatchSelectedBtn');
            dispatchSelectedBtn.disabled = selectedRequests.length === 0;
            dispatchSelectedBtn.textContent = selectedRequests.length > 0 
                ? `Dispatch Selected (${selectedRequests.length})` 
                : 'Dispatch Selected';
        }

        // Dispatch functions
        async function dispatchSingle(requestId) {
            const request = readyRequests.find(r => r.id === requestId);
            if (!request) return;

            showDispatchModal([request]);
        }

        function dispatchSelected() {
            if (selectedRequests.length === 0) {
                showAlert('⚠️ Please select at least one request to dispatch', 'warning');
                return;
            }

            const requests = readyRequests.filter(r => selectedRequests.includes(r.id));
            showDispatchModal(requests);
        }

        function bulkDispatch() {
            if (readyRequests.length === 0) {
                showAlert('⚠️ No requests available for dispatch', 'warning');
                return;
            }

            showDispatchModal(readyRequests);
        }

        // Show dispatch confirmation modal
        function showDispatchModal(requests) {
            const modal = document.getElementById('dispatchModal');
            const detailsContainer = document.getElementById('dispatchDetails');
            
            let totalItems = 0;
            let detailsHTML = `
                <div class="request-details">
                    <div class="detail-item">
                        <div class="detail-label">Total Requests</div>
                        <div class="detail-value">${requests.length}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Total Items</div>
                        <div class="detail-value">${requests.reduce((sum, r) => sum + parseInt(r.quantity_requested), 0)}</div>
                    </div>
                </div>

                <h4 style="margin: 1.5rem 0 1rem 0; color: var(--primary-color);">Items to Dispatch:</h4>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Requestor</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${requests.map(request => `
                                <tr>
                                    <td><strong>#${request.id}</strong></td>
                                    <td>${request.item_name}</td>
                                    <td>${request.quantity_requested}</td>
                                    <td>User #${request.requested_by}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;

            detailsContainer.innerHTML = detailsHTML;
            
            // Store requests for confirmation
            window.pendingDispatchRequests = requests;
            
            modal.classList.add('show');
        }

        function closeDispatchModal() {
            document.getElementById('dispatchModal').classList.remove('show');
            window.pendingDispatchRequests = null;
        }

        // Confirm dispatch
        async function confirmDispatch() {
            if (!window.pendingDispatchRequests) return;

            const confirmBtn = document.getElementById('confirmDispatchBtn');
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Dispatching...';

            try {
                let successCount = 0;
                let errorCount = 0;

                for (const request of window.pendingDispatchRequests) {
                    try {
                        const response = await fetch(`${API_BASE_URL}/dispatch/item`, {
                            method: 'POST',
                            headers: headers,
                            body: JSON.stringify({
                                request_id: request.id
                            })
                        });

                        if (response.ok) {
                            successCount++;
                        } else {
                            errorCount++;
                            console.error(`Failed to dispatch request ${request.id}`);
                        }
                    } catch (error) {
                        errorCount++;
                        console.error(`Error dispatching request ${request.id}:`, error);
                    }
                }

                closeDispatchModal();

                if (successCount > 0) {
                    showAlert(`✅ Successfully dispatched ${successCount} request(s)!`, 'success');
                }
                if (errorCount > 0) {
                    showAlert(`⚠️ Failed to dispatch ${errorCount} request(s)`, 'warning');
                }

                // Reset selections
                selectedRequests = [];
                document.getElementById('selectAllCheckbox').checked = false;
                updateSelectedRequests();

                // Refresh data
                await loadAllData();

            } catch (error) {
                console.error('Error during dispatch:', error);
                showAlert('❌ Error during dispatch: ' + error.message, 'error');
            } finally {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fas fa-shipping-fast"></i> Confirm Dispatch';
            }
        }

        // Authorize request
        async function authorizeRequest(requestId) {
            if (!confirm('Are you sure you want to authorize this request for dispatch?')) {
                return;
            }

            try {
                const response = await fetch(`${API_BASE_URL}/requests/authorize`, {
                    method: 'PUT',
                    headers: headers,
                    body: JSON.stringify({
                        request_id: requestId
                    })
                });

                if (response.ok) {
                    showAlert('✅ Request authorized successfully!', 'success');
                    await loadAllData(); // Refresh data
                } else {
                    const error = await response.json();
                    showAlert('❌ Error authorizing request: ' + (error.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error authorizing request:', error);
                showAlert('❌ Error authorizing request: ' + error.message, 'error');
            }
        }

        // View request details
        function viewRequestDetails(requestId) {
            const request = readyRequests.find(r => r.id === requestId);
            if (!request) return;

            // You can create a detailed view modal here
            showAlert(`📋 Request Details: #${request.id} - ${request.item_name} (${request.quantity_requested} units)`, 'info');
        }

        // Utility functions
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (error) {
                return 'Invalid Date';
            }
        }

        // Refresh data
        async function refreshData() {
            showAlert('🔄 Refreshing data...', 'info');
            await loadAllData();
        }

        // Show alert
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
            
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 3000);
        }

        // Make functions globally accessible
        window.switchTab = switchTab;
        window.dispatchSingle = dispatchSingle;
        window.dispatchSelected = dispatchSelected;
        window.bulkDispatch = bulkDispatch;
        window.selectAll = selectAll;
        window.toggleSelectAll = toggleSelectAll;
        window.updateSelectedRequests = updateSelectedRequests;
        window.closeDispatchModal = closeDispatchModal;
        window.confirmDispatch = confirmDispatch;
        window.authorizeRequest = authorizeRequest;
        window.viewRequestDetails = viewRequestDetails;
        window.refreshData = refreshData;

        console.log('🚚 Dispatch Center Initialized');
    </script>
</body>
</html>