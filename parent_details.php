
<?php
// Include database connection
require_once 'config/db_connect.php';

// Check if parent ID is provided
if (!isset($_GET['id'])) {
    header("Location: parents.php");
    exit();
}

$parent_id = $_GET['id'];

// Get parent information
$parent_sql = "SELECT * FROM parents WHERE id = ?";
$parent_stmt = $conn->prepare($parent_sql);
$parent_stmt->bind_param("i", $parent_id);
$parent_stmt->execute();
$parent_result = $parent_stmt->get_result();

if ($parent_result->num_rows !== 1) {
    // Parent not found
    header("Location: parents.php");
    exit();
}

$parent = $parent_result->fetch_assoc();

// Get pupils associated with this parent
$pupils_sql = "SELECT p.* FROM pupils p 
               JOIN pupil_parents pp ON p.id = pp.pupil_id 
               WHERE pp.parent_id = ?
               ORDER BY p.last_name, p.first_name";
$pupils_stmt = $conn->prepare($pupils_sql);
$pupils_stmt->bind_param("i", $parent_id);
$pupils_stmt->execute();
$pupils_result = $pupils_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Details - St Alphonsus Primary School</title>
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
        <h2>Parent/Guardian Details</h2>
        
        <div class="card">
            <h3><?php echo htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name']); ?></h3>
            <p><strong>Relationship:</strong> <?php echo htmlspecialchars(ucfirst($parent['relationship'])); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($parent['address']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($parent['phone_number']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($parent['email']); ?></p>
            
            <div class="actions">
                <a href="parent_form.php?id=<?php echo $parent_id; ?>" class="btn">Edit</a>
                <a href="parents.php" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
        
        <h3>Children</h3>
        <?php if ($pupils_result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date of Birth</th>
                        <th>Class</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($pupil = $pupils_result->fetch_assoc()): ?>
                        <?php 
                            // Get class name
                            $class_name = "Not Assigned";
                            if ($pupil['class_id']) {
                                $class_sql = "SELECT name FROM classes WHERE id = ?";
                                $class_stmt = $conn->prepare($class_sql);
                                $class_stmt->bind_param("i", $pupil['class_id']);
                                $class_stmt->execute();
                                $class_result = $class_stmt->get_result();
                                
                                if ($class_result->num_rows === 1) {
                                    $class = $class_result->fetch_assoc();
                                    $class_name = $class['name'];
                                }
                            }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pupil['first_name'] . ' ' . $pupil['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($pupil['date_of_birth']); ?></td>
                            <td><?php echo htmlspecialchars($class_name); ?></td>
                            <td>
                                <a href="pupil_details.php?id=<?php echo $pupil['id']; ?>" class="btn">View</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No children associated with this parent/guardian.</p>
        <?php endif; ?>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; 2025 St Alphonsus Primary School. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
<?php $conn->close(); ?>
