<?php
/**
 * In simple factory pattern, we have a factory class which has a method that returns different types of object based on given input
 */

interface CalcResult {
    public function getResult();
}

class Operation {
    protected $num1;
    protected $num2;

    public function setNum($num1,$num2) {
        $this->num1 = $num1;
        $this->num2 = $num2;
    }
}

class OperationAdd extends Operation implements CalcResult {

    public function getResult()
    {
        return $this->num1 + $this->num2;
    }
}

class OperationSub extends Operation implements CalcResult {

    public function getResult()
    {
        return $this->num1 - $this->num2;
    }
}

class OperationMul extends Operation implements CalcResult {

    public function getResult()
    {
        return $this->num1 * $this->num2;
    }
}

class OperationDiv extends Operation implements CalcResult {

    public function getResult()
    {
        if($this->num2 == 0) return 'Division by zero illegally';
        return $this->num1 / $this->num2;
    }
}

class OperationFactory {
    public static function createOperation($type) {
        try {
            switch ($type) {
                case '+':
                    return new OperationAdd();
                    break;
                case '-':
                    return new OperationSub();
                    break;
                case '*':
                    return new OperationMul();
                    break;
                case '/':
                    return new OperationDiv();
                    break;
                default:
                    throw new Exception('unknown type');
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }
}

$operation = OperationFactory::createOperation('+');
$operation->setNum(20,10);
echo $operation->getResult()."\n";

$operation = OperationFactory::createOperation('-');
$operation->setNum(20,10);
echo $operation->getResult()."\n";

$operation = OperationFactory::createOperation('*');
$operation->setNum(20,10);
echo $operation->getResult()."\n";

$operation = OperationFactory::createOperation('/');
$operation->setNum(20,10);
echo $operation->getResult()."\n";
