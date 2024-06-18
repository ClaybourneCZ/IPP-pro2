<?php

namespace IPP\Student\inst;

use IPP\Core\FileInputReader;
use IPP\Core\StreamWriter;

use IPP\Student\VarLit;
use IPP\Student\Stack;
use IPP\Student\Exists;

class JUMPCLASS {
    
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
    public static function jumpFunc(  int &$instPoint,array &$instArr, array &$VarList,
                                         array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                         Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                         bool &$TFaccess, bool &$LFaccess, array &$labelMap,
                                         $input, $stdout, $stderr) : void {

        // takes op1 as label,  changes the ip
        // to corespoding position from label map

        //FIRST OPERAND
        $instruction = $instArr[$instPoint];
        $try = $instruction['args'];
        $tryArr = (array)$try[0];
        $lookForLabel = $tryArr['value'];

        foreach ( $labelMap as $label ){
            if($label['label'] === $lookForLabel){

                $instPoint = (int)($label['pos']) - 1;

            }
        }        
    }
}