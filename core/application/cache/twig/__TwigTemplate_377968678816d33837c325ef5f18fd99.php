<?php

/* sidebar/members.html */
class __TwigTemplate_377968678816d33837c325ef5f18fd99 extends Twig_Template
{
    public function display(array $context)
    {
        $this->checkSecurity();
    }

    protected function checkSecurity() {
        $this->env->getExtension('sandbox')->checkSecurity(
            array(),
            array()
        );
    }

}
