<?php



// include the classes

include("../lib/db.class.php");
include_once "config.php";

$db = new DB($config['database'], $config['host'],$config['username'],$config['password']);

// 
$data = json_decode(file_get_contents("php://input"));

// To protect MySQL injection (more detail about MySQL injection)
$getemail = mysqli_real_escape_string($db->connection, $data->email);
$getpassword = mysqli_real_escape_string($db->connection,$data->password);


$email = stripslashes($getemail);
$password = stripslashes($getpassword);

 $response=array();
 $memberdata = array();

//md5('$password')
$sql = $db->loginUser("user", $email, md5($password));

$result = mysqli_query($db->connection, $sql) or $db->debugAndDie($sql);

// mysqli_num_row is counting table row
$count = mysqli_num_rows($result);
// If result matched $myusername and $mypassword, table row must be 1 row

if ($count == 1) {
// fetching the unique details from the user table
    $row = mysqli_fetch_array($result);

            $fullname=$row['fullname'];
		    $useremail=$row['email'];

        $response['email'] = $useremail;
        $response['fullname'] = $fullname;
        $response['status'] = "success";
		
		 //echo json_encode(array("result"=>$response));
         echo json_encode($response);
    
} 
else{
        $response['status'] = "error";
		$response['message'] = 'User details does not exist in our database.';
        $response['email'] = $getemail;
        $response['fullname'] = $getpassword;
    echo json_encode($response);
}


?>