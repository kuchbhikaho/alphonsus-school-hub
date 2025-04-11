
<?php
// Include database connection
require_once 'config/db_connect.php';

// Initialize variables
$id = 0;
$first_name = '';
$last_name = '';
$date_of_birth = '';
$address = '';
$medical_information = '';
$class_id = '';
$parent_ids = [];
$errors = [];
$isEditing = false;

// Fetch all classes for the dropdown
$classes_query = "SELECT id, name FROM classes ORDER BY name";
$classes_result = $conn->query($classes_query);

// Fetch all parents for the checkboxes
$parents_query = "SELECT id, first_name, last_name FROM parents ORDER BY last_name, first_name";
$parents_result = $conn->query($parents_query);

// If editing an existing pupil
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $isEditing = true;
    $id = $_GET['id'];
    
    // Get pupil data
    $sql = "SELECT * FROM pupils WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $pupil = $result->fetch_assoc();
        $first_name = $pupil['first_name'];
        $last_name = $pupil['last_name'];
        $date_of_birth = $pupil['date_of_birth'];
        $address = $pupil['address'];
        $medical_information = $pupil['medical_information'];
        $class_id = $pupil['class_id'];
        
        // Get pupil's parents
        $parents_sql = "SELECT parent_id FROM pupil_parents WHERE pupil_id = ?";
        $parents_stmt = $conn->prepare($parents_sql);
        $parents_stmt->bind_param("i", $id);
        $parents_stmt->execute();
        $parents_result_for_pupil = $parents_stmt->get_result();
        
        while ($parent = $parents_result_for_pupil->fetch_assoc()) {
            $parent_ids[] = $parent['parent_id'];
        }
    } else {
        header("Location: pupils.php");
        exit();
    }
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $address = $_POST['address'] ?? '';
    $medical_information = $_POST['medical_information'] ?? '';
    $class_id = !empty($_POST['class_id']) ? $_POST['class_id'] : NULL;
    $parent_ids = isset($_POST['parent_ids']) ? $_POST['parent_ids'] : [];
    
    // Validation
    if (empty($first_name)) {
        $errors['first_name'] = 'First name is required';
    }
    
    if (empty($last_name)) {
        $errors['last_name'] = 'Last name is required';
    }
    
    if (empty($date_of_birth)) {
        $errors['date_of_birth'] = 'Date of birth is required';
    } else {
        $date = new DateTime($date_of_birth);
        $now = new DateTime();
        if ($date > $now) {
            $errors['date_of_birth'] = 'Date of birth cannot be in the future';
        }
    }
    
    if (empty($address)) {
        $errors['address'] = 'Address is required';
    }
    
    if (empty($errors)) {
        if ($isEditing) {
            // Update pupil
            $sql = "UPDATE pupils SET first_name = ?, last_name = ?, date_of_birth = ?, address = ?, medical_information = ?, class_id = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssii", $first_name, $last_name, $date_of_birth, $address, $medical_information, $class_id, $id);
            
            if ($stmt->execute()) {
                // Delete all existing parent relationships and insert new ones
                $delete_sql = "DELETE FROM pupil_parents WHERE pupil_id = ?";
                $delete_stmt = $conn->prepare($delete_sql);
                $delete_stmt->bind_param("i", $id);
                $delete_stmt->execute();
                
                // Insert new parent relationships
                if (!empty($parent_ids)) {
                    $insert_sql = "INSERT INTO pupil_parents (pupil_id, parent_id) VALUES (?, ?)";
                    $insert_stmt = $conn->prepare($insert_sql);
                    
                    foreach ($parent_ids as $parent_id) {
                        $insert_stmt->bind_param("ii", $id, $parent_id);
                        $insert_stmt->execute();
                    }
                }
                
                header("Location: pupils.php?success=updated");
                exit();
            } else {
                $errors['db'] = 'Database error: ' . $conn->error;
            }
        } else {
            // Add new pupil
            $sql = "INSERT INTO pupils (first_name, last_name, date_of_birth, address, medical_information, class_id) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $first_name, $last_name, $date_of_birth, $address, $medical_information, $class_id);
            
            if ($stmt->execute()) {
                $new_pupil_id = $conn->insert_id;
                
                // Insert parent relationships
                if (!empty($parent_ids)) {
                    $insert_sql = "INSERT INTO pupil_parents (pupil_id, parent_id) VALUES (?, ?)";
                    $insert_stmt = $conn->prepare($insert_sql);
                    
                    foreach ($parent_ids as $parent_id) {
                        $insert_stmt->bind_param("ii", $new_pupil_id, $parent_id);
                        $insert_stmt->execute();
                    }
                }
                
                header("Location: pupils.php?success=added");
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
    <title><?php echo $isEditing ? 'Edit' : 'Add'; ?> Pupil - St Alphonsus Primary School</title>
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
        <h2><?php echo $isEditing ? 'Edit Pupil' : 'Add New Pupil'; ?></h2>
        
        <?php if (isset($errors['db'])): ?>
            <div class="alert error"><?php echo $errors['db']; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo $isEditing ? "pupil_form.php?id=$id" : 'pupil_form.php'; ?>">
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
                <label for="date_of_birth">Date of Birth *</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($date_of_birth); ?>" required>
                <?php if (isset($errors['date_of_birth'])): ?>
                    <div class="error"><?php echo $errors['date_of_birth']; ?></div>
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
                <label for="medical_information">Medical Information</label>
                <textarea id="medical_information" name="medical_information" rows="3"><?php echo htmlspecialchars($medical_information); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="class_id">Class</label>
                <select id="class_id" name="class_id">
                    <option value="">-- Select Class --</option>
                    <?php while ($class = $classes_result->fetch_assoc()): ?>
                        <option value="<?php echo $class['id']; ?>" <?php echo ($class_id == $class['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Parents/Guardians</label>
                <div class="checkbox-group">
                    <?php 
                    // Reset the pointer to the first row
                    $parents_result->data_seek(0);
                    while ($parent = $parents_result->fetch_assoc()): 
                    ?>
                        <div class="checkbox-item">
                            <input type="checkbox" id="parent_<?php echo $parent['id']; ?>" name="parent_ids[]" value="<?php echo $parent['id']; ?>" <?php echo in_array($parent['id'], $parent_ids) ? 'checked' : ''; ?>>
                            <label for="parent_<?php echo $parent['id']; ?>"><?php echo htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name']); ?></label>
                        </div>
                    <?php endwhile; ?>
                </div>
                <?php if (empty($parents_result->num_rows)): ?>
                    <p class="note">No parents available. <a href="parent_form.php">Add parents first</a>.</p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Save Pupil</button>
                <a href="pupils.php" class="btn btn-secondary">Cancel</a>
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
