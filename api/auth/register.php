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
    
    !isset($data['name'])
    || !isset($data['email'])
    || !isset($data['password'])
    || empty(trim($data['name']))
    || empty(trim($data['email']))
    || empty(trim($data['password']))
) :


    $fields = ['fields' => ['name', 'email', 'password']];
    $returnData = msg(0, 422, 'Please Fill in all Required Fields! '. $data['email'] , $fields);

// IF THERE ARE NO EMPTY FIELDS THEN-
else :
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    // connect to database
    $conn = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME, PORT);

    $name = trim($data['name']);
    $email = trim($data['email']);
    $password = trim($data['password']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) :
        $returnData = msg(0, 422, 'Invalid Email Address!');

    elseif (strlen($password) < 8) :
        $returnData = msg(0, 422, 'Your password must be at least 8 characters long!');

    elseif (strlen($name) < 3) :
        $returnData = msg(0, 422, 'Your name must be at least 3 characters long!');

    else :
        try {

            // check if the users exists
            if( $smt = $conn->prepare("SELECT * FROM users WHERE email = ?") ) {
                $smt->bind_param('s', $data['email']);
                $smt->execute();
                $smt->store_result();


                if ( $smt->num_rows < 1 ) {
                    // create new users
                    
                    $createUser = $conn->prepare("INSERT INTO users(email, password, name) VALUES(?, ?, ?)");
                    if ( $createUser ) {
                        $createUser->bind_param('sss', $data['email'], $data['password'], $data['name']);
                        $createUser->execute();
                        


                        if ($createUser->store_result() ) {
                            $returnData = msg(200, 200, 'Account Has Been Created Successfully');
                        }

                        $createUser->close();
                    }
                    else {
                        $returnData = msg(0,519, 'An Error Occurred In Inserting New Users Records');
                    }
                }
                else {
                    $returnData = msg(0, 422, 'An Account Exists with email Address');
                }

                $smt->close();
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