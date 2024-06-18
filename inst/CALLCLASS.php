<?php

namespace IPP\Student\inst;

use IPP\Core\Exception\NotImplementedException;
use IPP\Student\VarLit;
use IPP\Student\Stack;

class CALLCLASS {
    
    /**
     * @param int $instPoint
     * @param array<array<string|array<string|array<string>>>> $instArr
     * @param array<mixed> $VarList
     * @param array<string> $GF
     * @param array<string> $TF
     * @param array<string> $LF
     * @param bool $notIncrement
     * @param Stack $stackData
     * @param Stack $stackFrame
     * @param Stack $stackCall
     * @param bool $TFaccess
     * @param bool $LFaccess
     * @param array<array<string|array<string|int>|null>> $labelMap
    */
    public static function callFunc(  int &$instPoint,array &$instArr, array &$VarList,
                                         array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                         Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                         bool &$TFaccess, bool &$LFaccess, array &$labelMap ) : void {
        
        // find label in labelMap and save ip to stackCall,
        // set new ip from labelMap                                            

        //FIRST OPERAND
        $instruction = $instArr[$instPoint];
        $try = $instruction['args'];
        $tryArr = (array)$try[0];
        $lookForLabel = $tryArr['value'];

        foreach ( $labelMap as $label ){

            if($label['label'] === $lookForLabel){

                // due to my implementation i dont save the incremented ip,
                // save my current pos tu stackCall and change to ip to desired label 
                $stackCall->push((int)$instPoint);
                $instPoint = (int)($label['pos']) - 1;

            }
        }
    }

}