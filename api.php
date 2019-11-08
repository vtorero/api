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

/*dashboard adops*/

   $app->post("/reporte",function() use($db,$app){
    header("Content-type: application/json; charset=utf-8");
    $json = $app->request->getBody();
    $dat = json_decode($json, true);
    $emp=$dat['emp'];
    $arraymeses=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
    $arraynros=array('01','02','03','04','05','06','07','08','09','10','11','12');
    $mes1=substr($dat['ini'], 0,3);
    $mes2=substr($dat['fin'], 0,3);
    $dia1=substr($dat['ini'], 3,2);
    $dia2=substr($dat['fin'], 3,2);
    $ano1=substr($dat['ini'], 5,4);
    $ano2=substr($dat['fin'], 5,4);
    $fmes1=str_replace($arraymeses,$arraynros,$mes1);
    $fmes2=str_replace($arraymeses,$arraynros,$mes2);
    $ini=$ano1.'-'.$fmes1.'-'.$dia1;
    $fin=$ano2.'-'.$fmes2.'-'.$dia2;

    $datocliente=$db->query("SELECT * FROM api.usuarios where empresa='".$emp."'");
       $infocliente=array();
  while ($cliente = $datocliente->fetch_array()) {
            $infocliente[]=$cliente;
        }

        $tasa=(float) $infocliente[0]["tasa"];


$ingreso=$db->query("SELECT ROUND(sum(columnad_exchange_ad_ecpm)*".$tasa.",2) ingreso_cpm,ROUND(sum(columnad_exchange_estimated_revenue)*".$tasa.",2) ingreso_total  FROM adops.11223363888   where  dimensionad_exchange_device_category <>'Connected TV' and dimensionad_exchange_network_partner_name='".$emp."' and dimensionad_exchange_date between '".$ini."' and '".$fin."'");
       $infoingreso=array();
  while ($row = $ingreso->fetch_array()) {
            $infoingreso[]=$row;
        }

    $resultado = $db->query("SELECT dimensionad_exchange_device_category,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888
    where dimensionad_exchange_device_category <>'Connected TV' and dimensionad_exchange_network_partner_name='".$emp."'  and 
    dimensionad_exchange_date between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00 group by 1 order by 2 desc");  
    $info=array();
        while ($fila = $resultado->fetch_array()) {
            
            $info[]=$fila;
        }
        $data = array("status"=>200,"data"=>$info,"envio"=>$dat,"ingreso"=>$infoingreso);
        echo json_encode($data);
        });
 


$app->post("/inicio",function() use($db,$app){
    header("Content-type: application/json; charset=utf-8");
    $json = $app->request->getBody();
    $dat = json_decode($json, true);
    $date = new DateTime();
    $date->modify('last day of this month');
    $date->format('Y-m-d');
    $ini=substr( $date->format('Y-m-d'),0,7).'-01';
    $fin = substr($date->format('Y-m-d'),0,10);
    $emp=$dat['emp'];

    
    $datocliente=$db->query("SELECT * FROM api.usuarios where empresa='".$emp."'");
       $infocliente=array();
  while ($cliente = $datocliente->fetch_array()) {
            $infocliente[]=$cliente;
        }

        $tasa=(float) $infocliente[0]["tasa"];

$ingreso=$db->query("SELECT ROUND(sum(columnad_exchange_ad_ecpm)*".$tasa.",2) ingreso_cpm,ROUND(sum(columnad_exchange_estimated_revenue)*".$tasa.",2) ingreso_total  FROM adops.11223363888   where  dimensionad_exchange_device_category <>'Connected TV' and dimensionad_exchange_network_partner_name='".$emp."' and dimensionad_exchange_date between '".$ini."' and '".$fin."'");
       $infoingreso=array();
  while ($row = $ingreso->fetch_array()) {
            $infoingreso[]=$row;
        }


  $resultado_diario = $db->query("SELECT dimensionad_exchange_date,round(sum(columnad_exchange_estimated_revenue),2)*0.8 as total FROM adops.11223363888
    where dimensionad_exchange_device_category <>'Connected TV' and dimensionad_exchange_network_partner_name='Latina.pe'  and 
    dimensionad_exchange_date between '2019-11-01' and '2019-11-06' and round(columnad_exchange_estimated_revenue,2)>0.00 group by 1 order by 1 asc");  
    $infodia=array();
        while ($filadia = $resultado_diario->fetch_array()) {
            
            $infodia[]=$filadia;
        }


    $resultado = $db->query("SELECT dimensionad_exchange_device_category,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888
    where dimensionad_exchange_device_category <>'Connected TV' and dimensionad_exchange_network_partner_name='".$emp."'  and 
    dimensionad_exchange_date between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00 group by 1 order by 2 desc");  
    $info=array();
        while ($fila = $resultado->fetch_array()) {
            
            $info[]=$fila;
        }
        $data = array("status"=>200,"data"=>$info,"ingreso"=>$infoingreso,"diario"=>$infodia);
        echo  json_encode($data);




    });


/*final adops dashobard*/

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