<?php

namespace IPP\Student;

use IPP\Student\VarLit;

class Exists {
    
    /**
    * @param string $str
    * @param array<string> $GF
    * @param array<string> $TF
    * @param array<string> $LF          
    */
    public static function findVar(string &$str, array &$GF, array &$TF, array &$LF) : bool {
        
        $split = explode("@",$str);
        //echo "IN EXISTS:\n";
        //echo "SPLIT: $split[0]\n";
        //echo "SPLIT: $split[1]\n";
        //print_r($GF);
        //print_r($TF);
        //print_r($LF);
        $found = false;
        if ((in_array($split[1], $GF)) && ($split[0] == "GF")) {

            $found = true;

        } elseif ((in_array($split[1], $TF)) && ($split[0] == "TF")) {
            
            $found = true;

        } elseif ((in_array($split[1], $LF)) && ($split[0] == "LF")) {
            
            $found = true;

        }

        return $found;
    }

    /**
    * @param string $str
    * @param array<VarLit> $arrVarLit         
    */
    public static function removeVar(string &$str, array &$arrVarLit) : void {
        $arrVarLit = array_filter($arrVarLit, fn($Var) => $Var->Name !== $str);
    }    


}