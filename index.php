<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../student-management-api/config/db.php";
header("Content-Type: application/json");

// API Key-based Authentication
$headers = getallheaders();
$apiKey = $headers["API-KEY"] ?? null;
$validApiKey = "it311finaltesting"; // Replace with your actual API key
if ($apiKey !== $validApiKey) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized: Invalid API key"]);
    exit;
}

$requestMethod = $_SERVER["REQUEST_METHOD"];
$request = isset($_GET['request']) ? explode("/", trim($_GET['request'], "/")) : [];
$entity = $request[0] ?? null; // students or courses
$id = $request[1] ?? null;



switch ($entity) {
    case "students":
        handleStudents($requestMethod, $id);
        break;

    case "courses":
        handleCourses($requestMethod, $id);
        break;

    default:
        http_response_code(404);
        echo json_encode(["message" => "Invalid endpoint"]);
        break;
}

mysqli_close($connected);

// Students CRUD Operations
function handleStudents($method, $id) {
    global $connected;

    switch ($method) {
        case "POST":
            $data = json_decode(file_get_contents("php://input"), true);
            $firstName = $data['first_name'] ?? null;
            $lastName = $data['last_name'] ?? null;
            $email = $data['email'] ?? null;
            $birthdate = $data['birthdate'] ?? null;
            $courseId = $data['course_id'] ?? null;
        
            // Check if all required fields are provided
            if (!$firstName || !$lastName || !$email || !$birthdate || !$courseId) {
                http_response_code(400);
                echo json_encode(["message" => "All fields are required"]);
                return;
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(["message" => "Invalid email format"]);
                return;
            }
        
            // Check for duplicate email
            $checkEmail = mysqli_query($connected, "SELECT * FROM students WHERE email = '$email'");
            if (mysqli_num_rows($checkEmail) > 0) {
                http_response_code(409);
                echo json_encode(["message" => "Duplicate email"]);
                return;
            }
        
            try {
                // Prepare and execute SQL query
                $sql = "INSERT INTO students (first_name, last_name, email, birthdate, course_id) 
                        VALUES ('$firstName', '$lastName', '$email', '$birthdate', $courseId)";
                mysqli_query($connected, $sql);
        
                http_response_code(201);
                echo json_encode(["message" => "Student created successfully"]);
            } catch (mysqli_sql_exception $e) {
                // Specific handling for incorrect date value
                if (strpos($e->getMessage(), 'Incorrect date value') !== false) {
                    http_response_code(400);
                    echo json_encode(["message" => "Invalid date value. Use the format YYYY-MM-DD."]);
                } else {
                    // General database error
                    http_response_code(500);
                    echo json_encode(["message" => $e->getMessage()]);
                }
            }
            break;
        

            case "GET":
                if ($id) {
                    // If an ID is provided, fetch student by ID
                    $result = mysqli_query($connected, "SELECT * FROM students WHERE student_id = $id");
                    $student = mysqli_fetch_assoc($result);
                    echo json_encode($student ?: ["message" => "Student not found"]);
                } else {
                    // Handle search and course_id filters
                    $search = $_GET['search'] ?? null;
                    $course_id = $_GET['course_id'] ?? null;
                    
                    // Start with a basic query
                    $query = "SELECT * FROM students WHERE 1";  // Always true, allows adding more filters
                    
                    // Add search filter if provided
                    if ($search) {
                        $query .= " AND (first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR email LIKE '%$search%')";
                    }
                    
                    // Add course_id filter if provided
                    if ($course_id) {
                        $query .= " AND course_id = $course_id";
                    }
            
                    // Execute the query
                    $result = mysqli_query($connected, $query);
            
                    // Error handling
                    if (!$result) {
                        echo json_encode(["message" => "Error executing query: " . mysqli_error($connected)]);
                        exit;
                    }
            
                    // Fetch all results
                    $students = mysqli_fetch_all($result, MYSQLI_ASSOC);
                    
                    // If no results are found, return a message saying so
                    if (empty($students)) {
                        echo json_encode(["message" => "No students found matching your criteria"]);
                    } else {
                        echo json_encode($students);
                    }
                }
                break;
            
            

                case "PUT":
                    case "PATCH":
                        if (!$id) {
                            http_response_code(400);
                            echo json_encode(["message" => "Student ID is required"]);
                            return;
                        }
                    
                        $data = json_decode(file_get_contents("php://input"), true);
                        $updates = [];
                    
                        // Validate fields
                        if (isset($data['email'])) {
                            $email = $data['email'];
                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                http_response_code(400);
                                echo json_encode(["message" => "Invalid email format"]);
                                return;
                            }
                    
                            // Check for duplicate email
                            $checkEmail = mysqli_query($connected, "SELECT * FROM students WHERE email = '$email' AND student_id != $id");
                            if (mysqli_num_rows($checkEmail) > 0) {
                                http_response_code(409);
                                echo json_encode(["message" => "Duplicate email"]);
                                return;
                            }
                    
                            $updates[] = "email = '{$data['email']}'";
                        }
                    
                        if (isset($data['birthdate'])) {
                            $birthdate = $data['birthdate'];
                            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthdate)) {
                                http_response_code(400);
                                echo json_encode(["message" => "Invalid birthdate format. Use YYYY-MM-DD"]);
                                return;
                            }
                    
                            $updates[] = "birthdate = '{$data['birthdate']}'";
                        }
                    
                        if (isset($data['first_name'])) $updates[] = "first_name = '{$data['first_name']}'";
                        if (isset($data['last_name'])) $updates[] = "last_name = '{$data['last_name']}'";
                        if (isset($data['course_id'])) $updates[] = "course_id = {$data['course_id']}";
                    
                        if (empty($updates)) {
                            http_response_code(400);
                            echo json_encode(["message" => "At least one field is required for update"]);
                            return;
                        }
                    
                        $sql = "UPDATE students SET " . implode(", ", $updates) . " WHERE student_id = $id";
                        if (mysqli_query($connected, $sql)) {
                            echo json_encode(["message" => "Student updated successfully"]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["message" => "Error updating student"]);
                        }
                        break;
                    

            case "DELETE":
                if ($id) {
                    // Delete a specific student
                    $sql = "DELETE FROM students WHERE student_id = $id";
                    if (mysqli_query($connected, $sql)) {
                        echo json_encode(["message" => "Student deleted successfully"]);
                    } else {
                        http_response_code(500);
                        echo json_encode(["message" => "Error deleting student"]);
                    }
                } else {
                    // Delete all students
                    $sql = "DELETE FROM students";
                    if (mysqli_query($connected, $sql)) {
                        echo json_encode(["message" => "All students deleted successfully"]);
                    } else {
                        http_response_code(500);
                        echo json_encode(["message" => "Error deleting all students"]);
                    }
                }
                break;

        default:
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed"]);
            break;
    }
}

