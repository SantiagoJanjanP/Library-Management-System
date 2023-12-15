<?php
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

    $stmt = $mysqli->prepare("CALL Update_Books(?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $book_id, $book_title, $book_author, $book_description, $isbn);
    $stmt->execute();
    $stmt->close();

    header("Location: insert_books.php");
    exit();
}

$book_id = $_GET["book_id"];
$result = $mysqli->query("SELECT * FROM books WHERE book_id = $book_id");
$book = $result->fetch_assoc();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Book</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
        }
    </style>
</head>

<body>

    <!-- Update Book Modal -->
    <div class="modal" id="updateBookModal" tabindex="-1" role="dialog" aria-labelledby="updateBookModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateBookModalLabel">Update Book</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                        <div class="form-group">
                            <label for="book_title">Book Title:</label>
                            <input type="text" class="form-control" id="book_title" name="book_title"
                                value="<?php echo $book['book_title']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="book_author">Book Author:</label>
                            <input type="text" class="form-control" id="book_author" name="book_author"
                                value="<?php echo $book['book_author']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="book_description">Book Description:</label>
                            <input type="text" class="form-control" id="book_description" name="book_description"
                                value="<?php echo $book['book_description']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="isbn">ISBN:</label>
                            <input type="text" class="form-control" id="isbn" name="isbn"
                                value="<?php echo $book['isbn']; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <h2 class="mb-4">Update Book</h2>
        <button type="button" class="btn btn-primary mb-4" data-toggle="modal" data-target="#updateBookModal">
            Update Book
        </button>
    </div>
    

    <!-- Bootstrap JS, Popper.js, and jQuery (required for modal) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>
