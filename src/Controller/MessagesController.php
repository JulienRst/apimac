<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;

/**
 * Messages Controller
 *
 * @property \App\Model\Table\MessagesTable $Messages
 */
class MessagesController extends AppController
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

        $session = new SessionsController();
        $apikey = $this->request->data["key"];

        if($session->verify($apikey)){
            $messages = $this->Messages->find('all', [
                'order' => ['Messages.timestamp' => 'ASC'],
                'contain' => ['Users']
            ]);

            $datas["messages"] = array();
            foreach($messages as $message_){
                if($message_->user->id == $session->getUserId($apikey)){
                    array_push($datas["messages"],["message" => $message_->message, "pseudo" => $message_->user->pseudo, "self" => true]);
                } else {
                    array_push($datas["messages"],["message" => $message_->message, "pseudo" => $message_->user->pseudo, "self" => false]);
                }
            }
        } else {
            $status = "BACKOFF";
            $message = "NOT CONNECTED";
        }

        $this->set(compact('status', 'message', 'datas'));
        $this->set('_serialize', ['status', 'message', 'datas']);
    }

    /**
     * View method
     *
     * @param string|null $id Message id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $message = $this->Messages->get($id, [
            'contain' => []
        ]);

        $this->set('message', $message);
        $this->set('_serialize', ['message']);
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
        $this->loadModel("Users");
        $status = "OK";
        $message = "";
        $datas = [];

        $message_ = $this->Messages->newEntity();
        if ($this->request->is('post')) {
            $message_ = $this->Messages->patchEntity($message_, $this->request->data);
            $session = new SessionsController();
            $apikey = $this->request->data["key"];

            if($session->verify($apikey)){
                $idUser = $session->getUserId($apikey);
                $query_user_exist = $this->Users->find('all')->where(['id = ' => $idUser]);
                $user_db = $query_user_exist->first();
                if($user_db !== null){
                    $message_->id_user = $idUser;
                    $message_->timestamp = Time::now()->timestamp;
                    if ($this->Messages->save($message_)) {
                        $message = "Message is successfully save";
                        array_push($datas,$message_);
                    } else {
                        $status = "KO";
                        $message = "Not possible to save message ::: Check the content of your request";
                    }
                } else {
                    $status = "BACKOFF";
                    $message = "USER NOT FOUND";
                }
            } else {
                $status = "BACKOFF";
                $message = "NOT CONNECTED";
            }


        } else {
            $status = "KO";
            $message = "Bad Request ::: Not POST";
        }

        $this->set(compact('status', 'message', 'datas'));
        $this->set('_serialize', ['status', 'message', 'datas']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Message id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $message = $this->Messages->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $message = $this->Messages->patchEntity($message, $this->request->data);
            if ($this->Messages->save($message)) {
                $this->Flash->success(__('The message has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The message could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('message'));
        $this->set('_serialize', ['message']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Message id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $message = $this->Messages->get($id);
        if ($this->Messages->delete($message)) {
            $this->Flash->success(__('The message has been deleted.'));
        } else {
            $this->Flash->error(__('The message could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
