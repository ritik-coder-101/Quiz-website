<?php
include 'db.php'; // Includes the database connection file

// Handle AJAX request for login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $message = '';
    $messageType = 'error';

    if (empty($email) || empty($password)) {
        $message = "Both fields are required!";
    } else {
        // Check if email exists in the database
        $emailCheckQuery = "SELECT * FROM signin WHERE email = ?";
        $stmt = $conn->prepare($emailCheckQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $message = "No account found with this email!";
        } else {
            $user = $result->fetch_assoc();
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Successful login, you can redirect to the index page or another page
                $message = "Login successful! Redirecting...";
                $messageType = 'success';
            } else {
                $message = "Incorrect password!";
            }
        }
    }

    echo json_encode(['message' => $message, 'type' => $messageType]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        $(document).ready(function() {
            // Handle form submission
            $('#login-form').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                // Prevent multiple form submissions (if the form is already submitting)
                if ($(this).data('isSubmitting')) {
                    return;
                }

                // Mark form as submitting
                $(this).data('isSubmitting', true);

                // Get form data
                var formData = {
                    email: $('#email').val(),
                    password: $('#password').val(),
                    ajax: true // Flag to identify AJAX request
                };

                // Disable the submit button during AJAX request to prevent double submission
                $('button[type="submit"]').prop('disabled', true);

                // Send AJAX request to login.php
                $.ajax({
                    url: 'login.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        // Display the message with the appropriate icon
                        var icon = response.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
                        $('.message').html('<i class="fas ' + icon + '"></i> ' + response.message);

                        // If login is successful, redirect after a brief delay
                        if (response.type === 'success') {
                            setTimeout(function() {
                                window.location.href = 'index.html'; // Redirect to index page or another page
                            }, 2000); // 2 seconds delay for user feedback
                        }
                    },
                    error: function() {
                        $('.message').html('<i class="fas fa-exclamation-circle"></i> An error occurred. Please try again.');
                    },
                    complete: function() {
                        // Re-enable the submit button after AJAX request is complete
                        $('button[type="submit"]').prop('disabled', false);

                        // Mark form as not submitting anymore
                        $('#login-form').data('isSubmitting', false);
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        <form id="login-form" method="POST">
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

            <button type="submit">Login</button>
        </form>
        <p class="message"></p>
        <p class="message">Don't have an account? <a href="signup.php">Sign up here</a>.</p>
    </div>
</body>
</html>
