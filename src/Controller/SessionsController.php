<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;

/**
 * Sessions Controller
 *
 * @property \App\Model\Table\SessionsTable $Sessions
 */
class SessionsController extends AppController
{
    public function create($user_id) {
        $session = $this->Sessions->newEntity();
        $session->user_id = $user_id;
        $request_session_exist = $this->Sessions->find('all')->where(["user_id =" => $user_id]);
        $session_db = $request_session_exist->first();
        if($session_db !== null){
            if($this->update($session_db->apikey)){
                return $session_db->apikey;
            } else {
                return false;
            }
        } else {
            $session->timestamp = Time::now()->timestamp;
            $session->apikey = md5(uniqid(rand(), true));
            if($this->Sessions->save($session)){
                return $session->apikey;
            } else {
                return false;
            }
        }
    }

    public function update($key) {
        $session = $this->Sessions->find('all')->where(['apikey =' => $key]);
        $session = $session->first();
        if($session !== null){
            $session->timestamp = Time::now()->timestamp;
            $this->Sessions->save($session);
            return true;
        } else {
            return false;
        }
    }

    public function verify($key) {
        // $this->clean();
        $query = $this->Sessions->find('all')->where(['apikey =' => $key]);
        $session = $query->first();
        if($session !== null){
            return true;
        } else {
            return false;
        }
    }

    public function delete($id) {
        $session = $this->Sessions->get($id);
        if($this->Sessions->delete($session)){
            return true;
        } else {
            return false;
        }
    }

    public function clean() {
        $sessions = $this->Sessions->find('all');
        foreach ($sessions as $session) {
            $this->delete($session->id);
        }
    }

    public function getUserId($key) {
        $query = $this->Sessions->find('all')->where(['apikey =' => $key]);
        $session = $query->first();
        if($session !== null){
            return $session->user_id;
        } else {
            return false;
        }
    }
}
