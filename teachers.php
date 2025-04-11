
<?php
// Include database connection
require_once 'config/db_connect.php';

// Delete teacher if requested
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Check if the teacher has a class
    $check_sql = "SELECT id FROM classes WHERE teacher_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $error = "Cannot delete teacher - they are assigned to a class. Please reassign the class first.";
    } else {
        $sql = "DELETE FROM teachers WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: teachers.php?success=deleted");
            exit();
        } else {
            $error = "Failed to delete teacher";
        }
    }
}

// Fetch all teachers with their class names
$sql = "SELECT t.*, c.name as class_name 
        FROM teachers t 
        LEFT JOIN classes c ON t.class_id = c.id 
        ORDER BY t.last_name, t.first_name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teachers - St Alphonsus Primary School</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>St Alphonsus Primary School</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="pupils.php">Pupils</a></li>
                    <li><a href="teachers.php">Teachers</a></li>
                    <li><a href="classes.php">Classes</a></li>
                    <li><a href="parents.php">Parents/Guardians</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <h2>Manage Teachers</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success']) && $_GET['success'] == 'added'): ?>
            <div class="alert success">Teacher was added successfully!</div>
        <?php elseif (isset($_GET['success']) && $_GET['success'] == 'updated'): ?>
            <div class="alert success">Teacher was updated successfully!</div>
        <?php elseif (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
            <div class="alert success">Teacher was deleted successfully!</div>
        <?php endif; ?>
        
        <p><a href="teacher_form.php" class="btn">Add New Teacher</a></p>
        
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Class</th>
                    <th>Background Check</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['class_name'] ?? 'Not Assigned'); ?></td>
                            <td>
                                <?php 
                                    $status = $row['background_check_status'];
                                    $statusClass = '';
                                    
                                    if ($status == 'passed') {
                                        $statusClass = 'status-passed';
                                    } elseif ($status == 'pending') {
                                        $statusClass = 'status-pending';
                                    } elseif ($status == 'failed') {
                                        $statusClass = 'status-failed';
                                    }
                                    
                                    echo '<span class="status ' . $statusClass . '">' . ucfirst($status) . '</span>';
                                ?>
                            </td>
                            <td>
                                <a href="teacher_form.php?id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                                <a href="teacher_details.php?id=<?php echo $row['id']; ?>" class="btn">View</a>
                                <a href="teachers.php?delete=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this teacher?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No teachers found</td>
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
