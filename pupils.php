
<?php
// Include database connection
require_once 'config/db_connect.php';

// Delete pupil if requested
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM pupil_parents WHERE pupil_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    $sql = "DELETE FROM pupils WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: pupils.php?success=deleted");
        exit();
    } else {
        $error = "Failed to delete pupil";
    }
}

// Fetch all pupils with their class names
$sql = "SELECT p.*, c.name as class_name 
        FROM pupils p 
        LEFT JOIN classes c ON p.class_id = c.id 
        ORDER BY p.last_name, p.first_name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pupils - St Alphonsus Primary School</title>
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
        <h2>Manage Pupils</h2>
        
        <?php if (isset($_GET['success']) && $_GET['success'] == 'added'): ?>
            <div class="alert success">Pupil was added successfully!</div>
        <?php elseif (isset($_GET['success']) && $_GET['success'] == 'updated'): ?>
            <div class="alert success">Pupil was updated successfully!</div>
        <?php elseif (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
            <div class="alert success">Pupil was deleted successfully!</div>
        <?php endif; ?>
        
        <p><a href="pupil_form.php" class="btn">Add New Pupil</a></p>
        
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Date of Birth</th>
                    <th>Address</th>
                    <th>Class</th>
                    <th>Medical Information</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['date_of_birth']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td><?php echo htmlspecialchars($row['class_name'] ?? 'Not Assigned'); ?></td>
                            <td><?php echo htmlspecialchars($row['medical_information']); ?></td>
                            <td>
                                <a href="pupil_form.php?id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                                <a href="pupil_details.php?id=<?php echo $row['id']; ?>" class="btn">View</a>
                                <a href="pupils.php?delete=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this pupil?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No pupils found</td>
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
