<?php

namespace Rector\Tests\Nette\Rector\MethodCall\ConvertAddUploadWithThirdArgumentTrueToAddMultiUploadRector\Fixture;

use Nette\Forms\Form;

final class SomeClass
{
    public function upload()
    {
        $form = new Form();
        $form->addUpload('a', 'a', true);
    }
}

?>
-----
<?php

namespace Rector\Tests\Nette\Rector\MethodCall\ConvertAddUploadWithThirdArgumentTrueToAddMultiUploadRector\Fixture;

use Nette\Forms\Form;

final class SomeClass
{
    public function upload()
    {
        $form = new Form();
        $form->addMultiUpload('a', 'a');
    }
}

?>
