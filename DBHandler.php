<?php

require("Toro.php");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Content-type: application/json');
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

            if($data['isUpdate'] == 'true'){
                try {
                  echo $this-> updatePaciente($data['PatientId'], $data['NationalId'], $data['PatientFirstNm'], $data['PatientLastNm'],  $data['MedicationDescription']);

                } catch (Exception $e) {
                    echo "Failed: " . $e->getMessage();
                }
            } else{
                try {
                    echo $this-> insertarPaciente( $data['NationalId'],$data['PatientFirstNm'], $data['PatientLastNm'],  $data['MedicationDescription']);
                } catch (Exception $e) {
                    echo "Failed: " . $e->getMessage();
                }
            }
         }
    }

    function put() {
        try {
           echo $this-> updatePaciente($_POST['PatientId'], $_POST['NationalId'],$_POST['PatientFirstNm'], $_POST['PatientLastNm'],  $_POST['MedicationDescription']);
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

    public function insertarPaciente($NationalId, $PatientFirstNm, $PatientLastNm,  $MedicationDescription){
            try{
                  $file_db = new PDO('sqlite:farmacia.sqlite3');
		              $file_db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                // Create tables  #autoincremental
                $file_db->exec("CREATE TABLE IF NOT EXISTS patient (
                                PatientId INTEGER PRIMARY KEY AUTOINCREMENT,
                                NationalId TEXT,
                                PatientFirstNm  TEXT,
                                PatientLastNm TEXT,
                                MedicationDescription TEXT,
                                LastUpdateDtm TEXT )");


                $insert = "INSERT INTO patient (NationalId, PatientFirstNm, PatientLastNm,MedicationDescription,LastUpdateDtm)
                            VALUES (:NationalId, :PatientFirstNm, :PatientLastNm,:MedicationDescription,:LastUpdateDtm)";
                $stmt = $file_db->prepare($insert);

                // Execute statement
                $stmt->execute([
                        ':NationalId' => $NationalId,
                        ':PatientFirstNm' => $PatientFirstNm,
                        ':PatientLastNm' => $PatientLastNm,
                        ':MedicationDescription' => $MedicationDescription,
                        ':LastUpdateDtm' => date("D M d, Y G:i")
                    ]);

                $lastId = $file_db->lastInsertId();

                return json_encode($lastId);

            }
            catch(PDOException $e) {
                echo $e->getMessage();
                return null;
            }
    }

    public function updatePaciente($PatientId, $NationalId, $PatientFirstNm, $PatientLastNm,  $MedicationDescription) {
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                $flag = 1;

                $sql = 'UPDATE patient SET  NationalId = "'.$NationalId.'", PatientFirstNm = "'.$PatientFirstNm.'" , PatientLastNm = "'.$PatientLastNm.'", MedicationDescription = "'.$MedicationDescription.'", LastUpdateDtm = "'.date("D M d, Y G:i").'" WHERE PatientId = ' .$PatientId.';';

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

            if($data['isUpdate'] == 'true'){
                try {
                  echo $this-> updateEmpleado($data['EmployeeId'], $data['NationalId'], $data['EmployeeFirstNm'], $data['EmployeeLastNm'], $data['UserNm'], $data['Password']);

                } catch (Exception $e) {
                    echo "Failed: " . $e->getMessage();
                }
            } else{
                try {
                    echo $this-> insertarEmpleado($data['NationalId'], $data['EmployeeFirstNm'], $data['EmployeeLastNm'], $data['UserNm'],$data['Password']);
                } catch (Exception $e) {
                    echo "Failed: " . $e->getMessage();
                }
            }
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
                $result = $statement->fetch();
                return json_encode($result);
            }catch(PDOException $e) {
                echo $e->getMessage();
                return null;
            }
    }


    public function insertarEmpleado($NationalId,$EmployeeFirstNm, $EmployeeLastNm, $UserNm, $Password){
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                // Create tables  #autoincremental
                $file_db->exec("CREATE TABLE IF NOT EXISTS Employee (
                                EmployeeId INTEGER PRIMARY KEY AUTOINCREMENT,
                                NationalId TEXT,
                                EmployeeFirstNm  TEXT,
                                EmployeeLastNm TEXT,
                                UserNm TEXT,
                                _Password TEXT,
                                CreateDtm TEXT,
                                LastUpdateDtm TEXT
                                )");


                $insert = "INSERT INTO Employee (NationalId, EmployeeFirstNm, EmployeeLastNm, UserNm, _Password, CreateDtm, LastUpdateDtm)
                            VALUES (:NationalId, :EmployeeFirstNm, :EmployeeLastNm, :UserNm, :_Password, :CreateDtm, :LastUpdateDtm)";
                $stmt = $file_db->prepare($insert);

                // Execute statement
                $stmt->execute([
                        ':NationalId' => $NationalId,
                        ':EmployeeFirstNm' => $EmployeeFirstNm,
                        ':EmployeeLastNm' => $EmployeeLastNm,
                        ':UserNm' => $UserNm,
                        ':_Password' => $Password,
                        ':CreateDtm' => date("D M d, Y G:i"),
                        ':LastUpdateDtm' => date("D M d, Y G:i")
                    ]);

                $lastId = $file_db->lastInsertId();

                return json_encode($lastId);

            }
            catch(PDOException $e) {
                echo $e->getMessage();
                return null;
            }
    }

    public function updateEmpleado($EmployeeId, $NationalId, $EmployeeFirstNm, $EmployeeLastNm, $UserNm, $Password) {
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                $flag = 1;

                $sql = 'UPDATE Employee SET NationalId = "'.$NationalId.'" EmployeeFirstNm = "'.$EmployeeFirtsNm.'" , EmployeeLastNm = "'.$EmployeeLastNm.'", UserNm = "'.$UserNm.'", _Password = "'.$Password.'", LastUpdateDtm = "'.date("D M d, Y G:i").'" WHERE EmployeeId = ' .$EmployeeId.';';

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

class DrugHandler {
    function get($DrugId = null) {
        if($DrugId != null){
            try {
                echo $this-> selectDrug($DrugId);
            } catch (Exception $e) {
                echo "Failed: " . $e->getMessage();
            }
        }else{
            try {
                echo $this-> selectDrugs();
            } catch (Exception $e) {
                echo "Failed: " . $e->getMessage();
            }
        }
    }

    function post($DrugId = null) {
        $data = json_decode(file_get_contents('php://input'), true);

         if($DrugId != null){
            try {
            echo $this-> deleteDrug($DrugId);
            } catch (Exception $e) {
            echo "Failed: " . $e->getMessage();
            }
         }else{

            if($data['isUpdate'] == 'true'){
                try {
                  echo $this-> updateDrug($data['DrugId'], $data['DrugNm']);

                } catch (Exception $e) {
                    echo "Failed: " . $e->getMessage();
                }
            } else{
                try {
                    echo $this-> insertarDrug( $data['DrugNm'] );
                } catch (Exception $e) {
                    echo "Failed: " . $e->getMessage();
                }
            }
         }
    }

    function put() {
        try {
           echo $this-> updateDrug($data['DrugId'],  $data['DrugNm'] );
        } catch (Exception $e) {
          echo "Failed: " . $e->getMessage();
        }
    }

    public  function selectDrugs(){
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                $query = 'SELECT * FROM Drug;';
                $results = $file_db->query($query);
                $data = $results->fetchAll();
                return json_encode($data);
            }catch(PDOException $e) {

                return array();
            }

    }

     public function selectDrug($DrugId){
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                $statement  = $file_db->prepare('SELECT * FROM Drug WHERE DrugId = :DrugId;');
                $statement->bindValue(':DrugId', $DrugId);
                $statement->execute();
                $result = $statement->fetch();
                return json_encode($result);
            }catch(PDOException $e) {
                echo $e->getMessage();
                return null;
            }
    }


    public function insertarDrug( $DrugNm){
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                // Create tables  #autoincremental
                $file_db->exec("CREATE TABLE IF NOT EXISTS Drug (
                                DrugId INTEGER PRIMARY KEY AUTOINCREMENT,
                                DrugNm  TEXT,
                                CreateDtm TEXT,
                                LastUpdateDtm TEXT
                                )");


                $insert = "INSERT INTO Drug ( DrugNm,  CreateDtm, LastUpdateDtm)
                            VALUES ( :DrugNm, :CreateDtm, :LastUpdateDtm)";
                $stmt = $file_db->prepare($insert);

                // Execute statement
                $stmt->execute([
                        ':DrugNm' => $DrugNm,
                        ':CreateDtm' => date("D M d, Y G:i"),
                        ':LastUpdateDtm' => date("D M d, Y G:i")
                    ]);

                $lastId = $file_db->lastInsertId();

                return json_encode($lastId);

            }
            catch(PDOException $e) {
                echo $e->getMessage();
                return null;
            }
    }

    public function updateDrug($DrugId, $DrugNm) {
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                $flag = 1;

                $sql = 'UPDATE Drug SET DrugNm = "'.$DrugNm.'" , LastUpdateDtm = "'.date("D M d, Y G:i").'" WHERE DrugId = ' .$DrugId.';';

                $result = $file_db->exec($sql);

                return json_encode($result);

            }	catch(PDOException $e) {
                echo $e->getMessage();
                return null;
            }
    }

    public function deleteDrug($DrugId) {
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                $sql = "DELETE FROM Drug WHERE DrugId = :DrugId";

                $stmt = $file_db->prepare($sql);
                $stmt->execute([':DrugId' => $DrugId]);

                $result = $stmt->rowCount();
                return json_encode($result);;
            }	catch(PDOException $e) {
                return null;
            }
    }

}


