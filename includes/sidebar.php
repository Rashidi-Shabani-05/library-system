
<aside class="app-sidebar bg-dark shadow" data-bs-theme="dark">

    <!-- Sidebar Brand -->
    <div class="sidebar-brand">
        <a href="index.php" class="brand-link text-decoration-none">
            <i class="bi bi-book-half me-2"></i>
            <span class="brand-text fw-light">Library System</span>
        </a>
    </div>

    <!-- Sidebar Wrapper -->
    <div class="sidebar-wrapper">

        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column"
                data-lte-toggle="treeview"
                role="navigation"
                aria-label="Main navigation"
                data-accordion="false">

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>

                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="index.php" class="nav-link">
                            <i class="nav-icon bi bi-house-door-fill"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <!-- Categories -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-tags-fill"></i>
                            <p>
                                Book Categories
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="categories.php" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>View Categories</p>
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

                    <!-- Books -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-book-fill"></i>
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

                    <!-- Members -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-people-fill"></i>
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

                    <!-- Loan Records -->
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
                                    <p>View Loans</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="loans-form.php" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Add Loan</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'member'): ?>

                    <!-- Member Dashboard -->
                    <li class="nav-item">
                        <a href="member-dashboard.php" class="nav-link">
                            <i class="nav-icon bi bi-house-door-fill"></i>
                            <p>My Dashboard</p>
                        </a>
                    </li>

                    <!-- Book Catalog -->
                    <li class="nav-item">
                        <a href="catalog.php" class="nav-link">
                            <i class="nav-icon bi bi-book"></i>
                            <p>Book Catalog</p>
                        </a>
                    </li>

                    <!-- My Loans -->
                    <li class="nav-item">
                        <a href="my-loans.php" class="nav-link">
                            <i class="nav-icon bi bi-journal-check"></i>
                            <p>My Loans</p>
                        </a>
                    </li>

                <?php endif; ?>

                <!-- Logout -->
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
