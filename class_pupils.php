
<?php
// Include database connection
require_once 'config/db_connect.php';

// Check if class ID is provided
if (!isset($_GET['id'])) {
    header("Location: classes.php");
    exit();
}

$class_id = $_GET['id'];

// Get class information
$class_sql = "SELECT * FROM classes WHERE id = ?";
$class_stmt = $conn->prepare($class_sql);
$class_stmt->bind_param("i", $class_id);
$class_stmt->execute();
$class_result = $class_stmt->get_result();

if ($class_result->num_rows !== 1) {
    // Class not found
    header("Location: classes.php");
    exit();
}

$class = $class_result->fetch_assoc();

// Get teacher information
$teacher_name = "Not Assigned";
if ($class['teacher_id']) {
    $teacher_sql = "SELECT first_name, last_name FROM teachers WHERE id = ?";
    $teacher_stmt = $conn->prepare($teacher_sql);
    $teacher_stmt->bind_param("i", $class['teacher_id']);
    $teacher_stmt->execute();
    $teacher_result = $teacher_stmt->get_result();
    
    if ($teacher_result->num_rows === 1) {
        $teacher = $teacher_result->fetch_assoc();
        $teacher_name = $teacher['first_name'] . ' ' . $teacher['last_name'];
    }
}

// Get pupils in this class
$pupils_sql = "SELECT * FROM pupils WHERE class_id = ? ORDER BY last_name, first_name";
$pupils_stmt = $conn->prepare($pupils_sql);
$pupils_stmt->bind_param("i", $class_id);
$pupils_stmt->execute();
$pupils_result = $pupils_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($class['name']); ?> - Pupils - St Alphonsus Primary School</title>
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
        <h2>Pupils in <?php echo htmlspecialchars($class['name']); ?></h2>
        
        <div class="class-info">
            <p><strong>Teacher:</strong> <?php echo htmlspecialchars($teacher_name); ?></p>
            <p><strong>Capacity:</strong> <?php echo $pupils_result->num_rows; ?> / <?php echo htmlspecialchars($class['capacity']); ?></p>
        </div>
        
        <p>
            <a href="classes.php" class="btn btn-secondary">Back to Classes</a>
            <a href="pupil_form.php?class_id=<?php echo $class_id; ?>" class="btn">Add New Pupil to Class</a>
        </p>
        
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Date of Birth</th>
                    <th>Address</th>
                    <th>Medical Information</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($pupils_result->num_rows > 0): ?>
                    <?php while($pupil = $pupils_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pupil['first_name'] . ' ' . $pupil['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($pupil['date_of_birth']); ?></td>
                            <td><?php echo htmlspecialchars($pupil['address']); ?></td>
                            <td><?php echo htmlspecialchars($pupil['medical_information']); ?></td>
                            <td>
                                <a href="pupil_form.php?id=<?php echo $pupil['id']; ?>" class="btn">Edit</a>
                                <a href="pupil_details.php?id=<?php echo $pupil['id']; ?>" class="btn">View</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No pupils found in this class</td>
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
