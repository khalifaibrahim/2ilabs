<?php



// include the classes

include("../lib/db.class.php");
include_once "config.php";

$db = new DB($config['database'], $config['host'],$config['username'],$config['password']);



 $response=array();
 $quizdata = array();
$quizList_array =array();

//md5('$password')
$sql = $db->fetchQuiz("quiz");

$result = mysqli_query($db->connection, $sql) or $db->debugAndDie($sql);

// mysqli_num_row is counting table row
$count = mysqli_num_rows($result);
// If result matched $myusername and $mypassword, table row must be 1 row

if(mysqli_num_rows ($result) > 0) {
    // fetching the unique details from the user table
   
	while($row = mysqli_fetch_array($result))
    { 
        $quizdata[] = $row;
//      $quizdata['question']=$row['question'];
//      //$quizdata['quizlist'] = array();
//        $response['status'] = "success";
//        
//        array_push($quizList_array,$quizdata);
                                              
     }
    
     echo json_encode(array('quizlist'=>$quizdata));

    
    
	}else{  $response['status'] = "error";
          $response['message'] = "No question is found"; 
          echo json_encode($response);
         }







?>