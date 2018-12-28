<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Persona
 *
 * @author xhuix
 */
class Persona {
    //put your code here
    
    private $nom;
   
    public function __construct1($nom){
        $this->nom=$nom;
        
    }
    
   
    public  static function socPersona(){
        
        echo 'soc pesona';
        
    }

    

    public function getNom(){
        
       return $this->nom; 
    }
    
    
    
    public function setNom($nom){
        
        $this->nom=$nom;
        
    }
    
}
