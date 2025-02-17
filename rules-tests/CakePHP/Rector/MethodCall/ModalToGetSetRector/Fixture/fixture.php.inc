<?php

namespace Rector\Tests\CakePHP\Rector\MethodCall\ModalToGetSetRector\Fixture;

use Rector\Tests\CakePHP\Rector\MethodCall\ModalToGetSetRector\Source;

class Fixture
{
    function run()
    {
        $object = new Source\SomeModelType;

        $config = $object->config();
        $config = $object->config('key');
        $object->config('key', 'value');
        $object->config(['key' => 'value']);
        $object->config('key');
    }
}

?>
-----
<?php

namespace Rector\Tests\CakePHP\Rector\MethodCall\ModalToGetSetRector\Fixture;

use Rector\Tests\CakePHP\Rector\MethodCall\ModalToGetSetRector\Source;

class Fixture
{
    function run()
    {
        $object = new Source\SomeModelType;

        $config = $object->getConfig();
        $config = $object->getConfig('key');
        $object->setConfig('key', 'value');
        $object->setConfig(['key' => 'value']);
        $object->getConfig('key');
    }
}

?>
