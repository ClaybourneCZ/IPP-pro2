<?php

namespace IPP\Student\inst;

use IPP\Core\Exception\NotImplementedException;
use IPP\Student\exceptions\FrameAccessException;
use IPP\Student\exceptions\SemanticException;
use IPP\Student\VarLit;
use IPP\Student\Stack;

class DEFVARCLASS {
    
    /**
     * @param int $instPoint
     * @param array<array<string|array<string|array<string>>>> $instArr
     * @param array<mixed> $VarList
     * @param array<string> $GF
     * @param array<string> $TF
     * @param array<string> $LF
     * @param bool $notIncrement
     * @param Stack $stackData
     * @param Stack $stackFrame
     * @param Stack $stackCall
     * @param bool $TFaccess
     * @param bool $LFaccess
     * @param array<array<string|array<string>|null>> $labelMap    
    */
    public static function defvarFunc(  int &$instPoint,array &$instArr, array &$VarList,
                                         array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                         Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                         bool &$TFaccess, bool &$LFaccess, array &$labelMap) : void {
        // FIRST OPERAND
        $VarLit = new VarLit();
        $instruction = $instArr[$instPoint];
        $try = $instruction['args'];
        $tryArr = (array)$try[0];
        
        $name = strval($tryArr['value']);
        $name = (string) $name;
        $split = explode('@', $name);

        // seting new variable
        $VarLit->Name = $split[1];
        $VarLit->Defined = true;
        $VarLit->Inited = false;
         
        // if exists error, else continue definition
        if ((in_array($split[1], $GF)) && ($split[0] == "GF")) {

            throw new SemanticException();

        } elseif ((in_array($split[1], $TF)) && ($split[0] == "TF")) {
            
            throw new SemanticException();

        } elseif ((in_array($split[1], $LF)) && ($split[0] == "LF")) {
            
            throw new SemanticException();

        } elseif ($split[0] == "GF") {
            $VarLit->Where = "GF";
            $VarList[] = $VarLit;
            $GF[] = $VarLit->Name;
            
        } elseif (($split[0] == "TF") && ($TFaccess)) {
            
            $VarLit->Where = "TF";
            $VarList[] = $VarLit;
            $TF[] = $VarLit->Name;

        }elseif (($split[0] == "LF") && ($LFaccess)) {
            
            $VarLit->Where = "LF";
            $VarList[] = $VarLit;
            // extra needed to add value to stack, 
            // if just added to $LF, will be forgoten on inst popframe 
            // this way we pop, add var to $LF 
            // and than push modified $LF back to stack
            $stackFrame->pop();
            $LF[] = $VarLit->Name;
            $stackFrame->push($LF);
            

        } elseif (($split[0] == "TF") && !($TFaccess)) {
            
            throw new FrameAccessException();

        } elseif (($split[0] == "LF") && !($LFaccess)) {
            
            throw new FrameAccessException();

        }            
    }
}