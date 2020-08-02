<?php
require 'db/MyPDO.php';

ini_set('display_errors', false);
ini_set('html_errors', false);

function newIdEtudiant(){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $res = $pdo->query("SELECT (cin) AS newIdEtudiant FROM etudiant ");
        $record = $res->fetchAll(PDO::FETCH_ASSOC);
        $data = ( (int) $record[0]['newIdEtudiant']) + 1 ;
        $resultat = array("success" => true, "action" => "newIdEtudiant", "data" => $data);
    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "newIdEtudiant", "title" => "Erreur : ", "message" => "Erreur : " . $e->getMessage(), "icon" => "warning");
    }

    $pdo->close($pdo);
    return $resultat;
}

function getEtudiant($cin){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $req = $pdo->prepare("SELECT * FROM etudiant where cin = :cin ");
        $req->bindParam(":cin", $cin, MyPDO::PARAM_INT);
        $req->execute();

        if($req->rowCount() == 1) {
            $record = $req->fetchAll(PDO::FETCH_ASSOC);
            $resultat = array("success" => true, "action" => "getEtudiant", "cin" => $record[0]['cin'], "nomPrenom" => $record[0]['nomPrenom'], "numCarte" => $record[0]['numCarte'],"dateNaiss" => $record[0]['dateNaiss']);
        }else{
            $resultat = array("success" => false, "action" => "getEtudiant", "title" => "Erreur : ", "message" => "Erreur : This student is nonexistent in the list of students! ", "icon" => "warning");
        }
    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "getEtudiant", "title" => "Erreur : ", "message" => "Erreur : " . $e->getMessage(), "icon" => "warning");
    }

    $pdo->close($pdo);
    return $resultat;
}

function readAction($action){
    if($action == 'read'){
        $resultat = array();
        $pdo = new MyPDO();

        try {
            $result = array();
            $res = $pdo->query("SELECT * FROM etudiant ");
            foreach ($res->fetchAll(PDO::FETCH_ASSOC) as $record) {
                array_push($result, $record);
            }
            $resultat = array("success" => true, "action" => "read", "data" => $result);
        } catch (Exception $e) {
            $resultat = array("success" => false, "action" => "read", "title" => "Erreur : ", "message" => "Erreur : " . $e->getMessage(), "icon" => "warning");
        }

        $pdo->close($pdo);
        return $resultat;

    }else{
        return array("success" => false, "action" => "read", "title" => "Erreur : ", "message" => "Action non specifiée", "icon" => "warning");
    }
}
function searchAction($data){
    $result = array();
    $pdo = new MyPDO();
    try {
        $req = $pdo->query("SELECT * FROM etudiant where nomPrenom like '%".$data['search']."%'");
        //$req->bindParam(":search", $data['search'], MyPDO::PARAM_STR);
        //$req->execute();

        foreach ($req->fetchAll(PDO::FETCH_ASSOC) as $record) {
            array_push($result, $record);
        }
        $resultat = array("success" => true, "action" => "search", "data" => $result);

    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "search", "title" => "Erreur : ", "message" => $e->getMessage() . " ! ", "icon" => "warning");
    }
    $pdo->close($pdo);
    return $resultat;

}

function deleteAction($cin){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $req = $pdo->prepare("DELETE FROM etudiant WHERE cin=:cin");
        $req->bindParam(":cin", $cin, MyPDO::PARAM_INT);
        $req->execute();

        $resultat = array("success" => true, "action" => "delete", "data" => $cin , "title" => "Info : ", "message" => "The deletion was successful ! ", "icon" => "success");
    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "delete", "data" => $cin, "title" => "Erreur : ", "message" => $e->getMessage() . " ! ", "icon" => "warning");
    }

    $pdo->close($pdo);
    return $resultat;
}


function createAction($data) {
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $req = $pdo->prepare("INSERT INTO `etudiant` (`cin`, `nomPrenom`, `numCarte`,`dateNaiss`) VALUES (:cin, :nomPrenom, :numCarte, :dateNaiss) ");
        if($data['cin']==''){
            $val = null;
            $req->bindParam(":cin", $val, MyPDO::PARAM_NULL);
        }else{
            $req->bindParam(":cin", intval($data['cin'], 10), MyPDO::PARAM_INT);
        }
        $req->bindParam(":nomPrenom", $data['nomPrenom'], MyPDO::PARAM_STR);
        $req->bindParam(":numCarte", $data['numCarte'], MyPDO::PARAM_STR);
        $req->bindParam(":dateNaiss", $data['dateNaiss'], MyPDO::PARAM_STR);

        $req->execute();

        $resultat = array("success" => true, "action" => "create", "title" => "Info : ", "message" => "The new student has been successfully added ! ", "icon" => "success");
    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "create", "title" => "Erreur : ", "message" => $e->getMessage() . " ! ", "icon" => "warning");
    }

    $pdo->close($pdo);
    return $resultat;
}


function updateAction($data) {
    if ($data["action"] == 'update'){
        $resultat = array();
        $pdo = new MyPDO();

        try {
            $req = $pdo->prepare("UPDATE `etudiant` SET nomPrenom = :nomPrenom, numCarte = :numCarte,dateNaiss = :dateNaiss WHERE cin = :cin ");
            $req->bindParam(":cin", intval($data['cin'], 10), MyPDO::PARAM_INT);
            $req->bindParam(":nomPrenom", utf8_decode($data['nomPrenom']), MyPDO::PARAM_STR);
            $req->bindParam(":numCarte", utf8_decode($data['numCarte']), MyPDO::PARAM_STR);
            $req->bindParam(":dateNaiss", $data['dateNaiss'], MyPDO::PARAM_STR);
            $req->execute();

            $resultat = array("success" => true, "action" => "update", "title" => "Info : ", "message" => "The modification of the student has been successfully completed! ", "icon" => "success");
        } catch (Exception $e) {
            $resultat = array("success" => false, "action" => "update", "title" => "Erreur : ", "message" => $e->getMessage() . " ! ", "icon" => "warning");
        }

        $pdo->close($pdo);
        return $resultat;
    }
}


//Programme principal
$method = $_SERVER['REQUEST_METHOD'];
(isset($_GET['action'])) ? $action = $_GET['action'] : $action = null;
$json_data = file_get_contents('php://input');
$result = '';

switch ($method) {
    case 'GET':
        $result = readAction($action);
        break;

    case 'POST':
        $data = json_decode($json_data, true);
        switch ($data["action"]) {
            case 'create':
                $result = createAction($data);
                break;

            case 'update':
                $result = updateAction($data);
                break;
            case 'search':
                $result = searchAction($data);
                break;

            case 'delete':
                $result = deleteAction($data["cin"]);
                break;

            case 'newIdEtudiant':
                $result = newIdEtudiant();
                break;

            case 'getEtudiant':
                $result = getEtudiant($data["cin"]);
                break;
        }
        break;
}

header('Cache-Control: no-cache, must-revalidate');
header('Content-Type: application/json;charset=utf-8');
echo json_encode($result);
