<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Nakymapohja
 *
 * @author kerkjuk_admin
 */
abstract class Nakymapohja extends Pohja{
    
    private $oliot;
    
    function __construct(){
        parent::__construct();
        $this->oliot = array();
    }

    public function get_oliot(){
        return $this->oliot;
    }
    
    public function set_oliot($oliot){
        $this->oliot = $oliot;
    }


    // Pakkototeutettavat metodit perittäessä.
    /**
     * Palauttaa html-koodin yhdelle oliolle tai vaikkapa kaikille
     * tietokantaosumille (toteutus tarpeen mukaan vaihdellen).
     */
    //public abstract function nayta();
}

?>
