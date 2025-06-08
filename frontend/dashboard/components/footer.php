<?php
// dashboard/components/footer.php
// Footer Component - Include hii file katika pages zako
?>

<!-- Main Footer Component -->
<footer class="main-footer">
    <!-- Footer Top Section -->
    <div class="footer-top">
        <div class="footer-content">
            <!-- System Information -->
            <div class="footer-section">
                <div class="system-info">
                    <div class="system-logo">
                        <div class="logo-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="system-details">
                            <h2>MSICT</h2>
                            <p>Military School of ICT</p>
                        </div>
                    </div>
                    <div class="system-version">
                        <i class="fas fa-code-branch"></i> Version 1.0.0
                    </div>
                    <div class="last-updated">
                        <i class="fas fa-clock"></i> Last Updated: <?php echo date('F j, Y'); ?>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-section">
                <h3><i class="fas fa-link"></i> Quick Links</h3>
                <ul class="quick-links">
                    <li><a href="../dashboard/admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="../modules/requests/new-request.php"><i class="fas fa-plus-circle"></i> New Request</a></li>
                    <li><a href="../modules/requests/my-requests.php"><i class="fas fa-clipboard-list"></i> My Requests</a></li>
                    <li><a href="../modules/reports/analytics.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                    <li><a href="../help.php"><i class="fas fa-question-circle"></i> Help & Support</a></li>
                    <li><a href="../documentation.php"><i class="fas fa-book"></i> Documentation</a></li>
                </ul>
            </div>

            <!-- Contact Information -->
            <div class="footer-section">
                <h3><i class="fas fa-address-book"></i> Contact Information</h3>
                <div class="contact-info">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <strong>MSICT Headquarters</strong><br>
                            <span>Dar es Salaam, Tanzania</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <strong>Phone:</strong><br>
                            <span>+255-XX-XXX-XXXX</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <strong>Email:</strong><br>
                            <span>support@msict.mil.tz</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support Information -->
            <div class="footer-section">
                <h3><i class="fas fa-headset"></i> Support</h3>
                <div class="support-section">
                    <div class="support-hours">
                        <h4><i class="fas fa-clock"></i> Support Hours</h4>
                        <p><strong>Monday - Friday:</strong> 08:00 - 17:00</p>
                        <p><strong>Saturday:</strong> 08:00 - 13:00</p>
                        <p><strong>Sunday:</strong> Emergency Only</p>
                    </div>
                    
                    <div class="emergency-contact">
                        <h4><i class="fas fa-exclamation-triangle"></i> Emergency Contact</h4>
                        <p><strong>24/7 Emergency:</strong></p>
                        <p>+255-XX-XXX-XXXX (Ext. 911)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="footer-bottom-content">
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> Tanzania People's Defence Forces - Military School of Information and Communication Technology. All rights reserved.</p>
                <p style="margin-top: 0.25rem; font-size: 0.75rem;">Developed by Group 3 - DIT Program</p>
            </div>

            <div class="footer-stats">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <span>150+ Active Users</span>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <span>500+ Requests Processed</span>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <span>99.9% Uptime</span>
                </div>
            </div>

            <div class="system-status">
                <div class="status-indicator">
                    <div class="status-dot online"></div>
                    <span>System Online</span>
                </div>
                <div class="status-indicator">
                    <div class="status-dot online"></div>
                    <span>Database Connected</span>
                </div>
                <div class="status-indicator">
                    <div class="status-dot online"></div>
                    <span>All Services Running</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button class="back-to-top" id="backToTop" onclick="scrollToTop()">
    <i class="fas fa-chevron-up"></i>
</button>

<script>
// Footer JavaScript Functions
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Show/hide back to top button
window.addEventListener('scroll', function() {
    const backToTop = document.getElementById('backToTop');
    if (window.pageYOffset > 300) {
        backToTop.classList.add('visible');
    } else {
        backToTop.classList.remove('visible');
    }
});

// Update system status in real-time (demo)
function updateSystemStatus() {
    const statusDots = document.querySelectorAll('.status-dot');
    
    setInterval(() => {
        statusDots.forEach(dot => {
            // Keep most statuses as online for stability appearance
            const isOnline = Math.random() > 0.1; // 90% chance of staying online
            if (isOnline) {
                dot.className = 'status-dot online';
            } else {
                dot.className = 'status-dot warning';
            }
        });
    }, 30000); // Update every 30 seconds
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSystemStatus();
});

// Emergency contact click handler
document.querySelector('.emergency-contact').addEventListener('click', function() {
    if (confirm('This will initiate emergency contact procedures. Continue?')) {
        alert('Emergency contact protocol initiated. Help is on the way.');
    }
});
</script>