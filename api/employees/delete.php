<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// database connection
include_once dirname(__FILE__, 3) . '/config.php';

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
        // check if employee id exists
        if ( $checkEmployeeId = $conn->prepare("SELECT id FROM employees WHERE id = ? ORDER BY id DESC LIMIT 1") ) {
            $checkEmployeeId->bind_param("i", $data['id']);
            $checkEmployeeId->execute();
            $checkEmployeeId->store_result();
            
            if ( $checkEmployeeId->num_rows > 0 ) {
                
                // delete selected employee data
                if( $smt = $conn->prepare("DELETE FROM employees WHERE id = ? ORDER BY id DESC LIMIT 1") ) {
                    $smt->bind_param('s', $data['id']);
                    $smt->execute();
        
        
                    if ( $smt->store_result() ) {
                        // employee has been deleted successfully
                        $returnData = msg(200, 200, 'Records Has Been Deleted Successfully');
                    }
                    else {
                        $returnData = msg(0, 422, 'An Error Occurred Please Contact Admin');
                    }
        
                    $smt->close();
                }
            }
            else {
                $returnData = msg(0, 422, 'Invalid Employee ID');
            }
            
            // close connections
            $checkEmployeeId->close();
        }
    }
    else {
        $returnData = msg(0, 422, 'Id Cannot Be Empty');
    }

else: 
    $returnData = msg(0, 422, 'Pass Employee Id as GET Parameter');
endif;

if ( isset($conn) && !$conn ) {
    mysqli_close($conn);
}

echo json_encode($returnData);
?>