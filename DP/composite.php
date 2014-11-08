<?php

/**
 * Design Patterns Lab 1
 * Composite Pattern
 * (((a+b)*(a-c))+((b*d)-a))
 */

abstract class ArithmeticComponent {
    public abstract function EvaluateTraverse();
    public abstract function PrintTraverse();
}

class PlusComposite extends ArithmeticComponent {
    private $components;

    public function __construct() {
        $this->components = array();
    }

    public function add(ArithmeticComponent $component) {
        array_push($this->components, $component);
    }

    public function EvaluateTraverse() {
        $sum = 0;

        foreach ($this->components as $num)
            $sum += $num->EvaluateTraverse();

        return $sum;
    }

    public function PrintTraverse() {
        $tmp = array();

        foreach ($this->components as $num)
            $tmp[] = $num->PrintTraverse();

        return '('.join(' + ', $tmp).')';
    }
}
class MinusComposite extends ArithmeticComponent {
    private $components;

    public function __construct() {
        $this->components = array();
    }

    public function add(ArithmeticComponent $component) {
        array_push($this->components, $component);
    }

    public function EvaluateTraverse() {
        $sum = 0;

        for ($i=0; $i<count($this->components); $i++) {
            if($i==0) {
                $sum = $this->components[$i]->EvaluateTraverse();
            }else{
                $sum -= $this->components[$i]->EvaluateTraverse();
            }
        }

        return $sum;
    }

    public function PrintTraverse() {
        $tmp = array();

        foreach ($this->components as $num)
            $tmp[] = $num->PrintTraverse();

        return '('.join(' - ', $tmp).')';
    }
}
class MultiplicateComposite extends ArithmeticComponent {
    private $components;

    public function __construct() {
        $this->components = array();
    }

    public function add(ArithmeticComponent $component) {
        array_push($this->components, $component);
    }

    public function EvaluateTraverse() {
        $sum = 0;

        for ($i=0; $i<count($this->components); $i++) {
            if($i==0) {
                $sum = $this->components[$i]->EvaluateTraverse();
            }else{
                $sum *= $this->components[$i]->EvaluateTraverse();
            }
        }

        return $sum;
    }

    public function PrintTraverse() {
        $tmp = array();

        foreach ($this->components as $num)
            $tmp[] = $num->PrintTraverse();

        return '('.join(' * ', $tmp).')';
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

    public function PrintTraverse() {
        return $this->val;
    }
}

function main() {
    $a = new NumberLeaf(3.14);
    $b = new NumberLeaf(27);
    $c = new NumberLeaf(33);
    $d = new NumberLeaf(42);

    $a_plus_b = new PlusComposite();
    $a_plus_b->add($a);
    $a_plus_b->add($b);

    $a_minus_c = new MinusComposite();
    $a_minus_c->add($a);
    $a_minus_c->add($c);

    $mult_first_brackets = new MultiplicateComposite();
    $mult_first_brackets->add($a_plus_b);
    $mult_first_brackets->add($a_minus_c);

    $b_mult_d = new MultiplicateComposite();
    $b_mult_d->add($b);
    $b_mult_d->add($d);

    $second_bracket_minus_a = new MinusComposite();
    $second_bracket_minus_a->add($b_mult_d);
    $second_bracket_minus_a->add($a);

    $add_brackets = new PlusComposite();
    $add_brackets->add($mult_first_brackets);
    $add_brackets->add($second_bracket_minus_a);

    echo $add_brackets->PrintTraverse()." = ", $add_brackets->EvaluateTraverse();
}

//
// Make it so
//
main();