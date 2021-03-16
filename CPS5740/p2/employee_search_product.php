<?php include 'dbconfig.php'; ?>
<?php if(employee_or_manager_logged_in()){ ?>
<html>
<head>
  <title>Search Products</title>
</head>
<body>
  <a href='phase2_emp_login.php?logout=true'>Employee Logout</a>
  <form action='employee_display_product.php' method='POST'>
    <table>
      <tr><td colspan=2>Search Product (* for all):</tr>
      <tr><td><input type='text' name='search_value' /></td><td><input type='submit' name='search' /></tr>
    </table>
  </form>
</body>
</html>
<?php } else { header("location: phase2_emp_login.php"); } ?>
