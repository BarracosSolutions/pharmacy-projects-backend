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

class EmpleadoHandler {
    function get($EmployeeId = null) {
        if($EmployeeId != null){
            try {
                echo $this-> selectEmpleado($EmployeeId);
            } catch (Exception $e) {
                echo "Failed: " . $e->getMessage();
            }
        }else{
            try {
                echo $this-> selectEmpleados();
            } catch (Exception $e) {
                echo "Failed: " . $e->getMessage();
            }
        }
    }

    function post($EmployeeId = null) {
        $data = json_decode(file_get_contents('php://input'), true);

         if($EmployeeId != null){
            try {
            echo $this-> deleteEmpleado($EmployeeId);
            } catch (Exception $e) {
            echo "Failed: " . $e->getMessage();
            }
         }else{
            
            if($data['isUpdate'] == true){  
                try {
                  echo $this-> updateEmpleado($data['EmployeeId'], $data['EmployeeFirtsNm'], $data['EmployeeLastNm'], $data['UserNm'], $data['Password'], $data['CreateDtm'],  $data['LastUpdateDtm']);
                
                } catch (Exception $e) {
                    echo "Failed: " . $e->getMessage();
                }
            } else{
                try {
                    echo $this-> insertarEmpleado( $data['EmployeeFirtsNm'], $data['EmployeeLastNm'], $data['UserNm'],$data['Password'], $data['CreateDtm'],  $data['LastUpdateDtm']);
                } catch (Exception $e) {
                    echo "Failed: " . $e->getMessage();
                }
            }
         }  
    }

    function put() {
        try {
           echo $this-> updateEmpleado($data['EmployeeId'], $data['EmployeeFirtsNm'], $data['EmployeeLastNm'], $data['UserNm'],$data['Password'], $data['CreateDtm'],  $data['LastUpdateDtm']);
        } catch (Exception $e) {
          echo "Failed: " . $e->getMessage();
        }
    }

    public  function selectEmpleados(){
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                $query = 'SELECT * FROM Employee;';
                $results = $file_db->query($query);		
                $data = $results->fetchAll();	
                return json_encode($data);
            }catch(PDOException $e) {

                return array();
            }
        
    }

     public function selectEmpleado($EmployeeId){
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                $statement  = $file_db->prepare('SELECT * FROM Employee WHERE EmployeeId = :EmployeeId;');
                $statement->bindValue(':EmployeeId', $EmployeeId);
                $statement->execute();
                sleep(2);
                $result = $statement->fetch();
                return json_encode($result);
            }catch(PDOException $e) {
                echo $e->getMessage();
                return null;
            }
    }


    public function insertarEmpleado( $EmployeeFirtsNm, $EmployeeLastNm, $UserNm, $Password, $CreateDtm, $LastUpdateDtm){
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                // Create tables  #autoincremental
                $file_db->exec("CREATE TABLE IF NOT EXISTS Employee (
                                EmployeeId INTEGER PRIMARY KEY AUTOINCREMENT, 
                                EmployeeFirtsNm  TEXT, 
                                EmployeeLastNm TEXT,
                                UserNm TEXT,
                                _Password TEXT,
                                CreateDtm TEXT,
                                LastUpdateDtm TEXT 
                                )");
                    
                
                $insert = "INSERT INTO Employee ( EmployeeFirtsNm, EmployeeLastNm, UserNm, _Password, CreateDtm, LastUpdateDtm) 
                            VALUES ( :EmployeeFirtsNm, :EmployeeLastNm, :UserNm, :_Password, :CreateDtm, :LastUpdateDtm)";
                $stmt = $file_db->prepare($insert);
            
                // Execute statement
                $stmt->execute([
                        ':EmployeeFirtsNm' => $EmployeeFirtsNm,
                        ':EmployeeLastNm' => $EmployeeLastNm,
                        ':UserNm' => $UserNm,
                        ':_Password' => $Password,
                        ':CreateDtm' => $CreateDtm,
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

    public function updateEmpleado($EmployeeId, $EmployeeFirtsNm, $EmployeeLastNm, $UserNm, $Password, $CreateDtm, $LastUpdateDtm) {
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                $flag = 1;

                $sql = 'UPDATE Employee SET EmployeeFirtsNm = "'.$EmployeeFirtsNm.'" , EmployeeLastNm = "'.$EmployeeLastNm.'", UserNm = "'.$UserNm.'", _Password = "'.$Password.'", CreateDtm = "'.$CreateDtm.'", LastUpdateDtm = "'.$LastUpdateDtm.'" WHERE EmployeeId = ' .$EmployeeId.';';
            
                $result = $file_db->exec($sql);
                
                return json_encode($result);

            }	catch(PDOException $e) {
                echo $e->getMessage();
                return null;
            }
    }

    public function deleteEmpleado($EmployeeId) {
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE, 
									PDO::ERRMODE_EXCEPTION);
                $sql = "DELETE FROM Employee WHERE EmployeeId = :EmployeeId";
        
                $stmt = $file_db->prepare($sql);
                $stmt->execute([':EmployeeId' => $EmployeeId]);

                $result = $stmt->rowCount();
                return json_encode($result);;
            }	catch(PDOException $e) {
                return null;
            }
    }

}



Toro::serve(array(
    "/Employee" => "EmpleadoHandler",
    "/Employee/:alpha" => "EmpleadoHandler",

    "/Patient" => "PacienteHandler",
    "/Patient/:alpha" => "PacienteHandler",
));

?>