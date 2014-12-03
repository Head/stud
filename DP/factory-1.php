<?php

/**
 * Design Patterns Lab 4
 * F a c t o r y M e t h o d
 * 1. Use a Factory Method to assign a suitable Iterator to a given Visitor.
 * 2. Define an additional Iterator for printing the Composite structure in Polish
 * Notation (+ab or ab+ instead of a+b).
 * 3. Your main program should test all of the operations defined so far.
 */

abstract class Visitor {
    abstract function visitPlus(PlusComposite $composite);
    abstract function visitMinus(MinusComposite $composite);
    abstract function visitMultiplicate(MultiplicateComposite $composite);
    abstract function visitLeaf(NumberLeaf $leaf);
    abstract function isLeaf(ArithmeticComponent $leaf);
    
}

class EvaluateVisitor extends Visitor {
    private $stack;
    private $component;
    
    public function __construct(ArithmeticComponent $component) {
        $this->stack = array();
        $this->component  = $component;
    }

    public function visitPlus(PlusComposite $composite) {
        array_push($this->stack, array_pop($this->stack) + array_pop($this->stack));
    }

    public function visitMinus(MinusComposite $composite) {
        $p1 = array_pop($this->stack);
        $p2 = array_pop($this->stack);
        array_push($this->stack, $p2 - $p1);
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

    public function getResult($iteratorType) {

        $this->string  = '';

        IteratorFactoryMethod::makeIterator($iteratorType, $this)->traverse($this->component);

        return array_pop($this->stack);
    }
}

class PrintVisitor extends Visitor {
    
    private $string;
    private $component;
    
    public function __construct(ArithmeticComponent $component) {
        $this->component  = $component;
    }
    
    private $state;
    
    public function setVisit($state) {
        $this->state = $state;
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
                $this->string .= ' '.$leaf->getValue().' ';
                break;
            case 3:
                $this->string .= ')';
                break;
        }
    }
    public function isLeaf(ArithmeticComponent $leaf) {
        return $leaf->isLeaf();
    }
    public function getResult($iteratorType) {

        $this->string  = '';

        IteratorFactoryMethod::makeIterator($iteratorType, $this)->traverse($this->component);

        return $this->string;
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
            $this->traverse($composite->getRight());
            $this->visitor->setVisit(3);
            $composite->accept($this->visitor);
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

class polishOrderIterator extends AritheticIterator {

    private $visitor;

    public function __construct($visitor) {
        $this->visitor = $visitor;
    }

    public function traverse(ArithmeticComponent $composite) {

        $this->visitor->setVisit(2);
        $composite->accept($this->visitor);

        if($composite->getLeft()) {
            $this->traverse($composite->getLeft());
        }

        if($composite->getRight()) {
            $this->traverse($composite->getRight());
        }
    }
}


abstract class AbstractFactoryMethod {
    abstract static function makeIterator($type, Visitor $visitor);
}

class IteratorFactoryMethod extends AbstractFactoryMethod {
    static function makeIterator($type, Visitor $visitor) {
        $iterator = NULL;
        switch ($type) {
            case "in":
                $iterator = new inOrderIterator($visitor);
                break;
            case "post":
                $iterator = new postOrderIterator($visitor);
                break;
            case "polish":
                $iterator = new polishOrderIterator($visitor);
                break;
            default:
                $iterator = new inOrderIterator($visitor);
                break;
        }
        return $iterator;
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

    $print                  = new PrintVisitor($add_brackets);
    $evaluate               = new EvaluateVisitor($add_brackets);

    echo $print->getResult('in').' = '.$evaluate->getResult('post');
    echo "<hr>";
    echo "Polish Notation: ".$print->getResult('polish').' = '.$evaluate->getResult('post');
}

//
// Make it so
//
main();
