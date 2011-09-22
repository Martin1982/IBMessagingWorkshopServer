<?php

/**
 * Action User 
 * 
 * GET: Retrieve the data of a user
 * POST: Create or edit a user
 * 
 * In both cases a username needs to be provided.
 * 
 * @link http://getfrapi.com
 * @author Frapi <frapi@getfrapi.com>
 * @link /user
 */
class Action_User extends Frapi_Action implements Frapi_Action_Interface
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
        return $this->executeGet();
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

        $authKey = $this->getParam('authKey');
        $username = $this->getParam('username');
        $meta = new stdClass();
        
        if (empty($authKey) && empty($username)) {
            $meta->success = 0;
            $meta->error = 'No username or authKey given';
            return $this->toArray();
        }
        
        $db = Frapi_Database::getInstance();
        if (!empty($username)) {
            $userSQL = "SELECT * FROM users WHERE username = ?";
            $userStmt = $db->prepare($userSQL);
            $userStmt->execute(array($username));
        } else {
            $userSQL = "SELECT * FROM users WHERE MD5(CONCAT(username, password)) = ?";
            $userStmt = $db->prepare($userSQL);
            $userStmt->execute(array($authKey));
        }
        
        $userData = $userStmt->fetch(PDO::FETCH_OBJ);
        
        $meta->success = $userData ? 1 : 0;
        $meta->data = $userData;
        
        $this->data['meta'] = $meta;
        $this->data['data'] = $userData;
        
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
        $valid = $this->hasRequiredParameters($this->requiredParams);
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }
        
        $db = Frapi_Database::getInstance();
        $params = array(
            'username' => $this->getParam('username'),
            'password' => $this->getParam('password'),
            'firstname' => $this->getParam('firstname'),
            'lastname' => $this->getParam('lastname'),
            'email' => $this->getParam('email'),
            'phone' => $this->getParam('phone')
        );
        
        $userSQL = "SELECT * FROM users WHERE username = ?";
        $userStmt = $db->prepare($userSQL);
        $userStmt->execute(array($params['username']));
        
        $userData = $userStmt->fetch(PDO::FETCH_OBJ);
                
        if (!isset($params['username']) || empty($params['username']) || !ctype_alnum($params['username'])) {
            throw new Frapi_Error('INVALID_USERNAME', 'Users can only contain letters and numbers', 406);
        }
        
        $meta = new stdClass();
        $sql = "REPLACE INTO 
                    users (
                        username,
                        password,
                        firstname,
                        lastname,
                        email,
                        phone
                    ) VALUES (
                        :username,
                        :password,
                        :firstname,
                        :lastname,
                        :email,
                        :phone
                    )";
        $q = $db->prepare($sql);
        $q->execute(
            array(
                ':username' => $params['username'],
                ':password' => $params['password'],
                ':firstname' => $params['firstname'],
                ':lastname' => $params['lastname'],
                ':email' => $params['email'],
                ':phone' => $params['phone']
            )
        );
        $meta->success = (int) 1;
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
        return $this->executePost();
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

