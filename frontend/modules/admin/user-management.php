<?php
// modules/admin/user-management.php
session_start();

// Check if user is logged in and is Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header('Location: ../../auth/login.php');
    exit();
}

// Set user data for components
$_SESSION['username'] = $_SESSION['username'] ?? 'admin001';
$_SESSION['full_name'] = $_SESSION['full_name'] ?? 'Administrator';
$_SESSION['user_role'] = 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - MSICT Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* User Management Styles */
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
        .user-management-container {
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

        /* Role Badges */
        .role-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .role-badge.admin { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
        .role-badge.quartermaster { background: linear-gradient(135deg, #f093fb, #f5576c); color: white; }
        .role-badge.department { background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; }
        .role-badge.co { background: linear-gradient(135deg, #43e97b, #38f9d7); color: #1e3c72; }
        .role-badge.auditor { background: linear-gradient(135deg, #fa709a, #fee140); color: #1e3c72; }

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
            max-width: 500px;
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

        .stat-card.total-users { --card-bg-1: #667eea; --card-bg-2: #764ba2; }
        .stat-card.active-users { --card-bg-1: #43e97b; --card-bg-2: #38f9d7; }
        .stat-card.admin-users { --card-bg-1: #f093fb; --card-bg-2: #f5576c; }
        .stat-card.recent-users { --card-bg-1: #4facfe; --card-bg-2: #00f2fe; }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .user-management-container {
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
        <div class="user-management-container">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-users-cog"></i>
                        User Management
                    </h1>
                    <p class="page-subtitle">Manage system users, roles and permissions</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-success" onclick="showAddUserModal()">
                        <i class="fas fa-user-plus"></i>
                        Add New User
                    </button>
                    <button class="btn btn-primary" onclick="refreshUsers()">
                        <i class="fas fa-sync"></i>
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- User Statistics -->
            <div class="stats-row">
                <div class="stat-card total-users">
                    <div class="stat-value" id="totalUsers">-</div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-card active-users">
                    <div class="stat-value" id="activeUsers">-</div>
                    <div class="stat-label">Active Users</div>
                </div>
                <div class="stat-card admin-users">
                    <div class="stat-value" id="adminUsers">-</div>
                    <div class="stat-label">Admin Users</div>
                </div>
                <div class="stat-card recent-users">
                    <div class="stat-value" id="recentUsers">-</div>
                    <div class="stat-label">This Month</div>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="search-filter-bar">
                <input type="text" id="searchInput" class="search-input" placeholder="🔍 Search users by name, email, or role...">
                <select id="roleFilter" class="filter-select">
                    <option value="">All Roles</option>
                    <option value="Admin">Admin</option>
                    <option value="QuarterMaster">QuarterMaster</option>
                    <option value="Department">Department</option>
                    <option value="CO">CO</option>
                    <option value="Auditor">Auditor</option>
                </select>
                <button class="btn btn-info" onclick="exportUsers()">
                    <i class="fas fa-download"></i>
                    Export
                </button>
            </div>

            <!-- Users Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i>
                        System Users
                    </h3>
                    <div>
                        <span id="userCount">Loading...</span> users found
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table" id="usersTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 2rem;">
                                        <i class="fas fa-spinner fa-spin"></i> Loading users...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add/Edit User Modal -->
    <div class="modal" id="userModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Add New User</h2>
                <button class="close-modal" onclick="closeUserModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="userId">
                    
                    <div class="form-group">
                        <label class="form-label" for="userName">
                            <i class="fas fa-user"></i>
                            Full Name
                        </label>
                        <input type="text" id="userName" class="form-control" placeholder="Enter full name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="userEmail">
                            <i class="fas fa-envelope"></i>
                            Email Address
                        </label>
                        <input type="email" id="userEmail" class="form-control" placeholder="Enter email address" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="userRole">
                            <i class="fas fa-id-badge"></i>
                            Role
                        </label>
                        <select id="userRole" class="form-control" required>
                            <option value="">Select Role</option>
                            <option value="1">Admin</option>
                            <option value="2">QuarterMaster</option>
                            <option value="3">Department</option>
                            <option value="4">CO</option>
                            <option value="5">Auditor</option>
                        </select>
                    </div>

                    <div class="form-group" id="passwordGroup">
                        <label class="form-label" for="userPassword">
                            <i class="fas fa-lock"></i>
                            Password
                        </label>
                        <input type="password" id="userPassword" class="form-control" placeholder="Enter password" required>
                        <small style="color: #666; font-size: 0.8rem;">Minimum 8 characters</small>
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                        <button type="button" class="btn btn-secondary" onclick="closeUserModal()">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-success" id="saveUserBtn">
                            <i class="fas fa-save"></i>
                            Save User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete User Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-exclamation-triangle" style="color: var(--danger);"></i>
                    Confirm Delete User
                </h2>
                <button class="close-modal" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="deleteUserId">
                
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--danger), #e74c3c); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i class="fas fa-user-times" style="color: white; font-size: 2rem;"></i>
                    </div>
                    
                    <h3 style="color: var(--danger); margin-bottom: 1rem;">Delete User Account</h3>
                    
                    <p style="color: #666; margin-bottom: 1.5rem;">
                        Are you sure you want to delete this user? This action cannot be undone.
                    </p>
                    
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; border-left: 4px solid var(--danger); text-align: left;">
                        <strong>User Details:</strong><br>
                        <strong>Name:</strong> <span id="deleteUserName"></span><br>
                        <strong>Email:</strong> <span id="deleteUserEmail"></span>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn" onclick="confirmDeleteUser()">
                        <i class="fas fa-trash"></i>
                        Yes, Delete User
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
    let users = [];
    let filteredUsers = [];

    // Get JWT token from session
    const token = '<?php echo $_SESSION["jwt_token"] ?? ""; ?>';

    // API Headers
    const headers = {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    };

    // Load users on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Page loaded, initializing...');
        loadUsers();
        setupEventListeners();
        // Use event delegation for better performance and reliability
        setupTableEventDelegation();
    });

    // Setup event listeners
    function setupEventListeners() {
        console.log('📋 Setting up event listeners...');
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', filterUsers);
        document.getElementById('roleFilter').addEventListener('change', filterUsers);

        // Form submission
        document.getElementById('userForm').addEventListener('submit', handleUserSubmit);

        // Modal click outside to close
        document.getElementById('userModal').addEventListener('click', function(e) {
            if (e.target === this) closeUserModal();
        });

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeUserModal();
                closeDeleteModal();
            }
        });
    }

    // Setup event delegation for table buttons (RECOMMENDED APPROACH)
    function setupTableEventDelegation() {
        console.log('🎯 Setting up event delegation for table buttons...');
        
        // Add single event listener to the table (event delegation)
        document.getElementById('usersTable').addEventListener('click', handleTableClick);
    }

    // Handle all table button clicks with event delegation
    function handleTableClick(e) {
        // Handle edit button clicks
        if (e.target.closest('.edit-user-btn')) {
            e.preventDefault();
            e.stopPropagation();
            const button = e.target.closest('.edit-user-btn');
            const userIdStr = button.getAttribute('data-user-id');
            const userId = parseInt(userIdStr);
            console.log('✏️ Edit button clicked for user ID:', userId, 'Original:', userIdStr, 'Type:', typeof userId);
            if (userIdStr && !isNaN(userId)) {
                showEditUserModal(userId);
            } else {
                console.error('❌ Invalid user ID:', userIdStr);
                showAlert('❌ Invalid user ID!', 'error');
            }
        }
        
        // Handle delete button clicks
        if (e.target.closest('.delete-user-btn')) {
            e.preventDefault();
            e.stopPropagation();
            const button = e.target.closest('.delete-user-btn');
            const userIdStr = button.getAttribute('data-user-id');
            const userId = parseInt(userIdStr);
            console.log('🗑️ Delete button clicked for user ID:', userId, 'Original:', userIdStr, 'Type:', typeof userId);
            if (userIdStr && !isNaN(userId)) {
                showDeleteUserModal(userId);
            } else {
                console.error('❌ Invalid user ID:', userIdStr);
                showAlert('❌ Invalid user ID!', 'error');
            }
        }
    }

    // Load users from API or use mock data
    async function loadUsers() {
        try {
            showAlert('🔄 Loading users...', 'info');
            
            // FIXED: Correct API endpoint structure
            const apiUrl = `${API_BASE_URL}/adminController.php/api/admin/users`;
            
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

            // Handle response format - your backend returns users array directly
            if (Array.isArray(apiResponse)) {
                users = apiResponse;
                // Ensure all user IDs are numbers for consistency
                users = users.map(user => ({
                    ...user,
                    id: parseInt(user.id) // Convert to number
                }));
            } else if (apiResponse.data && Array.isArray(apiResponse.data)) {
                users = apiResponse.data.map(user => ({
                    ...user,
                    id: parseInt(user.id)
                }));
            } else if (apiResponse.message) {
                throw new Error(apiResponse.message);
            } else {
                throw new Error('Unexpected response format');
            }

            console.log('✅ Loaded users:', users);

            filteredUsers = [...users];
            displayUsers();
            updateUserStats();
            
            showAlert(`✅ Loaded ${users.length} users!`, 'success');
            
        } catch (error) {
            console.error('❌ Error loading users:', error);
            showAlert('❌ Error loading users: ' + error.message, 'error');
            
            // Fallback to mock data for demo
            console.log('🔄 Using mock data as fallback...');
            users = [
                { id: 1, name: 'John Admin', email: 'admin@msict.go.tz', role: 'Admin' },
                { id: 2, name: 'Jane Quartermaster', email: 'quartermaster@msict.go.tz', role: 'QuarterMaster' },
                { id: 3, name: 'Bob Department', email: 'department@msict.go.tz', role: 'Department' },
                { id: 4, name: 'Alice CO', email: 'co@msict.go.tz', role: 'CO' },
                { id: 5, name: 'Charlie Auditor', email: 'auditor@msict.go.tz', role: 'Auditor' }
            ];

            filteredUsers = [...users];
            displayUsers();
            updateUserStats();
            
            showAlert('⚠️ Using demo data - API connection failed', 'warning');
        }
    }

    // Display users in table
    function displayUsers() {
        console.log('📊 Displaying users:', filteredUsers);
        const tbody = document.getElementById('usersTableBody');
        const userCount = document.getElementById('userCount');
        
        if (filteredUsers.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem;">
                        <i class="fas fa-users"></i> No users found
                    </td>
                </tr>
            `;
            userCount.textContent = '0';
            return;
        }

        tbody.innerHTML = filteredUsers.map(user => `
            <tr>
                <td><strong>#${user.id}</strong></td>
                <td>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 35px; height: 35px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.8rem;">
                            ${user.name.split(' ').map(n => n[0]).join('').toUpperCase()}
                        </div>
                        <div>
                            <div style="font-weight: 600;">${user.name}</div>
                        </div>
                    </div>
                </td>
                <td>${user.email}</td>
                <td><span class="role-badge ${user.role.toLowerCase()}">${user.role}</span></td>
                <td><span class="role-badge admin">Active</span></td>
                <td>
                    <button class="btn btn-info btn-sm edit-user-btn" data-user-id="${user.id}" title="Edit User">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm delete-user-btn" data-user-id="${user.id}" title="Delete User">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        userCount.textContent = filteredUsers.length;
        
        // No need to call bindActionButtons() because we're using event delegation
        console.log('✅ Table updated with event delegation handling button clicks');
    }

    // Update user statistics
    function updateUserStats() {
        const totalUsers = users.length;
        const activeUsers = users.filter(u => u.role).length;
        const adminUsers = users.filter(u => u.role && u.role.toLowerCase() === 'admin').length;
        const recentUsers = Math.floor(totalUsers * 0.1);

        document.getElementById('totalUsers').textContent = totalUsers;
        document.getElementById('activeUsers').textContent = activeUsers;
        document.getElementById('adminUsers').textContent = adminUsers;
        document.getElementById('recentUsers').textContent = recentUsers;
    }

    // Filter users
    function filterUsers() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const roleFilter = document.getElementById('roleFilter').value;

        filteredUsers = users.filter(user => {
            const matchesSearch = user.name.toLowerCase().includes(searchTerm) ||
                                user.email.toLowerCase().includes(searchTerm) ||
                                user.role.toLowerCase().includes(searchTerm);
            
            const matchesRole = !roleFilter || user.role === roleFilter;

            return matchesSearch && matchesRole;
        });

        displayUsers();
    }

    // Show Add User Modal
    function showAddUserModal() {
        console.log('➕ Opening Add User Modal');
        document.getElementById('modalTitle').textContent = 'Add New User';
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('userPassword').required = true;
        // Show password field for new users
        document.getElementById('passwordGroup').style.display = 'block';
        document.getElementById('userModal').classList.add('show');
    }

    // Show Edit User Modal
    function showEditUserModal(userId) {
        console.log('✏️ Opening Edit User Modal for ID:', userId, 'Type:', typeof userId);
        console.log('🔍 Looking for user in array:', users.map(u => ({ id: u.id, type: typeof u.id, name: u.name })));
        
        // Convert userId to number to ensure match
        const numericUserId = parseInt(userId);
        const user = users.find(u => parseInt(u.id) === numericUserId);
        
        if (user) {
            console.log('✅ Found user:', user);
            document.getElementById('modalTitle').textContent = 'Edit User';
            document.getElementById('userId').value = user.id;
            document.getElementById('userName').value = user.name;
            document.getElementById('userEmail').value = user.email;
            // Map role name to role_id
            const roleMap = {'Admin': 1, 'QuarterMaster': 2, 'Department': 3, 'CO': 4, 'Auditor': 5};
            document.getElementById('userRole').value = roleMap[user.role] || '';
            document.getElementById('userPassword').value = '';
            document.getElementById('userPassword').required = false;
            // Hide password field for editing
            document.getElementById('passwordGroup').style.display = 'none';
            document.getElementById('userModal').classList.add('show');
        } else {
            console.error('❌ User not found:', userId);
            console.error('🔍 Available users:', users.map(u => ({ id: u.id, type: typeof u.id, name: u.name })));
            showAlert('❌ User not found!', 'error');
        }
    }

    // Show Delete User Modal
    function showDeleteUserModal(userId) {
        console.log('🗑️ Opening Delete User Modal for ID:', userId, 'Type:', typeof userId);
        console.log('🔍 Looking for user in array:', users.map(u => ({ id: u.id, type: typeof u.id, name: u.name })));
        
        // Convert userId to number to ensure match
        const numericUserId = parseInt(userId);
        const user = users.find(u => parseInt(u.id) === numericUserId);
        
        if (user) {
            console.log('✅ Found user:', user);
            document.getElementById('deleteUserName').textContent = user.name;
            document.getElementById('deleteUserEmail').textContent = user.email;
            document.getElementById('deleteUserId').value = user.id;
            document.getElementById('deleteModal').classList.add('show');
        } else {
            console.error('❌ User not found:', userId);
            console.error('🔍 Available users:', users.map(u => ({ id: u.id, type: typeof u.id, name: u.name })));
            showAlert('❌ User not found!', 'error');
        }
    }

    // Close User Modal
    function closeUserModal() {
        console.log('❌ Closing User Modal');
        document.getElementById('userModal').classList.remove('show');
    }

    // Close Delete Modal
    function closeDeleteModal() {
        console.log('❌ Closing Delete Modal');
        document.getElementById('deleteModal').classList.remove('show');
    }

    // Handle form submission
    async function handleUserSubmit(e) {
        e.preventDefault();
        console.log('💾 Submitting user form...');
        
        const submitBtn = document.getElementById('saveUserBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<div class="loading"></div> Saving...';
        submitBtn.disabled = true;

        try {
            const userId = document.getElementById('userId').value;
            const userData = {
                name: document.getElementById('userName').value,
                email: document.getElementById('userEmail').value,
                role_id: parseInt(document.getElementById('userRole').value)
            };

            // Only include password for new users
            if (!userId) {
                userData.password = document.getElementById('userPassword').value;
            }

            console.log('💾 User data to save:', userData);

            // Try actual API call first
            let apiUrl, method;
            
            if (userId) {
                apiUrl = `${API_BASE_URL}/adminController.php/api/admin/users/${userId}`;
                method = 'PUT';
            } else {
                apiUrl = `${API_BASE_URL}/adminController.php/api/admin/users`;
                method = 'POST';
            }

            try {
                const response = await fetch(apiUrl, {
                    method: method,
                    headers: headers,
                    body: JSON.stringify(userData)
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

                showAlert(`✅ User ${userId ? 'updated' : 'created'} successfully!`, 'success');
                closeUserModal();
                loadUsers(); // Reload from API

            } catch (apiError) {
                console.log('⚠️ API call failed, using mock update:', apiError.message);
                
                // Fallback to mock data update
                if (userId) {
                    // Update existing user in mock data
                    const userIndex = users.findIndex(u => u.id === parseInt(userId));
                    if (userIndex !== -1) {
                        const roleNames = {1: 'Admin', 2: 'QuarterMaster', 3: 'Department', 4: 'CO', 5: 'Auditor'};
                        users[userIndex] = {
                            ...users[userIndex],
                            name: userData.name,
                            email: userData.email,
                            role: roleNames[userData.role_id]
                        };
                    }
                } else {
                    // Add new user to mock data
                    const roleNames = {1: 'Admin', 2: 'QuarterMaster', 3: 'Department', 4: 'CO', 5: 'Auditor'};
                    const newUser = {
                        id: Math.max(...users.map(u => u.id)) + 1,
                        name: userData.name,
                        email: userData.email,
                        role: roleNames[userData.role_id]
                    };
                    users.push(newUser);
                }

                showAlert(`✅ User ${userId ? 'updated' : 'created'} successfully! (Demo mode)`, 'success');
                closeUserModal();
                
                // Refresh display
                filteredUsers = [...users];
                displayUsers();
                updateUserStats();
            }

        } catch (error) {
            console.error('❌ Error saving user:', error);
            showAlert('❌ Error saving user: ' + error.message, 'error');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }

    // Confirm delete user
    async function confirmDeleteUser() {
        console.log('🗑️ Confirming user deletion...');
        const deleteBtn = document.getElementById('confirmDeleteBtn');
        const originalText = deleteBtn.innerHTML;
        deleteBtn.innerHTML = '<div class="loading"></div> Deleting...';
        deleteBtn.disabled = true;

        try {
            const userId = parseInt(document.getElementById('deleteUserId').value);
            
            console.log('🗑️ Deleting user ID:', userId);
            
            // Try actual API call first
            try {
                const response = await fetch(`${API_BASE_URL}/adminController.php/api/admin/users/${userId}`, {
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

                showAlert('🗑️ User deleted successfully!', 'success');
                closeDeleteModal();
                loadUsers(); // Reload from API

            } catch (apiError) {
                console.log('⚠️ API call failed, using mock delete:', apiError.message);
                
                // Fallback to mock data deletion
                users = users.filter(u => u.id !== userId);

                showAlert('🗑️ User deleted successfully! (Demo mode)', 'success');
                closeDeleteModal();
                
                // Refresh display
                filteredUsers = [...users];
                displayUsers();
                updateUserStats();
            }
            
        } catch (error) {
            console.error('❌ Error deleting user:', error);
            showAlert('❌ Error deleting user: ' + error.message, 'error');
        } finally {
            deleteBtn.innerHTML = originalText;
            deleteBtn.disabled = false;
        }
    }

    // Refresh users
    function refreshUsers() {
        console.log('🔄 Refreshing users...');
        loadUsers();
    }

    // Export users
    function exportUsers() {
        console.log('📁 Exporting users...');
        const csvContent = [
            ['ID', 'Name', 'Email', 'Role', 'Status'],
            ...filteredUsers.map(user => [
                user.id,
                user.name,
                user.email,
                user.role,
                'Active'
            ])
        ].map(row => row.join(',')).join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `MSICT_Users_${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        showAlert('📁 Users exported successfully!', 'success');
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

    // Make functions globally accessible for debugging and HTML onclick attributes
    window.showEditUserModal = showEditUserModal;
    window.showDeleteUserModal = showDeleteUserModal;
    window.showAddUserModal = showAddUserModal;
    window.closeUserModal = closeUserModal;
    window.closeDeleteModal = closeDeleteModal;
    window.confirmDeleteUser = confirmDeleteUser;
    window.refreshUsers = refreshUsers;
    window.exportUsers = exportUsers;

    // Initialize system
    console.log('👥 MSICT User Management System Initialized');
    console.log('🔗 API Base URL:', API_BASE_URL);
    console.log('🔗 Users API Endpoint:', `${API_BASE_URL}/adminController.php/api/admin/users`);
    console.log('🔑 JWT Token Status:', token ? 'Present' : 'Missing');
    console.log('👤 Current Admin:', '<?php echo $_SESSION['full_name']; ?>');
    
    // Show welcome message after page loads
    setTimeout(() => {
        showAlert('👥 User Management System Ready!', 'success');
    }, 2000);
</script>
</body>
</html>