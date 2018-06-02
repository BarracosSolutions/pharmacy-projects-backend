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

            if($data['isUpdate'] == "true"){  #quitar las comillas si es raw
                try {
                  echo $this-> updatePaciente($data['PatientId'], $data['PatientFirtsNm'], $data['PatientLastNm'],  $data['MedicationDescription']);

                } catch (Exception $e) {
                    echo "Failed: " . $e->getMessage();
                }
            } else{
                try {
                    echo $this-> insertarPaciente( $data['PatientFirtsNm'], $data['PatientLastNm'],  $data['MedicationDescription']);
                } catch (Exception $e) {
                    echo "Failed: " . $e->getMessage();
                }
            }
         }
    }

    function put() {
        try {
           echo $this-> updatePaciente($_POST['PatientId'], $_POST['PatientFirtsNm'], $_POST['PatientLastNm'],  $_POST['MedicationDescription']);
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

    public function insertarPaciente( $PatientFirtsNm, $PatientLastNm,  $MedicationDescription){
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

    public function updatePaciente($PatientId, $PatientFirtsNm, $PatientLastNm,  $MedicationDescription) {
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                $flag = 1;

                $sql = 'UPDATE patient SET PatientFirtsNm = "'.$PatientFirtsNm.'" , PatientLastNm = "'.$PatientLastNm.'", MedicationDescription = "'.$MedicationDescription.'", LastUpdateDtm = "'.date("D M d, Y G:i").'" WHERE PatientId = ' .$PatientId.';';

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
                  echo $this-> updateEmpleado($data['EmployeeId'], $data['EmployeeFirtsNm'], $data['EmployeeLastNm'], $data['UserNm'], $data['Password']);

                } catch (Exception $e) {
                    echo "Failed: " . $e->getMessage();
                }
            } else{
                try {
                    echo $this-> insertarEmpleado( $data['EmployeeFirtsNm'], $data['EmployeeLastNm'], $data['UserNm'],$data['Password']);
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


    public function insertarEmpleado( $EmployeeFirtsNm, $EmployeeLastNm, $UserNm, $Password){
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

    public function updateEmpleado($EmployeeId, $EmployeeFirtsNm, $EmployeeLastNm, $UserNm, $Password) {
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                $flag = 1;

                $sql = 'UPDATE Employee SET EmployeeFirtsNm = "'.$EmployeeFirtsNm.'" , EmployeeLastNm = "'.$EmployeeLastNm.'", UserNm = "'.$UserNm.'", _Password = "'.$Password.'", LastUpdateDtm = "'.date("D M d, Y G:i").'" WHERE EmployeeId = ' .$EmployeeId.';';

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

            if($data['isUpdate'] == true){
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

            if($data['isUpdate'] == true){
                try {
                  echo $this-> updateProject($data['ProjectId'], $data['ProjectStatusId'],  $data['PatientId'], $data['DrugId'], $data['DirectorId'], $data['Founds'], $data['Regime'], $data['Report'] );

                } catch (Exception $e) {
                    echo "Failed: " . $e->getMessage();
                }
            } else{
                try {
                    echo $this-> insertarProject(  $data['ProjectStatusId'],  $data['PatientId'], $data['DrugId'], $data['DirectorId'], $data['Founds'], $data['Regime'], $data['Report'] );
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



    public function insertarProject( $ProjectStatusId, $PatientId, $DrugId, $DirectorId, $Founds, $Regime, $Report){
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                // Create tables # problema si employee table no esta
                $file_db->exec("CREATE TABLE IF NOT EXISTS ProjectStatus(
                                    ProjectStatusId Integer PRIMARY KEY,
                                    StatusNm TEXT,
                                    LastUpdateDtm TEXT
                                );

                                INSERT OR IGNORE INTO ProjectStatus(ProjectStatusId, LastUpdateDtm) VALUES(1, \"Pendiente\");
                                INSERT OR IGNORE INTO ProjectStatus(ProjectStatusId, LastUpdateDtm) VALUES(2, \"En Proceso\");
                                INSERT OR IGNORE INTO ProjectStatus(ProjectStatusId, LastUpdateDtm) VALUES(3, \"Completo\");

                                CREATE TABLE IF NOT EXISTS Project_x_Employee(
                                    ProjectId Integer PRIMARY KEY,
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
                                Founds TEXT,
                                Regime TEXT,
                                Report TEXT,
                                LastUpdateDtm TEXT,
                                FOREIGN KEY(ProjectStatusId) REFERENCES ProjectStatus(ProjectStatusId),
                                FOREIGN KEY(PatientId) REFERENCES Patient(PatientId),
                                FOREIGN KEY(DrugId) REFERENCES Drug(DrugId),
                                FOREIGN KEY(DirectorId) REFERENCES Employee(EmployeeId)
                                )");


                $insert = "INSERT INTO Project ( ProjectStatusId, PatientId, DrugId, DirectorId, Founds, Regime, Report, LastUpdateDtm )
                            VALUES ( :ProjectStatusId, :PatientId, :DrugId, :DirectorId, :Founds, :Regime, :Report, :LastUpdateDtm)";
                $stmt = $file_db->prepare($insert);

                // Execute statement
                $stmt->execute([
                        ':ProjectStatusId' => $ProjectStatusId,
                        ':PatientId' => $PatientId,
                        ':DrugId' => $DrugId,
                        ':DirectorId' => $DirectorId,
                        ':Founds' => $Founds,
                        ':Regime' => $Regime,
                        ':Report' => $Report,
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

    public function updateProject($ProjectId, $ProjectStatusId, $PatientId, $DrugId, $DirectorId, $Founds, $Regime, $Report) {
            try{
                $file_db = new PDO('sqlite:farmacia.sqlite3');
		        $file_db->setAttribute(PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION);
                $flag = 1;

                $sql = 'UPDATE Project SET ProjectStatusId = "'.$ProjectStatusId.'" , PatientId = "'.$PatientId.'", DrugId = "'.$DrugId.'", DirectorId = "'.$DirectorId.'", Founds = "'.$Founds.'",
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

Toro::serve(array(
    "/Patient" => "PacienteHandler",
    "/Patient/:alpha" => "PacienteHandler",

    "/Employee" => "EmpleadoHandler",
    "/Employee/:alpha" => "EmpleadoHandler",

    "/Drug" => "DrugHandler",
    "/Drug/:alpha" => "DrugHandler",

    "/Project" => "ProjectHandler",
    "/Project/:alpha" => "ProjectHandler",
));

?>
