<?php
  include 'dbconfig.php';
  $warning = "";

  if(isset($_GET['logout'])){
    setcookie("user_id","",time() - 3600);
    setcookie("user_role","",time() - 3600);
    header("location: phase1_cus_login.php");
  }

  if(isset($_POST['cus_submit'])){
    //check if login is EmptyIterator
    if($_POST['cus_login'] == ""){
      $warning = "The login field cannot be empty.<br>";
    }

    //check if password is Empty
    if($_POST['cus_password'] == ""){
      $warning = $warning."the password field cannot be empty.";
    }

    //if neither field is empty then...
    if($warning == ""){
      $conn = connect_to_db("2019F_kuleszar");
      $query = "select login_id from CUSTOMER where LOWER(login_id)=LOWER(?) and password COLLATE latin1_general_cs =?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("ss",$_POST['cus_login'],$_POST['cus_password']);
      $stmt->bind_result($login_id);
      $stmt->execute();

      //if username or password fails then stop...
      $stmt->store_result();
      $stmt->fetch();
      if($stmt->num_rows == 0){
        $warning = "Authentication Error. Please Try Again.";
      }

      $stmt->close();
      $conn->close();

      //if login is successful, let's go!
      if($warning == ""){
        setcookie("user_id",$_POST['cus_login'],time() + 60*60*5);
        setcookie("user_role","C",time() + 60*60*5);
        header("location: phase1_cus_login.php");
      }
    }
  }

?>

<html>
  <head>
    <title>Customer Login</title>
    <style>
      table {
        border: 1px black solid;
      }
      .warning {
        color:red;
      }
    </style>
  </head>
  <body>
    <?php
    if(customer_logged_in()){
      $ip = $_SERVER['REMOTE_ADDR'];
      if(substr($ip,0,3) == '10.' || substr($ip,0,8) == '131.125.'){
        $from_kean = "You are from Kean Unviersity.";
      } else {
        $from_kean = "You are NOT from Kean University.";
      }

      //get data from DB
      $conn = connect_to_db("2019F_kuleszar");
      $query = "select first_name, last_name, address, city, zipcode from CUSTOMER where login_id=?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("s",$_COOKIE['user_id']);
      $stmt->bind_result($first_name, $last_name, $address, $city, $zipcode);
      $stmt->execute();
      $stmt->fetch();
      $stmt->close();
      $conn->close();

    ?>

    <table>
      <tr><td>Welcome customer:<strong><?php echo $first_name." ".$last_name ?></strong></td></tr>
      <tr><td><?php echo $address.", ".$city.", ".$zipcode ?></td></tr>
      <tr><td><?php echo "Your IP: ".$ip; ?></td></tr>
      <tr><td><?php echo $from_kean; ?></td></tr>
      <tr><td><a href='phase1_cus_login.php?logout=true'>Customer logout</a></td></tr>
      <tr><td><a href='phase1_cus_update.php'>Update my data</a></td></tr>
      <tr><td></td></tr>
      <tr><td><a href='Phase1.php'>project home page</a></td></tr>
    </table>

    <?php } else { ?>
      <form action='phase1_cus_login.php' method='post'>
        <span class='warning'><?php echo $warning; ?></span>
        <table>
          <tr><th colspan=2>Customer Login</th></tr>
          <tr><td>Login ID:</td><td><input type='text' name='cus_login' /></td></tr>
          <tr><td>Password:</td><td><input type='password' name='cus_password' /></td></tr>
          <tr><td><input type='submit' value='Login' name='cus_submit' /></td></tr>
        </table>
      </form>
    <?php } ?>
  </body>
  <footer>
  </footer>
</html>
