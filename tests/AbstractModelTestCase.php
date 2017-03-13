<?php

use PHPUnit_Framework_TestCase as PHPUnitTestCase;
use Dsh\AbstractModel\AbstractModel;
use Dsh\AbstractModel\Exception\AbstractModelException;
use Dsh\AbstractModel\Exception\PropertyNotFoundException;

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
    }

    public function testDirectAccess()
    {
        $model = $this->mockModel()->init();
        $this->assertEquals('Public', $model->pub);

        $model->init(AbstractModel::USE_ALL);
        $this->assertEquals('Private', $model->getPri());
    }

    /**
     * @expectedException Dsh\AbstractModel\Exception\PropertyNotFoundException
     */
    public function testFailures()
    {
        $model = $this->mockModel()->init();
        $model->getPri();
    }

    public function testSetPrivateAccess()
    {
        $model = $this->mockModel()->init(AbstractModel::USE_PRIVATE);
        $this->assertEquals('Private', $model->getPri());

        try {
            $this->assertEquals('Public', $model->getPub());
        } catch (PropertyNotFoundException $pnfe) {
            $model->init(AbstractModel::USE_PUBLIC | AbstractModel::USE_PRIVATE);
            $this->assertEquals('Public', $model->getPub());
        }
    }

    public function testPropertiesAsCallables()
    {
        $model = $this->mockModel()->init();
        $model->set('pub', function () {
            return 'Public';
        });

        $this->assertInstanceOf('Closure', $model->getPub());
    }
}