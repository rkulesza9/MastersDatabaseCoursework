<?php include 'dbconfig.php'; ?>

<?php
  $works_there = employee_or_manager_logged_in();
 if($works_there){ ?>

<?php
  $warning = "";
  $success = "";
  $submit = $_GET['add_product'];
  $product_name = $_GET['product_name'];
  $description = $_GET['description'];
  $cost = $_GET['cost'];
  $sell_price = $_GET['sell_price'];
  $quantity = $_GET['quantity'];
  $vendor_id = $_GET['vendor'];
  $user_id = $_COOKIE['user_id'];

  $insert = true;

  if(isset($submit)){
    /* no duplicate product name */
    $conn = connect_to_db("2019F_kuleszar");
    $query = "select name from PRODUCT where name=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s",$product_name);
    $stmt->bind_result($pname);
    $stmt->execute();
    $stmt->store_result();
    $stmt->fetch();

    if($stmt->num_rows > 0){
        $insert = false;
        $warning .= "There is already a product with that name.<br>";
    }

    /* negative numbers */
    if($cost < 0 || $sell_price < 0 || $quantity < 0){
      $insert = false;
      $warning .= "Negative numbers are not allowed.<br>";
    }

    /* cost < sell_price */
    if($cost >= $sell_price){
      $insert = false;
      $warning .= "Sell Price must be more than Cost.<br>";
    }

    if($insert){
      $query = "insert into PRODUCT (employee_id,name,description,cost,sell_price,quantity,vendor_id) values ((select employee_id from CPS5740.EMPLOYEE2 where name=?),?,?,?,?,?,?)";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("sssddii",$user_id,$product_name,$description,$cost,$sell_price,$quantity,$vendor_id);
      $stmt->execute();
      //echo    "insert into PRODUCT (employee_id,name,description,cost,sell_price,quantity,vendor_id) values ((select employee_id from CPS5740.EMPLOYEE2 where name='$user_id'),'$product_name','$description',$cost,$sell_price,$quantity,$vendor_id)";
      if($stmt->affected_rows > 0){
        $success .= "The product $product_name was successfully inserted!<br>";
      } else {
        $warning .= "For some reason $product_name was not inserted! Please Try Again.<br>";
      }
    }
  }

?>

    <html>
      <head>
        <title>Add Products</title>
        <link rel='stylesheet' type='text/css' href='style.css' />
      </head>
      <body>
        <div class='success'><?php echo $success; ?></div>
        <div class='warning'><?php echo $warning; ?></div>
        <form action='phase2_emp_add_products.php' method='GET'>
        <table class='bb1s'>
          <tr><td colspan=2><a href='phase2_emp_login.php?logout=true'>Employee Logout</a></td></tr>
          <tr><td colspan=2><h1>Add Products</h1></td></tr>
          <tr><td>Product Name:</td><td><input type='text' name='product_name'/></td></tr>
          <tr><td>description:</td><td><input type='text' name='description' /></td></tr>
          <tr><td>Cost:</td><td><input type='text' name='cost' /></td></tr>
          <tr><td>Sell Price:</td><td><input type='text' name='sell_price' /></td></tr>
          <tr><td>Quantity:</td><td><input type='text' name='quantity' /></td></tr>
          <tr><td>Select vendor:</td><td><?php dropdown_vendors(); ?></td></tr>
          <tr><td colspan=2><input type='submit' name='add_product' /></td></tr>
        </table>
      </form>
      <a href='phase2_emp_login.php'>Employee Home Page</a><br>
      <a href='phase2.php'>Project Home Page</a>
      </body>
      <footer>
      </footer>
    </html>

<?php }else{ header("location: phase2_emp_login.php"); } ?>
