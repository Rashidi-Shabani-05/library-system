<?php
require __DIR__. "/includes/header.php";
?>
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="bi bi-person-plus me-2"></i>
            Register New Member
        </h3>
    </div>

    <form action="./config/process.php" method="post">

        <div class="card-body">
            <div class="row">

                <!-- Full Name -->
                <div class="col-md-6 mb-3">
                    <label for="full_name" class="form-label">
                        Full Name
                    </label>
                    <input type="text" name="full_name"
                           id="full_name" class="form-control"
                           placeholder="Enter full name" required>
                </div>

                <!-- Email -->
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">
                        Email Address
                    </label>
                    <input type="email" name="email"
                           id="email" class="form-control"
                           placeholder="Enter email address">
                </div>

                <!-- Phone -->
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">
                        Phone Number
                    </label>
                    <input type="tel" name="phone"
                           id="phone" class="form-control"
                           placeholder="Enter phone number">
                </div>

                <!-- Address -->
                <div class="col-md-6 mb-3">
                    <label for="address" class="form-label">
                        Address
                    </label>
                    <input type="text" name="address"
                           id="address" class="form-control"
                           placeholder="Enter address">
                </div>

                <!-- Registration Date -->
                <div class="col-md-6 mb-3">
                    <label for="registration_date"
                           class="form-label">
                        Registration Date
                    </label>
                    <input type="date" name="registration_date"
                           id="registration_date"
                           class="form-control" required>
                </div>

                <!-- Status -->
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">
                        Member Status
                    </label>
                    <select name="status" id="status"
                            class="form-select">
                        <option value="Active" selected>
                            Active
                        </option>
                        <option value="Inactive">
                            Inactive
                        </option>
                    </select>
                    <small class="form-text text-muted">
                        New members are active by default.
                    </small>
                </div>

            </div>
        </div>

        <div class="card-footer d-flex justify-content-between">
            <a href="members.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Cancel
            </a>

            <button type="submit" name="submit1"
                    class="btn btn-primary">
                <i class="bi bi-person-check me-1"></i>
                Register Member
            </button>
        </div>
    </form>
</div>
<?php
require __DIR__. "/includes/footer.php";
?>