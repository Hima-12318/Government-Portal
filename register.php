<?php
include 'db.php';

$error="";
$success="";

if($_SERVER["REQUEST_METHOD"]=="POST"){

$name=$_POST['name'];
$email=$_POST['email'];
$password=$_POST['password'];

$check=mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");

if(mysqli_num_rows($check)>0){

$error="Email already exists";

}else{

$sql="INSERT INTO users(name,email,password,role)
VALUES('$name','$email','$password','user')";

if(mysqli_query($conn,$sql)){
$success="Registered Successfully";
}else{
$error="Database error";
}

}

}

?>

<!DOCTYPE html>
<html>
<head>

<title>Register</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

<style>

body{
font-family:Poppins;
background:linear-gradient(135deg,#43cea2,#185a9d);
height:100vh;
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
background:#43cea2;
color:white;
border:none;
border-radius:6px;
font-size:16px;
cursor:pointer;
}

button:hover{
background:#2bbf8a;
}

.error{color:red;}
.success{color:green;}

a{
color:#185a9d;
text-decoration:none;
}

</style>

</head>

<body>

<div class="card">

<h2>Register</h2>

<?php if($error!="") echo "<div class='error'>$error</div>"; ?>
<?php if($success!="") echo "<div class='success'>$success</div>"; ?>

<form method="POST">

<input type="text" name="name" placeholder="Name" required>

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="password" placeholder="Password" required>

<button type="submit">Register</button>

</form>

<br>

<a href="index.php">Back to Login</a>

</div>

</body>
</html>