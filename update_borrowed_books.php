<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
    $book_id = $_POST["book_id"];
    $borrower_id = $_POST["borrower_id"];
    $date_borrowed = $_POST["date_borrowed"];
    $date_return = $_POST["date_return"];
    $date_received = $_POST["date_received"];

  
    $mysqli = new mysqli("localhost", "root", "", "new_library");

   
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Call the stored procedure for updating
    $stmt = $mysqli->prepare("CALL Update_Borrowed_Book(?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $book_id, $borrower_id, $date_borrowed, $date_return, $date_received);
    $stmt->execute();
    $stmt->close();

    $mysqli->close();

    echo "<div class='container mt-3'><div class='alert alert-success'>Borrowed book successfully updated!</div></div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowed Books</title>
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

<div class="container">
    <br><br><br><br><br><br><br><br>
    <h2 class="mb-4">Borrowed Books</h2>

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#insertBorrowedBookModal">
        Insert Borrowed Book
    </button>

    <!-- Table to display borrowed books -->
    <table class="table mt-3">
        <thead class="thead-dark">
        <tr>
            <th scope="col">Book ID</th>
            <th scope="col">Borrower ID</th>
            <th scope="col">Borrower Name</th>
            <th scope="col">Book Title</th>
            <th scope="col">Date Borrowed</th>
            <th scope="col">Date Return</th>
            <th scope="col">Date Received</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
       
        $mysqli = new mysqli("localhost", "root", "", "new_library");

        // Check connection
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }

        $result = $mysqli->query("SELECT borrowed_books.book_id, borrowed_books.borrower_id, borrowed_books.date_borrowed, borrowed_books.date_return, borrowed_books.date_received, books.book_title, CONCAT(borrowers.first_name, ' ', borrowers.last_name) AS borrower_name
                                FROM borrowed_books 
                                JOIN books ON borrowed_books.book_id = books.book_id
                                JOIN borrowers ON borrowed_books.borrower_id = borrowers.borrower_id");

        $borrowedBooks = $result->fetch_all(MYSQLI_ASSOC);
        $mysqli->close();

        foreach ($borrowedBooks as $borrowedBook) {
            echo "<tr>";
            echo "<td>{$borrowedBook['book_id']}</td>";
            echo "<td>{$borrowedBook['borrower_id']}</td>";
            echo "<td>{$borrowedBook['borrower_name']}</td>";
            echo "<td>{$borrowedBook['book_title']}</td>";
            echo "<td>{$borrowedBook['date_borrowed']}</td>";
            echo "<td>{$borrowedBook['date_return']}</td>";
            echo "<td>{$borrowedBook['date_received']}</td>";
            echo "<td>
                    <a href='#' data-toggle='modal' data-target='#updateBorrowedBookModal{$borrowedBook['book_id']}' class='btn btn-info btn-sm'>Update</a>
                    <a href='delete_borrowed_books.php?book_id={$borrowedBook['book_id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this record?\")'>Delete</a>
                </td>";
            echo "</tr>";

            // Update Borrowed Book Modal
            echo "<div class='modal fade' id='updateBorrowedBookModal{$borrowedBook['book_id']}' tabindex='-1' role='dialog' aria-labelledby='updateBorrowedBookModalLabel{$borrowedBook['book_id']}' aria-hidden='true'>";
            echo "<div class='modal-dialog' role='document'>";
            echo "<div class='modal-content'>";
            echo "<div class='modal-header'>";
            echo "<h5 class='modal-title' id='updateBorrowedBookModalLabel{$borrowedBook['book_id']}'>Update Borrowed Book</h5>";
            echo "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>";
            echo "<span aria-hidden='true'>&times;</span>";
            echo "</button>";
            echo "</div>";
            echo "<div class='modal-body'>";
            echo "<form method='post' action='update_borrowed_books.php'>";
            echo "<div class='form-group'>";
            echo "<label for='book_id_modal'>Book ID:</label>";
            echo "<input type='text' class='form-control' id='book_id_modal' name='book_id' value='{$borrowedBook['book_id']}' readonly>";
            echo "</div>";
            echo "<div class='form-group'>";
            echo "<label for='borrower_id_modal'>Borrower ID:</label>";
            echo "<input type='text' class='form-control' id='borrower_id_modal' name='borrower_id' value='{$borrowedBook['borrower_id']}' readonly>";
            echo "</div>";


            
            echo "<div class='form-group'>";
            echo "<label for='date_borrowed_modal'>Date Borrowed:</label>";
            echo "<input type='date' class='form-control' id='date_borrowed_modal' name='date_borrowed' value='{$borrowedBook['date_borrowed']}' required>";
            echo "</div>";
            echo "<div class='form-group'>";
            echo "<label for='date_return_modal'>Date Return:</label>";
            echo "<input type='date' class='form-control' id='date_return_modal' name='date_return' value='{$borrowedBook['date_return']}' required>";
            echo "</div>";
            echo "<div class='form-group'>";
            echo "<label for='date_received_modal'>Date Received:</label>";
            echo "<input type='date' class='form-control' id='date_received_modal' name='date_received' value='{$borrowedBook['date_received']}'>";
            echo "</div>";
            echo "<button type='submit' class='btn btn-primary'>Update</button>";
            echo "</form>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
        ?>
        </tbody>
    </table>
</div>

<!-- Insert Borrowed Book Modal -->
<div class="modal fade" id="insertBorrowedBookModal" tabindex="-1" role="dialog" aria-labelledby="insertBorrowedBookModalLabel" aria-hidden="true">
    <!-- Modal content and form -->
</div>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Additional scripts (if needed) -->

</body>
</html>
