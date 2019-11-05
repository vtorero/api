<?php

header('Access-Control-Allow-Origin:*');
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
$data=array();

$app->get("/skoda",function() use($db,$app){
    header("Content-type: application/json; charset=utf-8");
    $resultado = $db->query("SELECT * FROM  adops.skoda");  
    $clientes=array();
        while ($fila = $resultado->fetch_array()) {
            
            $clientes[]=$fila;
        }
        $data = array("status"=>200,"data"=>$clientes);
        echo  json_encode($data);
    });

$app->get("/productos",function() use($db,$app){
    header("Content-type: application/json; charset=utf-8");
    $resultado = $db->query("SELECT dimensionad_exchange_device_category,count(*) as total FROM adops.11223363888 
    where dimensionad_exchange_date between '2019-09-01' and '2019-09-29' group by 1 order by 2 desc");  
    $productos=array();
        while ($fila = $resultado->fetch_array()) {
            
            $productos[]=$fila;
        }
        $data = array("status"=>200,"data"=>$productos);
        echo  json_encode($data);
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

   $app->post("/login",function() use($db,$app){
         $json = $app->request->getBody();
        $data = json_decode($json, true);

        $resultado = $db->query("SELECT * FROM api.usuarios where usuario='".$data['usuario']."' and password='".$data['password']."'");  
        $usuario=array();
        while ($fila = $resultado->fetch_object()) {
        $usuario[]=$fila;
        }
        if(count($usuario)==1){
            $data = array("status"=>true,"rows"=>1,"data"=>$usuario);
        }else{
            $data = array("status"=>false,"rows"=>0,"data"=>null);
        }
        echo  json_encode($data);
    });



   $app->post("/reporte",function() use($db,$app){
    header("Content-type: application/json; charset=utf-8");
    $json = $app->request->getBody();
    $dat = json_decode($json, true);
    $arraymeses=array('Jan','Oct','Nov');
    $arraynros=array('01','10','11');
    $mes1=substr($dat['ini'], 0,3);
    $mes2=substr($dat['fin'], 0,3);
    $dia1=substr($dat['ini'], 3,2);
    $dia2=substr($dat['fin'], 3,2);
    $ano1=substr($dat['ini'], 5,4);
    $ano2=substr($dat['fin'], 5,4);
    $fmes1=str_replace($arraymeses,$arraynros,$mes1);
    $fmes2=str_replace($arraymeses,$arraynros,$mes2);
    $f1=$ano1.'-'.$fmes1.'-'.$dia1;
    $f2=$ano2.'-'.$fmes2.'-'.$dia2;


    $resultado = $db->query("SELECT dimensionad_exchange_device_category,count(*) as total FROM adops.11223363888  where dimensionad_exchange_date between '".$f1."' and '".$f2."' group by 1 order by 2 desc");  
    $datos=array();
        while ($fila = $resultado->fetch_array()) {
             $datos[]=$fila;
        }
        $data = array("status"=>200,"data"=>$datos,"envio"=>$f1.'--'.$f2);
        echo json_encode($data);
        });
   

    $app->post("/skoda",function() use($db,$app){
        $query ="INSERT INTO skoda (source,origen,nombres,apellidos,rut,telefono,correo,marca,modelo,concesionario)  VALUES ("
        ."'{$app->request->post("source")}',"
        ."'{$app->request->post("origen")}',"
         ."'{$app->request->post("nombres")}',"
         ."'{$app->request->post("apellidos")}',"
         ."'{$app->request->post("rut")}',"
         ."'{$app->request->post("telefono")}',"
         ."'{$app->request->post("correo")}',"
         ."'{$app->request->post("marca")}',"
         ."'{$app->request->post("modelo")}',"
         ."'{$app->request->post("concesionario")}'"
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