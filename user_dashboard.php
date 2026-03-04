<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
header("Location:index.php");
}

$user_id = $_SESSION['user_id'];


// ================= APPLY =================

if(isset($_POST['apply'])){

$scheme_id=$_POST['scheme_id'];

// check if already applied
$check=mysqli_query($conn,"SELECT * FROM applications 
WHERE user_id=$user_id AND scheme_id=$scheme_id");

if(mysqli_num_rows($check)==0){

mysqli_query($conn,"INSERT INTO applications(user_id,scheme_id)
VALUES($user_id,$scheme_id)");

echo "<script>alert('Applied Successfully');</script>";

}else{

echo "<script>alert('You already applied for this scheme');</script>";

}

}


// ================= DELETE ACCOUNT =================

if(isset($_POST['delete_account'])){
$id=$_SESSION['user_id'];
mysqli_query($conn,"DELETE FROM users WHERE id=$id");
header("Location:logout.php");
}
?>

<!DOCTYPE html>
<html>
<head>

<title>User Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

<style>

body{
font-family:Poppins;
background:#f4f6fb;
margin:0;
}

.navbar{
background:linear-gradient(90deg,#43cea2,#185a9d);
color:white;
padding:15px 30px;
display:flex;
justify-content:space-between;
}

.container{
padding:30px;
}

.card{
background:white;
padding:20px;
border-radius:10px;
box-shadow:0 5px 15px rgba(0,0,0,0.1);
margin-bottom:20px;
}

button{
padding:10px;
border:none;
border-radius:6px;
cursor:pointer;
background:#43cea2;
color:white;
}

button.applied{
background:gray;
cursor:not-allowed;
}

.delete{
background:red;
}

</style>

</head>

<body>

<div class="navbar">
<h2>User Dashboard</h2>
<form action="logout.php" method="POST" style="margin:0;">
    <button type="submit">Logout</button>
</form>
</div>

<div class="container">

<h3>Available Schemes</h3>
<form method="GET" style="margin-bottom:20px; display:flex; gap:10px;">

<input type="text" name="search"
placeholder="Search schemes..."
value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>"
style="flex:1;padding:10px;border-radius:6px;border:1px solid #ddd;">

<button type="submit">Search</button>

<a href="user_dashboard.php">
<button type="button" style="background:#43cea2;">Reset</button>
</a>

</form>

<?php
$search = "";

if(isset($_GET['search']) && $_GET['search']!=""){

$search = mysqli_real_escape_string($conn,$_GET['search']);

$result=mysqli_query($conn,"
SELECT * FROM schemes
WHERE title LIKE '%$search%'
OR description LIKE '%$search%'
OR eligibility LIKE '%$search%'
");

}else{

$result=mysqli_query($conn,"SELECT * FROM schemes");

}

while($row=mysqli_fetch_assoc($result)){

$scheme_id = $row['id'];

// check applied status
$check=mysqli_query($conn,"SELECT * FROM applications 
WHERE user_id=$user_id AND scheme_id=$scheme_id");

$applied = mysqli_num_rows($check) > 0;
?>

<div class="card">

<h4><?php echo $row['title']; ?></h4>

<p><?php echo $row['description']; ?></p>

<p><b>Eligibility:</b> <?php echo $row['eligibility']; ?></p>

<form method="POST">

<input type="hidden" name="scheme_id" value="<?php echo $row['id']; ?>">

<?php if($applied){ ?>

<button type="button" class="applied">Applied</button>

<?php } else { ?>

<button name="apply">Apply</button>

<?php } ?>

</form>

</div>

<?php } ?>

<div class="card">

<h3>Delete Account</h3>

<form method="POST">

<button name="delete_account" class="delete">
Delete My Account
</button>

</form>

</div>

</div>

</body>
</html>