class ProjectHandler {
    function get($ProjectId = null) {
        if($ProjectId != null){
            try {
                echo $this-> selectProject($ProjectId);
            } catch (Exception $e) {
                echo "Failed: " . $e->getMessage();
            }
        }else{
            try {
                echo $this-> selectProjects();
            } catch (Exception $e) {
                echo "Failed: " . $e->getMessage();
            }
        }
    }

    function post($ProjectId = null) {
        $data = json_decode(file_get_contents('php://input'), true);

         if($ProjectId != null){
            try {
            echo $this-> deleteProject($ProjectId);
            } catch (Exception $e) {
            echo "Failed: " . $e->getMessage();
            }
         }else{

            if($data['isUpdate'] == 'true'){
                try {
                  echo $this-> updateProject($data['ProjectId'], $data['ProjectStatusId'],  $data['PatientId'], $data['DrugId'], $data['DirectorId'], $data["ProjectNm"], $data['Funds'], $data['Regime'], $data['Report'] );

                } catch (Exception $e) {
                    echo "Failed: " . $e->getMessage();
                }
            } else{
                try {
                    echo $this-> insertarProject($data["ProjectNm"], $data['PatientId'], $data['DrugId'], $data['DirectorId'], $data['Funds'], $data['Regime']);
                } catch (Exception $e) {
                    echo "Failed: " . $e->getMessage();
                }
            }
         }
    }


