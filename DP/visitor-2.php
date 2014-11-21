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
    abstract function isLeaf(ArithmeticComponent $leaf);
    
    private $state;
    
    public function setVisit($state) {
        $this->state = $state;
    }
}

class EvaluateVisitor extends Visitor {
    
    private $stack;
    
    public function __construct() {
        $this->stack = array();
    }
    
    public function getResult() {
        return array_pop($this->stack);
    }
    
    public function visitPlus(PlusComposite $composite) {
        array_push($this->stack, array_pop($this->stack) + array_pop($this->stack));
    }

    public function visitMinus(MinusComposite $composite) {
        array_push($this->stack, array_pop($this->stack) - array_pop($this->stack));
    }

    public function visitMultiplicate(MultiplicateComposite $composite) {
        array_push($this->stack, array_pop($this->stack) * array_pop($this->stack));
    }

    public function visitLeaf(NumberLeaf $leaf) {
        array_push($this->stack, $leaf->getValue());
    }
    
    public function isLeaf(ArithmeticComponent $leaf) {
        return $leaf->isLeaf();
    }
}

class PrintVisitor extends Visitor {
    
    private $string;
    
    public function __construct() {
        $this->string  = '';
    }
    
    public function visitPlus(PlusComposite $composite) {
        
        switch($this->state) {
            case 1:
                $this->string .= '(';
                break;
            case 2:
                $this->string .= ' + ';
                break;
            case 3:
                $this->string .= ')';
                break;
        }
    }

    public function visitMinus(MinusComposite $composite) {
        switch($this->state) {
            case 1:
                $this->string .= '(';
                break;
            case 2:
                $this->string .= ' - ';
                break;
            case 3:
                $this->string .= ')';
                break;
        }
    }

    public function visitMultiplicate(MultiplicateComposite $composite) {
        switch($this->state) {
            case 1:
                $this->string .= '(';
                break;
            case 2:
                $this->string .= ' * ';
                break;
            case 3:
                $this->string .= ')';
                break;
        }
    }

    public function visitLeaf(NumberLeaf $leaf) {
        switch($this->state) {
            case 1:
                $this->string .= '(';
                break;
            case 2:
                $this->string .= $leaf->getValue();
                break;
            case 3:
                $this->string .= ')';
                break;
        }
    }
    public function isLeaf(ArithmeticComponent $leaf) {
        return $leaf->isLeaf();
    }
    public function getResult() {
        return $this->string . str_repeat(')', $this->counterRight);;
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
    public abstract function isLeaf();
}

class PlusComposite extends ArithmeticComponent {
    public function accept(Visitor $visitor) {
        return $visitor->visitPlus($this);
    }
    public function isLeaf() {
        return false;
    }
}

class MinusComposite extends ArithmeticComponent {
    public function accept(Visitor $visitor) {
        return $visitor->visitMinus($this);
    }
    public function isLeaf() {
        return false;
    }
}

class MultiplicateComposite extends ArithmeticComponent {
    public function accept(Visitor $visitor) {
        return $visitor->visitMultiplicate($this);
    }
    public function isLeaf() {
        return false;
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
    public function isLeaf() {
        return true;
    }
}


abstract class AritheticIterator {
    abstract function traverse(ArithmeticComponent $composite);
    
    /*
    public function postOrder(ArithmeticComponent $composite) {
        if(!$composite->isLeaf()) {
            $this->postOrder($composite->getLeft());
            $this->postOrder($composite->getRight());
        }
        $composite->accept($this->visitor);
    }
    
    
    public function inDirtyOrder(ArithmeticComponent $composite) {
        $result = '';
        if(!$composite->isLeaf()) {
            $result .= '(';
            $result .= $this->inOrder($composite->getLeft());
        }
        
        if($composite->isLeaf()) $result .= $composite->accept($this->visitor);
        
        if(!$composite->isLeaf()) {
            $result .= $composite->accept($this->visitor);
            $result .= $this->inOrder($composite->getRight());
            $result .= ')';
        }
        return $result;
    }*/
}

class inOrderIterator extends AritheticIterator {
    
    private $visitor;
    
    public function __construct($visitor) {
        $this->visitor = $visitor;
    }
    
    public function traverse(ArithmeticComponent $composite) {
        if($composite->getLeft()) {
            $this->visitor->setVisit(1);
            $composite->accept($this->visitor);
            $this->traverse($composite->getLeft());
        }

        $this->visitor->setVisit(2);
        $composite->accept($this->visitor);

        if($composite->getRight()) {
            $composite->accept($this->visitor);
            $this->traverse($composite->getRight());
            $this->visitor->setVisit(3);
        }
    }
}

class postOrderIterator extends AritheticIterator {
    
    private $visitor;
    
    public function __construct($visitor) {
        $this->visitor = $visitor;
    }
    
    public function traverse(ArithmeticComponent $composite) {
        if(!$composite->isLeaf()) {
            $this->traverse($composite->getLeft());
            $this->traverse($composite->getRight());
        }
        $composite->accept($this->visitor);
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
    
    $iteratorIn = new inOrderIterator($print);
    $iteratorIn->traverse($add_brackets);
    echo $print->getResult();
    
    $iteratorPost = new postOrderIterator($evaluate);
    $iteratorPost->traverse($add_brackets);
    echo ' = '.$evaluate->getResult();
    
    //echo $iterator->traverse($add_brackets->accept($print))." = ",$iterator->traverse($add_brackets->accept($evaluate));
}

//
// Make it so
//
main();
