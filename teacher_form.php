
<?php
// Include database connection
require_once 'config/db_connect.php';

// Initialize variables
$id = 0;
$first_name = '';
$last_name = '';
$address = '';
$phone_number = '';
$email = '';
$annual_salary = '';
$background_check_status = 'pending';
$class_id = null;
$errors = [];
$isEditing = false;

// Fetch all classes for the dropdown
$classes_query = "SELECT id, name FROM classes ORDER BY name";
$classes_result = $conn->query($classes_query);

// If editing an existing teacher
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $isEditing = true;
    $id = $_GET['id'];
    
    // Get teacher data
    $sql = "SELECT * FROM teachers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $teacher = $result->fetch_assoc();
        $first_name = $teacher['first_name'];
        $last_name = $teacher['last_name'];
        $address = $teacher['address'];
        $phone_number = $teacher['phone_number'];
        $email = $teacher['email'];
        $annual_salary = $teacher['annual_salary'];
        $background_check_status = $teacher['background_check_status'];
        $class_id = $teacher['class_id'];
    } else {
        header("Location: teachers.php");
        exit();
    }
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $email = $_POST['email'] ?? '';
    $annual_salary = $_POST['annual_salary'] ?? '';
    $background_check_status = $_POST['background_check_status'] ?? 'pending';
    $class_id = !empty($_POST['class_id']) ? $_POST['class_id'] : NULL;
    
    // Validation
    if (empty($first_name)) {
        $errors['first_name'] = 'First name is required';
    }
    
    if (empty($last_name)) {
        $errors['last_name'] = 'Last name is required';
    }
    
    if (empty($address)) {
        $errors['address'] = 'Address is required';
    }
    
    if (empty($phone_number)) {
        $errors['phone_number'] = 'Phone number is required';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email is not valid';
    } else {
        // Check if email is unique (except for current teacher if editing)
        $email_sql = "SELECT id FROM teachers WHERE email = ? AND id != ?";
        $email_stmt = $conn->prepare($email_sql);
        $email_stmt->bind_param("si", $email, $id);
        $email_stmt->execute();
        $email_result = $email_stmt->get_result();
        
        if ($email_result->num_rows > 0) {
            $errors['email'] = 'Email is already in use by another teacher';
        }
    }
    
    if (empty($annual_salary)) {
        $errors['annual_salary'] = 'Annual salary is required';
    } elseif (!is_numeric($annual_salary) || $annual_salary < 0) {
        $errors['annual_salary'] = 'Annual salary must be a positive number';
    }
    
    // Check if class assignment would create a conflict
    if (!empty($class_id)) {
        $class_check_sql = "SELECT teacher_id FROM classes WHERE id = ?";
        $class_check_stmt = $conn->prepare($class_check_sql);
        $class_check_stmt->bind_param("i", $class_id);
        $class_check_stmt->execute();
        $class_check_result = $class_check_stmt->get_result();
        
        if ($class_check_result->num_rows > 0) {
            $class_check_data = $class_check_result->fetch_assoc();
            if (!empty($class_check_data['teacher_id']) && $class_check_data['teacher_id'] != $id) {
                $errors['class_id'] = 'This class is already assigned to another teacher';
            }
        }
    }
    
    if (empty($errors)) {
        if ($isEditing) {
            // Update teacher
            $sql = "UPDATE teachers SET first_name = ?, last_name = ?, address = ?, phone_number = ?, email = ?, annual_salary = ?, background_check_status = ?, class_id = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssdssi", $first_name, $last_name, $address, $phone_number, $email, $annual_salary, $background_check_status, $class_id, $id);
            
            if ($stmt->execute()) {
                // Update class teacher assignment if a class was selected
                if (!empty($class_id)) {
                    $update_class_sql = "UPDATE classes SET teacher_id = ? WHERE id = ?";
                    $update_class_stmt = $conn->prepare($update_class_sql);
                    $update_class_stmt->bind_param("ii", $id, $class_id);
                    $update_class_stmt->execute();
                }
                
                header("Location: teachers.php?success=updated");
                exit();
            } else {
                $errors['db'] = 'Database error: ' . $conn->error;
            }
        } else {
            // Add new teacher
            $sql = "INSERT INTO teachers (first_name, last_name, address, phone_number, email, annual_salary, background_check_status, class_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssdsi", $first_name, $last_name, $address, $phone_number, $email, $annual_salary, $background_check_status, $class_id);
            
            if ($stmt->execute()) {
                $new_teacher_id = $conn->insert_id;
                
                // Update class teacher assignment if a class was selected
                if (!empty($class_id)) {
                    $update_class_sql = "UPDATE classes SET teacher_id = ? WHERE id = ?";
                    $update_class_stmt = $conn->prepare($update_class_sql);
                    $update_class_stmt->bind_param("ii", $new_teacher_id, $class_id);
                    $update_class_stmt->execute();
                }
                
                header("Location: teachers.php?success=added");
                exit();
            } else {
                $errors['db'] = 'Database error: ' . $conn->error;
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
    <title><?php echo $isEditing ? 'Edit' : 'Add'; ?> Teacher - St Alphonsus Primary School</title>
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
        <h2><?php echo $isEditing ? 'Edit Teacher' : 'Add New Teacher'; ?></h2>
        
        <?php if (isset($errors['db'])): ?>
            <div class="alert error"><?php echo $errors['db']; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo $isEditing ? "teacher_form.php?id=$id" : 'teacher_form.php'; ?>">
            <div class="form-group">
                <label for="first_name">First Name *</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                <?php if (isset($errors['first_name'])): ?>
                    <div class="error"><?php echo $errors['first_name']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="last_name">Last Name *</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                <?php if (isset($errors['last_name'])): ?>
                    <div class="error"><?php echo $errors['last_name']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="address">Address *</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" required>
                <?php if (isset($errors['address'])): ?>
                    <div class="error"><?php echo $errors['address']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="phone_number">Phone Number *</label>
                <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" required>
                <?php if (isset($errors['phone_number'])): ?>
                    <div class="error"><?php echo $errors['phone_number']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <div class="error"><?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="annual_salary">Annual Salary (£) *</label>
                <input type="number" id="annual_salary" name="annual_salary" value="<?php echo htmlspecialchars($annual_salary); ?>" step="0.01" min="0" required>
                <?php if (isset($errors['annual_salary'])): ?>
                    <div class="error"><?php echo $errors['annual_salary']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="background_check_status">Background Check Status *</label>
                <select id="background_check_status" name="background_check_status" required>
                    <option value="pending" <?php echo ($background_check_status === 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="passed" <?php echo ($background_check_status === 'passed') ? 'selected' : ''; ?>>Passed</option>
                    <option value="failed" <?php echo ($background_check_status === 'failed') ? 'selected' : ''; ?>>Failed</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="class_id">Assigned Class</label>
                <select id="class_id" name="class_id">
                    <option value="">-- Not Assigned --</option>
                    <?php 
                    // Reset the pointer to the first row
                    $classes_result->data_seek(0);
                    while ($class = $classes_result->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $class['id']; ?>" <?php echo ($class_id == $class['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <?php if (isset($errors['class_id'])): ?>
                    <div class="error"><?php echo $errors['class_id']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Save Teacher</button>
                <a href="teachers.php" class="btn btn-secondary">Cancel</a>
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
