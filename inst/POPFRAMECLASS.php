<?php

namespace IPP\Student\inst;

use IPP\Student\exceptions\FrameAccessException;
use IPP\Student\VarLit;
use IPP\Student\Stack;

class POPFRAMECLASS {

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
    public static function popFrameFunc(   int &$instPoint,array &$instArr, array &$VarList,
                                            array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                            Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                            bool &$TFaccess, bool &$LFaccess, array &$labelMap) : void {
        
        // for each var in LF, change it to TF, 
        // pop LF from stackFrame, if nothing on stack throw err
        // peek returns top of stack new LF, if empty, set empty LF 
        // and LFaccess and set TFaccess, after straight after 
        // POPFRAME instruction TF can be accessed, LF might not be accessible   
        
        foreach($VarList as $Var){
            if(($Var->Where == "LF") && (in_array($Var->Name, $LF))) 
            {
                $Var->Where = "TF";
            }
        }
        $TF = $stackFrame->pop();
        if($TF == null) {
            throw new FrameAccessException();
        }
        $LF = $stackFrame->peek();
        if($LF == false){
            $LFaccess = false;
            $LF = [];
        }
        $TFaccess = true;                                                

    }
}