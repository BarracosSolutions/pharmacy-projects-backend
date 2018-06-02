<?php

require("Toro.php"); 
header('Access-Control-Allow-Origin: *'); 
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

class PacienteHandler {
    function get($PatientId = null) {
        if($PatientId != null){
            try {
                echo $this-> selectPaciente($PatientId);
            } catch (Exception $e) {
                echo "Failed: " . $e->getMessage();
            }
        }else{
            try {
                echo $this-> selectPacientes();
            } catch (Exception $e) {
                echo "Failed: " . $e->getMessage();
            }
        }
    }

    function post($PatientId = null) {
        $data = json_decode(file_get_contents('php://input'), true);

         if($PatientId != null){
            try {
            echo $this-> deletePaciente($PatientId);
            } catch (Exception $e) {
            echo "Failed: " . $e->getMessage();
            }
         }else{
            
            if($data['isUpdate'] == true){  #quitar las comillas si es raw
                try {
                  echo $this-> updatePaciente($data['PatientId'], $data['PatientFirtsNm'], $data['PatientLastNm'],  $data['MedicationDescription'],  $data['LastUpdateDtm']);
                
                } catch (Exception $e) {
                    echo "Failed: " . $e->getMessage();
                }
            } else{
                try {
                    echo $this-> insertarPaciente( $data['PatientFirtsNm'], $data['PatientLastNm'],  $data['MedicationDescription'],  $data['LastUpdateDtm']);
                } catch (Exception $e) {
                    echo "Failed: " . $e->getMessage();
                }
            }
         }  
    }

    function put() {
        try {
           echo $this-> updatePaciente($_POST['PatientId'], $_POST['PatientFirtsNm'], $_POST['PatientLastNm'],  $_POST['MedicationDescription'],  $_POST['LastUpdateDtm']);
        } catch (Exception $e) {
          echo "Failed: " . $e->getMessage();
        }
    }

    public  function selectPacientes(){
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                $flag = 1;
                $query = 'SELECT * FROM patient;'; # WHERE flag = '.$flag.';';
                $results = $file_db->query($query);		
                $data = $results->fetchAll();	
                return json_encode($data);
            }catch(PDOException $e) {
                //echo $e->getMessage();
                return array();
            }
        
    }

     public function selectPaciente($PatientId){
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                $statement  = $file_db->prepare('SELECT * FROM patient WHERE PacientId = :PatientId;');
                $statement->bindValue(':PatientId', $PatientId);
                $statement->execute();
                sleep(2);
                $result = $statement->fetch();
                return json_encode($result);
            }catch(PDOException $e) {
                //echo $e->getMessage();
                return null;
            }
    }

    public function insertarPaciente( $PatientFirtsNm, $PatientLastNm,  $MedicationDescription, $LastUpdateDtm){
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                // Create tables  #autoincremental
                $file_db->exec("CREATE TABLE IF NOT EXISTS patient (
                                PatientId INTEGER PRIMARY KEY AUTOINCREMENT, 
                                PatientFirtsNm  TEXT, 
                                PatientLastNm TEXT, 
                                MedicationDescription TEXT,
                                LastUpdateDtm TEXT )");
                    
                
                $insert = "INSERT INTO patient ( PatientFirtsNm, PatientLastNm,MedicationDescription,LastUpdateDtm) 
                            VALUES ( :PatientFirtsNm, :PatientLastNm,:MedicationDescription,:LastUpdateDtm)";
                $stmt = $file_db->prepare($insert);
            
                // Execute statement
                $stmt->execute([
                        ':PatientFirtsNm' => $PatientFirtsNm,
                        ':PatientLastNm' => $PatientLastNm,
                        ':MedicationDescription' => $MedicationDescription,
                        ':LastUpdateDtm' => $LastUpdateDtm
                    ]);
                
                $lastId = $file_db->lastInsertId();
                
                return json_encode($lastId);
            
            }
            catch(PDOException $e) {
                echo $e->getMessage();
                return null;
            }
    }

    public function updatePaciente($PatientId, $PatientFirtsNm, $PatientLastNm,  $MedicationDescription, $LastUpdateDtm) {
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                $flag = 1;

                $sql = 'UPDATE patient SET PatientFirtsNm = "'.$PatientFirtsNm.'" , PatientLastNm = "'.$PatientLastNm.'", MedicationDescription = "'.$MedicationDescription.'", LastUpdateDtm = "'.$LastUpdateDtm.'" WHERE PatientId = ' .$PatientId.';';
            
                $result = $file_db->exec($sql);
                
                return json_encode($result);

            }	catch(PDOException $e) {
                echo $e->getMessage();
                return null;
            }
    }

    public function deletePaciente($PatientId) {
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                $sql = "DELETE FROM patient WHERE PatientId = :PatientId";
        
                $stmt = $file_db->prepare($sql);
                $stmt->execute([':PatientId' => $PatientId]);

                $result = $stmt->rowCount();
                return json_encode($result);;
            }	catch(PDOException $e) {
                return null;
            }
    }

}

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



Toro::serve(array(
    "/Factura" => "FacturaHandler",
    "/Factura/:alpha" => "FacturaHandler",

    "/Patient" => "PacienteHandler",
    "/Patient/:alpha" => "PacienteHandler",
));

?>