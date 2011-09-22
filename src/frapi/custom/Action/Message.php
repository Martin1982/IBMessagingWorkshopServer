<?php

/**
 * Action Message 
 * 
 * GET: Get a list of messages (optional parameter 'username' to retrieve the
 * messages of 1 user)
 * POST: Send a new message (required parameter 'message')
 * 
 * @link http://getfrapi.com
 * @author Frapi <frapi@getfrapi.com>
 * @link /message
 */
class Action_Message extends Frapi_Action implements Frapi_Action_Interface
{

    /**
     * Required parameters
     * 
     * @var An array of required parameters.
     */
    protected $requiredParams = array();

    /**
     * The data container to use in toArray()
     * 
     * @var A container of data to fill and return in toArray()
     */
    private $data = array();

    /**
     * To Array
     * 
     * This method returns the value found in the database 
     * into an associative array.
     * 
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Default Call Method
     * 
     * This method is called when no specific request handler has been found
     * 
     * @return array
     */
    public function executeAction()
    {
        $this->executeGet();
    }

    /**
     * Get Request Handler
     * 
     * This method is called when a request is a GET
     * 
     * @return array
     */
    public function executeGet()
    {
        $db = Frapi_Database::getInstance();
        $username = $this->getParam('username');
        $userfilter = '';
        $filter = array();
        
        if(!empty($username)) {
            $userfilter = " WHERE m.username = ? ";
            $filter[] = $username;
        }        
        
        $msgSQL = "SELECT m.*, u.* FROM messages m LEFT JOIN users u ON m.username = u.username " . $userfilter . " ORDER BY placed DESC";
        $msgStmt = $db->prepare($msgSQL);
        $msgStmt->execute($filter);
        
        $msgData = $msgStmt->fetchAll(PDO::FETCH_OBJ);
        $meta = new stdClass();
        $meta->success = $msgData ? 1 : 0;
        
        $this->data['meta'] = $meta;
        $this->data['data'] = $msgData;
        
        return $this->toArray();
    }

    /**
     * Post Request Handler
     * 
     * This method is called when a request is a POST
     * 
     * @return array
     */
    public function executePost()
    {
        $db = Frapi_Database::getInstance();
        $params = $this->getParams();
        
        if (!isset($params['authKey'])) {
            throw new Frapi_Error('NO_KEY', 'No authkey given', 406);
        }
        
        $userSQL = "SELECT * FROM users WHERE MD5(CONCAT(username,password)) = ?";
        $userStmt = $db->prepare($userSQL);
        $userStmt->execute(array($params['authKey']));
        
        $userData = $userStmt->fetchObject();
        
        if (!$userData) { 
            throw new Frapi_Error('INCORRECT_USER', 'No valid authkey given', 406);
        }
        
        if (!isset($params['message']) || empty($params['message'])) {
            throw new Frapi_Error('NO_MESSAGE', 'No or empty message', 406);
        }
        $meta = new stdClass();
        $sql = "INSERT INTO messages (username,message) VALUES (:username,:message)";
        $q = $db->prepare($sql);
        $meta->success = (int) $q->execute(
            array(
                ':message'  => strip_tags($params['message']),
                ':username' => $userData->username
            )
        );
        
        $this->data['meta'] = $meta;
       
        return $this->toArray();
    }

    /**
     * Put Request Handler
     * 
     * This method is called when a request is a PUT
     * 
     * @return array
     */
    public function executePut()
    {
        throw new Frapi_Error('NOT_ALLOWED', 'Updating is not allowed', 405);
    }

    /**
     * Delete Request Handler
     * 
     * This method is called when a request is a DELETE
     * 
     * @return array
     */
    public function executeDelete()
    {
        throw new Frapi_Error('NOT_ALLOWED', 'Delete is not allowed', 405);
    }

    /**
     * Head Request Handler
     * 
     * This method is called when a request is a HEAD
     * 
     * @return array
     */
    public function executeHead()
    {
        return $this->executeGet();
    }


}

