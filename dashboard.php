<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "new_library";


$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/font-awesome/css/all.min.css">
  <!-- Bootstrap core CSS -->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <!-- Your custom styles (optional) -->
  <link href="assets/css/style.min.css" rel="stylesheet">
  <link href="assets/css/jquery.dataTables.min.css" rel="stylesheet">
  <link href="assets/css/select2.min.css" rel="stylesheet">
  <link href="assets/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
  <link href="assets/css/custom.css" rel="stylesheet">
  
  <?php
    include 'topbar.php';
    include 'navbar.php';
  ?>
</head>
<body>
  <br><br><br><br>
  <div class="container">
    <div class="row">
      <div class="col-md-12 mx-auto">
        <div class="row">
          <div class="card bg-success dash_total text-white col-md-3 mr-4 float-left">
            <center>
              <h4><b>Total Books</b></h4>
              <hr>
              <h3><b><?php echo $conn->query("SELECT * FROM books")->num_rows ?></b></h3>
            </center>
          </div>
          <div class="card bg-info dash_total text-white col-md-3 mr-4 float-left">
            <center>
              <h4><b>Total Borrowed Books</b></h4>
              <hr>
              <h3><b><?php echo $conn->query("SELECT * FROM borrowed_books")->num_rows ?></b></h3>
            </center>
          </div>
          <div class="card bg-warning dash_total text-white col-md-3 mr-4 float-left">
            <center>
              <h4><b>Total Borrowers</b></h4>
              <hr>
              <h3><b><?php echo $conn->query("SELECT * FROM borrowers ")->num_rows ?></b></h3>
            </center>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS, Popper.js, and jQuery -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <!-- SCRIPTS -->
  <!-- JQuery -->
  <script type="text/javascript" src="assets/js/jquery-3.4.1.min.js"></script>
  <!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="assets/js/jquery.dataTables.min.js"></script>
  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="assets/js/mdb.min.js"></script>
  <script type="text/javascript" src="assets/js/select2.min.js"></script>
  <script type="text/javascript" src="assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
  <script type="text/javascript" src="assets/font-awesome/js/all.min.js"></script>
</body>
</html>
