<?php

/*
Sample Code FTMGC
 */

class Pools_Abstract {
    
    protected $_fileHandler;
    
    public function __construct($params) {
        $this->_apiURL = $params['apiurl'];
    }
    
}