<?php
  include 'dbconfig.php';

  $conn = connect_to_db("CPS5740");
  $query = "select * from EMPLOYEE2;";
  $stmt = $conn->prepare($query);
  $stmt->bind_result($employee_id, $login, $password, $name, $role);
  $stmt->execute();

?>

<html>
  <head>
    <title>View All Employees</title>
    <style>
      table, tr, th, td {
        border-collapse: collapse;
        border: solid black 1px;
      }
    </style>
  </head>
  <body>
    <h3>The following employees are in the database:</h3>
    <table>
      <tr><th>Employee ID</th><th>Login</th><th>Password</th><th>Name</th><th>Role</th></tr>
      <?php
        while($stmt->fetch()){
          echo<<<html
            <tr><td>$employee_id</td><td>$login</td><td>$password</td><td>$name</td><td>$role</td></tr>
html;
        }
        $stmt->close();
        $conn->close();
       ?>
    </table>
  </body>
  <footer>
  </footer>
</html>
