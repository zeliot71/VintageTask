<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  	<meta charset="utf-8">
  	<meta name="viewport" content="initial-scale=1, width=device-width">
  	<title>VintageTask - Group Work Management</title>
  	<link rel="stylesheet" href="./assets/css/index.css">
  	<link href="https://fonts.googleapis.com/css2?family=Inder:wght@400&display=swap" rel="stylesheet">
</head>
<body>
  	<div class="landing">
    		<img class="image-bg-1" src="assets/images/image 1.png" alt="">
    		<img class="image-bg-2" src="assets/images/image 2.png" alt="">

    		<div class="content-wrapper">
      			<div class="logo-section">
        				<img src="assets/images/logo.png" alt="VintageTask" class="logo">
      			</div>

      			<h1 class="main-title">To smooth your Group Work Experience</h1>

      			<div class="cta-buttons">
        				<?php if (isset($_SESSION['user_id'])): ?>
          					<a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
          					<a href="actions/logout.php" class="btn btn-secondary">Logout</a>
        				<?php else: ?>
          					<a href="login.php" class="btn btn-primary">Login</a>
          					<a href="signup.php" class="btn btn-secondary">Sign Up</a>
        				<?php endif; ?>
      			</div>
    		</div>
  	</div>
</body>
</html>