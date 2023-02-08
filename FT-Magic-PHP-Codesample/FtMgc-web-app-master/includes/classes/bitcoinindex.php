<?php
/*
Sample Code FTMGC
 */
class BitcoinIndex {

    // Settings
    protected $_url = 'https://bitcoinindex.es/api';
    
    public function convert($to, $from) {
        $fileHandler = new FileHandler('fiat/bitcoinindex/' . strtolower($to) . '_' . strtolower($from) . '.json');
        
        if ($GLOBALS['cached'] == false || $fileHandler->lastTimeModified() >= 3600) { // updates every 1 minute
            $data = array();
            
            $data = curlCall($this->_url . '/v0.1/conversions/' . strtolower($from) . '/' . strtolower($to)); // /v0.1/conversions/:from/:to
            
            $fileHandler->write(json_encode($data));
            return $data;
        }
        
        return json_decode($fileHandler->read(), true);
    }
    

}