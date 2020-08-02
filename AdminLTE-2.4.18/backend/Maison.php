<?php
require 'db/MyPDO.php';

ini_set('display_errors', false);
ini_set('html_errors', false);

function newIdMaison(){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $res = $pdo->query("SELECT MAX(idMaison) AS newIdMaison FROM Maison ");
        $record = $res->fetchAll(PDO::FETCH_ASSOC);
        $data = ( (int) $record[0]['newIdMaison']) + 1 ;
        $resultat = array("success" => true, "action" => "newIdMaison", "data" => $data);
    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "newIdMaison", "title" => "Erreur : ", "message" => "Erreur : " . $e->getMessage(), "icon" => "warning");
    }

    $pdo->close($pdo);
    return $resultat;
}

function getMaisonByIdAction($idMaison){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $req = $pdo->prepare("SELECT * FROM Maison WHERE idMaison = :idMaison ");
        $req->bindParam(":idMaison", $idMaison, MyPDO::PARAM_INT);
        $req->execute();

        if($req->rowCount() == 1) {
            $record = $req->fetchAll(PDO::FETCH_ASSOC);
            $resultat = array("success" => true, "action" => "getMaisonById", "idMaison" => $record[0]['idMaison'], "designMaison" => $record[0]['designMaison'], "adresse" => $record[0]['adresse']);
        }else{
            $resultat = array("success" => false, "action" => "getMaisonById", "title" => "Erreur : ", "message" => "Erreur : Cette maison est inexistante dans la liste des maisons ! ", "icon" => "warning");
        }
    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "getMaisonById", "title" => "Erreur : ", "message" => "Erreur : " . $e->getMessage(), "icon" => "warning");
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
            $res = $pdo->query("SELECT * FROM Maison ");
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

function deleteAction($idMaison){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $req = $pdo->prepare("DELETE FROM Maison WHERE idMaison=:idMaison");
        $req->bindParam(":idMaison", $idMaison, MyPDO::PARAM_INT);
        $req->execute();

        $resultat = array("success" => true, "action" => "delete", "data" => $idMaison , "title" => "Info : ", "message" => "La suppression a été effectuée avec succès ! ", "icon" => "success");
    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "delete", "data" => $idMaison, "title" => "Erreur : ", "message" => $e->getMessage() . " ! ", "icon" => "warning");
    }

    $pdo->close($pdo);
    return $resultat;
}

function searchAction($data){
    $result = array();
    $pdo = new MyPDO();
    try {
        $req = $pdo->query("SELECT * FROM maison where designMaison like '%".$data['search']."%'");
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

function createAction($data) {
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $req = $pdo->prepare("INSERT INTO `Maison` (`idMaison`, `designMaison`, `adresse`) VALUES (:idMaison, :designMaison, :adresse) ");
        if($data['idMaison']==''){
            $val = null;
            $req->bindParam(":idMaison", $val, MyPDO::PARAM_NULL);
        }else{
            $req->bindParam(":idMaison", intval($data['idMaison'], 10), MyPDO::PARAM_INT);
        }
        $req->bindParam(":designMaison", $data['designMaison'], MyPDO::PARAM_STR);
        $req->bindParam(":adresse", $data['adresse'], MyPDO::PARAM_STR);
        $req->execute();

        $resultat = array("success" => true, "action" => "create", "title" => "Info : ", "message" => "La nouvelle maison d'édition a été ajoutée avec succès ! ", "icon" => "success");
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
            $req = $pdo->prepare("UPDATE `Maison` SET designMaison = :designMaison, adresse = :adresse WHERE idMaison = :idMaison ");
            $req->bindParam(":idMaison", intval($data['idMaison'], 10), MyPDO::PARAM_INT);
            $req->bindParam(":designMaison", utf8_decode($data['designMaison']), MyPDO::PARAM_STR);
            $req->bindParam(":adresse", utf8_decode($data['adresse']), MyPDO::PARAM_STR);
            $req->execute();

            $resultat = array("success" => true, "action" => "update", "title" => "Info : ", "message" => "La modification de la maison d'édition a été effectuée avec succès ! ", "icon" => "success");
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

            case 'delete':
                $result = deleteAction($data["idMaison"]);
                break;
            case 'search':
                $result = searchAction($data);
                break;
            case 'newIdMaison':
                $result = newIdMaison();
                break;

            case 'getMaisonById':
                $result = getMaisonByIdAction($data["idMaison"]);
                break;
        }
        break;
}

header('Cache-Control: no-cache, must-revalidate');
header('Content-Type: application/json;charset=utf-8');
echo json_encode($result);
