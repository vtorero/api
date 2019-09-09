<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}


require_once 'vendor/autoload.php';

$app = new Slim\Slim();

$db = new mysqli("localhost","marife","libido16","adops");
//mysqli_set_charset($db, 'utf8');
if (mysqli_connect_errno()) {
    printf("Conexión fallida: %s\n", mysqli_connect_error());
    exit();
}


$app->get("/productos",function() use($db,$app){

    $resultado = $db->query("SELECT dimensionad_exchange_device_category,count(*) as total FROM api.11223363888 
    where dimensionad_exchange_date between '2019-08-01' and '2019-08-25'group by 1 order by 2 desc");
    
    $productos=array();
        while ($fila = $resultado->fetch_array()) {
            
            $productos[]=$fila;
        }

        echo  json_encode($productos);
    });


    $app->post("/productos",function() use($db,$app){

        $query ="INSERT INTO productos VALUES (NULL,"
        ."'{$app->request->post("name")}',"
        ."'{$app->request->post("description")}',"
        ."'{$app->request->post("price")}'"
        .")";
        $insert= $db->query($query);
        if($insert){
            $result = array("STATUS"=>true,"messaje"=>"Producto creado correctamente");
        }else{
            $result = array("STATUS"=>false,"messaje"=>"Producto no creado");
        }

         echo json_encode($result);

    });        
    
    $app->put("/productos/:id",function($id) use($db,$app){

        $query ="UPDATE productos SET "
        ."name ='{$app->request->post("name")}',"
        ."description = '{$app->request->post("description")}',"
        ."price = {$app->request->post("price")} "
        ." WHERE id={$id}";

        echo $query;
        $update= $db->query($query);

        if($update){
            $result = array("STATUS"=>true,"messaje"=>"Producto actualizado correctamente");
        }else{
            $result = array("STATUS"=>false,"messaje"=>"Producto no actualizado");
        }

         echo json_encode($result);

    });        
        
    $app->delete("/productos/:id",function($id) use($db,$app){

    $query ="DELETE FROM productos where id = {$id}";
    $delete = $db->query($query);
    if($delete){
        $result = array("STATUS"=>true,"messaje"=>"Producto eliminado correctamente");
    }else{
        $result = array("STATUS"=>false,"messaje"=>"Producto no eliminado");
    }
     echo json_encode($result);
    });


    $app->post("/skoda",function() use($db,$app){
        $query ="INSERT INTO skoda VALUES (NULL,"
         ."'{$app->request->post("name")}',"
         ."'{$app->request->post("description")}',"
         ."'{$app->request->post("price")}'"
         .")";
         $insert= $db->query($query);
          if($insert){
          $result = array("STATUS"=>true,"messaje"=>"Skoda registrado correctamente");
           }else{
           $result = array("STATUS"=>false,"messaje"=>"Skoda no creado");
           }
            echo json_encode($result);
           }); 

$app->run();