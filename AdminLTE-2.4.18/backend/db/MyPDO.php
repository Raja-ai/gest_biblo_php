<?php

class MyPDO extends PDO
{
    private $driver;
    private $host;
    private $user;
    private $pass;
    private $dbName;

    /**
     * @return string
     */
    public function getDriver(){
        return $this->driver;
    }

    /**
     * @param string $driver
     */
    public function setDriver($driver){
        $this->driver = $driver;
    }

    /**
     * @return string
     */
    public function getHost(){
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host){
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getUser(){
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user){
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getPass(){
        return $this->pass;
    }

    /**
     * @param string $pass
     */
    public function setPass($pass){
        $this->pass = $pass;
    }

    /**
     * @return string
     */
    public function getDbName(){
        return $this->dbName;
    }

    /**
     * @param string $dbName
     */
    public function setDbName($dbName){
        $this->dbName = $dbName;
    }

    public function __construct (){
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-Type: application/json;charset=utf-8');
        $file = "db/config.ini";

        if(file_exists($file)) {
            $config = parse_ini_file($file, true);

            $this->setDriver( $config['DataBase']['sgbd'] );
            $this->setHost( $config['DataBase']['serveur'] );
            $this->setUser( $config['DataBase']['utilisateur'] );
            $this->setPass( $config['DataBase']['mot_de_passe'] );
            $this->setDbName( $config['DataBase']['base_de_donnees'] );

            try {
                parent::__construct("" . self::getDriver() . ":host=" . self::getHost() . ";dbname=" . self::getDbName() . "", self::getUser(), self::getPass(), array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                //$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAME'utf8'");
                $this->query("SET character_set_results=utf8");
            } catch (PDOException $e) {
                $result = array("success" => false, "action" => "connect", "title" => "Erreur : ", "message" => $e->getMessage() . " ! ", "icon" => "warning");
                echo json_encode($result);
                die();
            }
        }else{
            $result = array("success" => false, "action" => "connect", "title" => "Erreur : ", "message" => "Le fichier de configuration est inexistant ! ", "icon" => "warning");
            echo json_encode($result);
            die();
        }
    }

    public function close(&$pdo) {
        if($pdo != null){
            $pdo=null;
        }
    }
}
