<?php include 'dbconfig.php'; ?>

<?php if(employee_or_manager_logged_in()){ ?>

<?php
  $warning = "";
  $success = "";
  $submit = $_POST['update_product'];
  if(isset($submit)){
    $product_names = $_POST['product_name'];
    $product_descr = $_POST['product_descr'];
    $product_cost = $_POST['product_cost'];
    $product_sellprice = $_POST['product_sellprice'];
    $product_quantity = $_POST['product_quantity'];
    $vendor_id = $_POST['vendor'];
    $product_id = $_POST['product_id'];
    $num_successful = 0;
      for($x=0; $x<count($product_id); $x++){
        $insert = true;
        $pname = $product_names[$x];
        $pdescr = $product_descr[$x];
        $pcost = $product_cost[$x];
        $psellprice = $product_sellprice[$x];
        $pquantity = $product_quantity[$x];
        $vid = $vendor_id[$x];
        $pid = $product_id[$x];
        $username = $_COOKIE['user_id'];

        /* no duplicate names allowed */
        $conn = connect_to_db("2019F_kuleszar");
        $query = "select product_id from PRODUCT where name=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s",$pname);
        $stmt->bind_result($output_pid);
        $stmt->execute();
        $stmt->store_result();

        $duplicate = 0;
        while($stmt->fetch()){
          if($output_pid <> $pid) $duplicate = 1;
        }

        if($duplicate == 1){
            $insert = false;
            $warning .= "There is already a product with the name $pname.<br>";
        }
        $stmt->close();
        $conn->close();

        /* negative numbers */
        if($pcost < 0 || $psellprice < 0 || $pquantity < 0){
          $insert = false;
          $warning .= "Negative numbers are not allowed for product $pname.<br>";
        }

        /* cost < sell_price */
        if($pcost >= $psellprice){
          $insert = false;
          $warning .= "Sell Price must be more than Cost for product $pname.<br>";
        }

        if($insert){
          $conn = connect_to_db("2019F_kuleszar");
          $query = "update PRODUCT set description=?, name=?, vendor_id=?, cost=?, sell_price=?, quantity=?, employee_id=(select employee_id from CPS5740.EMPLOYEE2 where name=?) where product_id=? and (description<>? OR  name<>? OR vendor_id<>? OR cost<>? OR sell_price<>? OR quantity<>?)";
          $stmt = $conn->prepare($query);
          $stmt->bind_param("ssiddisissiddi",$pdescr, $pname, $vid, $pcost, $psellprice, $pquantity, $username, $pid, $pdescr, $pname, $vid, $pcost, $psellprice, $pquantity);
          $stmt->execute();

          if($stmt->affected_rows > 0){
            $success .= "Product ID $pid was successfully updated!<br>";
            $num_successful = $num_successful + 1;
          }
          if($stmt->affected_rows < 0){
            $warning .= "Product ID $pid could not be updated.<br>";
          }

          $stmt->close();
        }
      }
      $success .= "$num_successful products were updated.<br>";
      $conn->close();
  }

?>

<html>
  <head>
    <title>Display and Update Products</title>

    <link rel='stylesheet' type='text/css' href='style.css' />
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
    google.charts.load('current', {'packages':['table']});
    google.charts.setOnLoadCallback(drawTable);

    function drawTable() {
      var data = new google.visualization.DataTable();
      data.addColumn('number', 'Product ID');
      data.addColumn('string', 'Product Name');
      data.addColumn('string', 'Description');
      data.addColumn('string', 'Cost');
      data.addColumn('string', 'Sell Price');
      data.addColumn('string', 'Available Quantity');
      data.addColumn('string', 'Vendor Name');
      data.addColumn('string', 'Last Update By');

      <?php
        if(isset($_POST['search'])){
        $conn = connect_to_db("2019F_kuleszar");
        $apple = mysqli_escape_string($conn,$_POST['search_value']);
        $query = "call p_SEARCH_PRODUCTS('$apple')";
        $result = $conn->query($query);


      ?>

      data.addRows([
        <?php
        function input_tag($type,$name,$value){
          return "<input type=\"$type\" name=\"$name\" value=\"$value\" />";
        }
        function str_select_vendor($vendor){
          $conn = connect_to_db("CPS5740");
          $query = "SELECT vendor_id, name FROM VENDOR";
          $stmt = $conn->prepare($query);
          $stmt->bind_result($vendor_id, $name);
          $stmt->execute();
          $str = "<select name=\"vendor[]\">";
          $sel = "";
          while($stmt->fetch()){
            if($vendor == $name) $sel = "selected";
            $str .= "<option value=\"$vendor_id\" $sel>$name</option>";
            $sel = "";
          }
          $str .= "</select>";
          $stmt->close();
          $conn->close();
          return $str;
        }

        while($row = $result->fetch_assoc()){
          $pid = $row['product_id'];
          $employee = $row['employee_name'];
          $pname = input_tag('text',"product_name[]",$row['name']);
          $pdescr = input_tag('text',"product_descr[]",$row['description']);
          $pcost = input_tag('text',"product_cost[]",$row['cost']);
          $psellprice = input_tag('text',"product_sellprice[]",$row['sell_price']);
          $pquantity = input_tag('text',"product_quantity[]",$row['quantity']);
          $vendor = str_select_vendor($row['vendor_name']).input_tag('hidden','product_id[]',$row['product_id']);
          echo "[$pid, '$pname', '$pdescr', '$pcost', '$psellprice', '$pquantity', '$vendor', '$employee'],\n ";
        }
        $conn->close();
      } else {

      }
        ?>
      ]);

      var table = new google.visualization.Table(document.getElementById('table_div'));

      table.draw(data, {showRowNumber: true, /*width: '100%', height: '100%',*/ allowHtml: true});
    }
    </script>
    <style>
    </style>
  </head>
  <body>
    <a href='phase2_emp_login.php?logout'>Employee Logout</a>
    <h1>Product List For Search: <?php echo $_POST['search_value']; ?></h1>
    <div class='success'><?php echo $success; ?></div>
    <div class='warning'><?php echo $warning; ?></div>
    <?php if(!isset($_POST['update_product'])) { ?>
    <form action='employee_display_product.php' method='POST'>
    <?php } ?>
      <div id='table_div'>
      </div>
      <?php if(!isset($_POST['update_product'])) { ?>
      <input type='submit' name='update_product' value='Update Products' />
      </form>
    <?php } ?>
    <table>
      <tr><td><a href='phase2_emp_login.php'>Employee Home</a></td></tr>
      <tr><td><a href='../'>Project Home</a></td></tr>
    </table>
  </body>
</html>
<?php } else { header("location: phase2_emp_login.php"); } ?>
