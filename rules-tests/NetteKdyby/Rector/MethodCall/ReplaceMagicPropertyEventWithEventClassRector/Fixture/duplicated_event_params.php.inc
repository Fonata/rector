<?php

namespace Rector\Tests\NetteKdyby\Rector\MethodCall\ReplaceMagicPropertyEventWithEventClassRector\Fixture;

use Rector\Tests\NetteKdyby\Rector\MethodCall\ReplaceMagicPropertyEventWithEventClassRector\Source\SomeUser;

final class DuplicatedEventParams
{
    public $onUpload;

    public function run(SomeUser $user, string $some_underscore)
    {
        $this->onUpload($user['owner_id'], $user->name_value, $some_underscore);
    }
}

?>
-----
<?php

namespace Rector\Tests\NetteKdyby\Rector\MethodCall\ReplaceMagicPropertyEventWithEventClassRector\Fixture;

use Rector\Tests\NetteKdyby\Rector\MethodCall\ReplaceMagicPropertyEventWithEventClassRector\Source\SomeUser;

final class DuplicatedEventParams
{
    public function __construct(private \Symfony\Contracts\EventDispatcher\EventDispatcherInterface $eventDispatcher)
    {
    }

    public function run(SomeUser $user, string $some_underscore)
    {
        $duplicatedEventParamsUploadEvent = new \Rector\Tests\NetteKdyby\Rector\MethodCall\ReplaceMagicPropertyEventWithEventClassRector\Fixture\Event\DuplicatedEventParamsUploadEvent($user['owner_id'], $user->name_value, $some_underscore);
        $this->eventDispatcher->dispatch($duplicatedEventParamsUploadEvent);
    }
}

?>
