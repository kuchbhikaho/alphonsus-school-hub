
<?php
// Include database connection
require_once 'config/db_connect.php';

// Delete parent if requested
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Check if parent has pupils
    $check_sql = "SELECT COUNT(*) as count FROM pupil_parents WHERE parent_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $check_data = $check_result->fetch_assoc();
    
    if ($check_data['count'] > 0) {
        $error = "Cannot delete parent - they have pupils assigned to them.";
    } else {
        $sql = "DELETE FROM parents WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            header("Location: parents.php?success=deleted");
            exit();
        } else {
            $error = "Failed to delete parent";
        }
    }
}

// Fetch all parents
$sql = "SELECT * FROM parents ORDER BY last_name, first_name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parents/Guardians - St Alphonsus Primary School</title>
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
        <h2>Manage Parents/Guardians</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success']) && $_GET['success'] == 'added'): ?>
            <div class="alert success">Parent/Guardian was added successfully!</div>
        <?php elseif (isset($_GET['success']) && $_GET['success'] == 'updated'): ?>
            <div class="alert success">Parent/Guardian was updated successfully!</div>
        <?php elseif (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
            <div class="alert success">Parent/Guardian was deleted successfully!</div>
        <?php endif; ?>
        
        <p><a href="parent_form.php" class="btn">Add New Parent/Guardian</a></p>
        
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Relationship</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($row['relationship'])); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td>
                                <a href="parent_form.php?id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                                <a href="parent_details.php?id=<?php echo $row['id']; ?>" class="btn">View</a>
                                <a href="parents.php?delete=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this parent/guardian?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No parents/guardians found</td>
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
