<?php

require '../vendor/autoload.php';


$app = new \Slim\App;

//get container
$container = $app->getContainer();

$container['view'] = function($container){
    return new \Slim\Views\PhpRenderer('../templates/');
};



$app->get('/admin',function($req, $res){
    return $this->view->render($res,'calender_admin.html');
});

$app->get('/api',function( $request, $response){
    $starttimestamp = $request->get('start');
    $endtimestamp = $request->get('end');
    
    try{
        //open database
        $conn = new PDO('mysql:host=127.0.0.1;dbname=ucsc_calender','Emalsha','1994224er');
        
        //Query database
        $stmt = $conn->prepare('SELECT * FROM events WHERE start >= FROM_UNIXTIME(:start) AND end < FROM_UNIXTIME(:end) ORDER BY start ASC');
        $stmt->bindParam(':start',$starttimestamp,\PDO::PARAM_INT);
        $stmt->binfParam(':end',$endtimestamp,\PDO::PARAM_INT);
        
        //Execute query
        $stmt->execute();
        
        //fetch results
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        //return result
        echo json_encode($results);
        
    }catch(\PDOException $e){
        $app -> halt(500,$e->getMessage());
    }
});

$app->run();

?>