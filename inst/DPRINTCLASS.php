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

class DPRINTCLASS {
    
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
    public static function dprintFunc(  int &$instPoint,array &$instArr, array &$VarList,
                                         array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                         Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                         bool &$TFaccess, bool &$LFaccess, array &$labelMap,
                                          $input, $stdout, $stderr) : void {
        
        // write value given from op1 to stderr

        //FIRST OPERAND
        $instruction = $instArr[$instPoint];
        $try = $instruction['args'];
        $tryArr = (array)$try[0];
        $value = $tryArr['value'];
        $type = $tryArr['type'];

        $valForPar1 = "smth";
        $typeForPar1 = "smth";

        //WRITE OP 
        if ($type === "var") {
            $split1 = explode("@",$value);
            $found = Exists::findVar($value, $GF, $TF, $LF);
            if (!$found) {
                //src does not exist
                throw new VariableAccessException();
            }            
            foreach ($VarList as $Var) {
                if (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === true)){
                    if ($Var->Type === "bool"){
                        $typeForPar1 = "bool";
                        $valForPar1 = $Var->Value;
                    } elseif ($Var->Type === "int"){
                        $typeForPar1 = "int";
                        $valForPar1 = $Var->Value;
                    } elseif ($Var->Type === "string"){
                        $typeForPar1 = "string";
                        $valForPar1 = $Var->Value;
                    } elseif ($Var->Type === "float"){
                        $typeForPar1 = "float";
                        $valForPar1 = $Var->Value;
                    } elseif ($Var->Type === "nil"){
                        $typeForPar1 = "nil";
                        $valForPar1 = "";
                    } else {
                        //exist
                        throw new OperandTypeException();
                    }
                } elseif (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === false)) {
                    //not inited for and param1
                    throw new ValueException();
                }
            }
        } elseif ($type === "bool"){
            $typeForPar1 = "bool";
            $valForPar1 = $value;
        }  elseif ($type === "int"){
            $typeForPar1 = "int";
            $valForPar1 = $value;
        }  elseif ($type === "string"){
            $typeForPar1 = "string";
            $valForPar1 = $value;
        }   elseif ($type === "float"){
            $typeForPar1 = "float";
            $valForPar1 = $value;
        } elseif ($type === "nil"){
            $typeForPar1 = "nil";
            $valForPar1 = "";
        } 

        //WRITE (DPRINT)
        if($typeForPar1 === "string"){
            $stderr->writeString((string)$valForPar1);
        } elseif($typeForPar1 === "int") {
            $stderr->writeInt((int)$valForPar1);
        } elseif($typeForPar1 === "bool") {
            $stderr->writeBool((bool)$valForPar1);
        } elseif($typeForPar1 === "float") {
            $stderr->writeFloat((float)$valForPar1);
        } elseif($typeForPar1 === "nil") {
            $stderr->writeString((string)$valForPar1);
        } else {
            //in arg of type is unknown word
            throw new OperandTypeException();
        }
    }
}