// Courses CRUD Operations
function handleCourses($method, $id) {
    global $connected;

    switch ($method) {
        case "POST":
            $data = json_decode(file_get_contents("php://input"), true);
            $courseName = $data['coursename'] ?? null;
            $description = $data['description'] ?? null;

            if (!$courseName || !$description) {
                http_response_code(400);
                echo json_encode(["message" => "Course name and description are required"]);
                return;
            }

            $sql = "INSERT INTO courses (coursename, description) VALUES ('$courseName', '$description')";
            if (mysqli_query($connected, $sql)) {
                http_response_code(201);
                echo json_encode(["message" => "Course created successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Error creating course"]);
            }
            break;

        case "GET":
            if ($id) {
                $result = mysqli_query($connected, "SELECT * FROM courses WHERE id = $id");
                $course = mysqli_fetch_assoc($result);
                echo json_encode($course ?: ["message" => "Course not found"]);
            } else {
                $result = mysqli_query($connected, "SELECT * FROM courses");
                echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
            }
            break;

        case "PUT":
        case "PATCH":
            if (!$id) {
                http_response_code(400);
                echo json_encode(["message" => "Course ID is required"]);
                return;
            }

            $data = json_decode(file_get_contents("php://input"), true);
            $updates = [];
            if (isset($data['coursename'])) $updates[] = "coursename = '{$data['coursename']}'";
            if (isset($data['description'])) $updates[] = "description = '{$data['description']}'";

            if (empty($updates)) {
                http_response_code(400);
                echo json_encode(["message" => "At least one field is required for update"]);
                return;
            }

            $sql = "UPDATE courses SET " . implode(", ", $updates) . " WHERE id = $id";
            if (mysqli_query($connected, $sql)) {
                echo json_encode(["message" => "Course updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Error updating course"]);
            }
            break;

        case "DELETE":
            if (!$id) {
                http_response_code(400);
                echo json_encode(["message" => "Course ID is required"]);
                return;
            }

            $sql = "DELETE FROM courses WHERE id = $id";
            if (mysqli_query($connected, $sql)) {
                echo json_encode(["message" => "Course deleted successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Error deleting course"]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed"]);
            break;
    }
}
?>
