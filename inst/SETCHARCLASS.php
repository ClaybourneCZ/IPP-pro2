<?php

namespace IPP\Student\inst;

use IPP\Core\FileInputReader;
use IPP\Core\StreamWriter;
use IPP\Student\exceptions\OperandTypeException;
use IPP\Student\exceptions\StringOperationException;
use IPP\Student\exceptions\ValueException;
use IPP\Student\exceptions\VariableAccessException;
use IPP\Student\VarLit;
use IPP\Student\Stack;
use IPP\Student\Exists;

class SETCHARCLASS {
    
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
    public static function setcharFunc(  int &$instPoint,array &$instArr, array &$VarList,
                                         array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                         Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                         bool &$TFaccess, bool &$LFaccess, array &$labelMap,
                                         $input, $stdout, $stderr) : void {

        // takes op1 as string(var) and on op2 as int, 
        // into op1 on idex op2 saves first char from op3 (string)
        
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

        //SETCHAR OPs
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
                    if ($Var->Type === "int"){
                        $typeForPar1 = "int";
                        $valForPar1 = $Var->Value;
                    } else {
                        //not correct type
                        throw new OperandTypeException();
                    }
                } elseif (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === false)) {
                    //not inited for and param1
                    throw new ValueException();
                }
            }
        } elseif ($tryArr2['type'] === "int"){
            $typeForPar1 = "int";
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
                    if ($Var->Type === "string"){
                        $typeForPar2 = "string";
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
        }elseif ($tryArr3['type'] === "string"){
            $typeForPar2 = "string";
            $valForPar2 = $name3;
        } 

        
        //SETCHAR
        // dest = result;
        foreach ($VarList as $Var) {
            if (($Var->Name === $splitVal[1]) && ($Var->Where === $splitVal[0]) && ($Var->Inited === true)){
                if($Var->Type === "string"){
                    
                    $len = strlen((string)$Var->Value);
                    
                    if ($valForPar1 >= $len) { 
                        
                        //len is larger than string len 
                        throw new StringOperationException();
                    
                    }elseif (($typeForPar1 === "int") && ($typeForPar2 === "string")) {
                        $resString = $Var->Value; 
                        $idx = (int)$valForPar1;
                        $resString = (string)$resString; 
                        $resString[$idx] = ((string)$valForPar2)[0];
                        $Var->Value = $resString;
                        
                    }else{
                        //not correct types 
                        throw new OperandTypeException();
                    }
                                        
                }else{
                    //not correct type
                    throw new OperandTypeException();
                }
            } elseif (($Var->Name === $splitVal[1]) && ($Var->Where === $splitVal[0]) && ($Var->Inited === false)) {
                //len is larger than string len or not inited 58
                throw new ValueException();
            }
        }
    }
}