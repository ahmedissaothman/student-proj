<?php
// dashboard/officer-dashboard.php
session_start();

// Check if user is logged in and is officer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'officer') {
    header('Location: ../auth/login.php');
    exit();
}

// Set user data for components
$_SESSION['username'] = $_SESSION['username'] ?? 'officer001';
$_SESSION['full_name'] = $_SESSION['full_name'] ?? 'Lt. Mwala';
$_SESSION['user_role'] = 'officer';
$_SESSION['department'] = $_SESSION['department'] ?? 'Quarter Master Office';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer Dashboard - MSICT Ordering System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Officer Dashboard Specific Styles */
        
        /* Officer Stats Cards - Approval focused */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--card-bg-1), var(--card-bg-2));
            border-radius: 15px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            min-height: 140px;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        /* Officer specific gradient colors */
        .stat-card.pending {
            --card-bg-1: #ff9a9e;
            --card-bg-2: #fecfef;
        }

        .stat-card.approved {
            --card-bg-1: #a8edea;
            --card-bg-2: #fed6e3;
        }

        .stat-card.my-department {
            --card-bg-1: #667eea;
            --card-bg-2: #764ba2;
        }

        .stat-card.processing {
            --card-bg-1: #4facfe;
            --card-bg-2: #00f2fe;
        }

        .stat-card.urgent {
            --card-bg-1: #fa709a;
            --card-bg-2: #fee140;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: scale(0);
            transition: transform 0.6s ease;
        }

        .stat-card:hover::before {
            transform: scale(3);
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            backdrop-filter: blur(10px);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: white;
            margin: 0.5rem 0 0.25rem 0;
        }

        .stat-label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 0.75rem;
            font-weight: 500;
        }

        .stat-change {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            background: rgba(255, 255, 255, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        /* Priority Actions */
        .priority-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .priority-action {
            background: white;
            border-left: 4px solid;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .priority-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .priority-action.urgent {
            border-left-color: #e74c3c;
        }

        .priority-action.normal {
            border-left-color: #f39c12;
        }

        .priority-action.low {
            border-left-color: #27ae60;
        }

        .priority-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            color: white;
        }

        .priority-action.urgent .priority-icon {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        .priority-action.normal .priority-icon {
            background: linear-gradient(135deg, #f39c12, #e67e22);
        }

        .priority-action.low .priority-icon {
            background: linear-gradient(135deg, #27ae60, #16a085);
        }

        .priority-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .priority-count {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        /* Approval Queue */
        .approval-queue {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .queue-header {
            padding: 1.5rem;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .queue-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .queue-item {
            padding: 1.5rem;
            border-bottom: 1px solid #f8f9fa;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
        }

        .queue-item:hover {
            background: #f8f9fa;
        }

        .queue-item:last-child {
            border-bottom: none;
            border-radius: 0 0 15px 15px;
        }

        .request-priority {
            width: 8px;
            height: 60px;
            border-radius: 4px;
            flex-shrink: 0;
        }

        .request-priority.high {
            background: linear-gradient(to bottom, #e74c3c, #c0392b);
        }

        .request-priority.medium {
            background: linear-gradient(to bottom, #f39c12, #e67e22);
        }

        .request-priority.low {
            background: linear-gradient(to bottom, #27ae60, #16a085);
        }

        .request-info {
            flex: 1;
        }

        .request-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .request-details {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.25rem;
        }

        .request-meta {
            font-size: 0.8rem;
            color: #999;
        }

        .request-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-approve {
            background: linear-gradient(135deg, #27ae60, #16a085);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }

        .btn-reject {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }

        .btn-view {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }

        /* Department Overview */
        .department-overview {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        /* Quick Stats for Officers */
        .officer-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .officer-stat {
            background: white;
            padding: 1.25rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .officer-stat:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .officer-stat-icon {
            width: 45px;
            height: 45px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
        }

        .officer-stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.25rem;
        }

        .officer-stat-label {
            font-size: 0.8rem;
            color: #666;
            font-weight: 500;
        }

        /* Recent Decisions */
        .recent-decisions {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .decision-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .decision-item:last-child {
            border-bottom: none;
        }

        .decision-icon {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.9rem;
        }

        .decision-icon.approved {
            background: linear-gradient(135deg, #27ae60, #16a085);
        }

        .decision-icon.rejected {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        .decision-content {
            flex: 1;
        }

        .decision-title {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .decision-details {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 0.25rem;
        }

        .decision-time {
            font-size: 0.75rem;
            color: #999;
        }

        /* Status badges for officers */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.pending {
            background: linear-gradient(135deg, #ffeaa7, #fab1a0);
            color: #d63031;
        }

        .status-badge.approved {
            background: linear-gradient(135deg, #55efc4, #81ecec);
            color: #00b894;
        }

        .status-badge.rejected {
            background: linear-gradient(135deg, #ff7675, #fd79a8);
            color: #e84393;
        }

        .status-badge.high-priority {
            background: linear-gradient(135deg, #fd79a8, #e84393);
            color: white;
            animation: pulse-priority 2s infinite;
        }

        @keyframes pulse-priority {
            0% { box-shadow: 0 0 0 0 rgba(232, 67, 147, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(232, 67, 147, 0); }
            100% { box-shadow: 0 0 0 0 rgba(232, 67, 147, 0); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            }

            .department-overview {
                grid-template-columns: 1fr;
            }

            .priority-actions {
                grid-template-columns: 1fr;
            }

            .officer-stats {
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            }

            .request-actions {
                flex-direction: column;
                gap: 0.25rem;
            }

            .queue-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .request-priority {
                width: 100%;
                height: 4px;
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
                        <i class="fas fa-user-tie" style="color: var(--primary-color);"></i>
                        Officer Dashboard
                    </h1>
                    <p class="page-subtitle">Welcome, <?php echo $_SESSION['full_name']; ?>! Manage approvals and oversee department operations</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-primary" onclick="viewPendingAll()">
                        <i class="fas fa-list"></i>
                        All Pending
                    </button>
                    <button class="btn btn-success" onclick="quickApproveMode()">
                        <i class="fas fa-bolt"></i>
                        Quick Approve
                    </button>
                </div>
            </div>

            <!-- Alert for pending approvals -->
            <div class="alert" style="background: linear-gradient(135deg, #fff3cd, #ffeaa7); color: #856404; border: 2px solid #ffeaa7;">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>Attention Required:</strong> You have <strong>5 pending requests</strong> requiring immediate approval. Review priority items first.
                </div>
            </div>

            <!-- Main Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card pending">
                    <div class="stat-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-value">5</div>
                    <div class="stat-label">Pending Approvals</div>
                    <div class="stat-change">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Urgent</span>
                    </div>
                </div>

                <div class="stat-card approved">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value">89</div>
                    <div class="stat-label">Approved Today</div>
                    <div class="stat-change">
                        <i class="fas fa-arrow-up"></i>
                        <span>+12%</span>
                    </div>
                </div>

                <div class="stat-card my-department">
                    <div class="stat-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-value">34</div>
                    <div class="stat-label">Department Requests</div>
                    <div class="stat-change">
                        <i class="fas fa-chart-line"></i>
                        <span>This Week</span>
                    </div>
                </div>

                <div class="stat-card processing">
                    <div class="stat-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="stat-value">12</div>
                    <div class="stat-label">Processing</div>
                    <div class="stat-change">
                        <i class="fas fa-clock"></i>
                        <span>In Progress</span>
                    </div>
                </div>

                <div class="stat-card urgent">
                    <div class="stat-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div class="stat-value">3</div>
                    <div class="stat-label">High Priority</div>
                    <div class="stat-change">
                        <i class="fas fa-exclamation"></i>
                        <span>Critical</span>
                    </div>
                </div>
            </div>

            <!-- Priority Actions -->
            <div class="priority-actions">
                <div class="priority-action urgent" onclick="filterByPriority('high')">
                    <div class="priority-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div class="priority-title">High Priority</div>
                    <div class="priority-count">3 Items</div>
                </div>

                <div class="priority-action normal" onclick="filterByPriority('medium')">
                    <div class="priority-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="priority-title">Normal Priority</div>
                    <div class="priority-count">8 Items</div>
                </div>

                <div class="priority-action low" onclick="filterByPriority('low')">
                    <div class="priority-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <div class="priority-title">Low Priority</div>
                    <div class="priority-count">12 Items</div>
                </div>
            </div>

            <!-- Officer Performance Stats -->
            <div class="officer-stats">
                <div class="officer-stat">
                    <div class="officer-stat-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="officer-stat-value">94%</div>
                    <div class="officer-stat-label">Approval Rate</div>
                </div>

                <div class="officer-stat">
                    <div class="officer-stat-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div class="officer-stat-value">1.8</div>
                    <div class="officer-stat-label">Avg Days</div>
                </div>

                <div class="officer-stat">
                    <div class="officer-stat-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="officer-stat-value">247</div>
                    <div class="officer-stat-label">Total Reviewed</div>
                </div>

                <div class="officer-stat">
                    <div class="officer-stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="officer-stat-value">23</div>
                    <div class="officer-stat-label">Team Members</div>
                </div>
            </div>

            <!-- Department Overview -->
            <div class="department-overview">
                <!-- Approval Queue -->
                <div class="approval-queue">
                    <div class="queue-header">
                        <h3 class="queue-title">
                            <i class="fas fa-list-alt"></i>
                            Approval Queue
                        </h3>
                        <span class="status-badge high-priority">5 Pending</span>
                    </div>

                    <div class="queue-item">
                        <div class="request-priority high"></div>
                        <div class="request-info">
                            <div class="request-title">Office Equipment Request - #REQ-045</div>
                            <div class="request-details">Pte. Johnson requesting new computers and printers</div>
                            <div class="request-meta">Submitted: 2 hours ago • Priority: High • Department: IT</div>
                        </div>
                        <div class="request-actions">
                            <button class="btn-approve" onclick="approveRequest('REQ-045')">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn-reject" onclick="rejectRequest('REQ-045')">
                                <i class="fas fa-times"></i> Reject
                            </button>
                            <button class="btn-view" onclick="viewRequest('REQ-045')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>

                    <div class="queue-item">
                        <div class="request-priority medium"></div>
                        <div class="request-info">
                            <div class="request-title">Stationery Supplies - #REQ-046</div>
                            <div class="request-details">Pte. Williams requesting office stationery and supplies</div>
                            <div class="request-meta">Submitted: 5 hours ago • Priority: Medium • Department: Admin</div>
                        </div>
                        <div class="request-actions">
                            <button class="btn-approve" onclick="approveRequest('REQ-046')">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn-reject" onclick="rejectRequest('REQ-046')">
                                <i class="fas fa-times"></i> Reject
                            </button>
                            <button class="btn-view" onclick="viewRequest('REQ-046')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>

                    <div class="queue-item">
                        <div class="request-priority low"></div>
                        <div class="request-info">
                            <div class="request-title">Furniture Request - #REQ-047</div>
                            <div class="request-details">Pte. Brown requesting office furniture for new workspace</div>
                            <div class="request-meta">Submitted: 1 day ago • Priority: Low • Department: Facilities</div>
                        </div>
                        <div class="request-actions">
                            <button class="btn-approve" onclick="approveRequest('REQ-047')">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn-reject" onclick="rejectRequest('REQ-047')">
                                <i class="fas fa-times"></i> Reject
                            </button>
                            <button class="btn-view" onclick="viewRequest('REQ-047')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Recent Decisions -->
                <div class="recent-decisions">
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-history"></i>
                        Recent Decisions
                    </h3>

                    <div class="decision-item">
                        <div class="decision-icon approved">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="decision-content">
                            <div class="decision-title">Approved: Office Supplies #REQ-044</div>
                            <div class="decision-details">Pte. Davis - Stationery and printing materials</div>
                            <div class="decision-time">15 minutes ago</div>
                        </div>
                    </div>

                    <div class="decision-item">
                        <div class="decision-icon approved">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="decision-content">
                            <div class="decision-title">Approved: IT Equipment #REQ-043</div>
                            <div class="decision-details">Pte. Miller - Computer hardware upgrade</div>
                            <div class="decision-time">1 hour ago</div>
                        </div>
                    </div>

                    <div class="decision-item">
                        <div class="decision-icon rejected">
                            <i class="fas fa-times"></i>
                        </div>
                        <div class="decision-content">
                            <div class="decision-title">Rejected: Furniture #REQ-042</div>
                            <div class="decision-details">Pte. Wilson - Insufficient budget justification</div>
                            <div class="decision-time">2 hours ago</div>
                        </div>
                    </div>

                    <div class="decision-item">
                        <div class="decision-icon approved">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="decision-content">
                            <div class="decision-title">Approved: Maintenance #REQ-041</div>
                            <div class="decision-details">Pte. Taylor - Office maintenance supplies</div>
                            <div class="decision-time">3 hours ago</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Department Requests Summary -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i>
                        Department Summary - <?php echo $_SESSION['department']; ?>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Request ID</th>
                                    <th>Requestor</th>
                                    <th>Item Category</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>#REQ-045</strong></td>
                                    <td>Pte. Johnson</td>
                                    <td>IT Equipment</td>
                                    <td><span class="status-badge high-priority">High</span></td>
                                    <td><span class="status-badge pending">Pending</span></td>
                                    <td>2 hours ago</td>
                                    <td>
                                        <button class="btn btn-success btn-sm" onclick="quickApprove('REQ-045')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="quickReject('REQ-045')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button class="btn btn-info btn-sm" onclick="viewRequest('REQ-045')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>#REQ-046</strong></td>
                                    <td>Pte. Williams</td>
                                    <td>Stationery</td>
                                    <td><span class="status-badge" style="background: linear-gradient(135deg, #f39c12, #e67e22); color: white;">Medium</span></td>
                                    <td><span class="status-badge pending">Pending</span></td>
                                    <td>5 hours ago</td>
                                    <td>
                                        <button class="btn btn-success btn-sm" onclick="quickApprove('REQ-046')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="quickReject('REQ-046')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button class="btn btn-info btn-sm" onclick="viewRequest('REQ-046')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>#REQ-047</strong></td>
                                    <td>Pte. Brown</td>
                                    <td>Furniture</td>
                                    <td><span class="status-badge" style="background: linear-gradient(135deg, #27ae60, #16a085); color: white;">Low</span></td>
                                    <td><span class="status-badge pending">Pending</span></td>
                                    <td>1 day ago</td>
                                    <td>
                                        <button class="btn btn-success btn-sm" onclick="quickApprove('REQ-047')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="quickReject('REQ-047')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button class="btn btn-info btn-sm" onclick="viewRequest('REQ-047')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>#REQ-044</strong></td>
                                    <td>Pte. Davis</td>
                                    <td>Office Supplies</td>
                                    <td><span class="status-badge" style="background: linear-gradient(135deg, #f39c12, #e67e22); color: white;">Medium</span></td>
                                    <td><span class="status-badge approved">Approved</span></td>
                                    <td>Yesterday</td>
                                    <td>
                                        <button class="btn btn-info btn-sm" onclick="viewRequest('REQ-044')">
                                            <i class="fas fa-eye"></i> View
                                        </button>
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
    <?php 
    //include 'components/footer.php'; 
    ?>

    <!-- Chart.js Library for Future Charts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

    <!-- Officer Dashboard JavaScript -->
    <script>
        // Officer Dashboard Functions
        
        function viewPendingAll() {
            showAlert('📋 Loading all pending requests...', 'info');
            setTimeout(() => {
                window.location.href = '../modules/approvals/pending-approvals.php';
            }, 800);
        }

        function quickApproveMode() {
            if (confirm('🚀 Enable Quick Approve Mode?\n\nThis will allow you to approve multiple requests rapidly with simplified confirmation.')) {
                showAlert('⚡ Quick Approve Mode Activated!', 'success');
                // Add visual indicator for quick mode
                document.body.classList.add('quick-approve-mode');
                
                // Add quick mode indicator
                const indicator = document.createElement('div');
                indicator.innerHTML = '<i class="fas fa-bolt"></i> Quick Approve Mode';
                indicator.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: linear-gradient(135deg, #f39c12, #e67e22);
                    color: white;
                    padding: 0.5rem 1rem;
                    border-radius: 20px;
                    font-size: 0.8rem;
                    font-weight: 600;
                    z-index: 9999;
                    animation: pulse 2s infinite;
                `;
                document.body.appendChild(indicator);
            }
        }

        function filterByPriority(priority) {
            const priorityNames = {
                'high': 'High Priority',
                'medium': 'Medium Priority', 
                'low': 'Low Priority'
            };
            
            showAlert(`🔍 Filtering by ${priorityNames[priority]} requests...`, 'info');
            
            // Simulate filtering
            setTimeout(() => {
                showAlert(`✅ Found requests with ${priorityNames[priority]}`, 'success');
                // In real app: window.location.href = `../modules/approvals/pending-approvals.php?priority=${priority}`;
            }, 1000);
        }

        function approveRequest(requestId) {
            const requestData = {
                'REQ-045': { user: 'Pte. Johnson', items: 'IT Equipment', priority: 'High' },
                'REQ-046': { user: 'Pte. Williams', items: 'Stationery', priority: 'Medium' },
                'REQ-047': { user: 'Pte. Brown', items: 'Furniture', priority: 'Low' }
            };

            const request = requestData[requestId];
            if (request) {
                if (confirm(`✅ APPROVE REQUEST: ${requestId}\n\n👤 User: ${request.user}\n📦 Items: ${request.items}\n⚡ Priority: ${request.priority}\n\nConfirm approval?`)) {
                    // Show loading state
                    const btn = event.target;
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    btn.disabled = true;

                    setTimeout(() => {
                        showAlert(`✅ Request ${requestId} approved successfully!`, 'success');
                        
                        // Update UI
                        const row = btn.closest('tr') || btn.closest('.queue-item');
                        if (row) {
                            row.style.background = 'linear-gradient(135deg, #d4edda, #c3e6cb)';
                            row.style.border = '2px solid #28a745';
                            
                            // Find status badge and update
                            const statusBadge = row.querySelector('.status-badge.pending');
                            if (statusBadge) {
                                statusBadge.className = 'status-badge approved';
                                statusBadge.textContent = 'Approved';
                            }
                            
                            // Disable approve/reject buttons
                            const actionButtons = row.querySelectorAll('.btn-approve, .btn-reject');
                            actionButtons.forEach(button => {
                                button.disabled = true;
                                button.style.opacity = '0.5';
                            });
                        }
                        
                        // Update pending count
                        updatePendingCount(-1);
                        
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }, 1500);
                }
            }
        }

        function rejectRequest(requestId) {
            const requestData = {
                'REQ-045': { user: 'Pte. Johnson', items: 'IT Equipment' },
                'REQ-046': { user: 'Pte. Williams', items: 'Stationery' },
                'REQ-047': { user: 'Pte. Brown', items: 'Furniture' }
            };

            const request = requestData[requestId];
            if (request) {
                const reason = prompt(`❌ REJECT REQUEST: ${requestId}\n\n👤 User: ${request.user}\n📦 Items: ${request.items}\n\nPlease provide rejection reason:`);
                
                if (reason && reason.trim()) {
                    // Show loading state
                    const btn = event.target;
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    btn.disabled = true;

                    setTimeout(() => {
                        showAlert(`❌ Request ${requestId} rejected with reason: "${reason}"`, 'warning');
                        
                        // Update UI
                        const row = btn.closest('tr') || btn.closest('.queue-item');
                        if (row) {
                            row.style.background = 'linear-gradient(135deg, #f8d7da, #f5c6cb)';
                            row.style.border = '2px solid #dc3545';
                            
                            // Find status badge and update
                            const statusBadge = row.querySelector('.status-badge.pending');
                            if (statusBadge) {
                                statusBadge.className = 'status-badge rejected';
                                statusBadge.textContent = 'Rejected';
                            }
                            
                            // Disable approve/reject buttons
                            const actionButtons = row.querySelectorAll('.btn-approve, .btn-reject');
                            actionButtons.forEach(button => {
                                button.disabled = true;
                                button.style.opacity = '0.5';
                            });
                        }
                        
                        // Update pending count
                        updatePendingCount(-1);
                        
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }, 1500);
                } else if (reason !== null) {
                    showAlert('⚠️ Rejection reason is required', 'warning');
                }
            }
        }

        function quickApprove(requestId) {
            // Simplified approval for quick mode
            if (document.body.classList.contains('quick-approve-mode')) {
                approveRequest(requestId);
            } else {
                approveRequest(requestId);
            }
        }

        function quickReject(requestId) {
            // Simplified rejection for quick mode
            if (document.body.classList.contains('quick-approve-mode')) {
                if (confirm(`❌ Quick reject request ${requestId}?`)) {
                    rejectRequest(requestId);
                }
            } else {
                rejectRequest(requestId);
            }
        }

        function viewRequest(requestId) {
            const requestData = {
                'REQ-045': { user: 'Pte. Johnson', items: 'IT Equipment', dept: 'IT Department', priority: 'High', amount: 'TSH 2,500,000' },
                'REQ-046': { user: 'Pte. Williams', items: 'Stationery', dept: 'Administration', priority: 'Medium', amount: 'TSH 350,000' },
                'REQ-047': { user: 'Pte. Brown', items: 'Furniture', dept: 'Facilities', priority: 'Low', amount: 'TSH 1,200,000' },
                'REQ-044': { user: 'Pte. Davis', items: 'Office Supplies', dept: 'Administration', priority: 'Medium', amount: 'TSH 180,000' }
            };

            const request = requestData[requestId];
            if (request) {
                alert(`📋 REQUEST DETAILS: ${requestId}\n\n👤 Requestor: ${request.user}\n🏢 Department: ${request.dept}\n📦 Items: ${request.items}\n⚡ Priority: ${request.priority}\n💰 Estimated Cost: ${request.amount}\n\n🔍 Click OK to view full details`);
                // In real app: window.location.href = `../modules/requests/view-request.php?id=${requestId}`;
            }
        }

        function updatePendingCount(change) {
            const pendingElements = document.querySelectorAll('.stat-value');
            const pendingCard = document.querySelector('.stat-card.pending .stat-value');
            
            if (pendingCard) {
                const currentCount = parseInt(pendingCard.textContent);
                const newCount = Math.max(0, currentCount + change);
                pendingCard.textContent = newCount;
                
                // Update alert message
                if (newCount === 0) {
                    const alert = document.querySelector('.alert');
                    if (alert) {
                        alert.innerHTML = `
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Great Job!</strong> All requests have been reviewed. No pending approvals at this time.
                            </div>
                        `;
                        alert.style.background = 'linear-gradient(135deg, #d4edda, #c3e6cb)';
                        alert.style.color = '#155724';
                        alert.style.borderColor = '#c3e6cb';
                    }
                }
            }
        }

        // Auto-refresh pending count every 30 seconds
        function refreshOfficerStats() {
            console.log('🔄 Refreshing officer dashboard stats...');
            
            // Simulate real-time updates for officer stats
            const officerStatValues = document.querySelectorAll('.officer-stat-value');
            officerStatValues.forEach(stat => {
                if (stat.textContent.includes('%')) {
                    // Approval rate - small variations
                    const currentRate = parseFloat(stat.textContent);
                    const variation = (Math.random() - 0.5) * 0.2; // ±0.1%
                    const newRate = Math.max(90, Math.min(100, currentRate + variation));
                    stat.textContent = newRate.toFixed(1) + '%';
                }
            });
        }

        // Show enhanced alert notifications
        function showAlert(message, type = 'info') {
            const alert = document.createElement('div');
            alert.className = `officer-alert alert-${type}`;
            alert.style.cssText = `
                position: fixed;
                top: 90px;
                right: 20px;
                background: ${type === 'success' ? 'linear-gradient(135deg, #d4edda, #c3e6cb)' : 
                           type === 'warning' ? 'linear-gradient(135deg, #fff3cd, #ffeaa7)' : 
                           'linear-gradient(135deg, #e3f2fd, #bbdefb)'};
                color: ${type === 'success' ? '#155724' : type === 'warning' ? '#856404' : '#1565c0'};
                padding: 1rem 1.5rem;
                border-radius: 12px;
                box-shadow: 0 8px 30px rgba(0,0,0,0.15);
                z-index: 9999;
                animation: slideInRight 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
                max-width: 400px;
                border: 2px solid ${type === 'success' ? '#c3e6cb' : type === 'warning' ? '#ffeaa7' : '#bbdefb'};
                backdrop-filter: blur(10px);
            `;
            
            const icon = type === 'success' ? 'fa-check-circle' : 
                        type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
            
            alert.innerHTML = `
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas ${icon}" style="font-size: 1.2rem;"></i>
                    <span style="font-weight: 600;">${message}</span>
                </div>
            `;
            
            document.body.appendChild(alert);
            
            setTimeout(() => {
                alert.style.animation = 'slideOutRight 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 400);
            }, 5000);
        }

        // Initialize officer dashboard
        document.addEventListener('DOMContentLoaded', function() {
            // Set up auto-refresh
            setInterval(refreshOfficerStats, 30000);
            
            // Welcome message
            setTimeout(() => {
                showAlert('👮‍♂️ Welcome to Officer Dashboard! You have 5 requests pending approval.', 'info');
            }, 1000);

            // Highlight urgent items
            setTimeout(() => {
                const urgentItems = document.querySelectorAll('.status-badge.high-priority');
                urgentItems.forEach(item => {
                    item.style.animation = 'pulse-priority 2s infinite';
                });
            }, 2000);
        });

        // Keyboard shortcuts for officers
        document.addEventListener('keydown', function(e) {
            // Alt + A for quick approve mode
            if (e.altKey && e.key === 'a') {
                e.preventDefault();
                quickApproveMode();
            }
            
            // Alt + P for view all pending
            if (e.altKey && e.key === 'p') {
                e.preventDefault();
                viewPendingAll();
            }
        });

        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from { 
                    transform: translateX(100%) rotate(5deg); 
                    opacity: 0; 
                    scale: 0.8;
                }
                to { 
                    transform: translateX(0) rotate(0deg); 
                    opacity: 1; 
                    scale: 1;
                }
            }
            @keyframes slideOutRight {
                from { 
                    transform: translateX(0) rotate(0deg); 
                    opacity: 1; 
                    scale: 1;
                }
                to { 
                    transform: translateX(100%) rotate(-5deg); 
                    opacity: 0; 
                    scale: 0.8;
                }
            }
            
            /* Quick approve mode styles */
            .quick-approve-mode .btn-approve {
                animation: glow 1.5s ease-in-out infinite alternate;
            }
            
            @keyframes glow {
                from { box-shadow: 0 0 5px rgba(39, 174, 96, 0.5); }
                to { box-shadow: 0 0 20px rgba(39, 174, 96, 0.8); }
            }
            
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
