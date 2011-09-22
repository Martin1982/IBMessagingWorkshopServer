<?php

/**
 * Action Auth 
 * 
 * Authenticate to the server and get an authkey on success
 * 
 * @link http://getfrapi.com
 * @author Frapi <frapi@getfrapi.com>
 * @link /auth
 */
class Action_Auth extends Frapi_Action implements Frapi_Action_Interface
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
        $username = $this->getParam('username');
        $password = $this->getParam('password');
        
        $db = Frapi_Database::getInstance();
        $sql = "SELECT * FROM USERS WHERE username = :username and password = :password";
        $query = $db->prepare($sql);
        $exec = $query->execute(
            array(
                ':username' => $username,
                ':password' => $password
            )
        );
        
        $userData = $query->fetchObject();
        
        $meta = new stdClass();
        $meta->success = count($userData) ? 1 : 0;
        
        if ($meta->success == 1) {
            $data = new stdClass();
            $data->authKey = md5($userData->username . $userData->password);
            $data->username = $userData->username;
        }
        
        $this->data['meta'] = $meta;
        $this->data['data'] = $data;
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
        return $this->executeGet();
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
        return $this->executeGet();
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
        return $this->executeGet();
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
        return $this->toArray();
    }

    public static function isAuthenticated($userkey) {
        $db = Frapi_Database::getInstance();
        $sql = "SELECT * FROM USERS WHERE MD5(CONCAT(username, password)) = :userkey";
        $query = $db->prepare($sql);
        $exec = $query->execute(
            array(
                ':userkey' => $userkey,
            )
        );
        $userData = $query->fetchAll();
        
        return count($userData) ? true : false;
    }

}

