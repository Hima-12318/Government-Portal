<?php
session_start();
include 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $password = mysqli_real_escape_string($conn,$_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn,$sql);

    if(mysqli_num_rows($result)>0){

        $row = mysqli_fetch_assoc($result);
$otp = strval(rand(100000,999999));

mysqli_query($conn,"UPDATE users SET otp='$otp' WHERE id=".$row['id']);
       
        // Store temporary session
        $_SESSION['temp_user_id'] = $row['id'];
        $_SESSION['temp_role'] = $row['role'];

        // Redirect to OTP page
        header("Location: verify_otp.php");
        exit();

    }else{
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Poppins;
}

body{
height:100vh;
background:linear-gradient(135deg,#667eea,#764ba2);
display:flex;
justify-content:center;
align-items:center;
}

.card{
background:white;
padding:40px;
border-radius:12px;
width:350px;
box-shadow:0 10px 25px rgba(0,0,0,0.2);
text-align:center;
}

h2{
margin-bottom:20px;
}

input{
width:100%;
padding:12px;
margin:10px 0;
border:1px solid #ccc;
border-radius:6px;
}

button{
width:100%;
padding:12px;
background:#667eea;
color:white;
border:none;
border-radius:6px;
font-size:16px;
cursor:pointer;
transition:0.3s;
}

button:hover{
background:#5a67d8;
}

.error{
color:red;
margin-bottom:10px;
}

a{
text-decoration:none;
color:#667eea;
}

</style>

</head>

<body>

<div class="card">

<h2>Login</h2>

<?php if($error!="") echo "<div class='error'>$error</div>"; ?>

<form method="POST">

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="password" placeholder="Password" required>

<button type="submit">Login</button>

</form>

<br>

<p>Don't have account? <a href="register.php">Register</a></p>

</div>

</body>
</html>