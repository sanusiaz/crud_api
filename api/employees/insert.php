<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// database connection
include_once dirname(__FILE__, 3) . './config.php';

function msg($success, $status, $message, $extra = [])
{
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ], $extra);
}
// DATA FORM REQUEST
$data = $_POST;
$returnData = [];

if ($_SERVER["REQUEST_METHOD"] != "POST") :

    $returnData = msg(0, 404, 'Page Not Found!');

elseif (
    
    !isset($data['firstname'])
    || !isset($data['lastname'])
    || !isset($data['email'])
    || !isset($data['user_job'])
    || empty(trim($data['firstname']))
    || empty(trim($data['lastname']))
    || empty(trim($data['email']))
    || empty(trim($data['user_job']))
) :


    $fields = ['fields' => ['firstname', 'lastname', 'email', 'user_job']];
    $returnData = msg(0, 422, 'Please Fill in all Required Fields! ');

// IF THERE ARE NO EMPTY FIELDS THEN-
else :
    
    // connect to database
    $conn = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME, PORT);

    $firstname 	= trim($data['firstname']);
    $lastname 	= trim($data['lastname']);
    $email 		= trim($data['email']);
    $user_job 	= trim($data['user_job']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) :
        $returnData = msg(0, 422, 'Invalid Email Address!');

    else :
        try {

           $createEmployee = $conn->prepare("INSERT INTO employees(firstname, lastname, email, job_title) VALUES(?, ?, ?, ?)");
            if ( $createEmployee ) {
                $createEmployee->bind_param('ssss', $firstname, $lastname, $email, $user_job);
                $createEmployee->execute();
                


                if ($createEmployee->store_result() ) {
                    $returnData = msg(200, 200, 'Employee Has Been Added');
                }

                $createEmployee->close();
            }
            else {
                $returnData = msg(0,519, 'An Error Occurred In Inserting New Users Records');
            }
        } catch (PDOException $e) {
            $returnData = msg(0, 500, $e->getMessage());
        }
    endif;
endif;

if ( isset($conn) && !$conn ) {
    mysqli_close($conn);
}

echo json_encode($returnData);
?>