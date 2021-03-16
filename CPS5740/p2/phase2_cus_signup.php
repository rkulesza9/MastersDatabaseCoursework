<?php
  include 'dbconfig.php';
  $warning = "";
  $success = "";
  if(isset($_POST['cus_submit'])){

    //missing fields
    if(isset($_POST['cus_login']) || isset($_POST['cus_password1']) || isset($_POST['cus_password2']) || isset($_POST['cus_fname']) || isset($_POST['cus_lname']) || isset($_POST['cus_tel']) || isset($_POST['cus_addr']) || isset($_POST['cus_city']) || isset($_POST['cus_zip']) || isset($_POST['cus_state'])){
      //check password
      if($_POST['cus_password1'] != $_POST['cus_password2']){
        $warning = "The passwords you entered did not match.";
      }
    }else {
      $warning = "All Fields Must Be Filled Out!";
    }

    //nothing bad :) insert the new record
    if($warning == ""){
      $conn = connect_to_db("2019F_kuleszar");
      $query = "insert into CUSTOMER (login_id, password, first_name, last_name, TEL, address, city, state, zipcode) values (?,?,?,?,?,?,?,?,?)";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("sssssssss",$_POST['cus_login'],$_POST['cus_password1'],$_POST['cus_fname'],$_POST['cus_lname'],$_POST['cus_tel'],$_POST['cus_addr'],$_POST['cus_city'],$_POST['cus_state'],$_POST['cus_zip']);
      $stmt->execute();
      if($stmt->affected_rows <= 0){
        $warning = "That username was taken!";
      } else {
        $success = "You have been signed up successfully!";
      }
      $stmt->close();

    }
  }
?>
<html>
  <head>
      <title>CUSTOMER Sign Up</title>
      <style>
        .warning {
          color: red;
        }
        .success {
          color: blue;
        }
      </style>
  </head>
  <body>
    <h1>Customer Sign Up</h1>
    <?php if($success == ""){ ?>
    <span class='warning'><?php echo $warning; ?></span>
    <form action='phase2_cus_signup.php' method='POST'>
      <table>
        <tr><td>Login ID: </td><td><input type='text' name='cus_login' required /></td></tr>
        <tr><td>Password: </td><td><input type='password' name='cus_password1' required /></td></tr>
        <tr><td>Retype Password: </td><td><input type='password' name='cus_password2' required /></td></tr>
        <tr><td>First Name: </td><td><input type='text' name='cus_fname' required /></td></tr>
        <tr><td>Last Name: </td><td><input type='text' name='cus_lname' required /></td></tr>
        <tr><td>TEL: </td><td><input type='text' name='cus_tel' required /></td></tr>
        <tr><td>address: </td><td><input type='text' name='cus_addr' required /></td></tr>
        <tr><td>city: </td><td><input type='text' name='cus_city' required /></td></tr>
        <tr><td>zipcode: </td><td><input type='text' name='cus_zip' required /></td></tr>
        <tr><td>state: </td><td>
          <?php echo state_select("NJ"); ?> </td>
        <tr><td><input type='submit' value='Sign Up' name='cus_submit' /></td></tr>
      </table>
    </form>
  <?php } else { ?>
    <span class='success'><?php echo $success; ?></span>
  <?php } ?>
  </body>
  <footer>
  </footer>
</html>
</td>
