<?php
// if (!isset($_SESSION['user_id'])) {
//     // Redirect to the login page
//     header("Location: log_in.php");
//     exit(); // Make sure to exit after a header redirect
// }

$mysqli = new mysqli("localhost", "root", "", "new_library");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}


if (isset($_GET["book_id"])) {
    $book_id = $_GET["book_id"];


    $stmt = $mysqli->prepare("CALL Delete_Book(?)");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $stmt->close();

 
    
    $_SESSION["delete_success"] = true;

  
    header("Refresh: 5; url=" . $_SERVER['HTTP_REFERER']); 
}



$deleteSuccess = isset($_SESSION["delete_success"]) && $_SESSION["delete_success"];
if ($deleteSuccess) {
  
    unset($_SESSION["delete_success"]);
}
$result = $mysqli->query("SELECT * FROM books");
$books = $result->fetch_all(MYSQLI_ASSOC);


$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book List</title>
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

    <!-- Modal for updating borrower -->
    <div class="modal fade" id="updateBorrowerModal" tabindex="-1" role="dialog" aria-labelledby="updateBorrowerModalLabel" aria-hidden="true">
        <!-- Your existing update modal code here -->
    </div>

    <!-- Modal for deleting borrower -->
    <div class="modal fade" id="deleteBookModal" tabindex="-1" role="dialog" aria-labelledby="deleteBookModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteBookModalLabel">Delete Book</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="hidden" id="delete_book_id" name="delete_book_id">
                        <p>Are you sure you want to delete this book?</p>
                        <button type="submit" class="btn btn-danger" name="delete_submit">Delete</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <br><br><br><br><br><br>
        <h2 class="text-center mb-4">Book List</h2>

        <!-- Button to trigger modal -->
        <button type="button" class="btn btn-primary mb-4" data-toggle="modal" data-target="#deleteBookModal">
            Insert Book
        </button>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                            <th scope="col">Book ID</th>
                            <th scope="col">Title</th>
                            <th scope="col">Author</th>
                            <th scope="col">Description</th>
                            <th scope="col">ISBN</th>
                            <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book) : ?>
                        <tr>
                        <td><?php echo $book['book_id']; ?></td>
                                <td><?php echo $book['book_title']; ?></td>
                                <td><?php echo $book['book_author']; ?></td>
                                <td><?php echo $book['book_description']; ?></td>
                                <td><?php echo $book['isbn']; ?></td>
                            <td>
                            <a href="update_book.php?book_id=<?php echo $book['book_id']; ?>"
                                        class="btn btn-warning btn-sm">Update</a>
                                <a href="#" class="btn btn-danger btn-sm" onclick="openDeleteBookModal(<?php echo $book['book_id']; ?>)">Delete</a>
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
        // Function to open the delete modal and set the book ID
        function openDeleteBookModal(book_id) {
            document.getElementById('delete_book_id').value = book_id;
            $('#deleteBookModal').modal('show');
        }
    </script>
</body>

</html>
