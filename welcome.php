<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
include 'db_config.php';

$user = $_SESSION['user'] ?? null;

if (!$user) {
    header('Location: login.php');
    exit;
}

include 'products.php';

// Handle profile update
$updateSuccess = false;
$updateErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $updateData = [
        'full_name' => trim($_POST['full_name'] ?? ''),
        'gender' => trim($_POST['gender'] ?? ''),
        'date_of_birth' => trim($_POST['date_of_birth'] ?? ''),
        'phone_number' => trim($_POST['phone_number'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'street' => trim($_POST['street'] ?? ''),
        'city' => trim($_POST['city'] ?? ''),
        'province' => trim($_POST['province'] ?? ''),
        'zip_code' => trim($_POST['zip_code'] ?? ''),
        'country' => trim($_POST['country'] ?? '')
    ];

    // Validation
    if (!preg_match("/^[a-zA-Z ]{2,50}$/", $updateData['full_name'])) {
        $updateErrors['full_name'] = "Full name must be 2-50 letters and spaces only.";
    }
    if (empty($updateData['gender'])) {
        $updateErrors['gender'] = "Gender is required.";
    }
    if (!preg_match("/^09\\d{9}$/", $updateData['phone_number'])) {
        $updateErrors['phone_number'] = "Phone must start with 09 and be 11 digits.";
    }
    if (!preg_match("/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\\.[a-z]{2,}$/", $updateData['email'])) {
        $updateErrors['email'] = "Invalid email format.";
    }
    if (!preg_match("/^[a-zA-Z0-9\\s#.,-]{5,100}$/", $updateData['street'])) {
        $updateErrors['street'] = "Street must be 5–100 characters.";
    }
    if (!preg_match("/^[a-zA-Z ]{2,50}$/", $updateData['city'])) {
        $updateErrors['city'] = "City must be 2–50 letters and spaces only.";
    }
    if (!preg_match("/^[a-zA-Z ]{2,50}$/", $updateData['province'])) {
        $updateErrors['province'] = "Province/State must be 2–50 letters and spaces only.";
    }
    if (!preg_match("/^\\d{4}$/", $updateData['zip_code'])) {
        $updateErrors['zip_code'] = "ZIP Code must be 4 digits.";
    }
    if (!preg_match("/^[a-zA-Z ]+$/", $updateData['country'])) {
        $updateErrors['country'] = "Country must contain letters and spaces only.";
    }

    // Check if email is already used by another user
    if (empty($updateErrors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND username != ?");
        $stmt->bind_param("ss", $updateData['email'], $user['username']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $updateErrors['email'] = "Email is already used by another account.";
        }
        $stmt->close();
    }

    // Update database if no errors
    if (empty($updateErrors)) {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, gender = ?, date_of_birth = ?, phone_number = ?, email = ?, street = ?, city = ?, province = ?, zip_code = ?, country = ? WHERE username = ?");
        $stmt->bind_param("sssssssssss", 
            $updateData['full_name'], 
            $updateData['gender'], 
            $updateData['date_of_birth'], 
            $updateData['phone_number'],
            $updateData['email'], 
            $updateData['street'], 
            $updateData['city'], 
            $updateData['province'],
            $updateData['zip_code'], 
            $updateData['country'],
            $user['username']
        );
        
        if ($stmt->execute()) {
            // Update session data
            $_SESSION['user'] = [
                'id' => $user['id'],
                'fullname' => $updateData['full_name'],
                'gender' => $updateData['gender'],
                'dob' => $updateData['date_of_birth'],
                'phone' => $updateData['phone_number'],
                'email' => $updateData['email'],
                'street' => $updateData['street'],
                'city' => $updateData['city'],
                'province' => $updateData['province'],
                'zip' => $updateData['zip_code'],
                'country' => $updateData['country'],
                'username' => $user['username']
            ];
            $user = $_SESSION['user'];
            $updateSuccess = true;
        } else {
            $updateErrors['general'] = "Update failed. Please try again.";
        }
        $stmt->close();
    }
}

// Function to get user's recent orders from database
function getUserRecentOrders($username, $limit = 3) {
    global $conn;
    $orders = [];
    
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT ?");
    $stmt->bind_param("si", $username, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        // Decode JSON items
        $row['items'] = json_decode($row['items'], true);
        $orders[] = $row;
    }
    
    $stmt->close();
    return $orders;
}

// Function to get order status badge
function getStatusBadge($status) {
    $badges = [
        'pending' => ['class' => 'bg-warning', 'icon' => 'fas fa-clock', 'text' => 'Processing'],
        'confirmed' => ['class' => 'bg-info', 'icon' => 'fas fa-check-circle', 'text' => 'Confirmed'],
        'shipped' => ['class' => 'bg-primary', 'icon' => 'fas fa-shipping-fast', 'text' => 'Shipped'],
        'delivered' => ['class' => 'bg-success', 'icon' => 'fas fa-box-open', 'text' => 'Delivered'],
        'cancelled' => ['class' => 'bg-danger', 'icon' => 'fas fa-times-circle', 'text' => 'Cancelled']
    ];
    
    return $badges[$status] ?? $badges['pending'];
}

$recentOrders = getUserRecentOrders($user['username']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Account - GlowCare Skincare</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    .order-mini-card {
      transition: all 0.3s ease;
      border: 1px solid var(--border-color);
      border-radius: 10px;
      background: white;
    }
    
    .order-mini-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(212, 132, 122, 0.15);
    }
    
    .order-id {
      font-family: 'Courier New', monospace;
      font-weight: bold;
      color: var(--primary-color);
      font-size: 0.9rem;
    }
    
    .quick-action-card {
      transition: all 0.3s ease;
      cursor: pointer;
      border: 2px solid var(--border-color);
    }
    
    .quick-action-card:hover {
      border-color: var(--primary-color);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(212, 132, 122, 0.2);
    }
    
    .dashboard-stats {
      background: linear-gradient(135deg, var(--primary-color), var(--dark-accent));
      color: white;
      border-radius: 15px;
      padding: 2rem;
      margin-bottom: 2rem;
    }
    
    .stat-item {
      text-align: center;
      padding: 1rem;
    }
    
    .stat-number {
      font-size: 2rem;
      font-weight: bold;
      display: block;
    }
    
    .stat-label {
      font-size: 0.9rem;
      opacity: 0.9;
    }

    .profile-edit-modal .modal-content {
      border-radius: 20px;
      border: none;
      box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .profile-edit-modal .modal-header {
      background: linear-gradient(135deg, var(--primary-color), var(--dark-accent));
      color: white;
      border-radius: 20px 20px 0 0;
      padding: 1.5rem 2rem;
    }

    .profile-edit-modal .form-control {
      border-radius: 10px;
      border: 2px solid var(--border-color);
      padding: 12px 15px;
      transition: all 0.3s ease;
    }

    .profile-edit-modal .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(212, 132, 122, 0.25);
    }

    .btn-edit-profile {
      background: linear-gradient(135deg, var(--primary-color), var(--dark-accent));
      border: none;
      color: white;
      padding: 12px 30px;
      border-radius: 25px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-edit-profile:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(212, 132, 122, 0.3);
      color: white;
    }
  </style>
</head>
<body>
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
        <li class="nav-item"><a class="nav-link" href="about.php"><i class="fas fa-info-circle me-1"></i>About Us</a></li>
        <li class="nav-item"><a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart me-1"></i>Cart</a></li>
        <li class="nav-item"><a class="nav-link active" href="welcome.php"><i class="fas fa-user me-1"></i>My Account</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a></li>
        <li class="nav-item">
          <span class="navbar-text">
            <i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($user['fullname']) ?>
          </span>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
  
  <?php if ($updateSuccess): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle me-2"></i>
      <strong>Success!</strong> Your profile has been updated successfully.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if (!empty($updateErrors)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-triangle me-2"></i>
      <strong>Error!</strong> Please fix the following issues:
      <ul class="mb-0 mt-2">
        <?php foreach ($updateErrors as $error): ?>
          <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Dashboard Stats -->
  <div class="dashboard-stats">
    <div class="row">
      <div class="col-md-3">
        <div class="stat-item">
          <span class="stat-number"><?= count($recentOrders) ?></span>
          <span class="stat-label">Recent Orders</span>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-item">
          <span class="stat-number">
            <?php 
            $totalSpent = 0;
            foreach ($recentOrders as $order) {
              $totalSpent += $order['total'];
            }
            echo '$' . number_format($totalSpent, 2);
            ?>
          </span>
          <span class="stat-label">Total Spent</span>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-item">
          <span class="stat-number">
            <?php 
            $memberSince = new DateTime($user['dob']);
            $now = new DateTime();
            $years = $now->diff($memberSince)->y;
            echo $years;
            ?>
          </span>
          <span class="stat-label">Years Old</span>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-item">
          <span class="stat-number">
            <i class="fas fa-star"></i>
          </span>
          <span class="stat-label">VIP Member</span>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Profile Information -->
    <div class="col-lg-8">
      <div class="card border-0 shadow mb-4">
        <div class="card-header bg-primary text-white">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">
              <i class="fas fa-user-circle me-2"></i>Profile Information
            </h3>
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
              <i class="fas fa-edit me-1"></i>Edit Profile
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold text-muted">Full Name</label>
              <div class="form-control-plaintext"><?= htmlspecialchars($user['fullname']) ?></div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold text-muted">Gender</label>
              <div class="form-control-plaintext"><?= htmlspecialchars($user['gender']) ?></div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold text-muted">Date of Birth</label>
              <div class="form-control-plaintext"><?= htmlspecialchars($user['dob']) ?></div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold text-muted">Phone Number</label>
              <div class="form-control-plaintext"><?= htmlspecialchars($user['phone']) ?></div>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label fw-bold text-muted">Email Address</label>
              <div class="form-control-plaintext"><?= htmlspecialchars($user['email']) ?></div>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label fw-bold text-muted">Street Address</label>
              <div class="form-control-plaintext"><?= htmlspecialchars($user['street']) ?></div>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label fw-bold text-muted">City</label>
              <div class="form-control-plaintext"><?= htmlspecialchars($user['city']) ?></div>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label fw-bold text-muted">Province</label>
              <div class="form-control-plaintext"><?= htmlspecialchars($user['province']) ?></div>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label fw-bold text-muted">ZIP Code</label>
              <div class="form-control-plaintext"><?= htmlspecialchars($user['zip']) ?></div>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label fw-bold text-muted">Country</label>
              <div class="form-control-plaintext"><?= htmlspecialchars($user['country']) ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Orders -->
      <div class="card border-0 shadow">
        <div class="card-header bg-primary text-white">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">
              <i class="fas fa-shopping-bag me-2"></i>Recent Orders
            </h3>
            <a href="orders.php" class="btn btn-light btn-sm">
              <i class="fas fa-eye me-1"></i>View All
            </a>
          </div>
        </div>
        <div class="card-body">
          <?php if (empty($recentOrders)): ?>
            <div class="text-center py-4">
              <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
              <p class="text-muted">You haven't placed any orders yet.</p>
              <a href="shop.php" class="btn btn-primary">
                <i class="fas fa-shopping-bag me-1"></i>Start Shopping
              </a>
            </div>
          <?php else: ?>
            <div class="row">
              <?php foreach ($recentOrders as $order): ?>
                <div class="col-md-4 mb-3">
                  <div class="order-mini-card p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <div class="order-id">#<?= htmlspecialchars($order['order_id']) ?></div>
                      <?php $badge = getStatusBadge($order['status']); ?>
                      <span class="badge <?= $badge['class'] ?>">
                        <i class="<?= $badge['icon'] ?> me-1"></i><?= $badge['text'] ?>
                      </span>
                    </div>
                    <div class="text-muted small mb-2">
                      <i class="fas fa-calendar me-1"></i><?= date('M j, Y', strtotime($order['order_date'])) ?>
                    </div>
                    <div class="fw-bold text-primary">
                      $<?= number_format($order['total'], 2) ?>
                    </div>
                    <div class="text-muted small">
                      <?= count($order['items']) ?> item(s)
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4">
      <div class="card border-0 shadow">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">
            <i class="fas fa-bolt me-2"></i>Quick Actions
          </h5>
        </div>
        <div class="card-body">
          <div class="d-grid gap-3">
            <a href="shop.php" class="btn btn-outline-primary quick-action-card">
              <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
            </a>
            <a href="orders.php" class="btn btn-outline-primary quick-action-card">
              <i class="fas fa-list me-2"></i>View All Orders
            </a>
            <a href="cart.php" class="btn btn-outline-primary quick-action-card">
              <i class="fas fa-shopping-cart me-2"></i>View Cart
            </a>
            <button class="btn btn-outline-primary quick-action-card" data-bs-toggle="modal" data-bs-target="#editProfileModal">
              <i class="fas fa-user-edit me-2"></i>Edit Profile
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade profile-edit-modal" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editProfileModalLabel">
          <i class="fas fa-user-edit me-2"></i>Edit Profile
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="welcome.php">
        <div class="modal-body">
          <input type="hidden" name="action" value="update_profile">
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="edit_full_name" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="edit_full_name" name="full_name" value="<?= htmlspecialchars($user['fullname']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="edit_gender" class="form-label">Gender</label>
              <select class="form-control" id="edit_gender" name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male" <?= $user['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= $user['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                <option value="Other" <?= $user['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="edit_date_of_birth" class="form-label">Date of Birth</label>
              <input type="date" class="form-control" id="edit_date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($user['dob']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="edit_phone_number" class="form-label">Phone Number</label>
              <input type="tel" class="form-control" id="edit_phone_number" name="phone_number" value="<?= htmlspecialchars($user['phone']) ?>" required>
            </div>
            <div class="col-12 mb-3">
              <label for="edit_email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="edit_email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="col-12 mb-3">
              <label for="edit_street" class="form-label">Street Address</label>
              <input type="text" class="form-control" id="edit_street" name="street" value="<?= htmlspecialchars($user['street']) ?>" required>
            </div>
            <div class="col-md-4 mb-3">
              <label for="edit_city" class="form-label">City</label>
              <input type="text" class="form-control" id="edit_city" name="city" value="<?= htmlspecialchars($user['city']) ?>" required>
            </div>
            <div class="col-md-4 mb-3">
              <label for="edit_province" class="form-label">Province/State</label>
              <input type="text" class="form-control" id="edit_province" name="province" value="<?= htmlspecialchars($user['province']) ?>" required>
            </div>
            <div class="col-md-4 mb-3">
              <label for="edit_zip_code" class="form-label">ZIP Code</label>
              <input type="text" class="form-control" id="edit_zip_code" name="zip_code" value="<?= htmlspecialchars($user['zip']) ?>" required>
            </div>
            <div class="col-12 mb-3">
              <label for="edit_country" class="form-label">Country</label>
              <input type="text" class="form-control" id="edit_country" name="country" value="<?= htmlspecialchars($user['country']) ?>" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-edit-profile">
            <i class="fas fa-save me-1"></i>Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Form validation for edit profile modal
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.querySelector('#editProfileModal form');
    
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            const fullName = document.getElementById('edit_full_name').value;
            const phone = document.getElementById('edit_phone_number').value;
            const email = document.getElementById('edit_email').value;
            const zipCode = document.getElementById('edit_zip_code').value;
            
            let hasError = false;
            
            // Validate full name
            if (!/^[a-zA-Z ]{2,50}$/.test(fullName)) {
                alert('Full name must be 2-50 letters and spaces only.');
                hasError = true;
            }
            
            // Validate phone number
            if (!/^09\d{9}$/.test(phone)) {
                alert('Phone must start with 09 and be 11 digits.');
                hasError = true;
            }
            
            // Validate email
            if (!/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-z]{2,}$/.test(email)) {
                alert('Please enter a valid email address.');
                hasError = true;
            }
            
            // Validate ZIP code
            if (!/^\d{4}$/.test(zipCode)) {
                alert('ZIP Code must be 4 digits.');
                hasError = true;
            }
            
            if (hasError) {
                e.preventDefault();
            }
        });
    }
});
</script>
</body>
</html>