<?php
require 'db/MyPDO.php';

ini_set('display_errors', false);
ini_set('html_errors', false);


function getEmpruntRetourByIdAction($cin){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $req = $pdo->prepare("SELECT * FROM empruntretour WHERE cin = :cin");
        $req->bindParam(":cin", $cin, MyPDO::PARAM_INT);
        $req->execute();

        if($req->rowCount() == 1) {
            $record = $req->fetchAll(PDO::FETCH_ASSOC);
            $resultat = array("success" => true, "action" => "getEmpruntRetourById", "cin" => $record[0]['cin'], "idLivre" => $record[0]['idLivre'], "dateEmprunt" => $record[0]['dateEmprunt'],"dateRetour" => $record[0]['dateRetour']);
        }else{
            $resultat = array("success" => false, "action" => "getEmpruntRetourById", "title" => "Erreur : ", "message" => "Erreur : Cette Emprunt/Retour est inexistant dans la liste des EmpruntRetours ! ", "icon" => "warning");
        }
    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "getEmpruntRetourById", "title" => "Erreur : ", "message" => "Erreur : " . $e->getMessage(), "icon" => "warning");
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
            $res = $pdo->query("SELECT * FROM empruntretour ");
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
        return array("success" => false, "action" => "read", "title" => "Erreur : ", "message" => "Unspecified action", "icon" => "warning");
    }
}

function deleteAction($cin){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $req = $pdo->prepare("DELETE FROM empruntretour WHERE cin=:cin");
        $req->bindParam(":cin", $cin, MyPDO::PARAM_INT);
        $req->execute();

        $resultat = array("success" => true, "action" => "delete", "data" => $cin , "title" => "Info : ", "message" => "The deletion was successful! ", "icon" => "success");
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
        $req = $pdo->prepare("INSERT INTO `empruntretour` (`cin`, `idLivre`, `dateEmprunt`, `dateRetour`) VALUES (:cin, :idLivre, :dateEmprunt, :dateRetour)");
        if($data['cin']==''){
            $val = null;
            $req->bindParam(":cin", $val, MyPDO::PARAM_NULL);
        }else{
            $req->bindParam(":cin", intval($data['cin'], 10), MyPDO::PARAM_INT);
        }
        $req->bindParam(":idLivre", $data['idLivre'], MyPDO::PARAM_STR);
        $req->bindParam(":dateEmprunt", $data['dateEmprunt'], MyPDO::PARAM_STR);
        $req->bindParam(":dateRetour", $data['dateRetour'], MyPDO::PARAM_STR);
        
        $req->execute();

        $resultat = array("success" => true, "action" => "create", "title" => "Info : ", "message" => "The new brrower has been successfully added! ! ", "icon" => "success");
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
            $req = $pdo->prepare("UPDATE `empruntretour` SET idLivre = :idLivre,dateEmprunt =:dateEmprunt, dateRetour = :dateRetour WHERE cin = :cin ");
            $req->bindParam(":cin", intval($data['cin'], 10), MyPDO::PARAM_INT);
            $req->bindParam(":idLivre", utf8_decode($data['idLivre']), MyPDO::PARAM_STR);
            $req->bindParam(":dateEmprunt", utf8_decode($data['dateEmprunt']), MyPDO::PARAM_STR);
            $req->bindParam(":dateRetour", utf8_decode($data['dateRetour']), MyPDO::PARAM_STR);
            $req->execute();

            $resultat = array("success" => true, "action" => "update", "title" => "Info : ", "message" => "The  brrower has been changed successfully!  ", "icon" => "success");
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
                $result = deleteAction($data["cin"]);
                break;

           

            case 'getEmpruntRetourById':
                $result = getEmpruntRetourByIdAction($data["cin"]);
                break;
        }
        break;
}

header('Cache-Control: no-cache, must-revalidate');
header('Content-Type: application/json;charset=utf-8');
echo json_encode($result);
