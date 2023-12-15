<?php

session_start();

// Check if the user is not logged in
// if (!isset($_SESSION['user_id'])) {
//     // Redirect to the login page
//     header("Location: log_in.php");
//     exit(); // Make sure to exit after a header redirect
// }
$insertSuccess = isset($_SESSION["insert_success"]) && $_SESSION["insert_success"];
if ($insertSuccess) {
 
    unset($_SESSION["insert_success"]);
}


$mysqli = new mysqli("localhost", "root", "", "new_library");


if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $book_id = $_POST["book_id"];
    $book_title = $_POST["book_title"];
    $book_author = $_POST["book_author"];
    $book_description = $_POST["book_description"];
    $isbn = $_POST["isbn"];

    // Call the stored procedure
    $stmt = $mysqli->prepare("CALL Insert_Books(?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $book_id, $book_title, $book_author, $book_description, $isbn);
    $stmt->execute();
    $stmt->close();


    $_SESSION["insert_success"] = true;

   
    $mysqli->close();

   


    header("Refresh: 5; url=" . $_SERVER['PHP_SELF']);
    exit();
}


$search_query = isset($_GET['search']) ? $_GET['search'] : '';
if (!empty($search_query)) {
    $search_query = '%' . $mysqli->real_escape_string($search_query) . '%';
    $query = "SELECT * FROM books WHERE 
              book_id LIKE ? OR 
              book_title LIKE ? OR 
              book_author LIKE ? OR 
              book_description LIKE ? OR 
              isbn LIKE ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sssss", $search_query, $search_query, $search_query, $search_query, $search_query);
    $stmt->execute();
    $result = $stmt->get_result();
    $books = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {

    $result = $mysqli->query("SELECT * FROM books");
    $books = $result->fetch_all(MYSQLI_ASSOC);
}


$mysqli->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Book</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="assets/font-awesome/css/all.min.css">
    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <!-- <link href="assets/css/mdb.min.css" rel="stylesheet"> -->
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

        .delete-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #dc3545;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .delete-btn:hover {
            background-color: red;
        }
    </style>
    <?php 
 
 include 'topbar.php';
 include 'navbar.php';
?>
</head>

<body>

    <!-- Modal -->
    <div class="modal fade" id="insertBookModal" tabindex="-1" role="dialog" aria-labelledby="insertBookModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="insertBookModalLabel">Insert Book</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post"  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-group">
                            <label for="book_id">Book ID:</label>
                            <input type="text" class="form-control" id="book_id" name="book_id" required>
                        </div>
                        <div class="form-group">
                            <label for="book_title">Book Title:</label>
                            <input type="text" class="form-control" id="book_title" name="book_title" required>
                        </div>
                        <div class="form-group">
                            <label for="book_author">Book Author:</label>
                            <input type="text" class="form-control" id="book_author" name="book_author" required>
                        </div>
                        <div class="form-group">
                            <label for="book_description">Book Description:</label>
                            <textarea class="form-control" id="book_description" name="book_description"
                                required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="isbn">ISBN:</label>
                            <input type="text" class="form-control" id="isbn" name="isbn" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <br><br><br><br><br><br>
        <h2 class="text-center mb-4">Book List</h2>

        <form method="get" class="form-inline mb-4">
            <div class="form-group mx-sm-3 mb-2">
                <label for="search" class="sr-only">Search</label>
                <input type="text" class="form-control" id="search" name="search" placeholder="Search">
            </div>
            <button type="submit" class="btn btn-primary mb-2">Search</button>
        </form>

        <button type="button" class="btn btn-primary mb-4" data-toggle="modal" data-target="#insertBookModal">
            Insert Book
        </button>

        <?php if (!empty($books)) : ?>
            <?php if ($insertSuccess) : ?>
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000"
                    style="position: absolute; top: 10px; right: 10px;">
                    <div class="toast-header">
                        <strong class="mr-auto">Success</strong>
                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="toast-body">
                        Book successfully inserted!
                    </div>
                </div>
            <?php endif; ?>
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
                                    <a href="delete_book.php?book_id=<?php echo $book['book_id']; ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this book?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <p class="alert alert-info">No books found.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery (required for modal) -->
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

    <!-- Script to show the toast -->
    <!-- Script to handle form submission via AJAX -->
<script>
    $(document).ready(function () {
        // Submit form via AJAX
        $('#insertBookForm').submit(function (e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: '<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>',
                data: $(this).serialize() + '&ajax_submit=1',
                success: function (response) {
                    // Check if the insertion was successful
                    if (response.success) {
                        // Show the toast
                        $(".toast").toast('show');

                        // Reload the table or perform any other necessary actions
                        // ...

                        // Optional: Clear the form fields
                        $('#insertBookForm')[0].reset();
                    }
                }
            });
        });
    });
</script>
</body>

</html>
