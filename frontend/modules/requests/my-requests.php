<?php
// modules/requests/my-requests.php - Department My Requests
session_start();

// Check if user is logged in and is Department
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Department') {
    header('Location: ../../auth/login.php');
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
    <title>My Requests - MSICT Department</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* My Requests Page Styles */
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

        /* Stats Grid */
        .stats-grid {
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
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-card.total-requests { --card-bg-1: #667eea; --card-bg-2: #764ba2; }
        .stat-card.pending-requests { --card-bg-1: #ff7675; --card-bg-2: #fd79a8; }
        .stat-card.approved-requests { --card-bg-1: #00b894; --card-bg-2: #00cec9; }
        .stat-card.denied-requests { --card-bg-1: #fdcb6e; --card-bg-2: #e17055; }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            opacity: 0.8;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.85rem;
            opacity: 0.9;
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

        /* Table */
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

        /* Request Details */
        .request-details {
            display: grid;
            gap: 1rem;
        }

        .detail-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }

        .detail-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
        }

        .detail-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .detail-value {
            font-size: 0.9rem;
            font-weight: 500;
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

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }

            .stat-card {
                padding: 1rem;
            }

            .table th,
            .table td {
                padding: 0.5rem 0.25rem;
                font-size: 0.8rem;
            }

            .detail-row {
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
        <div class="requests-container">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-clipboard-list"></i>
                        My Requests
                    </h1>
                    <p class="page-subtitle">View and manage all your submitted requests</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-info" onclick="refreshRequests()">
                        <i class="fas fa-sync"></i>
                        Refresh
                    </button>
                    <button class="btn btn-success" onclick="createNewRequest()">
                        <i class="fas fa-plus"></i>
                        New Request
                    </button>
                    <button class="btn btn-primary" onclick="exportMyRequests()">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- Request Statistics -->
            <div class="stats-grid">
                <div class="stat-card total-requests" onclick="filterByStatus('all')">
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stat-value" id="totalRequests">
                        <div class="loading-skeleton" style="width: 40px; height: 32px;"></div>
                    </div>
                    <div class="stat-label">Total Requests</div>
                </div>
                
                <div class="stat-card pending-requests" onclick="filterByStatus('pending')">
                    <div class="stat-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-value" id="pendingRequests">
                        <div class="loading-skeleton" style="width: 40px; height: 32px;"></div>
                    </div>
                    <div class="stat-label">Pending</div>
                </div>
                
                <div class="stat-card approved-requests" onclick="filterByStatus('approved')">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value" id="approvedRequests">
                        <div class="loading-skeleton" style="width: 40px; height: 32px;"></div>
                    </div>
                    <div class="stat-label">Approved</div>
                </div>
                
                <div class="stat-card denied-requests" onclick="filterByStatus('denied')">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-value" id="deniedRequests">
                        <div class="loading-skeleton" style="width: 40px; height: 32px;"></div>
                    </div>
                    <div class="stat-label">Denied</div>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="search-filter-bar">
                <input type="text" id="searchInput" class="search-input" placeholder="🔍 Search by request ID, item name, or purpose...">
                <select id="statusFilter" class="filter-select">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="denied">Denied</option>
                    <option value="in_progress">In Progress</option>
                </select>
                <select id="priorityFilter" class="filter-select">
                    <option value="">All Priorities</option>
                    <option value="high">High Priority</option>
                    <option value="medium">Medium Priority</option>
                    <option value="low">Low Priority</option>
                </select>
                <select id="dateFilter" class="filter-select">
                    <option value="">All Dates</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="quarter">This Quarter</option>
                </select>
            </div>

            <!-- Requests Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i>
                        My Request History
                    </h3>
                    <div>
                        <span id="requestCount">Loading...</span> requests found
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Request ID</th>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Date Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="requestsTableBody">
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem;">
                                        <i class="fas fa-spinner fa-spin"></i> Loading your requests...
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
        const API_BASE_URL = 'http://localhost/unfedZombie/Controllers';
        let allRequests = [];
        let filteredRequests = [];

        // Get JWT token from session
        const token = '<?php echo $_SESSION["jwt_token"] ?? ""; ?>';

        // API Headers
        const headers = {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        };

        // Load requests on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📋 My Requests page loading...');
            loadMyRequests();
            setupEventListeners();
        });

        // Setup event listeners
        function setupEventListeners() {
            // Search and filter functionality
            document.getElementById('searchInput').addEventListener('input', filterRequests);
            document.getElementById('statusFilter').addEventListener('change', filterRequests);
            document.getElementById('priorityFilter').addEventListener('change', filterRequests);
            document.getElementById('dateFilter').addEventListener('change', filterRequests);
        }

        // Load my requests from Department API
        async function loadMyRequests() {
            try {
                showAlert('🔄 Loading your requests...', 'info');
                
                const response = await fetch(`${API_BASE_URL}/Department/api/requests/my`, {
                    method: 'GET',
                    headers: headers
                });

                if (!response.ok) {
                    throw new Error(`My Requests API Error: ${response.status}`);
                }

                const requests = await response.json();
                allRequests = Array.isArray(requests) ? requests : [];
                filteredRequests = [...allRequests];
                
                updateStatistics();
                displayRequests();
                
                showAlert(`✅ Loaded ${allRequests.length} requests!`, 'success');
                
            } catch (error) {
                console.error('Error loading requests:', error);
                showAlert('❌ Error loading requests: ' + error.message, 'error');
                
                // Fallback to demo data
                allRequests = [
                    {
                        id: 1,
                        item_name: 'Office Pens',
                        quantity_requested: 10,
                        status: 'pending',
                        priority: 'medium',
                        purpose: 'Office supplies for daily work',
                        request_date: '2024-12-07 10:30:00',
                        remarks: null
                    },
                    {
                        id: 2,
                        item_name: 'Laptops',
                        quantity_requested: 2,
                        status: 'approved',
                        priority: 'high',
                        purpose: 'New staff computers',
                        request_date: '2024-12-05 14:20:00',
                        remarks: 'Approved for urgent need'
                    },
                    {
                        id: 3,
                        item_name: 'Filing Cabinets',
                        quantity_requested: 5,
                        status: 'denied',
                        priority: 'low',
                        purpose: 'Document storage',
                        request_date: '2024-12-03 09:15:00',
                        remarks: 'Budget constraints - resubmit next quarter'
                    }
                ];
                
                filteredRequests = [...allRequests];
                updateStatistics();
                displayRequests();
                showAlert('⚠️ Using demo data - API connection failed', 'warning');
            }
        }

        // Update statistics
        function updateStatistics() {
            const total = allRequests.length;
            const pending = allRequests.filter(req => req.status === 'pending').length;
            const approved = allRequests.filter(req => req.status === 'approved').length;
            const denied = allRequests.filter(req => req.status === 'denied').length;

            document.getElementById('totalRequests').textContent = total;
            document.getElementById('pendingRequests').textContent = pending;
            document.getElementById('approvedRequests').textContent = approved;
            document.getElementById('deniedRequests').textContent = denied;
        }

        // Display requests in table
        function displayRequests() {
            const tbody = document.getElementById('requestsTableBody');
            const requestCount = document.getElementById('requestCount');
            
            if (filteredRequests.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>No Requests Found</h3>
                            <p>No requests match your current filters</p>
                        </td>
                    </tr>
                `;
                requestCount.textContent = '0';
                return;
            }

            tbody.innerHTML = filteredRequests.map(request => `
                <tr>
                    <td><strong>#${request.id}</strong></td>
                    <td>${request.item_name || 'Unknown Item'}</td>
                    <td><strong>${request.quantity_requested || 0}</strong></td>
                    <td><span class="priority-badge ${request.priority || 'medium'}">${request.priority || 'Medium'}</span></td>
                    <td><span class="status-badge ${request.status}">${request.status || 'Pending'}</span></td>
                    <td>${formatDate(request.request_date)}</td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="viewRequestDetails(${request.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="trackRequestStatus(${request.id})" title="Track Status">
                            <i class="fas fa-search"></i>
                        </button>
                    </td>
                </tr>
            `).join('');

            requestCount.textContent = filteredRequests.length;
        }

        // Filter requests
        function filterRequests() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const priorityFilter = document.getElementById('priorityFilter').value;
            const dateFilter = document.getElementById('dateFilter').value;

            filteredRequests = allRequests.filter(request => {
                // Search filter
                const matchesSearch = !searchTerm || 
                    request.id.toString().includes(searchTerm) ||
                    (request.item_name || '').toLowerCase().includes(searchTerm) ||
                    (request.purpose || '').toLowerCase().includes(searchTerm);

                // Status filter
                const matchesStatus = !statusFilter || request.status === statusFilter;

                // Priority filter
                const matchesPriority = !priorityFilter || request.priority === priorityFilter;

                // Date filter
                let matchesDate = true;
                if (dateFilter && request.request_date) {
                    const requestDate = new Date(request.request_date);
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
                        case 'quarter':
                            const quarter = Math.floor(today.getMonth() / 3);
                            const quarterStart = new Date(today.getFullYear(), quarter * 3, 1);
                            matchesDate = requestDate >= quarterStart;
                            break;
                    }
                }

                return matchesSearch && matchesStatus && matchesPriority && matchesDate;
            });

            displayRequests();
        }

        // Filter by status (from stat cards)
        function filterByStatus(status) {
            if (status === 'all') {
                document.getElementById('statusFilter').value = '';
            } else {
                document.getElementById('statusFilter').value = status;
            }
            filterRequests();
            showAlert(`📋 Showing ${status === 'all' ? 'all' : status} requests`, 'info');
        }

        // View request details
        async function viewRequestDetails(requestId) {
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
                showRequestModal(request);
                
            } catch (error) {
                console.error('Error viewing request:', error);
                
                // Fallback: find request in local data
                const request = allRequests.find(r => r.id === requestId);
                if (request) {
                    showRequestModal(request);
                } else {
                    showAlert('❌ Error loading request details: ' + error.message, 'error');
                }
            }
        }

        // Show request modal with details
        function showRequestModal(request) {
            const modalBody = document.getElementById('modalBody');
            const modalTitle = document.getElementById('modalTitle');
            
            modalTitle.textContent = `Request #${request.id} Details`;
            
            modalBody.innerHTML = `
                <div class="request-details">
                    <div class="detail-row">
                        <div class="detail-item">
                            <div class="detail-label">Request ID</div>
                            <div class="detail-value">#${request.id}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Status</div>
                            <div class="detail-value">
                                <span class="status-badge ${request.status}">${request.status || 'Pending'}</span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Priority</div>
                            <div class="detail-value">
                                <span class="priority-badge ${request.priority || 'medium'}">${request.priority || 'Medium'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-item">
                            <div class="detail-label">Item Requested</div>
                            <div class="detail-value">${request.item_name || 'Unknown Item'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Quantity</div>
                            <div class="detail-value">${request.quantity_requested || 0}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Date Submitted</div>
                            <div class="detail-value">${formatDate(request.request_date)}</div>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Purpose/Justification</div>
                        <div class="detail-value">${request.purpose || 'No purpose specified'}</div>
                    </div>
                    
                    ${request.remarks ? `
                    <div class="detail-item">
                        <div class="detail-label">Remarks/Comments</div>
                        <div class="detail-value">${request.remarks}</div>
                    </div>
                    ` : ''}
                    
                    <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                        <button class="btn btn-info" onclick="trackRequestStatus(${request.id}); closeRequestModal();">
                            <i class="fas fa-search"></i> Track Status
                        </button>
                        <button class="btn btn-secondary" onclick="closeRequestModal()">
                            <i class="fas fa-times"></i> Close
                        </button>
                    </div>
                </div>
            `;

            document.getElementById('requestModal').classList.add('show');
        }

        // Track request status
        async function trackRequestStatus(requestId) {
            try {
                showAlert('🔄 Tracking request status...', 'info');
                
                const response = await fetch(`${API_BASE_URL}/Department/api/requests/status`, {
                    method: 'GET',
                    headers: headers
                });

                if (!response.ok) {
                    throw new Error(`Request tracking failed: ${response.status}`);
                }

                const statusData = await response.json();
                const targetRequest = statusData.find(req => req.id === requestId);
                
                if (targetRequest) {
                    showAlert(`📊 Request #${requestId} Status: ${targetRequest.progress || targetRequest.status}`, 'info');
                } else {
                    showAlert(`📋 Request #${requestId} not found in status tracking`, 'warning');
                }
                
            } catch (error) {
                console.error('Error tracking request:', error);
                
                // Fallback: show status from local data
                const request = allRequests.find(r => r.id === requestId);
                if (request) {
                    let statusMessage = '';
                    switch(request.status) {
                        case 'pending':
                            statusMessage = 'Your request is pending approval';
                            break;
                        case 'approved':
                            statusMessage = 'Your request has been approved and is being processed';
                            break;
                        case 'denied':
                            statusMessage = 'Your request has been denied';
                            break;
                        case 'in_progress':
                            statusMessage = 'Your request is currently in progress';
                            break;
                        default:
                            statusMessage = 'Request status unknown';
                    }
                    showAlert(`📊 Request #${requestId}: ${statusMessage}`, 'info');
                } else {
                    showAlert('❌ Error tracking request: ' + error.message, 'error');
                }
            }
        }

        // Create new request
        function createNewRequest() {
            // Navigate to new request page or show modal
            window.location.href = '../requests/new-request.php';
        }

        // Export my requests
        function exportMyRequests() {
            if (filteredRequests.length === 0) {
                showAlert('📭 No requests to export', 'warning');
                return;
            }

            const csvContent = [
                ['Request ID', 'Item', 'Quantity', 'Priority', 'Status', 'Purpose', 'Date Submitted', 'Remarks'],
                ...filteredRequests.map(req => [
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
            
            showAlert('📊 Your requests exported successfully!', 'success');
        }

        // Close request modal
        function closeRequestModal() {
            document.getElementById('requestModal').classList.remove('show');
        }

        // Refresh requests
        function refreshRequests() {
            console.log('🔄 Refreshing requests...');
            
            // Reset loading states
            document.getElementById('totalRequests').innerHTML = '<div class="loading-skeleton" style="width: 40px; height: 32px;"></div>';
            document.getElementById('pendingRequests').innerHTML = '<div class="loading-skeleton" style="width: 40px; height: 32px;"></div>';
            document.getElementById('approvedRequests').innerHTML = '<div class="loading-skeleton" style="width: 40px; height: 32px;"></div>';
            document.getElementById('deniedRequests').innerHTML = '<div class="loading-skeleton" style="width: 40px; height: 32px;"></div>';
            
            document.getElementById('requestsTableBody').innerHTML = `
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem;">
                        <i class="fas fa-spinner fa-spin"></i> Refreshing your requests...
                    </td>
                </tr>
            `;
            
            // Reload data
            loadMyRequests();
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
        window.createNewRequest = createNewRequest;
        window.exportMyRequests = exportMyRequests;
        window.viewRequestDetails = viewRequestDetails;
        window.trackRequestStatus = trackRequestStatus;
        window.filterByStatus = filterByStatus;
        window.closeRequestModal = closeRequestModal;

        // Initialize
        console.log('📋 MSICT Department My Requests Initialized');
        console.log('🔗 API Base URL:', API_BASE_URL);
        console.log('🔑 JWT Token Status:', token ? 'Present' : 'Missing');
        console.log('👤 User Role: Department');
        console.log('📡 Using API Endpoints:');
        console.log('  - GET /Department/api/requests/my - Load user requests');
        console.log('  - GET /Department/api/requests/view?id=X - View request details');
        console.log('  - GET /Department/api/requests/status - Track request status');
    </script>
</body>
</html>