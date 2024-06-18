<?php

namespace IPP\Student\inst;

use IPP\Student\exceptions\OperandTypeException;
use IPP\Student\exceptions\ValueException;
use IPP\Student\exceptions\VariableAccessException;
use IPP\Student\VarLit;
use IPP\Student\Stack;
use IPP\Student\Exists;

class EQCLASS {
    
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
    public static function eqFunc(  int &$instPoint,array &$instArr, array &$VarList,
                                         array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                         Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                         bool &$TFaccess, bool &$LFaccess, array &$labelMap) : void {
        
        // EQ, op2 equal op3 => save bool res in op1
        
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

        //EQ OPs (both has to be same type)
        $tryArr2 = (array)$try[1];
        $name2 = strval($tryArr2['value']);
        $tryArr3 = (array)$try[2];
        $name3 = strval($tryArr3['value']);

        $valForEq1 = 0;
        $typeForEq1 = "smth";
        $valForEq2 = 0;
        $typeForEq2 = "smth";
        
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
                        $typeForEq1 = "int";
                        $valForEq1 = $Var->Value;
                    } elseif ($Var->Type === "string"){
                        $typeForEq1 = "string";
                        $valForEq1 = $Var->Value;
                    } elseif ($Var->Type === "float"){
                        $typeForEq1 = "float";
                        $valForEq1 = $Var->Value;
                    } elseif ($Var->Type === "bool"){
                        $typeForEq1 = "bool";
                        $valForEq1 = $Var->Value;
                    } else {
                        //not correct type
                        throw new OperandTypeException();
                    }
                } elseif (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === false)) {
                    //not inited for eq param1
                    throw new ValueException();
                }
            }
        } elseif ($tryArr2['type'] === "int"){
            $typeForEq1 = "int";
            $valForEq1 = $name2;
        } elseif ($tryArr2['type'] === "float"){
            $typeForEq1 = "float";
            $valForEq1 = $name2;
        } elseif ($tryArr2['type'] === "string"){
            $typeForEq1 = "string";
            $valForEq1 = $name2;
        } elseif ($tryArr2['type'] === "bool"){
            $typeForEq1 = "bool";
            $valForEq1 = $name2;
        } elseif ($tryArr2['type'] === "nil"){
            $typeForEq1 = "nil";
            $valForEq1 = $name2;
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
                        $typeForEq2 = "int";
                        $valForEq2 = $Var->Value;
                    } elseif ($Var->Type === "string"){
                        $typeForEq2 = "string";
                        $valForEq2 = $Var->Value;
                    } elseif ($Var->Type === "float"){
                        $typeForEq2 = "float";
                        $valForEq2 = $Var->Value;
                    } elseif ($Var->Type === "bool"){
                        $typeForEq2 = "bool";
                        $valForEq2 = $Var->Value;
                    } else {
                        //not correct type
                        throw new OperandTypeException();
                    }
                } elseif (($Var->Name === $split2[1]) && ($Var->Where === $split2[0]) && ($Var->Inited === false)) {
                    //not inited for eq param2
                    throw new ValueException();
                }
            }
        } elseif ($tryArr3['type'] === "int"){
            $typeForEq2 = "int";
            $valForEq2 = $name3;
        } elseif ($tryArr3['type'] === "float"){
            $typeForEq2 = "float";
            $valForEq2 = $name3;
        } elseif ($tryArr3['type'] === "string"){
            $typeForEq2 = "string";
            $valForEq2 = $name3;
        } elseif ($tryArr3['type'] === "bool"){
            $typeForEq2 = "bool";
            $valForEq2 = $name3;
        } elseif ($tryArr3['type'] === "nil"){
            $typeForEq2 = "nil";
            $valForEq2 = $name3;
        }
        
        // EQ === 
        if (($typeForEq1 === "int") && ($typeForEq2 === "int")) {
            $resultEq = (int)$valForEq1 === (int)$valForEq2;
        } elseif (($typeForEq1 === "bool") && ($typeForEq2 === "bool")) {
            $resultEq = $valForEq1 === $valForEq2;
        } elseif (($typeForEq1 === "float") && ($typeForEq2 === "float")) {
            $resultEq = (float)$valForEq1 === (float)$valForEq2;
        } elseif (($typeForEq1 === "string") && ($typeForEq2 === "string")) {
            $resultEq = (string)$valForEq1 === (string)$valForEq2;
        } elseif (($typeForEq1 === "nil") || ($typeForEq2 === "nil")) {
            //so if there is nil == nil it true, otherwise false
            $resultEq = (string)$valForEq1 === (string)$valForEq2; 
        }else{
            //not correct types 
            throw new OperandTypeException();
        }

        // dest = result;
        foreach ($VarList as $Var) {
            if (($Var->Name === $splitVal[1]) && ($Var->Where === $splitVal[0]) && ($Var->Inited === true)){
                $Var->Type = "bool";
                $Var->Value = $resultEq;
            } elseif (($Var->Name === $splitVal[1]) && ($Var->Where === $splitVal[0]) && ($Var->Inited === false)) {
                $Var->Inited = true;
                $Var->Value = $resultEq;
                $Var->Type = "bool";
            }
        }
    }
}