<?php
// dashboard/quartermaster-dashboard.php - QuarterMaster Dashboard
session_start();

// Check if user is logged in and is QuarterMaster
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'QuarterMaster') {
    header('Location: ../auth/login.php');
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
    <title>QuarterMaster Dashboard - MSICT Ordering System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Essential Dashboard Styles */
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

        .stat-card.stock { --card-bg-1: #667eea; --card-bg-2: #764ba2; }
        .stat-card.requests { --card-bg-1: #f093fb; --card-bg-2: #f5576c; }
        .stat-card.dispatched { --card-bg-1: #43e97b; --card-bg-2: #38f9d7; }
        .stat-card.items { --card-bg-1: #4facfe; --card-bg-2: #00f2fe; }

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

        .status-badge.authorized { background: #cce5ff; color: #0056b3; }
        .status-badge.low-stock { background: #f8d7da; color: #721c24; }
        .status-badge.in-stock { background: #d4edda; color: #155724; }

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

        .btn-warning {
            background: #ffc107;
            color: #212529;
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
                        <i class="fas fa-warehouse"></i>
                        QuarterMaster Dashboard
                    </h1>
                </div>
                <div class="page-actions">
                    <button class="btn btn-success" onclick="refreshDashboard()">
                        <i class="fas fa-sync"></i>
                        Refresh
                    </button>
                    <button class="btn btn-primary" onclick="addNewItem()">
                        <i class="fas fa-plus"></i>
                        Add Item
                    </button>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- Main Stats -->
            <div class="stats-grid">
                <div class="stat-card stock" onclick="viewStock()">
                    <div class="stat-value" id="totalStock">
                        <div class="loading-skeleton" style="width: 60px; height: 32px; border-radius: 4px;"></div>
                    </div>
                    <div class="stat-label">Total Stock Items</div>
                </div>
                <div class="stat-card requests" onclick="viewRequests()">
                    <div class="stat-value" id="authorizedRequests">
                        <div class="loading-skeleton" style="width: 40px; height: 32px; border-radius: 4px;"></div>
                    </div>
                    <div class="stat-label">Ready to Dispatch</div>
                </div>
                <div class="stat-card dispatched" onclick="viewDispatches()">
                    <div class="stat-value" id="dispatchedToday">
                        <div class="loading-skeleton" style="width: 50px; height: 32px; border-radius: 4px;"></div>
                    </div>
                    <div class="stat-label">Dispatched Today</div>
                </div>
                <div class="stat-card items" onclick="viewLowStock()">
                    <div class="stat-value" id="lowStockItems">
                        <div class="loading-skeleton" style="width: 30px; height: 32px; border-radius: 4px;"></div>
                    </div>
                    <div class="stat-label">Low Stock Items</div>
                </div>
            </div>

            <!-- Current Stock Overview -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-boxes"></i>
                        Stock Overview
                    </h3>
                    <button class="btn btn-primary btn-sm" onclick="viewStock()">
                        View All Stock
                    </button>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="stockTable">
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2rem;">
                                    <div class="loading-skeleton" style="width: 100%; height: 20px; border-radius: 4px; margin-bottom: 10px;"></div>
                                    <div class="loading-skeleton" style="width: 80%; height: 20px; border-radius: 4px; margin-bottom: 10px;"></div>
                                    <div class="loading-skeleton" style="width: 90%; height: 20px; border-radius: 4px;"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Ready to Dispatch -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shipping-fast"></i>
                        Ready to Dispatch
                    </h3>
                    <button class="btn btn-primary btn-sm" onclick="viewRequests()">
                        View All Requests
                    </button>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Requested By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="requestsTable">
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2rem;">
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
                        <button class="btn btn-primary" onclick="viewStock()">
                            <i class="fas fa-boxes"></i>
                            View Stock
                        </button>
                        <button class="btn btn-info" onclick="addNewItem()">
                            <i class="fas fa-plus"></i>
                            Add New Item
                        </button>
                        <button class="btn btn-success" onclick="viewRequests()">
                            <i class="fas fa-clipboard-list"></i>
                            Dispatch Requests
                        </button>
                        <button class="btn btn-warning" onclick="viewLowStock()">
                            <i class="fas fa-exclamation-triangle"></i>
                            Low Stock Alert
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
        const API_BASE_URL = 'http://localhost/unfedZombie/Controllers/QuarterMaster/api';
        let dashboardData = {
            stock: [],
            requests: []
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
            console.log('📦 MSICT QuarterMaster Dashboard Loading...');
            loadDashboardData();
        });

        // Load all dashboard data
        async function loadDashboardData() {
            try {
                showAlert('🔄 Loading dashboard data...', 'info');
                
                // Load data in parallel
                const [stockData, requestsData] = await Promise.allSettled([
                    loadStock(),
                    loadRequests()
                ]);

                // Process results
                if (stockData.status === 'fulfilled') {
                    dashboardData.stock = stockData.value;
                    updateStockStats(stockData.value);
                    displayStock(stockData.value);
                } else {
                    console.error('Failed to load stock:', stockData.reason);
                    document.getElementById('totalStock').textContent = 'Error';
                    document.getElementById('lowStockItems').textContent = 'Error';
                }

                if (requestsData.status === 'fulfilled') {
                    dashboardData.requests = requestsData.value;
                    updateRequestStats(requestsData.value);
                    displayRequests(requestsData.value);
                } else {
                    console.error('Failed to load requests:', requestsData.reason);
                    document.getElementById('authorizedRequests').textContent = 'Error';
                }

                // Set dispatched today (placeholder - you may need a separate API)
                document.getElementById('dispatchedToday').textContent = '0';

                showAlert('✅ Dashboard loaded successfully!', 'success');
                
            } catch (error) {
                console.error('Error loading dashboard:', error);
                showAlert('❌ Error loading dashboard: ' + error.message, 'error');
            }
        }

        // Load stock from API
        async function loadStock() {
    try {
        console.log('🔗 Loading stock from:', `${API_BASE_URL}/stock/view`);
        
        const response = await fetch(`${API_BASE_URL}/stock/view`, {
            method: 'GET',
            headers: headers
        });

        console.log('📡 Stock response status:', response.status);

        if (!response.ok) {
            if (response.status === 404) {
                console.warn('⚠️ Stock API endpoint not found');
                return []; // Return empty array instead of throwing error
            }
            throw new Error(`Stock API Error: ${response.status}`);
        }

        const stock = await response.json();
        console.log('✅ Loaded stock:', stock);
        
        return Array.isArray(stock) ? stock : [];
        
    } catch (error) {
        console.error('❌ Error loading stock:', error);
        
        // Return mock data as fallback
        console.log('🔄 Using fallback data for stock');
        return [
            {
                id: 1,
                name: 'Office Chairs',
                quantity: 25,
                unit: 'pieces'
            },
            {
                id: 2,
                name: 'Laptops',
                quantity: 8,
                unit: 'pieces'
            },
            {
                id: 3,
                name: 'Printers',
                quantity: 3,
                unit: 'pieces'
            }
        ];
    }
}

        // Load requests from API
        async function loadRequests() {
    try {
        console.log('🔗 Loading requests from:', `${API_BASE_URL}/requests/ready`);
        
        const response = await fetch(`${API_BASE_URL}/requests/ready`, {
            method: 'GET',
            headers: headers
        });

        console.log('📡 Requests response status:', response.status);

        if (!response.ok) {
            if (response.status === 404) {
                console.warn('⚠️ Requests API endpoint not found');
                return []; // Return empty array instead of throwing error
            }
            throw new Error(`Requests API Error: ${response.status}`);
        }

        const requests = await response.json();
        console.log('✅ Loaded requests:', requests);
        
        return Array.isArray(requests) ? requests : [];
        
    } catch (error) {
        console.error('❌ Error loading requests:', error);
        
        // Return mock data as fallback
        console.log('🔄 Using fallback data for requests');
        return [
            {
                id: 1,
                item_name: 'Office Chairs',
                quantity_requested: 10,
                requested_by: 1
            },
            {
                id: 2,
                item_name: 'Laptops',
                quantity_requested: 5,
                requested_by: 2
            }
        ];
    }
}


        // Update stock statistics
        function updateStockStats(stock) {
            document.getElementById('totalStock').textContent = stock.length;
            
            // Count low stock items (you may need to adjust this logic based on your reorder levels)
            const lowStockCount = stock.filter(item => item.quantity < 10).length; // Assuming 10 is low stock threshold
            document.getElementById('lowStockItems').textContent = lowStockCount;
        }

        // Update request statistics
        function updateRequestStats(requests) {
            document.getElementById('authorizedRequests').textContent = requests.length;
        }

        // Display stock in table
        function displayStock(stock) {
            const tbody = document.getElementById('stockTable');
            
            if (!stock || stock.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem; color: #666;">
                            <i class="fas fa-inbox"></i>
                            <br>No stock items found
                        </td>
                    </tr>
                `;
                return;
            }

            // Show first 5 items
            const displayStock = stock.slice(0, 5);

            tbody.innerHTML = displayStock.map(item => {
                const status = getStockStatus(item.quantity);
                
                return `
                    <tr>
                        <td><strong>${item.name}</strong></td>
                        <td>${item.quantity}</td>
                        <td>${item.unit}</td>
                        <td><span class="status-badge ${status.class}">${status.text}</span></td>
                        <td>
                            <button class="btn btn-info btn-sm" onclick="updateStock(${item.id})" title="Update Stock">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Display requests in table
        function displayRequests(requests) {
            const tbody = document.getElementById('requestsTable');
            
            if (!requests || requests.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem; color: #666;">
                            <i class="fas fa-inbox"></i>
                            <br>No requests ready for dispatch
                        </td>
                    </tr>
                `;
                return;
            }

            // Show first 5 requests
            const displayRequests = requests.slice(0, 5);

            tbody.innerHTML = displayRequests.map(request => {
                return `
                    <tr>
                        <td><strong>#${request.id}</strong></td>
                        <td>${request.item_name}</td>
                        <td>${request.quantity_requested}</td>
                        <td>User #${request.requested_by}</td>
                        <td>
                            <button class="btn btn-success btn-sm" onclick="dispatchRequest(${request.id})" title="Dispatch">
                                <i class="fas fa-shipping-fast"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Get stock status
        function getStockStatus(quantity) {
            if (quantity < 10) {
                return { class: 'low-stock', text: 'Low Stock' };
            } else {
                return { class: 'in-stock', text: 'In Stock' };
            }
        }

        // Navigation functions
        function viewStock() {
            // Navigate to stock management page
            window.location.href = '../modules/inventory/stock.php';
        }

        function viewRequests() {
            // Navigate to requests page
            window.location.href = '../modules/quartermaster/requests.php';
        }

        function viewDispatches() {
            // Navigate to dispatch history
            window.location.href = '../modules/quartermaster/dispatches.php';
        }

        function viewLowStock() {
            // Navigate to low stock page
            window.location.href = '../modules/inventory/low-stock.php';
        }

        function addNewItem() {
            // Navigate to add item page
            window.location.href = '../modules/inventory/add-item.php';
        }

        // Action functions
        async function updateStock(itemId) {
            const newQuantity = prompt('Enter new quantity:');
            if (newQuantity === null || newQuantity === '') return;

            try {
                const response = await fetch(`${API_BASE_URL}/stock/update/${itemId}`, {
                    method: 'PUT',
                    headers: headers,
                    body: JSON.stringify({
                        quantity: parseInt(newQuantity),
                        last_updated: new Date().toISOString()
                    })
                });

                if (response.ok) {
                    showAlert('✅ Stock updated successfully!', 'success');
                    refreshDashboard();
                } else {
                    const error = await response.json();
                    showAlert('❌ Error updating stock: ' + (error.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                showAlert('❌ Error updating stock: ' + error.message, 'error');
            }
        }

        // Fixed dispatch function for dashboard
async function dispatchRequest(requestId) {
    if (!confirm('Are you sure you want to dispatch this request?')) return;

    try {
        console.log('🚚 Dispatching request ID:', requestId);
        showAlert('🚚 Dispatching item...', 'info');
        
        const response = await fetch(`${API_BASE_URL}/dispatch/item`, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({
                request_id: requestId
            })
        });

        console.log('📡 Dispatch response status:', response.status);

        if (!response.ok) {
            const errorText = await response.text();
            console.log('❌ Dispatch error response:', errorText);
            
            let errorData;
            try {
                errorData = JSON.parse(errorText);
            } catch (e) {
                throw new Error(`HTTP ${response.status}: ${errorText}`);
            }
            
            // Handle specific errors
            if (errorData.message && errorData.message.includes('Approved and authorized request not found')) {
                throw new Error('Request must be approved by CO (Commanding Officer) before dispatch');
            } else if (errorData.message && errorData.message.includes('Insufficient stock')) {
                throw new Error('Insufficient stock available for this item');
            } else {
                throw new Error(errorData.message || errorData.error || `HTTP ${response.status}`);
            }
        }

        const result = await response.json();
        console.log('✅ Dispatch success:', result);
        
        showAlert('✅ Item dispatched successfully!', 'success');
        refreshDashboard();
        
    } catch (error) {
        console.error('❌ Error dispatching item:', error);
        
        if (error.message.includes('CO (Commanding Officer)')) {
            showAlert('⚠️ Cannot dispatch: Request must be approved by CO first', 'warning');
        } else if (error.message.includes('Insufficient stock')) {
            showAlert('⚠️ Cannot dispatch: Insufficient stock available', 'warning');
        } else if (error.message.includes('404') || error.message.includes('Not Found')) {
            showAlert('⚠️ Dispatch API not found. Please check backend setup', 'warning');
        } else {
            showAlert('❌ Error dispatching item: ' + error.message, 'error');
        }
    }
}

        // Refresh dashboard
        function refreshDashboard() {
            console.log('🔄 Refreshing dashboard...');
            
            // Reset loading states
            document.getElementById('totalStock').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 32px; border-radius: 4px;"></div>';
            document.getElementById('authorizedRequests').innerHTML = '<div class="loading-skeleton" style="width: 40px; height: 32px; border-radius: 4px;"></div>';
            document.getElementById('dispatchedToday').innerHTML = '<div class="loading-skeleton" style="width: 50px; height: 32px; border-radius: 4px;"></div>';
            document.getElementById('lowStockItems').innerHTML = '<div class="loading-skeleton" style="width: 30px; height: 32px; border-radius: 4px;"></div>';
            
            document.getElementById('stockTable').innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem;">
                        <i class="fas fa-spinner fa-spin"></i> Loading stock...
                    </td>
                </tr>
            `;

            document.getElementById('requestsTable').innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem;">
                        <i class="fas fa-spinner fa-spin"></i> Loading requests...
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
        window.viewStock = viewStock;
        window.viewRequests = viewRequests;
        window.viewDispatches = viewDispatches;
        window.viewLowStock = viewLowStock;
        window.addNewItem = addNewItem;
        window.updateStock = updateStock;
        window.dispatchRequest = dispatchRequest;

        // Initialize
        console.log('📦 MSICT QuarterMaster Dashboard Initialized');
        console.log('🔗 API Base URL:', API_BASE_URL);
        console.log('🔑 JWT Token Status:', token ? 'Present' : 'Missing');
    </script>
</body>
</html>