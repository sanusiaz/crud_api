<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// database connection
include_once dirname(__FILE__, 3) . './config.php';

// all functions file
include_once dirname(__FILE__, 3) . './includes/functions/all_functions.php';

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
    
    !isset($data['email'])
    || !isset($data['password'])
    || empty(trim($data['email']))
    || empty(trim($data['password']))
) :


    $fields = ['fields' => ['email', 'password']];
    $returnData = msg(0, 422, 'Please Fill in all Required Fields!');

// IF THERE ARE NO EMPTY FIELDS THEN-
else :

    // connect to database
    $conn = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME, PORT);
    
    $email = trim($data['email']);
    $password = trim($data['password']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) :
        $returnData = msg(0, 422, 'Invalid Email Address!');

    elseif (strlen($password) < 8) :
        $returnData = msg(0, 422, 'Your password must be at least 8 characters long!');

    else :
        try {

            // check if the users exists
            if( $smt = $conn->prepare("SELECT password as hashedPassword FROM users WHERE email = ?") ) {

                $smt->bind_param('s', $data['email']);
                $smt->execute();
                $smt->store_result();


                if ( $smt->num_rows > 0 ) {
                    $smt->bind_result($hashedPassword);
                    $smt->fetch();

                    // check if password matches
                    if ( password_verify($data['password'], $hashedPassword) ) {
                        # regenerate login token
                        $loginToken = generateRandomString(100);

                        $duration = 3600;

                        // set users token in database
                        if ( $usersToken = $conn->prepare("UPDATE users SET token = ?, duration = ? WHERE email = ?") ) {
                            $usersToken->bind_param("sss", $loginToken, $duration, $email);
                            $usersToken->execute();

                            if ( $usersToken->store_result() ) {
                                // records has been updated
                                $returnData = msg(200, 200, [
                                        'token' => $loginToken,
                                        'message' => 'Login Successful'
                                ]);
                                // set users cookies
                                setcookie('loginToken', $loginToken, $duration);
                            }
                            else {
                                $returnData = msg(0, 422, 'Cannot Check Token. Please Try Again Later');
                            }
                            // close connection
                            $usersToken->close();
                        }

                       
                    }
                    else {
                        // incorrect password
                        $returnData = msg(0, 422, 'Incorrect Details Entered ');
                    }
                }
                else {
                    $returnData = msg(0, 422, 'No Account Found');
                }

                // close connection
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