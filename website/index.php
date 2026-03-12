<?php
// Get waitlist count for social proof
$waitlistFile = 'data/waitlist.json';
$waitlistCount = 0;
if (file_exists($waitlistFile)) {
    $data = json_decode(file_get_contents($waitlistFile), true);
    $waitlistCount = count($data['entries'] ?? []);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdivoQ - Financial OS for Creators | India & UAE</title>
    <meta name="description" content="The complete financial management platform for creators, influencers & creative entrepreneurs. Track revenue, manage brand deals, automate payments.">
    <meta name="keywords" content="creator finance, influencer payments, brand deal management, invoice for creators, GST for influencers">
    
    <!-- Open Graph -->
    <meta property="og:title" content="AdivoQ - Financial OS for Creators">
    <meta property="og:description" content="From brand deal to bank — simplified.">
    <meta property="og:type" content="website">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚡</text></svg>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Background Orbs -->
    <div class="bg-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- Navbar -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <a href="#" class="logo">
                <div class="logo-icon">
                    <i data-lucide="zap"></i>
                </div>
                <span>Adivo<span class="text-green">Q</span></span>
            </a>
            
            <div class="nav-links" id="navLinks">
                <a href="#features">Features</a>
                <a href="#how-it-works">How It Works</a>
                <a href="#pricing">Pricing</a>
                <a href="#faq">FAQ</a>
            </div>
            
            <button class="btn btn-primary nav-cta" onclick="openWaitlistModal()">
                Join Waitlist <span class="badge">Free</span>
            </button>
            
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i data-lucide="menu"></i>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <button class="mobile-menu-close" onclick="closeMobileMenu()">
            <i data-lucide="x"></i>
        </button>
        <div class="mobile-menu-links">
            <a href="#features" onclick="closeMobileMenu()">Features</a>
            <a href="#how-it-works" onclick="closeMobileMenu()">How It Works</a>
            <a href="#pricing" onclick="closeMobileMenu()">Pricing</a>
            <a href="#faq" onclick="closeMobileMenu()">FAQ</a>
            <button class="btn btn-primary" onclick="closeMobileMenu(); openWaitlistModal();">
                Join Waitlist – It's Free
            </button>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-content" data-aos="fade-right">
                    <!-- Badge -->
                    <div class="hero-badge">
                        <span class="pulse-dot"></span>
                        <span>Now accepting early access signups</span>
                    </div>
                    
                    <!-- Headline -->
                    <h1 class="hero-title">
                        Run Your Creator Business Like a <span class="gradient-text">Company</span>
                    </h1>
                    
                    <!-- Subheadline -->
                    <p class="hero-subtitle">
                        The complete financial OS for creators, influencers & creative entrepreneurs. 
                        From brand deal to bank — simplified.
                    </p>
                    
                    <!-- Feature Pills -->
                    <div class="feature-pills">
                        <span class="pill"><i data-lucide="check-circle-2"></i> Brand deal tracking</span>
                        <span class="pill"><i data-lucide="check-circle-2"></i> Smart invoicing</span>
                        <span class="pill"><i data-lucide="check-circle-2"></i> Tax-ready reports</span>
                        <span class="pill"><i data-lucide="check-circle-2"></i> Payment automation</span>
                    </div>
                    
                    <!-- CTA Buttons -->
                    <div class="hero-cta">
                        <button class="btn btn-primary btn-lg" onclick="openWaitlistModal()">
                            Join Waitlist Free <i data-lucide="arrow-right"></i>
                        </button>
                        <a href="#how-it-works" class="btn btn-secondary btn-lg">
                            <i data-lucide="play"></i> See How It Works
                        </a>
                    </div>
                    
                    <!-- Social Proof -->
                    <div class="social-proof">
                        <div class="avatar-stack">
                            <div class="avatar">A</div>
                            <div class="avatar">B</div>
                            <div class="avatar">C</div>
                            <div class="avatar">D</div>
                            <div class="avatar">E</div>
                        </div>
                        <div class="proof-text">
                            <div class="stars">
                                <i data-lucide="star" class="star-filled"></i>
                                <i data-lucide="star" class="star-filled"></i>
                                <i data-lucide="star" class="star-filled"></i>
                                <i data-lucide="star" class="star-filled"></i>
                                <i data-lucide="star" class="star-filled"></i>
                            </div>
                            <p><strong><?php echo max(500, $waitlistCount); ?>+</strong> creators already signed up</p>
                        </div>
                    </div>
                </div>
                
                <!-- Hero Dashboard Preview -->
                <div class="hero-visual" data-aos="fade-left" data-aos-delay="200">
                    <div class="dashboard-preview">
                        <!-- Dashboard Header -->
                        <div class="dashboard-header">
                            <div>
                                <h3>Revenue Dashboard</h3>
                                <p>March 2024</p>
                            </div>
                            <div class="trend-badge">
                                <i data-lucide="trending-up"></i>
                                <span>+23.5%</span>
                            </div>
                        </div>
                        
                        <!-- Revenue Card -->
                        <div class="revenue-card">
                            <p class="label">Total Revenue</p>
                            <p class="amount">₹4,85,230</p>
                            <div class="revenue-breakdown">
                                <span><span class="dot green"></span> Received: ₹3,20,000</span>
                                <span><span class="dot yellow"></span> Pending: ₹1,65,230</span>
                            </div>
                        </div>
                        
                        <!-- Recent Payments -->
                        <div class="recent-payments">
                            <p class="section-label">Recent Payments</p>
                            <div class="payment-item">
                                <div class="payment-icon"><i data-lucide="credit-card"></i></div>
                                <div class="payment-info">
                                    <p class="brand">Nike India</p>
                                    <p class="amount">₹75,000</p>
                                </div>
                                <span class="status paid">Paid</span>
                            </div>
                            <div class="payment-item">
                                <div class="payment-icon"><i data-lucide="credit-card"></i></div>
                                <div class="payment-info">
                                    <p class="brand">Myntra</p>
                                    <p class="amount">₹45,000</p>
                                </div>
                                <span class="status pending">Pending</span>
                            </div>
                            <div class="payment-item">
                                <div class="payment-icon"><i data-lucide="credit-card"></i></div>
                                <div class="payment-info">
                                    <p class="brand">Boat Audio</p>
                                    <p class="amount">₹25,000</p>
                                </div>
                                <span class="status paid">Paid</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating Cards -->
                    <div class="floating-card invoices">
                        <div class="floating-icon purple">
                            <i data-lucide="file-text"></i>
                        </div>
                        <div>
                            <p class="number">127</p>
                            <p class="label">Invoices Sent</p>
                        </div>
                    </div>
                    
                    <div class="floating-card gst">
                        <span class="flag">🇮🇳</span>
                        <div>
                            <p class="title">GST Ready</p>
                            <p class="subtitle">Auto-calculated</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="stats-grid" data-aos="fade-up">
                <div class="stat-item">
                    <p class="stat-number">₹50Cr+</p>
                    <p class="stat-label">Creator Revenue Tracked</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number">10,000+</p>
                    <p class="stat-label">Invoices Generated</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number"><?php echo max(500, $waitlistCount); ?>+</p>
                    <p class="stat-label">Early Access Signups</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-tag">Features</span>
                <h2 class="section-title">
                    Everything You Need to <span class="gradient-text">Manage Your Money</span>
                </h2>
                <p class="section-subtitle">
                    Stop juggling Excel sheets, WhatsApp threads, and random invoice apps. 
                    Get one platform designed specifically for how creators work.
                </p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card" data-aos="fade-up" data-aos-delay="0">
                    <div class="feature-icon green">
                        <i data-lucide="bar-chart-3"></i>
                    </div>
                    <h3>Brand Deal Manager</h3>
                    <p>Track campaigns, milestones, advances, and payment status. Never lose track of a deal again.</p>
                </div>
                
                <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon blue">
                        <i data-lucide="file-text"></i>
                    </div>
                    <h3>Smart Invoice Engine</h3>
                    <p>Create professional invoices in seconds. Support for milestones, multi-currency, GST/VAT.</p>
                </div>
                
                <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon purple">
                        <i data-lucide="wallet"></i>
                    </div>
                    <h3>Revenue Dashboard</h3>
                    <p>Visual breakdown of earnings, pending payments, and top clients. All in one glance.</p>
                </div>
                
                <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon orange">
                        <i data-lucide="calculator"></i>
                    </div>
                    <h3>Tax Estimator</h3>
                    <p>Automatic GST calculation, TDS tracking, and tax reserve suggestions for India & UAE.</p>
                </div>
                
                <div class="feature-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-icon pink">
                        <i data-lucide="users"></i>
                    </div>
                    <h3>Team & Commission Split</h3>
                    <p>Manage agency cuts, manager fees, and team payouts automatically.</p>
                </div>
                
                <div class="feature-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-icon yellow">
                        <i data-lucide="brain"></i>
                    </div>
                    <h3>AI-Powered Insights</h3>
                    <p>Smart payment reminders, income forecasts, and late payer risk scoring.</p>
                </div>
            </div>
            
            <!-- Additional Features -->
            <div class="additional-features" data-aos="fade-up">
                <div class="additional-feature">
                    <i data-lucide="globe"></i>
                    <span>Multi-Currency Support</span>
                </div>
                <div class="additional-feature">
                    <i data-lucide="bell"></i>
                    <span>WhatsApp Notifications</span>
                </div>
                <div class="additional-feature">
                    <i data-lucide="shield"></i>
                    <span>Bank-Level Security</span>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works" id="how-it-works">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-tag">How It Works</span>
                <h2 class="section-title">
                    From Chaos to <span class="gradient-text">Clarity</span>
                </h2>
                <p class="section-subtitle">
                    Get started in minutes, not days. Our simple 4-step process gets you 
                    organized and in control of your creator finances.
                </p>
            </div>
            
            <div class="steps-grid">
                <div class="step-card" data-aos="fade-up" data-aos-delay="0">
                    <div class="step-number">01</div>
                    <div class="step-icon">
                        <i data-lucide="user-plus"></i>
                    </div>
                    <h3>Sign Up in Seconds</h3>
                    <p>Create your account with just your email or phone. No complex setup required.</p>
                </div>
                
                <div class="step-arrow">
                    <i data-lucide="arrow-right"></i>
                </div>
                
                <div class="step-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="step-number">02</div>
                    <div class="step-icon">
                        <i data-lucide="link-2"></i>
                    </div>
                    <h3>Connect Your Deals</h3>
                    <p>Add your brand collaborations, set milestones, and track payment schedules.</p>
                </div>
                
                <div class="step-arrow">
                    <i data-lucide="arrow-right"></i>
                </div>
                
                <div class="step-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="step-number">03</div>
                    <div class="step-icon">
                        <i data-lucide="file-spreadsheet"></i>
                    </div>
                    <h3>Generate Invoices</h3>
                    <p>Create professional invoices with GST/VAT and send via WhatsApp or email.</p>
                </div>
                
                <div class="step-arrow">
                    <i data-lucide="arrow-right"></i>
                </div>
                
                <div class="step-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="step-number">04</div>
                    <div class="step-icon">
                        <i data-lucide="trending-up"></i>
                    </div>
                    <h3>Track & Grow</h3>
                    <p>Monitor your revenue, get payment reminders, and plan taxes confidently.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing" id="pricing">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-tag">Pricing</span>
                <h2 class="section-title">
                    Simple, Transparent <span class="gradient-text">Pricing</span>
                </h2>
                <p class="section-subtitle">
                    Start free, upgrade when you're ready. No hidden fees, no surprises.
                </p>
            </div>
            
            <div class="pricing-grid">
                <!-- Free Plan -->
                <div class="pricing-card" data-aos="fade-up" data-aos-delay="0">
                    <div class="pricing-icon">
                        <i data-lucide="zap"></i>
                    </div>
                    <h3 class="pricing-name">Free</h3>
                    <p class="pricing-desc">Perfect for getting started</p>
                    <div class="pricing-price">
                        <span class="amount">₹0</span>
                        <span class="period">forever</span>
                    </div>
                    <ul class="pricing-features">
                        <li><i data-lucide="check"></i> 5 invoices per month</li>
                        <li><i data-lucide="check"></i> Basic revenue dashboard</li>
                        <li><i data-lucide="check"></i> Single currency</li>
                        <li><i data-lucide="check"></i> Email support</li>
                        <li><i data-lucide="check"></i> Mobile app access</li>
                    </ul>
                    <button class="btn btn-outline" onclick="openWaitlistModal()">Start Free</button>
                </div>
                
                <!-- Pro Plan -->
                <div class="pricing-card popular" data-aos="fade-up" data-aos-delay="100">
                    <div class="popular-badge">Most Popular</div>
                    <div class="pricing-icon">
                        <i data-lucide="crown"></i>
                    </div>
                    <h3 class="pricing-name">Creator Pro</h3>
                    <p class="pricing-desc">For serious creators</p>
                    <div class="pricing-price">
                        <span class="amount">₹999</span>
                        <span class="period">/month</span>
                    </div>
                    <ul class="pricing-features">
                        <li><i data-lucide="check"></i> Unlimited invoices</li>
                        <li><i data-lucide="check"></i> Milestone tracking</li>
                        <li><i data-lucide="check"></i> Multi-currency support</li>
                        <li><i data-lucide="check"></i> GST/VAT automation</li>
                        <li><i data-lucide="check"></i> Brand deal manager</li>
                        <li><i data-lucide="check"></i> Payment reminders</li>
                        <li><i data-lucide="check"></i> Tax estimates</li>
                        <li><i data-lucide="check"></i> WhatsApp integration</li>
                        <li><i data-lucide="check"></i> Priority support</li>
                    </ul>
                    <button class="btn btn-primary" onclick="openWaitlistModal()">Join Waitlist</button>
                </div>
                
                <!-- Agency Plan -->
                <div class="pricing-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="pricing-icon">
                        <i data-lucide="building-2"></i>
                    </div>
                    <h3 class="pricing-name">Agency</h3>
                    <p class="pricing-desc">For talent agencies & teams</p>
                    <div class="pricing-price">
                        <span class="amount">₹10,000</span>
                        <span class="period">/month</span>
                    </div>
                    <ul class="pricing-features">
                        <li><i data-lucide="check"></i> Everything in Pro</li>
                        <li><i data-lucide="check"></i> Up to 50 creators</li>
                        <li><i data-lucide="check"></i> Team management</li>
                        <li><i data-lucide="check"></i> Commission splits</li>
                        <li><i data-lucide="check"></i> White-label option</li>
                        <li><i data-lucide="check"></i> API access</li>
                        <li><i data-lucide="check"></i> Custom branding</li>
                        <li><i data-lucide="check"></i> Dedicated support</li>
                        <li><i data-lucide="check"></i> Analytics & reports</li>
                    </ul>
                    <button class="btn btn-outline" onclick="openWaitlistModal()">Contact Sales</button>
                </div>
            </div>
            
            <p class="pricing-note" data-aos="fade-up">
                🔒 14-day money-back guarantee • No credit card required for free plan
            </p>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-tag">Testimonials</span>
                <h2 class="section-title">
                    Loved by <span class="gradient-text">Creators</span>
                </h2>
                <p class="section-subtitle">
                    Join hundreds of creators who've transformed their financial management
                </p>
            </div>
            
            <div class="testimonials-grid">
                <div class="testimonial-card" data-aos="fade-up" data-aos-delay="0">
                    <div class="quote-icon">
                        <i data-lucide="quote"></i>
                    </div>
                    <p class="testimonial-text">
                        "Finally! A platform that understands how creators work. I used to lose track of payments all the time. Now everything is in one place."
                    </p>
                    <div class="testimonial-stars">
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">👩‍🦰</div>
                        <div class="author-info">
                            <p class="author-name">Priya Sharma</p>
                            <p class="author-role">Fashion Influencer</p>
                            <p class="author-followers">450K followers</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="quote-icon">
                        <i data-lucide="quote"></i>
                    </div>
                    <p class="testimonial-text">
                        "The GST automation alone saves me hours every month. My accountant loves the tax-ready reports. Game changer!"
                    </p>
                    <div class="testimonial-stars">
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">👨‍💻</div>
                        <div class="author-info">
                            <p class="author-name">Rahul Mehta</p>
                            <p class="author-role">Tech YouTuber</p>
                            <p class="author-followers">280K subscribers</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="quote-icon">
                        <i data-lucide="quote"></i>
                    </div>
                    <p class="testimonial-text">
                        "I was using 5 different apps before AdivoQ. Now I have everything - invoices, payments, taxes - all in one beautiful dashboard."
                    </p>
                    <div class="testimonial-stars">
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">👩‍🎤</div>
                        <div class="author-info">
                            <p class="author-name">Ayesha Khan</p>
                            <p class="author-role">Lifestyle Creator</p>
                            <p class="author-followers">180K followers</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="quote-icon">
                        <i data-lucide="quote"></i>
                    </div>
                    <p class="testimonial-text">
                        "As an agency, tracking commissions was a nightmare. The team split feature is exactly what we needed. Highly recommend!"
                    </p>
                    <div class="testimonial-stars">
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">👨‍💼</div>
                        <div class="author-info">
                            <p class="author-name">Vikram Singh</p>
                            <p class="author-role">Talent Manager</p>
                            <p class="author-followers">Managing 25 creators</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="quote-icon">
                        <i data-lucide="quote"></i>
                    </div>
                    <p class="testimonial-text">
                        "The WhatsApp invoice feature is brilliant! My clients pay faster now because I can send reminders right where they're active."
                    </p>
                    <div class="testimonial-stars">
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">👩‍🎨</div>
                        <div class="author-info">
                            <p class="author-name">Sneha Patel</p>
                            <p class="author-role">Freelance Designer</p>
                            <p class="author-followers">50K followers</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="quote-icon">
                        <i data-lucide="quote"></i>
                    </div>
                    <p class="testimonial-text">
                        "Finally a platform that supports VAT for the UAE market. Multi-currency invoicing makes working with international brands seamless."
                    </p>
                    <div class="testimonial-stars">
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                        <i data-lucide="star" class="star-filled"></i>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">👨</div>
                        <div class="author-info">
                            <p class="author-name">Ahmed Al-Hassan</p>
                            <p class="author-role">UAE Content Creator</p>
                            <p class="author-followers">320K followers</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq" id="faq">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-tag">FAQ</span>
                <h2 class="section-title">
                    Frequently Asked <span class="gradient-text">Questions</span>
                </h2>
                <p class="section-subtitle">
                    Everything you need to know about AdivoQ
                </p>
            </div>
            
            <div class="faq-list" data-aos="fade-up">
                <div class="faq-item active">
                    <button class="faq-question">
                        <span>Who is AdivoQ designed for?</span>
                        <i data-lucide="chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>AdivoQ is built for influencers, content creators, freelancers, and creative entrepreneurs who earn through brand deals, sponsorships, and creative services. Whether you have 10K or 500K followers, if you manage multiple income streams and need better financial organization, AdivoQ is for you.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        <span>How is AdivoQ different from regular accounting software?</span>
                        <i data-lucide="chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Traditional accounting software like Zoho or QuickBooks are designed for traditional businesses. AdivoQ is built specifically for the creator economy - with features like brand deal tracking, milestone-based invoicing, WhatsApp integration, and creator-specific tax calculations that regular software doesn't offer.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Does AdivoQ handle GST and VAT?</span>
                        <i data-lucide="chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Yes! AdivoQ automatically calculates GST for Indian creators and VAT for UAE-based creators. It also tracks TDS deductions and provides tax-ready reports that your accountant will love.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Can I send invoices via WhatsApp?</span>
                        <i data-lucide="chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Absolutely! We know creators and their clients live on WhatsApp. You can generate professional invoices and send them directly via WhatsApp with just one tap. Payment reminders can also be sent through WhatsApp.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        <span>What payment gateways do you support?</span>
                        <i data-lucide="chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>We integrate with Razorpay, Stripe, and PayPal. This means you can accept payments in multiple currencies and offer your clients their preferred payment method.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Is there a free plan available?</span>
                        <i data-lucide="chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Yes! Our free plan includes 5 invoices per month, basic dashboard access, and core features. It's perfect for creators just starting out. You can upgrade anytime as your business grows.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Can talent agencies use AdivoQ?</span>
                        <i data-lucide="chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Definitely! Our Agency plan is designed for talent managers handling multiple creators. It includes commission split calculations, team management, white-label options, and API access for integration with your existing systems.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">
                        <span>How secure is my financial data?</span>
                        <i data-lucide="chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>We use bank-level encryption (AES-256) and are hosted on secure cloud infrastructure. Your data is backed up daily, and we never share your financial information with third parties.</p>
                    </div>
                </div>
            </div>
            
            <div class="faq-contact" data-aos="fade-up">
                <p>Still have questions?</p>
                <a href="https://wa.me/919876543210" target="_blank" class="whatsapp-link">
                    <i data-lucide="message-circle"></i>
                    <span>Chat with us on WhatsApp</span>
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-box" data-aos="fade-up">
                <h2>Ready to Transform Your <span class="gradient-text">Creator Finances?</span></h2>
                <p>Join <?php echo max(500, $waitlistCount); ?>+ creators who've already signed up for early access. Be the first to experience the future of creator finance.</p>
                <button class="btn btn-primary btn-lg" onclick="openWaitlistModal()">
                    Join Waitlist — It's Free <i data-lucide="arrow-right"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="#" class="logo">
                        <div class="logo-icon">
                            <i data-lucide="zap"></i>
                        </div>
                        <span>Adivo<span class="text-green">Q</span></span>
                    </a>
                    <p>The complete financial OS for creators, influencers & creative entrepreneurs.</p>
                    
                    <div class="footer-contact">
                        <a href="mailto:hello@AdivoQ.in">
                            <i data-lucide="mail"></i>
                            <span>hello@AdivoQ.in</span>
                        </a>
                        <p>
                            <i data-lucide="map-pin"></i>
                            <span>Mumbai, India 🇮🇳</span>
                        </p>
                    </div>
                    
                    <div class="social-links">
                        <a href="#" class="social-link"><i data-lucide="twitter"></i></a>
                        <a href="#" class="social-link"><i data-lucide="instagram"></i></a>
                        <a href="#" class="social-link"><i data-lucide="linkedin"></i></a>
                        <a href="#" class="social-link"><i data-lucide="youtube"></i></a>
                    </div>
                </div>
                
                <div class="footer-links">
                    <h4>Product</h4>
                    <ul>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#pricing">Pricing</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#">Roadmap</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Creator Guides</a></li>
                        <li><a href="#">Tax Calculator</a></li>
                        <li><a href="#">Invoice Templates</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Press Kit</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Refund Policy</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>© 2026 AdivoQ. All rights reserved.</p>
                <p>Made with ❤️ for creators in India & UAE 🇮🇳 🇦🇪</p>
            </div>
        </div>
    </footer>

    <!-- Waitlist Modal -->
    <div class="modal-overlay" id="waitlistModal">
        <div class="modal">
            <button class="modal-close" onclick="closeWaitlistModal()">
                <i data-lucide="x"></i>
            </button>
            
            <!-- Step 1 -->
            <div class="modal-step" id="step1">
                <div class="modal-header">
                    <div class="modal-badge">
                        <i data-lucide="sparkles"></i>
                        <span>Free Early Access</span>
                    </div>
                    <h2>Join the Waitlist</h2>
                    <p>Be among the first to experience AdivoQ</p>
                    <div class="progress-bar">
                        <div class="progress" style="width: 50%"></div>
                    </div>
                </div>
                
                <form id="waitlistForm" class="modal-form">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name" placeholder="Enter your name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" placeholder="you@example.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label>WhatsApp Number *</label>
                        <div class="input-group">
                            <span class="input-prefix">+91</span>
                            <input type="tel" name="phone" placeholder="9876543210" required>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-primary btn-block" onclick="goToStep2()">
                        Continue <i data-lucide="arrow-right"></i>
                    </button>
                </form>
            </div>
            
            <!-- Step 2 -->
            <div class="modal-step hidden" id="step2">
                <div class="modal-header">
                    <div class="modal-badge">
                        <i data-lucide="sparkles"></i>
                        <span>Free Early Access</span>
                    </div>
                    <h2>Tell Us More</h2>
                    <p>Help us personalize your experience</p>
                    <div class="progress-bar">
                        <div class="progress" style="width: 100%"></div>
                    </div>
                </div>
                
                <div class="modal-form">
                    <div class="form-group">
                        <label>What best describes you?</label>
                        <div class="option-grid">
                            <button type="button" class="option-btn" data-value="influencer" onclick="selectOption(this, 'creatorType')">
                                <span class="option-emoji">📸</span>
                                <span class="option-label">Influencer</span>
                                <span class="option-desc">Instagram, YouTube, etc.</span>
                            </button>
                            <button type="button" class="option-btn" data-value="freelancer" onclick="selectOption(this, 'creatorType')">
                                <span class="option-emoji">💼</span>
                                <span class="option-label">Freelancer</span>
                                <span class="option-desc">Designer, Editor, etc.</span>
                            </button>
                            <button type="button" class="option-btn" data-value="agency" onclick="selectOption(this, 'creatorType')">
                                <span class="option-emoji">🏢</span>
                                <span class="option-label">Agency</span>
                                <span class="option-desc">Managing creators</span>
                            </button>
                            <button type="button" class="option-btn" data-value="other" onclick="selectOption(this, 'creatorType')">
                                <span class="option-emoji">✨</span>
                                <span class="option-label">Other</span>
                                <span class="option-desc">Something else</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Follower Count</label>
                        <div class="pill-group">
                            <button type="button" class="pill-btn" onclick="selectPill(this, 'followers')">1K - 10K</button>
                            <button type="button" class="pill-btn" onclick="selectPill(this, 'followers')">10K - 50K</button>
                            <button type="button" class="pill-btn" onclick="selectPill(this, 'followers')">50K - 100K</button>
                            <button type="button" class="pill-btn" onclick="selectPill(this, 'followers')">100K - 500K</button>
                            <button type="button" class="pill-btn" onclick="selectPill(this, 'followers')">500K+</button>
                            <button type="button" class="pill-btn" onclick="selectPill(this, 'followers')">N/A</button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Monthly Invoices</label>
                        <div class="pill-group">
                            <button type="button" class="pill-btn" onclick="selectPill(this, 'invoices')">1 - 5</button>
                            <button type="button" class="pill-btn" onclick="selectPill(this, 'invoices')">5 - 15</button>
                            <button type="button" class="pill-btn" onclick="selectPill(this, 'invoices')">15 - 30</button>
                            <button type="button" class="pill-btn" onclick="selectPill(this, 'invoices')">30+</button>
                        </div>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="button" class="btn btn-outline" onclick="goToStep1()">Back</button>
                        <button type="button" class="btn btn-primary" onclick="submitWaitlist()" id="submitBtn">
                            <span class="btn-text">Join Waitlist</span>
                            <span class="btn-loader hidden">
                                <i data-lucide="loader-2" class="spin"></i>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Success -->
            <div class="modal-step hidden" id="successStep">
                <div class="success-content">
                    <div class="success-icon">
                        <i data-lucide="check-circle-2"></i>
                    </div>
                    <h2>You're In! 🎉</h2>
                    <p>Welcome to the AdivoQ family!</p>
                    
                    <div class="position-box">
                        <p class="label">Your waitlist position</p>
                        <p class="position" id="waitlistPosition">#1</p>
                    </div>
                    
                    <p class="success-note">We'll notify you via email & WhatsApp when we launch. Share with friends to move up the list!</p>
                    
                    <div class="share-buttons">
                        <button class="btn btn-outline" onclick="shareTwitter()">Share on Twitter</button>
                        <button class="btn btn-whatsapp" onclick="shareWhatsApp()">Share on WhatsApp</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- WhatsApp Popup -->
    <div class="whatsapp-popup" id="whatsappPopup">
        <button class="whatsapp-close" onclick="closeWhatsAppPopup()">
            <i data-lucide="x"></i>
        </button>
        
        <div class="whatsapp-chat hidden" id="whatsappChat">
            <div class="chat-header">
                <div class="chat-avatar">
                    <i data-lucide="message-circle"></i>
                </div>
                <div class="chat-info">
                    <p class="chat-name">AdivoQ Support</p>
                    <p class="chat-status">Usually replies instantly</p>
                </div>
                <button class="chat-close" onclick="toggleWhatsAppChat()">
                    <i data-lucide="x"></i>
                </button>
            </div>
            
            <div class="chat-body">
                <div class="chat-message">
                    <p>Hey! 👋 Welcome to AdivoQ. How can we help you today?</p>
                </div>
                
                <div class="quick-replies">
                    <p class="quick-label">Quick replies</p>
                    <button onclick="sendWhatsApp('Hi! I\'m interested in AdivoQ')">Hi! I'm interested in AdivoQ</button>
                    <button onclick="sendWhatsApp('I have a question about pricing')">I have a question about pricing</button>
                    <button onclick="sendWhatsApp('I want to schedule a demo')">I want to schedule a demo</button>
                    <button onclick="sendWhatsApp('How can I join the waitlist?')">How can I join the waitlist?</button>
                </div>
                
                <div class="chat-input">
                    <input type="text" id="whatsappMessage" placeholder="Type a message..." onkeypress="handleWhatsAppEnter(event)">
                    <button onclick="sendCustomWhatsApp()">
                        <i data-lucide="send"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <button class="whatsapp-btn" onclick="toggleWhatsAppChat()">
            <span class="pulse"></span>
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            <span class="notification-badge">1</span>
        </button>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    </script>
    
    <script src="assets/js/main.js"></script>
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>