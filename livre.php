<?php
require 'db/MyPDO.php';

ini_set('display_errors', false);
ini_set('html_errors', false);

function newidLivre(){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $res = $pdo->query("SELECT max(idLivre) AS newidLivre FROM livre ");
        $record = $res->fetchAll(PDO::FETCH_ASSOC);
        $data = ( (int) $record[0]['newidLivre']) + 1 ;
        $resultat = array("success" => true, "action" => "newidLivre", "data" => $data);
    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "newidLivre", "title" => "Erreur : ", "message" => "Erreur : " . $e->getMessage(), "icon" => "warning");
    }

    $pdo->close($pdo);
    return $resultat;
}

function getlivre($idLivre){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $req = $pdo->prepare("SELECT * FROM livre where idLivre =:idLivre");
        $req->bindParam(":idLivre", $idLivre, MyPDO::PARAM_INT);
        $req->execute();

        if($req->rowCount() == 1) {
            $record = $req->fetchAll(PDO::FETCH_ASSOC);
            $resultat = array("success" => true, "action" => "getlivre", "idLivre" => $record[0]['idLivre'], "idTheme" => $record[0]['idTheme'],"idMaison" => $record[0]['idMaison'],"idAuteur" => $record[0]['idAuteur'],"titre" => $record[0]['titre']
            ,"nbrePages" => $record[0]['nbrePages'],"prix" => $record[0]['prix'],"dateAchat" => $record[0]['dateAchat'],"disponible" => $record[0]['disponible']);
        }else{
            $resultat = array("success" => false, "action" => "getlivre", "title" => "Erreur : ", "message" => "Erreur : Cette livre est inexistante dans la liste des livres ! ", "icon" => "warning");
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
            $res = $pdo->query("SELECT * FROM livre where idLivre =: idLivre ");
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

function deleteAction($idLivre){
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $req = $pdo->prepare("DELETE FROM livre WHERE idLivre =: idLivre");
        $req->bindParam(":idLivre", $idLivre, MyPDO::PARAM_INT);
        $req->execute();

        $resultat = array("success" => true, "action" => "delete", "data" => $idLivre , "title" => "Info : ", "message" => "La suppression a été effectuée avec succès ! ", "icon" => "success");
    } catch (Exception $e) {
        $resultat = array("success" => false, "action" => "delete", "data" => $idLivre, "title" => "Erreur : ", "message" => $e->getMessage() . " ! ", "icon" => "warning");
    }

    $pdo->close($pdo);
    return $resultat;
}


function createAction($data) {
    $resultat = array();
    $pdo = new MyPDO();

    try {
        $req = $pdo->prepare("INSERT INTO `livre` (`idLivre`, `idTheme`, `idMaison`,`idAuteur`,`titre`, `nbrePages`, `prix`,`dateAchat`,`disponible`) VALUES (:idLivre, :idTheme, :idMaison, :idAuteur, :titre, :nbrePages, :prix, :dateAchat, :disponible) ");
        if($data['idLivre']==''){
            $val = null;
            $req->bindParam(":idLivre", $val, MyPDO::PARAM_NULL);
        }else{
            $req->bindParam(":idLivre", intval($data['idLivre'], 10), MyPDO::PARAM_INT);
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
            $req = $pdo->prepare("UPDATE `livre` SET  idTheme =: idTheme,idMaison = :idMaison,idAuteur = :idAuteur, titre = :titre ,nbrePages = :nbrePages, prix = :prix,dateAchat = :dateAchat,  disponible= :disponible WHERE idLivre = :idLivre ");
            $req->bindParam(":idLivre", intval($data['idLivre'], 10), MyPDO::PARAM_INT);
            $req->bindParam(":idTheme", utf8_decode($data['idTheme']), MyPDO::PARAM_STR);
            $req->bindParam(":idMaison", $data['idMaison'], MyPDO::PARAM_STR);
            $req->bindParam(":idAuteur", utf8_decode($data['idAuteur']), MyPDO::PARAM_STR);
            $req->bindParam(":titre", utf8_decode($data['titre']), MyPDO::PARAM_STR);
            $req->bindParam(":nbrePages", utf8_decode($data['nbrePages']), MyPDO::PARAM_STR);
            $req->bindParam(":prix", utf8_decode($data['prix']), MyPDO::PARAM_STR);
            $req->bindParam(":disponible", utf8_decode($data['disponible']), MyPDO::PARAM_STR);

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
                $result = deleteAction($data["idLivre"]);
                break;

            case 'newidLivre':
                $result = newidLivre();
                break;

            case 'getlivre':
                $result = getlivre($data["idLivre"]);
                break;
        }
        break;
}

header('Cache-Control: no-cache, must-revalidate');
header('Content-Type: application/json;charset=utf-8');
echo json_encode($result);