    public  function selectProjects(){
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                $query = 'SELECT * FROM Project;';
                $results = $file_db->query($query);
                $data = $results->fetchAll();
                return json_encode($data);
            }catch(PDOException $e) {

                return array();
            }

    }

     public function selectProject($ProjectId){
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                $statement  = $file_db->prepare('SELECT * FROM Project WHERE ProjectId = :ProjectId;');
                $statement->bindValue(':ProjectId', $EmployeeId);
                $statement->execute();
                $result = $statement->fetch();
                return json_encode($result);
            }catch(PDOException $e) {
                echo $e->getMessage();
                return null;
            }
    }



    public function insertarProject($ProjectNm,$PatientId, $DrugId, $DirectorId, $Funds, $Regime){
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                // Create tables # problema si employee table no esta
                $ProjectStatusId = 1;
                $file_db->exec("CREATE TABLE IF NOT EXISTS ProjectStatus(
                                    ProjectStatusId Integer PRIMARY KEY,
                                    StatusNm TEXT,
                                    LastUpdateDtm TEXT
                                );

                                INSERT OR IGNORE INTO ProjectStatus(ProjectStatusId, LastUpdateDtm) VALUES(1, \"Pendiente\");
                                INSERT OR IGNORE INTO ProjectStatus(ProjectStatusId, LastUpdateDtm) VALUES(2, \"En Proceso\");
                                INSERT OR IGNORE INTO ProjectStatus(ProjectStatusId, LastUpdateDtm) VALUES(3, \"Completo\");



                                CREATE TABLE IF NOT EXISTS Project_x_Employee(
                                    Project_EmployeeId Integer PRIMARY KEY AUTOINCREMENT,
                                    ProjectId Integer,
                                    EmployeeId Integer,
                                    FOREIGN KEY(ProjectId) REFERENCES Project(ProjectId),
                                    FOREIGN KEY(EmployeeId) REFERENCES Employee(EmployeeId)
                                );

                                CREATE TABLE IF NOT EXISTS Project (
                                ProjectId INTEGER PRIMARY KEY AUTOINCREMENT,
                                ProjectStatusId  INTEGER,
                                PatientId INTEGER,
                                DrugId INTEGER,
                                DirectorId INTEGER,
                                ProjectNm TEXT,
                                Funds TEXT,
                                Regime TEXT,
                                Report TEXT NULL,
                                LastUpdateDtm TEXT,
                                FOREIGN KEY(ProjectStatusId) REFERENCES ProjectStatus(ProjectStatusId),
                                FOREIGN KEY(PatientId) REFERENCES Patient(PatientId),
                                FOREIGN KEY(DrugId) REFERENCES Drug(DrugId),
                                FOREIGN KEY(DirectorId) REFERENCES Employee(EmployeeId)
                                )");


                $insert = "INSERT INTO Project ( ProjectStatusId, PatientId, DrugId, DirectorId, ProjectNm, Funds, Regime, LastUpdateDtm )
                            VALUES ( :ProjectStatusId, :PatientId, :DrugId, :DirectorId,:ProjectNm, :Funds, :Regime, :LastUpdateDtm)";
                $stmt = $file_db->prepare($insert);

                // Execute statement
                $stmt->execute([
                        ':ProjectStatusId' => $ProjectStatusId,
                        ':PatientId' => $PatientId,
                        ':DrugId' => $DrugId,
                        ':DirectorId' => $DirectorId,
                        ':ProjectNm' => $ProjectNm,
                        ':Funds' => $Funds,
                        ':Regime' => $Regime,
                        ':LastUpdateDtm' => date("D M d, Y G:i")
                    ]);

                $lastId = $file_db->lastInsertId();

                return json_encode($lastId);

            }
            catch(PDOException $e) {
                echo $e->getMessage();
                return null;
            }
    }

    public function updateProject($ProjectId, $ProjectStatusId, $PatientId, $DrugId, $DirectorId,$ProjectNm, $Funds, $Regime, $Report) {
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                $flag = 1;

                $sql = 'UPDATE Project SET ProjectStatusId = "'.$ProjectStatusId.'" , ProjectNm ="'.$ProjectNm.'" , PatientId = "'.$PatientId.'", DrugId = "'.$DrugId.'", DirectorId = "'.$DirectorId.'", Funds = "'.$Funds.'",
                 Regime = "'.$Regime.'", Report = "'.$Report.'", LastUpdateDtm = "'.date("D M d, Y G:i").'" WHERE ProjectId = ' .$ProjectId.';';

                $result = $file_db->exec($sql);

                return json_encode($result);

            }	catch(PDOException $e) {
                echo $e->getMessage();
                return null;
            }
    }

    public function deleteProject($ProjectId) {
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                $sql = "DELETE FROM Project WHERE ProjectId = :ProjectId";

                $stmt = $file_db->prepare($sql);
                $stmt->execute([':ProjectId' => $ProjectId]);

                $result = $stmt->rowCount();
                return json_encode($result);;
            }	catch(PDOException $e) {
                return null;
            }
    }

}

