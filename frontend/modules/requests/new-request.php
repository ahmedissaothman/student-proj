<?php
// modules/requests/submit-request.php - Department Submit Request
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
    <title>Submit New Request - MSICT Department</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* Submit Request Page Styles */
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
        .submit-container {
            padding: 2rem;
            max-width: 1000px;
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
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }

        .card-body {
            padding: 2rem;
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

        /* Form Styles */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .form-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 12px;
            border-left: 4px solid var(--primary-color);
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--primary-color);
            font-size: 0.9rem;
        }

        .form-label.required::after {
            content: '*';
            color: var(--danger);
            margin-left: 0.25rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
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

        .form-control.error {
            border-color: var(--danger);
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }

        .form-text {
            font-size: 0.8rem;
            color: #666;
            margin-top: 0.25rem;
        }

        .error-message {
            font-size: 0.8rem;
            color: var(--danger);
            margin-top: 0.25rem;
            display: none;
        }

        /* Item Selection */
        .item-selector {
            position: relative;
        }

        .search-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid var(--primary-color);
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s ease;
        }

        .dropdown-item:hover {
            background: var(--light-gray);
        }

        .dropdown-item:last-child {
            border-bottom: none;
        }

        .item-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .item-name {
            font-weight: 600;
            color: var(--primary-color);
        }

        .item-category {
            font-size: 0.8rem;
            color: #666;
        }

        /* Priority Selector */
        .priority-selector {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
        }

        .priority-option {
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .priority-option.high {
            border-color: #dc3545;
            color: #dc3545;
        }

        .priority-option.medium {
            border-color: #ffc107;
            color: #856404;
        }

        .priority-option.low {
            border-color: #28a745;
            color: #155724;
        }

        .priority-option.selected {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
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

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-large {
            padding: 1rem 2rem;
            font-size: 1rem;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid var(--light-gray);
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

        /* Progress Steps */
        .progress-steps {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #f8f9fa;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #666;
        }

        .step.active {
            background: var(--primary-color);
            color: white;
        }

        .step-divider {
            width: 30px;
            height: 2px;
            background: #e1e5e9;
            margin: 0 0.5rem;
        }

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

        /* Responsive */
        @media (max-width: 768px) {
            .submit-container {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .page-actions {
                justify-content: center;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .card-body {
                padding: 1.5rem;
            }

            .form-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .priority-selector {
                grid-template-columns: 1fr;
            }

            .progress-steps {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .step-divider {
                display: none;
            }
        }

        /* Success Animation */
        .success-animation {
            text-align: center;
            padding: 3rem 2rem;
        }

        .success-icon {
            font-size: 4rem;
            color: var(--success);
            margin-bottom: 1rem;
            animation: bounce 1s ease-in-out;
        }

        @keyframes bounce {
            0%, 20%, 60%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            80% { transform: translateY(-5px); }
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
        <div class="submit-container">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-plus-circle"></i>
                        Submit New Request
                    </h1>
                    <p class="page-subtitle">Create a new item request for your department</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-secondary" onclick="goBack()">
                        <i class="fas fa-arrow-left"></i>
                        Back to My Requests
                    </button>
                    <button class="btn btn-info" onclick="saveDraft()" id="saveDraftBtn">
                        <i class="fas fa-save"></i>
                        Save Draft
                    </button>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- Progress Steps -->
            <div class="progress-steps">
                <div class="step active">
                    <i class="fas fa-edit"></i>
                    Fill Details
                </div>
                <div class="step-divider"></div>
                <div class="step">
                    <i class="fas fa-check"></i>
                    Review
                </div>
                <div class="step-divider"></div>
                <div class="step">
                    <i class="fas fa-paper-plane"></i>
                    Submit
                </div>
            </div>

            <!-- Request Form -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-list"></i>
                        Request Information
                    </h3>
                </div>
                <div class="card-body">
                    <form id="requestForm">
                        <div class="form-grid">
                            <!-- Item Selection Section -->
                            <div class="form-section">
                                <div class="section-title">
                                    <i class="fas fa-box"></i>
                                    Item Selection
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label required" for="itemSearch">Search Item</label>
                                    <div class="item-selector">
                                        <input 
                                            type="text" 
                                            id="itemSearch" 
                                            class="form-control" 
                                            placeholder="Type to search for items..."
                                            autocomplete="off"
                                            required
                                        >
                                        <div id="searchDropdown" class="search-dropdown"></div>
                                    </div>
                                    <div class="form-text">Start typing to see available items</div>
                                    <div class="error-message" id="itemError">Please select an item</div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Selected Item</label>
                                    <div id="selectedItem" style="padding: 1rem; background: #e9ecef; border-radius: 8px; color: #666;">
                                        No item selected
                                    </div>
                                    <input type="hidden" id="selectedItemId" name="item_id">
                                </div>

                                <div class="form-group">
                                    <label class="form-label required" for="quantity">Quantity Requested</label>
                                    <input 
                                        type="number" 
                                        id="quantity" 
                                        class="form-control" 
                                        name="quantity_requested"
                                        min="1" 
                                        max="1000"
                                        placeholder="Enter quantity needed"
                                        required
                                    >
                                    <div class="form-text">Maximum 1000 units per request</div>
                                    <div class="error-message" id="quantityError">Please enter a valid quantity</div>
                                </div>
                            </div>

                            <!-- Request Details Section -->
                            <div class="form-section">
                                <div class="section-title">
                                    <i class="fas fa-info-circle"></i>
                                    Request Details
                                </div>

                                <div class="form-group">
                                    <label class="form-label required">Priority Level</label>
                                    <div class="priority-selector">
                                        <div class="priority-option high" data-priority="high">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <div>High</div>
                                            <small>Urgent</small>
                                        </div>
                                        <div class="priority-option medium selected" data-priority="medium">
                                            <i class="fas fa-minus-circle"></i>
                                            <div>Medium</div>
                                            <small>Normal</small>
                                        </div>
                                        <div class="priority-option low" data-priority="low">
                                            <i class="fas fa-check-circle"></i>
                                            <div>Low</div>
                                            <small>When available</small>
                                        </div>
                                    </div>
                                    <input type="hidden" id="priority" name="priority" value="medium">
                                    <div class="form-text">Select the urgency level for this request</div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label required" for="purpose">Purpose/Justification</label>
                                    <textarea 
                                        id="purpose" 
                                        class="form-control" 
                                        name="purpose"
                                        rows="4" 
                                        placeholder="Explain why you need these items, how they will be used, and their importance to your department's operations..."
                                        required
                                    ></textarea>
                                    <div class="form-text">Provide detailed justification for this request</div>
                                    <div class="error-message" id="purposeError">Please provide a purpose for this request</div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="expectedDate">Expected Usage Date</label>
                                    <input 
                                        type="date" 
                                        id="expectedDate" 
                                        class="form-control" 
                                        name="expected_date"
                                        min=""
                                    >
                                    <div class="form-text">When do you expect to use these items? (Optional)</div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="additionalNotes">Additional Notes</label>
                                    <textarea 
                                        id="additionalNotes" 
                                        class="form-control" 
                                        name="additional_notes"
                                        rows="3" 
                                        placeholder="Any additional information, special requirements, or delivery instructions..."
                                    ></textarea>
                                    <div class="form-text">Optional additional information</div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                <i class="fas fa-undo"></i>
                                Reset Form
                            </button>
                            <button type="button" class="btn btn-warning" onclick="previewRequest()">
                                <i class="fas fa-eye"></i>
                                Preview Request
                            </button>
                            <button type="submit" class="btn btn-success btn-large" id="submitBtn">
                                <i class="fas fa-paper-plane"></i>
                                Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Include Footer Component -->
    <?php include '../../dashboard/components/footer.php'; ?>

    <script>
        // API Configuration
        const API_BASE_URL = 'http://localhost/unfedZombie/Controllers';
        let availableItems = [];
        let selectedItem = null;

        // Get JWT token from session
        const token = '<?php echo $_SESSION["jwt_token"] ?? ""; ?>';

        // API Headers
        const headers = {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        };

        // Load page data
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📝 Submit Request page loading...');
            console.log('🔗 API Base URL:', API_BASE_URL);
            console.log('🔑 JWT Token Status:', token ? 'Present' : 'Missing');
            console.log('👤 User Role: Department');
            
            setupEventListeners();
            loadAvailableItems();
            setMinDate();
            loadDraftIfExists();
        });

        // Setup event listeners
        function setupEventListeners() {
            // Item search functionality
            document.getElementById('itemSearch').addEventListener('input', handleItemSearch);
            document.getElementById('itemSearch').addEventListener('focus', showDropdown);
            document.getElementById('itemSearch').addEventListener('blur', hideDropdownDelayed);

            // Priority selection
            document.querySelectorAll('.priority-option').forEach(option => {
                option.addEventListener('click', selectPriority);
            });

            // Form submission
            document.getElementById('requestForm').addEventListener('submit', submitRequest);

            // Form validation
            document.getElementById('quantity').addEventListener('input', validateQuantity);
            document.getElementById('purpose').addEventListener('input', validatePurpose);

            // Auto-save draft
            setInterval(autoSaveDraft, 30000); // Every 30 seconds
        }

        // Load available items
        async function loadAvailableItems() {
            try {
                showAlert('🔄 Loading available items...', 'info');
                
                // Since Department backend needs items from database but doesn't have items endpoint,
                // we'll use the existing items that are referenced in the backend's JOIN queries
                // This means items must exist in the database for requests to work
                
                console.log('📝 Loading items for Department users...');
                
                // These items should match what's in your database 'items' table
                // The Department backend will validate item_id against the items table
                availableItems = [
                    { 
                        id: 1, 
                        name: 'Blue Ballpoint Pens', 
                        category_id: 'Stationery', 
                        description: 'Blue ink ballpoint pens for office use',
                        unit: 'pieces'
                    },
                    { 
                        id: 2, 
                        name: 'HP Business Laptops', 
                        category_id: 'Electronics', 
                        description: 'HP ProBook laptops with Windows 11 Pro',
                        unit: 'units'
                    },
                    { 
                        id: 3, 
                        name: 'Steel Filing Cabinets', 
                        category_id: 'Furniture', 
                        description: '4-drawer steel filing cabinets with locks',
                        unit: 'units'
                    },
                    { 
                        id: 4, 
                        name: 'A4 Copy Paper', 
                        category_id: 'Stationery', 
                        description: 'White A4 copy paper (500 sheets per ream)',
                        unit: 'reams'
                    },
                    { 
                        id: 5, 
                        name: 'Office Chairs', 
                        category_id: 'Furniture', 
                        description: 'Ergonomic office chairs with adjustable height',
                        unit: 'units'
                    },
                    { 
                        id: 6, 
                        name: 'Desktop Computers', 
                        category_id: 'Electronics', 
                        description: 'Dell OptiPlex desktop computers',
                        unit: 'units'
                    },
                    { 
                        id: 7, 
                        name: 'Ink Cartridges', 
                        category_id: 'Electronics', 
                        description: 'HP black ink cartridges for office printers',
                        unit: 'pieces'
                    },
                    { 
                        id: 8, 
                        name: 'Notebooks', 
                        category_id: 'Stationery', 
                        description: 'A5 ruled notebooks for meetings',
                        unit: 'pieces'
                    },
                    { 
                        id: 9, 
                        name: 'Conference Phones', 
                        category_id: 'Electronics', 
                        description: 'Polycom conference phones for meeting rooms',
                        unit: 'units'
                    },
                    { 
                        id: 10, 
                        name: 'Desk Lamps', 
                        category_id: 'Electronics', 
                        description: 'LED desk lamps with adjustable brightness',
                        unit: 'units'
                    }
                ];
                
                showAlert(`✅ Loaded ${availableItems.length} available items!`, 'success');
                
            } catch (error) {
                console.error('Error loading items:', error);
                showAlert('❌ Error loading items: ' + error.message, 'error');
                
                // Minimal fallback
                availableItems = [
                    { id: 1, name: 'Office Supplies', category_id: 'General', description: 'Various office supplies' },
                    { id: 2, name: 'Computer Equipment', category_id: 'Electronics', description: 'Computer hardware' },
                    { id: 3, name: 'Office Furniture', category_id: 'Furniture', description: 'Desks and chairs' }
                ];
                showAlert('⚠️ Using basic item list', 'warning');
            }
        }

        // Handle item search
        function handleItemSearch() {
            const searchTerm = document.getElementById('itemSearch').value.toLowerCase();
            const dropdown = document.getElementById('searchDropdown');
            
            if (searchTerm.length === 0) {
                hideDropdown();
                return;
            }

            const filteredItems = availableItems.filter(item => 
                item.name.toLowerCase().includes(searchTerm) ||
                (item.category_id && item.category_id.toLowerCase().includes(searchTerm)) ||
                (item.description && item.description.toLowerCase().includes(searchTerm))
            );

            displaySearchResults(filteredItems);
            showDropdown();
        }

        // Display search results
        function displaySearchResults(items) {
            const dropdown = document.getElementById('searchDropdown');
            
            if (items.length === 0) {
                dropdown.innerHTML = `
                    <div class="dropdown-item" style="text-align: center; color: #666;">
                        <i class="fas fa-search"></i> No items found
                    </div>
                `;
                return;
            }

            dropdown.innerHTML = items.map(item => `
                <div class="dropdown-item" onclick="selectItem(${item.id}, '${item.name}', '${item.category_id || 'General'}')">
                    <div class="item-info">
                        <div>
                            <div class="item-name">${item.name}</div>
                            <div class="item-category">${item.category_id || 'General'}</div>
                        </div>
                        <i class="fas fa-plus-circle" style="color: var(--success);"></i>
                    </div>
                </div>
            `).join('');
        }

        // Select item from dropdown
        function selectItem(itemId, itemName, category) {
            selectedItem = { id: itemId, name: itemName, category: category };
            
            document.getElementById('itemSearch').value = itemName;
            document.getElementById('selectedItemId').value = itemId;
            document.getElementById('selectedItem').innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>${itemName}</strong><br>
                        <small>Category: ${category}</small>
                    </div>
                    <button type="button" onclick="clearSelection()" style="background: none; border: none; color: var(--danger); font-size: 1.2rem; cursor: pointer;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            hideDropdown();
            validateItem();
            showAlert(`✅ Selected: ${itemName}`, 'success');
        }

        // Clear item selection
        function clearSelection() {
            selectedItem = null;
            document.getElementById('itemSearch').value = '';
            document.getElementById('selectedItemId').value = '';
            document.getElementById('selectedItem').innerHTML = 'No item selected';
            hideDropdown();
        }

        // Dropdown visibility functions
        function showDropdown() {
            document.getElementById('searchDropdown').style.display = 'block';
        }

        function hideDropdown() {
            document.getElementById('searchDropdown').style.display = 'none';
        }

        function hideDropdownDelayed() {
            setTimeout(hideDropdown, 200);
        }

        // Priority selection
        function selectPriority(event) {
            // Remove selected class from all options
            document.querySelectorAll('.priority-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Add selected class to clicked option
            event.currentTarget.classList.add('selected');
            
            // Update hidden input
            const priority = event.currentTarget.dataset.priority;
            document.getElementById('priority').value = priority;
            
            showAlert(`Priority set to: ${priority.charAt(0).toUpperCase() + priority.slice(1)}`, 'info');
        }

        // Form validation functions
        function validateItem() {
            const itemInput = document.getElementById('itemSearch');
            const itemError = document.getElementById('itemError');
            
            if (!selectedItem || !selectedItem.id) {
                itemInput.classList.add('error');
                itemError.style.display = 'block';
                return false;
            } else {
                itemInput.classList.remove('error');
                itemError.style.display = 'none';
                return true;
            }
        }

        function validateQuantity() {
            const quantityInput = document.getElementById('quantity');
            const quantityError = document.getElementById('quantityError');
            const quantity = parseInt(quantityInput.value);
            
            if (!quantity || quantity < 1 || quantity > 1000) {
                quantityInput.classList.add('error');
                quantityError.style.display = 'block';
                return false;
            } else {
                quantityInput.classList.remove('error');
                quantityError.style.display = 'none';
                return true;
            }
        }

        function validatePurpose() {
            const purposeInput = document.getElementById('purpose');
            const purposeError = document.getElementById('purposeError');
            
            if (!purposeInput.value.trim() || purposeInput.value.trim().length < 10) {
                purposeInput.classList.add('error');
                purposeError.style.display = 'block';
                purposeError.textContent = 'Please provide at least 10 characters explaining the purpose';
                return false;
            } else {
                purposeInput.classList.remove('error');
                purposeError.style.display = 'none';
                return true;
            }
        }

        function validateForm() {
            const isItemValid = validateItem();
            const isQuantityValid = validateQuantity();
            const isPurposeValid = validatePurpose();
            
            return isItemValid && isQuantityValid && isPurposeValid;
        }

        // Submit request
        async function submitRequest(event) {
            event.preventDefault();
            
            if (!validateForm()) {
                showAlert('❌ Please fix the errors in the form before submitting', 'error');
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

            try {
                // Prepare form data exactly as Department backend expects
                const formData = {
                    item_id: parseInt(document.getElementById('selectedItemId').value),
                    quantity_requested: parseInt(document.getElementById('quantity').value),
                    purpose: document.getElementById('purpose').value.trim(),
                    priority: document.getElementById('priority').value
                };

                // Validate required fields as per backend
                if (!formData.item_id || !formData.quantity_requested) {
                    throw new Error('item_id and quantity_requested are required');
                }

                console.log('📤 Submitting request data:', formData);
                console.log('🎯 API Endpoint:', `${API_BASE_URL}/Department/api/requests/add`);

                showAlert('🔄 Submitting your request...', 'info');
                
                const response = await fetch(`${API_BASE_URL}/Department/api/requests/add`, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify(formData)
                });

                console.log('📡 Response status:', response.status);

                // Get response text first to handle both success and error cases
                const responseText = await response.text();
                console.log('📄 Response text:', responseText);

                if (!response.ok) {
                    let errorMessage = `HTTP ${response.status}`;
                    try {
                        const errorData = JSON.parse(responseText);
                        errorMessage = errorData.message || errorData.error || errorMessage;
                    } catch (e) {
                        errorMessage = responseText || errorMessage;
                    }
                    throw new Error(errorMessage);
                }

                // Parse successful response
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (e) {
                    throw new Error('Invalid response format from server');
                }

                console.log('✅ Submission result:', result);
                
                // Clear any saved draft
                clearDraft();
                
                // Show success message with request ID
                const requestId = result.request_id || result.id || 'NEW';
                showSuccessScreen(requestId);
                
            } catch (error) {
                console.error('❌ Error submitting request:', error);
                
                // Provide specific error messages based on error type
                let errorMessage = error.message;
                
                if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError')) {
                    errorMessage = 'Network error. Please check your connection and try again.';
                } else if (error.message.includes('403') || error.message.includes('Access denied')) {
                    errorMessage = 'Access denied. Please ensure you are logged in as a Department user.';
                } else if (error.message.includes('400') || error.message.includes('required')) {
                    errorMessage = 'Invalid request data. Please check all required fields are filled correctly.';
                } else if (error.message.includes('401') || error.message.includes('Authorization')) {
                    errorMessage = 'Authentication failed. Please log in again.';
                } else if (error.message.includes('404')) {
                    errorMessage = 'API endpoint not found. Please contact system administrator.';
                } else if (error.message.includes('500')) {
                    errorMessage = 'Server error. Please try again later or contact support.';
                }
                
                showAlert('❌ ' + errorMessage, 'error');
                
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Request';
            }
        }

        // Show success screen
        function showSuccessScreen(requestId) {
            const container = document.querySelector('.submit-container');
            container.innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <div class="success-animation">
                            <div class="success-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h2 style="color: var(--success); margin-bottom: 1rem;">Request Submitted Successfully!</h2>
                            <p style="font-size: 1.1rem; margin-bottom: 2rem;">
                                Your request has been submitted and assigned ID: <strong>#${requestId}</strong>
                            </p>
                            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem;">
                                <h4 style="color: var(--primary-color); margin-bottom: 1rem;">What happens next?</h4>
                                <div style="text-align: left; max-width: 400px; margin: 0 auto;">
                                    <div style="margin-bottom: 0.5rem;">
                                        <i class="fas fa-clock" style="color: var(--warning); margin-right: 0.5rem;"></i>
                                        Your request will be reviewed by the appropriate authority
                                    </div>
                                    <div style="margin-bottom: 0.5rem;">
                                        <i class="fas fa-bell" style="color: var(--info); margin-right: 0.5rem;"></i>
                                        You'll be notified of any status changes
                                    </div>
                                    <div>
                                        <i class="fas fa-check" style="color: var(--success); margin-right: 0.5rem;"></i>
                                        Once approved, your items will be prepared for collection
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                                <button class="btn btn-primary" onclick="goToMyRequests()">
                                    <i class="fas fa-list"></i>
                                    View My Requests
                                </button>
                                <button class="btn btn-success" onclick="submitAnother()">
                                    <i class="fas fa-plus"></i>
                                    Submit Another Request
                                </button>
                                <button class="btn btn-info" onclick="trackRequest('${requestId}')">
                                    <i class="fas fa-search"></i>
                                    Track This Request
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Preview request
        function previewRequest() {
            if (!validateForm()) {
                showAlert('❌ Please fix the errors in the form before previewing', 'error');
                return;
            }

            const itemName = selectedItem ? selectedItem.name : 'Unknown Item';
            const quantity = document.getElementById('quantity').value;
            const priority = document.getElementById('priority').value;
            const purpose = document.getElementById('purpose').value;
            const expectedDate = document.getElementById('expectedDate').value;
            const additionalNotes = document.getElementById('additionalNotes').value;

            const previewContent = `
                <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 10px; margin: 1rem 0;">
                    <h4 style="color: var(--primary-color); margin-bottom: 1rem;">Request Preview</h4>
                    <div style="display: grid; gap: 0.75rem;">
                        <div><strong>Item:</strong> ${itemName}</div>
                        <div><strong>Quantity:</strong> ${quantity}</div>
                        <div><strong>Priority:</strong> <span style="text-transform: capitalize;">${priority}</span></div>
                        <div><strong>Purpose:</strong> ${purpose}</div>
                        ${expectedDate ? `<div><strong>Expected Date:</strong> ${new Date(expectedDate).toLocaleDateString()}</div>` : ''}
                        ${additionalNotes ? `<div><strong>Notes:</strong> ${additionalNotes}</div>` : ''}
                    </div>
                    <div style="margin-top: 1rem; text-align: center;">
                        <small style="color: #666;">Review the details above before submitting</small>
                    </div>
                </div>
            `;

            showAlert(previewContent, 'info');
        }

        // Draft functionality
        function saveDraft() {
            const draftData = {
                item_id: document.getElementById('selectedItemId').value,
                item_name: selectedItem ? selectedItem.name : '',
                quantity: document.getElementById('quantity').value,
                priority: document.getElementById('priority').value,
                purpose: document.getElementById('purpose').value,
                expected_date: document.getElementById('expectedDate').value,
                additional_notes: document.getElementById('additionalNotes').value,
                saved_at: new Date().toISOString()
            };

            try {
                localStorage.setItem('request_draft', JSON.stringify(draftData));
                showAlert('💾 Draft saved successfully!', 'success');
            } catch (error) {
                showAlert('❌ Could not save draft', 'error');
            }
        }

        function autoSaveDraft() {
            const hasContent = document.getElementById('selectedItemId').value || 
                             document.getElementById('quantity').value || 
                             document.getElementById('purpose').value;
            
            if (hasContent) {
                saveDraft();
                console.log('📝 Auto-saved draft');
            }
        }

        function loadDraftIfExists() {
            try {
                const savedDraft = localStorage.getItem('request_draft');
                if (savedDraft) {
                    const draftData = JSON.parse(savedDraft);
                    
                    // Ask user if they want to restore draft
                    if (confirm('📝 Found a saved draft from ' + new Date(draftData.saved_at).toLocaleString() + '. Would you like to restore it?')) {
                        restoreDraft(draftData);
                    } else {
                        clearDraft();
                    }
                }
            } catch (error) {
                console.error('Error loading draft:', error);
            }
        }

        function restoreDraft(draftData) {
            if (draftData.item_id && draftData.item_name) {
                selectItem(parseInt(draftData.item_id), draftData.item_name, 'Restored');
            }
            
            document.getElementById('quantity').value = draftData.quantity || '';
            document.getElementById('purpose').value = draftData.purpose || '';
            document.getElementById('expectedDate').value = draftData.expected_date || '';
            document.getElementById('additionalNotes').value = draftData.additional_notes || '';
            
            if (draftData.priority) {
                document.querySelectorAll('.priority-option').forEach(option => {
                    option.classList.remove('selected');
                    if (option.dataset.priority === draftData.priority) {
                        option.classList.add('selected');
                    }
                });
                document.getElementById('priority').value = draftData.priority;
            }

            showAlert('📝 Draft restored successfully!', 'success');
        }

        function clearDraft() {
            try {
                localStorage.removeItem('request_draft');
            } catch (error) {
                console.error('Error clearing draft:', error);
            }
        }

        // Utility functions
        function setMinDate() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('expectedDate').min = today;
        }

        function resetForm() {
            if (confirm('⚠️ Are you sure you want to reset the form? All entered data will be lost.')) {
                document.getElementById('requestForm').reset();
                clearSelection();
                
                // Reset priority to medium
                document.querySelectorAll('.priority-option').forEach(option => {
                    option.classList.remove('selected');
                });
                document.querySelector('.priority-option[data-priority="medium"]').classList.add('selected');
                document.getElementById('priority').value = 'medium';
                
                // Clear validation errors
                document.querySelectorAll('.form-control').forEach(input => {
                    input.classList.remove('error');
                });
                document.querySelectorAll('.error-message').forEach(error => {
                    error.style.display = 'none';
                });
                
                clearDraft();
                showAlert('🔄 Form reset successfully', 'info');
            }
        }

        // Navigation functions
        function goBack() {
            window.location.href = '../requests/my-requests.php';
        }

        function goToMyRequests() {
            window.location.href = '../requests/my-requests.php';
        }

        function submitAnother() {
            window.location.reload();
        }

        function trackRequest(requestId) {
            window.location.href = `../requests/my-requests.php?track=${requestId}`;
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
            
            // Auto remove after timeout (longer for preview)
            const timeout = message.includes('Request Preview') ? 10000 : 
                           ['warning', 'error'].includes(type) ? 6000 : 4000;
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
        window.goBack = goBack;
        window.saveDraft = saveDraft;
        window.resetForm = resetForm;
        window.previewRequest = previewRequest;
        window.selectItem = selectItem;
        window.clearSelection = clearSelection;
        window.goToMyRequests = goToMyRequests;
        window.submitAnother = submitAnother;
        window.trackRequest = trackRequest;

        // Initialize
        console.log('📝 MSICT Department Submit Request Initialized');
        console.log('🔗 API Base URL:', API_BASE_URL);
        console.log('🔑 JWT Token Status:', token ? 'Present' : 'Missing');
        console.log('👤 User Role: Department');
        console.log('📡 Using API Endpoint: POST /Department/api/requests/add');
        console.log('📋 Backend Requirements:');
        console.log('  - item_id (required): must exist in items table');
        console.log('  - quantity_requested (required): integer');
        console.log('  - purpose (optional): text description');
        console.log('  - priority (optional): defaults to "medium"');
        console.log('💾 Backend validates item_id against items table via JOIN');
        console.log('⚠️ Items displayed must match database items table for successful submission');
    </script>
</body>
</html>