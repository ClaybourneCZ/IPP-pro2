<?php

namespace IPP\Student\inst;

use IPP\Student\VarLit;
use IPP\Student\Exists;
use IPP\Student\Stack;

class CREATEFRAMECLASS {

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
    public static function createFrameFunc( int &$instPoint,array &$instArr, array &$VarList,
                                             array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                             Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                             bool &$TFaccess, bool &$LFaccess, array &$labelMap) : void {
        
        // Create new TF, if there is any variable in TF
        // before CREATEFRAME, its deleted, than access for TF is setted
        $newTF = [];
        foreach ($TF as $str){
            Exists::removeVar($str,$VarList);
        }
        $TF = $newTF;
        $TFaccess = true;

    }
}