class Project_EmployeeHandler {

    function get($ProjectId = null) { # los empelados de ese proyecto
        if($ProjectId != null){
            try {
                echo $this-> selectProject_Employees($ProjectId);
            } catch (Exception $e) {
                echo "Failed: " . $e->getMessage();
            }
        }

    }

    function post($ProjectId = null) {
        $data = json_decode(file_get_contents('php://input'), true);

         if($ProjectId != null){
            try {
            echo $this-> deleteProject_Employee($ProjectId);
            } catch (Exception $e) {
            echo "Failed: " . $e->getMessage();
            }
         }else{
                try {
                    echo $this-> insertarProject_Employee(  $data['ProjectId'],  $data['EmployeeId']);
                } catch (Exception $e) {
                    echo "Failed: " . $e->getMessage();
                }
         }
    }

     public function selectProject_Employees($ProjectId){ # los proyectos de un colaborador
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                $statement  = $file_db->prepare('SELECT * FROM Project_x_Employee WHERE ProjectId = :ProjectId;');
                $statement->bindValue(':ProjectId', $ProjectId);
                $statement->execute();
                $result = $statement->fetch();
                return json_encode($result);
            }catch(PDOException $e) {
                echo $e->getMessage();
                return null;
            }
    }



    public function insertarProject_Employee( $ProjectId, $EmployeeId ){
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                // Create tables # problema si employee table no esta
                $file_db->exec("    CREATE TABLE IF NOT EXISTS Project_x_Employee(
                                    Project_EmployeeId Integer PRIMARY KEY AUTOINCREMENT,
                                    ProjectId Integer,
                                    EmployeeId Integer,
                                    FOREIGN KEY(ProjectId) REFERENCES Project(ProjectId),
                                    FOREIGN KEY(EmployeeId) REFERENCES Employee(EmployeeId)
                                )");


                $insert = "INSERT INTO Project_x_Employee ( ProjectId, EmployeeId )
                            VALUES ( :ProjectId, :EmployeeId )";
                $stmt = $file_db->prepare($insert);

                // Execute statement
                $stmt->execute([
                        ':ProjectId' => $ProjectId,
                        ':EmployeeId' => $EmployeeId
                    ]);

                $lastId = $file_db->lastInsertId();

                return json_encode($lastId);

            }
            catch(PDOException $e) {
                echo $e->getMessage();
                return null;
            }
    }


    public function deleteProject($ProjectId, $EmployeeId) {
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                $sql = "DELETE FROM Project_x_Employee WHERE ProjectId = :ProjectId AND EmployeeId = :EmployeeId";

                $stmt = $file_db->prepare($sql);
                $stmt->execute([':ProjectId' => $ProjectId,
                                ':EmployeeId' => $EmployeeId]
                            );

                $result = $stmt->rowCount();
                return json_encode($result);;
            }	catch(PDOException $e) {
                return null;
            }
    }

}

