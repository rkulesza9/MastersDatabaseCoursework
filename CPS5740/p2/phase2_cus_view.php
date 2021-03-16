<?php
  include 'dbconfig.php';

  //test if employee/manager is logged in
  if(employee_or_manager_logged_in()){
    $conn = connect_to_db("2019F_kuleszar");
    $sql = "select * from CUSTOMER";
    $stmt = $conn->prepare($sql);
    $stmt->bind_result($customer_id,$login_id,$password,$first_name,$last_name,$tel,$address,$zipcode,$state,$city);
    $stmt->execute();
  }


?>

<html>
  <head>
    <title>View All Customers</title>
    <style>
      table, tr, th, td {
        border-collapse: collapse;
        border: solid black 1px;
      }
    </style>
  </head>
  <body>
    The following customers are in the database:
    <?php if(employee_or_manager_logged_in()) { ?>
    <table>
      <tr><th>ID</th><th>Login</th><th>Password</th><th>Last Name</th><th>First Name</th><th>TEL</th><th>address</th><th>city</th><th>zipcode</th><th>state</th></tr>

      <?php while($stmt->fetch()){ ?>
        <tr><td><?php echo $customer_id;?></td><td><?php echo $login_id; ?></td><td><?php echo $password; ?></td><td><?php echo $last_name; ?></td><td><?php echo $first_name; ?></td><td><?php echo $tel; ?></td>
          <td><?php echo $address; ?></td><td><?php echo $city; ?></td><td><?php echo $zipcode; ?></td><td><?php echo $state; ?></td></tr>
      <?php }
        $stmt->close();
        $conn->close();
      ?>

    </table>
  <?php } else { ?>
    <h1> This page is for employee only. Please login as an employee/manager first.</h1>
  <?php } ?>
  </body>
  <footer>
</footer>
