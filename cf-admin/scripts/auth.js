// auth.js - Enhanced authentication handling
let csrfToken = null;

// Initialize CSRF token when page loads
document.addEventListener('DOMContentLoaded', async function() {
    await initializeCSRFToken();
});

async function initializeCSRFToken() {
    try {
        const response = await fetch('../server/scripts/php/auth.php', {
            method: 'GET',
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const data = await response.json();
            csrfToken = data.csrf_token;
        }
    } catch (error) {
        console.error('Failed to initialize CSRF token:', error);
    }
}

// Enhanced login form handler
document.getElementById('loginForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    
    const submitButton = this.querySelector('button[type="submit"]');
    const errorBox = document.getElementById('error');
    
    // Clear previous errors
    errorBox.style.display = 'none';
    
    // Disable submit button to prevent double-submission
    submitButton.disabled = true;
    submitButton.textContent = 'Logging in...';
    
    try {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        
        // Client-side validation
        if (!username || !password) {
            showError('Please enter both username and password');
            return;
        }
        
        if (!csrfToken) {
            await initializeCSRFToken();
        }
        
        const response = await fetch('../api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ 
                username, 
                password,
                csrf_token: csrfToken 
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Update CSRF token for future requests
            if (data.csrf_token) {
                csrfToken = data.csrf_token;
            }
            
            // Show success message briefly before redirect
            showSuccess('Login successful! Redirecting...');
            
            // Redirect after a short delay
            setTimeout(() => {
                window.location.href = "../index.php";
            }, 1000);
        } else {
            showError(data.message || 'Login failed');
        }
        
    } catch (error) {
        console.error('Login error:', error);
        showError('Connection error. Please try again.');
    } finally {
        // Re-enable submit button
        submitButton.disabled = false;
        submitButton.textContent = 'Login';
    }
});

// Enhanced logout function
async function logout() {
    try {
        const response = await fetch('../api/login.php', {
            method: 'DELETE',
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            window.location.href = '../../../view/login.html';
        } else {
            console.error('Logout failed');
            // Force redirect anyway
            window.location.href = '../../../view/login.html';
        }
    } catch (error) {
        console.error('Logout error:', error);
        // Force redirect anyway
        window.location.href = '../../../view/login.html';
    }
}

// Password reset function (placeholder)
function resetPassword() {
    const username = document.getElementById('username').value.trim();
    
    if (!username) {
        showError('Please enter your username first');
        return;
    }
    
    // Implement password reset logic here
    // This could open a modal or redirect to a reset page
    if (confirm(`Send password reset link to the email associated with "${username}"?`)) {
        // Call password reset API
        handlePasswordReset(username);
    }
}

async function handlePasswordReset(username) {
    try {
        const response = await fetch('../api/reset-password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ username })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess('Password reset link sent to your email');
        } else {
            showError(data.message || 'Failed to send reset link');
        }
    } catch (error) {
        console.error('Password reset error:', error);
        showError('Connection error. Please try again.');
    }
}

// Utility functions for showing messages
function showError(message) {
    const errorBox = document.getElementById('error');
    errorBox.textContent = message;
    errorBox.className = 'error-message';
    errorBox.style.display = 'block';
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        errorBox.style.display = 'none';
    }, 5000);
}

function showSuccess(message) {
    const errorBox = document.getElementById('error');
    errorBox.textContent = message;
    errorBox.className = 'success-message';
    errorBox.style.display = 'block';
}

// Session timeout warning
function checkSessionTimeout() {
    // Check if user is logged in and warn about session timeout
    if (document.body.classList.contains('logged-in')) {
        setTimeout(() => {
            if (confirm('Your session is about to expire. Do you want to stay logged in?')) {
                // Refresh session by making a simple request
                fetch('../api/session-refresh.php', {
                    method: 'POST',
                    credentials: 'same-origin'
                });
            } else {
                logout();
            }
        }, 50 * 60 * 1000); // 50 minutes (10 minutes before 1-hour timeout)
    }
}

// Initialize session timeout check
document.addEventListener('DOMContentLoaded', checkSessionTimeout);

// Prevent form submission on Enter key in password reset
document.addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && e.target.type !== 'submit') {
        e.preventDefault();
    }
});