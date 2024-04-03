<?php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    header("location: login2.php");
    exit;
}

// Include database connection
include 'partials/_dbconnect2.php';

// Retrieved user details from session
$username = $_SESSION['username'];

// Fetch user details from database based on username
$sql = "SELECT * FROM users WHERE username = :username";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!doctype html>
<html lang="en">
<head>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
          crossorigin="anonymous">

    <title>Welcome - <?php echo $_SESSION['username']?> </title>
</head>
<body>
<?php include 'partials/_nav2.php'; ?>


<div class="container mt-3">
    <div class="alert alert-success" role="alert">
        <h4 class="alert-heading">Welcome - <?php echo $_SESSION['username']?>!</h4>
        <p>Hey how are you doing? Welcome to iSecure. You are logged in as - <?php echo $_SESSION['username']?> Thanks,
            you successfully read this important alert message.</p>
        <hr>
        <p class="mb-0">Whenever you need to, be sure to logout <a href="/loginSystem/logout.php">using this link.</a>
        </p>
    </div>
</div>

<div class="container">

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3 text-center">User Details</h5>
            <p class="card-text">Name: <?php echo $row['name']; ?></p>
            <p class="card-text">Username: <?php echo $row['username']; ?></p>
            <p class="card-text">Email: <?php echo $row['email']; ?></p>
            <p class="card-text">State: <?php echo $row['state']; ?></p>
            <p class="card-text">City: <?php echo $row['city']; ?></p>
            <p class="card-text">Pincode: <?php echo $row['Zip']; ?></p>
        </div>
    </div>
    <a href="logout2.php" class="btn btn-primary mt-3">Logout</a>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>
</html>
