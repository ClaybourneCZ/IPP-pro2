<?php

namespace IPP\Student\inst;

use IPP\Student\exceptions\OperandTypeException;
use IPP\Student\exceptions\ValueException;
use IPP\Student\exceptions\VariableAccessException;
use IPP\Student\VarLit;
use IPP\Student\Stack;
use IPP\Student\Exists;

class LTCLASS {
    
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
    public static function ltFunc(  int &$instPoint,array &$instArr, array &$VarList,
                                         array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                         Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                         bool &$TFaccess, bool &$LFaccess, array &$labelMap) : void {

        // LT, op2 les than op3 => save bool in op1 
                                            
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

        //LT OPs (both has to be same type)
        $tryArr2 = (array)$try[1];
        $name2 = strval($tryArr2['value']);
        
        $tryArr3 = (array)$try[2];
        $name3 = strval($tryArr3['value']);

        $valForLt1 = 0;
        $typeForLt1 = "smth";
        $valForLt2 = 0;
        $typeForLt2 = "smth";
        
        if ($tryArr2['type'] === "var") {
            $split1 = explode("@", $name2);
            $found2 = Exists::findVar($name2, $GF, $TF, $LF);
            if (!$found2) {
                //src1 does not exist
                throw new VariableAccessException();
            }            
            foreach ($VarList as $Var) {
                if (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === true)){
                    if($Var->Type === "int"){
                        $typeForLt1 = "int";
                        $valForLt1 = $Var->Value;
                    } elseif ($Var->Type === "string"){
                        $typeForLt1 = "string";
                        $valForLt1 = $Var->Value;
                    } elseif ($Var->Type === "float"){
                        $typeForLt1 = "float";
                        $valForLt1 = $Var->Value;
                    } elseif ($Var->Type === "bool"){
                        $typeForLt1 = "bool";
                        $valForLt1 = $Var->Value;
                    } else {
                        //not correct type
                        throw new OperandTypeException();
                    }
                } elseif (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === false)) {
                    //not inited for lt param1
                    throw new ValueException();
                }
            }
        } elseif ($tryArr2['type'] === "int"){
            $typeForLt1 = "int";
            $valForLt1 = $name2;
        } elseif ($tryArr2['type'] === "float"){
            $typeForLt1 = "float";
            $valForLt1 = $name2;
        } elseif ($tryArr2['type'] === "string"){
            $typeForLt1 = "string";
            $valForLt1 = $name2;
        } elseif ($tryArr2['type'] === "bool"){
            $typeForLt1 = "bool";
            $valForLt1 = $name2;
        } else {
            //not correct type
            throw new OperandTypeException();
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
                    if($Var->Type === "int"){
                        $typeForLt2 = "int";
                        $valForLt2 = $Var->Value;
                    } elseif ($Var->Type === "string"){
                        $typeForLt2 = "string";
                        $valForLt2 = $Var->Value;
                    } elseif ($Var->Type === "float"){
                        $typeForLt2 = "float";
                        $valForLt2 = $Var->Value;
                    } elseif ($Var->Type === "bool"){
                        $typeForLt2 = "bool";
                        $valForLt2 = $Var->Value;
                    } else {
                        //not correct type
                        throw new OperandTypeException();
                    }
                } elseif (($Var->Name === $split2[1]) && ($Var->Where === $split2[0]) && ($Var->Inited === false)) {
                    //not inited for lt param2
                    throw new ValueException();
                }
            }
        } elseif ($tryArr3['type'] === "int"){
            $typeForLt2 = "int";
            $valForLt2 = $name3;
        } elseif ($tryArr3['type'] === "float"){
            $typeForLt2 = "float";
            $valForLt2 = $name3;
        } elseif ($tryArr3['type'] === "string"){
            $typeForLt2 = "string";
            $valForLt2 = $name3;
        } elseif ($tryArr3['type'] === "bool"){
            $typeForLt2 = "bool";
            $valForLt2 = $name3;
        } else {
            //not correct type
            throw new OperandTypeException();
        }
        
        //LESS THAN < 
        if (($typeForLt1 === "int") && ($typeForLt2 === "int")) {
            $resultLt = (int)$valForLt1 < (int)$valForLt2;
        } elseif (($typeForLt1 === "bool") && ($typeForLt2 === "bool")) {
            $resultLt = $valForLt1 < $valForLt2;
        } elseif (($typeForLt1 === "float") && ($typeForLt2 === "float")) {
            $resultLt = (float)$valForLt1 < (float)$valForLt2;
        } elseif (($typeForLt1 === "string") && ($typeForLt2 === "string")) {
            $resultLt = (string)$valForLt1 < (string)$valForLt2;
        }else{
            //not correct types 
            throw new OperandTypeException();
        }

        // dest = result;
        foreach ($VarList as $Var) {
            if (($Var->Name === $splitVal[1]) && ($Var->Where === $splitVal[0]) && ($Var->Inited === true)){
                $Var->Type = "bool";
                $Var->Value = $resultLt;
            } elseif (($Var->Name === $splitVal[1]) && ($Var->Where === $splitVal[0]) && ($Var->Inited === false)) {
                $Var->Inited = true;
                $Var->Value = $resultLt;
                $Var->Type = "bool";
            }
        }
    }
}