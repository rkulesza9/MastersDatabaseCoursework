<?php
  include 'dbconfig.php';
  $warning = "";

  if(isset($_GET['logout'])){
    setcookie("user_id","",time() - 3600);
    setcookie("user_role","",time() - 3600);
    header("location: phase2_cus_login.php");
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
        header("location: customer_check_p2.php");
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
        header("location: customer_check_p2.php");
     }
     if(employee_or_manager_logged_in || !isset($_COOKIE['user_id']) || !isset($_COOKIE['user_role'])){
       header("location: customer_check_p2.php");
     }
     ?>
  </body>
  <footer>
  </footer>
</html>
