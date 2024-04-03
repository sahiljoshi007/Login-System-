<?php
$showAlert=false;
$showError=false;
$mandatory_error=false;

function validateInput($fieldName, $pattern, $errorMessage) {
    global $errors, $mandatory_error;
    if (empty($_POST[$fieldName]) || trim($_POST[$fieldName]) === '') {
        $errors[$fieldName] = "Please enter your $fieldName";
        $mandatory_error = "Please fill out mandatory fields(*) and try again...";
    } elseif (!preg_match($pattern, $_POST[$fieldName])) {
        $errors[$fieldName] = $errorMessage;
    }
}

if($_SERVER["REQUEST_METHOD"]=="POST")
{
  
  $errors = array();

  validateInput('name', "/^[a-zA-Z-' ]*$/", "Only letters and white space allowed");
  validateInput('email', "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", "Invalid email format");
  validateInput('state', "/^[a-zA-Z-' ]*$/", "Only letters and white space allowed");
  validateInput('city', "/^[a-zA-Z-' ]*$/", "Only letters and white space allowed");
  validateInput('pincode', "/^[0-9]*$/", "Only numeric value is allowed");
  validateInput('username', "/^[A-Za-z0-9_\.]+$/", "Your username must be in letters with either a number, underscore or a dot");
  validateInput('password', "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", "Password must have at least 8 characters with minimum 1 uppercase, 1 lowercase, 1 number, and 1 special character");

  // Validate confirm password
  if(empty($_POST['cpassword'])) 
  {
    $errors['cpassword'] = "Please confirm your password";
    $mandatory_error="please fill out mandatory fields(*) and try again...";
  }

  if(empty($errors))
  {
    try {
        include 'partials/_dbconnect2.php';
        $username = $_POST["username"];
        $password = $_POST["password"];
        $cpassword = $_POST["cpassword"];
        $name=$_POST["name"];
        $email=$_POST["email"];
        $state= $_POST["state"];
        $city= $_POST["city"];
        $pincode= $_POST["pincode"];

        // Check if username already exists
        $stmt = $conn->prepare("SELECT * FROM `users` WHERE `username` = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $numExistRows = $stmt->rowCount();

        if($numExistRows > 0) {
            $showError= "Username already exists";
        } else {
            if(($password==$cpassword)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO `users` (`name`, `username`, `password`, `email`, `Zip`, `state`, `city`, `Date`) VALUES (:name, :username, :hash, :email, :pincode, :state, :city, current_timestamp())";        
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':hash', $hash);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':pincode', $pincode);
                $stmt->bindParam(':state', $state);
                $stmt->bindParam(':city', $city);
                $stmt->execute();
                $showAlert=true;
            } else {
                $showError= "Passwords do not match";
            }
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
  }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title >Hello, world!</title>
    <style>
      .err{
        color: #D30000;
      }
    </style>
  </head>

  <body>  
    <?php
        include 'partials/_nav2.php';
    ?>

  <?php
    if($showAlert)
    {
      echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Hurray!</strong> Your account has been created and now you can login.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
    }
    
    if($showError)
    {
      echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong> '. $showError .'
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
    }
    if($mandatory_error)
    {
      echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Error!</strong> '. $mandatory_error .'
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
    }
  ?>
    <div class="container">
      <h1 class="text-center mt-3 mb-5">Signup to our Website!</h1>

      <form action="/loginSystem2/signup2.php" method="post" class="d-flex flex-column  align-items-center">

        <div class="col-md-6 mb-3">
          <label for="name" class="form-label">Name<span class="err"> *</span></label>
          <input type="text" maxlength="15" class="form-control"  id="name" name="name" >
          <span class="err"><?php echo  isset($errors['name']) ? $errors['name'] : "";?></span>
        </div>

        <div class="col-md-6 mb-3">
          <label for="email" class="form-label">Email<span class="err"> *</span></label>
          <input type="email" maxlength="30" class="form-control" id="email" name="email">
          <span class="err"><?php echo  isset($errors['email']) ? $errors['email'] : "";?></span>
        </div>
    
        
        <div class="col-md-6 mb-3">
            <label for="state" class="form-label">State<span class="err"> *</span></label>
            <select class="form-select" id="state" name="state">
                <option selected disabled>Select State</option>
                <?php
                  $states = array("Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chhattisgarh",
                  "Goa", "Gujarat", "Haryana", "Himachal Pradesh", "Jharkhand", "Karnataka", "Kerala",
                    "Madhya Pradesh", "Maharashtra", "Manipur", "Meghalaya", "Mizoram", "Nagaland",
                    "Odisha", "Punjab", "Rajasthan", "Sikkim", "Tamil Nadu", "Telangana", "Tripura", 
                    "Uttar Pradesh", "Uttarakhand", "West Bengal");
                  foreach ($states as $state) {
                      echo "<option value='$state'>$state</option>";
                  }
                ?>
            </select>
            <span class="err"><?php echo  isset($errors['state']) ? $errors['state'] : "";?></span>
        </div>

        <div class="col-md-6 mb-3">
          <label for="city" class="form-label">City<span class="err"> *</span></label>
          <input type="text" class="form-control" id="city" name="city" >
          <span class="err"><?php echo  isset($errors['city']) ? $errors['city'] : ""; ?></span>
        </div>


        <div class="col-md-6 mb-3">
          <label for="pincode" class="form-label">Pincode<span class="err"> *</span></label>
          <input type="text" class="form-control" id="pincode" name="pincode" >
          <span class="err"><?php echo  isset($errors['pincode']) ? $errors['pincode'] : "";?></span>
        </div>  

        <div class="mb-3 col-md-6" >
          <label for="username" class="form-label">Username<span class="err"> *</span></label>
          <input type="text" maxlength="12" class="form-control" id="username" name="username" >
          <span class="err"><?php echo isset($errors['username']) ? $errors['username'] : "";?></span>  
        </div>

        <div class="mb-3 col-md-6">
          <label for="password" class="form-label">Password<span class="err"> *</span></label>
          <input type="password" maxlength="20" class="form-control" id="password" name="password" >
          <span class="err"><?php echo isset($errors['password']) ? $errors['password'] : "";?></span>
        </div>

        <div class="mb-3 col-md-6">
          <label for="cpassword" class="form-label">Confirm Password<span class="err"> *</span></label>
          <input type="password" maxlength="20" class="form-control" id="cpassword" name="cpassword" >
          <div id="emailHelp" class="form-text">Make sure to type the same Password.</div>
          <span class="err"><?php echo  isset($errors['cpassword']) ? $errors['cpassword'] : "";?></span>
        </div>

        
        <button type="submit" class="btn btn-primary mb-5">Signup</button>
    </form>
    </div>


    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>

