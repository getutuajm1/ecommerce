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