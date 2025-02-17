<?php

namespace Rector\Tests\Nette\Rector\FuncCall\PregFunctionToNetteUtilsStringsRector\Fixture;

class PregReplace
{
    public function run()
    {
        $content = 'Hi my name is Tom';

        $newContent = preg_replace('#Hi#', 'Hello', $content);
    }
}

?>
-----
<?php

namespace Rector\Tests\Nette\Rector\FuncCall\PregFunctionToNetteUtilsStringsRector\Fixture;

class PregReplace
{
    public function run()
    {
        $content = 'Hi my name is Tom';

        $newContent = \Nette\Utils\Strings::replace($content, '#Hi#', 'Hello');
    }
}

?>
