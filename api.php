<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';

$app = new Slim\Slim();

$db = new mysqli("localhost","root","vistazo","api");
mysqli_set_charset($db, 'utf8');
if (mysqli_connect_errno()) {
    printf("Conexión fallida: %s\n", mysqli_connect_error());
    exit();
}


$app->get("/productos",function() use($db,$app){

    $resultado = $db->query("SELECT * FROM productos;");
    $productos=array();
        while ($fila = $resultado->fetch_assoc()) {
            
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

$app->run();