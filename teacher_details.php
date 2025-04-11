
<?php
// Include database connection
require_once 'config/db_connect.php';

// Check if id parameter exists
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: teachers.php");
    exit();
}

$id = $_GET['id'];

// Get teacher with class name
$sql = "SELECT t.*, c.name as class_name 
        FROM teachers t 
        LEFT JOIN classes c ON t.class_id = c.id 
        WHERE t.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: teachers.php");
    exit();
}

$teacher = $result->fetch_assoc();

// Get pupils in the class if teacher has a class assigned
$pupils = [];
if (!empty($teacher['class_id'])) {
    $pupils_sql = "SELECT * FROM pupils WHERE class_id = ? ORDER BY last_name, first_name";
    $pupils_stmt = $conn->prepare($pupils_sql);
    $pupils_stmt->bind_param("i", $teacher['class_id']);
    $pupils_stmt->execute();
    $pupils_result = $pupils_stmt->get_result();
    
    while ($pupil = $pupils_result->fetch_assoc()) {
        $pupils[] = $pupil;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Details - St Alphonsus Primary School</title>
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
        <h2>Teacher Details</h2>
        
        <div class="detail-card">
            <h3><?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></h3>
            
            <div class="detail-section">
                <h4>Contact Information</h4>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($teacher['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($teacher['phone_number']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($teacher['address']); ?></p>
            </div>
            
            <div class="detail-section">
                <h4>Employment Information</h4>
                <p><strong>Annual Salary:</strong> £<?php echo htmlspecialchars(number_format($teacher['annual_salary'], 2)); ?></p>
                <p><strong>Background Check Status:</strong> 
                    <?php 
                        $status = $teacher['background_check_status'];
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
                </p>
            </div>
            
            <div class="detail-section">
                <h4>Class Assignment</h4>
                <?php if (!empty($teacher['class_name'])): ?>
                    <p><strong>Class:</strong> <?php echo htmlspecialchars($teacher['class_name']); ?></p>
                    
                    <h5>Pupils in this class (<?php echo count($pupils); ?>)</h5>
                    <?php if (!empty($pupils)): ?>
                        <table class="detail-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Date of Birth</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pupils as $pupil): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($pupil['first_name'] . ' ' . $pupil['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($pupil['date_of_birth']); ?></td>
                                        <td>
                                            <a href="pupil_details.php?id=<?php echo $pupil['id']; ?>" class="btn">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No pupils in this class</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>No class assigned</p>
                <?php endif; ?>
            </div>
            
            <div class="actions">
                <a href="teacher_form.php?id=<?php echo $teacher['id']; ?>" class="btn">Edit Teacher</a>
                <a href="teachers.php" class="btn btn-secondary">Back to List</a>
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
