<?php
// modules/requests/request-history.php - Department Request History
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
    <title>Request History & Reports - MSICT Department</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* Request History Page Styles */
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
        .history-container {
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

        /* Summary Section */
        .summary-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .summary-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }

        .summary-item {
            text-align: center;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .summary-item:hover {
            transform: translateY(-2px);
        }

        .summary-value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .summary-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--card-bg-1), var(--card-bg-2));
            border-radius: 15px;
            padding: 2rem;
            color: white;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.total-requests { --card-bg-1: #667eea; --card-bg-2: #764ba2; }
        .stat-card.completed-requests { --card-bg-1: #00b894; --card-bg-2: #00cec9; }
        .stat-card.pending-requests { --card-bg-1: #ff7675; --card-bg-2: #fd79a8; }
        .stat-card.success-rate { --card-bg-1: #fdcb6e; --card-bg-2: #e17055; }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0.8;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
        }

        /* Filter Section */
        .filter-section {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .filter-control {
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .filter-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(45, 80, 22, 0.1);
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

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Timeline */
        .timeline-container {
            position: relative;
            padding: 1rem 0;
        }

        .timeline-item {
            display: flex;
            margin-bottom: 2rem;
            padding: 1rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .timeline-item:hover {
            transform: translateX(5px);
        }

        .timeline-date {
            flex: 0 0 120px;
            text-align: center;
            padding: 0.5rem;
            background: var(--primary-color);
            color: white;
            border-radius: 8px;
            margin-right: 1rem;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .timeline-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .timeline-status {
            display: inline-block;
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

        /* Charts */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .chart-container {
            background: var(--white);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            min-height: 300px;
        }

        .chart-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
            text-align: center;
        }

        .chart-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 250px;
            background: #f8f9fa;
            border-radius: 8px;
            color: #666;
            font-size: 0.9rem;
            text-align: center;
        }

        /* Tabs */
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
            .history-container {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .page-actions {
                justify-content: center;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }

            .stat-card {
                padding: 1.5rem;
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }

            .table th,
            .table td {
                padding: 0.5rem 0.25rem;
                font-size: 0.8rem;
            }

            .tabs-header {
                flex-wrap: wrap;
            }

            .tab-button {
                min-width: 120px;
            }

            .summary-grid {
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            }

            .timeline-item {
                flex-direction: column;
            }

            .timeline-date {
                flex: none;
                margin-right: 0;
                margin-bottom: 1rem;
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
        <div class="history-container">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-chart-line"></i>
                        Request History & Reports
                    </h1>
                    <p class="page-subtitle">Analyze your request patterns and track performance over time</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-secondary" onclick="goToMyRequests()">
                        <i class="fas fa-list"></i>
                        My Requests
                    </button>
                    <button class="btn btn-info" onclick="refreshData()">
                        <i class="fas fa-sync"></i>
                        Refresh
                    </button>
                    <button class="btn btn-primary" onclick="exportReport()">
                        <i class="fas fa-download"></i>
                        Export Report
                    </button>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- Summary Section -->
            <div class="summary-section">
                <div class="summary-title">My Request Performance Summary</div>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-value" id="summaryTotal">
                            <div class="loading-skeleton" style="width: 60px; height: 32px;"></div>
                        </div>
                        <div class="summary-label">Total Requests</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value" id="summaryApproved">
                            <div class="loading-skeleton" style="width: 60px; height: 32px;"></div>
                        </div>
                        <div class="summary-label">Approved</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value" id="summarySuccess">
                            <div class="loading-skeleton" style="width: 60px; height: 32px;"></div>
                        </div>
                        <div class="summary-label">Success Rate</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value" id="summaryThisMonth">
                            <div class="loading-skeleton" style="width: 60px; height: 32px;"></div>
                        </div>
                        <div class="summary-label">This Month</div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label class="filter-label">Date Range</label>
                        <select id="dateRangeFilter" class="filter-control">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month" selected>This Month</option>
                            <option value="quarter">This Quarter</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Status Filter</label>
                        <select id="statusFilter" class="filter-control">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="denied">Denied</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">From Date</label>
                        <input type="date" id="fromDate" class="filter-control">
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">To Date</label>
                        <input type="date" id="toDate" class="filter-control">
                    </div>
                    
                    <div class="filter-group">
                        <button class="btn btn-primary" onclick="applyFilters()">
                            <i class="fas fa-filter"></i>
                            Apply Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card total-requests" onclick="filterByPeriod('all')">
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stat-value" id="totalRequests">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Total Requests</div>
                </div>
                
                <div class="stat-card completed-requests" onclick="filterByStatus('approved')">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value" id="completedRequests">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Completed</div>
                </div>
                
                <div class="stat-card pending-requests" onclick="filterByStatus('pending')">
                    <div class="stat-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-value" id="pendingRequests">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Still Pending</div>
                </div>
                
                <div class="stat-card success-rate">
                    <div class="stat-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-value" id="successRate">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Success Rate</div>
                </div>
            </div>

            <!-- Tabs Container -->
            <div class="tabs-container">
                <div class="tabs-header">
                    <button class="tab-button active" onclick="switchTab('overview')">
                        <i class="fas fa-chart-bar"></i>
                        Overview
                    </button>
                    <button class="tab-button" onclick="switchTab('timeline')">
                        <i class="fas fa-timeline"></i>
                        Timeline
                    </button>
                    <button class="tab-button" onclick="switchTab('analytics')">
                        <i class="fas fa-chart-pie"></i>
                        Analytics
                    </button>
                    <button class="tab-button" onclick="switchTab('detailed')">
                        <i class="fas fa-table"></i>
                        Detailed View
                    </button>
                </div>

                <!-- Overview Tab -->
                <div class="tab-content active" id="overviewTab">
                    <!-- Charts -->
                    <div class="charts-grid">
                        <div class="chart-container">
                            <div class="chart-title">Monthly Request Trend</div>
                            <div class="chart-placeholder">
                                <div>
                                    <i class="fas fa-chart-line" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                    Chart showing your request patterns over time
                                </div>
                            </div>
                        </div>
                        
                        <div class="chart-container">
                            <div class="chart-title">Request Status Distribution</div>
                            <div class="chart-placeholder">
                                <div>
                                    <i class="fas fa-chart-pie" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                    Pie chart of request outcomes
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i>
                                Quick Insights
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="quickInsights">
                                <div class="loading-skeleton" style="width: 100%; height: 100px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline Tab -->
                <div class="tab-content" id="timelineTab">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-timeline"></i>
                                Request Timeline
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="timelineContainer">
                                <div class="loading-skeleton" style="width: 100%; height: 200px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Tab -->
                <div class="tab-content" id="analyticsTab">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie"></i>
                                Request Analytics
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="analyticsContainer">
                                <div class="loading-skeleton" style="width: 100%; height: 300px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed View Tab -->
                <div class="tab-content" id="detailedTab">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-table"></i>
                                Detailed Request History
                            </h3>
                            <button class="btn btn-info" onclick="exportDetailedData()">
                                <i class="fas fa-download"></i>
                                Export
                            </button>
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
                                            <th>Submitted</th>
                                            <th>Processing Time</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detailedTableBody">
                                        <tr>
                                            <td colspan="7" style="text-align: center; padding: 2rem;">
                                                <i class="fas fa-spinner fa-spin"></i> Loading detailed data...
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

    <!-- Include Footer Component -->
    <?php include '../../dashboard/components/footer.php'; ?>

    <script>
        // API Configuration
        const API_BASE_URL = 'http://localhost/unfedZombie/Controllers';
        let historyData = {
            allRequests: [],
            filteredRequests: []
        };

        // Get JWT token from session
        const token = '<?php echo $_SESSION["jwt_token"] ?? ""; ?>';

        // API Headers
        const headers = {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        };

        // Load page data
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📊 Request History page loading...');
            setupEventListeners();
            loadHistoryData();
            setDefaultDates();
        });

        // Setup event listeners
        function setupEventListeners() {
            // Filter controls
            document.getElementById('dateRangeFilter').addEventListener('change', handleDateRangeChange);
            document.getElementById('statusFilter').addEventListener('change', applyFilters);
            document.getElementById('fromDate').addEventListener('change', applyFilters);
            document.getElementById('toDate').addEventListener('change', applyFilters);
        }

        // Set default dates
        function setDefaultDates() {
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            
            document.getElementById('fromDate').value = firstDay.toISOString().split('T')[0];
            document.getElementById('toDate').value = today.toISOString().split('T')[0];
        }

        // Handle date range dropdown change
        function handleDateRangeChange() {
            const range = document.getElementById('dateRangeFilter').value;
            const today = new Date();
            let fromDate, toDate;

            switch(range) {
                case 'today':
                    fromDate = toDate = today;
                    break;
                case 'week':
                    fromDate = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                    toDate = today;
                    break;
                case 'month':
                    fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    toDate = today;
                    break;
                case 'quarter':
                    const quarter = Math.floor(today.getMonth() / 3);
                    fromDate = new Date(today.getFullYear(), quarter * 3, 1);
                    toDate = today;
                    break;
                case 'year':
                    fromDate = new Date(today.getFullYear(), 0, 1);
                    toDate = today;
                    break;
                default:
                    return; // Don't update for 'all'
            }

            if (range !== 'all') {
                document.getElementById('fromDate').value = fromDate.toISOString().split('T')[0];
                document.getElementById('toDate').value = toDate.toISOString().split('T')[0];
                applyFilters();
            }
        }

        // Load history data
        async function loadHistoryData() {
            try {
                showAlert('🔄 Loading your request history...', 'info');
                
                const response = await fetch(`${API_BASE_URL}/Department/api/requests/my`, {
                    method: 'GET',
                    headers: headers
                });

                if (!response.ok) {
                    throw new Error(`History API Error: ${response.status}`);
                }

                const requests = await response.json();
                historyData.allRequests = Array.isArray(requests) ? requests : [];
                
                applyFilters();
                showAlert(`✅ Loaded ${historyData.allRequests.length} requests!`, 'success');
                
            } catch (error) {
                console.error('Error loading history:', error);
                showAlert('❌ Error loading history: ' + error.message, 'error');
                
                // Fallback demo data
                historyData.allRequests = [
                    {
                        id: 1,
                        item_name: 'Office Pens',
                        quantity_requested: 10,
                        status: 'approved',
                        priority: 'medium',
                        purpose: 'Office supplies for daily work',
                        request_date: '2024-12-01 10:30:00',
                        remarks: 'Approved for urgent need'
                    },
                    {
                        id: 2,
                        item_name: 'Laptops',
                        quantity_requested: 2,
                        status: 'approved',
                        priority: 'high',
                        purpose: 'New staff computers',
                        request_date: '2024-11-25 14:20:00',
                        remarks: 'Approved - urgent requirement'
                    },
                    {
                        id: 3,
                        item_name: 'Filing Cabinets',
                        quantity_requested: 5,
                        status: 'denied',
                        priority: 'low',
                        purpose: 'Document storage',
                        request_date: '2024-11-20 09:15:00',
                        remarks: 'Budget constraints - resubmit next quarter'
                    },
                    {
                        id: 4,
                        item_name: 'Printer Paper',
                        quantity_requested: 20,
                        status: 'approved',
                        priority: 'medium',
                        purpose: 'Monthly paper supply',
                        request_date: '2024-11-15 11:45:00',
                        remarks: null
                    },
                    {
                        id: 5,
                        item_name: 'Desk Chairs',
                        quantity_requested: 3,
                        status: 'pending',
                        priority: 'medium',
                        purpose: 'New workstations',
                        request_date: '2024-12-05 16:30:00',
                        remarks: null
                    }
                ];
                
                applyFilters();
                showAlert('⚠️ Using demo data - API connection failed', 'warning');
            }
        }

        // Apply filters
        function applyFilters() {
            const statusFilter = document.getElementById('statusFilter').value;
            const fromDate = document.getElementById('fromDate').value;
            const toDate = document.getElementById('toDate').value;

            let filteredData = [...historyData.allRequests];

            // Apply status filter
            if (statusFilter) {
                filteredData = filteredData.filter(req => req.status === statusFilter);
            }

            // Apply date filter
            if (fromDate && toDate) {
                const from = new Date(fromDate);
                const to = new Date(toDate);
                to.setHours(23, 59, 59, 999);

                filteredData = filteredData.filter(req => {
                    const reqDate = new Date(req.request_date);
                    return reqDate >= from && reqDate <= to;
                });
            }

            historyData.filteredRequests = filteredData;
            
            updateStatistics();
            updateSummary();
            displayOverview();
            displayTimeline();
            displayAnalytics();
            displayDetailedView();
        }

        // Update statistics
        function updateStatistics() {
            const total = historyData.filteredRequests.length;
            const approved = historyData.filteredRequests.filter(req => req.status === 'approved').length;
            const pending = historyData.filteredRequests.filter(req => req.status === 'pending').length;
            const successRate = total > 0 ? Math.round((approved / total) * 100) : 0;

            document.getElementById('totalRequests').textContent = total;
            document.getElementById('completedRequests').textContent = approved;
            document.getElementById('pendingRequests').textContent = pending;
            document.getElementById('successRate').textContent = `${successRate}%`;
        }

        // Update summary
        function updateSummary() {
            const total = historyData.allRequests.length;
            const approved = historyData.allRequests.filter(req => req.status === 'approved').length;
            const successRate = total > 0 ? Math.round((approved / total) * 100) : 0;
            
            // This month's requests
            const thisMonth = new Date();
            const monthStart = new Date(thisMonth.getFullYear(), thisMonth.getMonth(), 1);
            const thisMonthRequests = historyData.allRequests.filter(req => {
                const reqDate = new Date(req.request_date);
                return reqDate >= monthStart;
            }).length;

            document.getElementById('summaryTotal').textContent = total;
            document.getElementById('summaryApproved').textContent = approved;
            document.getElementById('summarySuccess').textContent = `${successRate}%`;
            document.getElementById('summaryThisMonth').textContent = thisMonthRequests;
        }

        // Display overview
        function displayOverview() {
            const insights = document.getElementById('quickInsights');
            const total = historyData.filteredRequests.length;
            
            if (total === 0) {
                insights.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-chart-line"></i>
                        <h3>No Data for Selected Period</h3>
                        <p>Try adjusting your filters to see insights</p>
                    </div>
                `;
                return;
            }

            const approved = historyData.filteredRequests.filter(req => req.status === 'approved').length;
            const pending = historyData.filteredRequests.filter(req => req.status === 'pending').length;
            const denied = historyData.filteredRequests.filter(req => req.status === 'denied').length;
            
            // Calculate most requested item
            const itemCounts = {};
            historyData.filteredRequests.forEach(req => {
                const item = req.item_name || 'Unknown';
                itemCounts[item] = (itemCounts[item] || 0) + 1;
            });
            const mostRequested = Object.keys(itemCounts).length > 0 ? 
                Object.keys(itemCounts).reduce((a, b) => itemCounts[a] > itemCounts[b] ? a : b) : 'None';

            insights.innerHTML = `
                <div style="display: grid; gap: 1.5rem;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        <div style="text-align: center; padding: 1rem; background: #e8f5e8; border-radius: 8px;">
                            <div style="font-size: 2rem; color: var(--success); margin-bottom: 0.5rem;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div style="font-size: 1.5rem; font-weight: bold; color: var(--success);">${approved}</div>
                            <div style="font-size: 0.9rem; color: #666;">Approved</div>
                        </div>
                        
                        <div style="text-align: center; padding: 1rem; background: #fff3cd; border-radius: 8px;">
                            <div style="font-size: 2rem; color: var(--warning); margin-bottom: 0.5rem;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div style="font-size: 1.5rem; font-weight: bold; color: var(--warning);">${pending}</div>
                            <div style="font-size: 0.9rem; color: #666;">Pending</div>
                        </div>
                        
                        <div style="text-align: center; padding: 1rem; background: #f8d7da; border-radius: 8px;">
                            <div style="font-size: 2rem; color: var(--danger); margin-bottom: 0.5rem;">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div style="font-size: 1.5rem; font-weight: bold; color: var(--danger);">${denied}</div>
                            <div style="font-size: 0.9rem; color: #666;">Denied</div>
                        </div>
                    </div>
                    
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px;">
                        <h4 style="color: var(--primary-color); margin-bottom: 0.5rem;">Key Insights</h4>
                        <ul style="margin: 0; padding-left: 1.5rem; color: #666;">
                            <li>Most requested item: <strong>${mostRequested}</strong></li>
                            <li>Success rate: <strong>${total > 0 ? Math.round((approved / total) * 100) : 0}%</strong></li>
                            <li>Average processing time: <strong>2-5 days</strong></li>
                            <li>Peak request period: <strong>Beginning of month</strong></li>
                        </ul>
                    </div>
                </div>
            `;
        }

        // Display timeline
        function displayTimeline() {
            const container = document.getElementById('timelineContainer');
            
            if (historyData.filteredRequests.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-timeline"></i>
                        <h3>No Timeline Data</h3>
                        <p>No requests found for the selected period</p>
                    </div>
                `;
                return;
            }

            // Sort by date (newest first)
            const sortedRequests = historyData.filteredRequests
                .sort((a, b) => new Date(b.request_date) - new Date(a.request_date));

            container.innerHTML = `
                <div class="timeline-container">
                    ${sortedRequests.map(request => `
                        <div class="timeline-item">
                            <div class="timeline-date">
                                ${formatDateShort(request.request_date)}
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-title">Request #${request.id} - ${request.item_name}</div>
                                <div class="timeline-description">
                                    Requested ${request.quantity_requested} units for: ${request.purpose || 'No purpose specified'}
                                </div>
                                <div class="timeline-status">
                                    <span class="status-badge ${request.status}">${request.status}</span>
                                    ${request.remarks ? `<span style="margin-left: 1rem; font-size: 0.8rem; color: #666;">${request.remarks}</span>` : ''}
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        // Display analytics
        function displayAnalytics() {
            const container = document.getElementById('analyticsContainer');
            
            if (historyData.filteredRequests.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-chart-pie"></i>
                        <h3>No Analytics Data</h3>
                        <p>No requests found for analysis</p>
                    </div>
                `;
                return;
            }

            // Calculate analytics
            const statusCounts = {
                pending: 0,
                approved: 0,
                denied: 0,
                in_progress: 0
            };

            const priorityCounts = {
                high: 0,
                medium: 0,
                low: 0
            };

            historyData.filteredRequests.forEach(req => {
                statusCounts[req.status] = (statusCounts[req.status] || 0) + 1;
                priorityCounts[req.priority || 'medium'] = (priorityCounts[req.priority || 'medium'] || 0) + 1;
            });

            container.innerHTML = `
                <div style="display: grid; gap: 2rem;">
                    <!-- Status Analytics -->
                    <div>
                        <h4 style="color: var(--primary-color); margin-bottom: 1rem;">Status Distribution</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                            ${Object.entries(statusCounts).map(([status, count]) => `
                                <div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                                    <div style="font-size: 1.5rem; font-weight: bold; margin-bottom: 0.5rem;">${count}</div>
                                    <div style="text-transform: capitalize; color: #666;">${status}</div>
                                    <div style="width: 100%; height: 4px; background: #e0e0e0; border-radius: 2px; margin-top: 0.5rem;">
                                        <div style="width: ${(count / historyData.filteredRequests.length) * 100}%; height: 100%; background: var(--${status === 'approved' ? 'success' : status === 'denied' ? 'danger' : 'warning'}); border-radius: 2px;"></div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>

                    <!-- Priority Analytics -->
                    <div>
                        <h4 style="color: var(--primary-color); margin-bottom: 1rem;">Priority Distribution</h4>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                            ${Object.entries(priorityCounts).map(([priority, count]) => `
                                <div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                                    <div style="font-size: 1.5rem; font-weight: bold; margin-bottom: 0.5rem;">${count}</div>
                                    <div style="text-transform: capitalize; color: #666;">${priority} Priority</div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
        }

        // Display detailed view
        function displayDetailedView() {
            const tbody = document.getElementById('detailedTableBody');
            
            if (historyData.filteredRequests.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="fas fa-table"></i>
                            <h3>No Detailed Data</h3>
                            <p>No requests found for the selected filters</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = historyData.filteredRequests.map(request => `
                <tr>
                    <td><strong>#${request.id}</strong></td>
                    <td>${request.item_name || 'Unknown'}</td>
                    <td><strong>${request.quantity_requested || 0}</strong></td>
                    <td><span style="text-transform: capitalize;">${request.priority || 'Medium'}</span></td>
                    <td><span class="status-badge ${request.status}">${request.status}</span></td>
                    <td>${formatDate(request.request_date)}</td>
                    <td>${calculateProcessingTime(request)}</td>
                </tr>
            `).join('');
        }

        // Tab switching
        function switchTab(tabName) {
            // Remove active class from all tabs and buttons
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            // Add active class to selected tab
            document.querySelector(`[onclick="switchTab('${tabName}')"]`).classList.add('active');
            document.getElementById(tabName + 'Tab').classList.add('active');

            console.log(`🔄 Switched to ${tabName} tab`);
        }

        // Filter functions
        function filterByPeriod(period) {
            document.getElementById('dateRangeFilter').value = period;
            handleDateRangeChange();
            showAlert(`📅 Showing ${period} requests`, 'info');
        }

        function filterByStatus(status) {
            document.getElementById('statusFilter').value = status;
            applyFilters();
            showAlert(`📋 Showing ${status} requests`, 'info');
        }

        // Export functions
        function exportReport() {
            const total = historyData.allRequests.length;
            const approved = historyData.allRequests.filter(req => req.status === 'approved').length;
            const pending = historyData.allRequests.filter(req => req.status === 'pending').length;
            const denied = historyData.allRequests.filter(req => req.status === 'denied').length;

            const csvContent = [
                ['MSICT Department Request History Report'],
                ['Generated:', new Date().toLocaleDateString()],
                ['User:', '<?php echo $_SESSION["full_name"] ?? "Department User"; ?>'],
                [''],
                ['SUMMARY'],
                ['Total Requests', total],
                ['Approved', approved],
                ['Pending', pending],
                ['Denied', denied],
                ['Success Rate', `${total > 0 ? Math.round((approved / total) * 100) : 0}%`],
                [''],
                ['DETAILED HISTORY'],
                ['Request ID', 'Item', 'Quantity', 'Priority', 'Status', 'Date', 'Purpose', 'Remarks'],
                ...historyData.allRequests.map(req => [
                    req.id,
                    req.item_name || 'Unknown',
                    req.quantity_requested || 0,
                    req.priority || 'Medium',
                    req.status,
                    formatDate(req.request_date),
                    req.purpose || 'No purpose',
                    req.remarks || 'None'
                ])
            ].map(row => row.join(',')).join('\n');

            downloadCSV(csvContent, 'Request_History_Report');
            showAlert('📊 History report exported successfully!', 'success');
        }

        function exportDetailedData() {
            if (historyData.filteredRequests.length === 0) {
                showAlert('📭 No data to export for current filters', 'warning');
                return;
            }

            const csvContent = [
                ['Request ID', 'Item', 'Quantity', 'Priority', 'Status', 'Date', 'Processing Time', 'Purpose', 'Remarks'],
                ...historyData.filteredRequests.map(req => [
                    req.id,
                    req.item_name || 'Unknown',
                    req.quantity_requested || 0,
                    req.priority || 'Medium',
                    req.status,
                    formatDate(req.request_date),
                    calculateProcessingTime(req),
                    req.purpose || 'No purpose',
                    req.remarks || 'None'
                ])
            ].map(row => row.join(',')).join('\n');

            downloadCSV(csvContent, 'Detailed_Request_Data');
            showAlert('📊 Detailed data exported successfully!', 'success');
        }

        // Utility functions
        function downloadCSV(content, filename) {
            const blob = new Blob([content], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${filename}_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }

        function calculateProcessingTime(request) {
            // Mock processing time calculation
            const days = Math.floor(Math.random() * 10) + 1;
            return `${days} day${days !== 1 ? 's' : ''}`;
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            try {
                return new Date(dateString).toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (error) {
                return 'Invalid Date';
            }
        }

        function formatDateShort(dateString) {
            if (!dateString) return 'N/A';
            try {
                return new Date(dateString).toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric'
                });
            } catch (error) {
                return 'N/A';
            }
        }

        // Navigation functions
        function goToMyRequests() {
            window.location.href = '../requests/my-requests.php';
        }

        function refreshData() {
            console.log('🔄 Refreshing history data...');
            
            // Reset loading states
            document.getElementById('summaryTotal').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 32px;"></div>';
            document.getElementById('summaryApproved').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 32px;"></div>';
            document.getElementById('summarySuccess').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 32px;"></div>';
            document.getElementById('summaryThisMonth').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 32px;"></div>';
            
            // Reload data
            loadHistoryData();
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
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.style.animation = 'slideOutRight 0.4s ease';
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 400);
                }
            }, 4000);
        }

        // Make functions globally accessible
        window.switchTab = switchTab;
        window.filterByPeriod = filterByPeriod;
        window.filterByStatus = filterByStatus;
        window.applyFilters = applyFilters;
        window.exportReport = exportReport;
        window.exportDetailedData = exportDetailedData;
        window.goToMyRequests = goToMyRequests;
        window.refreshData = refreshData;

        // Initialize
        console.log('📊 MSICT Department Request History Initialized');
        console.log('🔗 API Base URL:', API_BASE_URL);
        console.log('🔑 JWT Token Status:', token ? 'Present' : 'Missing');
        console.log('👤 User Role: Department');
        console.log('📡 Using API Endpoint: GET /Department/api/requests/my');
    </script>
</body>
</html>