<?php
session_start();
include 'db.php';

if(!isset($_SESSION['role']) || $_SESSION['role']!='admin'){
header("Location:index.php");
exit();
}

// ================= COUNT STATS =================

// exclude admin from total users count
$total_users = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total FROM users WHERE role!='admin'"
))['total'];

$total_schemes = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total FROM schemes"
))['total'];

$total_approved = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(DISTINCT applications.id) as total
FROM applications
JOIN users ON users.id = applications.user_id
WHERE users.role!='admin'
AND LOWER(TRIM(applications.status))='approved'"
))['total'];

$total_rejected = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(DISTINCT applications.id) as total
FROM applications
JOIN users ON users.id = applications.user_id
WHERE users.role!='admin'
AND LOWER(TRIM(applications.status))='rejected'"
))['total'];

// ================= APPROVE =================

if(isset($_GET['approve'])){
$id=$_GET['approve'];
mysqli_query($conn,"UPDATE applications SET status='approved' WHERE id=$id");
header("Location: admin_dashboard.php");
exit();
}


// ================= REJECT =================

if(isset($_GET['reject'])){
$id=$_GET['reject'];
mysqli_query($conn,"UPDATE applications SET status='rejected' WHERE id=$id");
header("Location: admin_dashboard.php");
exit();
}


// ================= DELETE APPLICATION =================

if(isset($_GET['delete_app'])){
$id=$_GET['delete_app'];
mysqli_query($conn,"DELETE FROM applications WHERE id=$id");
header("Location: admin_dashboard.php");
exit();
}


// ================= ADD SCHEME =================

$edit_mode=false;
$title="";
$description="";
$eligibility="";
$id=0;

