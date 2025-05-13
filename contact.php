<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
        <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .containers {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .site-identity h1 {
            margin: 0;
            display: inline-block;
        }
        .logo {
            height: 50px;
        }
        .site-navigation {
            float: right;
        }
        .site-navigation ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
        }
        .site-navigation li {
            margin-left: 20px;
        }
        .site-navigation a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }
        .site-navigation a:hover {
            color: #007bff;
        }
        .nav-toggle {
            display: none;
        }
        .nav-toggle-label {
            display: none;
        }
        .contact-section {
            padding: 50px 0;
            background: #fff;
        }
        .contact-section h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .contact-form {
            max-width: 600px;
            margin: 0 auto;
            background: #f9f9f9;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 150px;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #007bff;
            outline: none;
        }
        .submit-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
        }
        .submit-btn:hover {
            background: #0056b3;
        }
        .footer {
            background: #333;
            color: #fff;
            text-align: center;
            padding: 20px 0;
            margin-top: 50px;
        }
        @media (max-width: 768px) {
            .site-navigation {
                float: none;
                text-align: center;
            }
            .site-navigation ul {
                display: none;
                flex-direction: column;
                width: 100%;
            }
            .site-navigation ul.active {
                display: flex;
            }
            .site-navigation li {
                margin: 10px 0;
            }
            .nav-toggle-label {
                display: block;
                cursor: pointer;
                position: absolute;
                top: 20px;
                right: 20px;
            }
            .nav-toggle-label span,
            .nav-toggle-label span::before,
            .nav-toggle-label span::after {
                display: block;
                background: #333;
                height: 3px;
                width: 25px;
                position: relative;
            }
            .nav-toggle-label span::before,
            .nav-toggle-label span::after {
                content: '';
                position: absolute;
            }
            .nav-toggle-label span::before {
                top: -8px;
            }
            .nav-toggle-label span::after {
                top: 8px;
            }
            .nav-toggle:checked ~ ul {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="containers">
            <div class="site-identity">
                <h1><a href="home.php"><img src="images/logo.gif" alt="Logo" class="logo"></a></h1>
            </div>
            <nav class="site-navigation">
                <input type="checkbox" id="nav-toggle" class="nav-toggle">
                <label for="nav-toggle" class="nav-toggle-label">
                    <span></span>
                </label>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="shop.php">Food</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="cart.php" class="cart-link"><i class="fas fa-shopping-cart"></i> <?php echo array_sum($_SESSION['cart'] ?? []); ?></a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="contact-section">
        <div class="containers">
            <h2>Contact Us</h2>
            <form action="process_contact.php" method="POST" class="contact-form">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" required></textarea>
                </div>
                <button type="submit" class="submit-btn">Send Message</button>
            </form>
        </div>
    </section>

    <footer class="footer">
        <div class="containers">
            <p>&copy; <?php echo date('Y'); ?> Food Website. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>