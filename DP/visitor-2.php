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
}

class EvaluateVisitor extends Visitor {
    
    private $left;
    private $right;
    
    public function visitPlus(PlusComposite $composite) {
        if($this->left && $this->right) {
            echo "calc: ".$this->left.' + '.$this->right."<br/>";
            $this->left = $this->left + $this->right;
            $this->right = false;
        }else{
            return $this->left;
        }
    }

    public function visitMinus(MinusComposite $composite) {
        if($this->left && $this->right) {
            echo "calc: ".$this->left.' - '.$this->right."<br/>";
            $this->left = $this->left - $this->right;
            $this->right = false;
        }else{
            return $this->left;
        }
    }

    public function visitMultiplicate(MultiplicateComposite $composite) {
        if($this->left && $this->right) {
            echo "calc: ".$this->left.' * '.$this->right."<br/>";
            $this->left = $this->left * $this->right;
            $this->right = false;
        }else{
            return $this->left;
        }
    }

    public function visitLeaf(NumberLeaf $leaf) {
        echo "in leaf<br/>";
        if(!$this->left) {
            $this->left = $leaf->getValue();
            echo "set left to ".$this->left."<br/>";
            //$this->right = false;
        }else{
            $this->right = $leaf->getValue();
            echo "set right to ".$this->right."<br/>";
        }
        return;
    }
    public function isLeaf(ArithmeticComponent $leaf) {
        return $leaf->isLeaf();
    }
}

class PrintVisitor extends Visitor {
    public function visitPlus(PlusComposite $composite) {
        return ' + ';
    }

    public function visitMinus(MinusComposite $composite) {
        return ' - ';
    }

    public function visitMultiplicate(MultiplicateComposite $composite) {
        return ' * ';
    }

    public function visitLeaf(NumberLeaf $leaf) {
        return $leaf->getValue();
    }
    public function isLeaf(ArithmeticComponent $leaf) {
        return $leaf->isLeaf();
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


class AritheticIterator {
    
    private $visitor;
    
    public function __construct($visitor) {
        $this->visitor = $visitor;
    }
    
    public function postOrder(ArithmeticComponent $composite) {
        if(!$composite->isLeaf()) {
            $this->postOrder($composite->getLeft());
            $this->postOrder($composite->getRight());
        }
        $composite->accept($visitor);
    }
    public function inCalcOrder(ArithmeticComponent $composite) {
        if(!$composite->isLeaf()) {
            $this->inOrder($composite->getLeft());
        }
        
        if($composite->isLeaf()) $composite->accept($visitor);
        
        if(!$composite->isLeaf()) {
            $composite->accept($visitor);
            $this->inOrder($composite->getRight());
        }
        return $result;
    }
    
    public function inOrder(ArithmeticComponent $composite) {
        $result = '';
        if(!$composite->isLeaf()) {
            $result .= '(';
            $result .= $this->inOrder($composite->getLeft());
        }
        
        if($composite->isLeaf()) $result .= $composite->accept($visitor);
        
        if(!$composite->isLeaf()) {
            $result .= $composite->accept($visitor);
            $result .= $this->inOrder($composite->getRight());
            $result .= ')';
        }
        return $result;
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
    
    $iterator = new AritheticIterator($print);
    echo $iterator->inOrder($add_brackets);
    
    $iterator2 = new AritheticIterator($evaluate);
    echo $iterator2->postOrder($add_brackets);
    
    //echo $iterator->traverse($add_brackets->accept($print))." = ",$iterator->traverse($add_brackets->accept($evaluate));
}

//
// Make it so
//
main();
