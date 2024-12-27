<?php
session_name('Zwe_Het_Zaw');
session_start(); 
$_SESSION['register_from'] = 'register';

// Set timeout duration to 30 minutes
$timeout_duration = 1800; // 30 minutes in seconds

// Check if 'last_activity' is set in session
if (isset($_SESSION['last_activity'])) {
    // If the last activity time is more than 30 minutes ago, log out the user
    if (time() - $_SESSION['last_activity'] > $timeout_duration) {
        // Clear the session and redirect to login
        session_unset();
        session_destroy();
        header("Location: login.php?message=Your session has expired due to inactivity.");
        exit();  // Make sure the script stops executing after the redirect
    }
}

// Update the last activity timestamp to the current time
$_SESSION['last_activity'] = time();

// Retrieve errors and form data from the session
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];

// Clear session errors and form data
unset($_SESSION['errors']);
unset($_SESSION['form_data']);
include_once "head.php"; ?>

<body id="reg-body">
    <?php include_once "header.php"; ?>
    <div class="container d-flex justify-content-center" id="reg-container">
        <form class="p-2" id="reg-form" action="process_register.php" method="POST">
            <legend class="text-center display-7 mb-3 mt-3">Create an account</legend>
            <?php if (isset($_SESSION['registration_success']) && $_SESSION['registration_success'] === true): ?>
                <div class="alert alert-success">
                    Registration successful! Redirecting to login page...
                </div>
                <meta http-equiv="refresh" content="3;url=login.php">
                <?php unset($_SESSION['registration_success']); ?>
            <?php endif; ?>
            <div class="row mb-3 p-3">
                <div class="col-md-6 mb-3 input-container">
                    <input type="text" name="first_name" id="first-name" class="custom-input" placeholder="First Name" value="<?= htmlspecialchars($form_data['first_name'] ?? '') ?>">
                    <?php if (isset($errors['first_name'])): ?>
                        <p class="error-message"><?= htmlspecialchars($errors['first_name']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 input-container">
                    <input type="text" name="last_name" id="last-name" class="custom-input" placeholder="Last Name" value="<?= htmlspecialchars($form_data['last_name'] ?? '') ?>">
                    <?php if (isset($errors['last_name'])): ?>
                        <p class="error-message"><?= htmlspecialchars($errors['last_name']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row mb-3 p-3">
                <div class="col-md-6 mb-3 input-container">
                    <label class="mb-3" for="date-of-birth">Date of Birth: </label>
                    <input type="date" name="dob" class="custom-input" id="date-of-birth" value="<?= htmlspecialchars($form_data['dob'] ?? '') ?>">
                    <?php if (isset($errors['dob'])): ?>
                        <p class="error-message"><?= htmlspecialchars($errors['dob']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 ml-3 input-container">
                    <p class="mb-4">Gender: </p>
                    <label class="form-check-label" for="male">Male</label>
                    <input class="form-check-input" type="radio" name="gender" id="male" value="Male" <?= (isset($form_data['gender']) && $form_data['gender'] === 'Male') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="female">Female</label>
                    <input class="form-check-input" type="radio" name="gender" id="female" value="Female" <?= (isset($form_data['gender']) && $form_data['gender'] === 'Female') ? 'checked' : 'checked' ?>>
                    <?php if (isset($errors['gender'])): ?>
                        <p class="error-message"><?= htmlspecialchars($errors['gender']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row mb-3 p-3">
                <div class="col-md-6 mb-3 input-container">
                    <input type="text" name="email" id="email" class="custom-input" placeholder="Email" value="<?= htmlspecialchars($form_data['email'] ?? '') ?>">
                    <?php if (isset($errors['email'])): ?>
                        <p class="error-message"><?= htmlspecialchars($errors['email']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 input-container">
                    <input type="text" name="home_town" id="home-town" class="custom-input" placeholder="Home Town" value="<?= htmlspecialchars($form_data['home_town'] ?? '') ?>">
                    <?php if (isset($errors['home_town'])): ?>
                        <p class="error-message"><?= htmlspecialchars($errors['home_town']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row mb-3 p-3">
                <div class="col-md-6 mb-3 input-container">
                    <input type="text" name="phone_number" id="phone-number" class="custom-input" placeholder="Phone Number" value="<?= htmlspecialchars($form_data['phone_number'] ?? '') ?>">
                    <?php if (isset($errors['phone_number'])): ?>
                        <p class="error-message"><?= htmlspecialchars($errors['phone_number']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 input-container">
                    <input type="text" name="student_id" id="student-id" class="custom-input" placeholder="Student ID" value="<?= htmlspecialchars($form_data['student_id'] ?? '') ?>">
                    <?php if (isset($errors['student_id'])): ?>
                        <p class="error-message"><?= htmlspecialchars($errors['student_id']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row mb-3 p-3">
                <div class="col-md-6 mb-3 input-container">
                    <input type="password" name="password" id="password" class="custom-input" placeholder="Password">
                    <?php if (isset($errors['password'])): ?>
                        <p class="error-message"><?= htmlspecialchars($errors['password']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 input-container">
                    <input type="password" name="confirm_password" id="confirm-password" class="custom-input" placeholder="Confirm Password">
                    <?php if (isset($errors['confirm_password'])): ?>
                        <p class="error-message"><?= htmlspecialchars($errors['confirm_password']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row mb-3 p-3">
                <div class="col-md-6 mb-3 d-flex justify-content-center">
                    <input type="submit" class="btn btn-primary rounded-0 d-flex custom-btn" value="Submit">
                </div>
                <div class="col-md-6 mb-3 d-flex justify-content-center">
                    <input type="reset" class="btn btn-primary rounded-0 d-flex custom-btn" value="Reset" onclick="clearErrors()">
                </div>
            </div>
        </form>
    </div>
    <?php include_once 'footer.php' ?>
</body>
</html>
