<?php
    /**
     * Created by PhpStorm.
     * User: wijaya
     * Date: 3/23/2017
     * Time: 17:54
     */

    namespace ryan\models;


    class users extends \ryan\main {

        protected $container;

        public function __construct ($container) {
            parent::__construct ($container);
            $this->container = $container;
        }

        public function checkAuth ($username, $password) {
            $login = $this->db->prepare ("select * from user where username=:username and password=:password");
            $login->bindParam (':username', $username);
            $login->bindParam (':password', $password);
            $login->execute ();

            return $login->fetch ();
        }

        public function getUserDetail($id_user){
            $user = $this->db->prepare("select * from user where id_user=:id_user");
            $user->bindParam(':id_user', $id_user);
            $user->execute();
            return $user->fetch();
        }

        public function getUserWithPreviledge($previledge) {
            $user = $this->db->prepare("select * from user where PREVILEDGE=:previledge");
            $user->bindParam(':previledge', $previledge);
            $user->execute();
            return $user->fetchAll();
        }

        public function getUser($id_user = null) {
            if($id_user == null){
                return $this->db->query('select * from user')->fetchAll();
            }else{
                $user = $this->db->prepare("select * from user where ID_USER=:ID_USER");
                $user->bindParam(':ID_USER', $id_user);
                $user->execute();
                return $user->fetchAll();
            }
        }
    }