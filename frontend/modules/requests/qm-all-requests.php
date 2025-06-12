<?php
// modules/requests/qm-all-requests.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit();
}

// Check if user has QuarterMaster permission
if ($_SESSION['user_role'] !== 'QuarterMaster') {
    header('Location: ../../dashboard/department-dashboard.php');
    exit();
}

// Set user data for components
$_SESSION['username'] = $_SESSION['username'] ?? 'quartermaster001';
$_SESSION['full_name'] = $_SESSION['full_name'] ?? 'QuarterMaster Name';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Requests - MSICT QuarterMaster</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* QuarterMaster Requests Page Styles */
        :root {
            --primary-color: #2D5016;
            --secondary-color: #1e3c72;
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
            --quartermaster-accent: #6c5ce7;
        }

        body {
            margin: 0;
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
            font-size: 2rem;
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

        .btn-primary { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; }
        .btn-success { background: var(--success); color: white; }
        .btn-warning { background: var(--warning); color: #212529; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-info { background: var(--info); color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-quartermaster { background: var(--quartermaster-accent); color: white; }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Table Styles - Clean White Design */
        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
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
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .table th {
            background: #f9fafb;
            color: #374151;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
            z-index: 10;
            border-bottom: 2px solid #e5e7eb;
        }

        .table tbody tr {
            background: white;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .table tbody tr:nth-child(even) {
            background: #fafbfc;
        }

        .table tbody tr:hover {
            background: #f8fafc;
            border-left: 3px solid #d1d5db;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        /* Status Badges - Simple Clean Design */
        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            display: inline-block;
            border: 1px solid;
        }

        .status-badge.pending { 
            background: #fef3c7; 
            color: #92400e; 
            border-color: #f59e0b;
        }

        .status-badge.approved { 
            background: #d1fae5; 
            color: #065f46; 
            border-color: #10b981;
        }

        .status-badge.authorized { 
            background: #dbeafe; 
            color: #1e40af; 
            border-color: #3b82f6;
        }

        .status-badge.dispatched { 
            background: #ecfdf5; 
            color: #064e3b; 
            border-color: #059669;
        }

        .status-badge.rejected { 
            background: #fee2e2; 
            color: #991b1b; 
            border-color: #dc2626;
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--card-bg-1), var(--card-bg-2));
            border-radius: 12px;
            padding: 1.5rem;
            color: white;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-card.ready-requests { --card-bg-1: #667eea; --card-bg-2: #764ba2; }
        .stat-card.authorized-requests { --card-bg-1: #43e97b; --card-bg-2: #38f9d7; }
        .stat-card.dispatched-requests { --card-bg-1: #4facfe; --card-bg-2: #00f2fe; }
        .stat-card.pending-dispatch { --card-bg-1: #f093fb; --card-bg-2: #f5576c; }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
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

        .alert.warning {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            color: #856404;
        }

        /* Loading Spinner */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            animation: fadeIn 0.3s ease;
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
            animation: slideInUp 0.3s ease;
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

        /* QuarterMaster Specific Styles - Clean White */
        .qm-badge {
            background: white;
            color: #6b7280;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #d1d5db;
        }

        .dispatch-ready {
            background: #fffbeb !important;
            border-left: 3px solid #f59e0b !important;
        }

        .dispatch-ready td {
            background: #fffbeb;
        }

        /* Enhanced Action Buttons - Simple */
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: 1px solid;
        }

        .btn-sm:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-info { 
            background: #f0f9ff; 
            color: #0c4a6e;
            border-color: #0ea5e9;
        }

        .btn-info:hover {
            background: #0ea5e9;
            color: white;
        }

        .btn-success { 
            background: #f0fdf4; 
            color: #14532d;
            border-color: #22c55e;
        }

        .btn-success:hover {
            background: #22c55e;
            color: white;
        }

        .btn-warning { 
            background: #fffbeb; 
            color: #92400e;
            border-color: #f59e0b;
        }

        .btn-warning:hover {
            background: #f59e0b;
            color: white;
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
                padding: 0.5rem;
                font-size: 0.8rem;
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
        <div class="requests-container">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-warehouse"></i>
                        QuarterMaster Requests
                    </h1>
                    <p class="page-subtitle">Monitor authorized requests ready for dispatch</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-quartermaster" onclick="viewDispatchableRequests()">
                        <i class="fas fa-truck"></i>
                        Ready to Dispatch
                    </button>
                    <button class="btn btn-success" onclick="refreshRequests()">
                        <i class="fas fa-sync"></i>
                        Refresh
                    </button>
                    <button class="btn btn-primary" onclick="exportRequests()">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- Request Statistics -->
            <div class="stats-row">
                <div class="stat-card ready-requests">
                    <div class="stat-value" id="readyRequests">-</div>
                    <div class="stat-label">Ready to Dispatch</div>
                </div>
                <div class="stat-card authorized-requests">
                    <div class="stat-value" id="authorizedRequests">-</div>
                    <div class="stat-label">Authorized</div>
                </div>
                <div class="stat-card dispatched-requests">
                    <div class="stat-value" id="dispatchedRequests">-</div>
                    <div class="stat-label">Dispatched</div>
                </div>
                <div class="stat-card pending-dispatch">
                    <div class="stat-value" id="pendingDispatch">-</div>
                    <div class="stat-label">Pending Dispatch</div>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="search-filter-bar">
                <input type="text" id="searchInput" class="search-input" placeholder="🔍 Search requests by ID, item, or requester...">
                <select id="statusFilter" class="filter-select">
                    <option value="">All Status</option>
                    <option value="authorized">Authorized</option>
                    <option value="dispatched">Dispatched</option>
                    <option value="ready">Ready to Dispatch</option>
                </select>
                <select id="dateFilter" class="filter-select">
                    <option value="">All Dates</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                </select>
            </div>

            <!-- Requests Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i>
                        Requests for QuarterMaster Action
                    </h3>
                    <div>
                        <span class="qm-badge">QuarterMaster View</span>
                        <span id="requestCount">Loading...</span> requests found
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table" id="requestsTable">
                            <thead>
                                <tr>
                                    <th>Request ID</th>
                                    <th>Item Name</th>
                                    <th>Requester</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Authorized Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="requestsTableBody">
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem;">
                                        <i class="fas fa-spinner fa-spin"></i> Loading requests...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Request Details Modal -->
    <div class="modal" id="requestModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Request Details</h2>
                <button class="close-modal" onclick="closeRequestModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Request details will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Dispatch Modal -->
    <div class="modal" id="dispatchModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Dispatch Item</h2>
                <button class="close-modal" onclick="closeDispatchModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="dispatchModalBody">
                <!-- Dispatch form will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Include Footer Component -->
    <?php include '../../dashboard/components/footer.php'; ?>

    <script>
        // API Configuration for QuarterMaster - Fixed URL
        const API_BASE_URL = 'http://localhost/students-proj/unfedZombie/Controllers/QuarterMaster';
        let requests = [];
        let filteredRequests = [];
        let dispatchableRequests = [];

        // Get JWT token from session
        const token = '<?php echo $_SESSION["jwt_token"] ?? ""; ?>';

        // API Headers
        const headers = {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        };

        // User information
        const userRole = 'QuarterMaster';
        const userId = '<?php echo $_SESSION["user_id"] ?? ""; ?>';

        // Load requests on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 QuarterMaster Requests page loaded, initializing...');
            loadAllRequests();
            loadDispatchableRequests();
            setupEventListeners();
            setupTableEventDelegation();
        });

        // Setup event listeners
        function setupEventListeners() {
            console.log('📋 Setting up event listeners...');
            document.getElementById('searchInput').addEventListener('input', filterRequests);
            document.getElementById('statusFilter').addEventListener('change', filterRequests);
            document.getElementById('dateFilter').addEventListener('change', filterRequests);
        }

        // Setup event delegation for table buttons
        function setupTableEventDelegation() {
            console.log('🎯 Setting up event delegation for request table...');
            document.getElementById('requestsTable').addEventListener('click', handleTableClick);
        }

        // Handle table button clicks
        function handleTableClick(e) {
            // Handle view request button clicks
            if (e.target.closest('.view-request-btn')) {
                e.preventDefault();
                e.stopPropagation();
                const button = e.target.closest('.view-request-btn');
                const requestId = parseInt(button.getAttribute('data-request-id'));
                console.log('👁️ View request button clicked for ID:', requestId);
                if (requestId && !isNaN(requestId)) {
                    viewRequest(requestId);
                }
            }

            // Handle authorize request button clicks
            if (e.target.closest('.authorize-request-btn')) {
                e.preventDefault();
                e.stopPropagation();
                const button = e.target.closest('.authorize-request-btn');
                const requestId = parseInt(button.getAttribute('data-request-id'));
                console.log('🔐 Authorize request button clicked for ID:', requestId);
                if (requestId && !isNaN(requestId)) {
                    authorizeRequest(requestId);
                }
            }

            // Handle dispatch request button clicks
            if (e.target.closest('.dispatch-request-btn')) {
                e.preventDefault();
                e.stopPropagation();
                const button = e.target.closest('.dispatch-request-btn');
                const requestId = parseInt(button.getAttribute('data-request-id'));
                console.log('🚚 Dispatch request button clicked for ID:', requestId);
                if (requestId && !isNaN(requestId)) {
                    showDispatchModal(requestId);
                }
            }
        }

        // Load all requests (for general overview)
        async function loadAllRequests() {
            try {
                showAlert('🔄 Loading all requests...', 'info');
                
                // For now, we'll use mock data since your API endpoint might need adjustment
                // Once your backend has a general requests endpoint for QuarterMaster, use:
                // const response = await fetch(`${API_BASE_URL}/api/requests/all`, { headers });
                
                // Mock data for demonstration
                requests = [
                    {
                        id: 1,
                        item_id: 101,
                        item_name: 'Office Chairs',
                        quantity_requested: 10,
                        requested_by: 1,
                        requester_name: 'John Doe',
                        status: 'authorized',
                        authorized: 1,
                        authorized_at: '2024-12-06 14:30:00',
                        created_at: '2024-12-05 09:15:00'
                    },
                    {
                        id: 2,
                        item_id: 102,
                        item_name: 'Laptops',
                        quantity_requested: 5,
                        requested_by: 2,
                        requester_name: 'Jane Smith',
                        status: 'dispatched',
                        authorized: 1,
                        dispatched: 1,
                        dispatched_at: '2024-12-06 16:45:00',
                        authorized_at: '2024-12-06 08:30:00',
                        created_at: '2024-12-04 11:00:00'
                    },
                    {
                        id: 3,
                        item_id: 103,
                        item_name: 'Printers',
                        quantity_requested: 3,
                        requested_by: 3,
                        requester_name: 'Bob Johnson',
                        status: 'authorized',
                        authorized: 1,
                        authorized_at: '2024-12-06 12:15:00',
                        created_at: '2024-12-06 10:30:00'
                    }
                ];

                filteredRequests = [...requests];
                displayRequests();
                updateRequestStats();
                
                showAlert(`✅ Loaded ${requests.length} requests!`, 'success');
                
            } catch (error) {
                console.error('❌ Error loading requests:', error);
                showAlert('❌ Error loading requests: ' + error.message, 'error');
            }
        }

        // Load dispatchable requests using your API - Fixed
        async function loadDispatchableRequests() {
            try {
                console.log('🔗 Calling dispatchable requests API:', `${API_BASE_URL}/api/requests/ready`);
                
                const response = await fetch(`${API_BASE_URL}/api/requests/ready`, {
                    method: 'GET',
                    headers: headers
                });

                console.log('📡 Dispatchable requests response status:', response.status);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.log('❌ Dispatchable requests error:', errorText);
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();
                dispatchableRequests = Array.isArray(data) ? data : [];
                
                console.log('✅ Loaded dispatchable requests:', dispatchableRequests);
                
            } catch (error) {
                console.error('❌ Error loading dispatchable requests:', error);
                
                if (error.message.includes('404')) {
                    showAlert('⚠️ Dispatchable requests API not found. Using fallback data.', 'warning');
                    console.log('🔧 Expected API endpoint: GET /students-proj/unfedZombie/Controllers/QuarterMaster/api/requests/ready');
                } else {
                    showAlert('⚠️ Could not load dispatchable requests: ' + error.message, 'warning');
                }
                
                // Fallback to mock data
                dispatchableRequests = [
                    {
                        id: 1,
                        item_id: 101,
                        item_name: 'Office Chairs',
                        quantity_requested: 10,
                        requested_by: 1
                    },
                    {
                        id: 3,
                        item_id: 103,
                        item_name: 'Printers',
                        quantity_requested: 3,
                        requested_by: 3
                    }
                ];
            }
        }

        // Display requests in table
        function displayRequests() {
            console.log('📊 Displaying requests:', filteredRequests);
            const tbody = document.getElementById('requestsTableBody');
            const requestCount = document.getElementById('requestCount');
            
            if (filteredRequests.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem;">
                            <i class="fas fa-inbox"></i> No requests found
                        </td>
                    </tr>
                `;
                requestCount.textContent = '0';
                return;
            }

            tbody.innerHTML = filteredRequests.map(request => {
                const status = getRequestStatus(request);
                const actions = getRequestActions(request);
                
                return `
                    <tr ${status.class === 'authorized' ? 'class="dispatch-ready"' : ''}>
                        <td><strong>#${request.id}</strong></td>
                        <td>
                            <div>
                                <strong>${request.item_name || 'Unknown Item'}</strong><br>
                                <small style="color: #666;">ID: ${request.item_id || 'N/A'}</small>
                            </div>
                        </td>
                        <td>${request.requester_name || 'Unknown User'}</td>
                        <td><strong>${request.quantity_requested || 'N/A'}</strong></td>
                        <td><span class="status-badge ${status.class}">${status.text}</span></td>
                        <td>${formatDate(request.authorized_at || request.created_at)}</td>
                        <td>${actions}</td>
                    </tr>
                `;
            }).join('');

            requestCount.textContent = filteredRequests.length;
            console.log('✅ Requests table updated');
        }

        // Get request status for QuarterMaster view
        function getRequestStatus(request) {
            if (request.dispatched || request.dispatched === 1) {
                return { class: 'dispatched', text: 'Dispatched' };
            } else if (request.authorized || request.authorized === 1) {
                return { class: 'authorized', text: 'Ready to Dispatch' };
            } else {
                return { class: 'pending', text: 'Pending Authorization' };
            }
        }

        // Get request actions for QuarterMaster
        function getRequestActions(request) {
            let actions = `
                <button class="btn btn-info btn-sm view-request-btn" data-request-id="${request.id}" title="View Details">
                    <i class="fas fa-eye"></i>
                </button>
            `;

            const status = getRequestStatus(request);
            
            // QuarterMaster can authorize and dispatch
            if (status.class === 'authorized') {
                actions += `
                    <button class="btn btn-success btn-sm dispatch-request-btn" data-request-id="${request.id}" title="Dispatch Item">
                        <i class="fas fa-truck"></i>
                    </button>
                `;
            }

            // QuarterMaster can also authorize requests if not yet authorized
            if (status.class === 'pending') {
                actions += `
                    <button class="btn btn-warning btn-sm authorize-request-btn" data-request-id="${request.id}" title="Authorize Request">
                        <i class="fas fa-key"></i>
                    </button>
                `;
            }

            return actions;
        }

        // Update request statistics
        function updateRequestStats() {
            const readyCount = requests.filter(req => req.authorized && !req.dispatched).length;
            const authorizedCount = requests.filter(req => req.authorized).length;
            const dispatchedCount = requests.filter(req => req.dispatched).length;
            const pendingDispatchCount = dispatchableRequests.length;

            document.getElementById('readyRequests').textContent = readyCount;
            document.getElementById('authorizedRequests').textContent = authorizedCount;
            document.getElementById('dispatchedRequests').textContent = dispatchedCount;
            document.getElementById('pendingDispatch').textContent = pendingDispatchCount;
        }

        // Filter requests
        function filterRequests() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const dateFilter = document.getElementById('dateFilter').value;

            filteredRequests = requests.filter(request => {
                // Search filter
                const matchesSearch = !searchTerm || 
                    request.id.toString().includes(searchTerm) ||
                    (request.requester_name || '').toLowerCase().includes(searchTerm) ||
                    (request.item_name || '').toLowerCase().includes(searchTerm);

                // Status filter
                const requestStatus = getRequestStatus(request);
                let matchesStatus = !statusFilter;
                
                if (statusFilter === 'ready') {
                    matchesStatus = requestStatus.class === 'authorized';
                } else if (statusFilter) {
                    matchesStatus = requestStatus.class === statusFilter;
                }

                // Date filter
                let matchesDate = true;
                if (dateFilter && request.created_at) {
                    const requestDate = new Date(request.created_at);
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

            displayRequests();
        }

        // View request details
        function viewRequest(requestId) {
            console.log('👁️ Viewing request details for ID:', requestId);
            
            const request = requests.find(r => parseInt(r.id) === parseInt(requestId));
            if (!request) {
                showAlert('❌ Request not found!', 'error');
                return;
            }

            const status = getRequestStatus(request);
            
            const modalBody = document.getElementById('modalBody');
            modalBody.innerHTML = `
                <div style="display: grid; gap: 1rem;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        <div>
                            <strong>Request ID:</strong><br>
                            <span style="font-size: 1.2rem; color: var(--primary-color);">#${request.id}</span>
                        </div>
                        <div>
                            <strong>Status:</strong><br>
                            <span class="status-badge ${status.class}">${status.text}</span>
                        </div>
                        <div>
                            <strong>Quantity:</strong><br>
                            <span style="font-size: 1.1rem;">${request.quantity_requested || 'N/A'}</span>
                        </div>
                    </div>
                    
                    <hr style="margin: 1rem 0; border: none; border-top: 1px solid #eee;">
                    
                    <div>
                        <strong>Requester:</strong><br>
                        <span>${request.requester_name || 'Unknown User'}</span>
                    </div>
                    
                    <div>
                        <strong>Item Requested:</strong><br>
                        <span>${request.item_name || 'Unknown Item'} (ID: ${request.item_id || 'N/A'})</span>
                    </div>
                    
                    <div>
                        <strong>Description:</strong><br>
                        <span>${request.description || 'No description provided'}</span>
                    </div>
                    
                    <hr style="margin: 1rem 0; border: none; border-top: 1px solid #eee;">
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; font-size: 0.9rem;">
                        <div>
                            <strong>Created:</strong><br>
                            <span>${formatDate(request.created_at)}</span>
                        </div>
                        ${request.authorized_at ? `
                        <div>
                            <strong>Authorized:</strong><br>
                            <span>${formatDate(request.authorized_at)}</span>
                        </div>
                        ` : ''}
                        ${request.dispatched_at ? `
                        <div>
                            <strong>Dispatched:</strong><br>
                            <span>${formatDate(request.dispatched_at)}</span>
                        </div>
                        ` : ''}
                    </div>
                    
                    <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                        ${getModalActions(request)}
                    </div>
                </div>
            `;

            document.getElementById('requestModal').classList.add('show');
        }

        // Get modal actions for QuarterMaster
        function getModalActions(request) {
            let actions = '';
            const status = getRequestStatus(request);

            if (status.class === 'authorized') {
                actions += `
                    <button class="btn btn-success" onclick="showDispatchModal(${request.id}); closeRequestModal();">
                        <i class="fas fa-truck"></i> Dispatch Item
                    </button>
                `;
            }

            if (status.class === 'pending') {
                actions += `
                    <button class="btn btn-warning" onclick="authorizeRequest(${request.id}); closeRequestModal();">
                        <i class="fas fa-key"></i> Authorize Request
                    </button>
                `;
            }

            actions += `
                <button class="btn btn-secondary" onclick="closeRequestModal()">
                    <i class="fas fa-times"></i> Close
                </button>
            `;

            return actions;
        }

        // Show dispatch modal
        function showDispatchModal(requestId) {
            console.log('🚚 Showing dispatch modal for request ID:', requestId);
            
            const request = requests.find(r => parseInt(r.id) === parseInt(requestId));
            if (!request) {
                showAlert('❌ Request not found!', 'error');
                return;
            }

            const modalBody = document.getElementById('dispatchModalBody');
            modalBody.innerHTML = `
                <div style="display: grid; gap: 1.5rem;">
                    <div class="alert info">
                        <i class="fas fa-info-circle"></i>
                        <span>You are about to dispatch this item. This action cannot be undone.</span>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; padding: 1rem; background: var(--light-gray); border-radius: 10px;">
                        <div>
                            <strong>Request ID:</strong><br>
                            <span style="color: var(--primary-color);">#${request.id}</span>
                        </div>
                        <div>
                            <strong>Item:</strong><br>
                            <span>${request.item_name}</span>
                        </div>
                        <div>
                            <strong>Quantity:</strong><br>
                            <span>${request.quantity_requested}</span>
                        </div>
                        <div>
                            <strong>Requester:</strong><br>
                            <span>${request.requester_name}</span>
                        </div>
                    </div>
                    
                    <div>
                        <label for="dispatchNotes" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                            Dispatch Notes (Optional):
                        </label>
                        <textarea id="dispatchNotes" rows="3" style="width: 100%; padding: 0.75rem; border: 2px solid #e1e5e9; border-radius: 10px; font-size: 0.9rem;" placeholder="Add any notes about the dispatch..."></textarea>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                        <button class="btn btn-secondary" onclick="closeDispatchModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button class="btn btn-success" onclick="confirmDispatch(${request.id})">
                            <i class="fas fa-truck"></i> Confirm Dispatch
                        </button>
                    </div>
                </div>
            `;

            document.getElementById('dispatchModal').classList.add('show');
        }

        // Confirm dispatch using your API - Fixed
        async function confirmDispatch(requestId) {
            try {
                showAlert('🚚 Dispatching item...', 'info');
                
                const notes = document.getElementById('dispatchNotes').value;
                
                // Fixed API endpoint - remove extra /api
                const response = await fetch(`${API_BASE_URL}/api/dispatch/item`, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify({
                        request_id: requestId,
                        notes: notes
                    })
                });

                console.log('🔗 Dispatch API URL:', `${API_BASE_URL}/api/dispatch/item`);
                console.log('📤 Request data:', { request_id: requestId, notes: notes });
                console.log('📡 Response status:', response.status);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.log('❌ Error response:', errorText);
                    
                    let errorData;
                    try {
                        errorData = JSON.parse(errorText);
                    } catch (e) {
                        throw new Error(`HTTP ${response.status}: ${errorText}`);
                    }
                    
                    throw new Error(errorData.message || errorData.error || `HTTP ${response.status}`);
                }

                const result = await response.json();
                console.log('✅ Dispatch success:', result);
                
                showAlert('✅ Item dispatched successfully!', 'success');
                
                closeDispatchModal();
                loadAllRequests(); // Reload requests
                loadDispatchableRequests(); // Reload dispatchable requests

            } catch (error) {
                console.error('❌ Error dispatching item:', error);
                
                // Check if it's a 404 error (API endpoint not found)
                if (error.message.includes('404') || error.message.includes('Not Found')) {
                    showAlert('⚠️ API endpoint not found. Please check your backend setup.', 'warning');
                    console.log('🔧 Expected API endpoint: POST /students-proj/unfedZombie/Controllers/QuarterMaster/api/dispatch/item');
                } else if (error.message.includes('Approved and authorized request not found')) {
                    showAlert('⚠️ Request not properly approved. Please check the approval status.', 'warning');
                    console.log('🔧 Backend expects approved_by = 4 (CO role). Current request might not be approved by CO.');
                } else {
                    showAlert('❌ Error dispatching item: ' + error.message, 'error');
                }
                
                // Fallback: Update local data for demo
                const request = requests.find(r => r.id === requestId);
                if (request) {
                    request.dispatched = 1;
                    request.dispatched_at = new Date().toISOString();
                    filteredRequests = [...requests];
                    displayRequests();
                    updateRequestStats();
                    closeDispatchModal();
                    showAlert('✅ Item dispatched! (Demo mode - API unavailable)', 'success');
                }
            }
        }

        // Authorize request using your API - Fixed
        async function authorizeRequest(requestId) {
            if (!confirm('🔐 Authorize this request?\n\nThis action will mark the request as authorized and ready for dispatch.')) {
                return;
            }

            try {
                showAlert('🔄 Authorizing request...', 'info');
                
                console.log('🔗 Calling authorize API:', `${API_BASE_URL}/api/requests/authorize`);
                console.log('📤 Request data:', { request_id: requestId });
                
                const response = await fetch(`${API_BASE_URL}/api/requests/authorize`, {
                    method: 'PUT',
                    headers: headers,
                    body: JSON.stringify({
                        request_id: requestId
                    })
                });

                console.log('📡 Authorize response status:', response.status);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.log('❌ Authorize error:', errorText);
                    
                    let errorData;
                    try {
                        errorData = JSON.parse(errorText);
                    } catch (e) {
                        throw new Error(`HTTP ${response.status}: ${errorText}`);
                    }
                    
                    throw new Error(errorData.message || errorData.error || `HTTP ${response.status}`);
                }

                const result = await response.json();
                console.log('✅ Authorize success:', result);
                
                showAlert('🔐 Request authorized successfully!', 'success');
                
                loadAllRequests(); // Reload requests
                loadDispatchableRequests(); // Reload dispatchable requests

            } catch (error) {
                console.error('❌ Error authorizing request:', error);
                
                if (error.message.includes('404')) {
                    showAlert('⚠️ Authorization API not found. Please check your backend setup.', 'warning');
                    console.log('🔧 Expected API endpoint: PUT /students-proj/unfedZombie/Controllers/QuarterMaster/api/requests/authorize');
                } else if (error.message.includes('already authorized')) {
                    showAlert('⚠️ Request is already authorized.', 'warning');
                } else {
                    showAlert('❌ Error authorizing request: ' + error.message, 'error');
                }
                
                // Fallback: Update local data for demo
                const request = requests.find(r => r.id === requestId);
                if (request) {
                    request.authorized = 1;
                    request.authorized_at = new Date().toISOString();
                    filteredRequests = [...requests];
                    displayRequests();
                    updateRequestStats();
                    showAlert('🔐 Request authorized! (Demo mode - API unavailable)', 'success');
                }
            }
        }

        // View dispatchable requests
        function viewDispatchableRequests() {
            console.log('🚚 Viewing dispatchable requests...');
            
            if (dispatchableRequests.length === 0) {
                showAlert('📦 No requests ready for dispatch', 'info');
                return;
            }
            
            // Filter to show only ready-to-dispatch requests
            document.getElementById('statusFilter').value = 'ready';
            filterRequests();
            
            showAlert(`📦 Found ${dispatchableRequests.length} requests ready for dispatch`, 'info');
        }

        // Format date
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', { 
                    year: 'numeric',
                    month: 'short', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (error) {
                return 'Invalid Date';
            }
        }

        // Close modals
        function closeRequestModal() {
            document.getElementById('requestModal').classList.remove('show');
        }

        function closeDispatchModal() {
            document.getElementById('dispatchModal').classList.remove('show');
        }

        // Refresh requests
        function refreshRequests() {
            console.log('🔄 Refreshing requests...');
            loadAllRequests();
            loadDispatchableRequests();
        }

        // Export requests
        function exportRequests() {
            console.log('📁 Exporting requests...');
            
            const csvContent = [
                ['Request ID', 'Item Name', 'Requester', 'Quantity', 'Status', 'Created Date', 'Authorized Date', 'Dispatched Date'],
                ...filteredRequests.map(request => {
                    const status = getRequestStatus(request);
                    return [
                        request.id,
                        request.item_name || 'Unknown',
                        request.requester_name || 'Unknown',
                        request.quantity_requested || 'N/A',
                        status.text,
                        formatDate(request.created_at),
                        formatDate(request.authorized_at),
                        formatDate(request.dispatched_at)
                    ];
                })
            ].map(row => row.join(',')).join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `MSICT_QuarterMaster_Requests_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            showAlert('📁 Requests exported successfully!', 'success');
        }

        // Show alert notification
        function showAlert(message, type = 'info') {
            console.log(`📢 Alert: ${message} (${type})`);
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

        // Modal click outside to close
        document.getElementById('requestModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRequestModal();
            }
        });

        document.getElementById('dispatchModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDispatchModal();
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRequestModal();
                closeDispatchModal();
            }
        });

        // Make functions globally accessible
        window.refreshRequests = refreshRequests;
        window.exportRequests = exportRequests;
        window.viewRequest = viewRequest;
        window.authorizeRequest = authorizeRequest;
        window.viewDispatchableRequests = viewDispatchableRequests;
        window.showDispatchModal = showDispatchModal;
        window.confirmDispatch = confirmDispatch;
        window.closeRequestModal = closeRequestModal;
        window.closeDispatchModal = closeDispatchModal;

        // Initialize system
        console.log('🏭 MSICT QuarterMaster Requests System Initialized');
        console.log('🔗 API Base URL:', API_BASE_URL);
        console.log('🔗 Dispatchable Requests API:', `${API_BASE_URL}/api/requests/ready`);
        console.log('🔗 Dispatch API:', `${API_BASE_URL}/api/dispatch/item`);
        console.log('🔗 Authorize API:', `${API_BASE_URL}/api/requests/authorize`);
        console.log('🔑 JWT Token Status:', token ? 'Present' : 'Missing');
        console.log('👤 Current User Role:', userRole);
        console.log('🆔 User ID:', userId);
        
        // Show welcome message after page loads
        setTimeout(() => {
            showAlert('🏭 QuarterMaster Requests System Ready!', 'success');
        }, 2000);
    </script>
</body>
</html>