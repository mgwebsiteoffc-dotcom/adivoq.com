// ===== Global Variables =====
let formData = {
    name: '',
    email: '',
    phone: '',
    creatorType: '',
    followers: '',
    monthlyInvoices: ''
};

// ===== Navbar Scroll Effect =====
window.addEventListener('scroll', function() {
    const navbar = document.getElementById('navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// ===== Mobile Menu =====
document.getElementById('mobileMenuBtn').addEventListener('click', function() {
    document.getElementById('mobileMenu').classList.add('active');
    document.body.style.overflow = 'hidden';
});

function closeMobileMenu() {
    document.getElementById('mobileMenu').classList.remove('active');
    document.body.style.overflow = '';
}

// ===== FAQ Accordion =====
document.querySelectorAll('.faq-question').forEach(function(question) {
    question.addEventListener('click', function() {
        const item = this.parentElement;
        const isActive = item.classList.contains('active');
        
        // Close all items
        document.querySelectorAll('.faq-item').forEach(function(faq) {
            faq.classList.remove('active');
        });
        
        // Open clicked item if it wasn't active
        if (!isActive) {
            item.classList.add('active');
        }
        
        // Reinitialize icons
        lucide.createIcons();
    });
});

// ===== Waitlist Modal =====
function openWaitlistModal() {
    document.getElementById('waitlistModal').classList.add('active');
    document.body.style.overflow = 'hidden';
    lucide.createIcons();
}

function closeWaitlistModal() {
    document.getElementById('waitlistModal').classList.remove('active');
    document.body.style.overflow = '';
    
    // Reset form after close
    setTimeout(function() {
        resetWaitlistForm();
    }, 300);
}

function resetWaitlistForm() {
    document.getElementById('step1').classList.remove('hidden');
    document.getElementById('step2').classList.add('hidden');
    document.getElementById('successStep').classList.add('hidden');
    
    // Reset form data
    formData = {
        name: '',
        email: '',
        phone: '',
        creatorType: '',
        followers: '',
        monthlyInvoices: ''
    };
    
    // Clear inputs
    document.querySelectorAll('.modal-form input').forEach(function(input) {
        input.value = '';
    });
    
    // Clear selections
    document.querySelectorAll('.option-btn, .pill-btn').forEach(function(btn) {
        btn.classList.remove('selected');
    });
}

function goToStep2() {
    const name = document.querySelector('input[name="name"]').value.trim();
    const email = document.querySelector('input[name="email"]').value.trim();
    const phone = document.querySelector('input[name="phone"]').value.trim();
    
    // Validation
    if (!name) {
        showToast('Please enter your name', 'error');
        return;
    }
    
    if (!email || !isValidEmail(email)) {
        showToast('Please enter a valid email', 'error');
        return;
    }
    
    if (!phone || phone.length < 10) {
        showToast('Please enter a valid phone number', 'error');
        return;
    }
    
    // Store data
    formData.name = name;
    formData.email = email;
    formData.phone = phone;
    
    // Switch to step 2
    document.getElementById('step1').classList.add('hidden');
    document.getElementById('step2').classList.remove('hidden');
    lucide.createIcons();
}

function goToStep1() {
    document.getElementById('step2').classList.add('hidden');
    document.getElementById('step1').classList.remove('hidden');
    lucide.createIcons();
}

function selectOption(btn, field) {
    // Remove selection from siblings
    btn.parentElement.querySelectorAll('.option-btn').forEach(function(b) {
        b.classList.remove('selected');
    });
    
    // Add selection
    btn.classList.add('selected');
    formData[field] = btn.dataset.value;
}

function selectPill(btn, field) {
    // Remove selection from siblings in same group
    btn.parentElement.querySelectorAll('.pill-btn').forEach(function(b) {
        b.classList.remove('selected');
    });
    
    // Add selection
    btn.classList.add('selected');
    
    if (field === 'followers') {
        formData.followers = btn.textContent;
    } else if (field === 'invoices') {
        formData.monthlyInvoices = btn.textContent;
    }
}

function submitWaitlist() {
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoader = submitBtn.querySelector('.btn-loader');
    
    // Show loading
    btnText.classList.add('hidden');
    btnLoader.classList.remove('hidden');
    submitBtn.disabled = true;
    lucide.createIcons();
    
    // Prepare data
    const data = {
        name: formData.name,
        email: formData.email,
        phone: formData.phone,
        creatorType: formData.creatorType || 'Not specified',
        followers: formData.followers || 'Not specified',
        monthlyInvoices: formData.monthlyInvoices || 'Not specified',
        source: 'waitlist_modal'
    };
    
    // Submit to API
    fetch('process_waitlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(result) {
        // Hide loading
        btnText.classList.remove('hidden');
        btnLoader.classList.add('hidden');
        submitBtn.disabled = false;
        
        if (result.success) {
            // Show success
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('successStep').classList.remove('hidden');
            document.getElementById('waitlistPosition').textContent = '#' + result.data.position;
            lucide.createIcons();
            
            showToast('Successfully joined the waitlist!', 'success');
        } else {
            showToast(result.message || 'Something went wrong', 'error');
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        
        // Hide loading
        btnText.classList.remove('hidden');
        btnLoader.classList.add('hidden');
        submitBtn.disabled = false;
        
        showToast('Network error. Please try again.', 'error');
    });
}

function shareTwitter() {
    const text = encodeURIComponent("I just joined the CreatorPay waitlist! 🚀 The future of creator finance is here. Join me: ");
    const url = encodeURIComponent(window.location.href);
    window.open('https://twitter.com/intent/tweet?text=' + text + url, '_blank');
}

function shareWhatsApp() {
    const text = encodeURIComponent("I just joined the CreatorPay waitlist! 🚀 The future of creator finance is here. Join me: " + window.location.href);
    window.open('https://wa.me/?text=' + text, '_blank');
}

// ===== WhatsApp Popup =====
let whatsappShown = false;

// Show WhatsApp popup after 5 seconds
setTimeout(function() {
    if (!whatsappShown) {
        document.getElementById('whatsappPopup').style.display = 'block';
        whatsappShown = true;
    }
}, 5000);

function closeWhatsAppPopup() {
    document.getElementById('whatsappPopup').style.display = 'none';
}

function toggleWhatsAppChat() {
    const chat = document.getElementById('whatsappChat');
    const notificationBadge = document.querySelector('.notification-badge');
    
    if (chat.classList.contains('hidden')) {
        chat.classList.remove('hidden');
        if (notificationBadge) {
            notificationBadge.style.display = 'none';
        }
    } else {
        chat.classList.add('hidden');
    }
    
    lucide.createIcons();
}

function sendWhatsApp(message) {
    const phoneNumber = '919876543210'; // Replace with your actual number
    const encodedMessage = encodeURIComponent(message);
    window.open('https://wa.me/' + phoneNumber + '?text=' + encodedMessage, '_blank');
}

function sendCustomWhatsApp() {
    const input = document.getElementById('whatsappMessage');
    const message = input.value.trim();
    
    if (message) {
        sendWhatsApp(message);
        input.value = '';
    }
}

function handleWhatsAppEnter(event) {
    if (event.key === 'Enter') {
        sendCustomWhatsApp();
    }
}

// ===== Toast Notifications =====
function showToast(message, type) {
    const container = document.getElementById('toastContainer');
    
    const toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.innerHTML = `
        <div class="toast-icon">
            <i data-lucide="${type === 'success' ? 'check' : 'x'}"></i>
        </div>
        <span class="toast-message">${message}</span>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <i data-lucide="x"></i>
        </button>
    `;
    
    container.appendChild(toast);
    lucide.createIcons();
    
    // Auto remove after 4 seconds
    setTimeout(function() {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 4000);
}

// ===== Utility Functions =====
function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// ===== Close Modal on Overlay Click =====
document.getElementById('waitlistModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeWaitlistModal();
    }
});

// ===== Close Modal on Escape Key =====
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeWaitlistModal();
        closeMobileMenu();
    }
});

// ===== Smooth Scroll for Anchor Links =====
document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            const offset = 80; // Navbar height
            const position = target.getBoundingClientRect().top + window.pageYOffset - offset;
            window.scrollTo({
                top: position,
                behavior: 'smooth'
            });
        }
    });
});

// ===== Initialize =====
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});