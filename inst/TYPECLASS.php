<?php

namespace IPP\Student\inst;

use IPP\Core\FileInputReader;
use IPP\Core\StreamWriter;
use IPP\Student\exceptions\OperandTypeException;
use IPP\Student\exceptions\VariableAccessException;
use IPP\Student\VarLit;
use IPP\Student\Stack;
use IPP\Student\Exists;


class TYPECLASS {
    
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
    public static function typeFunc(  int &$instPoint,array &$instArr, array &$VarList,
                                         array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                         Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                         bool &$TFaccess, bool &$LFaccess, array &$labelMap,
                                         $input, $stdout, $stderr) : void {

        // op1 as var, op2 as symb
        // save string into op1 that refers type of op2
        
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

        //TYPE OP
        $tryArr2 = (array)$try[1];
        $name2 = strval($tryArr2['value']);

        $typeForPar1 = "smth";
        
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
                    } elseif ($Var->Type === "int"){
                        $typeForPar1 = "int";
                    } elseif ($Var->Type === "float"){
                        $typeForPar1 = "float";
                    } elseif ($Var->Type === "bool"){
                        $typeForPar1 = "bool";
                    } elseif ($Var->Type === "nil"){
                        $typeForPar1 = "";
                    }  else {
                        //not correct type
                        throw new OperandTypeException();
                    }
                } elseif (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === false)) {
                    //not inited for and param1, so empty string here
                    $typeForPar1 = "";
                }
            }
        } elseif ($tryArr2['type'] === "string"){
            $typeForPar1 = "string";
        } elseif ($tryArr2['type'] === "int"){
            $typeForPar1 = "int";
        } elseif ($tryArr2['type'] === "float"){
            $typeForPar1 = "float";
        } elseif ($tryArr2['type'] === "bool"){
            $typeForPar1 = "bool";
        }  elseif ($tryArr2['type'] === "nil"){
            $typeForPar1 = "";
        }  else {
            //not correct type
            throw new OperandTypeException();
        } 
        
        //type 
        // dest = result;
        foreach ($VarList as $Var) {
            if (($Var->Name === $splitVal[1]) && ($Var->Where === $splitVal[0]) && ($Var->Inited === true)){
                $Var->Type = "string";
                $Var->Value = $typeForPar1;
            } elseif (($Var->Name === $splitVal[1]) && ($Var->Where === $splitVal[0]) && ($Var->Inited === false)) {
                $Var->Inited = true;
                $Var->Value = $typeForPar1;
                $Var->Type = "string";
            }
        }
    }
}