if(isset($_POST['add_scheme'])){

$title=$_POST['title'];
$description=$_POST['description'];
$eligibility=$_POST['eligibility'];

mysqli_query($conn,"INSERT INTO schemes(title,description,eligibility)
VALUES('$title','$description','$eligibility')");

header("Location: admin_dashboard.php");
exit();
}


// ================= DELETE SCHEME =================

if(isset($_GET['delete'])){
$id=$_GET['delete'];
mysqli_query($conn,"DELETE FROM schemes WHERE id=$id");

header("Location: admin_dashboard.php");
exit();
}


// ================= EDIT SCHEME =================

if(isset($_GET['edit'])){

$id=$_GET['edit'];
$edit_mode=true;

$result=mysqli_query($conn,"SELECT * FROM schemes WHERE id=$id");

$row=mysqli_fetch_assoc($result);

$title=$row['title'];
$description=$row['description'];
$eligibility=$row['eligibility'];
}


// ================= UPDATE SCHEME =================

if(isset($_POST['update_scheme'])){

$id=$_POST['id'];

$title=$_POST['title'];
$description=$_POST['description'];
$eligibility=$_POST['eligibility'];

mysqli_query($conn,"UPDATE schemes SET
title='$title',
description='$description',
eligibility='$eligibility'
WHERE id=$id");

header("Location: admin_dashboard.php");
exit();

}
?>

<!DOCTYPE html>
<html>
<head>

<title>Admin Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

<style>

body{
font-family:Poppins;
background:#f4f6fb;
margin:0;
}


.navbar{
background:linear-gradient(90deg,#667eea,#764ba2);
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

input,textarea{
width:100%;
padding:10px;
margin:8px 0;
border:1px solid #ddd;
border-radius:6px;
}

button{
padding:10px 15px;
border:none;
border-radius:6px;
cursor:pointer;
color:white;
}
.search-btn, .reset-btn{
background:#667eea;
color:white;
width:100px;
height:40px;
}

.add-btn{background:#2ecc71;}
.update-btn{background:#3498db;}
.delete-btn{background:#e74c3c;}
.edit-btn{background:#f39c12;}
.approve-btn{background:#2ecc71;}
.reject-btn{background:#e74c3c;}

table{
width:100%;
border-collapse:collapse;
}

th{
background:#667eea;
color:white;
padding:12px;
}

td{
padding:12px;
border-bottom:1px solid #ddd;
}

.stat-box{
display:flex;
gap:20px;
margin-bottom:20px;
}

.stat{
flex:1;
background:white;
padding:15px;
border-radius:8px;
box-shadow:0 0 10px rgba(0,0,0,0.1);
text-align:center;
}

.stat h2{
margin:0;
color:#667eea;
}

</style>

</head>

<body>

<div class="navbar">
<h2>Admin Dashboard</h2>
<form action="logout.php" method="POST" style="margin:0;">
    <button type="submit" class="delete-btn">Logout</button>
</form>
</div>

<div class="container">

<!-- STATS -->
<div class="stat-box">

<div class="stat">
<h2><?php echo $total_users; ?></h2>
<p>Total Users</p>
</div>

<div class="stat">
<h2><?php echo $total_schemes; ?></h2>
<p>Total Schemes</p>
</div>

<div class="stat">
<h2><?php echo $total_approved; ?></h2>
<p>Approved</p>
</div>

<div class="stat">
<h2><?php echo $total_rejected; ?></h2>
<p>Rejected</p>
</div>

</div>


<!-- ADD / EDIT SCHEME -->

<div class="card">

<h3><?php echo $edit_mode?"Update Scheme":"Add Scheme"; ?></h3>

<form method="POST">

<input type="hidden" name="id" value="<?php echo $id; ?>">

<input type="text" name="title" value="<?php echo $title; ?>" placeholder="Title" required>

<textarea name="description"><?php echo $description; ?></textarea>

<input type="text" name="eligibility" value="<?php echo $eligibility; ?>" placeholder="Eligibility">

<?php if($edit_mode){ ?>

<button class="update-btn" name="update_scheme">Update Scheme</button>

<?php } else { ?>

<button class="add-btn" name="add_scheme">Add Scheme</button>

<?php } ?>

</form>

</div>


<!-- APPLICATION LIST -->

<div class="card">

<h3>User Applications</h3>
<form method="GET" style="margin-bottom:15px; display:flex; gap:10px;">

<input type="text" name="search"
placeholder="Search by user, scheme, or status..."
value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>"
style="flex:1;padding:10px;border-radius:6px;border:1px solid #ddd;">

<button type="submit" class="search-btn">Search</button>

<a href="admin_dashboard.php">
<button type="button" class="reset-btn">Reset</button>
</a>

</form>

<table>

<tr>
<th>ID</th>
<th>User</th>
<th>Scheme</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php

$search = "";

if(isset($_GET['search']) && $_GET['search']!=""){

$search=mysqli_real_escape_string($conn,$_GET['search']);

$result=mysqli_query($conn,"
SELECT applications.id,
users.name,
schemes.title,
applications.status
FROM applications
JOIN users ON users.id=applications.user_id
JOIN schemes ON schemes.id=applications.scheme_id
WHERE users.role!='admin'
AND (
users.name LIKE '%$search%'
OR schemes.title LIKE '%$search%'
OR applications.status LIKE '%$search%'
)
");

}else{

$result=mysqli_query($conn,"
SELECT applications.id,
users.name,
schemes.title,
applications.status
FROM applications
JOIN users ON users.id=applications.user_id
JOIN schemes ON schemes.id=applications.scheme_id
WHERE users.role!='admin'
");

}
$serial = 1;

while($row=mysqli_fetch_assoc($result)){
?>

<tr>

<td><?php echo $serial++; ?></td>

<td><?php echo $row['name']; ?></td>

<td><?php echo $row['title']; ?></td>

<td><?php echo $row['status']; ?></td>

<td>

<?php if(strtolower(trim($row['status'])) != 'approved'){ ?>
<a href="?approve=<?php echo $row['id']; ?>">
<button class="approve-btn">Approve</button>
</a>
<?php } ?>

<?php if(strtolower(trim($row['status'])) != 'rejected'){ ?>
<a href="?reject=<?php echo $row['id']; ?>">
<button class="reject-btn">Reject</button>
</a>
<?php } ?>

<a href="?delete_app=<?php echo $row['id']; ?>">
<button class="delete-btn">Delete</button>
</a>

</td>

</tr>

<?php } ?>

</table>

</div>


<!-- SCHEME LIST -->

<div class="card">

<h3>All Schemes</h3>

<table>

<tr>
<th>ID</th>
<th>Title</th>
<th>Description</th>
<th>Eligibility</th>
<th>Action</th>
</tr>

<?php

$result=mysqli_query($conn,"SELECT * FROM schemes");

$serial = 1;

while($row=mysqli_fetch_assoc($result)){
?>

<tr>

<td><?php echo $serial++; ?></td>

<td><?php echo $row['title']; ?></td>

<td><?php echo $row['description']; ?></td>

<td><?php echo $row['eligibility']; ?></td>

<td>

<a href="?edit=<?php echo $row['id']; ?>">
<button class="edit-btn">Edit</button>
</a>

<a href="?delete=<?php echo $row['id']; ?>">
<button class="delete-btn">Delete</button>
</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</body>
</html>
