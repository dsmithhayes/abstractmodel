<?php

use PHPUnit_Framework_TestCase as PHPUnitTestCase;
use Phpmut\AbstractModel;

class AbstractModelTestCase extends PHPUnitTestCase
{
    protected function mockModel()
    {
        return new class extends AbstractModel {
            public $pub = "Public";
            protected $pro = "Protected";
            private $pri = "Private";
        };
    }

    public function testAbstractModel()
    {
        $model = $this->mockModel()->init();

        $this->assertEquals('Public', $model->getPub());
        $this->assertEquals('Protected', $model->getPro());
    }

    /**
     * @expectedException Exception
     */
    public function testFailures()
    {
        $model = $this->mockModel();
        $model->getPri();
    }

    public function testSetPrivateAccess()
    {
        $model = $this->mockModel()->init(AbstractModel::MASK_PRIVATE);
        $this->assertEquals('Private', $model->getPri());
    }
}