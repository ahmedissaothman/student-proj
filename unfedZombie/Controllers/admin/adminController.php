<div class="form-group">
                                    <label class="form-label" for="backupFrequency">Backup Frequency</label>
                                    <select id="backupFrequency" class="form-control">
                                        <option value="daily" selected>Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                    <div class="form-text">How often to create automatic backups</div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="backupRetention">Backup Retention (days)</label>
                                    <input type="number" id="backupRetention" class="form-control" value="30" min="1" max="365">
                                    <div class="form-text">Number of days to keep backup files</div>
                                </div>

                                <div class="form-group">
                                    <button class="btn btn-primary" onclick="createBackup()">
                                        <i class="fas fa-download"></i>
                                        Create Backup Now
                                    </button>
                                    <button class="btn btn-info" onclick="testBackupSystem()">
                                        <i class="fas fa-check"></i>
                                        Test Backup System
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Backups -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-history"></i>
                                    Recent Backups
                                </h3>
                            </div>
                            <div class="card-body">
                                <div id="backupsList">
                                    <div class="backup-item">
                                        <div class="backup-info">
                                            <div class="backup-date">December 7, 2024 - 02:00 AM</div>
                                            <div class="backup-size">Database: 12.5 MB | Files: 45.2 MB</div>
                                        </div>
                                        <div>
                                            <button class="btn btn-info btn-sm" onclick="downloadBackup('backup_20241207_0200')">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-warning btn-sm" onclick="restoreBackup('backup_20241207_0200')">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="backup-item">
                                        <div class="backup-info">
                                            <div class="backup-date">December 6, 2024 - 02:00 AM</div>
                                            <div class="backup-size">Database: 12.3 MB | Files: 44.8 MB</div>
                                        </div>
                                        <div>
                                            <button class="btn btn-info btn-sm" onclick="downloadBackup('backup_20241206_0200')">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-warning btn-sm" onclick="restoreBackup('backup_20241206_0200')">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="backup-item">
                                        <div class="backup-info">
                                            <div class="backup-date">December 5, 2024 - 02:00 AM</div>
                                            <div class="backup-size">Database: 12.1 MB | Files: 44.5 MB</div>
                                        </div>
                                        <div>
                                            <button class="btn btn-info btn-sm" onclick="downloadBackup('backup_20241205_0200')">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-warning btn-sm" onclick="restoreBackup('backup_20241205_0200')">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div style="margin-top: 1rem;">
                                    <div class="form-group">
                                        <label class="form-label">Upload Backup File</label>
                                        <input type="file" id="backupFile" class="form-control" accept=".sql,.zip,.tar.gz">
                                        <div class="form-text">Upload a backup file to restore</div>
                                    </div>
                                    <button class="btn btn-warning" onclick="uploadAndRestore()">
                                        <i class="fas fa-upload"></i>
                                        Upload & Restore
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Info Tab -->
                <div class="tab-content" id="systemTab">
                    <div class="settings-grid">
                        <!-- System Status -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-server"></i>
                                    System Status
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="setting-item">
                                    <div class="setting-info">
                                        <div class="setting-name">Database Connection</div>
                                        <div class="setting-description">MySQL database connectivity status</div>
                                    </div>
                                    <div class="setting-control">
                                        <span class="status-indicator online" id="dbStatus">
                                            <i class="fas fa-circle"></i>
                                            Online
                                        </span>
                                    </div>
                                </div>

                                <div class="setting-item">
                                    <div class="setting-info">
                                        <div class="setting-name">Email Service</div>
                                        <div class="setting-description">SMTP email service status</div>
                                    </div>
                                    <div class="setting-control">
                                        <span class="status-indicator online" id="emailStatus">
                                            <i class="fas fa-circle"></i>
                                            Online
                                        </span>
                                    </div>
                                </div>

                                <div class="setting-item">
                                    <div class="setting-info">
                                        <div class="setting-name">File Storage</div>
                                        <div class="setting-description">File system and storage status</div>
                                    </div>
                                    <div class="setting-control">
                                        <span class="status-indicator online" id="storageStatus">
                                            <i class="fas fa-circle"></i>
                                            Available
                                        </span>
                                    </div>
                                </div>

                                <div class="setting-item">
                                    <div class="setting-info">
                                        <div class="setting-name">Backup Service</div>
                                        <div class="setting-description">Automated backup system status</div>
                                    </div>
                                    <div class="setting-control">
                                        <span class="status-indicator online" id="backupStatus">
                                            <i class="fas fa-circle"></i>
                                            Active
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Information -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle"></i>
                                    System Information
                                </h3>
                            </div>
                            <div class="card-body">
                                <table class="table" style="margin: 0;">
                                    <tr>
                                        <td><strong>System Version:</strong></td>
                                        <td>MSICT v1.0.0</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PHP Version:</strong></td>
                                        <td>8.1.2</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Database Version:</strong></td>
                                        <td>MySQL 8.0.25</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Server OS:</strong></td>
                                        <td>Ubuntu 20.04.3 LTS</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Web Server:</strong></td>
                                        <td>Apache/2.4.41</td>
                                    </tr>
                                    <tr>
                                        <td><strong>System Uptime:</strong></td>
                                        <td>15 days, 8 hours, 32 minutes</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Last Update:</strong></td>
                                        <td>November 28, 2024</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Active Users:</strong></td>
                                        <td>23 users</td>
                                    </tr>
                                </table>

                                <div style="margin-top: 1.5rem;">
                                    <button class="btn btn-info" onclick="checkForUpdates()">
                                        <i class="fas fa-sync"></i>
                                        Check for Updates
                                    </button>
                                    <button class="btn btn-primary" onclick="downloadSystemLogs()">
                                        <i class="fas fa-download"></i>
                                        Download Logs
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Maintenance -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tools"></i>
                                System Maintenance
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="alert warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <div>
                                    <strong>Maintenance Mode:</strong> Use these tools carefully. Some operations may temporarily interrupt service.
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                                <button class="btn btn-warning" onclick="clearCache()">
                                    <i class="fas fa-broom"></i>
                                    Clear Cache
                                </button>
                                
                                <button class="btn btn-info" onclick="optimizeDatabase()">
                                    <i class="fas fa-database"></i>
                                    Optimize Database
                                </button>
                                
                                <button class="btn btn-secondary" onclick="rebuildIndex()">
                                    <i class="fas fa-refresh"></i>
                                    Rebuild Search Index
                                </button>
                                
                                <button class="btn btn-primary" onclick="runDiagnostics()">
                                    <i class="fas fa-stethoscope"></i>
                                    Run Diagnostics
                                </button>
                                
                                <button class="btn btn-warning" onclick="enableMaintenanceMode()">
                                    <i class="fas fa-pause"></i>
                                    Maintenance Mode
                                </button>
                                
                                <button class="btn btn-danger" onclick="restartServices()">
                                    <i class="fas fa-power-off"></i>
                                    Restart Services
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>
        /* System Settings Styles */
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
            margin: 0;
        }

        /* Page Layout */
        .settings-container {
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

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
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

        /* Settings Grid */
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
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
            font-size: 0.9rem;
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

        .form-text {
            font-size: 0.8rem;
            color: #666;
            margin-top: 0.25rem;
        }

        /* Toggle Switch */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 28px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 28px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: var(--success);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(22px);
        }

        /* Setting Items */
        .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border: 1px solid #e1e5e9;
            border-radius: 8px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .setting-item:hover {
            border-color: var(--primary-color);
            box-shadow: 0 2px 8px rgba(45, 80, 22, 0.1);
        }

        .setting-info {
            flex: 1;
        }

        .setting-name {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.25rem;
        }

        .setting-description {
            font-size: 0.85rem;
            color: #666;
        }

        .setting-control {
            margin-left: 1rem;
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

        /* Status Indicators */
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-indicator.online {
            background: #d4edda;
            color: #155724;
        }

        .status-indicator.offline {
            background: #f8d7da;
            color: #721c24;
        }

        .status-indicator.warning {
            background: #fff3cd;
            color: #856404;
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

        .alert.warning {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            color: #856404;
        }

        /* Table */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table td {
            padding: 0.75rem;
            border-bottom: 1px solid #e1e5e9;
        }

        .table td:first-child {
            font-weight: 600;
            color: var(--primary-color);
            width: 40%;
        }

        /* Backup Section */
        .backup-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border: 1px solid #e1e5e9;
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }

        .backup-info {
            flex: 1;
        }

        .backup-date {
            font-weight: 600;
            color: var(--primary-color);
        }

        .backup-size {
            font-size: 0.85rem;
            color: #666;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .settings-container {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .page-actions {
                justify-content: center;
            }

            .settings-grid {
                grid-template-columns: 1fr;
            }

            .tabs-header {
                flex-wrap: wrap;
            }

            .tab-button {
                min-width: 120px;
            }

            .setting-item {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }

            .setting-control {
                margin-left: 0;
            }
        }
    </style>

    <script>
        // Current settings state
        let currentSettings = {};
        let hasUnsavedChanges = false;

        // Load settings on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 System Settings page loaded, initializing...');
            loadCurrentSettings();
            setupEventListeners();
            checkSystemStatus();
        });

        // Setup event listeners
        function setupEventListeners() {
            // Track changes to form inputs
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('change', markAsUnsaved);
            });

            // Warn before leaving with unsaved changes
            window.addEventListener('beforeunload', function(e) {
                if (hasUnsavedChanges) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        }

        // Mark settings as having unsaved changes
        function markAsUnsaved() {
            hasUnsavedChanges = true;
            console.log('📝 Settings have unsaved changes');
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

        // Load current settings
        function loadCurrentSettings() {
            console.log('📋 Loading current system settings...');
            
            currentSettings = {
                general: {
                    systemName: document.getElementById('systemName')?.value || 'MSICT Ordering System',
                    timezone: document.getElementById('timezone')?.value || 'Africa/Dar_es_Salaam',
                    language: document.getElementById('language')?.value || 'en'
                },
                backup: {
                    backupFrequency: document.getElementById('backupFrequency')?.value || 'daily',
                    backupRetention: document.getElementById('backupRetention')?.value || '30'
                }
            };

            hasUnsavedChanges = false;
            showAlert('✅ System settings loaded successfully!', 'success');
        }

        // Save all settings
        async function saveAllSettings() {
            console.log('💾 Saving all system settings...');
            
            try {
                showAlert('🔄 Saving settings...', 'info');

                // Collect all current form values
                const newSettings = {
                    backup: {
                        backupFrequency: document.getElementById('backupFrequency')?.value || 'daily',
                        backupRetention: parseInt(document.getElementById('backupRetention')?.value) || 30
                    }
                };

                // Simulate API call
                await new Promise(resolve => setTimeout(resolve, 1500));

                currentSettings = newSettings;
                hasUnsavedChanges = false;
                
                showAlert('✅ All settings saved successfully!', 'success');
                
            } catch (error) {
                console.error('❌ Error saving settings:', error);
                showAlert('❌ Error saving settings: ' + error.message, 'error');
            }
        }

        // Reset to defaults
        function resetToDefaults() {
            if (confirm('⚠️ Reset all settings to default values?\n\nThis action cannot be undone.')) {
                console.log('🔄 Resetting to default settings...');
                
                // Reset form values to defaults
                if (document.getElementById('backupFrequency')) {
                    document.getElementById('backupFrequency').value = 'daily';
                }
                if (document.getElementById('backupRetention')) {
                    document.getElementById('backupRetention').value = '30';
                }
                
                markAsUnsaved();
                showAlert('🔄 Settings reset to defaults. Click "Save All Changes" to apply.', 'warning');
            }
        }

        // Check system status
        function checkSystemStatus() {
            console.log('🔍 Checking system status...');
            
            setTimeout(() => {
                updateStatusIndicator('dbStatus', 'online', 'Online');
                updateStatusIndicator('emailStatus', 'online', 'Online');
                updateStatusIndicator('storageStatus', 'online', 'Available');
                updateStatusIndicator('backupStatus', 'online', 'Active');
            }, 1000);
        }

        // Update status indicator
        function updateStatusIndicator(elementId, status, text) {
            const element = document.getElementById(elementId);
            if (element) {
                element.className = `status-indicator ${status}`;
                element.innerHTML = `<i class="fas fa-circle"></i> ${text}`;
            }
        }

        // Backup functions
        function createBackup() {
            console.log('💾 Creating system backup...');
            showAlert('🔄 Creating backup... This may take a few minutes.', 'info');
            
            setTimeout(() => {
                showAlert('✅ Backup created successfully!', 'success');
                // Add new backup to the list
                const backupsList = document.getElementById('backupsList');
                const newBackup = document.createElement('div');
                newBackup.className = 'backup-item';
                const now = new Date();
                const dateStr = now.toLocaleDateString('en-US', { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
                
                newBackup.innerHTML = `
                    <div class="backup-info">
                        <div class="backup-date">${dateStr}</div>
                        <div class="backup-size">Database: 12.7 MB | Files: 45.5 MB</div>
                    </div>
                    <div>
                        <button class="btn btn-info btn-sm" onclick="downloadBackup('backup_${now.getTime()}')">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="restoreBackup('backup_${now.getTime()}')">
                            <i class="fas fa-undo"></i>
                        </button>
                    </div>
                `;
                
                back