<?php
  include 'dbconfig.php';
  $success = "";

  //customer is logged in, but has not pressed the submit button
  if(customer_logged_in()){
    $login_id = $_COOKIE['user_id'];

    $conn = connect_to_db("2019F_kuleszar");
    $query = "select * from CUSTOMER where LOWER(login_id)=LOWER(?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s",$login_id);
    $stmt->bind_result($customer_id, $login_id, $password, $first_name, $last_name, $tel ,$address, $zipcode, $state, $city);
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
    $conn->close();

  }

  //submit button was pressed
  if(customer_logged_in() & isset($_POST['submit'])){
    $customer_id = $_POST['customer_id'];
    $login_id = $_COOKIE['user_id'];
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $tel = $_POST['tel'];
    $address = $_POST['address'];
    $zipcode =$_POST['zipcode'];
    $state = $_POST['cus_state'];
    $city = $_POST['city'];
    $conn = connect_to_db("2019F_kuleszar");
    $query = "Update CUSTOMER set password=?, first_name=?, last_name=?, TEL=?, address=?, zipcode=?, state=?, city=? where login_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssss",$password,$first_name,$last_name,$tel,$address,$zipcode,$state,$city,$login_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    $success = "Update was successful!<br>";
  }

?>

<html>
  <head>
    <title>Update My Data</title>
    <style>
      .readonly {
        background-color: yellow;
      }
      .successful_update {
        color: blue;
      }
    </style>
    <style>
      table, tr, th, td {
        border: 1px solid black;
        border-collapse: collapse;
      }
    </style>
  </head>
  <body>
    <?php if(customer_logged_in()){ ?>
      <span class='successful_update'><?php echo $success; ?></span>
      <a href='phase1_cus_login.php?logout=true'>Customer logout</a>
      <form action='phase1_cus_update.php' method='post'>
        <table>
          <tr><th>Customer ID</th><th>Login ID</th><th>password</th><th>Last Name</th><th>First Name</th><th>TEL</th><th>address</th><th>city</th><th>zipcode</th><th>state</th></tr>

          <tr>
            <td><input type='text' class='readonly' name='customer_id' value='<?php echo $customer_id; ?>' readonly/></td>
            <td><input type='text' class='readonly' name='login_id' value='<?php echo $login_id; ?>' readonly/></td>
            <td><input type='text' name='password' value='<?php echo $password; ?>' /></td>
            <td><input type='text' name='last_name' value='<?php echo $last_name; ?>' /></td>
            <td><input type='text' name='first_name' value='<?php echo $first_name; ?>' /></td>
            <td><input type='text' name='tel' value='<?php echo $tel; ?>' /></td>
            <td><input type='text' name='address' value='<?php echo $address; ?>' /></td>
            <td><input type='text' name='city' value='<?php echo $city; ?>' /></td>
            <td><input type='text' name='zipcode' value='<?php echo $zipcode; ?>' /></td>
            <td><?php state_select($state); ?></td>
          </tr>
        </table>
        <input type='submit' value='Update Information' name='submit' /><br>
        <a href='phase1_cus_login.php'>Customer's home page</a><br>
        <a href='phase1.php'>project home page</a>
      </form>
    <?php } else { ?>
      <h1>Please Log In First!</h1>
    <?php } ?>
  </body>
  <footer>
  </footer>
</html>
