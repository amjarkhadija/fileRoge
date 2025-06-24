  <style>
           /* Header Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .logo img{
            width: 100px;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            gap: 30px;
        }

        .navbar ul li a {
            color: #666;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .navbar ul li a:hover {
            color: #8B5A87;
        }
 
  </style>
  
  <!-- Navigation -->
    <nav class="navbar">
        <div class="logo"><img src="img/logo-dark.png" alt="Logo"></div>
        <ul>
            <li><a href="landing.php">Home</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="#">About</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Log In</a></li>
                <li><a href="register.php">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </nav>