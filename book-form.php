<?php
require __DIR__. "/includes/header.php";
require __DIR__. "/config/connection.php";
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Add New Book</h3>
    </div>

    <form action="./config/process.php" method="post">
        <div class="card-body">

            <div class="mb-3">
                <label for="title" class="form-label">
                    Book Title:
                </label>
                <input type="text" name="title" id="title"
                       class="form-control"
                       placeholder="Enter Book Title" required>
            </div>

            <div class="mb-3">
                <label for="author" class="form-label">
                    Author:
                </label>
                <input type="text" name="author" id="author"
                       class="form-control"
                       placeholder="Enter Author of the book" required>
            </div>

            <div class="mb-3">
                <label for="publisher" class="form-label">
                    Publisher:
                </label>
                <input type="text" name="publisher" id="publisher"
                       class="form-control"
                       placeholder="Enter book publisher">
            </div>

            <div class="mb-3">
                <label for="publication_year" class="form-label">
                    Publication Year:
                </label>
                <input type="number" name="publication_year"
                       id="publication_year" class="form-control"
                       placeholder="Enter publication year">
            </div>

            <div class="mb-3">
                <label for="total_copies" class="form-label">
                    Total Copies:
                </label>
                <input type="number" name="total_copies"
                       id="total_copies" class="form-control"
                       placeholder="Enter total copies"
                       min="1" required>
            </div>
             <div class="mb-3">
               <label for="category_id" class="form-label">
                  Book Category
               </label>

               <select name="category_id"
                id="category_id"
                class="form-select" required>

               <option value="">Select Category</option>

               <?php $result = mysqli_query($conn, "SELECT category_id, category_name
               FROM categories ORDER BY category_name" );
               while ($category = mysqli_fetch_assoc($result)) { ?>
                <option value="<?= (int) $category['category_id'] ?>">
                <?= htmlspecialchars($category['category_name']) ?>
               </option>
              <?php } ?>

              </select>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" name="submit"
                    class="btn btn-primary">
                Add Book
            </button>

            <a href="books.php" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>
<?php
require __DIR__. "/includes/footer.php";
?>