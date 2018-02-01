<?php

 if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }


// include the classes

include("../lib/db.class.php");
include_once "config.php";

$db = new DB($config['database'], $config['host'],$config['username'],$config['password']);

$data = json_decode(file_get_contents("php://input"));

// To protect MySQL injection (more detail about MySQL injection)
$getemail = mysqli_real_escape_string($db->connection, $data->email);
$getpassword = mysqli_real_escape_string($db->connection,$data->password);
$getfullname = mysqli_real_escape_string($db->connection,$data->fullname);


$email = stripslashes($getemail);
$v_password = stripslashes($getpassword);
$password = md5($v_password);
$fullname = stripslashes($getfullname);

 $response=array();
 $memberdata = array();


$sqlcheck = $db->loginUser("user", $email, $password);

$resultcheck = mysqli_query($db->connection, $sqlcheck) or $db->debugAndDie($sqlcheck);

// mysqli_num_row is counting table row
$countcheck = mysqli_num_rows($resultcheck);
// If result matched $myusername and $mypassword, table row must be 1 row

if ($countcheck == 1) {
    
        $response['status'] = "error";
		$response['message'] = 'Are you sure the email is yours? Member already exist';
    
} else{
    
    // for user details upload    


$sql = $db->addUser("user", $fullname, $email, $password);

$result = mysqli_query($db->connection, $sql) or $db->debugAndDie($sql);


if ($result) {

        $response['status'] = "success";
        $response['message'] = 'User details has been added succesfully';
		
         echo json_encode($response);
    
    // for user passport upload
    
    if($_GET['action'] == "upload"){
	
	if(!empty($_FILES['file'])){

		$ext = pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION);
		
		$file_size = $_FILES['file']['size'];
		$file_type = $_FILES['file']['type'];
		// new file size in KB
		$valid_size = 5000024;  
		// valid image extensions
		$valid_extensions = array('jpg','jpeg'); // valid extensions , 'jpg', 'png', 'gif'
		
		// allow valid image file formats
			if(in_array($ext, $valid_extensions)){		
			// check file size <= 1024
		if ($file_size > $valid_size){
			echo json_encode(array("response"=>'image size must not exceed 40kb')); 
		}else {
	

                $image = time().'.'.$ext;

               if( move_uploaded_file($_FILES["file"]["tmp_name"], 'images/'.$image)){

		echo json_encode(array("response"=>'image has been uploaded')); 
		// .$image;
			   }
			   else{echo json_encode(array("response"=>'does not move image')); 
			   }
	}
			}else{
				echo json_encode(array("response"=>'Sorry, only JPEG is allowed.')); 
				
	}
			   
			   

	}else{echo json_encode(array("response"=>'image is empty')); }
}

else{
		echo json_encode(array("response"=>'invalid access url')); 
}

    
} 
else{
        $response['status'] = "error";
		$response['message'] = 'User details cannot be added, please contact your administrator.';
    echo json_encode($response);
}

}

?>