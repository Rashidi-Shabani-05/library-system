<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">

        <!-- Sidebar toggle -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link"
                   data-lte-toggle="sidebar"
                   href="#"
                   role="button"
                   aria-label="Toggle sidebar">
                    <i class="bi bi-list"></i>
                </a>
            </li>

            <li class="nav-item d-none d-md-block">
                <a href="index.php" class="nav-link">
                    Home
                </a>
            </li>
             <li class="nav-item d-none d-md-block">
                <a href="categories.php" class="nav-link">
                    Book Category
                </a>
            </li>
             <li class="nav-item d-none d-md-block">
                <a href="books.php" class="nav-link">
                    Books
                </a>
            </li>
             <li class="nav-item d-none d-md-block">
                <a href="members.php" class="nav-link">
                    Member
                </a>
            </li>
             <li class="nav-item d-none d-md-block">
                <a href="loans.php" class="nav-link">
                    Loans Record
                </a>
            </li>
        </ul>

        <!-- Right-side navigation -->
        <ul class="navbar-nav ms-auto">

            <li class="nav-item">
                <form action="books.php" method="get"
                      class="d-flex">
                    <input type="search"
                           name="search"
                           class="form-control form-control-sm"
                           placeholder="Search books..."
                           aria-label="Search books">

                    <button class="btn btn-outline-secondary btn-sm ms-1"
                            type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </li>

        </ul>
    </div>
</nav>