<?php

/**
 * Design Patterns Lab 2
 * Iterator Pattern
 * (((a+b)*(a-c))+((b*d)-a))
 *
 *  1. Define the Print and Evaluate methods of the 1st exercise as element
 *  functions of an Iterator. Print and Evaluate methods should no longer be
 *  element functions of Component nodes.
 *
 *  2. Print-out the arithmetic expression of the 1st exercise by means of an
 *  Internal Iterator.
 *
 *  3. Evaluate the arithmetic expression for some value assignment by means of
 *  an Internal Iterator.
 */

class ArithmeticComponent {
    private $val;

    public function getValue() {
        return $this->val;
    }
    public function setValue($val) {
        $this->val = $val;
    }
}

class ArithmeticComposite extends ArithmeticComponent {
    private $left;
    private $right;

    public function __construct(ArithmeticComponent $left, $operation, ArithmeticComponent $right) {
        $this->left   = $left;
        $this->setValue($operation);
        $this->right  = $right;
    }
    public function getLeft(){
        return $this->left;
    }
    public function getRight(){
        return $this->right;
    }
    public function isLeaf() {
        return false;
    }
}

class NumberLeaf extends ArithmeticComponent {
    public function __construct($num) {
        $this->setValue($num);
    }
    public function isLeaf() {
        return true;
    }
}

class AritheticIterator {
    public function PrintTraverse(ArithmeticComponent $component) {
        $return = '';
        if(!$component->isLeaf()) {
            $return .= '(';
            $return .= $this->PrintTraverse($component->getLeft());
        }

        $return .= ' '.$component->getValue().' ';

        if(!$component->isLeaf()) {
            $return .= $this->PrintTraverse($component->getRight());
            $return .= ')';
        }
        return $return;
    }

    public function EvaluateTraverse(ArithmeticComponent $component) {
        if(!$component->isLeaf()) {
            switch($component->getValue()) {
                case '+':
                    $return = $this->EvaluateTraverse($component->getLeft()) + $this->EvaluateTraverse($component->getRight());
                    break;
                case '-':
                    $return = $this->EvaluateTraverse($component->getLeft()) - $this->EvaluateTraverse($component->getRight());
                    break;
                case '*':
                    $return = $this->EvaluateTraverse($component->getLeft()) * $this->EvaluateTraverse($component->getRight());
                    break;
                default: $return = 0;
            }
        }else{
            $return = $component->getValue();
        }
        return $return;
    }
}

function main() {
    $a = new NumberLeaf(3.14);
    $b = new NumberLeaf(27);
    $c = new NumberLeaf(33);
    $d = new NumberLeaf(42);

    $a_plus_b               = new ArithmeticComposite($a, '+', $b);
    $a_minus_c              = new ArithmeticComposite($a, '-', $c);
    $mult_first_brackets    = new ArithmeticComposite($a_plus_b, '*', $a_minus_c);
    $b_mult_d               = new ArithmeticComposite($b, '*', $d);
    $second_bracket_minus_a = new ArithmeticComposite($b_mult_d, '-', $a);
    $add_brackets           = new ArithmeticComposite($mult_first_brackets, '+', $second_bracket_minus_a);

    $iterator = new AritheticIterator();
    echo $iterator->PrintTraverse($add_brackets)." = ", $iterator->EvaluateTraverse($add_brackets);
}

//
// Make it so
//
main();