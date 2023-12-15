<!-- Sidebar -->
    <div class="sidebar-fixed position-fixed">

      <a class="logo-wrapper waves-effect">
        <img src="" class="" alt=""  style="width:100%;max-height : 15vh !important">
      </a>

      <div class="list-group list-group-flush" id="navigations">
        <a href="dashboard.php" class="list-group-item list-group-item-action nav-home waves-effect">
          <i class="fas fa-chart-pie mr-3"></i>Dashboard
        </a>
        <a href="insert_books.php?page=books&title=books" class="list-group-item list-group-item-action nav-books waves-effect">
          <i class="fas fa-book mr-3"></i>Book Management
        </a>
        <a href="insert_borrower.php?page=borrower&title=borrowers" class="list-group-item list-group-item-action nav-borrower waves-effect">
          <i class="fas fa-user mr-3"></i>Borrowers
        </a>

        <a href="insert_borrowed_books.php?page=borrower&title=borrowers" class="list-group-item list-group-item-action nav-borrower waves-effect">
          <i class="fas fa-user mr-3"></i>Borrowed Books
        </a>
        <!-- <a href="admin_user.php?page=borrower&title=borrowers" class="list-group-item list-group-item-action nav-borrower waves-effect">
          <i class="fas fa-user mr-3"></i>Admin User
        </a> -->
        
      </div>

    </div>
    <!-- Sidebar -->

    <script>
      $('#navigations a.nav-<?php echo $_GET['page'] ?>').addClass('active')
    </script>