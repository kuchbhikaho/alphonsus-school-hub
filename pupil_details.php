
<?php
// Include database connection
require_once 'config/db_connect.php';

// Check if id parameter exists
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: pupils.php");
    exit();
}

$id = $_GET['id'];

// Get pupil with class name
$sql = "SELECT p.*, c.name as class_name 
        FROM pupils p 
        LEFT JOIN classes c ON p.class_id = c.id 
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: pupils.php");
    exit();
}

$pupil = $result->fetch_assoc();

// Get pupil's parents
$parents_sql = "SELECT pr.* 
                FROM parents pr 
                JOIN pupil_parents pp ON pr.id = pp.parent_id 
                WHERE pp.pupil_id = ?";
$parents_stmt = $conn->prepare($parents_sql);
$parents_stmt->bind_param("i", $id);
$parents_stmt->execute();
$parents_result = $parents_stmt->get_result();

// Get teacher for pupil's class if assigned
$teacher = null;
if (!empty($pupil['class_id'])) {
    $teacher_sql = "SELECT t.* 
                   FROM teachers t
                   JOIN classes c ON t.id = c.teacher_id
                   WHERE c.id = ?";
    $teacher_stmt = $conn->prepare($teacher_sql);
    $teacher_stmt->bind_param("i", $pupil['class_id']);
    $teacher_stmt->execute();
    $teacher_result = $teacher_stmt->get_result();
    
    if ($teacher_result->num_rows > 0) {
        $teacher = $teacher_result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pupil Details - St Alphonsus Primary School</title>
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
        <h2>Pupil Details</h2>
        
        <div class="detail-card">
            <h3><?php echo htmlspecialchars($pupil['first_name'] . ' ' . $pupil['last_name']); ?></h3>
            
            <div class="detail-section">
                <h4>Personal Information</h4>
                <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($pupil['date_of_birth']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($pupil['address']); ?></p>
                <p><strong>Medical Information:</strong> <?php echo htmlspecialchars($pupil['medical_information'] ?: 'None provided'); ?></p>
            </div>
            
            <div class="detail-section">
                <h4>Class Information</h4>
                <?php if (!empty($pupil['class_name'])): ?>
                    <p><strong>Class:</strong> <?php echo htmlspecialchars($pupil['class_name']); ?></p>
                    <?php if ($teacher): ?>
                        <p><strong>Teacher:</strong> <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></p>
                    <?php else: ?>
                        <p><strong>Teacher:</strong> No teacher assigned to this class</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>No class assigned</p>
                <?php endif; ?>
            </div>
            
            <div class="detail-section">
                <h4>Parents/Guardians</h4>
                <?php if ($parents_result->num_rows > 0): ?>
                    <table class="detail-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Relationship</th>
                                <th>Contact</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($parent = $parents_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($parent['relationship'])); ?></td>
                                    <td><?php echo htmlspecialchars($parent['phone_number']); ?></td>
                                    <td><?php echo htmlspecialchars($parent['email']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No parents/guardians assigned</p>
                <?php endif; ?>
            </div>
            
            <div class="actions">
                <a href="pupil_form.php?id=<?php echo $pupil['id']; ?>" class="btn">Edit Pupil</a>
                <a href="pupils.php" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; 2025 St Alphonsus Primary School. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
<?php $conn->close(); ?>
