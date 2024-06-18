<?php

namespace IPP\Student\inst;

use IPP\Student\exceptions\OperandTypeException;
use IPP\Student\exceptions\OperandValueException;
use IPP\Student\exceptions\ValueException;
use IPP\Student\exceptions\VariableAccessException;
use IPP\Student\VarLit;
use IPP\Student\Stack;
use IPP\Student\Exists;

class GTCLASS {
    
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
    public static function gtFunc(  int &$instPoint,array &$instArr, array &$VarList,
                                         array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                         Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                         bool &$TFaccess, bool &$LFaccess, array &$labelMap) : void {
        
        // GT, op2 greater than op3 => save bool res in op1 
        
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

        //GT OPs (both has to be same type)
        $tryArr2 = (array)$try[1];
        $name2 = strval($tryArr2['value']);
        $tryArr3 = (array)$try[2];
        $name3 = strval($tryArr3['value']);

        $valForGt1 = 0;
        $typeForGt1 = "smth";
        $valForGt2 = 0;
        $typeForGt2 = "smth";
        
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
                        $typeForGt1 = "int";
                        $valForGt1 = $Var->Value;
                    } elseif ($Var->Type === "string"){
                        $typeForGt1 = "string";
                        $valForGt1 = $Var->Value;
                    } elseif ($Var->Type === "float"){
                        $typeForGt1 = "float";
                        $valForGt1 = $Var->Value;
                    } elseif ($Var->Type === "bool"){
                        $typeForGt1 = "bool";
                        $valForGt1 = $Var->Value;
                    } else {
                        //not correct type
                        throw new OperandValueException();
                    }
                } elseif (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === false)) {
                    //not inited for gt param1
                    throw new ValueException();
                }
            }
        } elseif ($tryArr2['type'] === "int"){
            $typeForGt1 = "int";
            $valForGt1 = $name2;
        } elseif ($tryArr2['type'] === "float"){
            $typeForGt1 = "float";
            $valForGt1 = $name2;
        } elseif ($tryArr2['type'] === "string"){
            $typeForGt1 = "string";
            $valForGt1 = $name2;
        } elseif ($tryArr2['type'] === "bool"){
            $typeForGt1 = "bool";
            $valForGt1 = $name2;
        } else {
            //not correct type
            throw new OperandValueException();
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
                        $typeForGt2 = "int";
                        $valForGt2 = $Var->Value;
                    } elseif ($Var->Type === "string"){
                        $typeForGt2 = "string";
                        $valForGt2 = $Var->Value;
                    } elseif ($Var->Type === "float"){
                        $typeForGt2 = "float";
                        $valForGt2 = $Var->Value;
                    } elseif ($Var->Type === "bool"){
                        $typeForGt2 = "bool";
                        $valForGt2 = $Var->Value;
                    } else {
                        //not correct type
                        throw new OperandTypeException();
                    }
                } elseif (($Var->Name === $split2[1]) && ($Var->Where === $split2[0]) && ($Var->Inited === false)) {
                    //not inited for gt param2
                    throw new ValueException();
                }
            }
        } elseif ($tryArr3['type'] === "int"){
            $typeForGt2 = "int";
            $valForGt2 = $name3;
        } elseif ($tryArr3['type'] === "float"){
            $typeForGt2 = "float";
            $valForGt2 = $name3;
        } elseif ($tryArr3['type'] === "string"){
            $typeForGt2 = "string";
            $valForGt2 = $name3;
        } elseif ($tryArr3['type'] === "bool"){
            $typeForGt2 = "bool";
            $valForGt2 = $name3;
        } else {
            //not correct type
            throw new OperandTypeException();
        }
        
        //GREATERE THAN > 
        if (($typeForGt1 === "int") && ($typeForGt2 === "int")) {
            $resultGt = (int)$valForGt1 > (int)$valForGt2;
        } elseif (($typeForGt1 === "bool") && ($typeForGt2 === "bool")) { 
            $resultGt = $valForGt1 > $valForGt2;
        } elseif (($typeForGt1 === "float") && ($typeForGt2 === "float")) {
            $resultGt = (float)$valForGt1 > (float)$valForGt2;
        } elseif (($typeForGt1 === "string") && ($typeForGt2 === "string")) {
            $resultGt = (string)$valForGt1 > (string)$valForGt2;
        }else{
            //not correct types 
            throw new OperandTypeException();
        }

        // dest = result;
        foreach ($VarList as $Var) {
            if (($Var->Name === $splitVal[1]) && ($Var->Where === $splitVal[0]) && ($Var->Inited === true)){
                $Var->Type = "bool";
                $Var->Value = $resultGt;
            } elseif (($Var->Name === $splitVal[1]) && ($Var->Where === $splitVal[0]) && ($Var->Inited === false)) {
                $Var->Inited = true;
                $Var->Value = $resultGt;
                $Var->Type = "bool";
            }
        }
    }
}