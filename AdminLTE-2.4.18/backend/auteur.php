<?php
require 'db/MyPDO.php';

ini_set('display_errors', false);
ini_set('html_errors', false);

function newIdAuteur(){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $res = $pdo->query("SELECT (idAuteur) AS newIdAuteur FROM auteur ");
        $record = $res->fetchAll(PDO::FETCH_ASSOC);
        $data = ( (int) $record[0]['newIdAuteur']) + 1 ;
        $resultat = array("success" => true, "action" => "newIdAuteur", "data" => $data);
    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "newIdAuteur", "title" => "Erreur : ", "message" => "Erreur : " . $e->getMessage(), "icon" => "warning");
    }

    $pdo->close($pdo);
    return $resultat;
}

function getAuteur($idAuteur){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $req = $pdo->prepare("SELECT * FROM auteur  WHERE idAuteur=:idAuteur ");
        $req->bindParam(":idAuteur", $idAuteur, MyPDO::PARAM_INT);
        $req->execute();

        if($req->rowCount() == 1) {
            $record = $req->fetchAll(PDO::FETCH_ASSOC);
            $resultat = array("success" => true, "action" => "getAuteur", "idAuteur" => $record[0]['idAuteur'], "nomPrenom" => $record[0]['nomPrenom'],"dateNaiss" => $record[0]['dateNaiss'],"nationalite" => $record[0]['nationalite']);
        }else{
            $resultat = array("success" => false, "action" => "getAuteur", "title" => "Erreur : ", "message" => "Erreur : Cette auteur est inexistante dans la liste des auteurs ! ", "icon" => "warning");
        }
    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "getAuteur", "title" => "Erreur : ", "message" => "Erreur : " . $e->getMessage(), "icon" => "warning");
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
            $res = $pdo->query("SELECT * FROM auteur ");
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

function deleteAction($idAuteur){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $req = $pdo->prepare("DELETE FROM auteur WHERE idAuteur=:idAuteur");
        $req->bindParam(":idAuteur", $idAuteur, MyPDO::PARAM_INT);
        $req->execute();

        $resultat = array("success" => true, "action" => "delete", "data" => $idAuteur , "title" => "Info : ", "message" => "La suppression a été effectuée avec succès ! ", "icon" => "success");
    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "delete", "data" => $idAuteur, "title" => "Erreur : ", "message" => $e->getMessage() . " ! ", "icon" => "warning");
    }

    $pdo->close($pdo);
    return $resultat;
}
function searchAction($data){
    $result = array();
    $pdo = new MyPDO();
    try {
        $req = $pdo->query("SELECT * FROM auteur where nomPrenom like '%".$data['search']."%'");
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
        $req = $pdo->prepare("INSERT INTO `auteur` (`idAuteur`, `nomPrenom`, `dateNaiss`,`nationalite`) VALUES (:idAuteur, :nomPrenom, :dateNaiss, :nationalite) ");
        if($data['idAuteur']==''){
            $val = null;
            $req->bindParam(":idAuteur", $val, MyPDO::PARAM_NULL);
        }else{
            $req->bindParam(":idAuteur", intval($data['idAuteur'], 10), MyPDO::PARAM_INT);
        }
        $req->bindParam(":nomPrenom", $data['nomPrenom'], MyPDO::PARAM_STR);
        $req->bindParam(":dateNaiss", $data['dateNaiss'], MyPDO::PARAM_STR);
        $req->bindParam(":nationalite", $data['nationalite'], MyPDO::PARAM_STR);
        $req->execute();

        $resultat = array("success" => true, "action" => "create", "title" => "Info : ", "message" => "La nouvelle auteur a été ajoutée avec succès ! ", "icon" => "success");
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
            $req = $pdo->prepare("UPDATE `auteur` SET nomPrenom = :nomPrenom,dateNaiss = :dateNaiss,  nationalite= :nationalite WHERE idAuteur= :idAuteur ");
            $req->bindParam(":idAuteur", intval($data['idAuteur'], 10), MyPDO::PARAM_INT);
            $req->bindParam(":nomPrenom", utf8_decode($data['nomPrenom']), MyPDO::PARAM_STR);
            $req->bindParam(":dateNaiss", $data['dateNaiss'], MyPDO::PARAM_STR);
            $req->bindParam(":nationalite", utf8_decode($data['nationalite']), MyPDO::PARAM_STR);
            $req->execute();

            $resultat = array("success" => true, "action" => "update", "title" => "Info : ", "message" => "La modification a été effectuée avec succès ! ", "icon" => "success");
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
                $result = deleteAction($data["idAuteur"]);
                break;

            case 'search':
                $result = searchAction($data);
                break;

            case 'newIdAuteur':
                $result = newIdAuteur();
                break;

            case 'getAuteur':
                $result = getAuteur($data["idAuteur"]);
                break;
        }
        break;
}

header('Cache-Control: no-cache, must-revalidate');
header('Content-Type: application/json;charset=utf-8');
echo json_encode($result);
