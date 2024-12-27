<?php
session_name('Zwe_Het_Zaw');
session_start();
include_once 'head.php';
?>
<body>
    <?php include_once 'header.php' ?>
    <div class="container about-container">
        <h2 class="d-flex justify-content-center mb-5">About This Web Application</h2>
        
        <!-- Original Table with Assignment Details -->
        <table class="table table-bordered mb-5">
            <thead class="table-light">
                <tr>
                    <th>Section</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>PHP Version</td>
                    <td>PHP Version: 8.2.12</td>
                </tr>
                <tr>
                    <td>Tasks Completed</td>
                    <td>
                        <ul>
                            <li>Task 1: Database Table Creation (user_table, account_table, plant_table)</li>
                            <li>Task 2.1: Registration Page (registration.php)</li>
                            <li>Task 2.2: Login Page (login.php)</li>
                            <li>Task 3.1: Main Menu Page (main_menu.php)</li>
                            <li>Task 3.2: View Plant Detail Page (plant_detail.php)</li>
                            <li>Task 3.3: Update Profile Page (update_profile.php)</li>
                            <li>Task 3.4: Contribution Page (contribute.php)</li>
                            <li>Task 4.1: Admin Main Menu Page (main_menu_admin.php)</li>
                            <li>Task 4.2: Manage Users’ Account Page (manage_accounts.php)</li>
                            <li>Task 4.3: Manage Plants Page (manage_plants.php)</li>
                            <li>Task 4.4: About This Assignment Page (about.php)</li>
                            <li>Task 5.1: Identify Page (identify.php)</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td>Tasks Not Completed</td>
                    <td>All tasks have been completed.</td>
                </tr>
                <tr>
                    <td>Frameworks/3rd Party Libraries Used</td>
                    <td>
                        <ul>
                            <li>Bootstrap version 5.3.0</li>
                            <li>DOMPdf version 3.0.0</li>
                            <li>Teachable Machine AI Model for Plant Identification</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td>Video Presentation</td>
                    <td><a href="https://youtu.be/Vhefcjco1Qo" class="btn btn-primary">Link to YouTube</a></td>
                </tr>
            </tbody>
        </table>

        <div class="btn-container d-flex justify-content-center">
            <a href="index.php" class="btn btn-success">Back to Home Page</a>
        </div>
    </div>

    <?php include_once "footer.php" ?>
</body>
</html>
