<?php
include 'db.php'; // Includes the database connection file

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $message = '';   
    $messageType = 'error';

    if (empty($username) || empty($email) || empty($password)) {
        $message = "All fields are required!";
    } else {
        $emailCheckQuery = "SELECT * FROM signin WHERE email = ?";
        $stmt = $conn->prepare($emailCheckQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "This email is already registered!";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $insertQuery = "INSERT INTO signin (username, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("sss", $username, $email, $hashedPassword);

            if ($stmt->execute()) {
                $message = "Account created successfully! Please <a href='login.php'>Login here</a>.";
                $messageType = 'success'; 
            } else {
                $message = "Error: Could not create account. Please try again.";
            }
        }
    }
    
    // Send response back to AJAX
    echo json_encode(['message' => $message, 'type' => $messageType]);
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        $(document).ready(function() {
    // Handle form submission
    $('#signup-form').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        // Prevent multiple form submissions (if the form is already submitting)
        if ($(this).data('isSubmitting')) {
            return;
        }

        // Mark form as submitting
        $(this).data('isSubmitting', true);

        // Get form data
        var formData = {
            username: $('#username').val(),
            email: $('#email').val(),
            password: $('#password').val(),
            ajax: true // Flag to identify AJAX request
        };

        // Disable the submit button during AJAX request to prevent double submission
        $('button[type="submit"]').prop('disabled', true);

        // Send AJAX request to signup.php
        $.ajax({
            url: 'signup.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                // Display the message with the appropriate icon
                var icon = response.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
                $('.message').html('<i class="fas ' + icon + '"></i> ' + response.message);
            },
            error: function() {
                // Handle errors
                $('.message').html('<i class="fas fa-exclamation-circle"></i> An error occurred. Please try again.');
            },
            complete: function() {
                // Re-enable the submit button after AJAX request is complete
                $('button[type="submit"]').prop('disabled', false);

                // Mark form as not submitting anymore
                $('#signup-form').data('isSubmitting', false);
            }
        });
    });
});
    </script>
</head>
<body>
    <div class="form-container">
        <h2>Create Account</h2>
        <form id="signup-form" method="POST">
            <div class="input-container">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
                <i class="fas fa-user"></i> <!-- Username icon -->
            </div>

            <div class="input-container">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <i class="fas fa-envelope"></i> <!-- Email icon -->
            </div>

            <div class="input-container">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <i class="fas fa-lock"></i> <!-- Lock icon for password -->
            </div>

            <button type="submit">Sign Up</button>
        </form>
        <p class="message"></p>
        <p class="message">Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</body>
</html>
