<?php

namespace IPP\Student;

use IPP\Core\Exception\NotImplementedException;

class Stack {

    /** @var array<array<string>|int|float|string|bool> $stack*/
    private array $stack;

    public function __construct() {
        $this->stack = [];
    }

    /** 
     * @param array<string>|int|float|string|bool $item
     * */
    public function push( $item): void {
        array_push($this->stack, $item);
    }

    /** 
     * @return array<string>|string|null|int|float|bool $item
     * */
    public function pop() {
        if ($this->isEmpty()) {
            //throw new Exception("Stack is empty");
            return null;
        }
        return array_pop($this->stack);
    }

    public function isEmpty(): bool {
        return empty($this->stack);
    }

    /** 
     * @return array<string>|int|float|string|bool|false $item
     * */
    public function peek() {
        if ($this->isEmpty()) {
            //throw new Exception("Stack is empty");
            $empty = [];
            return $empty;
        }
        return end($this->stack);
    }

    public function size(): int {
        return count($this->stack);
    }
}

