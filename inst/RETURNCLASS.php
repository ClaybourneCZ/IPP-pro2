<?php

namespace IPP\Student\inst;

use IPP\Student\exceptions\ValueException;
use IPP\Student\Stack;

class RETURNCLASS {
    
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
     * @param array<array<string|array<string>|null>> $labelMap
    */
    public static function returnFunc(  int &$instPoint,array &$instArr, array &$VarList,
                                         array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                         Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                         bool &$TFaccess, bool &$LFaccess, array &$labelMap) : void {
        
        // pop ip from stackCall
        // if empty than throw err
        // save poped ip to actual ip                                   
        $newInstPoint = $stackCall->pop();
        if( $newInstPoint == null) {
            throw new ValueException();
        }        
        $newInstPoint = (int)$newInstPoint;
        $instPoint = $newInstPoint;
    
                                        
    }

}