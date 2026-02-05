<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include("connect.php");

// Initialize variables
$userName = "User";
$error = "";

try {
    // Validate session data
    if (empty($_SESSION['email'])) {
        throw new Exception("Invalid session data");
    }
    
    $email = $_SESSION['email'];
    
    // Use prepared statement to prevent SQL injection
    $query = $conn->prepare("SELECT firstName, lastName FROM users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $userName = htmlspecialchars($row['firstName'] . ' ' . $row['lastName']);
    } else {
        // User not found in database - possible session inconsistency
        session_destroy();
        header("Location: login.php");
        exit();
    }
    
    $query->close();
    
} catch (Exception $e) {
    $error = "Error retrieving user data: " . $e->getMessage();
    error_log($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 600px;
            width: 90%;
        }
        .welcome-text {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 2rem;
            line-height: 1.4;
        }
        .logout-btn {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .logout-btn:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .error {
            color: #e74c3c;
            margin-bottom: 1rem;
            padding: 10px;
            background: #ffeaea;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <p class="welcome-text">
            Hello <?php echo $userName; ?>! 👋
        </p>
        
        <p style="color: #666; margin-bottom: 2rem;">
            Welcome to your dashboard. We're glad to see you back!
        </p>
        
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>