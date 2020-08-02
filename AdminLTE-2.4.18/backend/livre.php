<?php
require 'db/MyPDO.php';

ini_set('display_errors', false);
ini_set('html_errors', false);

function newIdlivre(){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $res = $pdo->query("SELECT max(idlivre) AS newIdlivre FROM livre ");
        $record = $res->fetchAll(PDO::FETCH_ASSOC);
        $data = ( (int) $record[0]['newIdlivre']) + 1 ;
        $resultat = array("success" => true, "action" => "newIdlivre", "data" => $data);
    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "newIdlivre", "title" => "Erreur : ", "message" => "Erreur : " . $e->getMessage(), "icon" => "warning");
    }

    $pdo->close($pdo);
    return $resultat;
}

function getlivre($idLivre){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $req = $pdo->prepare("SELECT * FROM livre  where idlivre =:idlivre");
        $req->bindParam(":idlivre", $idLivre, MyPDO::PARAM_INT);
        $req->execute();

        if($req->rowCount() == 1) {
            $record = $req->fetchAll(PDO::FETCH_ASSOC);
            $resultat = array("success" => true, "action" => "getlivre", "idlivre" => $record[0]['idLivre'], "idTheme" => $record[0]['idTheme'],"idMaison" => $record[0]['idMaison'],"idAuteur" => $record[0]['idAuteur'],"titre" => $record[0]['titre']
            ,"nbrePages" => $record[0]['nbrePages'],"prix" => $record[0]['prix'],"dateAchat" => $record[0]['dateAchat'],"disponible" => $record[0]['disponible']);
        }else{
            $resultat = array("success" => false, "action" => "getlivre", "title" => "Erreur : ", "message" => "Erreur : This book does not exist in the list of book! ", "icon" => "warning");
        }
    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "getlivre", "title" => "Erreur : ", "message" => "Erreur : " . $e->getMessage(), "icon" => "warning");
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
            $res = $pdo->query("SELECT * FROM livre ");
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

function deleteAction($idlivre){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $req = $pdo->prepare("DELETE FROM livre WHERE idlivre=:idlivre");
        $req->bindParam(":idlivre", $idlivre, MyPDO::PARAM_INT);
        $req->execute();

        $resultat = array("success" => true, "action" => "delete", "data" => $idlivre , "title" => "Info : ", "message" => "The deletion was successful! ", "icon" => "success");
    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "delete", "data" => $idlivre, "title" => "Erreur : ", "message" => $e->getMessage() . " ! ", "icon" => "warning");
    }

    $pdo->close($pdo);
    return $resultat;
}

function searchAction($data){
    $result = array();
    $pdo = new MyPDO();
    try {
        $req = $pdo->query("SELECT * FROM livre where titre like '%".$data['search']."%'");
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
        $req = $pdo->prepare("INSERT INTO `livre` (`idlivre`, `idTheme`, `idMaison`,`idAuteur`,`titre`, `nbrePages`, `prix`,`dateAchat`,`disponible`) VALUES (:idlivre, :idTheme, :idMaison, :idAuteur, :titre, :nbrePages, :prix, :dateAchat, :disponible) ");
        if($data['idlivre']==''){
            $val = null;
            $req->bindParam(":idlivre", $val, MyPDO::PARAM_NULL);
        }else{
            $req->bindParam(":idlivre", intval($data['idlivre'], 10), MyPDO::PARAM_INT);
        }
        $req->bindParam(":idTheme", $data['idTheme'], MyPDO::PARAM_STR);
        $req->bindParam(":idMaison", $data['idMaison'], MyPDO::PARAM_STR);
        $req->bindParam(":idAuteur", $data['idAuteur'], MyPDO::PARAM_STR);
        $req->bindParam(":titre", $data['titre'], MyPDO::PARAM_STR);
        $req->bindParam(":nbrePages", $data['nbrePages'], MyPDO::PARAM_STR);
        $req->bindParam(":prix", $data['prix'], MyPDO::PARAM_STR);
        $req->bindParam(":dateAchat", $data['dateAchat'], MyPDO::PARAM_STR);
        $req->bindParam(":disponible", $data['disponible'], MyPDO::PARAM_STR);


        $req->execute();

        $resultat = array("success" => true, "action" => "create", "title" => "Info : ", "message" => "La nouvelle livre a été ajoutée avec succès ! ", "icon" => "success");
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
            $req = $pdo->prepare("UPDATE `livre` SET  idTheme = :idTheme,idMaison = :idMaison,idAuteur = :idAuteur, titre = :titre ,nbrePages = :nbrePages, prix = :prix,dateAchat = :dateAchat,  disponible= :disponible WHERE idlivre = :idlivre ");//:idlivre, :idTheme, :idMaison, :idAuteur, :titre, :nbrePages, 
            $req->bindParam(":idlivre", intval($data['idlivre'], 10), MyPDO::PARAM_INT);
            $req->bindParam(":idTheme", utf8_decode($data['idTheme']), MyPDO::PARAM_STR);
            $req->bindParam(":idMaison", $data['idMaison'], MyPDO::PARAM_STR);
            $req->bindParam(":idAuteur", utf8_decode($data['idAuteur']), MyPDO::PARAM_STR);
            $req->bindParam(":titre", utf8_decode($data['titre']), MyPDO::PARAM_STR);
            $req->bindParam(":nbrePages", utf8_decode($data['nbrePages']), MyPDO::PARAM_STR);
            $req->bindParam(":prix", utf8_decode($data['prix']), MyPDO::PARAM_STR);
            $req->bindParam(":dateAchat", $data['dateAchat'], MyPDO::PARAM_STR);
            $req->bindParam(":disponible", utf8_decode($data['disponible']), MyPDO::PARAM_STR);

            $req->execute();

            $resultat = array("success" => true, "action" => "update", "title" => "Info : ", "message" => "The book has been changed successfully! ", "icon" => "success");
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
                $result = deleteAction($data["idlivre"]);
                break;
            case 'search':
                $result = searchAction($data);
                break;
            case 'newIdlivre':
                $result = newIdlivre();
                break;

            case 'getlivre':
                $result = getlivre($data["idlivre"]);
                break;
        }
        break;
}

header('Cache-Control: no-cache, must-revalidate');
header('Content-Type: application/json;charset=utf-8');
echo json_encode($result);
