<?php
    namespace ryan\controllers;

    use \Slim\Http\Request as Req;
    use \Slim\Http\Response as Res;

    class auth extends \ryan\main {

        protected $container;
        protected $userModels;
        protected $notificationModels;

        public function __construct ($container) {
            parent::__construct ($container);
            $this->container = $container;
            $this->userModels = new \ryan\models\users($container);
            $this->notificationModels = new \ryan\models\notifikasi($container);
        }

        public function loginPage (Req $req, Res $res, $args) {
            $req = $req->withAttribute ('lala', 'lolo');
            $req = $req->withAttribute ('authError', $this->flash->getMessage ('AuthError'));

            if(isset($this->session->id_user)){
                return $res->withStatus (302)->withHeader ('Location', $this->router->pathFor ('DashboardPage'));
            }else{
                return $this->view->render ("login", $req->getAttributes());
            }
        }

        public function doLogin ($req, $res, $args) {
            $username = $_POST[ 'username' ];
            $password = $_POST[ 'password' ];
            $login = $this->userModels->checkAuth ($username, md5 ($password));
            if ($login) {
                $this->session->set ('id_user', $login[ 'id_user' ]);
                $this->session->set ('previledge', $login[ 'previledge' ]);

                return $res->withStatus (302)->withHeader ('Location', $this->router->pathFor ('DashboardPage'));
            } else {
                $this->flash->addMessage ('AuthError', true);

                return $res->withStatus (302)->withHeader ('Location', $this->router->pathFor ('loginPage'));
            }
        }


        public function doLogout ($req, $res, $args) {
            $this->session->destroy ();

            return $res->withStatus (302)->withHeader ('Location', $this->router->pathFor ('loginPage'));
        }

        public function __invoke ($req, $res, $next) {
            $this->view->registerFunction('getUserDetail', function($id_user){
                $user = $this->userModels->getUserDetail($id_user);
                return $user;
            });
            $this->view->registerFunction('thisPriviledge', function(){
                $user = $this->userModels->getUserDetail($this->session->id_user)['previledge'];
                switch ($user){
                    case '1':
                        return 'admin';
                        break;
                    case '2':
                        return 'direktur';
                        break;
                    case '3':
                        return 'manajer';
                        break;
                    case '4':
                        return 'unitkerja';
                        break;
                }
                return $user;
            });
            if(!isset($this->session->id_user)){
                if($req->isGet()) {
                    return $res->withStatus(302)->withHeader('Location', $this->router->pathFor('loginPage'));
                }else {
                    return $res->withStatus(405)->withJson(['success'=>false, 'cause'=>'Session Expired']);
                }
            }else{
                $userData = $this->userModels->getUserDetail($this->session->id_user);
                $notificationData = $this->notificationModels->getNotificationForUser($this->session->id_user);
                $req = $req->withAttribute('active_user_data', $userData);
                $req = $req->withAttribute('active_notification_list', $notificationData);
                $res = $next($req, $res);
                return $res;
            }

        }

        public function signUpPage(Req $req, Res $res, $args){
            $req = $req->withAttribute ('sign_data', $this->userModels->getUserByToken($args['token']));
            return $this->view->render ("signup", $req->getAttributes());
        }

        public function check(Req $req, Res $res, $args){
            if(isset($_GET['username'])){
                if($this->userModels->getUserByUsername($_GET['username'])){
                    return $res->withStatus(404);
                }else{
                    return $res->withStatus(200);
                }
            }
        }

        public function coba(Req $req, Res $res, $args){
//            $uri = $req->getUri();
//            $this->mailer->addAddress('genthowijaya@gmail.com', 'Amin Wijaya');
//            $this->mailer->Subject = 'Undangan penggunaan aplikasi';
//            $this->mailer->isHTML(true);
//            $this->mailer->Body = $this->view->render ("email", $req->getAttributes());
//            $this->mailer->send();
//            $data = $this->pdo->select()->from('user')->where('id_user', '=', 1)->count('*', 'count')->execute()->fetch();
//            return $res->withJson($data);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => 'http://www.freesms4us.com/kirimsms.php?user=mientz&pass=1412dk&no=085649411233&isi=halooooooooo'
            ));
            return $res->write(curl_exec($curl));
        }
    }
