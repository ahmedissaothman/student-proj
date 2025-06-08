<?php
// modules/quartermaster/stock-management.php - Comprehensive Stock Management
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
    <title>Stock Management - MSICT Ordering System</title>
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

        .filter-select {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            background: white;
            min-width: 150px;
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

        .status-badge.in-stock { background: #d4edda; color: #155724; }
        .status-badge.low-stock { background: #f8d7da; color: #721c24; }
        .status-badge.out-of-stock { background: #f0f0f0; color: #666; }

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

        /* Forms */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.9rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        /* Modals */
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
            max-width: 500px;
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
        }

        .stat-card.total { --card-bg-1: #667eea; --card-bg-2: #764ba2; }
        .stat-card.low { --card-bg-1: #f093fb; --card-bg-2: #f5576c; }
        .stat-card.out { --card-bg-1: #4facfe; --card-bg-2: #00f2fe; }

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
                        <i class="fas fa-boxes"></i>
                        Stock Management
                    </h1>
                </div>
                <div class="page-actions">
                    <button class="btn btn-success" onclick="showAddItemModal()">
                        <i class="fas fa-plus"></i>
                        Add Item
                    </button>
                    <button class="btn btn-info" onclick="refreshData()">
                        <i class="fas fa-sync"></i>
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab active" onclick="switchTab('overview')">
                    <i class="fas fa-chart-bar"></i>
                    Overview
                </button>
                <button class="tab" onclick="switchTab('stock')">
                    <i class="fas fa-warehouse"></i>
                    Current Stock
                </button>
                <button class="tab" onclick="switchTab('items')">
                    <i class="fas fa-cubes"></i>
                    Manage Items
                </button>
                <button class="tab" onclick="switchTab('alerts')">
                    <i class="fas fa-exclamation-triangle"></i>
                    Low Stock Alerts
                </button>
            </div>

            <!-- Overview Tab -->
            <div id="overview" class="tab-content active">
                <div class="stats-grid">
                    <div class="stat-card total">
                        <div class="stat-value" id="totalItems">0</div>
                        <div class="stat-label">Total Items</div>
                    </div>
                    <div class="stat-card low">
                        <div class="stat-value" id="lowStockCount">0</div>
                        <div class="stat-label">Low Stock Items</div>
                    </div>
                    <div class="stat-card out">
                        <div class="stat-value" id="outOfStockCount">0</div>
                        <div class="stat-label">Out of Stock</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line"></i>
                            Stock Summary
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Current Stock</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                        <th>Quick Update</th>
                                    </tr>
                                </thead>
                                <tbody id="overviewTable">
                                    <tr>
                                        <td colspan="5" class="loading">
                                            <div class="spinner"></div>
                                            <br>Loading stock data...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Stock Tab -->
            <div id="stock" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-warehouse"></i>
                            Current Stock Levels
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="search-filter-bar">
                            <div class="search-box">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" class="search-input" id="stockSearch" placeholder="Search items...">
                            </div>
                            <select class="filter-select" id="stockFilter">
                                <option value="">All Items</option>
                                <option value="in-stock">In Stock</option>
                                <option value="low-stock">Low Stock</option>
                                <option value="out-of-stock">Out of Stock</option>
                            </select>
                        </div>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Item Name</th>
                                        <th>Current Quantity</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="stockTable">
                                    <tr>
                                        <td colspan="6" class="loading">
                                            <div class="spinner"></div>
                                            <br>Loading stock data...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Manage Items Tab -->
            <div id="items" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cubes"></i>
                            Item Catalog
                        </h3>
                        <button class="btn btn-primary btn-sm" onclick="showAddItemModal()">
                            <i class="fas fa-plus"></i>
                            Add New Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="search-filter-bar">
                            <div class="search-box">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" class="search-input" id="itemSearch" placeholder="Search items...">
                            </div>
                        </div>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>SKU</th>
                                        <th>Description</th>
                                        <th>Unit</th>
                                        <th>Reorder Level</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTable">
                                    <tr>
                                        <td colspan="7" class="loading">
                                            <div class="spinner"></div>
                                            <br>Loading items...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alerts Tab -->
            <div id="alerts" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exclamation-triangle"></i>
                            Low Stock Alerts
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Current Stock</th>
                                        <th>Reorder Level</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="alertsTable">
                                    <tr>
                                        <td colspan="6" class="loading">
                                            <div class="spinner"></div>
                                            <br>Loading alerts...
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

    <!-- Add/Edit Item Modal -->
    <div id="itemModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Add New Item</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="itemForm">
                <input type="hidden" id="itemId">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Item Name *</label>
                        <input type="text" class="form-control" id="itemName" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">SKU *</label>
                        <input type="text" class="form-control" id="itemSku" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" id="itemDescription" rows="3"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Unit *</label>
                        <select class="form-control" id="itemUnit" required>
                            <option value="">Select Unit</option>
                            <option value="pcs">Pieces</option>
                            <option value="kg">Kilograms</option>
                            <option value="ltr">Liters</option>
                            <option value="box">Box</option>
                            <option value="pack">Pack</option>
                            <option value="m">Meters</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Reorder Level *</label>
                        <input type="number" class="form-control" id="itemReorderLevel" min="0" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Category ID</label>
                    <input type="number" class="form-control" id="itemCategoryId" min="1">
                </div>
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Item</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Stock Modal -->
    <div id="stockModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Update Stock</h3>
                <button class="close-btn" onclick="closeStockModal()">&times;</button>
            </div>
            <form id="stockForm">
                <input type="hidden" id="stockId">
                <div class="form-group">
                    <label class="form-label">Item Name</label>
                    <input type="text" class="form-control" id="stockItemName" readonly>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Current Quantity</label>
                        <input type="number" class="form-control" id="currentQuantity" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Quantity *</label>
                        <input type="number" class="form-control" id="newQuantity" min="0" required>
                    </div>
                </div>
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeStockModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Stock</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Include Footer Component -->
    <?php include '../../dashboard/components/footer.php'; ?>

    <script>
        // API Configuration
        const API_BASE_URL = 'http://localhost/unfedZombie/Controllers/QuarterMaster/api';
        let stockData = [];
        let itemsData = [];

        // Get JWT token from session
        const token = '<?php echo $_SESSION["jwt_token"] ?? ""; ?>';

        // API Headers
        const headers = {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        };

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📦 Stock Management System Loading...');
            loadAllData();
            setupEventListeners();
        });

        // Load all data
        async function loadAllData() {
            try {
                showAlert('🔄 Loading data...', 'info');
                
                const [stock, items] = await Promise.allSettled([
                    loadStock(),
                    loadItems()
                ]);

                if (stock.status === 'fulfilled') {
                    stockData = stock.value;
                    updateOverview();
                    displayStock();
                    displayAlerts();
                }

                if (items.status === 'fulfilled') {
                    itemsData = items.value;
                    displayItems();
                }

                showAlert('✅ Data loaded successfully!', 'success');
                
            } catch (error) {
                console.error('Error loading data:', error);
                showAlert('❌ Error loading data: ' + error.message, 'error');
            }
        }

        // Load stock data
        async function loadStock() {
            const response = await fetch(`${API_BASE_URL}/stock/view`, {
                method: 'GET',
                headers: headers
            });

            if (!response.ok) {
                throw new Error(`Stock API Error: ${response.status}`);
            }

            return await response.json();
        }

        // Load items data (you might need to create this endpoint or use existing one)
        async function loadItems() {
            // Since there's no direct items endpoint, we'll use stock data
            // You might want to create a separate endpoint for items
            return stockData || [];
        }

        // Tab switching
        function switchTab(tabName) {
            // Remove active class from all tabs and content
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Add active class to selected tab and content
            event.target.classList.add('active');
            document.getElementById(tabName).classList.add('active');
        }

        // Update overview stats
        function updateOverview() {
            const totalItems = stockData.length;
            const lowStockCount = stockData.filter(item => item.quantity <= 10).length; // Assuming 10 is low stock threshold
            const outOfStockCount = stockData.filter(item => item.quantity === 0).length;

            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('lowStockCount').textContent = lowStockCount;
            document.getElementById('outOfStockCount').textContent = outOfStockCount;

            // Display overview table
            const tbody = document.getElementById('overviewTable');
            if (stockData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 2rem;">No stock data available</td></tr>';
                return;
            }

            tbody.innerHTML = stockData.slice(0, 10).map(item => `
                <tr>
                    <td><strong>${item.name}</strong></td>
                    <td>${item.quantity}</td>
                    <td>${item.unit}</td>
                    <td>${getStatusBadge(item.quantity)}</td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="showStockModal(${item.id}, '${item.name}', ${item.quantity})">
                            <i class="fas fa-edit"></i>
                            Update
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Display stock table
        function displayStock() {
            const tbody = document.getElementById('stockTable');
            
            if (stockData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem;">No stock data available</td></tr>';
                return;
            }

            tbody.innerHTML = stockData.map(item => `
                <tr>
                    <td><strong>#${item.id}</strong></td>
                    <td>${item.name}</td>
                    <td>${item.quantity}</td>
                    <td>${item.unit}</td>
                    <td>${getStatusBadge(item.quantity)}</td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="showStockModal(${item.id}, '${item.name}', ${item.quantity})" title="Update Stock">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Display items table
        function displayItems() {
            const tbody = document.getElementById('itemsTable');
            
            if (stockData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 2rem;">No items available</td></tr>';
                return;
            }

            tbody.innerHTML = stockData.map(item => `
                <tr>
                    <td><strong>#${item.id}</strong></td>
                    <td>${item.name}</td>
                    <td>${item.sku || 'N/A'}</td>
                    <td>${item.description || 'No description'}</td>
                    <td>${item.unit}</td>
                    <td>${item.reorder_level || 'Not set'}</td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="editItem(${item.id})" title="Edit Item">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteItem(${item.id})" title="Delete Item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Display low stock alerts
        function displayAlerts() {
            const tbody = document.getElementById('alertsTable');
            const lowStockItems = stockData.filter(item => item.quantity <= 10); // Assuming 10 is threshold
            
            if (lowStockItems.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem; color: green;"><i class="fas fa-check-circle"></i><br>All items are adequately stocked!</td></tr>';
                return;
            }

            tbody.innerHTML = lowStockItems.map(item => `
                <tr>
                    <td><strong>${item.name}</strong></td>
                    <td>${item.quantity}</td>
                    <td>${item.reorder_level || 10}</td>
                    <td>${item.unit}</td>
                    <td>${getStatusBadge(item.quantity)}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="showStockModal(${item.id}, '${item.name}', ${item.quantity})">
                            <i class="fas fa-plus"></i>
                            Restock
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Get status badge
        function getStatusBadge(quantity) {
            if (quantity === 0) {
                return '<span class="status-badge out-of-stock">Out of Stock</span>';
            } else if (quantity <= 10) {
                return '<span class="status-badge low-stock">Low Stock</span>';
            } else {
                return '<span class="status-badge in-stock">In Stock</span>';
            }
        }

        // Setup event listeners
        function setupEventListeners() {
            // Search functionality
            document.getElementById('stockSearch').addEventListener('input', filterStock);
            document.getElementById('itemSearch').addEventListener('input', filterItems);
            document.getElementById('stockFilter').addEventListener('change', filterStock);

            // Form submissions
            document.getElementById('itemForm').addEventListener('submit', handleItemSubmit);
            document.getElementById('stockForm').addEventListener('submit', handleStockSubmit);
        }

        // Filter stock table
        function filterStock() {
            const searchTerm = document.getElementById('stockSearch').value.toLowerCase();
            const filterValue = document.getElementById('stockFilter').value;
            
            const filteredData = stockData.filter(item => {
                const matchesSearch = item.name.toLowerCase().includes(searchTerm);
                let matchesFilter = true;
                
                if (filterValue === 'in-stock') {
                    matchesFilter = item.quantity > 10;
                } else if (filterValue === 'low-stock') {
                    matchesFilter = item.quantity > 0 && item.quantity <= 10;
                } else if (filterValue === 'out-of-stock') {
                    matchesFilter = item.quantity === 0;
                }
                
                return matchesSearch && matchesFilter;
            });

            displayFilteredStock(filteredData);
        }

        // Display filtered stock
        function displayFilteredStock(data) {
            const tbody = document.getElementById('stockTable');
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem;">No items match your search criteria</td></tr>';
                return;
            }

            tbody.innerHTML = data.map(item => `
                <tr>
                    <td><strong>#${item.id}</strong></td>
                    <td>${item.name}</td>
                    <td>${item.quantity}</td>
                    <td>${item.unit}</td>
                    <td>${getStatusBadge(item.quantity)}</td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="showStockModal(${item.id}, '${item.name}', ${item.quantity})" title="Update Stock">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Filter items table
        function filterItems() {
            const searchTerm = document.getElementById('itemSearch').value.toLowerCase();
            
            const filteredData = stockData.filter(item => 
                item.name.toLowerCase().includes(searchTerm) ||
                (item.sku && item.sku.toLowerCase().includes(searchTerm))
            );

            displayFilteredItems(filteredData);
        }

        // Display filtered items
        function displayFilteredItems(data) {
            const tbody = document.getElementById('itemsTable');
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 2rem;">No items match your search criteria</td></tr>';
                return;
            }

            tbody.innerHTML = data.map(item => `
                <tr>
                    <td><strong>#${item.id}</strong></td>
                    <td>${item.name}</td>
                    <td>${item.sku || 'N/A'}</td>
                    <td>${item.description || 'No description'}</td>
                    <td>${item.unit}</td>
                    <td>${item.reorder_level || 'Not set'}</td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="editItem(${item.id})" title="Edit Item">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteItem(${item.id})" title="Delete Item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Modal functions
        function showAddItemModal() {
            document.getElementById('modalTitle').textContent = 'Add New Item';
            document.getElementById('submitBtn').textContent = 'Add Item';
            document.getElementById('itemForm').reset();
            document.getElementById('itemId').value = '';
            document.getElementById('itemModal').classList.add('show');
        }

        function editItem(itemId) {
            const item = stockData.find(i => i.id == itemId);
            if (!item) return;

            document.getElementById('modalTitle').textContent = 'Edit Item';
            document.getElementById('submitBtn').textContent = 'Update Item';
            
            document.getElementById('itemId').value = item.id;
            document.getElementById('itemName').value = item.name;
            document.getElementById('itemSku').value = item.sku || '';
            document.getElementById('itemDescription').value = item.description || '';
            document.getElementById('itemUnit').value = item.unit;
            document.getElementById('itemReorderLevel').value = item.reorder_level || '';
            document.getElementById('itemCategoryId').value = item.category_id || '';
            
            document.getElementById('itemModal').classList.add('show');
        }

        function showStockModal(itemId, itemName, currentQty) {
            document.getElementById('stockId').value = itemId;
            document.getElementById('stockItemName').value = itemName;
            document.getElementById('currentQuantity').value = currentQty;
            document.getElementById('newQuantity').value = currentQty;
            document.getElementById('stockModal').classList.add('show');
        }

        function closeModal() {
            document.getElementById('itemModal').classList.remove('show');
        }

        function closeStockModal() {
            document.getElementById('stockModal').classList.remove('show');
        }

        // Handle item form submission
        async function handleItemSubmit(e) {
            e.preventDefault();
            
            const itemId = document.getElementById('itemId').value;
            const formData = {
                name: document.getElementById('itemName').value,
                sku: document.getElementById('itemSku').value,
                description: document.getElementById('itemDescription').value,
                unit: document.getElementById('itemUnit').value,
                reorder_level: parseInt(document.getElementById('itemReorderLevel').value),
                category_id: parseInt(document.getElementById('itemCategoryId').value) || 1
            };

            try {
                let response;
                if (itemId) {
                    // Update existing item
                    response = await fetch(`${API_BASE_URL}/items/update?id=${itemId}`, {
                        method: 'PUT',
                        headers: headers,
                        body: JSON.stringify(formData)
                    });
                } else {
                    // Add new item
                    response = await fetch(`${API_BASE_URL}/items/add`, {
                        method: 'POST',
                        headers: headers,
                        body: JSON.stringify(formData)
                    });
                }

                if (response.ok) {
                    const result = await response.json();
                    showAlert(`✅ ${itemId ? 'Item updated' : 'Item added'} successfully!`, 'success');
                    closeModal();
                    await loadAllData(); // Refresh data
                } else {
                    const error = await response.json();
                    showAlert('❌ Error: ' + (error.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                showAlert('❌ Error submitting form: ' + error.message, 'error');
            }
        }

        // Handle stock form submission
        async function handleStockSubmit(e) {
            e.preventDefault();
            
            const itemId = document.getElementById('stockId').value;
            const newQuantity = parseInt(document.getElementById('newQuantity').value);

            try {
                const response = await fetch(`${API_BASE_URL}/stock/update?id=${itemId}`, {
                    method: 'PUT',
                    headers: headers,
                    body: JSON.stringify({
                        quantity: newQuantity,
                        last_updated: new Date().toISOString()
                    })
                });

                if (response.ok) {
                    showAlert('✅ Stock updated successfully!', 'success');
                    closeStockModal();
                    await loadAllData(); // Refresh data
                } else {
                    const error = await response.json();
                    showAlert('❌ Error updating stock: ' + (error.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error updating stock:', error);
                showAlert('❌ Error updating stock: ' + error.message, 'error');
            }
        }

        // Delete item
        async function deleteItem(itemId) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch(`${API_BASE_URL}/items/delete?id=${itemId}`, {
                    method: 'DELETE',
                    headers: headers
                });

                if (response.ok) {
                    showAlert('✅ Item deleted successfully!', 'success');
                    await loadAllData(); // Refresh data
                } else {
                    const error = await response.json();
                    showAlert('❌ Error deleting item: ' + (error.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error deleting item:', error);
                showAlert('❌ Error deleting item: ' + error.message, 'error');
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
                        'fa-info-circle';
            
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
        window.showAddItemModal = showAddItemModal;
        window.editItem = editItem;
        window.deleteItem = deleteItem;
        window.showStockModal = showStockModal;
        window.closeModal = closeModal;
        window.closeStockModal = closeStockModal;
        window.refreshData = refreshData;

        console.log('📦 Stock Management System Initialized');
    </script>
</body>
</html>