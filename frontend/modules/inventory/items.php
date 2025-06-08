<?php
// modules/inventory/items.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit();
}

// Check if user has permission to manage inventory
$allowed_roles = ['Admin', 'QuarterMaster'];
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
    <title>Inventory Management - MSICT Ordering System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* Inventory Management Styles */
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
        .inventory-container {
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

        /* Category Badges */
        .category-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            color: white;
        }

        /* Stock Level Indicators */
        .stock-level {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .stock-level.high { background: #d4edda; color: #155724; }
        .stock-level.medium { background: #fff3cd; color: #856404; }
        .stock-level.low { background: #f8d7da; color: #721c24; }
        .stock-level.out { background: #e2e3e5; color: #383d41; }

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

        .stat-card.total-items { --card-bg-1: #667eea; --card-bg-2: #764ba2; }
        .stat-card.low-stock { --card-bg-1: #f093fb; --card-bg-2: #f5576c; }
        .stat-card.categories { --card-bg-1: #43e97b; --card-bg-2: #38f9d7; }
        .stat-card.out-stock { --card-bg-1: #4facfe; --card-bg-2: #00f2fe; }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
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

        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
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

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideInUp {
            from { 
                opacity: 0;
                transform: translateY(30px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .inventory-container {
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
        <div class="inventory-container">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-boxes"></i>
                        Inventory Management
                    </h1>
                    <p class="page-subtitle">Manage inventory items and stock levels</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-success" onclick="showAddItemModal()">
                        <i class="fas fa-plus"></i>
                        Add New Item
                    </button>
                    <button class="btn btn-primary" onclick="refreshItems()">
                        <i class="fas fa-sync"></i>
                        Refresh
                    </button>
                    <button class="btn btn-info" onclick="exportItems()">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- Inventory Statistics -->
            <div class="stats-row">
                <div class="stat-card total-items">
                    <div class="stat-value" id="totalItems">-</div>
                    <div class="stat-label">Total Items</div>
                </div>
                <div class="stat-card low-stock">
                    <div class="stat-value" id="lowStockItems">-</div>
                    <div class="stat-label">Low Stock</div>
                </div>
                <div class="stat-card categories">
                    <div class="stat-value" id="totalCategories">-</div>
                    <div class="stat-label">Categories</div>
                </div>
                <div class="stat-card out-stock">
                    <div class="stat-value" id="outOfStockItems">-</div>
                    <div class="stat-label">Out of Stock</div>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="search-filter-bar">
                <input type="text" id="searchInput" class="search-input" placeholder="🔍 Search items by name, SKU, or description...">
                <select id="categoryFilter" class="filter-select">
                    <option value="">All Categories</option>
                    <!-- Categories will be loaded dynamically -->
                </select>
                <select id="stockFilter" class="filter-select">
                    <option value="">All Stock Levels</option>
                    <option value="high">High Stock</option>
                    <option value="medium">Medium Stock</option>
                    <option value="low">Low Stock</option>
                    <option value="out">Out of Stock</option>
                </select>
            </div>

            <!-- Items Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-warehouse"></i>
                        Inventory Items
                    </h3>
                    <div>
                        <span id="itemCount">Loading...</span> items found
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table" id="itemsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Item Name</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Unit</th>
                                    <th>Current Stock</th>
                                    <th>Reorder Level</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <tr>
                                    <td colspan="9" style="text-align: center; padding: 2rem;">
                                        <i class="fas fa-spinner fa-spin"></i> Loading items...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add/Edit Item Modal -->
    <div class="modal" id="itemModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Add New Item</h2>
                <button class="close-modal" onclick="closeItemModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="itemForm">
                    <input type="hidden" id="itemId">
                    
                    <div class="form-group">
                        <label class="form-label" for="itemName">
                            <i class="fas fa-box"></i>
                            Item Name
                        </label>
                        <input type="text" id="itemName" class="form-control" placeholder="Enter item name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="itemSku">
                            <i class="fas fa-barcode"></i>
                            SKU (Stock Keeping Unit)
                        </label>
                        <input type="text" id="itemSku" class="form-control" placeholder="Enter SKU" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="itemCategory">
                            <i class="fas fa-tags"></i>
                            Category
                        </label>
                        <select id="itemCategory" class="form-control" required>
                            <option value="">Select Category</option>
                            <!-- Categories will be loaded dynamically -->
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="itemUnit">
                            <i class="fas fa-ruler"></i>
                            Unit of Measurement
                        </label>
                        <input type="text" id="itemUnit" class="form-control" placeholder="e.g., pieces, kg, liters" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="itemDescription">
                            <i class="fas fa-align-left"></i>
                            Description
                        </label>
                        <textarea id="itemDescription" class="form-control" rows="3" placeholder="Enter item description"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="reorderLevel">
                            <i class="fas fa-exclamation-triangle"></i>
                            Reorder Level
                        </label>
                        <input type="number" id="reorderLevel" class="form-control" placeholder="Minimum stock level" min="0" required>
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                        <button type="button" class="btn btn-secondary" onclick="closeItemModal()">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-success" id="saveItemBtn">
                            <i class="fas fa-save"></i>
                            Save Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Item Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-exclamation-triangle" style="color: var(--danger);"></i>
                    Confirm Delete Item
                </h2>
                <button class="close-modal" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="deleteItemId">
                
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--danger), #e74c3c); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i class="fas fa-trash" style="color: white; font-size: 2rem;"></i>
                    </div>
                    
                    <h3 style="color: var(--danger); margin-bottom: 1rem;">Delete Inventory Item</h3>
                    
                    <p style="color: #666; margin-bottom: 1.5rem;">
                        Are you sure you want to delete this item? This action cannot be undone.
                    </p>
                    
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; border-left: 4px solid var(--danger); text-align: left;">
                        <strong>Item Details:</strong><br>
                        <strong>Name:</strong> <span id="deleteItemName"></span><br>
                        <strong>SKU:</strong> <span id="deleteItemSku"></span>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn" onclick="confirmDeleteItem()">
                        <i class="fas fa-trash"></i>
                        Yes, Delete Item
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Footer Component -->
    <?php include '../../dashboard/components/footer.php'; ?>

    <script>
        // API Configuration
        const API_BASE_URL = 'http://localhost/students-proj/unfedZombie/Controllers/admin';
        let items = [];
        let categories = [];
        let filteredItems = [];

        // Get JWT token from session
        const token = '<?php echo $_SESSION["jwt_token"] ?? ""; ?>';

        // API Headers
        const headers = {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        };

        // User role for conditional actions
        const userRole = '<?php echo $_SESSION["user_role"] ?? "QuarterMaster"; ?>';

        // Load data on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Inventory Management page loaded, initializing...');
            loadItems();
            loadCategories();
            setupEventListeners();
            setupTableEventDelegation();
        });

        // Setup event listeners
        function setupEventListeners() {
            console.log('📋 Setting up event listeners...');
            // Search and filter functionality
            document.getElementById('searchInput').addEventListener('input', filterItems);
            document.getElementById('categoryFilter').addEventListener('change', filterItems);
            document.getElementById('stockFilter').addEventListener('change', filterItems);

            // Form submission
            document.getElementById('itemForm').addEventListener('submit', handleItemSubmit);
        }

        // Setup event delegation for table buttons
        function setupTableEventDelegation() {
            console.log('🎯 Setting up event delegation for inventory table...');
            document.getElementById('itemsTable').addEventListener('click', handleTableClick);
        }

        // Handle table button clicks
        function handleTableClick(e) {
            // Handle edit item button clicks
            if (e.target.closest('.edit-item-btn')) {
                e.preventDefault();
                e.stopPropagation();
                const button = e.target.closest('.edit-item-btn');
                const itemId = parseInt(button.getAttribute('data-item-id'));
                console.log('✏️ Edit item button clicked for ID:', itemId);
                if (itemId && !isNaN(itemId)) {
                    showEditItemModal(itemId);
                }
            }

            // Handle delete item button clicks
            if (e.target.closest('.delete-item-btn')) {
                e.preventDefault();
                e.stopPropagation();
                const button = e.target.closest('.delete-item-btn');
                const itemId = parseInt(button.getAttribute('data-item-id'));
                console.log('🗑️ Delete item button clicked for ID:', itemId);
                if (itemId && !isNaN(itemId)) {
                    showDeleteItemModal(itemId);
                }
            }
        }

        // Load items from API
        async function loadItems() {
            try {
                showAlert('🔄 Loading inventory items...', 'info');
                
                const apiUrl = `${API_BASE_URL}/api/admin/getitems`;
                
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
                    items = apiResponse;
                    // Ensure all item IDs are numbers for consistency
                    items = items.map(item => ({
                        ...item,
                        id: parseInt(item.id),
                        current_stock: parseInt(item.current_stock || 0),
                        reorder_level: parseInt(item.reorder_level || 0)
                    }));
                } else if (apiResponse.data && Array.isArray(apiResponse.data)) {
                    items = apiResponse.data.map(item => ({
                        ...item,
                        id: parseInt(item.id),
                        current_stock: parseInt(item.current_stock || 0),
                        reorder_level: parseInt(item.reorder_level || 0)
                    }));
                } else if (apiResponse.message) {
                    throw new Error(apiResponse.message);
                } else {
                    throw new Error('Unexpected response format');
                }

                console.log('✅ Loaded items:', items);

                filteredItems = [...items];
                displayItems();
                updateInventoryStats();
                
                showAlert(`✅ Loaded ${items.length} inventory items!`, 'success');
                
            } catch (error) {
                console.error('❌ Error loading items:', error);
                showAlert('❌ Error loading items: ' + error.message, 'error');
                
                // Fallback to mock data for demo
                console.log('🔄 Using mock data as fallback...');
                items = [
                    { 
                        id: 1, 
                        name: 'Office Pens',
                        sku: 'PEN001',
                        category_id: 'Office Supplies',
                        unit: 'pieces',
                        description: 'Blue ink ballpoint pens',
                        current_stock: 150,
                        reorder_level: 50
                    },
                    { 
                        id: 2, 
                        name: 'Laptop Computers',
                        sku: 'LAP001',
                        category_id: 'Electronics',
                        unit: 'units',
                        description: 'HP ProBook 450 G8',
                        current_stock: 5,
                        reorder_level: 10
                    },
                    { 
                        id: 3, 
                        name: 'Filing Cabinets',
                        sku: 'FIL001',
                        category_id: 'Furniture',
                        unit: 'units',
                        description: '4-drawer steel filing cabinet',
                        current_stock: 0,
                        reorder_level: 5
                    },
                    { 
                        id: 4, 
                        name: 'Copy Paper',
                        sku: 'PAP001',
                        category_id: 'Office Supplies',
                        unit: 'reams',
                        description: 'A4 80gsm white copy paper',
                        current_stock: 75,
                        reorder_level: 25
                    }
                ];

                filteredItems = [...items];
                displayItems();
                updateInventoryStats();
                
                showAlert('⚠️ Using demo data - API connection failed', 'warning');
            }
        }

        // Load categories from API
        async function loadCategories() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/admin/item-categories`, {
                    method: 'GET',
                    headers: headers
                });

                if (response.ok) {
                    const categoriesData = await response.json();
                    categories = Array.isArray(categoriesData) ? categoriesData : [];
                    populateCategoryDropdowns();
                } else {
                    console.log('Using fallback categories...');
                    categories = [
                        { id: 1, name: 'Office Supplies' },
                        { id: 2, name: 'Electronics' },
                        { id: 3, name: 'Furniture' },
                        { id: 4, name: 'Stationery' },
                        { id: 5, name: 'Equipment' }
                    ];
                    populateCategoryDropdowns();
                }
            } catch (error) {
                console.error('Error loading categories:', error);
                // Use fallback categories
                categories = [
                    { id: 1, name: 'Office Supplies' },
                    { id: 2, name: 'Electronics' },
                    { id: 3, name: 'Furniture' },
                    { id: 4, name: 'Stationery' },
                    { id: 5, name: 'Equipment' }
                ];
                populateCategoryDropdowns();
            }
        }

        // Populate category dropdowns
        function populateCategoryDropdowns() {
            const categoryFilter = document.getElementById('categoryFilter');
            const itemCategory = document.getElementById('itemCategory');
            
            // Clear existing options (except first)
            categoryFilter.innerHTML = '<option value="">All Categories</option>';
            itemCategory.innerHTML = '<option value="">Select Category</option>';
            
            categories.forEach(category => {
                // Filter dropdown
                const filterOption = document.createElement('option');
                filterOption.value = category.name;
                filterOption.textContent = category.name;
                categoryFilter.appendChild(filterOption);
                
                // Form dropdown
                const formOption = document.createElement('option');
                formOption.value = category.id;
                formOption.textContent = category.name;
                itemCategory.appendChild(formOption);
            });
        }

        // Display items in table
        function displayItems() {
            console.log('📊 Displaying items:', filteredItems);
            const tbody = document.getElementById('itemsTableBody');
            const itemCount = document.getElementById('itemCount');
            
            if (filteredItems.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 2rem;">
                            <i class="fas fa-boxes"></i> No items found
                        </td>
                    </tr>
                `;
                itemCount.textContent = '0';
                return;
            }

            tbody.innerHTML = filteredItems.map(item => {
                const stockLevel = getStockLevel(item);
                
                return `
                    <tr>
                        <td><strong>#${item.id}</strong></td>
                        <td>
                            <div>
                                <strong>${item.name}</strong><br>
                                <small style="color: #666;">${item.description || 'No description'}</small>
                            </div>
                        </td>
                        <td><code>${item.sku}</code></td>
                        <td><span class="category-badge">${item.category_id || 'Uncategorized'}</span></td>
                        <td>${item.unit}</td>
                        <td><strong>${item.current_stock || 0}</strong></td>
                        <td>${item.reorder_level || 0}</td>
                        <td><span class="stock-level ${stockLevel.class}">${stockLevel.text}</span></td>
                        <td>
                            <button class="btn btn-info btn-sm edit-item-btn" data-item-id="${item.id}" title="Edit Item">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm delete-item-btn" data-item-id="${item.id}" title="Delete Item">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');

            itemCount.textContent = filteredItems.length;
            console.log('✅ Items table updated with event delegation handling button clicks');
        }

        // Get stock level status
        function getStockLevel(item) {
            const stock = item.current_stock || 0;
            const reorderLevel = item.reorder_level || 0;
            
            if (stock === 0) {
                return { class: 'out', text: 'Out of Stock' };
            } else if (stock <= reorderLevel) {
                return { class: 'low', text: 'Low Stock' };
            } else if (stock <= reorderLevel * 2) {
                return { class: 'medium', text: 'Medium Stock' };
            } else {
                return { class: 'high', text: 'High Stock' };
            }
        }

        // Update inventory statistics
        function updateInventoryStats() {
            const totalItems = items.length;
            const lowStockItems = items.filter(item => {
                const stock = item.current_stock || 0;
                const reorderLevel = item.reorder_level || 0;
                return stock > 0 && stock <= reorderLevel;
            }).length;
            const outOfStockItems = items.filter(item => (item.current_stock || 0) === 0).length;
            const totalCategories = [...new Set(items.map(item => item.category_id))].length;

            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('lowStockItems').textContent = lowStockItems;
            document.getElementById('outOfStockItems').textContent = outOfStockItems;
            document.getElementById('totalCategories').textContent = totalCategories;
        }

        // Filter items
        function filterItems() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value;
            const stockFilter = document.getElementById('stockFilter').value;

            filteredItems = items.filter(item => {
                // Search filter
                const matchesSearch = !searchTerm || 
                    item.name.toLowerCase().includes(searchTerm) ||
                    item.sku.toLowerCase().includes(searchTerm) ||
                    (item.description || '').toLowerCase().includes(searchTerm);

                // Category filter
                const matchesCategory = !categoryFilter || item.category_id === categoryFilter;

                // Stock level filter
                let matchesStock = true;
                if (stockFilter) {
                    const stockLevel = getStockLevel(item);
                    matchesStock = stockLevel.class === stockFilter;
                }

                return matchesSearch && matchesCategory && matchesStock;
            });

            displayItems();
        }

        // Show Add Item Modal
        function showAddItemModal() {
            console.log('➕ Opening Add Item Modal');
            document.getElementById('modalTitle').textContent = 'Add New Item';
            document.getElementById('itemForm').reset();
            document.getElementById('itemId').value = '';
            document.getElementById('itemModal').classList.add('show');
        }

        // Show Edit Item Modal
        function showEditItemModal(itemId) {
            console.log('✏️ Opening Edit Item Modal for ID:', itemId);
            
            const item = items.find(i => parseInt(i.id) === parseInt(itemId));
            if (item) {
                console.log('✅ Found item:', item);
                document.getElementById('modalTitle').textContent = 'Edit Item';
                document.getElementById('itemId').value = item.id;
                document.getElementById('itemName').value = item.name;
                document.getElementById('itemSku').value = item.sku;
                document.getElementById('itemCategory').value = item.category_id;
                document.getElementById('itemUnit').value = item.unit;
                document.getElementById('itemDescription').value = item.description || '';
                document.getElementById('reorderLevel').value = item.reorder_level || 0;
                document.getElementById('itemModal').classList.add('show');
            } else {
                console.error('❌ Item not found:', itemId);
                showAlert('❌ Item not found!', 'error');
            }
        }

        // Show Delete Item Modal
        function showDeleteItemModal(itemId) {
            console.log('🗑️ Opening Delete Item Modal for ID:', itemId);
            
            const item = items.find(i => parseInt(i.id) === parseInt(itemId));
            if (item) {
                console.log('✅ Found item:', item);
                document.getElementById('deleteItemName').textContent = item.name;
                document.getElementById('deleteItemSku').textContent = item.sku;
                document.getElementById('deleteItemId').value = item.id;
                document.getElementById('deleteModal').classList.add('show');
            } else {
                console.error('❌ Item not found:', itemId);
                showAlert('❌ Item not found!', 'error');
            }
        }

        // Close Item Modal
        function closeItemModal() {
            console.log('❌ Closing Item Modal');
            document.getElementById('itemModal').classList.remove('show');
        }

        // Close Delete Modal
        function closeDeleteModal() {
            console.log('❌ Closing Delete Modal');
            document.getElementById('deleteModal').classList.remove('show');
        }

        // Handle form submission
        async function handleItemSubmit(e) {
            e.preventDefault();
            console.log('💾 Submitting item form...');
            
            const submitBtn = document.getElementById('saveItemBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<div class="loading"></div> Saving...';
            submitBtn.disabled = true;

            try {
                const itemId = document.getElementById('itemId').value;
                const itemData = {
                    name: document.getElementById('itemName').value,
                    sku: document.getElementById('itemSku').value,
                    category_id: parseInt(document.getElementById('itemCategory').value),
                    unit: document.getElementById('itemUnit').value,
                    description: document.getElementById('itemDescription').value,
                    reorder_level: parseInt(document.getElementById('reorderLevel').value)
                };

                console.log('💾 Item data to save:', itemData);

                // Try actual API call first
                let apiUrl, method;
                
                if (itemId) {
                    apiUrl = `${API_BASE_URL}/api/admin/items/${itemId}`;
                    method = 'PUT';
                } else {
                    apiUrl = `${API_BASE_URL}/api/admin/items`;
                    method = 'POST';
                }

                try {
                    const response = await fetch(apiUrl, {
                        method: method,
                        headers: headers,
                        body: JSON.stringify(itemData)
                    });

                    const responseText = await response.text();
                    console.log('📡 Response:', responseText);

                    let result;
                    try {
                        result = JSON.parse(responseText);
                    } catch (e) {
                        throw new Error('Invalid JSON response: ' + responseText);
                    }

                    if (!response.ok) {
                        throw new Error(result.message || result.error || `HTTP ${response.status}`);
                    }

                    showAlert(`✅ Item ${itemId ? 'updated' : 'created'} successfully!`, 'success');
                    closeItemModal();
                    loadItems(); // Reload from API

                } catch (apiError) {
                    console.log('⚠️ API call failed, using mock update:', apiError.message);
                    
                    // Fallback to mock data update
                    if (itemId) {
                        // Update existing item in mock data
                        const itemIndex = items.findIndex(i => i.id === parseInt(itemId));
                        if (itemIndex !== -1) {
                            items[itemIndex] = {
                                ...items[itemIndex],
                                ...itemData,
                                id: parseInt(itemId)
                            };
                        }
                    } else {
                        // Add new item to mock data
                        const newItem = {
                            ...itemData,
                            id: Math.max(...items.map(i => i.id)) + 1,
                            current_stock: 0
                        };
                        items.push(newItem);
                    }

                    showAlert(`✅ Item ${itemId ? 'updated' : 'created'} successfully! (Demo mode)`, 'success');
                    closeItemModal();
                    
                    // Refresh display
                    filteredItems = [...items];
                    displayItems();
                    updateInventoryStats();
                }

            } catch (error) {
                console.error('❌ Error saving item:', error);
                showAlert('❌ Error saving item: ' + error.message, 'error');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }

        // Confirm delete item
        async function confirmDeleteItem() {
            console.log('🗑️ Confirming item deletion...');
            const deleteBtn = document.getElementById('confirmDeleteBtn');
            const originalText = deleteBtn.innerHTML;
            deleteBtn.innerHTML = '<div class="loading"></div> Deleting...';
            deleteBtn.disabled = true;

            try {
                const itemId = parseInt(document.getElementById('deleteItemId').value);
                
                console.log('🗑️ Deleting item ID:', itemId);
                
                // Try actual API call first
                try {
                    const response = await fetch(`${API_BASE_URL}/api/admin/items/${itemId}`, {
                        method: 'DELETE',
                        headers: headers
                    });

                    const responseText = await response.text();
                    console.log('📡 Delete response:', responseText);

                    let result;
                    try {
                        result = JSON.parse(responseText);
                    } catch (e) {
                        throw new Error('Invalid JSON response: ' + responseText);
                    }

                    if (!response.ok) {
                        throw new Error(result.message || result.error || `HTTP ${response.status}`);
                    }

                    showAlert('🗑️ Item deleted successfully!', 'success');
                    closeDeleteModal();
                    loadItems(); // Reload from API

                } catch (apiError) {
                    console.log('⚠️ API call failed, using mock delete:', apiError.message);
                    
                    // Fallback to mock data deletion
                    items = items.filter(i => i.id !== itemId);

                    showAlert('🗑️ Item deleted successfully! (Demo mode)', 'success');
                    closeDeleteModal();
                    
                    // Refresh display
                    filteredItems = [...items];
                    displayItems();
                    updateInventoryStats();
                }
                
            } catch (error) {
                console.error('❌ Error deleting item:', error);
                showAlert('❌ Error deleting item: ' + error.message, 'error');
            } finally {
                deleteBtn.innerHTML = originalText;
                deleteBtn.disabled = false;
            }
        }

        // Refresh items
        function refreshItems() {
            console.log('🔄 Refreshing items...');
            loadItems();
            loadCategories();
        }

        // Export items
        function exportItems() {
            console.log('📁 Exporting items...');
            
            const csvContent = [
                ['ID', 'Name', 'SKU', 'Category', 'Unit', 'Current Stock', 'Reorder Level', 'Status', 'Description'],
                ...filteredItems.map(item => {
                    const stockLevel = getStockLevel(item);
                    return [
                        item.id,
                        item.name,
                        item.sku,
                        item.category_id || 'Uncategorized',
                        item.unit,
                        item.current_stock || 0,
                        item.reorder_level || 0,
                        stockLevel.text,
                        item.description || 'No description'
                    ];
                })
            ].map(row => row.join(',')).join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `MSICT_Inventory_Items_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            showAlert('📁 Inventory items exported successfully!', 'success');
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
        document.getElementById('itemModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeItemModal();
            }
        });

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeItemModal();
                closeDeleteModal();
            }
        });

        // Make functions globally accessible
        window.showAddItemModal = showAddItemModal;
        window.closeItemModal = closeItemModal;
        window.closeDeleteModal = closeDeleteModal;
        window.confirmDeleteItem = confirmDeleteItem;
        window.refreshItems = refreshItems;
        window.exportItems = exportItems;

        // Initialize system
        console.log('📦 MSICT Inventory Management System Initialized');
        console.log('🔗 API Base URL:', API_BASE_URL);
        console.log('🔗 Items API Endpoint:', `${API_BASE_URL}/api/admin/getitems`);
        console.log('🔑 JWT Token Status:', token ? 'Present' : 'Missing');
        console.log('👤 Current User Role:', userRole);
        
        // Show welcome message after page loads
        setTimeout(() => {
            showAlert('📦 Inventory Management System Ready!', 'success');
        }, 2000);
    </script>
</body>
</html>