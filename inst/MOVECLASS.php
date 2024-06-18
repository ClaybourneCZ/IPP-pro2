<?php

namespace IPP\Student\inst;

use IPP\Student\exceptions\OperandTypeException;
use IPP\Student\exceptions\VariableAccessException;
use IPP\Student\VarLit;
use IPP\Student\Exists;
use IPP\Student\Stack;

class MOVECLASS {

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
    public static function moveFunc( int &$instPoint,array &$instArr, array &$VarList,
                                     array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                     Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                     bool &$TFaccess, bool &$LFaccess, array &$labelMap) : void {

        // Extract inst args from InstArray                                        
        $instruction = $instArr[$instPoint];
        $try = $instruction['args'];
        $tryArr1 = (array)$try[0];
    
        // First Operand
        $name1 = strval($tryArr1['value']);
        $name1 = (string) $name1;
        $split1 = explode('@', $name1);

        
        // Second Operand
        $tryArr2 = (array)$try[1];
        $name2 = strval($tryArr2['value']);
        $name2 = (string) $name2;
        
        if ( !(Exists::findVar($name1, $GF, $TF, $LF))) {
            // dest doesnt exist
            throw new VariableAccessException();
        } elseif ($tryArr2['type'] == 'var') {
            if (!(Exists::findVar($name2, $GF, $TF, $LF))) {
                // src doesnt exist
                throw new VariableAccessException();
            }
        }

        // TYPE OF SECOND OPERAND IS:
        if ($tryArr2['type'] == 'var') {

            $split2 = explode('@', $name2);

            foreach ($VarList as $Var) {
                
                if (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === false)) {
                    
                    foreach ($VarList as $Var2){
                        
                        if (($Var2->Name === $split2[1]) && ($Var2->Where === $split2[0]) && ($Var2->Inited === true)) {
                    
                            $Var->Inited = true;
                            $Var->Value = $Var2->Value;
                            $Var->Type = $Var2->Type;
                    
                        } elseif (($Var2->Name === $split2[1]) && ($Var2->Where === $split2[0]) && ($Var2->Inited === false)) {
                    
                            // cant access variable
                            throw new VariableAccessException();

                        }
                    }
                } elseif(($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === true)) {
                    
                    foreach ($VarList as $Var2){

                        if (($Var2->Name === $split2[1]) && ($Var2->Where === $split2[0]) && ($Var2->Inited === true)) {
                            $Var->Value = $Var2->Value;
                            $Var->Type = $Var2->Type;

                        }  elseif (($Var2->Name === $split2[1]) && ($Var2->Where === $split2[0]) && ($Var2->Inited === false)) {
                            // cant access variable
                            throw new VariableAccessException();
                        }
                    }
                } 
            }
        } elseif ($tryArr2['type'] == 'string') {
            

            foreach ($VarList as $Var) {
                
                if (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === false)) {
                   
                    $Var->Inited = true;
                    $Var->Value = $name2;
                    $Var->Type = "string";
                    
                } elseif(($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === true)) {
                    $Var->Value = $name2;
                    $Var->Type = "string";
                } 
            }
        }elseif ($tryArr2['type'] == 'int') {
        
            foreach ($VarList as $Var) {
                
                if (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === false)) {
                    
                    $Var->Inited = true;
                    $Var->Value = (int)$name2;
                    $Var->Type = "int";
                    
                } elseif(($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === true)) {
                    $Var->Value = (int)$name2;
                    $Var->Type = "int";
                } 
            }
        }elseif ($tryArr2['type'] == 'float') {

            foreach ($VarList as $Var) {
                
                if (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === false)) {
                    
                    $Var->Inited = true;
                    $Var->Value = (float)$name2;
                    $Var->Type = "float";
                    
                } elseif(($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === true)) {
                    $Var->Value = $name2;
                    $Var->Type = "float";
                } 
            }
        }elseif ($tryArr2['type'] == 'bool') {

            foreach ($VarList as $Var) {
                
                if (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === false)) {
                    
                    $Var->Inited = true;
                    $Var->Value = (bool)$name2;
                    $Var->Type = "bool";
                    
                } elseif(($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === true)) {
                    $Var->Value = (bool)$name2;
                    $Var->Type = "bool";
                } 
            }
        } elseif ($tryArr2['type'] == 'nil') {

            foreach ($VarList as $Var) {
                
                if (($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === false)) {
                    
                    $Var->Inited = true;
                    $Var->Value = $name2;
                    $Var->Type = "nil";
                    
                } elseif(($Var->Name === $split1[1]) && ($Var->Where === $split1[0]) && ($Var->Inited === true)) {
                    $Var->Value = $name2;
                    $Var->Type = "nil";
                } 
            }
        }
    }
}
   