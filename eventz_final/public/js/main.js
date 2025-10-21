// EVENTZ - Main JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initModals();
    initForms();
    initAlerts();
    initAnimations();
    initLoadingStates();
});

// Modal functionality
function initModals() {
    const modals = document.querySelectorAll('.modal');
    const modalTriggers = document.querySelectorAll('[data-modal]');
    const modalCloses = document.querySelectorAll('.modal-close');
    
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-modal');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
            }
        });
    });
    
    modalCloses.forEach(close => {
        close.addEventListener('click', function() {
            this.closest('.modal').classList.remove('active');
        });
    });
    
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
}

// Form handling
function initForms() {
    // Event action buttons
    initEventActions();
    
    // AJAX form submissions
    const ajaxForms = document.querySelectorAll('[data-ajax-form]');
    
    ajaxForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const action = this.getAttribute('action');
            const method = this.getAttribute('method') || 'POST';
            
            fetch(action, {
                method: method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        showAlert('success', data.message || 'Operation successful');
                        if (data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1000);
                        } else {
                            // Close modal and refresh if no redirect
                            const modal = this.closest('.modal');
                            if (modal) {
                                modal.classList.remove('active');
                            }
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    } else {
                        showAlert('error', data.message || 'Operation failed');
                    }
                } catch (e) {
                    console.error('JSON Parse Error:', e);
                    console.error('Response text:', text);
                    showAlert('error', 'Invalid response from server');
                }
            })
            .catch(error => {
                showAlert('error', 'An error occurred');
                console.error('Error:', error);
            });
        });
    });
}

// Alert system
function initAlerts() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
}

function showAlert(type, message) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    alert.style.opacity = '0';
    
    document.body.insertBefore(alert, document.body.firstChild);
    
    setTimeout(() => {
        alert.style.opacity = '1';
    }, 10);
    
    setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => {
            alert.remove();
        }, 300);
    }, 5000);
}

// Event registration
function registerForEvent(eventId) {
    fetch(window.location.origin + '/eventz_final/participant/register-event', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'event_id=' + eventId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        showAlert('error', 'An error occurred');
        console.error('Error:', error);
    });
}

// Event unregistration
function unregisterFromEvent(eventId) {
    fetch(window.location.origin + '/eventz_final/participant/unregister-event', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'event_id=' + eventId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        showAlert('error', 'An error occurred');
        console.error('Error:', error);
    });
}

// Show event details popup
function showEventDetails(eventId) {
    fetch(window.location.origin + '/eventz_final/participant/event-details?id=' + eventId)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const event = data.event;
            showEventModal(event);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        showAlert('error', 'An error occurred');
        console.error('Error:', error);
    });
}

