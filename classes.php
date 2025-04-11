
<?php
// Include database connection
require_once 'config/db_connect.php';

// Delete class if requested
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Check if class has pupils
    $check_sql = "SELECT COUNT(*) as count FROM pupils WHERE class_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $check_data = $check_result->fetch_assoc();
    
    if ($check_data['count'] > 0) {
        $error = "Cannot delete class - it has pupils assigned to it.";
    } else {
        $sql = "DELETE FROM classes WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            header("Location: classes.php?success=deleted");
            exit();
        } else {
            $error = "Failed to delete class";
        }
    }
}

// Fetch all classes with teacher names
$sql = "SELECT c.*, CONCAT(t.first_name, ' ', t.last_name) as teacher_name 
        FROM classes c 
        LEFT JOIN teachers t ON c.teacher_id = t.id 
        ORDER BY c.name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes - St Alphonsus Primary School</title>
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
        <h2>Manage Classes</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success']) && $_GET['success'] == 'added'): ?>
            <div class="alert success">Class was added successfully!</div>
        <?php elseif (isset($_GET['success']) && $_GET['success'] == 'updated'): ?>
            <div class="alert success">Class was updated successfully!</div>
        <?php elseif (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
            <div class="alert success">Class was deleted successfully!</div>
        <?php endif; ?>
        
        <p><a href="class_form.php" class="btn">Add New Class</a></p>
        
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Teacher</th>
                    <th>Capacity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['teacher_name'] ?? 'Not Assigned'); ?></td>
                            <td><?php echo htmlspecialchars($row['capacity']); ?></td>
                            <td>
                                <a href="class_form.php?id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                                <a href="class_pupils.php?id=<?php echo $row['id']; ?>" class="btn">View Pupils</a>
                                <a href="classes.php?delete=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this class?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No classes found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; 2025 St Alphonsus Primary School. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
<?php $conn->close(); ?>
