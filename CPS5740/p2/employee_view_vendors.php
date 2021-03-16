<?php include 'dbconfig.php'; ?>

<?php if(employee_or_manager_logged_in()){ ?>

<html>
  <head>
    <title>View Vendors</title>
    <style>
      body {
        text-align: center;
      }
      #map-canvas {
        display:inline-block;
      }
    </style>
    <link rel='stylesheet' type='text/css' href='style.css' />
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
    google.charts.load('current', {'packages':['table']});
    google.charts.setOnLoadCallback(drawTable);

    function drawTable() {
      var data = new google.visualization.DataTable();
      data.addColumn('number', 'ID');
      data.addColumn('string', 'Name');
      data.addColumn('string', 'Address');
      data.addColumn('string', 'City');
      data.addColumn('string', 'State');
      data.addColumn('string', 'Zipcode');
      data.addColumn('string', 'Location');

      data.addRows([
        <?php
            $conn = connect_to_db("CPS5740");
            $query = 'SELECT vendor_id, name, address, city, state, zipcode, longitude, latitude from VENDOR';
            $stmt = $conn->prepare($query);
            $stmt->bind_result($id, $name, $address, $city, $state, $zipcode, $longitude, $latitude);
            $stmt->execute();

            $map_str = "";
            while($stmt->fetch()){
              $location = "(".$longitude.",".$latitude.")";
              echo "[$id,'$name','$address','$city','$state','$zipcode','$location'],\n";
              $map_str .= "['$id','$name',$latitude,$longitude],\n";
            }

            $stmt->close();
            $conn->close();
        ?>
      ]);

      var table = new google.visualization.Table(document.getElementById('table_div'));

      table.draw(data, {showRowNumber: true, /*width: '100%', height: '100%',*/ allowHtml: true});
    }
    </script>

    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <script>

    var i = 0;

    function initialize() {
        var mapOptions = {
                zoom: 4,

                center: new google.maps.LatLng(39.521741, -96.848224),
                mapTypeId: google.maps.MapTypeId.ROADMAP
       };

       var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

       var infowindow = new google.maps.InfoWindow();

    var markerIcon = {
      scaledSize: new google.maps.Size(80, 80),
      origin: new google.maps.Point(0, 0),
      anchor: new google.maps.Point(32,65),
      labelOrigin: new google.maps.Point(40,33)
    };
        var location;
        var mySymbol;
        var marker, m;
        var MarkerLocations= [
<?php
      echo $map_str;
?>
        ];

    for (m = 0; m < MarkerLocations.length; m++) {

        location = new google.maps.LatLng(MarkerLocations[m][2], MarkerLocations[m][3]),
        marker = new google.maps.Marker({
      map: map,
      position: location,
      icon: markerIcon,
      label: {
      text: MarkerLocations[m][0] ,
    color: "black",
        fontSize: "16px",
        fontWeight: "bold"
      }
    });

      google.maps.event.addListener(marker, 'click', (function(marker, m) {
        return function() {
          infowindow.setContent("Vendor Name: " + MarkerLocations[m][1]);
          infowindow.open(map, marker);
        }
      })(marker, m));
    }
    }
    google.maps.event.addDomListener(window, 'load', initialize);;

    </script>
  </head>
  <body>
    <h1>The following vendors are in the database:</h1>
    <div id='table_div'>
    </div><br>
    <div id="map-canvas" style="height: 400px; width: 720px;"></div>
  </body>
  <footer>
  </footer>
</html>
<?php }else{ header("location: phase2_emp_login.php"); } ?>
