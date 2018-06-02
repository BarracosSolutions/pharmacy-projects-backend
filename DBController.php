<?php
$path_only = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode('/', $path_only);
if(count($parts) > 1){
    $last = array_pop($parts);
}
$parts = implode('/', $parts);
if($parts == ''){
    $path = "http://localhost/DBHandler.php/";
}else{
    $path = "http://localhost".$parts."/DBHandler.php/";
}

 #Constants
define('_PATHFactura_', $path.'Factura');
define('_PATHProducto_', $path.'Producto');
define('_PATHDisable_', $path.'Factura');

class DBController {

    public function selectFacturas(){
        $data = file_get_contents(_PATHFactura_);
        $json = json_decode($data, true);
 
        return $json;
    
    }

    public function selectFactura($numero){
        $path = _PATHFactura_.'/'.$numero;
        $data = file_get_contents($path);
        $json = json_decode($data, true);

        return  $json;
    }

    public function selectProductos($facturaId){
        $path = _PATHProducto_.'/'.$facturaId;
        $data = file_get_contents($path);
        $json = json_decode($data, true);
        return  $json;
    }

    public function insertarFactura($numero, $fecha, $cliente,  $impuestos, $total){
        $data = array('numero'=>$numero,'fecha'=>$fecha,
              'cliente'=>$cliente,'impuestos' => $impuestos,'total' => $total, 'isUpdate' => false);
        $path = _PATHFactura_;
        $options = array(
            'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($path, false, $context);
        $json = json_decode($result, true);
        return  $json;
    }

     public function updateFactura($numero, $fecha, $cliente,  $impuestos, $total){
      
        $data = array('numero'=>$numero,'fecha'=>$fecha,
              'cliente'=>$cliente,'impuestos' => $impuestos,'total' => $total, 'isUpdate' => true);
        $path = _PATHFactura_;
        $options = array(
            'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($path, false, $context);
        $json = json_decode($result, true);
        return  $json;
     }

     public function deleteFactura($numero) {
        $data = array();
        $path = _PATHFactura_.'/'.$numero;
        $options = array(
            'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($path, false, $context);
        $json = json_decode($result, true);
        return  $json;
     }

     public function deleteProduct($id){
        $data = array();
        $path = _PATHProducto_.'/'.$id;
        $options = array(
            'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($path, false, $context);
        $json = json_decode($result, true);
        return  $json;

     }

      public function insertarProducto($facturaId, $producto){

        $data = array('facturaId'=>$facturaId,'cantidad'=>$producto['cantidad'],'descripcion'=>$producto['descripcion'],'valor'=>$producto['valor'],'subtotal'=>$producto['subtotal']);
        $path = _PATHProducto_;
        $options = array(
            'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($path, false, $context);
        $json = json_decode($result, true);
        return  $json;
      }

      public function deleteAllDisabledData(){
        $data = array();
        $path = _PATHDisable_;
        $options = array(
            'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($path, false, $context);
        $json = json_decode($result, true);
        return  $json;
      }

       public function deleteAllProducts($facturaId) {
        $data = array();
        $path = _PATHDisable_.'/'.$facturaId;
        $options = array(
            'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($path, false, $context);
        $json = json_decode($result, true);
        return  $json;
       }

}




?>