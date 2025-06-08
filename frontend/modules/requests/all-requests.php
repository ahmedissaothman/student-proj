<?php
// modules/requests/all-requests.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit();
}

// Check if user has permission to view all requests
$allowed_roles = ['Admin', 'QuarterMaster', 'CO'];
if (!in_array($_SESSION['user_role'] ?? '', $allowed_roles)) {
    header('Location: ../../dashboard/department-dashboard.php');
    exit();
}

// Set user data for components
$_SESSION['username'] = $_SESSION['username'] ?? 'user001';
$_SESSION['full_name'] = $_SESSION['full_name'] ?? 'User Name';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Requests - MSICT Ordering System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* All Requests Page Styles */
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

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
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
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
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
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.pending { background: #fff3cd; color: #856404; }
        .status-badge.approved { background: #d4edda; color: #155724; }
        .status-badge.authorized { background: #cce5ff; color: #0056b3; }
        .status-badge.rejected { background: #f8d7da; color: #721c24; }
        .status-badge.completed { background: #d1ecf1; color: #0c5460; }

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

        .stat-card.total-requests { --card-bg-1: #667eea; --card-bg-2: #764ba2; }
        .stat-card.pending-requests { --card-bg-1: #f093fb; --card-bg-2: #f5576c; }
        .stat-card.approved-requests { --card-bg-1: #43e97b; --card-bg-2: #38f9d7; }
        .stat-card.completed-requests { --card-bg-1: #4facfe; --card-bg-2: #00f2fe; }

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
                        <i class="fas fa-clipboard-list"></i>
                        All Requests
                    </h1>
                    <p class="page-subtitle">Monitor and manage all system requests</p>
                </div>
                <div class="page-actions">
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
                <div class="stat-card total-requests">
                    <div class="stat-value" id="totalRequests">-</div>
                    <div class="stat-label">Total Requests</div>
                </div>
                <div class="stat-card pending-requests">
                    <div class="stat-value" id="pendingRequests">-</div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card approved-requests">
                    <div class="stat-value" id="approvedRequests">-</div>
                    <div class="stat-label">Approved</div>
                </div>
                <div class="stat-card completed-requests">
                    <div class="stat-value" id="authorizedRequests">-</div>
                    <div class="stat-label">Authorized</div>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="search-filter-bar">
                <input type="text" id="searchInput" class="search-input" placeholder="🔍 Search requests by ID, user, or description...">
                <select id="statusFilter" class="filter-select">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="authorized">Authorized</option>
                    <option value="rejected">Rejected</option>
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
                        All System Requests
                    </h3>
                    <div>
                        <span id="requestCount">Loading...</span> requests found
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table" id="requestsTable">
                            <thead>
                                <tr>
                                    <th>Request ID</th>
                                    <th>User</th>
                                    <th>Item/Description</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Date Created</th>
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

    <!-- Include Footer Component -->
    <?php include '../../dashboard/components/footer.php'; ?>

    <script>
        // API Configuration
        const API_BASE_URL = 'http://localhost/students-proj/unfedZombie/Controllers/admin';
        let requests = [];
        let filteredRequests = [];

        // Get JWT token from session
        const token = '<?php echo $_SESSION["jwt_token"] ?? ""; ?>';

        // API Headers
        const headers = {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        };

        // User role for conditional actions
        const userRole = '<?php echo $_SESSION["user_role"] ?? "Department"; ?>';

        // Load requests on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 All Requests page loaded, initializing...');
            loadRequests();
            setupEventListeners();
            setupTableEventDelegation();
        });

        // Setup event listeners
        function setupEventListeners() {
            console.log('📋 Setting up event listeners...');
            // Search and filter functionality
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

            // Handle approve request button clicks
            if (e.target.closest('.approve-request-btn')) {
                e.preventDefault();
                e.stopPropagation();
                const button = e.target.closest('.approve-request-btn');
                const requestId = parseInt(button.getAttribute('data-request-id'));
                console.log('✅ Approve request button clicked for ID:', requestId);
                if (requestId && !isNaN(requestId)) {
                    approveRequest(requestId);
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
        }

        // Load requests from API
        async function loadRequests() {
            try {
                showAlert('🔄 Loading requests...', 'info');
                
                const apiUrl = `${API_BASE_URL}/api/admin/requests`;
                
                console.log('🔍 Calling API endpoint:', apiUrl);
                console.log('🔑 Using token:', token ? 'Present' : 'Missing');
                
                const response = await fetch(apiUrl, {
                    method: 'GET',
                    headers: headers
                });
                
                console.log('📡 Response status:', response.status);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('❌ API Error:', errorText);
                    
                    if (response.status === 401) {
                        throw new Error('Authentication failed. Please login again.');
                    } else if (response.status === 403) {
                        throw new Error('Access denied. Admin privileges required.');
                    } else {
                        throw new Error(`API Error: ${response.status} - ${errorText}`);
                    }
                }

                const responseText = await response.text();
                console.log('📄 Raw response:', responseText);

                let apiResponse;
                try {
                    apiResponse = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('❌ JSON parse error:', parseError);
                    console.log('🔍 Response that failed to parse:', responseText);
                    throw new Error('Invalid JSON response from server');
                }

                // Handle response format
                if (Array.isArray(apiResponse)) {
                    requests = apiResponse;
                    // Ensure all request IDs are numbers for consistency
                    requests = requests.map(request => ({
                        ...request,
                        id: parseInt(request.id)
                    }));
                } else if (apiResponse.data && Array.isArray(apiResponse.data)) {
                    requests = apiResponse.data.map(request => ({
                        ...request,
                        id: parseInt(request.id)
                    }));
                } else if (apiResponse.message) {
                    throw new Error(apiResponse.message);
                } else {
                    throw new Error('Unexpected response format');
                }

                console.log('✅ Loaded requests:', requests);

                filteredRequests = [...requests];
                displayRequests();
                updateRequestStats();
                
                showAlert(`✅ Loaded ${requests.length} requests!`, 'success');
                
            } catch (error) {
                console.error('❌ Error loading requests:', error);
                showAlert('❌ Error loading requests: ' + error.message, 'error');
                
                // Fallback to mock data for demo
                console.log('🔄 Using mock data as fallback...');
                requests = [
                    { 
                        id: 1, 
                        user_id: 1,
                        item_id: 1,
                        quantity: 10,
                        description: 'Office supplies needed urgently',
                        approved: 0,
                        authorized: 0,
                        created_at: '2024-12-06 10:30:00',
                        requester_name: 'John Doe',
                        item_name: 'Pens'
                    },
                    { 
                        id: 2, 
                        user_id: 2,
                        item_id: 2,
                        quantity: 5,
                        description: 'Laptop computers for new staff',
                        approved: 1,
                        authorized: 0,
                        approved_at: '2024-12-06 14:20:00',
                        created_at: '2024-12-05 09:15:00',
                        requester_name: 'Jane Smith',
                        item_name: 'Laptops'
                    },
                    { 
                        id: 3, 
                        user_id: 3,
                        item_id: 3,
                        quantity: 20,
                        description: 'Filing cabinets for document storage',
                        approved: 1,
                        authorized: 1,
                        approved_at: '2024-12-05 16:45:00',
                        authorized_at: '2024-12-06 08:30:00',
                        created_at: '2024-12-04 11:00:00',
                        requester_name: 'Bob Johnson',
                        item_name: 'Filing Cabinets'
                    }
                ];

                filteredRequests = [...requests];
                displayRequests();
                updateRequestStats();
                
                showAlert('⚠️ Using demo data - API connection failed', 'warning');
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
                    <tr>
                        <td><strong>#${request.id}</strong></td>
                        <td>${request.requester_name || request.user_name || 'Unknown User'}</td>
                        <td>
                            <div>
                                <strong>${request.item_name || 'Item'}</strong><br>
                                <small style="color: #666;">${request.description || 'No description'}</small>
                            </div>
                        </td>
                        <td><strong>${request.quantity || 'N/A'}</strong></td>
                        <td><span class="status-badge ${status.class}">${status.text}</span></td>
                        <td>${formatDate(request.created_at)}</td>
                        <td>${actions}</td>
                    </tr>
                `;
            }).join('');

            requestCount.textContent = filteredRequests.length;
            console.log('✅ Requests table updated with event delegation handling button clicks');
        }

        // Get request status
        function getRequestStatus(request) {
            if (request.authorized || request.authorized === 1 || request.authorized === '1') {
                return { class: 'authorized', text: 'Authorized' };
            } else if (request.approved || request.approved === 1 || request.approved === '1') {
                return { class: 'approved', text: 'Approved' };
            } else if (request.rejected || request.rejected === 1 || request.rejected === '1') {
                return { class: 'rejected', text: 'Rejected' };
            } else {
                return { class: 'pending', text: 'Pending' };
            }
        }

        // Get request actions based on user role and request status
        function getRequestActions(request) {
            let actions = `
                <button class="btn btn-info btn-sm view-request-btn" data-request-id="${request.id}" title="View Details">
                    <i class="fas fa-eye"></i>
                </button>
            `;

            const status = getRequestStatus(request);
            
            // Add role-specific actions
            if (userRole === 'Admin' || userRole === 'QuarterMaster') {
                if (status.class === 'pending') {
                    actions += `
                        <button class="btn btn-success btn-sm approve-request-btn" data-request-id="${request.id}" title="Approve Request">
                            <i class="fas fa-check"></i>
                        </button>
                    `;
                }
                
                if (status.class === 'approved' && userRole === 'Admin') {
                    actions += `
                        <button class="btn btn-warning btn-sm authorize-request-btn" data-request-id="${request.id}" title="Authorize Request">
                            <i class="fas fa-key"></i>
                        </button>
                    `;
                }
            }

            if (userRole === 'CO') {
                if (status.class === 'approved') {
                    actions += `
                        <button class="btn btn-warning btn-sm authorize-request-btn" data-request-id="${request.id}" title="Authorize Request">
                            <i class="fas fa-key"></i>
                        </button>
                    `;
                }
            }

            return actions;
        }

        // Update request statistics
        function updateRequestStats() {
            const totalRequests = requests.length;
            const pendingRequests = requests.filter(req => !req.approved && !req.authorized).length;
            const approvedRequests = requests.filter(req => req.approved && !req.authorized).length;
            const authorizedRequests = requests.filter(req => req.authorized).length;

            document.getElementById('totalRequests').textContent = totalRequests;
            document.getElementById('pendingRequests').textContent = pendingRequests;
            document.getElementById('approvedRequests').textContent = approvedRequests;
            document.getElementById('authorizedRequests').textContent = authorizedRequests;
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
                    (request.requester_name || request.user_name || '').toLowerCase().includes(searchTerm) ||
                    (request.item_name || '').toLowerCase().includes(searchTerm) ||
                    (request.description || '').toLowerCase().includes(searchTerm);

                // Status filter
                const requestStatus = getRequestStatus(request);
                const matchesStatus = !statusFilter || requestStatus.class === statusFilter;

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
                            <span style="font-size: 1.1rem;">${request.quantity || 'N/A'}</span>
                        </div>
                    </div>
                    
                    <hr style="margin: 1rem 0; border: none; border-top: 1px solid #eee;">
                    
                    <div>
                        <strong>Requester:</strong><br>
                        <span>${request.requester_name || request.user_name || 'Unknown User'}</span>
                    </div>
                    
                    <div>
                        <strong>Item Requested:</strong><br>
                        <span>${request.item_name || 'Unknown Item'}</span>
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
                        ${request.approved_at ? `
                        <div>
                            <strong>Approved:</strong><br>
                            <span>${formatDate(request.approved_at)}</span>
                        </div>
                        ` : ''}
                        ${request.authorized_at ? `
                        <div>
                            <strong>Authorized:</strong><br>
                            <span>${formatDate(request.authorized_at)}</span>
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

        // Get modal actions based on request status and user role
        function getModalActions(request) {
            let actions = '';
            const status = getRequestStatus(request);

            if (userRole === 'Admin' || userRole === 'QuarterMaster') {
                if (status.class === 'pending') {
                    actions += `
                        <button class="btn btn-success" onclick="approveRequest(${request.id}); closeRequestModal();">
                            <i class="fas fa-check"></i> Approve Request
                        </button>
                    `;
                }
                
                if (status.class === 'approved' && userRole === 'Admin') {
                    actions += `
                        <button class="btn btn-warning" onclick="authorizeRequest(${request.id}); closeRequestModal();">
                            <i class="fas fa-key"></i> Authorize Request
                        </button>
                    `;
                }
            }

            if (userRole === 'CO' && status.class === 'approved') {
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

        // Approve request
        async function approveRequest(requestId) {
            if (!confirm('✅ Approve this request?\n\nThis action will mark the request as approved.')) {
                return;
            }

            try {
                showAlert('🔄 Approving request...', 'info');
                
                const response = await fetch(`${API_BASE_URL}/api/admin/approve-request/${requestId}`, {
                    method: 'PUT',
                    headers: headers
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || result.error || `HTTP ${response.status}`);
                }

                showAlert('✅ Request approved successfully!', 'success');
                loadRequests(); // Reload requests

            } catch (error) {
                console.error('Error approving request:', error);
                showAlert('❌ Error approving request: ' + error.message, 'error');
                
                // Fallback: Update local data for demo
                const request = requests.find(r => r.id === requestId);
                if (request) {
                    request.approved = 1;
                    request.approved_at = new Date().toISOString();
                    filteredRequests = [...requests];
                    displayRequests();
                    updateRequestStats();
                    showAlert('✅ Request approved! (Demo mode)', 'success');
                }
            }
        }

        // Authorize request
        async function authorizeRequest(requestId) {
            if (!confirm('🔐 Authorize this request?\n\nThis action will mark the request as authorized and ready for dispatch.')) {
                return;
            }

            try {
                showAlert('🔄 Authorizing request...', 'info');
                
                const response = await fetch(`${API_BASE_URL}/api/admin/authorize-request/${requestId}`, {
                    method: 'PUT',
                    headers: headers
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || result.error || `HTTP ${response.status}`);
                }

                showAlert('🔐 Request authorized successfully!', 'success');
                loadRequests(); // Reload requests

            } catch (error) {
                console.error('Error authorizing request:', error);
                showAlert('❌ Error authorizing request: ' + error.message, 'error');
                
                // Fallback: Update local data for demo
                const request = requests.find(r => r.id === requestId);
                if (request) {
                    request.authorized = 1;
                    request.authorized_at = new Date().toISOString();
                    filteredRequests = [...requests];
                    displayRequests();
                    updateRequestStats();
                    showAlert('🔐 Request authorized! (Demo mode)', 'success');
                }
            }
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

        // Close request modal
        function closeRequestModal() {
            document.getElementById('requestModal').classList.remove('show');
        }

        // Refresh requests
        function refreshRequests() {
            console.log('🔄 Refreshing requests...');
            loadRequests();
        }

        // Export requests
        function exportRequests() {
            console.log('📁 Exporting requests...');
            
            const csvContent = [
                ['Request ID', 'User', 'Item', 'Quantity', 'Description', 'Status', 'Created Date', 'Approved Date', 'Authorized Date'],
                ...filteredRequests.map(request => {
                    const status = getRequestStatus(request);
                    return [
                        request.id,
                        request.requester_name || request.user_name || 'Unknown',
                        request.item_name || 'Unknown',
                        request.quantity || 'N/A',
                        request.description || 'No description',
                        status.text,
                        formatDate(request.created_at),
                        formatDate(request.approved_at),
                        formatDate(request.authorized_at)
                    ];
                })
            ].map(row => row.join(',')).join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `MSICT_All_Requests_${new Date().toISOString().split('T')[0]}.csv`;
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

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRequestModal();
            }
        });

        // Make functions globally accessible
        window.refreshRequests = refreshRequests;
        window.exportRequests = exportRequests;
        window.viewRequest = viewRequest;
        window.approveRequest = approveRequest;
        window.authorizeRequest = authorizeRequest;
        window.closeRequestModal = closeRequestModal;

        // Initialize system
        console.log('📋 MSICT All Requests System Initialized');
        console.log('🔗 API Base URL:', API_BASE_URL);
        console.log('🔗 Requests API Endpoint:', `${API_BASE_URL}/api/admin/requests`);
        console.log('🔑 JWT Token Status:', token ? 'Present' : 'Missing');
        console.log('👤 Current User Role:', userRole);
        
        // Show welcome message after page loads
        setTimeout(() => {
            showAlert('📋 All Requests System Ready!', 'success');
        }, 2000);
    </script>
</body>
</html>