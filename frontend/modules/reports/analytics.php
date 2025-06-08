<?php
// modules/reports/analytics.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit();
}

// Check if user has permission to view analytics
$allowed_roles = ['Admin', 'QuarterMaster', 'CO', 'Auditor'];
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
    <title>System Analytics - MSICT Ordering System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        /* Analytics Dashboard Styles */
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
        .analytics-container {
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

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--card-bg-1), var(--card-bg-2));
            border-radius: 15px;
            padding: 1.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }

        .stat-card.users { --card-bg-1: #667eea; --card-bg-2: #764ba2; }
        .stat-card.requests { --card-bg-1: #f093fb; --card-bg-2: #f5576c; }
        .stat-card.items { --card-bg-1: #43e97b; --card-bg-2: #38f9d7; }
        .stat-card.efficiency { --card-bg-1: #4facfe; --card-bg-2: #00f2fe; }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .stat-icon {
            font-size: 2rem;
            opacity: 0.8;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
            position: relative;
            z-index: 1;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .stat-change {
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin-top: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .stat-change.positive {
            color: #d4edda;
        }

        .stat-change.negative {
            color: #f8d7da;
        }

        /* Chart Containers */
        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        .chart-container canvas {
            border-radius: 10px;
        }

        /* Filter Bar */
        .filter-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            align-items: center;
            background: var(--white);
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-select {
            padding: 0.5rem 1rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 0.9rem;
            background: white;
            cursor: pointer;
            min-width: 150px;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(45, 80, 22, 0.1);
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .data-table th,
        .data-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
        }

        .data-table th {
            background: var(--light-gray);
            font-weight: 600;
            color: var(--primary-color);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table tbody tr:hover {
            background: #f8f9fa;
        }

        /* Progress Bars */
        .progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .progress-fill {
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 4px;
            transition: width 0.3s ease;
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

        /* Responsive */
        @media (max-width: 768px) {
            .analytics-container {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .page-actions {
                justify-content: center;
            }

            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .chart-grid {
                grid-template-columns: 1fr;
            }

            .chart-container {
                height: 250px;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        <div class="analytics-container">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-chart-bar"></i>
                        System Analytics
                    </h1>
                    <p class="page-subtitle">Comprehensive insights and performance metrics</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-success" onclick="refreshAnalytics()">
                        <i class="fas fa-sync"></i>
                        Refresh Data
                    </button>
                    <button class="btn btn-primary" onclick="exportAnalytics()">
                        <i class="fas fa-download"></i>
                        Export Report
                    </button>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="filter-group">
                    <label class="filter-label">Time Period</label>
                    <select id="periodFilter" class="filter-select" onchange="updateAnalytics()">
                        <option value="7">Last 7 Days</option>
                        <option value="30" selected>Last 30 Days</option>
                        <option value="90">Last 3 Months</option>
                        <option value="365">Last Year</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Data Type</label>
                    <select id="dataTypeFilter" class="filter-select" onchange="updateAnalytics()">
                        <option value="all" selected>All Data</option>
                        <option value="requests">Requests Only</option>
                        <option value="inventory">Inventory Only</option>
                        <option value="users">Users Only</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Department</label>
                    <select id="departmentFilter" class="filter-select" onchange="updateAnalytics()">
                        <option value="all" selected>All Departments</option>
                        <option value="admin">Administration</option>
                        <option value="quartermaster">Quarter Master</option>
                        <option value="operations">Operations</option>
                        <option value="intelligence">Intelligence</option>
                    </select>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="stats-grid">
                <div class="stat-card users">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="stat-value" id="totalUsers">-</div>
                    <div class="stat-label">Active Users</div>
                    <div class="stat-change positive" id="usersChange">
                        <i class="fas fa-arrow-up"></i>
                        <span>+12%</span> from last month
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 85%"></div>
                    </div>
                </div>

                <div class="stat-card requests">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                    <div class="stat-value" id="totalRequests">-</div>
                    <div class="stat-label">Total Requests</div>
                    <div class="stat-change positive" id="requestsChange">
                        <i class="fas fa-arrow-up"></i>
                        <span>+8%</span> from last month
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 92%"></div>
                    </div>
                </div>

                <div class="stat-card items">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>
                    <div class="stat-value" id="totalItems">-</div>
                    <div class="stat-label">Inventory Items</div>
                    <div class="stat-change positive" id="itemsChange">
                        <i class="fas fa-arrow-up"></i>
                        <span>+5%</span> from last month
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 78%"></div>
                    </div>
                </div>

                <div class="stat-card efficiency">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                    </div>
                    <div class="stat-value" id="systemEfficiency">-</div>
                    <div class="stat-label">System Efficiency</div>
                    <div class="stat-change positive" id="efficiencyChange">
                        <i class="fas fa-arrow-up"></i>
                        <span>+3%</span> from last month
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 94%"></div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="chart-grid">
                <!-- Request Status Distribution -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie"></i>
                            Request Status Distribution
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="requestStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Monthly Request Trends -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line"></i>
                            Monthly Request Trends
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="requestTrendsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="chart-grid">
                <!-- Department Activity -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar"></i>
                            Department Activity
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="departmentChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Inventory Status -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-warehouse"></i>
                            Inventory Status Overview
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="inventoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Tables -->
            <div class="chart-grid">
                <!-- Top Requested Items -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-trophy"></i>
                            Top Requested Items
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="data-table" id="topItemsTable">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Item Name</th>
                                    <th>Requests</th>
                                    <th>Total Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 2rem;">
                                        <i class="fas fa-spinner fa-spin"></i> Loading data...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- System Performance Metrics -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-speedometer"></i>
                            Performance Metrics
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="data-table" id="performanceTable">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th>Current</th>
                                    <th>Target</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 2rem;">
                                        <i class="fas fa-spinner fa-spin"></i> Loading metrics...
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
        const API_BASE_URL = 'http://localhost/students-proj/unfedZombie/Controllers/admin';
        let analyticsData = {
            users: [],
            requests: [],
            items: [],
            categories: []
        };

        // Chart instances
        let charts = {};

        // Get JWT token from session
        const token = '<?php echo $_SESSION["jwt_token"] ?? ""; ?>';

        // API Headers
        const headers = {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        };

        // User role for conditional features
        const userRole = '<?php echo $_SESSION["user_role"] ?? "Admin"; ?>';

        // Load analytics on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Analytics page loaded, initializing...');
            loadAnalyticsData();
        });

        // Load all analytics data
        async function loadAnalyticsData() {
            try {
                showAlert('🔄 Loading analytics data...', 'info');
                
                // Load data from multiple endpoints in parallel
                const [usersResponse, requestsResponse, itemsResponse] = await Promise.allSettled([
                    loadUsers(),
                    loadRequests(),
                    loadItems()
                ]);

                // Process results
                if (usersResponse.status === 'fulfilled') {
                    analyticsData.users = usersResponse.value;
                }

                if (requestsResponse.status === 'fulfilled') {
                    analyticsData.requests = requestsResponse.value;
                }

                if (itemsResponse.status === 'fulfilled') {
                    analyticsData.items = itemsResponse.value;
                }

                // Generate analytics
                updateKeyMetrics();
                createCharts();
                updateDataTables();
                
                showAlert('✅ Analytics data loaded successfully!', 'success');
                
            } catch (error) {
                console.error('Error loading analytics:', error);
                showAlert('❌ Error loading analytics: ' + error.message, 'error');
                
                // Use mock data for demo
                generateMockData();
                updateKeyMetrics();
                createCharts();
                updateDataTables();
                
                showAlert('⚠️ Using demo data - API connection failed', 'warning');
            }
        }

        // Load users from API
        async function loadUsers() {
            const response = await fetch(`${API_BASE_URL}/api/admin/users`, {
                method: 'GET',
                headers: headers
            });

            if (!response.ok) {
                throw new Error(`Users API Error: ${response.status}`);
            }

            const users = await response.json();
            return Array.isArray(users) ? users : [];
        }

        // Load requests from API
        async function loadRequests() {
            const response = await fetch(`${API_BASE_URL}/api/admin/requests`, {
                method: 'GET',
                headers: headers
            });

            if (!response.ok) {
                throw new Error(`Requests API Error: ${response.status}`);
            }

            const requests = await response.json();
            return Array.isArray(requests) ? requests : [];
        }

        // Load items from API
        async function loadItems() {
            const response = await fetch(`${API_BASE_URL}/api/admin/getitems`, {
                method: 'GET',
                headers: headers
            });

            if (!response.ok) {
                throw new Error(`Items API Error: ${response.status}`);
            }

            const items = await response.json();
            return Array.isArray(items) ? items : [];
        }

        // Generate mock data for demo
        function generateMockData() {
            console.log('📊 Generating mock analytics data...');
            
            // Mock users
            analyticsData.users = Array.from({length: 45}, (_, i) => ({
                id: i + 1,
                name: `User ${i + 1}`,
                role: ['Admin', 'QuarterMaster', 'Department', 'CO', 'Auditor'][i % 5],
                created_at: new Date(Date.now() - Math.random() * 365 * 24 * 60 * 60 * 1000).toISOString()
            }));

            // Mock requests with realistic patterns
            analyticsData.requests = Array.from({length: 120}, (_, i) => {
                const statuses = [
                    { approved: 0, authorized: 0 }, // Pending
                    { approved: 1, authorized: 0 }, // Approved
                    { approved: 1, authorized: 1 }, // Authorized
                ];
                const status = statuses[Math.floor(Math.random() * statuses.length)];
                
                return {
                    id: i + 1,
                    user_id: Math.floor(Math.random() * 45) + 1,
                    item_id: Math.floor(Math.random() * 20) + 1,
                    quantity: Math.floor(Math.random() * 50) + 1,
                    ...status,
                    created_at: new Date(Date.now() - Math.random() * 90 * 24 * 60 * 60 * 1000).toISOString()
                };
            });

            // Mock items
            const itemNames = ['Office Pens', 'Laptops', 'Chairs', 'Desks', 'Printers', 'Paper', 'Staplers', 'Folders', 'Monitors', 'Keyboards'];
            analyticsData.items = Array.from({length: 25}, (_, i) => ({
                id: i + 1,
                name: itemNames[i % itemNames.length] + ` ${Math.floor(i/itemNames.length) + 1}`,
                category_id: ['Office Supplies', 'Electronics', 'Furniture'][i % 3],
                current_stock: Math.floor(Math.random() * 100),
                reorder_level: Math.floor(Math.random() * 20) + 5
            }));
        }

        // Update key metrics
        function updateKeyMetrics() {
            const totalUsers = analyticsData.users.length;
            const totalRequests = analyticsData.requests.length;
            const totalItems = analyticsData.items.length;
            const efficiency = calculateSystemEfficiency();

            document.getElementById('totalUsers').textContent = totalUsers;
            document.getElementById('totalRequests').textContent = totalRequests;
            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('systemEfficiency').textContent = efficiency + '%';

            // Update progress bars based on targets
            updateProgressBar('users', totalUsers, 50);
            updateProgressBar('requests', totalRequests, 150);
            updateProgressBar('items', totalItems, 30);
        }

        // Calculate system efficiency
        function calculateSystemEfficiency() {
            if (analyticsData.requests.length === 0) return 0;
            
            const authorizedRequests = analyticsData.requests.filter(r => r.authorized).length;
            const totalRequests = analyticsData.requests.length;
            
            return Math.round((authorizedRequests / totalRequests) * 100);
        }

        // Update progress bars
        function updateProgressBar(type, current, target) {
            const percentage = Math.min((current / target) * 100, 100);
            const progressFill = document.querySelector(`.stat-card.${type} .progress-fill`);
            if (progressFill) {
                progressFill.style.width = percentage + '%';
            }
        }

        // Create all charts
        function createCharts() {
            createRequestStatusChart();
            createRequestTrendsChart();
            createDepartmentChart();
            createInventoryChart();
        }

        // Create request status pie chart
        function createRequestStatusChart() {
            const ctx = document.getElementById('requestStatusChart').getContext('2d');
            
            const pending = analyticsData.requests.filter(r => !r.approved && !r.authorized).length;
            const approved = analyticsData.requests.filter(r => r.approved && !r.authorized).length;
            const authorized = analyticsData.requests.filter(r => r.authorized).length;

            if (charts.requestStatus) {
                charts.requestStatus.destroy();
            }

            charts.requestStatus = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Approved', 'Authorized'],
                    datasets: [{
                        data: [pending, approved, authorized],
                        backgroundColor: [
                            '#ffc107',
                            '#17a2b8',
                            '#28a745'
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }

        // Create request trends line chart
        function createRequestTrendsChart() {
            const ctx = document.getElementById('requestTrendsChart').getContext('2d');
            
            // Group requests by month
            const monthlyData = getMonthlyRequestData();

            if (charts.requestTrends) {
                charts.requestTrends.destroy();
            }

            charts.requestTrends = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: monthlyData.labels,
                    datasets: [{
                        label: 'Total Requests',
                        data: monthlyData.totals,
                        borderColor: '#2D5016',
                        backgroundColor: 'rgba(45, 80, 22, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Authorized',
                        data: monthlyData.authorized,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        // Create department activity bar chart
        function createDepartmentChart() {
            const ctx = document.getElementById('departmentChart').getContext('2d');
            
            const departmentData = getDepartmentActivityData();

            if (charts.department) {
                charts.department.destroy();
            }

            charts.department = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: departmentData.labels,
                    datasets: [{
                        label: 'Active Users',
                        data: departmentData.users,
                        backgroundColor: 'rgba(45, 80, 22, 0.8)',
                        borderColor: '#2D5016',
                        borderWidth: 1
                    }, {
                        label: 'Requests Made',
                        data: departmentData.requests,
                        backgroundColor: 'rgba(23, 162, 184, 0.8)',
                        borderColor: '#17a2b8',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Create inventory status chart
        function createInventoryChart() {
            const ctx = document.getElementById('inventoryChart').getContext('2d');
            
            const inventoryData = getInventoryStatusData();

            if (charts.inventory) {
                charts.inventory.destroy();
            }

            charts.inventory = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['High Stock', 'Medium Stock', 'Low Stock', 'Out of Stock'],
                    datasets: [{
                        label: 'Number of Items',
                        data: [
                            inventoryData.high,
                            inventoryData.medium,
                            inventoryData.low,
                            inventoryData.out
                        ],
                        backgroundColor: [
                            '#28a745',
                            '#ffc107',
                            '#fd7e14',
                            '#dc3545'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        // Get monthly request data
        function getMonthlyRequestData() {
            const months = [];
            const totals = [];
            const authorized = [];
            
            // Get last 6 months
            for (let i = 5; i >= 0; i--) {
                const date = new Date();
                date.setMonth(date.getMonth() - i);
                const monthKey = date.toISOString().substring(0, 7); // YYYY-MM
                
                months.push(date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' }));
                
                const monthRequests = analyticsData.requests.filter(r => 
                    r.created_at && r.created_at.startsWith(monthKey)
                );
                
                totals.push(monthRequests.length);
                authorized.push(monthRequests.filter(r => r.authorized).length);
            }
            
            return {
                labels: months,
                totals: totals,
                authorized: authorized
            };
        }

        // Get department activity data
        function getDepartmentActivityData() {
            const departments = ['Admin', 'QuarterMaster', 'Department', 'CO', 'Auditor'];
            const userCounts = [];
            const requestCounts = [];
            
            departments.forEach(dept => {
                const deptUsers = analyticsData.users.filter(u => u.role === dept);
                const deptRequests = analyticsData.requests.filter(r => {
                    const user = analyticsData.users.find(u => u.id === r.user_id);
                    return user && user.role === dept;
                });
                
                userCounts.push(deptUsers.length);
                requestCounts.push(deptRequests.length);
            });
            
            return {
                labels: departments,
                users: userCounts,
                requests: requestCounts
            };
        }

        // Get inventory status data
        function getInventoryStatusData() {
            let high = 0, medium = 0, low = 0, out = 0;
            
            analyticsData.items.forEach(item => {
                const stock = item.current_stock || 0;
                const reorderLevel = item.reorder_level || 0;
                
                if (stock === 0) {
                    out++;
                } else if (stock <= reorderLevel) {
                    low++;
                } else if (stock <= reorderLevel * 2) {
                    medium++;
                } else {
                    high++;
                }
            });
            
            return { high, medium, low, out };
        }

        // Update data tables
        function updateDataTables() {
            updateTopItemsTable();
            updatePerformanceTable();
        }

        // Update top requested items table
        function updateTopItemsTable() {
            const itemRequestCounts = {};
            
            // Count requests per item
            analyticsData.requests.forEach(request => {
                const itemId = request.item_id;
                if (!itemRequestCounts[itemId]) {
                    itemRequestCounts[itemId] = {
                        count: 0,
                        totalQuantity: 0,
                        itemName: `Item ${itemId}`
                    };
                }
                itemRequestCounts[itemId].count++;
                itemRequestCounts[itemId].totalQuantity += request.quantity || 0;
                
                // Get actual item name if available
                const item = analyticsData.items.find(i => i.id === itemId);
                if (item) {
                    itemRequestCounts[itemId].itemName = item.name;
                }
            });
            
            // Sort by request count
            const sortedItems = Object.values(itemRequestCounts)
                .sort((a, b) => b.count - a.count)
                .slice(0, 5);
            
            const tbody = document.querySelector('#topItemsTable tbody');
            tbody.innerHTML = sortedItems.map((item, index) => `
                <tr>
                    <td><strong>#${index + 1}</strong></td>
                    <td>${item.itemName}</td>
                    <td>${item.count}</td>
                    <td>${item.totalQuantity}</td>
                </tr>
            `).join('');
        }

        // Update performance metrics table
        function updatePerformanceTable() {
            const metrics = [
                {
                    name: 'Request Approval Rate',
                    current: calculateApprovalRate(),
                    target: 90,
                    unit: '%'
                },
                {
                    name: 'Average Processing Time',
                    current: calculateAverageProcessingTime(),
                    target: 24,
                    unit: 'hours'
                },
                {
                    name: 'System Uptime',
                    current: 99.8,
                    target: 99.5,
                    unit: '%'
                },
                {
                    name: 'User Satisfaction',
                    current: 94,
                    target: 95,
                    unit: '%'
                }
            ];
            
            const tbody = document.querySelector('#performanceTable tbody');
            tbody.innerHTML = metrics.map(metric => {
                const status = metric.current >= metric.target ? 'success' : 'warning';
                const statusIcon = metric.current >= metric.target ? 'fa-check-circle' : 'fa-exclamation-triangle';
                const statusColor = metric.current >= metric.target ? '#28a745' : '#ffc107';
                
                return `
                    <tr>
                        <td><strong>${metric.name}</strong></td>
                        <td>${metric.current}${metric.unit}</td>
                        <td>${metric.target}${metric.unit}</td>
                        <td>
                            <i class="fas ${statusIcon}" style="color: ${statusColor};"></i>
                            <span style="color: ${statusColor};">${status.toUpperCase()}</span>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Calculate approval rate
        function calculateApprovalRate() {
            if (analyticsData.requests.length === 0) return 0;
            const approvedRequests = analyticsData.requests.filter(r => r.approved || r.authorized).length;
            return Math.round((approvedRequests / analyticsData.requests.length) * 100);
        }

        // Calculate average processing time (mock calculation)
        function calculateAverageProcessingTime() {
            // This would require approved_at timestamps in real data
            // For demo, return a reasonable value
            return Math.floor(Math.random() * 48) + 12;
        }

        // Update analytics based on filters
        function updateAnalytics() {
            const period = document.getElementById('periodFilter').value;
            const dataType = document.getElementById('dataTypeFilter').value;
            const department = document.getElementById('departmentFilter').value;
            
            console.log(`🔄 Updating analytics: ${period} days, ${dataType}, ${department}`);
            
            // Filter data based on selections
            let filteredData = filterAnalyticsData(period, dataType, department);
            
            // Update with filtered data
            const originalData = { ...analyticsData };
            analyticsData = filteredData;
            
            updateKeyMetrics();
            createCharts();
            updateDataTables();
            
            // Restore original data
            analyticsData = originalData;
            
            showAlert(`📊 Analytics updated for ${period} days`, 'info');
        }

        // Filter analytics data
        function filterAnalyticsData(period, dataType, department) {
            const cutoffDate = new Date();
            cutoffDate.setDate(cutoffDate.getDate() - parseInt(period));
            
            let filtered = {
                users: [...analyticsData.users],
                requests: [...analyticsData.requests],
                items: [...analyticsData.items]
            };
            
            // Filter by time period
            filtered.requests = filtered.requests.filter(r => 
                !r.created_at || new Date(r.created_at) >= cutoffDate
            );
            
            // Filter by department
            if (department !== 'all') {
                const departmentRoleMap = {
                    'admin': 'Admin',
                    'quartermaster': 'QuarterMaster',
                    'operations': 'Department',
                    'intelligence': 'CO'
                };
                
                const targetRole = departmentRoleMap[department];
                if (targetRole) {
                    filtered.users = filtered.users.filter(u => u.role === targetRole);
                    const userIds = filtered.users.map(u => u.id);
                    filtered.requests = filtered.requests.filter(r => userIds.includes(r.user_id));
                }
            }
            
            // Filter by data type
            if (dataType === 'requests') {
                filtered.users = filtered.users.filter(u => 
                    filtered.requests.some(r => r.user_id === u.id)
                );
            }
            
            return filtered;
        }

        // Refresh analytics
        function refreshAnalytics() {
            console.log('🔄 Refreshing analytics data...');
            loadAnalyticsData();
        }

        // Export analytics report
        function exportAnalytics() {
            console.log('📁 Exporting analytics report...');
            
            const reportData = {
                generated: new Date().toISOString(),
                period: document.getElementById('periodFilter').value + ' days',
                metrics: {
                    totalUsers: analyticsData.users.length,
                    totalRequests: analyticsData.requests.length,
                    totalItems: analyticsData.items.length,
                    systemEfficiency: calculateSystemEfficiency()
                },
                requestStatus: {
                    pending: analyticsData.requests.filter(r => !r.approved && !r.authorized).length,
                    approved: analyticsData.requests.filter(r => r.approved && !r.authorized).length,
                    authorized: analyticsData.requests.filter(r => r.authorized).length
                }
            };
            
            const csvContent = [
                ['MSICT System Analytics Report'],
                ['Generated:', new Date().toLocaleDateString()],
                ['Period:', document.getElementById('periodFilter').value + ' days'],
                [''],
                ['Key Metrics'],
                ['Total Users', reportData.metrics.totalUsers],
                ['Total Requests', reportData.metrics.totalRequests],
                ['Total Items', reportData.metrics.totalItems],
                ['System Efficiency', reportData.metrics.systemEfficiency + '%'],
                [''],
                ['Request Status'],
                ['Pending', reportData.requestStatus.pending],
                ['Approved', reportData.requestStatus.approved],
                ['Authorized', reportData.requestStatus.authorized],
                [''],
                ['Performance Metrics'],
                ['Approval Rate', calculateApprovalRate() + '%'],
                ['Avg Processing Time', calculateAverageProcessingTime() + ' hours']
            ].map(row => row.join(',')).join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `MSICT_Analytics_Report_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            showAlert('📊 Analytics report exported successfully!', 'success');
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

        // Make functions globally accessible
        window.refreshAnalytics = refreshAnalytics;
        window.exportAnalytics = exportAnalytics;
        window.updateAnalytics = updateAnalytics;

        // Initialize system
        console.log('📊 MSICT Analytics System Initialized');
        console.log('🔗 API Base URL:', API_BASE_URL);
        console.log('🔑 JWT Token Status:', token ? 'Present' : 'Missing');
        console.log('👤 Current User Role:', userRole);
        
        // Show welcome message after page loads
        setTimeout(() => {
            showAlert('📊 Analytics Dashboard Ready!', 'success');
        }, 2000);
    </script>
</body>
</html>
