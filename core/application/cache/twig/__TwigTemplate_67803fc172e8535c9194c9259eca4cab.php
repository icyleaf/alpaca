<?php

/* sidebar/about.html */
class __TwigTemplate_67803fc172e8535c9194c9259eca4cab extends Twig_Template
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
