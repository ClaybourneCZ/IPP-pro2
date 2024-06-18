<?php

namespace IPP\Student\inst;

use IPP\Student\exceptions\OperandTypeException;
use IPP\Student\exceptions\StringOperationException;
use IPP\Student\exceptions\ValueException;
use IPP\Student\exceptions\VariableAccessException;
use IPP\Student\VarLit;
use IPP\Student\Stack;
use IPP\Student\Exists;

class STRI2INTCLASS {
    
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
    public static function stri2intFunc(  int &$instPoint,array &$instArr, array &$VarList,
                                         array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                         Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                         bool &$TFaccess, bool &$LFaccess, array &$labelMap) : void {
        
        // takes char from string op2 on index op3, 
        // converts char to ordinal value and saves as int to op1

        //FIRST OPERAND
        $instruction = $instArr[$instPoint];
        $try = $instruction['args'];
        $tryArr = (array)$try[0];
        $value = $tryArr['value'];
        $type = $tryArr['type'];
        
        $splitVal = explode("@", $value);
        $found = Exists::findVar($value, $GF, $TF, $LF);
        if (!$found) {
            //dest does not exist
            throw new VariableAccessException();
        }

        //STRI2CHAR OPs (has to be type string, int)
        $tryArr2 = (array)$try[1];
        $name2 = strval($tryArr2['value']);
        $tryArr3 = (array)$try[2];
        $name3 = strval($tryArr3['value']);      
        

        $valForPar1 = 0;
        $typeForPar1 = "smth";
        $valForPar2 = 0;
        $typeForPar2 = "smth";


        if ($tryArr2['type'] === "var") {
            $split1 = explode("@", $name2);
            $found2 = Exists::findVar($name2, $GF, $TF, $LF);
            if (!$found2) {
                //src1 does not exist
                throw new VariableAccessException();
            }            
            foreach ($VarList as $Var) {
                if (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === true)){
                    if ($Var->Type === "string"){
                        $typeForPar1 = "string";
                        $valForPar1 = $Var->Value;
                    } else {
                        //not correct type
                        throw new OperandTypeException();
                    }
                } elseif (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === false)) {
                    //not inited for or param1
                    throw new ValueException();
                }
            }
        } elseif ($tryArr2['type'] === "string"){
            $typeForPar1 = "string";
            $valForPar1 = $name2;
            
        } 

        if ($tryArr3['type'] === "var") {
            $split2 = explode("@", $name3);
            $found3 = Exists::findVar($name3, $GF, $TF, $LF);
            if (!$found3) {
                //src2 does not exist
                throw new VariableAccessException();
            }             
            foreach ($VarList as $Var) {
                if (($Var->Name === $split2[1]) && ($Var->Where === $split2[0]) && ($Var->Inited === true)){
                    if ($Var->Type === "int"){
                        $typeForPar2 = "int";
                        $valForPar2 = $Var->Value;
                    } else {
                        //not correct type
                        throw new OperandTypeException();
                    }
                } elseif (($Var->Name === $split2[1]) && ($Var->Where === $split2[0]) && ($Var->Inited === false)) {
                    //not inited for and param2
                    throw new ValueException();
                }
            }
        }elseif ($tryArr3['type'] === "int"){
            $typeForPar2 = "int";
            $valForPar2 = $name3;
        }         
        
        //STRI2CHAR
        $valFoRPar1 = (string)$valForPar1;
        if ((int)$valForPar2 >= (int)(mb_strlen((string)$valForPar1))) {
            //index larger than acceptable 
            throw new StringOperationException();
        } elseif (($typeForPar1 === "string") && ($typeForPar2 === "int") ) {
            $charToConvert = mb_substr((string)$valForPar1, (int)$valForPar2, 1, 'UTF-8');
            if ($charToConvert == null){
                //index larger than acceptable 
                throw new StringOperationException();
            }
            $resultPar = mb_ord($charToConvert, "UTF-8");
            if(!$resultPar){
                //not correct types 
                throw new StringOperationException();
            }
        } else {
            //not correct types 
            throw new OperandTypeException();
        } 

        // dest = result;
        foreach ($VarList as $Var) {
            if (($Var->Name === $splitVal[1]) && ($Var->Where === $splitVal[0]) && ($Var->Inited === true)){
                $Var->Type = "int";
                $Var->Value = $resultPar;
            } elseif (($Var->Name === $splitVal[1]) && ($Var->Where === $splitVal[0]) && ($Var->Inited === false)) {
                $Var->Inited = true;
                $Var->Value = $resultPar;
                $Var->Type = "int";
            }
        }        
    }
}