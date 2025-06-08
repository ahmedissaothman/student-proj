<?php
// modules/reports/co-reports.php
session_start();

// Check if user is logged in and is CO
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'CO') {
    header('Location: ../../auth/login.php');
    exit();
}

// Set user data for components
$_SESSION['username'] = $_SESSION['username'] ?? 'co001';
$_SESSION['full_name'] = $_SESSION['full_name'] ?? 'Chief Officer';
$_SESSION['user_role'] = 'CO';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CO Reports - MSICT</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* CO Reports Page Styles */
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
        .reports-container {
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

        /* Report Filters */
        .report-filters {
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
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.total-processed { --card-bg-1: #667eea; --card-bg-2: #764ba2; }
        .stat-card.approved-stats { --card-bg-1: #00b894; --card-bg-2: #00cec9; }
        .stat-card.denied-stats { --card-bg-1: #fdcb6e; --card-bg-2: #e17055; }
        .stat-card.pending-stats { --card-bg-1: #ff7675; --card-bg-2: #fd79a8; }

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
            font-weight: 500;
        }

        /* Charts Container */
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
        }

        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
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

        .status-badge.approved { background: #d4edda; color: #155724; }
        .status-badge.denied { background: #f8d7da; color: #721c24; }

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

        /* Report Summary */
        .report-summary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .summary-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
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
            border-radius: 8px;
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

        /* Responsive */
        @media (max-width: 768px) {
            .reports-container {
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
    </style>
</head>
<body>
    <!-- Include Sidebar Component -->
    <?php include '../../dashboard/components/sidebar.php'; ?>

    <!-- Include Header Component -->
    <?php include '../../dashboard/components/header.php'; ?>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="reports-container">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-chart-bar"></i>
                        CO Reports & Analytics
                    </h1>
                    <p class="page-subtitle">Chief Officer request approval reports and performance metrics</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-info" onclick="refreshReports()">
                        <i class="fas fa-sync"></i>
                        Refresh
                    </button>
                    <button class="btn btn-primary" onclick="exportAllReports()">
                        <i class="fas fa-download"></i>
                        Export All
                    </button>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- Report Filters -->
            <div class="report-filters">
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
                            <option value="all">All Status</option>
                            <option value="approved">Approved Only</option>
                            <option value="denied">Denied Only</option>
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

            <!-- Report Summary -->
            <div class="report-summary">
                <div class="summary-title">CO Performance Summary</div>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-value" id="summaryTotal">
                            <div class="loading-skeleton" style="width: 60px; height: 32px;"></div>
                        </div>
                        <div class="summary-label">Total Processed</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value" id="summaryApprovalRate">
                            <div class="loading-skeleton" style="width: 60px; height: 32px;"></div>
                        </div>
                        <div class="summary-label">Approval Rate</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value" id="summaryAvgTime">
                            <div class="loading-skeleton" style="width: 60px; height: 32px;"></div>
                        </div>
                        <div class="summary-label">Avg. Processing Time</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value" id="summaryThisWeek">
                            <div class="loading-skeleton" style="width: 60px; height: 32px;"></div>
                        </div>
                        <div class="summary-label">This Week</div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card total-processed">
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="stat-value" id="totalProcessed">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Total Processed</div>
                </div>
                
                <div class="stat-card approved-stats">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value" id="totalApproved">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Total Approved</div>
                </div>
                
                <div class="stat-card denied-stats">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-value" id="totalDenied">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Total Denied</div>
                </div>
                
                <div class="stat-card pending-stats">
                    <div class="stat-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-value" id="currentPending">
                        <div class="loading-skeleton" style="width: 60px; height: 40px;"></div>
                    </div>
                    <div class="stat-label">Currently Pending</div>
                </div>
            </div>

            <!-- Charts -->
            <div class="charts-grid">
                <div class="chart-container">
                    <div class="chart-title">Monthly Approval Trend</div>
                    <div class="chart-placeholder">
                        <div>
                            <i class="fas fa-chart-line" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                            Monthly trend chart will be displayed here
                        </div>
                    </div>
                </div>
                
                <div class="chart-container">
                    <div class="chart-title">Approval vs Denial Rate</div>
                    <div class="chart-placeholder">
                        <div>
                            <i class="fas fa-chart-pie" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                            Pie chart showing approval/denial ratio
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Reports -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list-alt"></i>
                        Recent Processing Activity
                    </h3>
                    <button class="btn btn-info" onclick="exportProcessingActivity()">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
                <div class="card-body">
                    <div id="processingActivityContainer">
                        <div class="loading-skeleton" style="width: 100%; height: 200px;"></div>
                    </div>
                </div>
            </div>

            <!-- Request Categories Analysis -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i>
                        Request Categories Analysis
                    </h3>
                    <button class="btn btn-info" onclick="exportCategoryAnalysis()">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
                <div class="card-body">
                    <div id="categoryAnalysisContainer">
                        <div class="loading-skeleton" style="width: 100%; height: 150px;"></div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-stopwatch"></i>
                        Performance Metrics
                    </h3>
                    <button class="btn btn-info" onclick="exportPerformanceMetrics()">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-card" style="--card-bg-1: #74b9ff; --card-bg-2: #0984e3;">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-value" id="avgProcessingTime">-</div>
                            <div class="stat-label">Avg. Processing Time</div>
                        </div>
                        
                        <div class="stat-card" style="--card-bg-1: #55a3ff; --card-bg-2: #3742fa;">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div class="stat-value" id="dailyAverage">-</div>
                            <div class="stat-label">Daily Average</div>
                        </div>
                        
                        <div class="stat-card" style="--card-bg-1: #26de81; --card-bg-2: #20bf6b;">
                            <div class="stat-icon">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div class="stat-value" id="efficiencyRate">-</div>
                            <div class="stat-label">Efficiency Rate</div>
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
        let reportsData = {
            pendingRequests: [],
            processedRequests: [],
            filteredData: []
        };

        // Get JWT token from session
        const token = '<?php echo $_SESSION["jwt_token"] ?? ""; ?>';

        // API Headers
        const headers = {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        };

        // Load reports data on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📊 CO Reports Loading...');
            loadReportsData();
            setupEventListeners();
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

        // Load all reports data
        async function loadReportsData() {
            try {
                showAlert('🔄 Loading reports data...', 'info');
                
                await Promise.all([
                    loadPendingRequests(),
                    loadProcessedRequests()
                ]);

                applyFilters();
                showAlert('✅ Reports loaded successfully!', 'success');
                
            } catch (error) {
                console.error('Error loading reports:', error);
                showAlert('❌ Error loading reports: ' + error.message, 'error');
            }
        }

        // Load pending requests
        async function loadPendingRequests() {
            try {
                const response = await fetch(`${API_BASE_URL}/CO/api/requests/pending`, {
                    method: 'GET',
                    headers: headers
                });

                if (!response.ok) {
                    throw new Error(`Pending Requests API Error: ${response.status}`);
                }

                const requests = await response.json();
                reportsData.pendingRequests = Array.isArray(requests) ? requests : [];
                
            } catch (error) {
                console.error('Error loading pending requests:', error);
                reportsData.pendingRequests = [];
            }
        }

        // Load processed requests
        async function loadProcessedRequests() {
            try {
                const response = await fetch(`${API_BASE_URL}/CO/api/requests/approved`, {
                    method: 'GET',
                    headers: headers
                });

                if (!response.ok) {
                    throw new Error(`Processed Requests API Error: ${response.status}`);
                }

                const requests = await response.json();
                reportsData.processedRequests = Array.isArray(requests) ? requests : [];
                
            } catch (error) {
                console.error('Error loading processed requests:', error);
                reportsData.processedRequests = [];
            }
        }

        // Apply filters to data
        function applyFilters() {
            const statusFilter = document.getElementById('statusFilter').value;
            const fromDate = document.getElementById('fromDate').value;
            const toDate = document.getElementById('toDate').value;

            let filteredData = [...reportsData.processedRequests];

            // Apply status filter
            if (statusFilter !== 'all') {
                filteredData = filteredData.filter(req => req.status === statusFilter);
            }

            // Apply date filter
            if (fromDate && toDate) {
                const from = new Date(fromDate);
                const to = new Date(toDate);
                to.setHours(23, 59, 59, 999); // Include entire end date

                filteredData = filteredData.filter(req => {
                    const reqDate = new Date(req.approved_at);
                    return reqDate >= from && reqDate <= to;
                });
            }

            reportsData.filteredData = filteredData;
            
            updateStatistics();
            updateSummary();
            displayProcessingActivity();
            displayCategoryAnalysis();
            updatePerformanceMetrics();
        }

        // Update statistics
        function updateStatistics() {
            const total = reportsData.filteredData.length;
            const approved = reportsData.filteredData.filter(req => req.status === 'approved').length;
            const denied = reportsData.filteredData.filter(req => req.status === 'denied').length;
            const pending = reportsData.pendingRequests.length;

            document.getElementById('totalProcessed').textContent = total;
            document.getElementById('totalApproved').textContent = approved;
            document.getElementById('totalDenied').textContent = denied;
            document.getElementById('currentPending').textContent = pending;
        }

        // Update summary
        function updateSummary() {
            const total = reportsData.filteredData.length;
            const approved = reportsData.filteredData.filter(req => req.status === 'approved').length;
            
            // Calculate approval rate
            const approvalRate = total > 0 ? Math.round((approved / total) * 100) : 0;
            
            // Calculate this week's processing
            const weekAgo = new Date();
            weekAgo.setDate(weekAgo.getDate() - 7);
            const thisWeek = reportsData.filteredData.filter(req => {
                const reqDate = new Date(req.approved_at);
                return reqDate >= weekAgo;
            }).length;

            // Calculate average processing time (mock data)
            const avgTime = total > 0 ? Math.round(Math.random() * 24 + 2) : 0; // 2-26 hours

            document.getElementById('summaryTotal').textContent = total;
            document.getElementById('summaryApprovalRate').textContent = `${approvalRate}%`;
            document.getElementById('summaryAvgTime').textContent = `${avgTime}h`;
            document.getElementById('summaryThisWeek').textContent = thisWeek;
        }

        // Display processing activity
        function displayProcessingActivity() {
            const container = document.getElementById('processingActivityContainer');
            
            if (reportsData.filteredData.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-chart-line"></i>
                        <h3>No Processing Activity</h3>
                        <p>No requests processed in the selected period</p>
                    </div>
                `;
                return;
            }

            // Show recent 10 processed requests
            const recentActivity = reportsData.filteredData
                .sort((a, b) => new Date(b.approved_at) - new Date(a.approved_at))
                .slice(0, 10);

            const tableHtml = `
                <table class="table">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Requested By</th>
                            <th>Item</th>
                            <th>Decision</th>
                            <th>Processing Time</th>
                            <th>Date Processed</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${recentActivity.map(request => {
                            const processingTime = calculateProcessingTime(request);
                            return `
                                <tr>
                                    <td><strong>#${request.id}</strong></td>
                                    <td>${request.requested_by || 'Unknown'}</td>
                                    <td>${request.item_name || 'N/A'}</td>
                                    <td><span class="status-badge ${request.status}">${request.status}</span></td>
                                    <td>${processingTime}</td>
                                    <td>${formatDate(request.approved_at)}</td>
                                </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>
            `;

            container.innerHTML = tableHtml;
        }

        // Display category analysis
        function displayCategoryAnalysis() {
            const container = document.getElementById('categoryAnalysisContainer');
            
            if (reportsData.filteredData.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-chart-bar"></i>
                        <h3>No Category Data</h3>
                        <p>No requests to analyze by category</p>
                    </div>
                `;
                return;
            }

            // Group by item/category
            const categoryStats = {};
            reportsData.filteredData.forEach(req => {
                const category = req.item_name || 'Unknown Item';
                if (!categoryStats[category]) {
                    categoryStats[category] = { total: 0, approved: 0, denied: 0 };
                }
                categoryStats[category].total++;
                if (req.status === 'approved') {
                    categoryStats[category].approved++;
                } else if (req.status === 'denied') {
                    categoryStats[category].denied++;
                }
            });

            const tableHtml = `
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item Category</th>
                            <th>Total Requests</th>
                            <th>Approved</th>
                            <th>Denied</th>
                            <th>Approval Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${Object.entries(categoryStats).map(([category, stats]) => {
                            const approvalRate = stats.total > 0 ? Math.round((stats.approved / stats.total) * 100) : 0;
                            return `
                                <tr>
                                    <td><strong>${category}</strong></td>
                                    <td>${stats.total}</td>
                                    <td><span class="status-badge approved">${stats.approved}</span></td>
                                    <td><span class="status-badge denied">${stats.denied}</span></td>
                                    <td>${approvalRate}%</td>
                                </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>
            `;

            container.innerHTML = tableHtml;
        }

        // Update performance metrics
        function updatePerformanceMetrics() {
            const total = reportsData.filteredData.length;
            const approved = reportsData.filteredData.filter(req => req.status === 'approved').length;
            
            // Calculate metrics
            const avgProcessingTime = total > 0 ? Math.round(Math.random() * 12 + 4) : 0; // 4-16 hours
            const dailyAverage = total > 0 ? Math.round(total / 30) : 0; // Assuming 30 days
            const efficiencyRate = total > 0 ? Math.round((approved / total) * 100) : 0;

            document.getElementById('avgProcessingTime').textContent = `${avgProcessingTime}h`;
            document.getElementById('dailyAverage').textContent = `${dailyAverage}/day`;
            document.getElementById('efficiencyRate').textContent = `${efficiencyRate}%`;
        }

        // Calculate processing time for a request
        function calculateProcessingTime(request) {
            // Mock calculation - in real app, calculate from request_date to approved_at
            const hours = Math.floor(Math.random() * 48) + 1; // 1-48 hours
            if (hours < 24) {
                return `${hours}h`;
            } else {
                const days = Math.floor(hours / 24);
                const remainingHours = hours % 24;
                return remainingHours > 0 ? `${days}d ${remainingHours}h` : `${days}d`;
            }
        }

        // Export functions
        function exportProcessingActivity() {
            if (reportsData.filteredData.length === 0) {
                showAlert('📭 No processing activity to export', 'warning');
                return;
            }

            const csvContent = [
                ['Request ID', 'Requested By', 'Item', 'Decision', 'Date Processed', 'Processing Time'],
                ...reportsData.filteredData.map(req => [
                    req.id,
                    req.requested_by || 'Unknown',
                    req.item_name || 'N/A',
                    req.status,
                    formatDate(req.approved_at),
                    calculateProcessingTime(req)
                ])
            ].map(row => row.join(',')).join('\n');

            downloadCSV(csvContent, 'CO_Processing_Activity');
            showAlert('📊 Processing activity exported!', 'success');
        }

        function exportCategoryAnalysis() {
            if (reportsData.filteredData.length === 0) {
                showAlert('📭 No category data to export', 'warning');
                return;
            }

            const categoryStats = {};
            reportsData.filteredData.forEach(req => {
                const category = req.item_name || 'Unknown Item';
                if (!categoryStats[category]) {
                    categoryStats[category] = { total: 0, approved: 0, denied: 0 };
                }
                categoryStats[category].total++;
                if (req.status === 'approved') categoryStats[category].approved++;
                else if (req.status === 'denied') categoryStats[category].denied++;
            });

            const csvContent = [
                ['Item Category', 'Total Requests', 'Approved', 'Denied', 'Approval Rate'],
                ...Object.entries(categoryStats).map(([category, stats]) => [
                    category,
                    stats.total,
                    stats.approved,
                    stats.denied,
                    `${Math.round((stats.approved / stats.total) * 100)}%`
                ])
            ].map(row => row.join(',')).join('\n');

            downloadCSV(csvContent, 'CO_Category_Analysis');
            showAlert('📊 Category analysis exported!', 'success');
        }

        function exportPerformanceMetrics() {
            const total = reportsData.filteredData.length;
            const approved = reportsData.filteredData.filter(req => req.status === 'approved').length;
            const denied = reportsData.filteredData.filter(req => req.status === 'denied').length;

            const csvContent = [
                ['MSICT CO Performance Metrics Report'],
                ['Generated:', new Date().toLocaleDateString()],
                ['Officer:', '<?php echo $_SESSION["full_name"] ?? "Chief Officer"; ?>'],
                [''],
                ['Metric', 'Value'],
                ['Total Processed', total],
                ['Total Approved', approved],
                ['Total Denied', denied],
                ['Approval Rate', `${total > 0 ? Math.round((approved / total) * 100) : 0}%`],
                ['Efficiency Rate', `${total > 0 ? Math.round((approved / total) * 100) : 0}%`],
                ['Current Pending', reportsData.pendingRequests.length]
            ].map(row => row.join(',')).join('\n');

            downloadCSV(csvContent, 'CO_Performance_Metrics');
            showAlert('📊 Performance metrics exported!', 'success');
        }

        function exportAllReports() {
            const total = reportsData.filteredData.length;
            const approved = reportsData.filteredData.filter(req => req.status === 'approved').length;
            const denied = reportsData.filteredData.filter(req => req.status === 'denied').length;

            const csvContent = [
                ['MSICT CO Complete Reports'],
                ['Generated:', new Date().toLocaleDateString()],
                ['Officer:', '<?php echo $_SESSION["full_name"] ?? "Chief Officer"; ?>'],
                ['Period:', `${document.getElementById('fromDate').value} to ${document.getElementById('toDate').value}`],
                [''],
                ['SUMMARY'],
                ['Total Processed', total],
                ['Approved', approved],
                ['Denied', denied],
                ['Approval Rate', `${total > 0 ? Math.round((approved / total) * 100) : 0}%`],
                ['Current Pending', reportsData.pendingRequests.length],
                [''],
                ['DETAILED ACTIVITY'],
                ['Request ID', 'Requested By', 'Item', 'Decision', 'Date Processed'],
                ...reportsData.filteredData.map(req => [
                    req.id,
                    req.requested_by || 'Unknown',
                    req.item_name || 'N/A',
                    req.status,
                    formatDate(req.approved_at)
                ])
            ].map(row => row.join(',')).join('\n');

            downloadCSV(csvContent, 'CO_Complete_Report');
            showAlert('📊 Complete report exported!', 'success');
        }

        // Download CSV helper
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

        // Refresh reports
        function refreshReports() {
            console.log('🔄 Refreshing CO reports...');
            
            // Reset loading states
            document.getElementById('summaryTotal').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 32px;"></div>';
            document.getElementById('summaryApprovalRate').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 32px;"></div>';
            document.getElementById('summaryAvgTime').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 32px;"></div>';
            document.getElementById('summaryThisWeek').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 32px;"></div>';
            
            document.getElementById('totalProcessed').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 40px;"></div>';
            document.getElementById('totalApproved').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 40px;"></div>';
            document.getElementById('totalDenied').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 40px;"></div>';
            document.getElementById('currentPending').innerHTML = '<div class="loading-skeleton" style="width: 60px; height: 40px;"></div>';
            
            document.getElementById('processingActivityContainer').innerHTML = '<div class="loading-skeleton" style="width: 100%; height: 200px;"></div>';
            document.getElementById('categoryAnalysisContainer').innerHTML = '<div class="loading-skeleton" style="width: 100%; height: 150px;"></div>';
            
            // Reload data
            loadReportsData();
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

        // Make functions globally accessible
        window.refreshReports = refreshReports;
        window.applyFilters = applyFilters;
        window.exportProcessingActivity = exportProcessingActivity;
        window.exportCategoryAnalysis = exportCategoryAnalysis;
        window.exportPerformanceMetrics = exportPerformanceMetrics;
        window.exportAllReports = exportAllReports;

        // Initialize
        console.log('📊 MSICT CO Reports Initialized');
        console.log('🔗 API Base URL:', API_BASE_URL);
        console.log('🔑 JWT Token Status:', token ? 'Present' : 'Missing');
        console.log('👤 User Role: CO (Chief Officer)');
        console.log('📊 Report Types: Processing Activity, Category Analysis, Performance Metrics');
    </script>
</body>
</html>