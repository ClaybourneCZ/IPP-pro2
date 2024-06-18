<?php

namespace IPP\Student\inst;

use IPP\Core\FileInputReader;
use IPP\Core\StreamWriter;
use IPP\Student\exceptions\OperandTypeException;
use IPP\Student\exceptions\OperandValueException;
use IPP\Student\exceptions\ValueException;
use IPP\Student\exceptions\VariableAccessException;
use IPP\Student\VarLit;
use IPP\Student\Stack;
use IPP\Student\Exists;

class EXITCLASS {
    
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
     * @param int $ret  
     * @param bool $setcode
    */
    public static function exitFunc(  int &$instPoint,array &$instArr, array &$VarList,
                                         array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                         Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                         bool &$TFaccess, bool &$LFaccess, array &$labelMap,
                                         $input, $stdout, $stderr, int &$ret, bool &$setcode) : void {

        // exits the program with coresporing value in op1 as int

        //FIRST OPERAND
        $instruction = $instArr[$instPoint];
        $try = $instruction['args'];
        $tryArr = (array)$try[0];
        $newRet = $tryArr['value'];
        
        $valForPar1 = 0;

        if ($tryArr['type'] === "var") {
            $split1 = explode("@", $newRet);
            $found2 = Exists::findVar($newRet, $GF, $TF, $LF);
            if (!$found2) {
                //src1 does not exist
                throw new VariableAccessException();
            }            
            foreach ($VarList as $Var) {
                if (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === true)){
                    if($Var->Type === "int"){

                        $valForPar1 = $Var->Value;
                        $setcode = true;
                        if ($valForPar1 < 0 || $valForPar1 > 9) {
                            //not correct value
                            throw new OperandValueException();
                        } 

                    } else {
                        //not correct type
                        throw new OperandTypeException();
                    }
                } elseif (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === false)) {
                    //not inited for eq param1
                    throw new ValueException();
                }
            }
        } elseif ($tryArr['type'] === "int"){
            $setcode = true;
            $valForPar1 = $newRet;
            
            if ($valForPar1 < 0 || $valForPar1 > 9) {
                //not correct value
                throw new OperandValueException();
            }

        } else {
            //not correct type
            throw new OperandTypeException();
        }

        //ret value saved
        $ret = $valForPar1;
     
    }
}