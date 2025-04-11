
<?php
// Include database connection
require_once 'config/db_connect.php';

// Initialize variables
$name = '';
$capacity = '';
$teacher_id = '';
$id = null;
$errors = [];

// Get teachers for dropdown
$teachers_sql = "SELECT id, first_name, last_name FROM teachers ORDER BY last_name, first_name";
$teachers_result = $conn->query($teachers_sql);

// Check if editing existing class
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM classes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $class = $result->fetch_assoc();
        $name = $class['name'];
        $capacity = $class['capacity'];
        $teacher_id = $class['teacher_id'];
    } else {
        // Class not found
        header("Location: classes.php");
        exit();
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate name
    $name = trim($_POST['name']);
    if (empty($name)) {
        $errors['name'] = 'Class name is required';
    }
    
    // Validate capacity
    $capacity = trim($_POST['capacity']);
    if (empty($capacity)) {
        $errors['capacity'] = 'Capacity is required';
    } elseif (!is_numeric($capacity) || $capacity < 1) {
        $errors['capacity'] = 'Capacity must be a positive number';
    }
    
    // Validate teacher
    $teacher_id = isset($_POST['teacher_id']) ? $_POST['teacher_id'] : '';
    
    // Process valid form
    if (empty($errors)) {
        if ($id) {
            // Update existing class
            $sql = "UPDATE classes SET name = ?, capacity = ?, teacher_id = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("siii", $name, $capacity, $teacher_id, $id);
            
            if ($stmt->execute()) {
                header("Location: classes.php?success=updated");
                exit();
            } else {
                $errors['db'] = "Database error: " . $conn->error;
            }
        } else {
            // Create new class
            $sql = "INSERT INTO classes (name, capacity, teacher_id) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sii", $name, $capacity, $teacher_id);
            
            if ($stmt->execute()) {
                header("Location: classes.php?success=added");
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
    <title><?php echo $id ? 'Edit' : 'Add'; ?> Class - St Alphonsus Primary School</title>
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
        <h2><?php echo $id ? 'Edit' : 'Add New'; ?> Class</h2>
        
        <?php if (isset($errors['db'])): ?>
            <div class="alert error"><?php echo $errors['db']; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="name">Class Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                <?php if (isset($errors['name'])): ?>
                    <p class="error"><?php echo $errors['name']; ?></p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="capacity">Capacity:</label>
                <input type="number" id="capacity" name="capacity" min="1" value="<?php echo htmlspecialchars($capacity); ?>" required>
                <?php if (isset($errors['capacity'])): ?>
                    <p class="error"><?php echo $errors['capacity']; ?></p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="teacher_id">Teacher:</label>
                <select id="teacher_id" name="teacher_id">
                    <option value="">-- Select Teacher --</option>
                    <?php if ($teachers_result && $teachers_result->num_rows > 0): ?>
                        <?php while($teacher = $teachers_result->fetch_assoc()): ?>
                            <option value="<?php echo $teacher['id']; ?>" <?php echo $teacher_id == $teacher['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn"><?php echo $id ? 'Update' : 'Add'; ?> Class</button>
                <a href="classes.php" class="btn btn-secondary">Cancel</a>
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
