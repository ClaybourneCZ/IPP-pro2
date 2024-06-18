<?php

namespace IPP\Student;


use DOMNode;
use IPP\Core\AbstractInterpreter;
use IPP\Core\Exception\NotImplementedException;
use IPP\Student\exceptions\InvalidSourceStructException;
use IPP\Student\exceptions\SemanticException;
use IPP\Student\Stack;
use IPP\Student\VarLit;
use IPP\Student\inst\DEFVARCLASS;
use IPP\Student\inst\MOVECLASS;

use IPP\Student\inst\CREATEFRAMECLASS;
use IPP\Student\inst\PUSHFRAMECLASS;
use IPP\Student\inst\POPFRAMECLASS;

use IPP\Student\inst\CALLFRAMECLASS;
use IPP\Student\inst\RETURNCFRAMECLASS;

use IPP\Student\inst\PUSHSCLASS;
use IPP\Student\inst\POPSCLASS;

use IPP\Student\inst\ADDCLASS;
use IPP\Student\inst\SUBCLASS;
use IPP\Student\inst\MULCLASS;
use IPP\Student\inst\IDIVCLASS;

use IPP\Student\inst\LTCLASS;
use IPP\Student\inst\GTCLASS;
use IPP\Student\inst\EQCLASS;

use IPP\Student\inst\ANDCLASS;
use IPP\Student\inst\ORCLASS;
use IPP\Student\inst\NOTCLASS;

use IPP\Student\inst\INT2CHARCLASS;
use IPP\Student\inst\STRI2INTCLASS;

use IPP\Student\inst\READCLASS;
use IPP\Student\inst\WRITECLASS;

use IPP\Student\inst\CONCATCLASS;
use IPP\Student\inst\STRLENCLASS;
use IPP\Student\inst\GETCHARCLASS;
use IPP\Student\inst\SETCHARCLASS;

use IPP\Student\inst\TYPECLASS;

use IPP\Student\inst\LABELCLASS;
use IPP\Student\inst\JUMPCLASS;
use IPP\Student\inst\JUMPIFEQCLASS;
use IPP\Student\inst\JUMPIFNEQCLASS;
use IPP\Student\inst\EXITCLASS;

use IPP\Student\inst\DPRINTCLASS;
use IPP\Student\inst\BREAKCLASS;