class Director_ProjectsHandler {
    function get($DirectorId = null) {
        if($DirectorId != null){
            try {
                echo $this-> selectDirector_Projects($DirectorId);
            } catch (Exception $e) {
                echo "Failed: " . $e->getMessage();
            }
        }

    }

     public function selectDirector_Projects($DirectorId){
         try {
             $file_db = new PDO('sqlite:farmacia.sqlite3');
             $file_db->setAttribute(
                    PDO::ATTR_ERRMODE,
                                    PDO::ERRMODE_EXCEPTION
                );
             $statement  = $file_db->prepare('SELECT * FROM Project WHERE DirectorId = :DirectorId;');
             $statement->bindValue(':DirectorId', $DirectorId);
             $statement->execute();
             $result = $statement->fetch();
             return json_encode($result);
         } catch (PDOException $e) {
             echo $e->getMessage();
             return null;
         }
     }
}

class Employee_ProjectsHandler {
    function get($DirectorId = null) {
        if($DirectorId != null){
            try {
                echo $this-> selectEmployee_Projects($DirectorId);
            } catch (Exception $e) {
                echo "Failed: " . $e->getMessage();
            }
        }

    }

     public function selectEmployee_Projects($DirectorId){
         try {
             $file_db = new PDO('sqlite:farmacia.sqlite3');
             $file_db->setAttribute(
                    PDO::ATTR_ERRMODE,
                                    PDO::ERRMODE_EXCEPTION
                );
             $statement  = $file_db->prepare('SELECT * FROM Project WHERE DirectorId = :DirectorId;');
             $statement->bindValue(':DirectorId', $DirectorId);
             $statement->execute();
             $result = $statement->fetch();
             return json_encode($result);
         } catch (PDOException $e) {
             echo $e->getMessage();
             return null;
         }
     }
}

Toro::serve(array(
    "/Patient" => "PacienteHandler",
    "/Patient/:alpha" => "PacienteHandler",

    "/Employee" => "EmpleadoHandler",
    "/Employee/:alpha" => "EmpleadoHandler",

    "/Drug" => "DrugHandler",
    "/Drug/:alpha" => "DrugHandler",

    "/Project" => "ProjectHandler",
    "/Project/:alpha" => "ProjectHandler",

    "/Project_Employee" => "Project_EmployeeHandler",
    "/Project_Employee/:alpha" => "Project_EmployeeHandler",

    "/Director_Projects" => "Director_ProjectsHandler",
    "/Director_Projects/:alpha" => "Director_ProjectsHandler",
));

?>
