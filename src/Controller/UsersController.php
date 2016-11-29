<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Utility\Security;
/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
     public function index()
     {
         $this->RequestHandler->renderAs($this,'json');
         $this->viewBuilder()->layout(null);

         $status = "OK";
         $message = "";
         $datas = [];

         $final = json_encode(compact('status', 'message', 'datas'));
         $this->resonse->body($final);
     }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);

        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

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
        $final = json_encode(compact('status', 'message', 'datas'));
        $this->resonse->body($final);
    }

    public function connect()
    {
        $this->RequestHandler->renderAs($this,'json');
        $this->viewBuilder()->layout(null);

        $status = "OK";
        $message = "";
        $datas = [];

        $pseudo = $this->request->data["pseudo"];
        $password = $this->request->data["password"];

        // check if user exist
        $query_user_exist = $this->Users->find('all')->where(['pseudo = ' => $pseudo]);
        $user_db = $query_user_exist->first();
        if($user_db !== null){
            // check if password is ok
            if($password == $user_db->password){
                $message = "Connection is successfull";
                array_push($datas,$user_db);
            } else {
                $status = "KO";
                $message = "Impossible to connect ::: Wrong password";
            }
        } else {
            $status = "KO";
            $message = "Impossible to connect ::: User do not exist";
        }

        $final = json_encode(compact('status', 'message', 'datas'));
        $this->resonse->body($final);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->RequestHandler->renderAs($this,'json');
        $this->viewBuilder()->layout(null);

        $status = "OK";
        $message = "";
        $datas = [];

        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $message = "User is successfully delete";
        } else {
            $status = "KO";
            $message = "Impossible to delete user ::: Check the content of your request";
        }

        $this->set(compact('status', 'message', 'datas'));
        $this->set('_serialize', ['status', 'message', 'datas']);
    }
}