// Show event details modal
function showEventModal(event) {
    const modal = document.createElement('div');
    modal.className = 'modal active';
    modal.innerHTML = `
        <div class="modal-content event-details-modal">
            <div class="modal-header">
                <h2>${event.title}</h2>
                <button class="modal-close" onclick="this.closest('.modal').remove()">&times;</button>
            </div>
            <div class="event-details-content">
                ${event.banner_url ? `<img src="${window.location.origin}/eventz_final/public${event.banner_url}" alt="${event.title}" class="event-banner-large">` : ''}
                <div class="event-info">
                    <p><strong>Description:</strong> ${event.description || 'No description available'}</p>
                    <div class="event-stats">
                        <div class="stat-item">
                            <i class="fas fa-calendar"></i>
                            <span>${new Date(event.start_at).toLocaleDateString()}</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${event.location_text || event.venue_name || 'Location TBD'}</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-users"></i>
                            <span>${event.registration_count || 0} registered</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-eye"></i>
                            <span>${event.view_count || 0} views</span>
                        </div>
                    </div>
                    <div class="event-actions">
                        ${event.is_registered ? 
                            `<button onclick="unregisterFromEvent(${event.id})" class="btn btn-outline">Unregister</button>` :
                            `<button onclick="registerForEvent(${event.id})" class="btn btn-primary">Register</button>`
                        }
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.remove();
        }
    });
}

// Edit event
function editEvent(eventId) {
    // Fetch event details
    fetch(window.location.origin + '/eventz_final/participant/event-details?id=' + eventId)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const event = data.event;
            
            // Populate edit form
            document.getElementById('edit_event_id').value = event.id;
            document.getElementById('edit_title').value = event.title;
            document.getElementById('edit_description').value = event.description;
            document.getElementById('edit_category_id').value = event.category_ids ? event.category_ids.split(',')[0] : '';
            document.getElementById('edit_event_date').value = event.start_at.split(' ')[0];
            document.getElementById('edit_event_time').value = event.start_at.split(' ')[1];
            document.getElementById('edit_location').value = event.location_text || '';
            document.getElementById('edit_venue').value = event.venue_name || '';
            document.getElementById('edit_capacity').value = event.capacity || '';
            
            // Show edit modal
            document.getElementById('editEventModal').classList.add('active');
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        showAlert('error', 'An error occurred');
        console.error('Error:', error);
    });
}

// Delete event
function deleteEvent(eventId) {
    if (confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
        fetch(window.location.origin + '/eventz_final/organizer/delete-event', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'event_id=' + eventId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            showAlert('error', 'An error occurred');
            console.error('Error:', error);
        });
    }
}

// Follow/Unfollow user
function toggleFollow(userId, isFollowing) {
    const action = isFollowing ? 'unfollow' : 'follow';
    
    fetch(window.location.origin + '/eventz_final/' + action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'user_id=' + userId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        showAlert('error', 'An error occurred');
        console.error('Error:', error);
    });
}


// Approve event (admin)
function approveEvent(eventId) {
    fetch(window.location.origin + '/eventz_final/admin/approve-event', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'event_id=' + eventId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        showAlert('error', 'An error occurred');
        console.error('Error:', error);
    });
}

// Reject event (admin)
function rejectEvent(eventId) {
    if (confirm('Are you sure you want to reject this event?')) {
        fetch(window.location.origin + '/eventz_final/admin/reject-event', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'event_id=' + eventId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            showAlert('error', 'An error occurred');
            console.error('Error:', error);
        });
    }
}

// Delete event (admin)
function deleteEvent(eventId) {
    if (confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
        fetch(window.location.origin + '/eventz_final/admin/delete-event', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'event_id=' + eventId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            showAlert('error', 'An error occurred');
            console.error('Error:', error);
        });
    }
}

// Initialize event action buttons
function initEventActions() {
    // Delete event buttons
    document.querySelectorAll('[data-delete-event]').forEach(button => {
        button.addEventListener('click', function() {
            const eventId = this.getAttribute('data-delete-event');
            deleteEvent(eventId);
        });
    });

    // Edit event buttons
    document.querySelectorAll('[data-edit-event]').forEach(button => {
        button.addEventListener('click', function() {
            const eventId = this.getAttribute('data-edit-event');
            editEvent(eventId);
        });
    });

    // Upload video buttons
    document.querySelectorAll('[data-upload-video]').forEach(button => {
        button.addEventListener('click', function() {
            const eventId = this.getAttribute('data-upload-video');
            uploadVideo(eventId);
        });
    });
}

// Initialize animations
function initAnimations() {
    // Add staggered animation to event cards
    const eventCards = document.querySelectorAll('.event-card');
    eventCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
    
    // Add intersection observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe all animated elements
    document.querySelectorAll('.event-card, .participant-card, .events-section').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
}

// Initialize loading states
function initLoadingStates() {
    // Add loading state to all buttons on click
    document.querySelectorAll('button[type="submit"], .btn').forEach(button => {
        button.addEventListener('click', function(e) {
            // Only add loading state for form submissions and AJAX calls
            if (this.type === 'submit' || this.onclick) {
                this.classList.add('loading');
                this.disabled = true;
                
                // Remove loading state after 3 seconds as fallback
                setTimeout(() => {
                    this.classList.remove('loading');
                    this.disabled = false;
                }, 3000);
            }
        });
    });
}

// Enhanced showAlert with better animations
function showAlert(type, message) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <div class="alert-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    alert.style.opacity = '0';
    alert.style.transform = 'translateX(-100%)';
    
    // Add styles for alert content
    const alertContent = alert.querySelector('.alert-content');
    alertContent.style.display = 'flex';
    alertContent.style.alignItems = 'center';
    alertContent.style.gap = '0.5rem';
    
    document.body.insertBefore(alert, document.body.firstChild);
    
    // Animate in
    setTimeout(() => {
        alert.style.opacity = '1';
        alert.style.transform = 'translateX(0)';
    }, 10);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateX(-100%)';
        setTimeout(() => {
            alert.remove();
        }, 300);
    }, 5000);
}

// Add smooth scrolling for anchor links
function initSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Add keyboard navigation support
function initKeyboardNavigation() {
    document.addEventListener('keydown', function(e) {
        // Close modals with Escape key
        if (e.key === 'Escape') {
            const activeModal = document.querySelector('.modal.active');
            if (activeModal) {
                activeModal.classList.remove('active');
            }
        }
        
        // Submit forms with Ctrl+Enter
        if (e.ctrlKey && e.key === 'Enter') {
            const activeForm = document.querySelector('form:focus-within');
            if (activeForm) {
                const submitBtn = activeForm.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.click();
                }
            }
        }
    });
}

// Initialize all enhancements
document.addEventListener('DOMContentLoaded', function() {
    initSmoothScrolling();
    initKeyboardNavigation();
});
