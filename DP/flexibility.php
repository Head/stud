<?php

/**
 * Design Patterns Lab 5
 * F l e x i b i l i t y
    1. The patterns used so far should have introduced enough flexibility into your
    design so that an extension of the abstract syntax tree of exercise 1 can be
    carried out easily.
    2. The arithmetic expression of exercise 1 should now be embedded in an ifstatement
    as follows:
    if (x<0) x = (((a+b)*(a-c))+((b*d)-a));
    Extend your interpreter in such a way that, for a given initial assignment to
    the variables a, b, c, d and x, the variable x is assigned the value of the
    arithmetic expression if the initial value of x is negative. Print out the value
    of x.
    3. To represent the abstract syntax tree of the if-statement, new component
    types must be introduced: the binary composites IF, LESS and ASSIGN as
    well as a new leaf CONST.
    4. To visit the ASSIGN node in the abstract syntax tree in dependence on the
    value of the LESS operation, a Conditional Iterator is necessary. This requires
    a conditional recursive call of traverse(), depending on the results of
    the Accept() calls. Use the Evaluation Visitor’s attribute state as a bidirectional
    communication channel between Iterator and Visitor.
    5. The Evaluation Visitor should now have the attributes context (current values
    of the variables), state (alternately, the state of the iteration and the
    state of the visit operation) and stack (the values of the arithmetic and
    logical operations).
 */

abstract class Visitor {
    abstract function visitPlus(PlusComposite $composite);
    abstract function visitMinus(MinusComposite $composite);
    abstract function visitMultiplicate(MultiplicateComposite $composite);
    abstract function visitLeaf(NumberLeaf $leaf);

    abstract function visitIf(IfComposite $composite);
    abstract function visitLess(LessComposite $composite);
    abstract function visitAssign(AssignComposite $composite);

    abstract function isLeaf(ArithmeticComponent $leaf);
    abstract function setState($state);
    abstract function condition();
}

class EvaluateVisitor extends Visitor {
    private $stack;
    private $component;
    
    public function __construct(ArithmeticComponent $component) {
        $this->stack = array();
        $this->component  = $component;
        $this->condition = true;
    }

    private $state;
    private $condition;

    public function setState($state) {
        $this->state = $state;
    }
    public function condition() {
        return $this->condition;
    }
    
    public function visitIf(IfComposite $composite){
        echo "if ".$this->state;
    }
    public function visitLess(LessComposite $composite){
        echo "less ".$this->state;
        var_dump($composite);
        switch($this->state) {
            case 1:
                break;
            case 2:
                $this->condition = ($composite->getLeft()->getValue() < $composite->getRight()->getValue());
                break;
            case 3:
                $this->condition = true;
                break;
        }
        var_dump($this->condition);
    }
    public function visitAssign(AssignComposite $composite){
        echo "assign ".$this->state;
        var_dump($this->stack);
        var_dump($composite);
        switch($this->state) {
            case 1:
                break;
            case 2:
                $composite->getLeft()->setValue(array_pop($this->stack));
                break;
            case 3:
                break;
        }
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
        $this->condition = true;
    }
    
    private $state;
    private $condition;
    
    public function setState($state) {
        $this->state = $state;
    }
    public function condition() {
        return $this->condition;
    }

    public function visitIf(IfComposite $composite){
        switch($this->state) {
            case 1:
                $this->string .= '';
                break;
            case 2:
                $this->string .= ' if ';
                break;
            case 3:
                $this->string .= '';
                break;
        }
    }
    public function visitLess(LessComposite $composite){
        switch($this->state) {
            case 1:
                $this->string .= '';
                break;
            case 2:
                $this->string .= ' < ';
                break;
            case 3:
                $this->string .= '';
                break;
        }
    }
    public function visitAssign(AssignComposite $composite){
        switch($this->state) {
            case 1:
                $this->string .= '';
                break;
            case 2:
                $this->string .= ' = ';
                break;
            case 3:
                $this->string .= '';
                break;
        }
    }
    public function visitConst(ConstComposite $composite){
        switch($this->state) {
            case 1:
                $this->string .= '';
                break;
            case 2:
                $this->string .= ' CONST ';
                break;
            case 3:
                $this->string .= '';
                break;
        }
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
    protected $left;
    protected $right;

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
class IfComposite extends ArithmeticComponent {
    public function accept(Visitor $visitor) {
        return $visitor->visitIf($this);
    }
    public function isLeaf() {
        return false;
    }
}
class LessComposite extends ArithmeticComponent {
    public function accept(Visitor $visitor) {
        return $visitor->visitLess($this);
    }
    public function isLeaf() {
        return false;
    }
}
class AssignComposite extends ArithmeticComponent {
    public function accept(Visitor $visitor) {
        return $visitor->visitAssign($this);
    }
    public function isLeaf() {
        return false;
    }
}
/*
class ConstLeaf extends ArithmeticComponent {
    private $val;

    public function __construct($num) {
        $this->val = $num;
    }

    public function getValue() {
        return $this->val;
    }

    public function accept(Visitor $visitor) {
        //$visitor->setState('const');
        return $visitor->visitConst($this);
    }
    public function isLeaf() {
        return true;
    }
}
*/

class NumberLeaf extends ArithmeticComponent {
    private $val;

    public function __construct($num) {
        $this->val = $num;
    }

    public function getValue() {
        return $this->val;
    }
    public function setValue($val) {
        $this->val = $val;
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
            $this->visitor->setState(1);
            $composite->accept($this->visitor);
            $this->traverse($composite->getLeft());
        }

        $this->visitor->setState(2);
        $composite->accept($this->visitor);

        if($composite->getRight()) {
            $this->traverse($composite->getRight());
            $this->visitor->setState(3);
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

class preOrderIterator extends AritheticIterator {

    private $visitor;

    public function __construct($visitor) {
        $this->visitor = $visitor;
    }

    public function traverse(ArithmeticComponent $composite) {

        $this->visitor->setState(2);
        $composite->accept($this->visitor);

        if($composite->getLeft()) {
            $this->traverse($composite->getLeft());
        }

        if($composite->getRight()) {
            $this->traverse($composite->getRight());
        }
    }
}
class conditionalIterator extends AritheticIterator {

    private $visitor;

    public function __construct($visitor) {
        $this->visitor = $visitor;
    }

    public function traverse(ArithmeticComponent $composite) {
        if(!$composite->isLeaf()) {
            if($this->visitor->condition()) {
                $this->visitor->setState(1);
                $this->traverse($composite->getLeft());
            }

            if($this->visitor->condition()) {
                $this->visitor->setState(3);
                $this->traverse($composite->getRight());
            }
        }
        if($this->visitor->condition()) {
            $this->visitor->setState(2);
            $composite->accept($this->visitor);
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
            case "pre":
                $iterator = new preOrderIterator($visitor);
                break;
            case "conditional":
                $iterator = new conditionalIterator($visitor);
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
    echo "Pre (polish) Notation: ".$print->getResult('pre').' = '.$evaluate->getResult('post');
    echo "<hr>";

    $zero                   = new NumberLeaf(0);
    $x                      = new NumberLeaf(-5);
    $assign                 = new AssignComposite($x, $add_brackets);
    $if                     = new IfComposite(new LessComposite($x, $zero), $assign);

    $evaluate_if            = new EvaluateVisitor($if);
    $evaluate_if->getResult('conditional');
    echo "<hr>X = ".$x->getValue();

}

//
// Make it so
//
main();