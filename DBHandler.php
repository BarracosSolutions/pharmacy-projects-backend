<?php

require("Toro.php");
header('Access-Control-Allow-Origin: *'); 
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

class FacturaHandler {
    function get($numero = null) {
        if($numero != null){
            try {
                echo $this-> selectFactura($numero);
            } catch (Exception $e) {
                echo "Failed: " . $e->getMessage();
            }
        }else{
            try {
                echo $this-> selectFacturas();
            } catch (Exception $e) {
                echo "Failed: " . $e->getMessage();
            }
        }
    }

    function post($numero = null) {
         if($numero != null){
            try {
            echo $this-> deleteFactura($numero);
            } catch (Exception $e) {
            echo "Failed: " . $e->getMessage();
            }
         }else{
            if($_POST['isUpdate'] == true){
                try {
                    echo $this-> updateFactura($_POST['numero'], $_POST['fecha'], $_POST['cliente'],  $_POST['impuestos'],  $_POST['total']);
                } catch (Exception $e) {
                    echo "Failed: " . $e->getMessage();
                }
            } else{
                try {
                    echo $this-> insertarFactura($_POST['numero'], $_POST['fecha'], $_POST['cliente'],  $_POST['impuestos'],  $_POST['total']);
                } catch (Exception $e) {
                    echo "Failed: " . $e->getMessage();
                }
            }
         }
    }

    function put() {
        try {
           echo $this-> updateFactura($_PUT['numero'], $_PUT['fecha'], $_PUT['cliente'],  $_PUT['impuestos'],  $_PUT['total']);
        } catch (Exception $e) {
          echo "Failed: " . $e->getMessage();
        }
    }

