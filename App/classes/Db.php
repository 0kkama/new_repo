<?php

    namespace App\classes;

    use PDO;
    use PDOStatement;
    use App\classes\Config;

    class Db
    {

        private $dbh;

        public function __construct()
        {
            $this->dbh = $this->newConnection();
        }

        protected function newConnection() : PDO
        {
            $config = Config::getInstance();
            // $config->setInstance(include (PATH_TO_CONFIG));
            return new PDO
            ('mysql:host=' . $config->getDbHost() . ';dbname=' . $config->getDbName() . ';charset=' . $config->getDbChar(),
                $config->getDbUser(),$config->getDbPass(),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_CLASS,
                    PDO::ATTR_PERSISTENT => true,
                ]
            );
        }

        protected function checkQueryErr(PDOStatement $query) : bool
        {
            $errInfo = $query->errorInfo();
            if($errInfo[0] !== PDO::ERR_NONE) {
                trigger_error($errInfo[2], E_USER_ERROR);
            }
            return true;
        }

        public function execute(string $sql, array $data) : bool
        {
            // Метод execute(string $sql) выполняет запрос и возвращает true либо false в зависимости от того, удалось ли выполнение
            $query = $this->dbh->prepare($sql);
            $success = $query->execute($data);
            $this->checkQueryErr($query);
            return $success;
        }

        public function queryAll(string $sql, array $data, $class) : ?array
        {
            // Метод query(string $sql, array $data) выполняет запрос, подставляет в него данные $data, возвращает данные результата
            // запроса либо false, если выполнение не удалось
            $query = $this->dbh->prepare($sql);
            $query->execute($data);
            $this->checkQueryErr($query);
            $result = $query->fetchAll(PDO::FETCH_CLASS, $class);
            return $result ?: null;
        }

        public function queryOne(string $sql, array $data, $class) : ?object
        {
            $query = $this->dbh->prepare($sql);
            $query->setFetchMode(PDO::FETCH_CLASS, $class);
            $query->execute($data);
            $this->checkQueryErr($query);
            $result = $query->fetch();
            return  $result ?: null;
        }

        public function getLastId() : string
        {
            return $this->dbh->lastInsertId();
        }

    }
