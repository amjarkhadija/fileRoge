   <style>
    .footer {
    background: linear-gradient(135deg, #8b5a9f 0%, #7c4da3 100%);
    color: white;
    padding: 3rem 0 2rem 0;
    position: relative;
}
.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 3rem;
}
/* Footer Sections */
.footer-section {
    flex: 1;
}
/* Logo Section */
.logo-section {
    flex: 0 0 auto;
    min-width: 200px;
}
.logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.logo-icon {
    display: flex;
    align-items: center;
    justify-content: center;
}
.logo-text {
    font-size: 1.5rem;
    font-weight: 600;
    letter-spacing: 0.05em;
}
/* Links Section */
.links-section {
    flex: 0 0 auto;
    min-width: 180px;
}
.section-title {
    font-size: 1.1rem;
    font-weight: 500;
    margin-bottom: 1.5rem;
    color: white;
}
.footer-nav ul {
    list-style: none;
}
.footer-nav li {
    margin-bottom: 0.75rem;
}
.footer-nav a {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 400;
    transition: all 0.3s ease;
    position: relative;
}
.footer-nav a:hover {
    color: white;
    padding-left: 0.5rem;
}
.footer-nav a::before {
    content: '';
    position: absolute;
    left: -0.5rem;
    top: 50%;
    transform: translateY(-50%);
    width: 0;
    height: 2px;
    background: white;
    transition: width 0.3s ease;
}
.footer-nav a:hover::before {
    width: 0.25rem;
}
/* Contact Section */
.contact-section {
    flex: 1;
    min-width: 280px;
}
.contact-info {
    margin-bottom: 2rem;
}
.contact-item {
    margin-bottom: 1rem;
}
.contact-item p {
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.95rem;
    line-height: 1.4;
}
.address {
    margin-bottom: 0.25rem;
}
.phone {
    font-weight: 500;
}
.email {
    color: rgba(255, 255, 255, 0.8);
}
/* Social Media */
.social-media {
    margin-top: 2rem;
}
.follow-title {
    font-size: 1rem;
    font-weight: 500;
    margin-bottom: 1rem;
    color: white;
}
.social-icons {
    display: flex;
    gap: 1rem;
}
.social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}
.social-link:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.6);
    color: white;
    transform: translateY(-2px);
}
/* Responsive Design */
@media (max-width: 768px) {
    .main-content {
        min-height: 60vh;
    }
    
    .content-wrapper h1 {
        font-size: 2rem;
    }
    
    .content-wrapper p {
        font-size: 1rem;
    }
    
    .footer {
        padding: 2rem 0 1.5rem 0;
    }
    
    .footer-container {
        flex-direction: column;
        gap: 2rem;
        padding: 0 1.5rem;
    }
    
    .footer-section {
        text-align: center;
    }
    
    .logo {
        justify-content: center;
    }
    
    .social-icons {
        justify-content: center;
    }
    
    .footer-nav ul {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
    }
    
    .footer-nav li {
        margin-bottom: 0;
    }
}
@media (max-width: 480px) {
    .footer-container {
        padding: 0 1rem;
    }
    
    .logo-text {
        font-size: 1.3rem;
    }
    
    .section-title {
        font-size: 1rem;
    }
    
    .contact-item p {
        font-size: 0.9rem;
    }
    
    .footer-nav ul {
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }
    
    .social-link {
        width: 32px;
        height: 32px;
        font-size: 0.8rem;
    }
}
   </style>
   <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <!-- Logo Section -->
            <div class="footer-section logo-section">
                <div class="logo">
                    <div class="logo-icon">
                        <!-- <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 2L6 8v16l10 6 10-6V8l-10-6z" stroke="white" stroke-width="2" fill="none"/>
                            <path d="M16 8v16" stroke="white" stroke-width="2"/>
                            <path d="M6 8l10 6 10-6" stroke="white" stroke-width="2"/>
                        </svg> -->
                        <img class="logo-icon"><img src="img/logo.png" alt=""/>
                    </div>
                    
                </div>
            </div>
            <!-- Navigation Links Section -->
            <div class="footer-section links-section">
                <h3 class="section-title">Links</h3>
                <nav class="footer-nav">
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#contact">Contact Us</a></li>
                    </ul>
                </nav>
            </div>
            <!-- Contact Information Section -->
            <div class="footer-section contact-section">
                <h3 class="section-title">Find Us</h3>
                <div class="contact-info">
                    <div class="contact-item">
                        <p class="address">43 W. Wellington Road Fairhope</p>
                        <p class="address">AL 36532</p>
                    </div>
                    <div class="contact-item">
                        <p class="phone">(251) 388-6895</p>
                    </div>
                    <div class="contact-item">
                        <p class="email">terminaloutlook.com</p>
                    </div>
                </div>
                <div class="social-media">
                    <h4 class="follow-title">Follow Us</h4>
                    <div class="social-icons">
                        <a href="#" class="social-link" aria-label="Facebook">
                            <img src="img/social-icons/Facebook_black.svg" alt="">
                        </a>
                        <a href="#" class="social-link" aria-label="Twitter">
                            <img src="img/social-icons/X.svg" alt="">
                        </a>
                        <a href="#" class="social-link" aria-label="Instagram">
                            <img src="img/social-icons/Instagram.svg" alt="">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>