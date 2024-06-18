<?php

namespace IPP\Student\inst;

use IPP\Student\exceptions\OperandTypeException;
use IPP\Student\exceptions\ValueException;
use IPP\Student\exceptions\VariableAccessException;
use IPP\Student\VarLit;
use IPP\Student\Stack;
use IPP\Student\Exists;

class ANDCLASS {
    
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
    public static function andFunc(  int &$instPoint,array &$instArr, array &$VarList,
                                         array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                         Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                         bool &$TFaccess, bool &$LFaccess, array &$labelMap) : void {

        // AND, op2 logicAnd op3 => save res bool in op1                                            
        
        // FIRST OPERAND
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

        //AND OPs (has to be type bool)
        $tryArr2 = (array)$try[1];
        $name2 = strval($tryArr2['value']);
        $tryArr3 = (array)$try[2];
        $name3 = strval($tryArr3['value']);

        $valForAnd1 = 0;
        $typeForAnd1 = "smth";
        $valForAnd2 = 0;
        $typeForAnd2 = "smth";
        
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
                        $typeForAnd1 = "bool";
                        $valForAnd1 = $Var->Value;
                    } else {
                        //not correct type
                        throw new OperandTypeException();
                    }
                } elseif (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === false)) {
                    //not inited for and param1
                    throw new ValueException();
                }
            }
        } elseif ($tryArr2['type'] === "bool"){
            $typeForAnd1 = "bool";
            $valForAnd1 = $name2;
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
                        $typeForAnd2 = "bool";
                        $valForAnd2 = $Var->Value;
                    } else {
                        //not correct type
                        throw new OperandTypeException();
                    }
                } elseif (($Var->Name === $split2[1]) && ($Var->Where === $split2[0]) && ($Var->Inited === false)) {
                    //not inited for and param2
                    throw new ValueException();
                }
            }
        }elseif ($tryArr3['type'] === "bool"){
            $typeForAnd2 = "bool";
            $valForAnd2 = $name3;
        } 
        
        //logic and 
        if (($typeForAnd1 === "bool") && ($typeForAnd2 === "bool")) {
            $resultAnd = (bool)$valForAnd1 && (bool)$valForAnd2;
        }else{
            //not correct types 
            throw new OperandTypeException();
        }

        // dest = result;
        foreach ($VarList as $Var) {
            if (($Var->Name === $splitVal[1]) && ($Var->Where === $splitVal[0]) && ($Var->Inited === true)){
                $Var->Type = "bool";
                $Var->Value = $resultAnd;
            } elseif (($Var->Name === $splitVal[1]) && ($Var->Where === $splitVal[0]) && ($Var->Inited === false)) {
                $Var->Inited = true;
                $Var->Value = $resultAnd;
                $Var->Type = "bool";
            }
        }
    }
}