    public  function selectFacturas(){
            try{
                $file_db = new PDO('sqlite:facturas.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                $flag = 1;
                $query = 'SELECT * FROM factura WHERE flag ='.$flag.';';
                $results = $file_db->query($query);		
                $data = $results->fetchAll();	
                return json_encode($data);
            }catch(PDOException $e) {
                //echo $e->getMessage();
                return array();
            }
        
    }

     public function selectFactura($numero){
            try{
                $file_db = new PDO('sqlite:facturas.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                $statement  = $file_db->prepare('SELECT * FROM factura WHERE numero = :numero;');
                $statement->bindValue(':numero', $numero);
                $statement->execute();
                sleep(2);
                $result = $statement->fetch();
                return json_encode($result);
            }catch(PDOException $e) {
                //echo $e->getMessage();
                return null;
            }
    }

    public function insertarFactura($numero, $fecha, $cliente,  $impuestos, $total){
            try{
                $file_db = new PDO('sqlite:facturas.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                // Create tables
                $file_db->exec("CREATE TABLE IF NOT EXISTS factura (
                                numero INTEGER PRIMARY KEY, 
                                fecha  TEXT, 
                                cliente TEXT, 
                                impuestos REAL,
                                total REAL,
                                flag INTEGER)");
                    
                
                $insert = "INSERT INTO factura (numero, fecha, cliente,impuestos,total,flag) 
                            VALUES (:numero, :fecha, :cliente,:impuestos,:total, :flag)";
                $stmt = $file_db->prepare($insert);
            
                // Execute statement
                $stmt->execute([
                        ':numero' => $numero,
                        ':fecha' => $fecha,
                        ':cliente' => $cliente,
                        ':impuestos' => $impuestos,
                        ':total' => $total,
                        ':flag' => 0
                    ]);
                
                $lastId = $file_db->lastInsertId();
                
                return json_encode($lastId);
            
            }
            catch(PDOException $e) {
                echo $e->getMessage();
                return null;
            }
    }

    public function updateFactura($numero, $fecha, $cliente,  $impuestos, $total) {
            try{
                $file_db = new PDO('sqlite:facturas.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                $flag = 1;

                $sql = 'UPDATE factura SET fecha = "'.$fecha.'" , cliente = "'.$cliente.'", impuestos = '.$impuestos.', total = '.$total.', flag = '.$flag.' WHERE numero = ' .$numero.';';
            
                $result = $file_db->exec($sql);
                sleep(2);
                
                return json_encode($result);

            }	catch(PDOException $e) {
                echo $e->getMessage();
                return null;
            }
    }

    public function deleteFactura($numero) {
            try{
                $file_db = new PDO('sqlite:facturas.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                $sql = "DELETE FROM factura WHERE numero = :numero";
        
                $stmt = $file_db->prepare($sql);
                $stmt->execute([':numero' => $numero]);

                $result = $stmt->rowCount();
                return json_encode($result);;
            }	catch(PDOException $e) {
                return null;
            }
    }

}

class ProductoHandler {
    function get($facturaId) {
        try {
           echo  $this->selectProductos($facturaId);
        } catch (Exception $e) {
          echo "Failed: " . $e->getMessage();
        }
    }

    function post($id = null) {
    
        if($id != null){
            try {
                echo $this->deleteProduct($id);
            } catch (Exception $e) {
                echo "Failed: " . $e->getMessage();
            }
        } else{
            try {
                $producto = array( 'cantidad'=> $_POST['cantidad'],'descripcion'=> $_POST['descripcion'],'valor'=> $_POST['valor'],'subtotal'=> $_POST['subtotal']);
                echo $this->insertarProducto($_POST['facturaId'], $producto);
            } catch (Exception $e) {
                echo "Failed: " . $e->getMessage();
            }
        }
    }

  
    public function selectProductos($facturaId){
            try{
                $file_db = new PDO('sqlite:facturas.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                $flag= 1;
                $query = 'SELECT * FROM producto WHERE facturaId='.$facturaId.';';
                $results = $file_db->query($query);
                $data = $results->fetchAll();
                return json_encode($data);
            }catch(PDOException $e) {
                //echo $e->getMessage();
                return array();
            }
    }

    public function insertarProducto($facturaId, $producto){
            try{
                $file_db = new PDO('sqlite:facturas.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                $file_db->exec("CREATE TABLE IF NOT EXISTS producto (
                                id INTEGER PRIMARY KEY,
                                facturaId INTEGER,
                                cantidad  INTEGER, 
                                descripcion TEXT, 
                                valor REAL,
                                subtotal REAL)");

                $insert = "INSERT INTO producto (facturaId, cantidad, descripcion, valor, subtotal) 
                            VALUES (:facturaId, :cantidad, :descripcion, :valor, :subtotal)";
                $stmt = $file_db->prepare($insert);

                $stmt->execute([
                    ':facturaId' => $facturaId,
                    ':cantidad' => $producto['cantidad'],
                    ':descripcion' => $producto['descripcion'],
                    ':valor' => $producto['valor'],
                    ':subtotal' => $producto['subtotal']
                ]);
                

                $lastId = $file_db->lastInsertId();
                
                return json_encode($lastId);
            
            }	catch(PDOException $e) {
                //echo $e->getMessage();
            }
    }

    public function deleteProduct($id) {
            try{
                $file_db = new PDO('sqlite:facturas.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                $sql = "DELETE FROM producto WHERE id = :id";
        
                $stmt = $file_db->prepare($sql);
                $stmt->execute([':id' => $id]);
                sleep(2);
                $result = $stmt->rowCount();
                return json_encode($result);

            }	catch(PDOException $e) {
                return null;
            }
    }

}

class DisabledHandler {
    function post($facturaId = null) {

        if($facturaId != null){
            try {
                echo $this-> deleteAllProducts($facturaId) ;
            } catch (Exception $e) {
            echo "Failed: " . $e->getMessage();
            }
        }else{
            try {
                echo $this-> deleteAllDisabledData();
            } catch (Exception $e) {
            echo "Failed: " . $e->getMessage();
            }
        }
    }


    private function selectDisabledFacturas(){
            try{
                $file_db = new PDO('sqlite:facturas.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                $flag = 0;
                $query = 'SELECT * FROM factura WHERE flag ='.$flag.';';
                $results = $file_db->query($query);		
                $data = $results->fetchAll();
                return json_encode($data);
            }catch(PDOException $e) {
                //echo $e->getMessage();
                return array();
            }
        
}
        
    private function deleteDisabledFacturas() {
            try{
                $file_db = new PDO('sqlite:facturas.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                $sql = "DELETE FROM factura WHERE flag = :flag";
        
                $stmt = $file_db->prepare($sql);
                $stmt->execute([':flag' => 0]);

                $result = $stmt->rowCount();
                return json_encode($result);;
            }	catch(PDOException $e) {
                return null;
            }
    }

    public function deleteAllDisabledData(){
            try{
            $file_db = new PDO('sqlite:facturas.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
            $facturas= $this->selectDisabledFacturas();

            foreach($facturas as $f){
                $this-> deleteAllProducts($f['numero']);
            }

            $this-> deleteDisabledFacturas();
            }catch(PDOException $e) {
                return null;
            }
    }
        
    public function deleteAllProducts($facturaId) {
            try{
                $file_db = new PDO('sqlite:facturas.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                $sql = "DELETE FROM producto WHERE facturaId = :facturaId ";
        
                $stmt = $file_db->prepare($sql);
                $stmt->execute([':facturaId' => $facturaId]);
                sleep(2);
                $result = $stmt->rowCount();
                return $result;
            }	catch(PDOException $e) {
                return null;
            }
    }

        
}


Toro::serve(array(
    "/Factura" => "FacturaHandler",
    "/Factura/:alpha" => "FacturaHandler",
    "/Producto/:alpha" => "ProductoHandler",
    "/Producto" => "ProductoHandler",
    "/Disable"  => "DisabledHandler",
    "/Disable/:alpha"  => "DisabledHandler"

));

?>