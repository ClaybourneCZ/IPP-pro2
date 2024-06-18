<?php

namespace IPP\Student\inst;

use IPP\Core\FileInputReader;
use IPP\Core\StreamWriter;
use IPP\Student\exceptions\OperandTypeException;
use IPP\Student\exceptions\OperandValueException;
use IPP\Student\exceptions\VariableAccessException;
use IPP\Student\VarLit;
use IPP\Student\Stack;
use IPP\Student\Exists;

class READCLASS {
    
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
    public static function readFunc(  int &$instPoint,array &$instArr, array &$VarList,
                                         array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                         Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                         bool &$TFaccess, bool &$LFaccess, array &$labelMap,
                                          $input, $stdout, $stderr) : void {

        // takes input and as type op2 saves it in op1
        
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

        //READ OP 
        $tryArr2 = (array)$try[1];
        $name2 = strval($tryArr2['value']);
        
        $valForPar1 = "smth";
        $typeForPar1 = "smth";
        
        if ($tryArr2['type'] === "type") {
            $valForPar1 = $name2;
            $typeForPar1 = $tryArr2['type'];
        } 

        //READ
        if ($typeForPar1 === "type") {
            if($valForPar1 === "string"){
                $resultPar = $input->readString();
                if($resultPar === null){
                    $resultPar = "nil";
                    $typeForPar1 = "nil";
                    $valForPar1 = "nil";
                }
            } elseif($valForPar1 === "int"){
                $resultPar = $input->readInt();
                if($resultPar === null){
                    $resultPar = "nil";
                    $typeForPar1 = "nil";
                    $valForPar1 = "nil";
                }
            } elseif($valForPar1 === "bool"){
                $resultPar = $input->readBool();
                if($resultPar === null){
                    $resultPar = "nil";
                    $typeForPar1 = "nil";
                    $valForPar1 = "nil";
                }
            } elseif($valForPar1 === "float"){
                $resultPar = $input->readFloat();
                if($resultPar === null){
                    $resultPar = "nil";
                    $typeForPar1 = "nil";
                    $valForPar1 = "nil";
                }
            }else{
                //in arg of type is unknown word
                throw new OperandValueException();
            }
        }else{
            //not correct types 
            throw new OperandTypeException();
        }

        // dest = result;
        foreach ($VarList as $Var) {
            if (($Var->Name === $splitVal[1]) && ($Var->Where === $splitVal[0]) && ($Var->Inited === true)){
                if ($valForPar1 === "string") {
                    $Var->Value = (string)$resultPar;
                    $Var->Type = "string";
                } elseif ($valForPar1 === "int") {
                    $Var->Value = (int)$resultPar;
                    $Var->Type = "int";
                } elseif ($valForPar1 === "bool") {
                    $Var->Value = (bool)$resultPar;
                    $Var->Type = "bool";
                } elseif ($valForPar1 === "float") {
                    $Var->Value = (float)$resultPar;
                    $Var->Type = "float";
                } elseif ($valForPar1 === "nil" ) {
                    $Var->Value = (string)$resultPar;
                    $Var->Type = "nil";
                } 
            } elseif (($Var->Name === $splitVal[1]) && ($Var->Where === $splitVal[0]) && ($Var->Inited === false)) {
                $Var->Inited = true;
                if ($valForPar1 === "string") {
                    $Var->Value = (string)$resultPar;
                    $Var->Type = "string";
                } elseif ($valForPar1 === "int") {
                    $Var->Value = (int)$resultPar;
                    $Var->Type = "int";
                } elseif ($valForPar1 === "bool") {
                    $Var->Value = (bool)$resultPar;
                    $Var->Type = "bool";
                } elseif ($valForPar1 === "float") {
                    $Var->Value = (float)$resultPar;
                    $Var->Type = "float";
                } elseif ($valForPar1 === "nil" ) {
                    $Var->Value = (string)$resultPar;
                    $Var->Type = "nil";
                } 
            }
        }        
    }
}