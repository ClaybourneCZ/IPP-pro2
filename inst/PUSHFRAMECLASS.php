<?php

namespace IPP\Student\inst;

use IPP\Student\exceptions\FrameAccessException;
use IPP\Student\VarLit;
use IPP\Student\Stack;

class PUSHFRAMECLASS {

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
    public static function pushFrameFunc(   int &$instPoint,array &$instArr, array &$VarList,
                                            array &$GF, array &$TF, array &$LF, bool &$notIncrement,
                                            Stack &$stackData, Stack &$stackFrame, Stack &$stackCall,
                                            bool &$TFaccess, bool &$LFaccess, array &$labelMap) : void {
        
        // Set LFaccess, for each var in TF, change it to LF, 
        // pushing TF to stackFrame, peek returns top of stack(LF)
        // empty the TF and set TFaccess, after straight after 
        // PUSHFRAME instruction TF cant be accessed                                           
        
        $LFaccess = true;
        if (!$TFaccess){
            throw new FrameAccessException();
        }
        foreach($VarList as $Var){

            if(($Var->Where == "TF") && (in_array($Var->Name, $TF))) 
            {
                $Var->Where = "LF";
            }

        }
        $stackFrame->push($TF);
        $LF = $stackFrame->peek();
        $TF = [];
        $TFaccess = false;

    }
}