<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
include 'db_config.php';

function calculateAge($dob) {
    $birthDate = new DateTime($dob);
    $today = new DateTime();
    return $today->diff($birthDate)->y;
}

$fields = [
    'full_name' => '',
    'gender' => '',
    'date_of_birth' => '',
    'phone_number' => '',
    'email' => '',
    'street' => '',
    'city' => '',
    'province' => '',
    'zip_code' => '',
    'country' => '',
    'username' => '',
    'password' => '',
    'confirm_password' => ''
];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    foreach ($fields as $key => &$value) {
        $value = trim($_POST[$key] ?? '');
    }

    // Validation rules
    if (!preg_match("/^[a-zA-Z ]{2,50}$/", $fields['full_name']))
        $errors['full_name'] = "Full name must be 2-50 letters and spaces only.";
    if (empty($fields['gender']))
        $errors['gender'] = "Gender is required.";
    if (!DateTime::createFromFormat('Y-m-d', $fields['date_of_birth']) || calculateAge($fields['date_of_birth']) < 18)
        $errors['date_of_birth'] = "You must be at least 18 years old.";
    if (!preg_match("/^09\\d{9}$/", $fields['phone_number']))
        $errors['phone_number'] = "Phone must start with 09 and be 11 digits.";
    if (!preg_match("/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\\.[a-z]{2,}$/", $fields['email']))
        $errors['email'] = "Invalid email format.";
    if (!preg_match("/^[a-zA-Z0-9\\s#.,-]{5,100}$/", $fields['street']))
        $errors['street'] = "Street must be 5–100 characters.";
    if (!preg_match("/^[a-zA-Z ]{2,50}$/", $fields['city']))
        $errors['city'] = "City must be 2–50 letters and spaces only.";
    if (!preg_match("/^[a-zA-Z ]{2,50}$/", $fields['province']))
        $errors['province'] = "Province/State must be 2–50 letters and spaces only.";
    if (!preg_match("/^\\d{4}$/", $fields['zip_code']))
        $errors['zip_code'] = "ZIP Code must be 4 digits.";
    if (!preg_match("/^[a-zA-Z ]+$/", $fields['country']))
        $errors['country'] = "Country must contain letters and spaces only.";
    if (!preg_match("/^\\w{5,20}$/", $fields['username']))
        $errors['username'] = "Username must be 5–20 characters.";
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[\\W_]).{8,}$/", $fields['password']))
        $errors['password'] = "Password must have at least 8 characters, upper, lower, digit, and symbol.";
    if ($fields['password'] !== $fields['confirm_password'])
        $errors['confirm_password'] = "Passwords do not match.";

    if (empty($errors)) {
        // Check if username or email already exists using database
        // FIXED: Check for username column instead of id
        $stmt = $conn->prepare("SELECT username FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $fields['username'], $fields['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors['username'] = "Username or email already exists.";
        }
        $stmt->close();

        if (empty($errors)) {
            // Insert new user into database
            $hash = password_hash($fields['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, gender, date_of_birth, phone_number, email, street, city, province, zip_code, country, username, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssssss", 
                $fields['full_name'], 
                $fields['gender'], 
                $fields['date_of_birth'], 
                $fields['phone_number'],
                $fields['email'], 
                $fields['street'], 
                $fields['city'], 
                $fields['province'],
                $fields['zip_code'], 
                $fields['country'], 
                $fields['username'], 
                $hash
            );
            
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: login.php");
                exit;
            } else {
                $errors['general'] = "Registration failed. Please try again. Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - GlowCare Skincare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-spa me-2"></i>GlowCare
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i>Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="shop.php"><i class="fas fa-shopping-bag me-1"></i>Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php"><i class="fas fa-info-circle me-2"></i>About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart me-1"></i>Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a></li>
                    <li class="nav-item"><a class="nav-link active" href="register.php"><i class="fas fa-user-plus me-1"></i>Register</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Registration Form -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow border-0 rounded-lg">
                        <div class="card-header bg-primary text-white text-center py-4">
                            <h3 class="fw-light my-2">Create Account</h3>
                            <p class="mb-0">Join GlowCare for exclusive skincare benefits</p>
                        </div>
                        <div class="card-body p-4">
                            <?php if (!empty($errors['general'])): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
                            <?php endif; ?>
                            
                            <form method="POST" id="registrationForm" novalidate>
                                <div class="row">
                                    <!-- Personal Information -->
                                    <div class="col-12 mb-4">
                                        <h5 class="text-primary border-bottom pb-2"><i class="fas fa-user me-2"></i>Personal Information</h5>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="full_name" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" 
                                               id="full_name" name="full_name" value="<?= htmlspecialchars($fields['full_name']) ?>" required>
                                        <div class="invalid-feedback"><?= $errors['full_name'] ?? '' ?></div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label">Gender *</label>
                                        <select class="form-select <?= isset($errors['gender']) ? 'is-invalid' : '' ?>" 
                                                id="gender" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male" <?= $fields['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                                            <option value="Female" <?= $fields['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                                            <option value="Other" <?= $fields['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
                                        </select>
                                        <div class="invalid-feedback"><?= $errors['gender'] ?? '' ?></div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="date_of_birth" class="form-label">Date of Birth *</label>
                                        <input type="date" class="form-control <?= isset($errors['date_of_birth']) ? 'is-invalid' : '' ?>" 
                                               id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($fields['date_of_birth']) ?>" required>
                                        <div class="invalid-feedback"><?= $errors['date_of_birth'] ?? '' ?></div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="phone_number" class="form-label">Phone Number *</label>
                                        <input type="tel" class="form-control <?= isset($errors['phone_number']) ? 'is-invalid' : '' ?>" 
                                               id="phone_number" name="phone_number" value="<?= htmlspecialchars($fields['phone_number']) ?>" 
                                               placeholder="09XXXXXXXXX" required>
                                        <div class="invalid-feedback"><?= $errors['phone_number'] ?? '' ?></div>
                                    </div>
                                    
                                    <div class="col-12 mb-3">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                               id="email" name="email" value="<?= htmlspecialchars($fields['email']) ?>" required>
                                        <div class="invalid-feedback"><?= $errors['email'] ?? '' ?></div>
                                    </div>
                                    
                                    <!-- Address Information -->
                                    <div class="col-12 mb-4 mt-4">
                                        <h5 class="text-primary border-bottom pb-2"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h5>
                                    </div>
                                    
                                    <div class="col-12 mb-3">
                                        <label for="street" class="form-label">Street Address *</label>
                                        <input type="text" class="form-control <?= isset($errors['street']) ? 'is-invalid' : '' ?>" 
                                               id="street" name="street" value="<?= htmlspecialchars($fields['street']) ?>" required>
                                        <div class="invalid-feedback"><?= $errors['street'] ?? '' ?></div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label">City *</label>
                                        <input type="text" class="form-control <?= isset($errors['city']) ? 'is-invalid' : '' ?>" 
                                               id="city" name="city" value="<?= htmlspecialchars($fields['city']) ?>" required>
                                        <div class="invalid-feedback"><?= $errors['city'] ?? '' ?></div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="province" class="form-label">Province/State *</label>
                                        <input type="text" class="form-control <?= isset($errors['province']) ? 'is-invalid' : '' ?>" 
                                               id="province" name="province" value="<?= htmlspecialchars($fields['province']) ?>" required>
                                        <div class="invalid-feedback"><?= $errors['province'] ?? '' ?></div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="zip_code" class="form-label">ZIP Code *</label>
                                        <input type="text" class="form-control <?= isset($errors['zip_code']) ? 'is-invalid' : '' ?>" 
                                               id="zip_code" name="zip_code" value="<?= htmlspecialchars($fields['zip_code']) ?>" required>
                                        <div class="invalid-feedback"><?= $errors['zip_code'] ?? '' ?></div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="country" class="form-label">Country *</label>
                                        <input type="text" class="form-control <?= isset($errors['country']) ? 'is-invalid' : '' ?>" 
                                               id="country" name="country" value="<?= htmlspecialchars($fields['country']) ?>" required>
                                        <div class="invalid-feedback"><?= $errors['country'] ?? '' ?></div>
                                    </div>
                                    
                                    <!-- Account Information -->
                                    <div class="col-12 mb-4 mt-4">
                                        <h5 class="text-primary border-bottom pb-2"><i class="fas fa-lock me-2"></i>Account Information</h5>
                                    </div>
                                    
                                    <div class="col-12 mb-3">
                                        <label for="username" class="form-label">Username *</label>
                                        <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                                               id="username" name="username" value="<?= htmlspecialchars($fields['username']) ?>" required>
                                        <div class="invalid-feedback"><?= $errors['username'] ?? '' ?></div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Password *</label>
                                        <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                                               id="password" name="password" required>
                                        <div class="invalid-feedback"><?= $errors['password'] ?? '' ?></div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="confirm_password" class="form-label">Confirm Password *</label>
                                        <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                                               id="confirm_password" name="confirm_password" required>
                                        <div class="invalid-feedback"><?= $errors['confirm_password'] ?? '' ?></div>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-user-plus me-2"></i>Create Account
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer text-center py-3">
                            <small class="text-muted">Already have an account? 
                                <a href="login.php" class="text-decoration-none">Sign in here</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="main.js"></script>
</body>
</html>