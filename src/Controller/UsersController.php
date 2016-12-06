<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Controller\SessionsController;
use Cake\Utility\Security;
use Cake\Network\Session;
/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->RequestHandler->renderAs($this,'json');
        $this->viewBuilder()->layout(null);

        $status = "OK";
        $message = "";
        $datas = [];

        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            $query = $this->Users->find('all')->where(['pseudo =' => $user->pseudo]);
            if($query->first() === null){
                if ($this->Users->save($user)) {
                    $message = "User is successfully save";
                    array_push($datas,$user);
                } else {
                    $status = "KO";
                    $message = "Not possible to save user ::: Check the content of your request";
                }
            } else {
                $status = "KO";
                $message = "Impossible to add ::: Pseudo already taken";
            }

        } else {
            $status = "KO";
            $message = "Bad Request ::: Not POST";
        }
        $this->set(compact('status', 'message', 'datas'));
        $this->set('_serialize', ['status', 'message', 'datas']);
    }

    public function connect()
    {
        $this->RequestHandler->renderAs($this,'json');
        $this->viewBuilder()->layout(null);
        $this->loadModel('Sessions');

        $status = "OK";
        $message = "";
        $datas = [];

        $pseudo = (isset($this->request->data["pseudo"])) ? $this->request->data["pseudo"] : null;
        $password = (isset($this->request->data["password"])) ? $this->request->data["password"] : null;

        // check if user exist
        $query_user_exist = $this->Users->find('all')->where(['pseudo = ' => $pseudo]);
        $user_db = $query_user_exist->first();
        if($user_db !== null){
            // check if password is ok
            if($password == $user_db->password){
                $message = "Connection is successfull";
                $session = new SessionsController();
                $key = $session->create($user_db->id);
                $datas = ["key" => $key];
            } else {
                $status = "KO";
                $message = "Impossible to connect ::: Wrong password";
            }
        } else {
            $status = "KO";
            $message = "Impossible to connect ::: User do not exist";
        }

        $this->set(compact('status', 'message', 'datas'));
        $this->set('_serialize', ['status', 'message', 'datas']);
    }
}
