<?php
require 'db/MyPDO.php';

ini_set('display_errors', false);
ini_set('html_errors', false);

function newIdTheme(){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $res = $pdo->query("SELECT MAX(idTheme) AS newIdTheme FROM Theme ");
        $record = $res->fetchAll(PDO::FETCH_ASSOC);
        $data = ( (int) $record[0]['newIdTheme']) + 1 ;
        $resultat = array("success" => true, "action" => "newIdTheme", "data" => $data);
    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "newIdTheme", "title" => "Erreur : ", "message" => "Erreur : " . $e->getMessage(), "icon" => "warning");
    }

    $pdo->close($pdo);
    return $resultat;
}

function getThemeByIdAction($idTheme){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $req = $pdo->prepare("SELECT * FROM Theme WHERE idTheme = :idTheme ");
        $req->bindParam(":idTheme", $idTheme, MyPDO::PARAM_INT);
        $req->execute();

        if($req->rowCount() == 1) {
            $record = $req->fetchAll(PDO::FETCH_ASSOC);
            $resultat = array("success" => true, "action" => "getThemeById", "idTheme" => $record[0]['idTheme'], "designTheme" => $record[0]['designTheme']);
        }else{
            $resultat = array("success" => false, "action" => "getThemeById", "title" => "Erreur : ", "message" => "Erreur :This theme does not exist in the list of themes! ", "icon" => "warning");
        }
    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "getThemeById", "title" => "Erreur : ", "message" => "Erreur : " . $e->getMessage(), "icon" => "warning");
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
            $res = $pdo->query("SELECT * FROM Theme ");
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

function deleteAction($idTheme){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $req = $pdo->prepare("DELETE FROM Theme WHERE idTheme=:idTheme");
        $req->bindParam(":idTheme", $idTheme, MyPDO::PARAM_INT);
        $req->execute();

        $resultat = array("success" => true, "action" => "delete", "data" => $idTheme , "title" => "Info : ", "message" => "The deletion was successful! ", "icon" => "success");
    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "delete", "data" => $idTheme, "title" => "Erreur : ", "message" => $e->getMessage() . " ! ", "icon" => "warning");
    }

    $pdo->close($pdo);
    return $resultat;
}

function searchAction($data){
    $result = array();
    $pdo = new MyPDO();
    //return json_encode(array("success" => false, "action" => "search", "title" => "Erreur : ", "message" => "" . " ! ", "icon" => "warning"));
    
    try {
        $req = $pdo->query("SELECT * FROM Theme where designTheme like '%".$data['search']."%'");
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
        $req = $pdo->prepare("INSERT INTO `theme` (`idTheme`, `designTheme`) VALUES (:idTheme, :designTheme) ");
        if($data['idTheme']==''){
            $val = null;
            $req->bindParam(":idTheme", $val, MyPDO::PARAM_NULL);
        }else{
            $req->bindParam(":idTheme", intval($data['idTheme'], 10), MyPDO::PARAM_INT);
        }
        $req->bindParam(":designTheme", $data['designTheme'], MyPDO::PARAM_STR);
        $req->execute();

        $resultat = array("success" => true, "action" => "create", "title" => "Info : ", "message" => "The new theme has been successfully added! ", "icon" => "success");
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
            $req = $pdo->prepare("UPDATE `Theme` SET designTheme = :designTheme WHERE idTheme = :idTheme ");
            $req->bindParam(":idTheme", intval($data['idTheme'], 10), MyPDO::PARAM_INT);
            $req->bindParam(":designTheme", utf8_decode($data['designTheme']), MyPDO::PARAM_STR);
            $req->execute();

            $resultat = array("success" => true, "action" => "update", "title" => "Info : ", "message" => "The theme has been changed successfully! ", "icon" => "success");
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
                $result = deleteAction($data["idTheme"]);
                break;

            case 'newIdTheme':
                $result = newIdTheme();
                break;

            case 'getThemeById':
                $result = getThemeByIdAction($data["idTheme"]);
                break;
        }
        break;
}

header('Cache-Control: no-cache, must-revalidate');
header('Content-Type: application/json;charset=utf-8');
echo json_encode($result);
