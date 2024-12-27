<?php
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {

        if ($_SESSION['type'] === 'admin') {
            $menu_link = 'main_menu_admin.php';  
        } else {
            $menu_link = 'main_menu.php'; 
        }
    } else {
        $menu_link = 'login.php'; 
    }
    ?>
<nav>
    <div class="nav-container">
        <div class="nav-left">
            <h2>HERBARIUM FOR PLANT BIODIVERSITY</h2>
        </div>
        <div class="nav-middle">
            <a href="index.php"><img src="img/logo.png" alt="Logo" class="nav-logo" id="default-logo"></a>
            <a href="index.php"><img src="img/logo-alter.png" alt="Logo" class="nav-logo" id="scrolled-logo"></a>
        </div>
        <div class="nav-right">
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <form class="d-flex px-3" action="search.php" method="GET">
                    <input class="search-input" type="search" name="query" placeholder="Search" aria-label="Search" required>
                    <button class="search-icon" type="submit"><i class="fa fa-search"></i></button>
                </form>
            <?php endif; ?>   
            <a href="<?php echo $menu_link; ?>" class="nav-link">Menu</a>
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <?php if (isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image'])): ?>
                    <div class="user-container">
                        <img src="img/profile_images/<?= htmlspecialchars($_SESSION['profile_image']); ?>" alt="User Profile" class="nav-icon">
                        <div class="user-content">
                            <a href="view_profile.php" class="nav-link logout-link">Profile</a>
                            <a href="logout.php" class="nav-link logout-link">Logout</a> 
                        </div>     
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <a href="login.php" class="nav-link nav-menu">Login</a>
            <?php endif; ?>
        </div>
        <!-- Hidden checkbox to control the menu -->
        <input type="checkbox" id="hamburger-checkbox" class="hamburger-checkbox" />
        <label for="hamburger-checkbox" class="hamburger">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </label>
    </div>
    <div class="full-width-menu" id="full-width-menu">
    <div class="menu-content">
    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
        <form class="d-flex" action="search.php" method="GET">
            <input class="search-input" type="search" name="query" placeholder="Search" aria-label="Search" required>
            <button class="search-icon" type="submit"><i class="fa fa-search"></i></button>
        </form>
        <a href="view_profile.php" class="nav-link">Profile</a>
        <a href="logout.php" class="nav-link">Logout</a>
    <?php else: ?>
        <a href="login.php" class="nav-link">Login</a>
        <a href="registration.php" class="nav-link">Register</a>
    <?php endif; ?>
    <a href="<?php echo $menu_link; ?>" class="nav-link">Menu</a>

</div>


</div>
</nav>

<!-- Full Width Menu should be a sibling to nav-container -->

