<?php

namespace Rector\Tests\Symfony4\Rector\MethodCall\FormIsValidRector\Fixture;

use Symfony\Component\Form\Form;

class SkipNegativeIfsController
{
    public function action()
    {
        $form = new Form();

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->doSomething();
        }
    }
}
