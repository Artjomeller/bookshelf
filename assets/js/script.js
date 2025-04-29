// BookShelf Application JavaScript

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
    
    // Password strength indicator
    const passwordField = document.getElementById('new_password') || document.getElementById('password');
    const passwordStrengthFeedback = document.getElementById('password-strength-feedback');
    
    if (passwordField && passwordStrengthFeedback) {
        passwordField.addEventListener('input', function() {
            const password = passwordField.value;
            let strength = 0;
            let feedback = '';
            
            // Length check
            if (password.length >= 8) {
                strength += 1;
            }
            
            // Contains lowercase letters
            if (/[a-z]/.test(password)) {
                strength += 1;
            }
            
            // Contains uppercase letters
            if (/[A-Z]/.test(password)) {
                strength += 1;
            }
            
            // Contains numbers
            if (/[0-9]/.test(password)) {
                strength += 1;
            }
            
            // Contains special characters
            if (/[^a-zA-Z0-9]/.test(password)) {
                strength += 1;
            }
            
            // Update feedback based on strength
            if (password.length === 0) {
                feedback = '';
                passwordStrengthFeedback.className = 'form-text';
            } else if (strength < 2) {
                feedback = 'Weak password';
                passwordStrengthFeedback.className = 'form-text text-danger';
            } else if (strength < 4) {
                feedback = 'Medium strength password';
                passwordStrengthFeedback.className = 'form-text text-warning';
            } else {
                feedback = 'Strong password';
                passwordStrengthFeedback.className = 'form-text text-success';
            }
            
            passwordStrengthFeedback.textContent = feedback;
        });
    }
    
    // Confirm delete modals
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
    
    // Form validation for required fields
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    // Due date highlighting
    const dueDates = document.querySelectorAll('.due-date');
    dueDates.forEach(function(element) {
        const dueDate = new Date(element.dataset.date);
        const today = new Date();
        
        // If due date is in the past
        if (dueDate < today) {
            element.classList.add('text-danger', 'fw-bold');
        }
        // If due date is today or tomorrow
        else if ((dueDate - today) / (1000 * 60 * 60 * 24) <= 2) {
            element.classList.add('text-warning', 'fw-bold');
        }
    });
    
    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
});