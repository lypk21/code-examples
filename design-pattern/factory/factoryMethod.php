<?php

/**
 * Simple Factory can't satisfy OCP,Factory Method support OCP
 */

interface ICalcResult {
    public function getResult();
}

interface IOperationFactory {
    public static function createOperation();
}

class Operation {
    protected $num1;
    protected $num2;

    public function setNum($num1,$num2) {
        $this->num1 = $num1;
        $this->num2 = $num2;
    }
}

class OperationAdd extends Operation implements ICalcResult {

    public function getResult()
    {
        return $this->num1 + $this->num2;
    }
}

class OperationSub extends Operation implements ICalcResult {

    public function getResult()
    {
        return $this->num1 - $this->num2;
    }
}

class OperationMul extends Operation implements ICalcResult {

    public function getResult()
    {
        return $this->num1 * $this->num2;
    }
}

class OperationDiv extends Operation implements ICalcResult {

    public function getResult()
    {
        if($this->num2 == 0) return 'Division by zero illegally';
        return $this->num1 / $this->num2;
    }
}

class OperationAddFactory implements IOperationFactory {

    public static function createOperation()
    {
        return new OperationAdd();
    }
}

class OperationSubFactory implements IOperationFactory {

    public static function createOperation()
    {
        return new OperationSub();
    }
}

class OperationMulFactory implements IOperationFactory {

    public static function createOperation()
    {
        return new OperationMul();
    }
}

class OperationDivFactory implements IOperationFactory {

    public static function createOperation()
    {
        return new OperationDiv();
    }
}

$operation = OperationAddFactory::createOperation();
$operation->setNum(20,10);
echo $operation->getResult()."\n";

$operation = OperationsubFactory::createOperation();
$operation->setNum(20,10);
echo $operation->getResult()."\n";

$operation = OperationMulFactory::createOperation();
$operation->setNum(20,10);
echo $operation->getResult()."\n";

$operation = OperationDivFactory::createOperation();
$operation->setNum(20,10);
echo $operation->getResult()."\n";
