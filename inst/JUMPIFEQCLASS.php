<?php

namespace IPP\Student\inst;

use IPP\Core\FileInputReader;
use IPP\Core\StreamWriter;
use IPP\Student\exceptions\OperandTypeException;
use IPP\Student\exceptions\ValueException;
use IPP\Student\exceptions\VariableAccessException;
use IPP\Student\VarLit;
use IPP\Student\Stack;
use IPP\Student\Exists;

class JUMPIFEQCLASS {
    
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
     * @param FileInputReader $input    
     * @param StreamWriter $stdout
     * @param StreamWriter $stderr     
    */
    public static function jumpifeqFunc(  int &$instPoint,array &$instArr, array &$VarList,
                                         array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                         Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                         bool &$TFaccess, bool &$LFaccess, array &$labelMap,
                                         $input, $stdout, $stderr) : void {

        // jumps to op1 as label if op2 and op3 are same types and values

        //FIRST OPERAND
        $instruction = $instArr[$instPoint];
        $try = $instruction['args'];
        $tryArr = (array)$try[0];
        $lookForLabel = $tryArr['value'];

        // EQ OPs
        $tryArr2 = (array)$try[1];
        $cond1 = $tryArr2['value'];
        $tryArr3 = (array)$try[2];
        $cond2 = $tryArr3['value'];

        $valForPar1 = 0;
        $typeForPar1 = "smth";
        $valForPar2 = 0;
        $typeForPar2 = "smth";
        
        if ($tryArr2['type'] === "var") {
            $split1 = explode("@", $cond1);
            $found2 = Exists::findVar($cond1, $GF, $TF, $LF);
            if (!$found2) {
                //src1 does not exist
                throw new ValueException();
            }            
            foreach ($VarList as $Var) {
                if (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === true)){
                    if($Var->Type === "int"){
                        $typeForPar1 = "int";
                        $valForPar1 = $Var->Value;
                    } elseif ($Var->Type === "string"){
                        $typeForPar1 = "string";
                        $valForPar1 = $Var->Value;
                    } elseif ($Var->Type === "float"){
                        $typeForPar1 = "float";
                        $valForLt1 = $Var->Value;
                    } elseif ($Var->Type === "bool"){
                        $typeForPar1 = "bool";
                        $valForPar1 = $Var->Value;
                    } elseif ($Var->Type === "nil"){
                        $typeForPar1 = "nil";
                        $valForPar1 = $Var->Value;
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
            $typeForPar1 = "int";
            $valForPar1 = $cond1;
        } elseif ($tryArr2['type'] === "float"){
            $typeForPar1 = "float";
            $valForPar1 = $cond1;
        } elseif ($tryArr2['type'] === "string"){
            $typeForPar1 = "string";
            $valForPar1 = $cond1;
        } elseif ($tryArr2['type'] === "bool"){
            $typeForPar1 = "bool";
            $valForPar1 = $cond1;
        } elseif ($tryArr2['type'] === "nil"){
            $typeForPar1 = "nil";
            $valForPar1 = $cond1;
        }


        if ($tryArr3['type'] === "var") {
            $split2 = explode("@", $cond2);
            $found3 = Exists::findVar($cond2, $GF, $TF, $LF);
            if (!$found3) {
                //src2 does not exist
                throw new VariableAccessException();
            }             
            foreach ($VarList as $Var) {
                if (($Var->Name === $split2[1]) && ($Var->Where === $split2[0]) && ($Var->Inited === true)){
                    if($Var->Type === "int"){
                        $typeForPar2 = "int";
                        $valForPar2 = $Var->Value;
                    } elseif ($Var->Type === "string"){
                        $typeForPar2 = "string";
                        $valForPar2 = $Var->Value;
                    } elseif ($Var->Type === "float"){
                        $typeForPar2 = "float";
                        $valForPar2 = $Var->Value;
                    } elseif ($Var->Type === "bool"){
                        $typeForPar2 = "bool";
                        $valForPar2 = $Var->Value;
                    } elseif ($Var->Type === "nil"){
                        $typeForPar2 = "nil";
                        $valForPar2 = $Var->Value;
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
            $typeForPar2 = "int";
            $valForPar2 = $cond2;
        } elseif ($tryArr3['type'] === "float"){
            $typeForPar2 = "float";
            $valForPar2 = $cond2;
        } elseif ($tryArr3['type'] === "string"){
            $typeForPar2 = "string";
            $valForPar2 = $cond2;
        } elseif ($tryArr3['type'] === "bool"){
            $typeForPar2 = "bool";
            $valForPar2 = $cond2;
        } elseif ($tryArr3['type'] === "nil"){
            $typeForPar2 = "nil";
            $valForPar2 = $cond2;
        }
        
        // EQ === 
        if (($typeForPar1 === "int") && ($typeForPar2 === "int")) {
            $resultPar = (int)$valForPar1 === (int)$valForPar2;
        } elseif (($typeForPar1 === "bool") && ($typeForPar2 === "bool")) {
            $resultPar = $valForPar1 === $valForPar2;
        } elseif (($typeForPar1 === "float") && ($typeForPar2 === "float")) {
            $resultPar = (float)$valForPar1 === (float)$valForPar2;
        } elseif (($typeForPar1 === "string") && ($typeForPar2 === "string")) {
            $resultPar = (string)$valForPar1 === (string)$valForPar2;
        } elseif (($typeForPar1 === "nil") || ($typeForPar2 === "nil")) {
            //so if there is nil == nil it true, otherwise false
            $resultPar = (string)$valForPar1 === (string)$valForPar2; 
        }else{
            //not correct types 
            throw new OperandTypeException();
        }

        // dest = label;
        if ($resultPar) {
            foreach ( $labelMap as $label ){
                if($label['label'] === $lookForLabel){

                    $instPoint = (int)($label['pos']) - 1;

                }
            }
        }        
    }
}