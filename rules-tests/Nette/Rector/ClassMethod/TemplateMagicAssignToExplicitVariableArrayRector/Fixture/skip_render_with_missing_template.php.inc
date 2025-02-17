<?php

namespace Rector\Tests\Nette\Rector\ClassMethod\TemplateMagicAssignToExplicitVariableArrayRector\Fixture;

use Nette\Application\UI\Control;

final class SkipRenderWithMissingTemplate extends Control
{
    public function render()
    {
        $this->template->key = 'value';
        $this->template->render();
    }
}
