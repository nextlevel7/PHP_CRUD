<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = "user";
    $password = "password";

    if ($_POST["username"] == $username && $_POST["password"] == $password) {
        $_SESSION["authenticated"] = true;

        if ($_POST["page_choice"] == "page1") {
            // Redirect to the first protected page after authentication
            header("Location: employee.php");
        } elseif ($_POST["page_choice"] == "page2") {
            // Redirect to the second protected page after authentication
            header("Location: index.php");
        }
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px; /* Increased width */
        }
        .login-form {
            display: flex;
            flex-direction: column;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"], input[type="password"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="radio"] {
            margin-right: 5px;
        }
        .submit-btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 15px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .submit-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form class="login-form" method="post" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label>Select:</label>
                <input type="radio" name="page_choice" value="page1"> Employee CRUD
                <input type="radio" name="page_choice" value="page2"> Company CRUD
            </div>
            <button class="submit-btn" type="submit">Login</button>
        </form>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
    </div>
</body>
</html>
