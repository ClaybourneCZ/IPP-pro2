<?php

namespace IPP\Student\inst;

use IPP\Student\exceptions\OperandTypeException;
use IPP\Student\exceptions\ValueException;
use IPP\Student\exceptions\VariableAccessException;
use IPP\Student\VarLit;
use IPP\Student\Stack;
use IPP\Student\Exists;

class POPSCLASS {
    
    /**
     * @param int $instPoint
     * @param array<array<string|array<string|array<string>>>> $instArr
     * @param array<VarLit> $VarList
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
    public static function popsFunc(  int &$instPoint,array &$instArr, array &$VarList,
                                         array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                         Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                         bool &$TFaccess, bool &$LFaccess, array &$labelMap) : void {
        
        //FIRST OPERAND
        $instruction = $instArr[$instPoint];
        $try = $instruction['args'];
        $tryArr = (array)$try[0];
        $value = $tryArr['value'];
        $type = $tryArr['type'];

        $split = explode("@", $value);
        $found = Exists::findVar($value, $GF, $TF, $LF);
        if(!$found){
            // not found, doesnt have access or not exist
            throw new VariableAccessException();
        }

        //checking variable
        foreach ($VarList as $Var) {
                
            if (($Var->Name === $split[1]) && ($Var->Where === $split[0])) {
                $popedData = $stackData->pop();
                $splitPopped = "nic";
                if (is_string($popedData)){
                    $splitPopped = explode("@", $popedData);
                }
                if($splitPopped[0] === "string"){
                    $Var->Inited = true;
                    $Var->Value = (string)$splitPopped[1];
                    $Var->Type = "string";
                } elseif ($splitPopped[0] === "int"){
                    $Var->Inited = true;
                    $Var->Value =  (int)$splitPopped[1];
                    $Var->Type = "int";
                } elseif ($splitPopped[0] === "bool"){
                    $Var->Inited = true;
                    $Var->Value = (bool)$splitPopped[1];
                    $Var->Type = "bool";
                } elseif ($splitPopped[0] === "float"){
                    $Var->Inited = true;
                    $Var->Value = (float)$splitPopped[1];
                    $Var->Type = "float";
                } elseif ($splitPopped[0] === "nil"){
                    $Var->Inited = true;
                    $Var->Value = (string)$splitPopped[1];
                    $Var->Type = "nil";
                }  elseif ( $popedData === null ) {
                    
                    // 56, empty stackData
                    throw new ValueException();
                }

            }
        }
    }
}