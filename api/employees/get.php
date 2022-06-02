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
$data = $_GET;
$returnData = [];

if ($_SERVER["REQUEST_METHOD"] != "GET") :

    $returnData = msg(0, 404, 'Page Not Found!');

elseif (isset($data['id'])) :
      // connect to database
    $conn = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME, PORT);
    if ( $data['id'] !== "" ) {
         if( $smt = $conn->prepare("SELECT id, firstname, lastname, email, job_title FROM employees WHERE id = ?") ) {
            $smt->bind_param('s', $data['id']);
            $smt->execute();
            $smt->store_result();


            if ( $smt->num_rows > 0 ) {
                $smt->bind_result($id, $firstname, $lastname, $email, $job_title);
                $smt->fetch();

                $results = [
                    "id" => $id,
                    "firstname" => $firstname,
                    "lastname"  => $lastname,
                    "email"     => $email,
                    "job_title" => $job_title
                ];
                $returnData = msg(200, 200, [$results]);
                // create new users
                           
            }
            else {
                $returnData = msg(0, 422, 'No Employee Found');
            }

            $smt->close();
        } 
    }
    else {
        $returnData = msg(0, 422, 'Id Cannot Be Empty');
    }


// IF THERE ARE NO EMPTY FIELDS THEN-
else :
    // connect to database
    $conn = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME, PORT);

    $sql = "SELECT * FROM employees";
    $qry = mysqli_query($conn, $sql);

    if ( $qry ) {
        if ( mysqli_num_rows($qry) > 0 ) {
            $allResults = [];

            if ( mysqli_num_rows($qry) > 0 ) {

                while( $row = mysqli_fetch_assoc($qry) ) {
                    $allResults[] = $row;
                }
                $returnData = msg(200, 200, $allResults);
            }
            else {
                $returnData = msg(0, 422, 'Create New Employee');
            }

        }
        else {
            $returnData = msg(0, 422, 'No Employee Found');
        }
    }

endif;

if ( isset($conn) && !$conn ) {
    mysqli_close($conn);
}

echo json_encode($returnData);
?>