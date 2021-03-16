
<?php
  include "dbconfig.php";
  $warning = "";

  //log user out
  if(isset($_GET['logout'])){
    setcookie('user_id','',time() - 3600);
    setcookie('user_role','',time() - 3600);
    $warning = "Logout Successful!";
    header("location: phase2_emp_login.php");
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

      $query = "Select login from EMPLOYEE2 where login=?";
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

        //handle password hash
        $password = hash("sha256",$password);

        $query = "Select employee_id, name, role from EMPLOYEE2 where LOWER(login)=LOWER(?) and password=?";
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
    <link rel='stylesheet' type='text/css' href='style.css' />
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
        <tr><td colspan=2><a href='phase2_emp_login.php?logout=true'><?php echo $full_role; ?> logout</a></td></tr>
        <tr><td colspan=2><a href='employee_insert_product.php'>Add products</a></td></tr>
        <tr><td colspan=2><a href='employee_view_vendors.php'>View all vendors</a></td></tr>
        <tr><td colspan=2><a href='employee_search_product.php'>Search & update product</a></td></tr>
      </table>

      <?php if(manager_logged_in()){ ?>
        <form action='manager_view_reports.php' method='post'>
           <p>
             View Reports - period:
             <select name='period'>

               <option value='a' selected>all</option>
               <option value='pw'>past week</option>
               <option value='cm'>current month</option>
               <option value='pm'>past month</option>
               <option value='py'>past year</option>
             </select>
             , by:
             <select name='by'>
               <option value='as' selected>all sales</option>
               <option value='p'>products</option>
               <option value='v'>vendors</option>
             </select>
             <input type='submit' name='report_submit' />
           </p>
        </form>
      <?php } ?>

    <?php } else { ?>
    <form action='phase2_emp_login.php' method='POST'>
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
