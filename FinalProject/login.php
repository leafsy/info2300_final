<?php include_once("includes/header.php"); ?>

<div id="login-area">
	<?php

	if (isset($_GET["login-submit"])) {

		$username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING);
		$password = hash("sha256", $_GET['password']);

		require_once 'includes/config.php';
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		if($mysqli->connect_error) {
		   die("Connection failed: ".$mysqli->connect_error);
		}

		$query = "SELECT * FROM Accounts
				  WHERE username = ? AND password = ?";
		$stmt = $mysqli->stmt_init();
		if ($stmt->prepare($query)) {
			$stmt->bind_param("ss", $username, $password);
			$stmt->execute();
			$result = $stmt->get_result();
		}

		if ($result->fetch_row()) {
			$_SESSION["user"] = $username;
			echo "<p>Sign-in successful!</p>";
			echo '<script type="text/javascript">';
      		echo 'window.location.href="index.php";';
      		echo '</script>';
		}

		echo "<p>(!) Username or password incorrect</p>";
		
	}

	?>

	<h3>SIGN IN</h3>
	<form id="login-form" method="get">
		<p><input type="text" name="username" placeholder="username"></p>
	    <p><input type="password" name="password" placeholder="password"></p>
	    <p><input class="submit" type="submit" name="login-submit" value="sign in"></p>
	</form>
</div>

<div id="signup-area">
	<?php

	if (isset($_POST["signup-submit"])) {

		$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
		$password1 = hash("sha256", $_POST['password1']);
		$password2 = hash("sha256", $_POST['password2']);

		if (empty($username) || empty($password1) || empty($password2)) {
			echo "<p>(!) One or more input fields are empty</p>";
		} else if (!preg_match('/^[A-Za-z0-9_]+$/', $username)) {
			echo "<p>(!) Username must contain only alphanumeric characters or underscore</p>";
		} else if (strlen($username) > 16) {
			echo "<p>(!) Username must contain at most 16 characters</p>";
		} else if ($password1 !== $password2) {
			echo "<p>(!) Passwords must match</p>";
		} else {

			require_once 'includes/config.php';
			$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			if($mysqli->connect_error) {
			   die("Connection failed: ".$mysqli->connect_error);
			}

			$query = "SELECT * FROM Accounts
					  WHERE username = ?";
			$stmt = $mysqli->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$result = $stmt->get_result();
			$stmt->close();

			if ($result->fetch_row()) {
				echo "<p>(!) Username already taken</p>";
			} else {

				$query = "INSERT INTO Accounts (username,password)
						  VALUES (?,?)";
				$stmt = $mysqli->stmt_init();
				$stmt->prepare($query);
				$stmt->bind_param("ss", $username, $password1);
				if (!$stmt->execute()) {
					echo "<p>(!) Sign-up unsuccessful; check input</p>";
				} else {
					$_SESSION["user"] = $username;
					echo "<p>Sign-up successful!</p>";
					echo '<script type="text/javascript">';
		      		echo 'window.location.href="index.php";';
		      		echo '</script>';
				}
				$stmt->close();
			}

		}
	}

	?>

	<h3>SIGN UP</h3>
	<form id="signup-form" method="post">
		<p><input type="text" name="username" placeholder="enter username"></p>
	    <p><input type="password" name="password1" placeholder="enter password"></p>
	    <p><input type="password" name="password2" placeholder="confirm password"></p>
	    <p><input class="submit" type="submit" name="signup-submit" value="sign up"></p>
	</form>
</div>

</div>
</body>

</html>