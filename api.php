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

$app->post("/tablaconsulta",function() use($db,$app){
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

      $resultado_diario = $db->query("SELECT dimensionad_exchange_date,FORMAT(AVG(columnad_exchange_ad_ecpm),2) columnad_exchange_ad_ecpm,FORMAT(SUM(columnad_exchange_impressions),0) columnad_exchange_impressions,FORMAT(SUM(columnad_exchange_estimated_revenue*".$tasa."),2) columnad_exchange_estimated_revenue FROM adops.11223363888
    where dimensionad_exchange_network_partner_name='".$emp."' 
and dimensionad_exchange_date between '".$ini."' and '".$fin."' GROUP BY 1 order by 1 desc");  
    $infotabla=array();
        while ($filatabla = $resultado_diario->fetch_array()) {
                        $infotabla[]=$filatabla;
        }
        
        
        $respuesta=json_encode($infotabla);
        echo  $respuesta;

});



$app->post("/tabla",function() use($db,$app){
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

      $resultado_diario = $db->query("SELECT dimensionad_exchange_date,FORMAT(AVG(columnad_exchange_ad_ecpm),2) columnad_exchange_ad_ecpm,
        FORMAT(SUM(columnad_exchange_impressions),0) columnad_exchange_impressions,FORMAT(SUM(columnad_exchange_estimated_revenue*".$tasa."),2) columnad_exchange_estimated_revenue FROM adops.11223363888
    where dimensionad_exchange_network_partner_name='".$emp."' 
and dimensionad_exchange_date between '".$ini."' and '".$fin."' GROUP BY 1 order by 1 desc");  
    $infotabla=array();
        while ($filatabla = $resultado_diario->fetch_array()) {
                        $infotabla[]=$filatabla;
        }
        
        
        $respuesta=json_encode($infotabla);
        echo  $respuesta;

});



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

$app->post("/generalget",function() use($db,$app) {
header("Content-type: application/json; charset=utf-8");
    $json = $app->request->getBody();
    $data = json_decode($json, true);
      $datos=$db->query("SELECT * FROM api.dash_general WHERE empresa='{$data["empresa"]}'");
       $infocliente=array();
  while ($cliente = $datos->fetch_object()) {
            $infocliente[]=$cliente;
        }
        $return=array("data"=>$infocliente);

           echo  json_encode($return);
});


 $app->post("/general",function() use($db,$app){
    header("Content-type: application/json; charset=utf-8");
       $json = $app->request->getBody();
       $j = json_decode($json,true);
       $data = json_decode($j['json']);

        
        $nombres=(is_array($data->nombres))? array_shift($data->nombres): $data->nombres;
        $correo=(is_array($data->correo))? array_shift($data->correo): $data->correo;
        $telefono=(is_array($data->telefono))? array_shift($data->telefono): $data->telefono;
        $sociedad=(is_array($data->sociedad))? array_shift($data->sociedad): $data->sociedad;
        $paginas=(is_array($data->paginas))? array_shift($data->paginas): $data->paginas;
        $rut=(is_array($data->rut))? array_shift($data->rut): $data->rut;
        $domicilio=(is_array($data->domicilio))? array_shift($data->domicilio): $data->domicilio;
        $calle=(is_array($data->calle))? array_shift($data->calle): $data->calle;
        $numero=(is_array($data->numero))? array_shift($data->numero): $data->numero;            
        $ciudad=(is_array($data->ciudad))? array_shift($data->ciudad): $data->ciudad;
        $pais=(is_array($data->pais))? array_shift($data->pais): $data->pais;
        $confinanzas=(is_array($data->confinanzas))? array_shift($data->confinanzas): $data->confinanzas;
        $tlffinanzas=(is_array($data->tlffinanzas))? array_shift($data->tlffinanzas): $data->tlffinanzas;
        $correofinan=(is_array($data->correofinan))? array_shift($data->correofinan): $data->correofinan;
        $medios=(is_array($data->medios))? array_shift($data->medios): $data->medios;
        $empresa=$data->empresa;



        $contar=array();
        $cantidad=$db->query("SELECT * FROM api.dash_general WHERE empresa='{$empresa}'");
  while ($cliente = $cantidad->fetch_array()) {
            $contar[]=$cliente;
        }


if(count($contar)>0){ 

     $query ="UPDATE api.dash_general  SET "
        ."nombres ='{$nombres}',"
        ."correo = '{$correo}',"
        ."telefono = '{$telefono}',"
        ."sociedad = '{$sociedad}',"
        ."paginas = '{$paginas}',"
        ."rut = '{$rut}',"
        ."domicilio = '{$domicilio}',"
        ."calle = '{$calle}',"
        ."numero = '{$numero}',"
        ."ciudad = '{$ciudad}',"
        ."pais = '{$pais}',"
        ."confinanzas = '{$confinanzas}',"
        ."tlffinanzas = '{$tlffinanzas}',"
        ."correofinan = '{$correofinan}',"
        ."medios = '{$medios}'"
        ." WHERE empresa='{$empresa}'";
          
          $update=$db->query($query);

      
    }else{
        $query ="INSERT INTO api.dash_general (correo,empresa,nombres,telefono,sociedad,paginas,rut,domicilio,calle,numero,ciudad,pais,confinanzas,tlffinanzas,correofinan,medios) VALUES ("
      ."'{$correo}',"
      ."'{$empresa}',"
      ."'{$nombres}',"
      ."'{$telefono}',"
      ."'{$sociedad}',"
      ."'{$paginas}',"
      ."'{$rut}',"
      ."'{$domicilio}',"
      ."'{$calle}',"
      ."'{$numero}',"
      ."'{$ciudad}',"
      ."'{$pais}',"
      ."'{$confinanzas}',"
      ."'{$tlffinanzas}',"
      ."'{$correofinan}',"
      ."'{$medios}'"
          .")";
   
      $insert=$db->query($query);
    }
       if(count($contar)>0){
       $result = array("STATUS"=>true,"messaje"=>"Usuario actualizado correctamente");
        }else{
        $result = array("STATUS"=>false,"messaje"=>"Usuario creado correctamente");
        }
        echo  json_encode($result);
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
    $inicio=$dia1.'/'.$fmes1;
    $final=$dia2.'/'.$fmes2;


    $datocliente=$db->query("SELECT * FROM api.usuarios where empresa='".$emp."'");
       $infocliente=array();
  while ($cliente = $datocliente->fetch_array()) {
            $infocliente[]=$cliente;
        }

        $tasa=(float) $infocliente[0]["tasa"];


$ingreso=$db->query("SELECT FORMAT(avg(columnad_exchange_ad_ecpm)*".$tasa.",2) ingreso_cpm,ROUND(sum(columnad_exchange_estimated_revenue)*".$tasa.",2) ingreso_total ,FORMAT(sum(columnad_exchange_impressions),0) impresiones FROM adops.11223363888   where dimensionad_exchange_network_partner_name='".$emp."' and dimensionad_exchange_date between '".$ini."' and '".$fin."'");
       $infoingreso=array();
  while ($row = $ingreso->fetch_array()) {
            $infoingreso[]=$row;
        }

   
              $resultado_desk = $db->query("SELECT concat(SUBSTRING(dimensionad_exchange_date,6,2),'/',SUBSTRING(dimensionad_exchange_date,9,2)) dimensionad_exchange_date,FORMAT(sum(columnad_exchange_estimated_revenue)*".$tasa.",2) as total FROM adops.11223363888
    where  dimensionad_exchange_network_partner_name='".$emp."' and 
    dimensionad_exchange_date between '".$ini."' and '".$fin."' group by 1 order by 1 asc");  
    $infodesk=array();
        while ($filadesk= $resultado_desk->fetch_array()) {
            
            $infodesk[]=$filadesk;
        }    

        

          $resultado_table = $db->query("SELECT dimensionad_exchange_date,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888
    where  dimensionad_exchange_network_partner_name='".$emp."' and 
    dimensionad_exchange_date between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00 and dimensionad_exchange_device_category='Tablets' group by 1 order by 1 asc");  
    $infotablet=array();
        while ($filatab = $resultado_table->fetch_array()) {
            
            $infotablet[]=$filatab;
        }


          $resultado_mobil = $db->query("SELECT dimensionad_exchange_date,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888
    where dimensionad_exchange_network_partner_name='".$emp."' and 
    dimensionad_exchange_date between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00 and dimensionad_exchange_device_category='High-end mobile devices' group by 1 order by 1 asc");  
    $infomovil=array();
        while ($filamob = $resultado_mobil->fetch_array()) {
            
            $infomovil[]=$filamob;
        }


    $resultado = $db->query("SELECT REPLACE(dimensionad_exchange_device_category,'High-end mobile devices','Mobile') dimensionad_exchange_device_category,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888
    where  dimensionad_exchange_network_partner_name='".$emp."'  and 
    dimensionad_exchange_date between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00 group by 1 order by 2 desc");  
    $info=array();
        while ($fila = $resultado->fetch_array()) {
            
            $info[]=$fila;
        }

     $result_creative = $db->query("SELECT dimensionad_exchange_creative_sizes,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888
    where dimensionad_exchange_network_partner_name='".$emp."'  and 
    dimensionad_exchange_date between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00 group by 1 order by 2 desc limit 5");  
    $info_creative=array();
        while ($filac = $result_creative->fetch_array()) {
            
            $info_creative[]=$filac;
        }
        
        $data = array("status"=>200,"data"=>$info,"ingreso"=>$infoingreso,"creatives"=>$info_creative,"diario_desktop"=>$infodesk,"diario_tablet"=>$infotablet,"inicio"=>$inicio,"final"=>$final);
        echo json_encode($data);
        });
 


$app->post("/inicio",function() use($db,$app){
    header("Content-type: application/json; charset=utf-8");
    $json = $app->request->getBody();
    $dat = json_decode($json, true);
    $date = new DateTime();
    $date2 = new DateTime();
    $date->modify('last day of this month');
    $date2->modify('first day of this month');
    $date->format('Y-m-d');
    $ini=substr( $date->format('Y-m-d'),0,7).'-01';
    $fin = substr($date->format('Y-m-d'),0,10);
    $inicio=$date2->format('d/m');
    $final=date("d/m",strtotime("- 1 days"));
    $emp=$dat['emp'];

    
    $datocliente=$db->query("SELECT * FROM api.usuarios where empresa='".$emp."'");
       $infocliente=array();
      while ($cliente = $datocliente->fetch_array()) {
            $infocliente[]=$cliente;
        }

        $tasa=(float) $infocliente[0]["tasa"];


  $resultado_diario = $db->query("SELECT dimensionad_exchange_date ,dimensionad_exchange_creative_sizes ,dimensionad_exchange_device_category  ,columnad_exchange_impressions ,columnad_exchange_estimated_revenue*".$tasa." columnad_exchange_estimated_revenue FROM adops.11223363888
    where dimensionad_exchange_network_partner_name='".$emp."' and 
    dimensionad_exchange_date between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00  order by 1 desc");  
    $infotabla=array();
        while ($filatabla = $resultado_diario->fetch_array()) {
            
            $infotabla[]=$filatabla;
        }



  $resultado_diario = $db->query("SELECT dimensionad_exchange_date,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888
    where dimensionad_exchange_network_partner_name='".$emp."' and 
    dimensionad_exchange_date between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00 group by 1 order by 1 asc");  
    $infodia=array();
        while ($filadia = $resultado_diario->fetch_array()) {
            
            $infodia[]=$filadia;
        }


              $resultado_desk = $db->query("SELECT concat(SUBSTRING(dimensionad_exchange_date,6,2),'/',SUBSTRING(dimensionad_exchange_date,9,2)) dimensionad_exchange_date,FORMAT(sum(columnad_exchange_estimated_revenue)*".$tasa.",2) as total FROM adops.11223363888
    where  dimensionad_exchange_network_partner_name='".$emp."' and 
    dimensionad_exchange_date between '".$ini."' and '".$fin."' group by 1 order by 1 asc");  
    $infodesk=array();
        while ($filadesk= $resultado_desk->fetch_array()) {
            
            $infodesk[]=$filadesk;
        }    

        

          $resultado_table = $db->query("SELECT dimensionad_exchange_date,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888
    where dimensionad_exchange_network_partner_name='".$emp."' and 
    dimensionad_exchange_date between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00 and dimensionad_exchange_device_category='Tablets' group by 1 order by 1 asc");  
    $infotablet=array();
        while ($filatab = $resultado_table->fetch_array()) {
            
            $infotablet[]=$filatab;
        }


          $resultado_mobil = $db->query("SELECT dimensionad_exchange_date,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888
    where dimensionad_exchange_network_partner_name='".$emp."' and 
    dimensionad_exchange_date between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00 and dimensionad_exchange_device_category='High-end mobile devices' group by 1 order by 1 asc");  
    $infomovil=array();
        while ($filamob = $resultado_mobil->fetch_array()) {
            
            $infomovil[]=$filamob;
        }



    $resultado = $db->query("SELECT REPLACE(dimensionad_exchange_device_category,'High-end mobile devices','Mobile') dimensionad_exchange_device_category,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888
    where dimensionad_exchange_network_partner_name='".$emp."'  and 
    dimensionad_exchange_date between '".$ini."' and '".$fin."' group by 1 order by 2 desc");  
    $info=array();
        while ($fila = $resultado->fetch_array()) {
            
            $info[]=$fila;
        }

    $result_creative = $db->query("SELECT dimensionad_exchange_creative_sizes,round(sum(columnad_exchange_estimated_revenue),2)*".$tasa." as total FROM adops.11223363888
    where  dimensionad_exchange_network_partner_name='".$emp."'  and 
    dimensionad_exchange_date between '".$ini."' and '".$fin."' and round(columnad_exchange_estimated_revenue,2)>0.00 group by 1 order by 2 desc limit 5");  
    $info_creative=array();
        while ($filac = $result_creative->fetch_array()) {
            
            $info_creative[]=$filac;
        }

        
       $ingreso=$db->query("SELECT ROUND(AVG(columnad_exchange_ad_ecpm)*".$tasa.",2) ingreso_cpm,ROUND(sum(columnad_exchange_estimated_revenue)*".$tasa.",2) ingreso_total,FORMAT(sum(columnad_exchange_impressions),0) impresiones  FROM adops.11223363888   where  dimensionad_exchange_network_partner_name='".$emp."' and dimensionad_exchange_date between '".$ini."' and '".$fin."'");
       $infoingreso=array();
  while ($row = $ingreso->fetch_array()) {
            $infoingreso[]=$row;
        }
        
        $data = array("status"=>200,"data"=>$info,"ingreso"=>$infoingreso,"diario"=>$infodia,"diario_desktop"=>$infodesk,"diario_tablet"=>$infotablet,"diario_movil"=>$infomovil,"creatives"=>$info_creative,"inicio"=>$inicio,"final"=>$final);
        echo  json_encode($data);




    });


/*final adops dashobard*/

    $app->post("/skoda",function() use($db,$app){
        $query ="INSERT INTO skoda (source,origen,nombres,apellidos,rut,telefono,correo,marca,modelo,concesionario,dispositivo)  VALUES ("
        ."'{$app->request->post("source")}',"
        ."'{$app->request->post("origen")}',"
         ."'{$app->request->post("nombres")}',"
         ."'{$app->request->post("apellidos")}',"
         ."'{$app->request->post("rut")}',"
         ."'{$app->request->post("telefono")}',"
         ."'{$app->request->post("correo")}',"
         ."'{$app->request->post("marca")}',"
         ."'{$app->request->post("modelo")}',"
         ."'{$app->request->post("concesionario")}',"
         ."'{$app->request->post("dispositivo")}'"
         .")";

         $insert= $db->query($query);
          if($insert){
          $result = array("STATUS"=>true,"messaje"=>"Skoda registrado correctamente");
           }else{
           $result = array("STATUS"=>false,"messaje"=>"Skoda no creado");
           }
            echo json_encode($result);
           }); 


function traer_datos($ini,$fin,$emp,$tasa){
$db=new mysqli("localhost","marife","libido16","adops");
    
    $sql="SELECT ROUND(sum(columnad_exchange_ad_ecpm)*".$tasa.",2) ingreso_cpm,ROUND(sum(columnad_exchange_estimated_revenue)*".$tasa.",2) ingreso_total  FROM adops.11223363888   where dimensionad_exchange_network_partner_name='".$emp."' and dimensionad_exchange_date between ".$ini." and ".$fin;

 $ingreso=$db->query($sql);
    
     $data=array();
       while ($row = $ingreso->fetch_array()) {
         $data[]=$row;
     }
        return $data;
}

$app->run();