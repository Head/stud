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

abstract class ArithmeticComponent {
    private $left;
    private $right;

    public function __construct(ArithmeticComponent $left, ArithmeticComponent $right) {
        $this->left  = $left;
        $this->right = $right;
    }
    public function getLeft(){
        return $this->left;
    }
    public function getRight(){
        return $this->right;
    }
    public abstract function getOperation();
}

class PlusComposite extends ArithmeticComponent {
    public function getOperation(){
        return '+';
    }
}

class MinusComposite extends ArithmeticComponent {
    public function getOperation(){
        return '-';
    }
}

class MultiplicateComposite extends ArithmeticComponent {
    public function getOperation(){
        return '*';
    }
}

class NumberLeaf extends ArithmeticComponent {
    private $val;

    public function __construct($num) {
        $this->val = $num;
    }

    public function getValue() {
        return $this->val;
    }

    public function getOperation(){
        return $this->val;
    }
}

class AritheticIterator {
    public function PrintTraverse(ArithmeticComponent $component) {
        $return = '';
        if(get_class($component) != 'NumberLeaf') {
            $return .= '(';
            $return .= $this->PrintTraverse($component->getLeft());
        }

        $return .= ' '.$component->getOperation().' ';

        if(get_class($component) != 'NumberLeaf') {
            $return .= $this->PrintTraverse($component->getRight());
            $return .= ')';
        }
        return $return;
    }

    public function EvaluateTraverse(ArithmeticComponent $component) {
        if(get_class($component) != 'NumberLeaf') {
            switch($component->getOperation()) {
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

    $a_plus_b               = new PlusComposite($a, $b);
    $a_minus_c              = new MinusComposite($a, $c);
    $mult_first_brackets    = new MultiplicateComposite($a_plus_b, $a_minus_c);
    $b_mult_d               = new MultiplicateComposite($b, $d);
    $second_bracket_minus_a = new MinusComposite($b_mult_d, $a);
    $add_brackets           = new PlusComposite($mult_first_brackets, $second_bracket_minus_a);

    $iterator = new AritheticIterator();

    echo $iterator->PrintTraverse($add_brackets)." = ", $iterator->EvaluateTraverse($add_brackets);
}

//
// Make it so
//
main();