<?php
 
require 'vendor/autoload.php';
function getDB()
{
    $dbhost = "139.59.0.121";
    $dbuser = "fhdyvhcrdw";
    $dbpass = "PQRt9KM9aq";
    $dbname = "fhdyvhcrdw";
 
    $mysql_conn_string = "mysql:host=$dbhost;dbname=$dbname";
    $dbConnection = new PDO($mysql_conn_string, $dbuser, $dbpass); 
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbConnection;
}
 
$app = new \Slim\App(); 
 
$app->get('/api/', function ($request, $response, $args) {
    $response->withStatus(200, "success");
	$response->write("Welcome to slim framework folks!");
    return $response;
});

$app->get('/api/createTable/', function($request, $response, $args){
	try{
		$db = getDB();
		#$response->write("Database connection success!");
		$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$stmnt= ("CREATE TABLE IF NOT EXISTS `students` (
					`student_id` int(10) NOT NULL auto_increment,
					`score` int(10) default '0',
					`first_name` varchar(50) default NULL,
					`last_name` varchar(50) default NULL,
					PRIMARY KEY  (`student_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
					
		$db->execute($stmnt);
		
			$response->withStatus(200, "success");
			$response->withHeader('content-type','application/json');
			$response->write(json_encode(array("status" => "success", "code" => 1)));
			$db = null;
		
		}catch(PDOException $ex){
			$response->write($ex->getMessage());
		}
});

$app->get('/api/getScore/{id}', function ($request, $response, $args) {
	try{
		$db = getDB();
		#$response->write("Database connection success!");
		$stmnt= $db->prepare("SELECT * 
            FROM students
            WHERE student_id = :id");
		$id = $args['id'];
		$stmnt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmnt->execute();
		$student=$stmnt->fetch(PDO::FETCH_OBJ);
		if($student){
			$response->withStatus(200, "success");
			$response->withHeader('content-type','application/json');
			$response->write(json_encode($student));
			$db=null;
		} else{
			throw new PDOException("No record found");
		}
		
		}catch(PDOException $ex){
			$response->write($ex->getMessage());
		}
});
$app->get('/api/selectAll/', function ($request, $response, $args) {
	try{
		$db = getDB();
		#$response->write("Database connection success!");
		$stmnt= $db->prepare("SELECT * 
            FROM students");
		$stmnt->execute();
		$student=$stmnt->fetchAll();
		if($student){
			$response->withStatus(200, "success");
			$response->withHeader('content-type','application/json');
			$response->write(json_encode($student));
			$db=null;
		} else{
			throw new PDOException("No record found");
		}
		
		}catch(PDOException $ex){
			$response->write($ex->getMessage());
		}
});
$app->post('/api/createStudent', function ($request, $response, $args) {
	 $data = $request->getParsedBody();
	 
	 $fname = $data['firstName'];
	 $lname = $data['lastName'];
	 $score = $data['score'];
	 
	try{
		$db = getDB();
		#$response->write("Database connection success!");
		$stmnt= $db->prepare("INSERT INTO students (`score`, `first_name`, `last_name`) VALUES (:score, :fname, :lname)");
		$stmnt->bindParam(':score', $score, PDO::PARAM_INT);
		$stmnt->bindParam(':fname', $fname, PDO::PARAM_STR);
		$stmnt->bindParam(':lname', $lname, PDO::PARAM_STR);
		$stmnt->execute();
		
			$response->withStatus(200, "success");
			$response->withHeader('content-type','application/json');
			$response->write(json_encode(array("status" => "success", "code" => 1)));
			$db = null;
		
		}catch(PDOException $ex){
			$response->write($ex->getMessage());
		}
}); 
 
$app->run();
?>