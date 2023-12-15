<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "new_library";


$mysqli = new mysqli($host, $username, $password, $database);


if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {

    $borrower_id = $_POST["borrower_id"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $address = $_POST["address"];
    $contact_number = $_POST["contact_number"];
    $email = $_POST["email"];

  
    $stmt = $mysqli->prepare("CALL Insert_Borrower(?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $borrower_id, $first_name, $last_name, $address, $contact_number, $email);
    $stmt->execute();

  
    $stmt->close();

   
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


$limit = isset($_POST['limit']) ? $_POST['limit'] : 10;


$result = $mysqli->query("SELECT * FROM borrowers LIMIT $limit");
$borrowers = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower List</title>
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
    <style>
        .toast {
            max-width: 300px;
            background-color: #28a745;
            color: #fff;
        }
    </style>
    <?php
    include 'topbar.php';
    include 'navbar.php';
    ?>
</head>

<body>

    <!-- Modal -->
    <div class="modal fade" id="insertBorrowerModal" tabindex="-1" role="dialog"
        aria-labelledby="insertBorrowerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="insertBorrowerModalLabel">Insert New Borrower</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                 
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-group">
                            <label for="borrower_id">Borrower ID:</label>
                            <input type="text" class="form-control" id="borrower_id" name="borrower_id" required>
                        </div>
                        <div class="form-group">
                            <label for="first_name">First Name:</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name:</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_number">Contact Number:</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="text" class="form-control" id="email" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <br><br><br><br><br><br>
        <h2 class="text-center mb-4">Borrower List</h2>

        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="limitForm">
            <label for="limit">Select number of records to display:</label>
            <select name="limit" id="limit" onchange="document.getElementById('limitForm').submit()">
                <option value="5" <?php echo ($limit == 5) ? 'selected' : ''; ?>>5</option>
                <option value="25" <?php echo ($limit == 25) ? 'selected' : ''; ?>>25</option>
                <option value="50" <?php echo ($limit == 50) ? 'selected' : ''; ?>>50</option>
            </select>
        </form>

        
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#insertBorrowerModal">
            Insert New Borrower
        </button>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Borrower ID</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Address</th>
                        <th scope="col">Contact Number</th>
                        <th scope="col">Email</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($borrowers as $borrower) : ?>
                        <tr>
                            <td><?php echo $borrower['borrower_id']; ?></td>
                            <td><?php echo $borrower['first_name']; ?></td>
                            <td><?php echo $borrower['last_name']; ?></td>
                            <td><?php echo $borrower['address']; ?></td>
                            <td><?php echo $borrower['contact_number']; ?></td>
                            <td><?php echo $borrower['email']; ?></td>
                            <td>
                                <a href="update_borrower.php?id=<?php echo $borrower['id']; ?>"
                                    class="btn btn-warning btn-sm">Update</a>
                                <a href="delete_borrower.php?id=<?php echo $borrower['id']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this borrower?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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

    <script>
        // Automatically submit the form when the dropdown selection changes
        document.getElementById('limit').addEventListener('change', function () {
            document.getElementById('limitForm').submit();
        });
    </script>
</body>

</html>
