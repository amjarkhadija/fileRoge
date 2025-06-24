
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Contact Us - ATLAS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #ffffff;
            line-height: 1.6;
            color: #333;
        }


        /* Main Content */
        .main-content {
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
        }

        .contact-container {
            background-color: white;
            padding: 60px 50px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        .contact-header {
            margin-bottom: 40px;
        }

        .contact-header h1 {
            color: #333;
            margin-bottom: 15px;
            font-size: 2.5rem;
            font-weight: 300;
        }

        .contact-header p {
            color: #666;
            font-size: 1.1rem;
        }

        .contact-info {
            display: grid;
            gap: 25px;
            margin-bottom: 40px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .contact-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            background: #8B5A87;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            flex-shrink: 0;
        }

        .contact-details {
            text-align: left;
        }

        .contact-label {
            font-weight: 600;
            color: #333;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .contact-value {
            color: #666;
            font-size: 1rem;
        }

        .contact-value a {
            color: #8B5A87;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .contact-value a:hover {
            color: #7a4f7a;
            text-decoration: underline;
        }

        .contact-footer {
            padding: 25px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-top: 30px;
        }

        .contact-footer p {
            color: #666;
            font-size: 1.1rem;
            margin: 0;
        }

        .cta-section {
            margin-top: 40px;
        }

        .cta-button {
            display: inline-block;
            background: #8B5A87;
            color: white;
            padding: 15px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .cta-button:hover {
            background: #7a4f7a;
            transform: translateY(-2px);
        }


    </style>
</head>
<body>
    <!-- Include header -->
    <?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="contact-container">
            <div class="contact-header">
                <h1>Contact Us</h1>
                <p>Get in touch with our team. We're here to help you find your perfect property.</p>
            </div>

            <div class="contact-info">
                <div class="contact-item">
                    <div class="contact-icon">📞</div>
                    <div class="contact-details">
                        <div class="contact-label">Phone</div>
                        <div class="contact-value">
                            <a href="tel:+212600123456">+212 600 123 456</a>
                        </div>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">✉️</div>
                    <div class="contact-details">
                        <div class="contact-label">Email</div>
                        <div class="contact-value">
                            <a href="mailto:contact@3a9ari.ma">contact@3a9ari.ma</a>
                        </div>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">📍</div>
                    <div class="contact-details">
                        <div class="contact-label">Address</div>
                        <div class="contact-value">
                            Mohammed V Street<br>
                            Tangier, Morocco
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-footer">
                <p>We're available Monday to Friday, 9:00 AM to 6:00 PM. Feel free to reach out anytime!</p>
            </div>

            <div class="cta-section">
                <a href="landing.php" class="cta-button">Browse Properties</a>
            </div>
        </div>
    </main>

    
    <!-- Include Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>