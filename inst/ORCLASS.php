<?php

namespace IPP\Student\inst;

use IPP\Student\exceptions\OperandTypeException;
use IPP\Student\exceptions\ValueException;
use IPP\Student\exceptions\VariableAccessException;
use IPP\Student\VarLit;
use IPP\Student\Stack;
use IPP\Student\Exists;
use ValueError;

class ORCLASS {
    
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
    public static function orFunc(  int &$instPoint,array &$instArr, array &$VarList,
                                         array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                         Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                         bool &$TFaccess, bool &$LFaccess, array &$labelMap) : void {
        
        // OR, op2 logicOr op3 => save res as bool in op1
        
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

        //OR OPs (has to be type bool)
        $tryArr2 = (array)$try[1];
        $name2 = strval($tryArr2['value']);        
        $tryArr3 = (array)$try[2];
        $name3 = strval($tryArr3['value']);

        $valForOr1 = 0;
        $typeForOr1 = "smth";
        $valForOr2 = 0;
        $typeForOr2 = "smth";
        
        if ($tryArr2['type'] === "var") {
            $split1 = explode("@", $name2);
            $found2 = Exists::findVar($name2, $GF, $TF, $LF);
            if (!$found2) {
                //src1 does not exist
                throw new VariableAccessException();
            }            
            foreach ($VarList as $Var) {
                if (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === true)){
                    if ($Var->Type === "bool"){
                        $typeForOr1 = "bool";
                        $valForOr1 = $Var->Value;
                    } else {
                        //not correct type
                        throw new OperandTypeException();
                    }
                } elseif (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === false)) {
                    //not inited for or param1
                    throw new ValueException();
                }
            }
        } elseif ($tryArr2['type'] === "bool"){
            $typeForOr1 = "bool";
            $valForOr1 = $name2;
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
                    if ($Var->Type === "bool"){
                        $typeForOr2 = "bool";
                        $valForOr2 = $Var->Value;
                    } else {
                        //not correct type
                        throw new OperandTypeException();
                    }
                } elseif (($Var->Name === $split2[1]) && ($Var->Where === $split2[0]) && ($Var->Inited === false)) {
                    //not inited for or param2
                    throw new ValueError();
                }
            }
        }elseif ($tryArr3['type'] === "bool"){
            $typeForOr2 = "bool";
            $valForOr2 = $name3;
        } 
        
        //logic or 
        if (($typeForOr1 === "bool") && ($typeForOr2 === "bool")) {
            $resultOr = $valForOr1 || $valForOr2;
        }else{
            //not correct types 
            throw new OperandTypeException();
        } 

        // dest = result;
        foreach ($VarList as $Var) {
            if (($Var->Name === $splitVal[1]) && ($Var->Where === $splitVal[0]) && ($Var->Inited === true)){
                $Var->Type = "bool";
                $Var->Value = $resultOr;
            } elseif (($Var->Name === $splitVal[1]) && ($Var->Where === $splitVal[0]) && ($Var->Inited === false)) {
                $Var->Inited = true;
                $Var->Value = $resultOr;
                $Var->Type = "bool";
            }
        }
    }
}