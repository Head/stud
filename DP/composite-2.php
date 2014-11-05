<?php

/**
 * Design Patterns Lab 2
 * Iterator Pattern
 * (((a+b)*(a-c))+((b*d)-a))
 */

abstract class ArithmeticComponent {
    private $left;
    private $right;

    public function __construct(ArithmeticComponent $left, ArithmeticComponent $right) {
        $this->left  = $left;
        $this->right = $right;
    }
    public function getLeft() {
        return $this->left;
    }
    public function getRight(){
        return $this->right;
    }
    public abstract function EvaluateTraverse();
    public abstract function PrintEvaluateTraverse();
}

class PlusComposite extends ArithmeticComponent {
    public function EvaluateTraverse() {
        return $this->getLeft()->EvaluateTraverse() + $this->getRight()->EvaluateTraverse();
    }

    public function PrintEvaluateTraverse() {
        return '('.$this->getLeft()->PrintEvaluateTraverse().' + '.$this->getRight()->PrintEvaluateTraverse().')';
    }
}

class MinusComposite extends ArithmeticComponent {
    public function EvaluateTraverse() {
        return $this->getLeft()->EvaluateTraverse() - $this->getRight()->EvaluateTraverse();
    }

    public function PrintEvaluateTraverse() {
        return '('.$this->getLeft()->PrintEvaluateTraverse().' - '.$this->getRight()->PrintEvaluateTraverse().')';
    }
}

class MultiplicateComposite extends ArithmeticComponent {
    public function EvaluateTraverse() {
        return $this->getLeft()->EvaluateTraverse() * $this->getRight()->EvaluateTraverse();
    }

    public function PrintEvaluateTraverse() {
        return '('.$this->getLeft()->PrintEvaluateTraverse().' * '.$this->getRight()->PrintEvaluateTraverse().')';
    }
}

class NumberLeaf extends ArithmeticComponent {
    private $val;

    public function __construct($num) {
        $this->val = $num;
    }

    public function EvaluateTraverse() {
        return $this->val;
    }

    public function PrintEvaluateTraverse() {
        return $this->val;
    }
}
/*
class Iteratir {
    public function PrintEvaluateTraverse(AritheticComponent $composite) {
        $composite->PrintEvaluateTraverse();
    }

    public function EvaluateTraverse(AritheticComponent $composite) {

    }
}
*/
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

    echo $add_brackets->PrintEvaluateTraverse()." = ", $add_brackets->EvaluateTraverse();
}

//
// Make it so
//
main();