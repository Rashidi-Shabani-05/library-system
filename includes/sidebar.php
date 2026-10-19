<aside class="app-sidebar bg-dark shadow"
       data-bs-theme="dark">

    <!-- Sidebar brand -->
    <div class="sidebar-brand">
        <a href="index.php"
           class="brand-link text-decoration-none">
            <i class="bi bi-book-half me-2"></i>
            <span class="brand-text fw-light">
                Library System
            </span>
        </a>
    </div>

    <!-- Sidebar menu -->
    <div class="sidebar-wrapper">

        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column"
                data-lte-toggle="treeview"
                role="navigation"
                aria-label="Main navigation"
                data-accordion="false">

                <li class="nav-item">
                    <a href="index.php" class="nav-link active">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Home</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-tags"></i>
                        <p> 
                            Book Categories
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                     <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="categories.php" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>View Book Categories</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="categories-form.php" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Add Categories</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-book"></i>
                        <p>
                            Library Books
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="books.php" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>View Books</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="book-form.php" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Add Books</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-people"></i>
                        <p>
                            Library Members
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="members.php" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>View Members</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="member-form.php" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Add Members</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-journal-check"></i>
                        <p>
                            Loan Records
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="loans.php" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>View Loan Records</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="loans-form.php" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Add Loan Records</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="nav-icon bi bi-box-arrow-right"></i>
                        <p>Logout</p>
                    </a>
                </li>

            </ul>
        </nav>

    </div>
</aside>