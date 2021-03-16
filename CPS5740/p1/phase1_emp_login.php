<?php
  include "dbconfig.php";
  $warning = "";

  //log user out
  if(isset($_GET['logout'])){
    setcookie('user_id','',time() - 3600);
    setcookie('user_role','',time() - 3600);
    $warning = "Logout Successful!";
    header("location: phase1_emp_login.php");
  }

  if(isset($_POST['emp_submit'])){

    //is login or password empty?
    if($_POST['emp_login'] == ""){
      $warning = "Login cannot be empty.<br />";
    }
    if($_POST['emp_password'] == ""){
      $warning = $warning . "Password cannot be empty.";
    }

    //if there has been no problems yet, let's continue
    if($warning == ""){
      $conn = connect_to_db("CPS5740");
      $username = $_POST['emp_login'];
      $password = $_POST['emp_password'];

      $query = "Select login from EMPLOYEE where login=?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("s",$username);
      $stmt->bind_result($login_result);
      $stmt->execute();

      $stmt->store_result();
      $stmt->fetch();
      if($stmt->num_rows == 0){
        $stmt->close();
        $conn->close();
        $warning = "Login ID " . $username . " does not exist in the database!";
      } else {
        $stmt->close();
        $query = "Select employee_id, name, role from EMPLOYEE where LOWER(login)=LOWER(?) and password COLLATE latin1_general_cs =?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss",$username,$password);
        $stmt->bind_result($employee_id, $employee_name, $role);
        $stmt->execute();

        $stmt->store_result();
        $stmt->fetch();
        if($stmt->num_rows == 0){
          $stmt->close();
          $conn->close();
          $warning = "Login ID " . $username . " exists, but the password was incorrect.";
        }
      }
    }

    //login was successful! Set the cookie!
    if($warning == ""){
      $stmt->close();
      $expire = time() + 60*60*5; //5 hours;

      setcookie('user_role',$role,$expire);
      setcookie('user_id',$employee_name,$expire);
      header("location: #");
    }
  }
?>

<html>
  <head>
    <title>Employee Login</title>
    <style>
      .bb1s {
        border: black 1px solid;
      }
      .warning {
        color: red;
      }
    </style>
  </head>
  <body>
    <?php if(employee_or_manager_logged_in()){
      //$ip, $from_kean
      if($_COOKIE['user_role'] == 'M') $full_role = "Manager";
      elseif($_COOKIE['user_role'] == 'E') $full_role = "Employee";
      else $full_role = 'Unknown';

      $ip = $_SERVER['REMOTE_ADDR'];
      if(substr($ip,0,3) == '10.' || substr($ip,0,8) == '131.125.') $from_kean = "You are from Kean domain.";
      else $from_kean = "You are NOT from Kean domain.";

      $employee_name = $_COOKIE['user_id'];
    ?>
      <table class='bb1s'>
        <tr><td>Your IP:</td><td><?php echo $ip; ?></td></tr>
        <tr><td colspan=2><?php echo $from_kean; ?></td></tr>
        <tr><td>Welcome <?php echo $full_role; ?>:</td><td><strong><?php echo $employee_name; ?></strong></td></tr>
        <tr><td colspan=2><a href='phase1_emp_login.php?logout=true'><?php echo $full_role; ?> logout</a></td></tr>
      </table>

    <?php } else { ?>
    <form action='phase1_emp_login.php' method='POST'>
      <span class='warning'><?php echo $warning; ?></span>
      <table class='bb1s'>
        <tr><th>Login ID:</th><td><input type='text' name='emp_login' \></td></tr>
        <tr><th>Password:</th><td><input type='password' name='emp_password' \></td></tr>
        <tr><td><input type='submit' name='emp_submit' /></td></tr>
      </table>
    </form>
  <?php  } ?>
  </body>
  <footer>
  </footer>
</html>
