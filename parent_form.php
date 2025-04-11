
<?php
// Include database connection
require_once 'config/db_connect.php';

// Initialize variables
$first_name = '';
$last_name = '';
$address = '';
$phone_number = '';
$email = '';
$relationship = '';
$id = null;
$errors = [];

// Check if editing existing parent
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM parents WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $parent = $result->fetch_assoc();
        $first_name = $parent['first_name'];
        $last_name = $parent['last_name'];
        $address = $parent['address'];
        $phone_number = $parent['phone_number'];
        $email = $parent['email'];
        $relationship = $parent['relationship'];
    } else {
        // Parent not found
        header("Location: parents.php");
        exit();
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate first name
    $first_name = trim($_POST['first_name']);
    if (empty($first_name)) {
        $errors['first_name'] = 'First name is required';
    }
    
    // Validate last name
    $last_name = trim($_POST['last_name']);
    if (empty($last_name)) {
        $errors['last_name'] = 'Last name is required';
    }
    
    // Validate address
    $address = trim($_POST['address']);
    if (empty($address)) {
        $errors['address'] = 'Address is required';
    }
    
    // Validate phone number
    $phone_number = trim($_POST['phone_number']);
    if (empty($phone_number)) {
        $errors['phone_number'] = 'Phone number is required';
    }
    
    // Validate email
    $email = trim($_POST['email']);
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }
    
    // Validate relationship
    $relationship = trim($_POST['relationship']);
    if (empty($relationship)) {
        $errors['relationship'] = 'Relationship is required';
    }
    
    // Process valid form
    if (empty($errors)) {
        if ($id) {
            // Update existing parent
            $sql = "UPDATE parents SET first_name = ?, last_name = ?, address = ?, phone_number = ?, email = ?, relationship = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssi", $first_name, $last_name, $address, $phone_number, $email, $relationship, $id);
            
            if ($stmt->execute()) {
                header("Location: parents.php?success=updated");
                exit();
            } else {
                $errors['db'] = "Database error: " . $conn->error;
            }
        } else {
            // Create new parent
            $sql = "INSERT INTO parents (first_name, last_name, address, phone_number, email, relationship) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $first_name, $last_name, $address, $phone_number, $email, $relationship);
            
            if ($stmt->execute()) {
                header("Location: parents.php?success=added");
                exit();
            } else {
                $errors['db'] = "Database error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Edit' : 'Add'; ?> Parent/Guardian - St Alphonsus Primary School</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>St Alphonsus Primary School</h1>
            <nav>
                <ul>
                    <li><a href="index.html">Dashboard</a></li>
                    <li><a href="pupils.php">Pupils</a></li>
                    <li><a href="teachers.php">Teachers</a></li>
                    <li><a href="classes.php">Classes</a></li>
                    <li><a href="parents.php">Parents/Guardians</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <h2><?php echo $id ? 'Edit' : 'Add New'; ?> Parent/Guardian</h2>
        
        <?php if (isset($errors['db'])): ?>
            <div class="alert error"><?php echo $errors['db']; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                <?php if (isset($errors['first_name'])): ?>
                    <p class="error"><?php echo $errors['first_name']; ?></p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                <?php if (isset($errors['last_name'])): ?>
                    <p class="error"><?php echo $errors['last_name']; ?></p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="relationship">Relationship:</label>
                <select id="relationship" name="relationship" required>
                    <option value="" disabled <?php echo empty($relationship) ? 'selected' : ''; ?>>-- Select Relationship --</option>
                    <option value="mother" <?php echo $relationship == 'mother' ? 'selected' : ''; ?>>Mother</option>
                    <option value="father" <?php echo $relationship == 'father' ? 'selected' : ''; ?>>Father</option>
                    <option value="guardian" <?php echo $relationship == 'guardian' ? 'selected' : ''; ?>>Guardian</option>
                    <option value="other" <?php echo $relationship == 'other' ? 'selected' : ''; ?>>Other</option>
                </select>
                <?php if (isset($errors['relationship'])): ?>
                    <p class="error"><?php echo $errors['relationship']; ?></p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" rows="3" required><?php echo htmlspecialchars($address); ?></textarea>
                <?php if (isset($errors['address'])): ?>
                    <p class="error"><?php echo $errors['address']; ?></p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" required>
                <?php if (isset($errors['phone_number'])): ?>
                    <p class="error"><?php echo $errors['phone_number']; ?></p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <p class="error"><?php echo $errors['email']; ?></p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn"><?php echo $id ? 'Update' : 'Add'; ?> Parent/Guardian</button>
                <a href="parents.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; 2025 St Alphonsus Primary School. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
<?php $conn->close(); ?>
