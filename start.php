<?php

include_once 'include/dbh.inc.php';

if(isset($_POST['searchInput'])) {
    $studentId = $_POST['searchInput'];
    
    if(!empty($studentId)) {
        // Using prepared statement to prevent SQL injection
        $sql = "SELECT s.student_id, s.name, s.age, s.cgpa, u.university_name, u.university_location, u.university_ranking, 
                       c.course_name, c.course_id, c.course_description, c.course_duration_months, c.course_start_date,
                       s.student_image
                FROM students s
                INNER JOIN university u ON s.university_id = u.university_id
                INNER JOIN student_courses sc ON s.student_id = sc.student_id
                INNER JOIN courses c ON sc.course_id = c.course_id
                WHERE s.student_id = ?";
        
        $stmt = mysqli_stmt_init($conn);
        if(mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $studentId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if(mysqli_num_rows($result) > 0) {
                $studentData = mysqli_fetch_assoc($result);
            } else {
                $studentData = false;
            }
        } else {
            // Error handling for query preparation failure
            $errorMessage = "Database query failed";
        }
    } else {
        // Error handling for empty input
        $errorMessage = "Please enter a student ID";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Search</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>

<div class="search-container">
    <form method="post">
        <label for="searchInput" class="inline-element">Student ID:</label>
        <input type="text" id="searchInput" name="searchInput" placeholder="Enter student ID...">
        <button type="submit" id="searchButton"><i class="fas fa-search"></i></button>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" 
integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" 
crossorigin="anonymous" referrerpolicy="no-referrer" />
    </form>
</div>
<br><hr><br>

<div id="searchResults">
    <?php if(isset($studentData)): ?>
        <?php if($studentData): ?>
            <!-- Display student information -->
            
                <!-- Student Image -->
                <div class="container">
                <div class="image-frame">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($studentData['student_image']); ?>" alt="Student Image" width="150px" height="150px">
                </div>
                
                
                <!-- Student Info -->
                <div class="about-section">
                    <h2 class="about-heading">Student Information</h2>
                    <div class="student-info">
                        <div class="info-container">
                            <div class="info-item">
                                <span>Name:</span>
                                <span><?php echo $studentData['name']; ?></span>
                            </div>
                            <div class="info-item">
                                <span>ID:</span>
                                <span><?php echo $studentData['student_id']; ?></span>
                            </div>
                            <div class="info-item">
                                <span>CGPA:</span>
                                <span><?php echo $studentData['cgpa']; ?></span>
                            </div>
                            <div class="info-item">
                                <span>Age:</span>
                                <span><?php echo $studentData['age']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            <br><br>
            <!-- Display enrolled courses -->
            <div>
                <table border="3" width="80%">
                    <tr id="head">
                        <td>COURSE NAME</td>
                        <td>COURSE ID</td>
                        <td>COURSE DESCRIPTION</td>
                        <td>COURSE DURATION (Months)</td>
                        <td>COURSE START DATE</td>
                    </tr>
                    <?php mysqli_data_seek($result, 0); ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['course_name']; ?></td>
                            <td><?php echo $row['course_id']; ?></td>
                            <td><?php echo $row['course_description']; ?></td>
                            <td><?php echo $row['course_duration_months']; ?></td>
                            <td><?php echo $row['course_start_date']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        <?php else: ?>
            <p>No student found with the provided ID.</p>
        <?php endif; ?>
    <?php elseif(isset($errorMessage)): ?>
        <p><?php echo $errorMessage; ?></p>
    <?php endif; ?>
</div>

</body>
</html>
