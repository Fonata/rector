<?php

namespace Rector\Tests\CakePHP\Rector\Namespace_\AppUsesStaticCallToUseStatementRector\Fixture;

\App::uses('NotificationListener', 'Event');

class SomeClass
{
    public function run()
    {
        $values = new NotificationListener();
    }
}

?>
-----
<?php

namespace Rector\Tests\CakePHP\Rector\Namespace_\AppUsesStaticCallToUseStatementRector\Fixture;

use Event\NotificationListener;

class SomeClass
{
    public function run()
    {
        $values = new NotificationListener();
    }
}

?>
