<?php

    namespace App\classes\publication;

    use App\classes\Govno;
    use App\classes\Db;
    use App\interfaces\HasId;
    use App\interfaces\Shitty;
    use App\interfaces\UserInterface;
    use App\traits\GetSetTrait;
    use App\traits\SetControlTrait;

    /**
     * Class User
     * has following personal important methods:
     * <ul>
     * <li><b>findByLogin</b> - return null|object</li>
     * <li><b>CurrentUser</b> - return object containing data of a user by session and cookie or null</li>
     * </ul>
     * @package App\classes\publication
     */
    class User extends Govno implements HasId, UserInterface
    {
        protected const TABLE_NAME = 'users';
        protected $id = null, $date = null;
        protected $firstName = '', $middleName = '', $lastName = '', $login = '', $hash = '', $email = '', $rights = '';

        use SetControlTrait;
        /**
         * Возвращает объект с данными пользователя по его $login в случае успеха, либо null
         * @param string $login
         * @return object|null
         */
        public static function findByLogin(string $login) : ?User
        {
            $db = new Db();
            $sql = 'SELECT * FROM ' . static::TABLE_NAME . ' WHERE login = :login';
            $result = $db->queryOne($sql, ['login' => $login], static::class);
            return $result;
        }

        /**
         * Возвращает данные текущего пользователя по кукам и сесси, либо null
         * @param string $sessionFile
         * @return object|null
         */
        public static function getCurrent(string $sessionFile/*, string $cookeToken, string $sessionToken*/) : ?User
        {
            $cookeToken = $_COOKIE['token'] ?? null; // TODO добавить валидацию токенов?
            $sessionToken = $_SESSION['token'] ?? null;
            // если токен установлен и в сессии и в куки, то проверяем их совпадение
            // если совпадают, то возвращаем имя пользователя из сессии
            if ( ( $cookeToken && $sessionToken ) && ( $cookeToken === $sessionToken) ) {
                return self::findByLogin($_SESSION['user']);
            }
            // если токен в сессии и куке есть, но они не совпадают, то удаляем оба.
            if ( ( $cookeToken && $sessionToken ) && ( $cookeToken !== $sessionToken ) ) {
                unset($_SESSION['user'], $_SESSION['token']);
                setcookie('token', '', time() - 86400, '/');
                return null;
            }
            // если есть только куки-токен, или только сессионный токен, то сравниваем его с токеном из файла сессий (БД)
            // если совпадают, то берём имя пользователя из файла сессий (БД) и даём соответствующие права.
            $tokenOne =  $sessionToken ?? $cookeToken;
            $dbSession = getFileContent( $sessionFile );
            $haystack = array_column($dbSession, 'user', 'token');
            $userName = $haystack[$tokenOne] ?? null;
            // если нет ни куки-токена ни сессионного-токена, то возвращаем null
            // если пользователь получен, то вновь устанавливаем данные в сессию
            if (null !== $userName) {
                $_SESSION['user'] = $userName;
                $_SESSION['token'] = $tokenOne;
                return self::findByLogin($userName);
            }
            return null;
        }

//        public function checkPassword(string $login, string $password) : bool
//        {
//
//        }


//        public function checkPassword(string $login, string $password) : bool
//        {
//            if (!(empty($login) || empty($password))) {
//                $user = User::findByLogin($login);
//                if (isset($user)) {
//                    return password_verify($password, $user->getHash());
//                }
//            }
//            return false;
//        }

        public function __toString() : string
        {
            return "$this->login <br> $this->email <br> $this->date";
        }

        //<editor-fold desc="======================= getters">
        /**
         * @return string
         */
        public function getLogin(): ?string
        {
            return $this->login;
        }

        /**
         * @return string
         */
        public function getHash(): ?string
        {
            return $this->hash;
        }

        /**
         * @return null
         */
        public function getId() : ?string
        {
            return $this->id;
        }

        /**
         * @return string
         */
        public function getRights(): ?string
        {
            return $this->rights;
        }

        /**
         * @return string
         */
        public function getEmail(): ?string
        {
            return $this->email;
        }
        //</editor-fold>

        //<editor-fold desc="======================= setters">
        /**
         * @param string $email
         */
        public function setEmail(string $email): void
        {
            $this->email = $email;
        }

        /**
         * @param string $rights
         */
        public function setRights(string $rights): void
        {
            $this->rights = $rights;
        }

        /**
         * @param string $firstName
         */
        public function setFirstName(string $firstName): void
        {
            $this->firstName = $firstName;
        }

        /**
         * @param string $middleName
         */
        public function setMiddleName(string $middleName): void
        {
            $this->middleName = $middleName;
        }

        /**
         * @param string $lastName
         */
        public function setLastName(string $lastName): void
        {
            $this->lastName = $lastName;
        }

        /**
         * @param string $login
         */
        public function setLogin(string $login): void
        {
            $this->login = $login;
        }

        /**
         * @param null $id
         */
        private function setId($id): void
        {
        }

        /**
         * @param null $date
         */
        private function setDate($date): void
        {
        }

        /**
         * @param string $hash
         */
        private function setHash(string $hash): void
        {
        }
        //</editor-fold>
    }