class Interpreter extends AbstractInterpreter
{
    public function execute(): int
    {

        // TODO: Start your code here
        // Check \IPP\Core\AbstractInterpreter for predefined I/O objects:

        $dom = $this->source->getDOMDocument();

        $instructions = $dom->getElementsByTagName('instruction');

        $instructionsArray = [];
        $labelMap = [];
        $orderCheck = [];
        // basic instruction parsing
        foreach ($instructions as $instruction) {

            // order (>0, cant have value twice)
            $order = (string)$instruction->getAttribute('order');
            
            if (in_array((int)$order,  $orderCheck) || ((int)$order) < 1){
                throw new InvalidSourceStructException();
            }

            $orderCheck[]= (int)$order;

            // opcode (instruction)
            $opcode = (string)$instruction->getAttribute('opcode');

            $opcode = strtoupper($opcode);
            $opcode = $opcode . "CLASS";

            //arguments parsing
            $args = [];
            for ($i = 1; $arg = $instruction->getElementsByTagName("arg$i")->item(0); $i++) {
                $argType = $arg->getAttribute('type');
                $argType = (string)$argType;
                $argValue = $arg->nodeValue;
                $argValue = (string) $argValue;
                $argValue = trim($argValue);
                // validation of given data
                if ($argType == 'string' ) {
                    $pattern = '/\\\\([0-9]{3})/';
                    $argValue = preg_replace_callback($pattern, function ($matches) {
                        return mb_chr(intval($matches[1]), "UTF-8");
                    }, $argValue);
                    
                }
                if ($argType === 'bool'){
                    $argValue = filter_var($argValue, FILTER_VALIDATE_BOOLEAN);
                    
                }
                if ($argType === 'int'){
                    $argValue = intval($argValue);
                    
                }                
                if ($opcode == 'LABELCLASS' ) {
                    $labelMap[] = ["label" => $argValue, "order" => $order, "pos" => 0];
                }
                $args[] = ["type" => $argType, "value" => $argValue];
            }
            
            // insert data in InstructionArray
            $instructionsArray[] = [
                "order" => $order,
                "opcode" => $opcode,
                "args" => $args
            ];
        
        }

        // sort the array
        usort($instructionsArray, [self::class, 'sortByOrder']);
        

        $funcMap = array(
            "MOVECLASS" => "moveFunc", 
            "CREATEFRAMECLASS"  => "createFrameFunc", 
            "PUSHFRAMECLASS"  => "pushFrameFunc", 
            "POPFRAMECLASS"  => "popFrameFunc", 
            "DEFVARCLASS"  => "defvarFunc",
            
            "CALLCLASS"  => "callFunc",
            "RETURNCLASS"  => "returnFunc",
            
            "PUSHSCLASS"  => "pushsFunc",
            "POPSCLASS"  => "popsFunc",
            "ADDCLASS"  => "addFunc",
            "SUBCLASS"  => "subFunc",
            "MULCLASS"  => "mulFunc",
            "IDIVCLASS"  => "idivFunc", 

            "LTCLASS"  => "ltFunc",
            "GTCLASS"  => "gtFunc",
            "EQCLASS"  => "eqFunc", 
            
            "ANDCLASS"  => "andFunc",
            "ORCLASS"  => "orFunc",
            "NOTCLASS"  => "notFunc",

            "INT2CHARCLASS"  => "int2charFunc",
            "STRI2INTCLASS"  => "stri2intFunc",

            "READCLASS"  => "readFunc",
            "WRITECLASS"  => "writeFunc",

            "CONCATCLASS"  => "concatFunc",
            "STRLENCLASS"  => "strlenFunc",
            "GETCHARCLASS"  => "getcharFunc",
            "SETCHARCLASS"  => "setcharFunc",
            
            "TYPECLASS"  => "typeFunc", 
            
            "LABELCLASS"  => "labelFunc", 
            "JUMPCLASS"  => "jumpFunc", 
            "JUMPIFEQCLASS"  => "jumpifeqFunc", 
            "JUMPIFNEQCLASS"  => "jumpifneqFunc", 
            "EXITCLASS"  => "exitFunc", 
            
            "DPRINTCLASS"  => "dprintFunc", 
            "BREAKCLASS"  => "breakFunc" 
        );
        
        //stacks and other variables need for running interpret

        $stackCall = new Stack();
        $stackFrame = new Stack();
        $stackData = new Stack();
        
        // inf about all the variables
        $VarList = []; 

        $GlobalFrame = [];
        $TempFrame = [];
        $LocalFrame = [];
        
        $TFaccess = false;
        $LFaccess = false;

        $ip = 0;
        $notIncrement = false;
        $instructionsLen = count($instructionsArray);
        
        //LABEL CHECKING
        $ipPreRun = 0;
        $checkLabelMap = [];
        while ( $ipPreRun < $instructionsLen ) {

            //load pos for jumps
            if ($instructionsArray[$ipPreRun]['opcode'] == "LABELCLASS") {
                foreach ($labelMap as &$label) {
                    if($label['label'] == $instructionsArray[$ipPreRun]['args'][0]['value']){
                        $label["pos"] = $ipPreRun;
                    }
                }
            }
            if ( $instructionsArray[$ipPreRun]['args'] ){
                // check for invalid jumps
                if (("label" === $instructionsArray[$ipPreRun]['args'][0]['type']) && 
                    ($instructionsArray[$ipPreRun]['opcode'] !== "LABELCLASS")){
                    $checkLabel = false;
                    
                    foreach ($labelMap as &$label) {
                        
                        if($label['label'] == $instructionsArray[$ipPreRun]['args'][0]['value']){
                        $checkLabel = true; 
                        }

                    }   

                    if ( $checkLabel === false) {
                        throw new SemanticException();
                    }        

                }
                if (($instructionsArray[$ipPreRun]['args'][0]['type'] === "label") && 
                    ($instructionsArray[$ipPreRun]['opcode'] === "LABELCLASS")){
            
                    $checkLabel = false;
                    if (in_array( $instructionsArray[$ipPreRun]['args'][0]['value'],$checkLabelMap)) {
                        $checkLabel = true;
                    }
                    $checkLabelMap[] = $instructionsArray[$ipPreRun]['args'][0]['value'];
                    
                    if ( $checkLabel === true) {
                        throw new SemanticException();
                    }        

                } 
            }           
            $ipPreRun++;
        }
        
        // here i had a problem with phpstan, always for if condition
        // found this solution for it, but propably
        // the problem is with calling function via funcMap
        // so phpstan cant catch that it could be changed

        /** @var false|true $setCode */
        $setCode = false;
        
        //return code
        $code = -1;
        $ret = 0;

        while ( $ip < $instructionsLen ) {

            if (array_key_exists($instructionsArray[$ip]['opcode'], $funcMap)) {
                
               
                // call the corresponding function from coresponding class
                $className = "IPP\Student\inst\\".$instructionsArray[$ip]['opcode'];
                $funcName = $funcMap[$instructionsArray[$ip]['opcode']];
                

                $className::$funcName($ip,$instructionsArray, $VarList, $GlobalFrame, $TempFrame, $LocalFrame, $notIncrement, $stackData, $stackFrame, $stackCall, $TFaccess,  $LFaccess, $labelMap, $this->input, $this->stdout, $this->stderr, $code, $setCode);

            } 

            if ($instructionsArray[$ip]['opcode'] === "EXITCLASS" && $setCode){
                
                $ret = $code;

                break;
            }

            $ip++;

        }
     
        // base setted to 0, if EXIT instruction is processed than it could be changed
        return $ret;
    }

    /**
     * @param array<mixed> $a
     * @param array<mixed> $b
    */
    public static function sortByOrder(array $a, array $b ) : int {
        return $a['order'] <=> $b['order'];
    }

}
