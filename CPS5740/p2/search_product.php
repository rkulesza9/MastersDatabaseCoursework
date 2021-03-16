<?php include 'dbconfig.php';
$customer = customer_logged_in();?>

<html>
<head>
  <title>Search Product</title>
  <link rel='stylesheet' type='text/css' href='style.css' />
  <style>
    table, tr, th, td {
      border: 1px solid black;
      border-collapse: collapse;
    }
  </style>
</head>
<body>
  <?php if(!$customer){ echo "<a href='phase2_cus_login.php'>Customer Login</a>"; }
        else { echo "<a href='phase2_cus_login.php?logout'>Customer Logout</a>"; }
    $search = $_GET['search'];
    $search_value = $_GET['search_value'];

    if(isset($search)){
     ?>
     <h1>Available Product List For Search: <?php echo $search_value; ?></h1>
  <?php
      if($customer){
        $login_id = $_COOKIE['user_id'];
        $conn = connect_to_db("2019F_kuleszar");
        $query = "call p_save_keywords((select customer_id from CUSTOMER where login_id=?), ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss",$login_id,$search_value);
        $stmt->execute();
        $stmt->close();
        $conn->close();
      }

      $conn = connect_to_db("2019F_kuleszar");
      $login_id = mysqli_escape_string($conn,$_COOKIE['user_id']);
      $apple = mysqli_escape_string($conn,$search_value);
      $query = "call p_SEARCH_PRODUCTS('$apple')";
      $stmt = $conn->prepare($query);
      $result = $conn->query($query);

      // product_id, description, name, vendor_id, cost, sell_price, quantity, employee_id, employee_name, vendor_name
      // name, descrition, sell_price, quantity, /order quantity/, vendor_name
      if($customer) {
        echo "<form method='GET' action='customer_order.php'>";
      }
      echo "<table>";
      if($customer){ ?>
              <tr><th>Product Name</th><th>Description</th><th>Sell Price</th><th>Available Quantity</th><th>Order Quantity</th><th>Vendor Name</th></tr>
<?php   while($row = $result->fetch_assoc()){
          echo "<tr>";
          echo "<td><input type='hidden' name='product_name[]' value='".$row['name']."' />".$row['name']."</td>";
          echo "<td>".$row['description']."</td>";
          echo "<td><input type='hidden' name='unit_prices[]' value='".$row['sell_price']."'>".$row['sell_price']."</td>";
          echo "<td><input type='hidden' name='current_quantity[]' value='".$row['quantity']."' \>".$row['quantity']."</td>";
          echo "<td><input type='hidden' name='product_id[]' value='".$row['product_id']."' /><input type='text' name='order_quantity[]' /></td>";
          echo "<td>".$row['vendor_name']."</td>";
          echo "</tr>";
        }

        echo "</table>";
        echo "<input type='submit' name='place_order' value='Place Order' />";
        echo "</form>";
      }else { ?>
                      <tr><th>Product Name</th><th>Description</th><th>Sell Price</th><th>Available Quantity</th><th>Vendor Name</th></tr>
<?php while($row = $result->fetch_assoc()){
          echo "<tr>";
          echo "<td>".$row['name']."</td>";
          echo "<td>".$row['description']."</td>";
          echo "<td>".$row['sell_price']."</td>";
          echo "<td>".$row['quantity']."</td>";
          echo "<td>".$row['vendor_name']."</td>";
          echo "</tr>";
        }

        echo "</table>";
      }

      $conn->close();

    } else { ?>
      <H1>Search Products:</h1>
      <form action='search_product.php' method='GET'>
        <table>
          <tr><td colspan=2>Search Product (* for all):</tr>
          <tr><td><input type='text' name='search_value' /></td><td><input type='submit' name='search' /></tr>
        </table>
      </form>

<?php    }
  if($customer){
  ?>
  <br>
  <a href='phase2_cus_login.php'>Customer Home Page</a>
<?php } ?>
<br><a href='phase2.php'>Project Home Page</a>
</body>
<footer>
</footer>
</html>
