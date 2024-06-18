<?php

namespace IPP\Student\inst;

use IPP\Student\exceptions\VariableAccessException;
use IPP\Student\VarLit;
use IPP\Student\Stack;
use IPP\Student\Exists;
use IPP\Student\exceptions\ValueException;

class PUSHSCLASS {
    
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
    public static function pushsFunc(  int &$instPoint,array &$instArr, array &$VarList,
                                         array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                         Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                         bool &$TFaccess, bool &$LFaccess, array &$labelMap) : void {

        // FIRST OPERAND
        $instruction = $instArr[$instPoint];
        $try = $instruction['args'];
        $tryArr = (array)$try[0];
        $value = $tryArr['value'];
        $type = $tryArr['type'];
   
        // checking type of operand
        if ($type == "var") {
            $split = explode("@", $value);
            $found = Exists::findVar($value,$GF, $TF, $LF);
            if($found){
                foreach($VarList as $Var){
                    if (($Var->Name === $split[1]) && ($Var->Where === $split[0]) && ($Var->Inited === true)) {
                        if ($Var->Type == "string") {
                            $valToPush = "string@".(string)$Var->Value;
                            $stackData->push((string)$valToPush);
                        } elseif ($Var->Type == "int") {   
                            $valToPush = "int@".(string)$Var->Value;
                            $stackData->push((string)$valToPush);
                        } elseif ($Var->Type == "float") {
                            $valToPush = "float@".(string)$Var->Value;
                            $stackData->push((string)$valToPush);
                        } elseif ($Var->Type == "bool") {
                            $valToPush = "bool@".(string)$Var->Value;
                            $stackData->push((string)$valToPush);
                        } elseif ($Var->Type == "nil") {
                            $valToPush = "nil@".(string)$Var->Value;
                            $stackData->push((string)$valToPush);
                        }                         
                    } elseif (($Var->Name === $split[1]) && ($Var->Where === $split[0]) && ($Var->Inited === false)){
                        //exist but not inited
                        throw new ValueException();
                    }
                }
            } else {
                // not found, doesnt have access or not exist
                throw new VariableAccessException();
            }
        } elseif ($type == "string") {
            $valToPush = "string@".(string)$value;
            $stackData->push((string)$valToPush);
        } elseif ($type == "int") {   
            $valToPush = "int@".(string)$value;
            $stackData->push((string)$valToPush);
        } elseif ($type == "float") {
            $valToPush = "float@".(string)$value;
            $stackData->push((string)$valToPush);
        } elseif ($type == "bool") {
            $valToPush = "bool@".(string)$value;
            $stackData->push((string)$valToPush);
        } elseif ($type == "nil") {
            $valToPush = "nil@".(string)$value;
            $stackData->push((string)$valToPush);
        }
    }
}                                            