<?php
// modules/admin/role-management.php
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
    <title>Role Management - MSICT Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* Role Management Styles */
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

        .role-management-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

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

        .stat-card.total-roles { --card-bg-1: #667eea; --card-bg-2: #764ba2; }
        .stat-card.active-roles { --card-bg-1: #43e97b; --card-bg-2: #38f9d7; }
        .stat-card.total-permissions { --card-bg-1: #f093fb; --card-bg-2: #f5576c; }
        .stat-card.role-assignments { --card-bg-1: #4facfe; --card-bg-2: #00f2fe; }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
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
            .role-management-container {
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

            .tabs-header {
                flex-direction: column;
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
        <div class="role-management-container">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-user-shield"></i>
                        Role & Permission Management
                    </h1>
                    <p class="page-subtitle">Manage system roles and permissions</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-primary" onclick="refreshAll()">
                        <i class="fas fa-sync"></i>
                        Refresh All
                    </button>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- Statistics -->
            <div class="stats-row">
                <div class="stat-card total-roles">
                    <div class="stat-value" id="totalRoles">-</div>
                    <div class="stat-label">Total Roles</div>
                </div>
                <div class="stat-card active-roles">
                    <div class="stat-value" id="activeRoles">-</div>
                    <div class="stat-label">Active Roles</div>
                </div>
                <div class="stat-card total-permissions">
                    <div class="stat-value" id="totalPermissions">-</div>
                    <div class="stat-label">Total Permissions</div>
                </div>
                <div class="stat-card role-assignments">
                    <div class="stat-value" id="roleAssignments">-</div>
                    <div class="stat-label">Assignments</div>
                </div>
            </div>

            <!-- Tabs Container -->
            <div class="tabs-container">
                <div class="tabs-header">
                    <button class="tab-button active" onclick="switchTab('roles')">
                        <i class="fas fa-users-cog"></i>
                        Roles Management
                    </button>
                    <button class="tab-button" onclick="switchTab('permissions')">
                        <i class="fas fa-key"></i>
                        Role Permissions
                    </button>
                </div>

                <!-- Roles Tab -->
                <div class="tab-content active" id="rolesTab">
                    <!-- Search and Actions -->
                    <div class="search-filter-bar">
                        <input type="text" id="roleSearchInput" class="search-input" placeholder="🔍 Search roles by name or description...">
                        <button class="btn btn-success" onclick="showAddRoleModal()">
                            <i class="fas fa-plus"></i>
                            Add New Role
                        </button>
                    </div>

                    <!-- Roles Table -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-shield"></i>
                                System Roles
                            </h3>
                            <div>
                                <span id="roleCount">Loading...</span> roles found
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-container">
                                <table class="table" id="rolesTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Role Name</th>
                                            <th>Description</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rolesTableBody">
                                        <tr>
                                            <td colspan="4" style="text-align: center; padding: 2rem;">
                                                <i class="fas fa-spinner fa-spin"></i> Loading roles...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Tab -->
                <div class="tab-content" id="permissionsTab">
                    <!-- Search and Actions -->
                    <div class="search-filter-bar">
                        <input type="text" id="permissionSearchInput" class="search-input" placeholder="🔍 Search role permissions...">
                        <button class="btn btn-success" onclick="showAddPermissionModal()">
                            <i class="fas fa-plus"></i>
                            Assign Permission
                        </button>
                    </div>

                    <!-- Role Permissions Table -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-key"></i>
                                Role Permissions
                            </h3>
                            <div>
                                <span id="permissionCount">Loading...</span> assignments found
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-container">
                                <table class="table" id="permissionsTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Role</th>
                                            <th>Permission</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="permissionsTableBody">
                                        <tr>
                                            <td colspan="4" style="text-align: center; padding: 2rem;">
                                                <i class="fas fa-spinner fa-spin"></i> Loading permissions...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add/Edit Role Modal -->
    <div class="modal" id="roleModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="roleModalTitle">Add New Role</h2>
                <button class="close-modal" onclick="closeRoleModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="roleForm">
                    <input type="hidden" id="roleId">
                    
                    <div class="form-group">
                        <label class="form-label" for="roleName">
                            <i class="fas fa-user-shield"></i>
                            Role Name
                        </label>
                        <input type="text" id="roleName" class="form-control" placeholder="Enter role name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="roleDescription">
                            <i class="fas fa-file-text"></i>
                            Description
                        </label>
                        <textarea id="roleDescription" class="form-control" rows="3" placeholder="Enter role description"></textarea>
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                        <button type="button" class="btn btn-secondary" onclick="closeRoleModal()">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-success" id="saveRoleBtn">
                            <i class="fas fa-save"></i>
                            Save Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Role Permission Modal -->
    <div class="modal" id="permissionModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="permissionModalTitle">Assign Permission</h2>
                <button class="close-modal" onclick="closePermissionModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="permissionForm">
                    <input type="hidden" id="permissionId">
                    
                    <div class="form-group">
                        <label class="form-label" for="selectedRole">
                            <i class="fas fa-user-shield"></i>
                            Select Role
                        </label>
                        <select id="selectedRole" class="form-control" required>
                            <option value="">Select a role</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="selectedPermission">
                            <i class="fas fa-key"></i>
                            Select Permission
                        </label>
                        <select id="selectedPermission" class="form-control" required>
                            <option value="">Select a permission</option>
                            <option value="1">Create</option>
                            <option value="2">Read</option>
                            <option value="3">Update</option>
                            <option value="4">Delete</option>
                            <option value="5">Approve</option>
                            <option value="6">Authorize</option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                        <button type="button" class="btn btn-secondary" onclick="closePermissionModal()">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-success" id="savePermissionBtn">
                            <i class="fas fa-save"></i>
                            Assign Permission
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include Footer Component -->
    <?php include '../../dashboard/components/footer.php'; ?>

    <script>
    // API Configuration
    const API_BASE_URL = 'http://localhost/students-proj/unfedZombie/Controllers';
    let roles = [];
    let rolePermissions = [];
    let filteredRoles = [];
    let filteredPermissions = [];

    // Get JWT token from session
    const token = '<?php echo $_SESSION["jwt_token"] ?? ""; ?>';

    // API Headers
    const headers = {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    };

    // Load data on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadRoles();
        loadRolePermissions();
        setupEventListeners();
        setupTableEventDelegation();
    });

    // Setup event listeners
    function setupEventListeners() {
        // Search functionality
        document.getElementById('roleSearchInput').addEventListener('input', filterRoles);
        document.getElementById('permissionSearchInput').addEventListener('input', filterPermissions);

        // Form submissions
        document.getElementById('roleForm').addEventListener('submit', handleRoleSubmit);
        document.getElementById('permissionForm').addEventListener('submit', handlePermissionSubmit);
    }

    // Setup event delegation for table buttons
    function setupTableEventDelegation() {
        console.log('🎯 Setting up event delegation for role management tables...');
        
        // Event delegation for roles table
        document.getElementById('rolesTable').addEventListener('click', handleRolesTableClick);
        
        // Event delegation for permissions table
        document.getElementById('permissionsTable').addEventListener('click', handlePermissionsTableClick);
    }

    // Handle roles table button clicks
    function handleRolesTableClick(e) {
        // Handle delete role button clicks
        if (e.target.closest('.delete-role-btn')) {
            e.preventDefault();
            e.stopPropagation();
            const button = e.target.closest('.delete-role-btn');
            const roleId = parseInt(button.getAttribute('data-role-id'));
            console.log('🗑️ Delete role button clicked for ID:', roleId);
            if (roleId && !isNaN(roleId)) {
                deleteRole(roleId);
            } else {
                console.error('❌ Invalid role ID:', roleId);
                showAlert('❌ Invalid role ID!', 'error');
            }
        }
    }

    // Handle permissions table button clicks
    function handlePermissionsTableClick(e) {
        // Handle edit permission button clicks
        if (e.target.closest('.edit-permission-btn')) {
            e.preventDefault();
            e.stopPropagation();
            const button = e.target.closest('.edit-permission-btn');
            const permissionId = parseInt(button.getAttribute('data-permission-id'));
            console.log('✏️ Edit permission button clicked for ID:', permissionId);
            if (permissionId && !isNaN(permissionId)) {
                editPermission(permissionId);
            } else {
                console.error('❌ Invalid permission ID:', permissionId);
                showAlert('❌ Invalid permission ID!', 'error');
            }
        }
        
        // Handle delete permission button clicks
        if (e.target.closest('.delete-permission-btn')) {
            e.preventDefault();
            e.stopPropagation();
            const button = e.target.closest('.delete-permission-btn');
            const permissionId = parseInt(button.getAttribute('data-permission-id'));
            console.log('🗑️ Delete permission button clicked for ID:', permissionId);
            if (permissionId && !isNaN(permissionId)) {
                deletePermission(permissionId);
            } else {
                console.error('❌ Invalid permission ID:', permissionId);
                showAlert('❌ Invalid permission ID!', 'error');
            }
        }
    }

    // Tab switching
    function switchTab(tabName) {
        // Remove active class from all tabs and buttons
        document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

        // Add active class to selected tab
        document.querySelector(`[onclick="switchTab('${tabName}')"]`).classList.add('active');
        document.getElementById(tabName + 'Tab').classList.add('active');

        if (tabName === 'roles') {
            loadRoles();
        } else if (tabName === 'permissions') {
            loadRolePermissions();
        }
    }

    // Load roles from API
    async function loadRoles() {
        try {
            showAlert('🔄 Loading roles...', 'info');
            
            const response = await fetch(`${API_BASE_URL}/admin/api/admin/roles`, {
                method: 'GET',
                headers: headers
            });

            if (!response.ok) {
                throw new Error(`API Error: ${response.status}`);
            }

            const responseText = await response.text();
            roles = JSON.parse(responseText);
            
            // Ensure all role IDs are numbers for consistency
            roles = roles.map(role => ({
                ...role,
                id: parseInt(role.id)
            }));
            
            filteredRoles = [...roles];
            
            displayRoles();
            updateStats();
            populateRoleSelect();
            
            showAlert(`✅ Loaded ${roles.length} roles!`, 'success');
            
        } catch (error) {
            console.error('Error loading roles:', error);
            showAlert('❌ Error loading roles: ' + error.message, 'error');
            
            document.getElementById('rolesTableBody').innerHTML = `
                <tr>
                    <td colspan="4" style="text-align: center; padding: 2rem; color: #721c24;">
                        <i class="fas fa-exclamation-triangle"></i>
                        Failed to load roles
                    </td>
                </tr>
            `;
        }
    }

    // Load role permissions from API
    async function loadRolePermissions() {
        try {
            showAlert('🔄 Loading permissions...', 'info');
            
            const response = await fetch(`${API_BASE_URL}/admin/api/admin/role-permissions`, {
                method: 'GET',
                headers: headers
            });

            if (!response.ok) {
                throw new Error(`API Error: ${response.status}`);
            }

            const responseText = await response.text();
            rolePermissions = JSON.parse(responseText);
            
            // Ensure all permission IDs are numbers for consistency
            rolePermissions = rolePermissions.map(permission => ({
                ...permission,
                id: parseInt(permission.id)
            }));
            
            filteredPermissions = [...rolePermissions];
            
            displayPermissions();
            updateStats();
            
            showAlert(`✅ Loaded ${rolePermissions.length} permissions!`, 'success');
            
        } catch (error) {
            console.error('Error loading permissions:', error);
            showAlert('❌ Error loading permissions: ' + error.message, 'error');
            
            document.getElementById('permissionsTableBody').innerHTML = `
                <tr>
                    <td colspan="4" style="text-align: center; padding: 2rem; color: #721c24;">
                        <i class="fas fa-exclamation-triangle"></i>
                        Failed to load permissions
                    </td>
                </tr>
            `;
        }
    }

    // Display roles in table
    function displayRoles() {
        const tbody = document.getElementById('rolesTableBody');
        const roleCount = document.getElementById('roleCount');

        if (filteredRoles.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" style="text-align: center; padding: 2rem;">
                        <i class="fas fa-user-shield"></i> No roles found
                    </td>
                </tr>
            `;
            roleCount.textContent = '0';
            return;
        }

        tbody.innerHTML = filteredRoles.map(role => `
            <tr>
                <td><strong>#${role.id}</strong></td>
                <td>
                    <span class="role-badge ${role.name.toLowerCase()}">${role.name}</span>
                </td>
                <td>${role.description || 'No description'}</td>
                <td>
                    <button class="btn btn-danger btn-sm delete-role-btn" data-role-id="${role.id}" title="Delete Role">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        roleCount.textContent = filteredRoles.length;
        console.log('✅ Roles table updated with event delegation handling button clicks');
    }

    // Display permissions in table
    function displayPermissions() {
        const tbody = document.getElementById('permissionsTableBody');
        const permissionCount = document.getElementById('permissionCount');

        if (filteredPermissions.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" style="text-align: center; padding: 2rem;">
                        <i class="fas fa-key"></i> No permissions found
                    </td>
                </tr>
            `;
            permissionCount.textContent = '0';
            return;
        }

        tbody.innerHTML = filteredPermissions.map(permission => `
            <tr>
                <td><strong>#${permission.id}</strong></td>
                <td>
                    <span class="role-badge ${permission.role.toLowerCase()}">${permission.role}</span>
                </td>
                <td>
                    <span style="padding: 0.25rem 0.5rem; background: #e9ecef; border-radius: 5px; font-size: 0.8rem;">
                        <i class="fas fa-key"></i> ${permission.permission}
                    </span>
                </td>
                <td>
                    <button class="btn btn-warning btn-sm edit-permission-btn" data-permission-id="${permission.id}" title="Edit Permission">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm delete-permission-btn" data-permission-id="${permission.id}" title="Remove Permission">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        permissionCount.textContent = filteredPermissions.length;
        console.log('✅ Permissions table updated with event delegation handling button clicks');
    }

    // Update statistics
    function updateStats() {
        document.getElementById('totalRoles').textContent = roles.length;
        document.getElementById('activeRoles').textContent = roles.length;
        document.getElementById('totalPermissions').textContent = rolePermissions.length;
        document.getElementById('roleAssignments').textContent = rolePermissions.length;
    }

    // Filter roles
    function filterRoles() {
        const searchTerm = document.getElementById('roleSearchInput').value.toLowerCase();
        filteredRoles = roles.filter(role => 
            role.name.toLowerCase().includes(searchTerm) ||
            (role.description && role.description.toLowerCase().includes(searchTerm))
        );
        displayRoles();
    }

    // Filter permissions
    function filterPermissions() {
        const searchTerm = document.getElementById('permissionSearchInput').value.toLowerCase();
        filteredPermissions = rolePermissions.filter(permission => 
            permission.role.toLowerCase().includes(searchTerm) ||
            permission.permission.toLowerCase().includes(searchTerm)
        );
        displayPermissions();
    }

    // Show Add Role Modal
    function showAddRoleModal() {
        document.getElementById('roleModalTitle').textContent = 'Add New Role';
        document.getElementById('roleForm').reset();
        document.getElementById('roleId').value = '';
        document.getElementById('roleModal').classList.add('show');
    }

    // Show Add Permission Modal
    function showAddPermissionModal() {
        document.getElementById('permissionModalTitle').textContent = 'Assign Permission';
        document.getElementById('permissionForm').reset();
        document.getElementById('permissionId').value = '';
        document.getElementById('permissionModal').classList.add('show');
    }

    // Close modals
    function closeRoleModal() {
        document.getElementById('roleModal').classList.remove('show');
    }

    function closePermissionModal() {
        document.getElementById('permissionModal').classList.remove('show');
    }

    // Handle role form submission
    async function handleRoleSubmit(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('saveRoleBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<div class="loading"></div> Saving...';
        submitBtn.disabled = true;

        try {
            const roleData = {
                name: document.getElementById('roleName').value,
                description: document.getElementById('roleDescription').value
            };

            const response = await fetch(`${API_BASE_URL}/admin/api/admin/roles`, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(roleData)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || result.error || `HTTP ${response.status}`);
            }

            showAlert('✅ Role created successfully!', 'success');
            closeRoleModal();
            loadRoles();

        } catch (error) {
            console.error('Error saving role:', error);
            showAlert('❌ Error saving role: ' + error.message, 'error');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }

    // Handle permission form submission
    async function handlePermissionSubmit(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('savePermissionBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<div class="loading"></div> Saving...';
        submitBtn.disabled = true;

        try {
            const permissionId = document.getElementById('permissionId').value;
            const permissionData = {
                role_id: parseInt(document.getElementById('selectedRole').value),
                permission_id: parseInt(document.getElementById('selectedPermission').value)
            };

            let apiUrl = `${API_BASE_URL}/admin/api/admin/role-permissions`;
            let method = 'POST';

            if (permissionId) {
                apiUrl += `/${permissionId}`;
                method = 'PUT';
            }

            const response = await fetch(apiUrl, {
                method: method,
                headers: headers,
                body: JSON.stringify(permissionData)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || result.error || `HTTP ${response.status}`);
            }

            showAlert(`✅ Permission ${permissionId ? 'updated' : 'assigned'} successfully!`, 'success');
            closePermissionModal();
            loadRolePermissions();

        } catch (error) {
            console.error('Error saving permission:', error);
            showAlert('❌ Error saving permission: ' + error.message, 'error');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }

    // Delete role
    async function deleteRole(roleId) {
        console.log('🗑️ Deleting role with ID:', roleId, 'Type:', typeof roleId);
        console.log('🔍 Available roles:', roles.map(r => ({ id: r.id, type: typeof r.id, name: r.name })));
        
        // Convert roleId to number to ensure match
        const numericRoleId = parseInt(roleId);
        const role = roles.find(r => parseInt(r.id) === numericRoleId);
        
        if (role && confirm(`⚠️ Delete role "${role.name}"?\n\nThis action cannot be undone.`)) {
            try {
                const response = await fetch(`${API_BASE_URL}/admin/api/admin/roles/${roleId}`, {
                    method: 'DELETE',
                    headers: headers
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || result.error || `HTTP ${response.status}`);
                }

                showAlert('🗑️ Role deleted successfully!', 'success');
                loadRoles();

            } catch (error) {
                console.error('Error deleting role:', error);
                showAlert('❌ Error deleting role: ' + error.message, 'error');
            }
        } else if (!role) {
            console.error('❌ Role not found:', roleId);
            showAlert('❌ Role not found!', 'error');
        }
    }

    // Edit permission
    function editPermission(permissionId) {
        console.log('✏️ Editing permission with ID:', permissionId, 'Type:', typeof permissionId);
        console.log('🔍 Available permissions:', rolePermissions.map(p => ({ id: p.id, type: typeof p.id, role: p.role, permission: p.permission })));
        
        // Convert permissionId to number to ensure match
        const numericPermissionId = parseInt(permissionId);
        const permission = rolePermissions.find(p => parseInt(p.id) === numericPermissionId);
        
        if (permission) {
            console.log('✅ Found permission:', permission);
            document.getElementById('permissionModalTitle').textContent = 'Edit Permission';
            document.getElementById('permissionId').value = permission.id;
            
            // Find role by name and set value
            const roleOption = Array.from(document.getElementById('selectedRole').options)
                .find(option => option.text === permission.role);
            if (roleOption) {
                document.getElementById('selectedRole').value = roleOption.value;
            }
            
            // Set permission based on name
            const permissionMap = {
                'Create': 1, 'Read': 2, 'Update': 3, 'Delete': 4, 'Approve': 5, 'Authorize': 6
            };
            document.getElementById('selectedPermission').value = permissionMap[permission.permission] || '';
            
            document.getElementById('permissionModal').classList.add('show');
        } else {
            console.error('❌ Permission not found:', permissionId);
            console.error('🔍 Available permissions:', rolePermissions.map(p => ({ id: p.id, type: typeof p.id, role: p.role })));
            showAlert('❌ Permission not found!', 'error');
        }
    }

    // Delete permission
    async function deletePermission(permissionId) {
        console.log('🗑️ Deleting permission with ID:', permissionId, 'Type:', typeof permissionId);
        console.log('🔍 Available permissions:', rolePermissions.map(p => ({ id: p.id, type: typeof p.id, role: p.role, permission: p.permission })));
        
        // Convert permissionId to number to ensure match
        const numericPermissionId = parseInt(permissionId);
        const permission = rolePermissions.find(p => parseInt(p.id) === numericPermissionId);
        
        if (permission && confirm(`⚠️ Remove "${permission.permission}" permission from "${permission.role}" role?\n\nThis action cannot be undone.`)) {
            try {
                const response = await fetch(`${API_BASE_URL}/admin/api/admin/role-permissions/${permissionId}`, {
                    method: 'DELETE',
                    headers: headers
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || result.error || `HTTP ${response.status}`);
                }

                showAlert('🗑️ Permission removed successfully!', 'success');
                loadRolePermissions();

            } catch (error) {
                console.error('Error deleting permission:', error);
                showAlert('❌ Error deleting permission: ' + error.message, 'error');
            }
        } else if (!permission) {
            console.error('❌ Permission not found:', permissionId);
            showAlert('❌ Permission not found!', 'error');
        }
    }

    // Populate role select dropdown
    function populateRoleSelect() {
        const select = document.getElementById('selectedRole');
        select.innerHTML = '<option value="">Select a role</option>';
        
        roles.forEach(role => {
            const option = document.createElement('option');
            option.value = role.id;
            option.textContent = role.name;
            select.appendChild(option);
        });
    }

    // Refresh all data
    function refreshAll() {
        loadRoles();
        loadRolePermissions();
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

    // Make functions globally accessible for HTML onclick attributes
    window.switchTab = switchTab;
    window.showAddRoleModal = showAddRoleModal;
    window.showAddPermissionModal = showAddPermissionModal;
    window.closeRoleModal = closeRoleModal;
    window.closePermissionModal = closePermissionModal;
    window.refreshAll = refreshAll;

    // Modal click outside to close
    document.getElementById('roleModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRoleModal();
        }
    });

    document.getElementById('permissionModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePermissionModal();
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRoleModal();
            closePermissionModal();
        }
    });

    // Initialize
    console.log('👥 MSICT Role Management System Initialized');
    console.log('🔗 API Base URL:', API_BASE_URL);
    console.log('🔑 JWT Token Status:', token ? 'Present' : 'Missing');
</script>
</body>
</html>