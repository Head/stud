<?php

/**
 * Design Patterns Lab 3
 * V i s i t o r
 *  1. Move the Print and Evaluate methods of the Composite pattern of the first
 *  and second exercise to Visitor classes.
 *  2. The Composite class shall essentially contain one method only (Accept).
 *  3. Transfer the traverses for Printing and Evaluation to Iterator classes.
 */

abstract class Visitor {
    abstract function visitPlus(PlusComposite $composite);
    abstract function visitMinus(MinusComposite $composite);
    abstract function visitMultiplicate(MultiplicateComposite $composite);
    abstract function visitLeaf(NumberLeaf $leaf);
}

class EvaluateVisitor extends Visitor {
    public function visitPlus(PlusComposite $composite) {
        return $composite->getLeft()->accept($this) + $composite->getright()->accept($this);
    }

    public function visitMinus(MinusComposite $composite) {
        return $composite->getLeft()->accept($this) - $composite->getright()->accept($this);
    }

    public function visitMultiplicate(MultiplicateComposite $composite) {
        return $composite->getLeft()->accept($this) * $composite->getright()->accept($this);
    }

    public function visitLeaf(NumberLeaf $leaf) {
        return $leaf->getValue();
    }
}

class PrintVisitor extends Visitor {
    public function visitPlus(PlusComposite $composite) {
        return '(' . $composite->getLeft()->accept($this) . ' + ' . $composite->getright()->accept($this) . ')';
    }

    public function visitMinus(MinusComposite $composite) {
        return '(' . $composite->getLeft()->accept($this) . ' - ' . $composite->getright()->accept($this) . ')';
    }

    public function visitMultiplicate(MultiplicateComposite $composite) {
        return '(' . $composite->getLeft()->accept($this) . ' * ' . $composite->getright()->accept($this) . ')';
    }

    public function visitLeaf(NumberLeaf $leaf) {
        return $leaf->getValue();
    }
}

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
    public abstract function accept(Visitor $visitorIn);
}

class PlusComposite extends ArithmeticComponent {
    public function accept(Visitor $visitor) {
        return $visitor->visitPlus($this);
    }
}

class MinusComposite extends ArithmeticComponent {
    public function accept(Visitor $visitor) {
        return $visitor->visitMinus($this);
    }
}

class MultiplicateComposite extends ArithmeticComponent {
    public function accept(Visitor $visitor) {
        return $visitor->visitMultiplicate($this);
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

    public function accept(Visitor $visitor) {
        return $visitor->visitLeaf($this);
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

    $print                  = new PrintVisitor();
    $evaluate               = new EvaluateVisitor();

    echo $add_brackets->accept($print)." = ", $add_brackets->accept($evaluate);
}

//
// Make it so
//
main();