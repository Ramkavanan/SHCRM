<?php
$servername = "localhost";
$username = "zurmo";
$password = "pr0car3#";
$dbname = "zurmo";
$match =0;
$matchArr = array();
$mismatch=0;
$mismatchArr = array();
$recUpdated = 0;
$recUpdteFailed = 0;

$sql1NullResult = 0;
$sql2NullResult = 0;
$sql3NullResult = 0;

$log = array();
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql1 = "SELECT id,account_id,external_system_id FROM opportunity";
$result1 = $conn->query($sql1);
if ($result1->num_rows > 0) {    
    while($row1 = $result1->fetch_assoc()) {
    $currOppId = $row1['id'];
    $currAccId = $row1['account_id'];  
	$sql2="SELECT accId FROM importTempAkilan WHERE OpId ='".$row1["external_system_id"]."'";
        $result2 = $conn->query($sql2);
        if ($result2->num_rows > 0) { 
	    while($row2 = $result2->fetch_assoc()) {
           	$sql3 = "SELECT id FROM account WHERE external_system_id ='".$row2["accId"]."'";
                $result3 = $conn->query($sql3);
                if ($result3->num_rows > 0) {  
		        while($row3 = $result3->fetch_assoc()) {
		            if($row3['id'] ==$currAccId){
				$match++;
		                $matchArr[$currAccId] = $row3['id'];
		            }
		            else{
		                $mismatch++;
		                $mistmatchArr[$currAccId] = $row3['id'];
                                $updateSql = "UPDATE opportunity SET account_id='".$row3['id']."' WHERE id='".$currOppId."'";				
                                $updateRes = $conn->query($updateSql);
				if($updateRes){
					$recUpdated++;
					$log['success'][] = $currOppId;
				}
				else{
					$recUpdteFailed++;
					$log['failed'][] = $currOppId;
				}
		            }
		        }   
                 }
                 else{
                     $sql3NullResult++;
                 }             
            }
        }
        else{
          $sql2NullResult++;
        }
        //echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
    }
} else {
   $sql1NullResult++ ;
}
var_dump($log);
$conn->close();
?> 

