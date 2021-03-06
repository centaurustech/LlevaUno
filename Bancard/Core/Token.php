<?php

namespace LlevaUno\Bancard\Core;

/**
 *
 * Token class for construction and handling tokens.
 *   
 **/

class Token
{
    private $type;
    private $private_key;
    private $shop_process_id;
    private $data;
    private $unhashed_string = "";
    private $hash;
    
    /**
     *
     * Set data and hash.
     *
     **/

    private function __construct($type, $shop_process_id, $data)
    {
        $this->type = $type;
        $this->getPrivateKey();
        $this->shop_process_id = $shop_process_id;
        $this->data = $data;
        $this->make();
        $this->hash();
    }
    
    /**
     *
     * Get configured private key.
     *
     **/
     
    private function getPrivateKey()
    {
        $this->private_key = Config::get('private_key');
    }
    
    /**
     *
     * Construct token string for given operation type.
     *
     **/

    private function make()
    {
        if ($this->type == "pre_authorization" or $this->type == "single_buy") {
            $this->unhashed_string .= $this->private_key;
            $this->unhashed_string .= $this->shop_process_id;
            $this->unhashed_string .= $this->data['amount'];
            $this->unhashed_string .= $this->data['currency'];
        }

        if ($this->type == "pre_authorization_rollback" or $this->type == "single_buy_rollback") {
            $this->unhashed_string .= $this->private_key;
            $this->unhashed_string .= $this->shop_process_id;
            $this->unhashed_string .= "rollback";
            $this->unhashed_string .= "0.00";
        }

        if ($this->type == "pre_authorization_confirm") {
            $this->unhashed_string .= $this->private_key;
            $this->unhashed_string .= $this->shop_process_id;
            $this->unhashed_string .= "pre-authorization-confirm";
        }

        if ($this->type == "pre_authorization_abort") {
            $this->unhashed_string .= $this->private_key;
            $this->unhashed_string .= $this->shop_process_id;
            $this->unhashed_string .= "pre-authorization-abort";
        }
        
        if ($this->type == "pre_authorization_rollback_confirm") {
            $this->unhashed_string .= $this->private_key;
            $this->unhashed_string .= $this->shop_process_id;
            $this->unhashed_string .= "rollback-confirm";
            $this->unhashed_string .= "0.00";
        }

        if ($this->type == "pre_authorization_client_confirm") {
            $this->unhashed_string .= $this->private_key;
            $this->unhashed_string .= $this->shop_process_id;
            $this->unhashed_string .= "pre-authorization-client-confirm";
            $this->unhashed_string .= $this->data['amount'];
            $this->unhashed_string .= $this->data['currency'];
        }
    }
    
    /**
     *
     * MD5 hash of constructed hash.
     *
     **/

    private function hash()
    {
        $this->hash = md5($this->unhashed_string);
    }
    
    /**
     *
     * Create and return hash object.
     *
     * @return Token object
     *
     **/

    public static function create($type, $shop_process_id, $data = array())
    {
        $self = new self($type, $shop_process_id, $data);
        return $self;
    }
    
    /**
     *
     * Return hash string.
     *
     * @return string
     *
     **/

    public function get()
    {
        return $this->hash;
    }
}
