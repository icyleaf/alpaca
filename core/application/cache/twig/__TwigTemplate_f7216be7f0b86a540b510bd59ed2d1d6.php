<?php

/* template/forum.html */
class __TwigTemplate_f7216be7f0b86a540b510bd59ed2d1d6 extends Twig_Template
{
    public function display(array $context)
    {
        $this->checkSecurity();
        // line 1
        echo "<!DOCTYPE html>
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"utf-8\" lang=\"utf-8\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
";
        // line 5
        echo twig_escape_filter($this->env, (isset($context['header']) ? $context['header'] : null), "1");
        echo "
</head>
<body>
<div id=\"wrap\">
\t<div id=\"header\">
\t\t<div class=\"block\">
\t\t\t";
        // line 11
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_iterator_to_array((isset($context['user_nav']) ? $context['user_nav'] : null));
        $countable = is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable);
        $length = $countable ? count($context['_seq']) : null;
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if ($countable) {
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context['_key'] => $context['item']) {
            echo "
\t\t\t";
            echo html::anchor($this->getAttribute((isset($context['item']) ? $context['item'] : null), "link", array(), "any")            ,$this->getAttribute((isset($context['item']) ? $context['item'] : null), "title", array(), "any")            ,$this->getAttribute((isset($context['item']) ? $context['item'] : null), "attr", array(), "any")            );
            // line 12
            echo "
\t\t\t";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if ($countable) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 13
        echo "

\t\t\t<!--<?php \$auth_menu = array();-->
\t\t\t<!--foreach (\$auth_links as \$link => \$item)-->
\t\t\t<!--{-->
\t\t\t\t<!--\$attr = isset(\$item['attr'])?\$item['attr']:array();-->
\t\t\t\t<!--if (empty(\$link))-->
\t\t\t\t<!--{-->
\t\t\t\t\t<!--\$auth_menu[] = \$item['title'];-->
\t\t\t\t<!--}-->
\t\t\t\t<!--elseif (preg_match('/^<(\\w+)>\$/i', \$link, \$match))-->
\t\t\t\t<!--{-->
\t\t\t\t\t<!--\$auth_menu[] = '<'.\$match[1].HTML::attributes(\$attr).'>'.-->
\t\t\t\t\t\t<!--\$item['title'].'</'.\$match[1].'>';-->
\t\t\t\t<!--}-->
\t\t\t\t<!--else-->
\t\t\t\t<!--{-->
\t\t\t\t\t<!--\$auth_menu[] = HTML::anchor(\$link, \$item['title'], \$attr);-->
\t\t\t\t<!--}-->
\t\t\t<!--} ?>-->
\t\t\t<div class=\"auth\">";
        // line 33
        echo twig_escape_filter($this->env, (isset($context['auth_menu']) ? $context['auth_menu'] : null), "1");
        echo "</div>
\t\t\t<div class=\"search\">
\t\t\t<form action=\"/search\">
\t\t\t\t<input type=\"text\" name=\"q\" id=\"header_search_query\" value=\"";
        // line 36
        echo __("Search Topic");
        echo "\"/><input type=\"submit\" value=\"";
        echo __("Go");
        echo "\" />
\t\t\t</form>
\t\t\t</div>
\t\t</div>

\t\t<div class=\"logo\">";
        // line 41
        echo twig_safe_filter((isset($context['logo']) ? $context['logo'] : null));
        echo "</div><!-- /logo -->
\t\t<div class=\"clear\"></div>

\t\t<div id=\"cpanel\">
\t\t\t";
        // line 45
        echo twig_safe_filter((isset($context['menu']) ? $context['menu'] : null));
        echo "
\t\t\t<div class=\"clear\"></div>
\t\t</div><!-- /cpanel -->
\t</div><!-- /header -->

\t<div id=\"container\">
\t\t<!--[if lt IE 7]>
\t\t<div style='border: 1px solid #F7941D; background: #FEEFDA; text-align: center; clear: both; height: 75px; position: relative;margin-bottom: 20px;'>
\t\t<div style='position: absolute; right: 3px; top: 3px; font-family: courier new; font-weight: bold;'><a href='#' onclick='javascript:this.parentNode.parentNode.style.display=\"none\"; return false;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-cornerx.jpg' style='border: none;' alt='Close this notice'/></a></div>
\t\t<div style='width: 640px; margin: 0 auto; text-align: left; padding: 0; overflow: hidden; color: black;'>
\t\t  <div style='width: 75px; float: left;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-warning.jpg' alt='Warning!'/></div>
\t\t  <div style='width: 275px; float: left; font-family: Arial, sans-serif;'>
\t\t\t<div style='font-size: 14px; font-weight: bold; margin-top: 12px;'>You are using an outdated browser</div>
\t\t\t<div style='font-size: 12px; margin-top: 6px; line-height: 12px;'>For a better experience using this site, please upgrade to a modern web browser.</div>
\t\t  </div>
\t\t  <div style='width: 75px; float: left;'><a href='http://www.firefox.com' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-firefox.jpg' style='border: none;' alt='Get Firefox 3.5'/></a></div>
\t\t  <div style='width: 75px; float: left;'><a href='http://www.browserforthebetter.com/download.html' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-ie8.jpg' style='border: none;' alt='Get Internet Explorer 8'/></a></div>
\t\t  <div style='width: 73px; float: left;'><a href='http://www.apple.com/safari/download/' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-safari.jpg' style='border: none;' alt='Get Safari 4'/></a></div>
\t\t  <div style='float: left;'><a href='http://www.google.com/chrome' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-chrome.jpg' style='border: none;' alt='Get Google Chrome'/></a></div>
\t\t</div>
\t\t</div>
\t\t<![endif]-->

\t\t";
        // line 68
        if ((isset($context['sidebar']) ? $context['sidebar'] : null)) {
            echo "
\t\t<?php if (isset(\$sidebar)): ?>
\t \t<div id=\"sidebar\">";
            // line 70
            echo twig_escape_filter($this->env, (isset($context['sidebar']) ? $context['sidebar'] : null), "1");
            echo "</div><!-- /sidebar -->
\t\t<div id=\"content\" class=\"right_column\">
\t\t";
        } else {
            // line 72
            echo "
\t\t<div id=\"content\">
\t\t";
        }
        // line 74
        echo "
\t\t\t";
        // line 75
        if ((isset($context['sidebar']) ? $context['sidebar'] : null)) {
            echo "
\t\t\t";
            // line 76
            echo twig_escape_filter($this->env, (isset($context['content']) ? $context['content'] : null), "1");
            echo "
\t\t\t";
        }
        // line 77
        echo "
\t\t</div><!-- /content -->

\t\t<div class=\"clear\"></div>
\t</div><!-- /container -->

\t<div id=\"footer\">
\t\t<div class=\"left\">
\t\t\t";
        // line 85
        echo twig_safe_filter((isset($context['copyrights']) ? $context['copyrights'] : null));
        echo "
\t\t</div>
\t\t<div id=\"right\">
\t\t\t";
        // line 88
        if ($this->getAttribute((isset($context['config']) ? $context['config'] : null), "execution_time", array(), "any")) {
            echo "
\t\t\t";
            // line 89
            echo __("Rendered in {execution_time}");
            echo ".
\t\t\t";
        }
        // line 90
        echo "
\t\t\t";
        // line 91
        echo __(sprintf("Powered by %s", (isset($context['powered_by']) ? $context['powered_by'] : null)));
        echo "
\t\t</div>
\t\t<div class=\"clear\"></div>
\t</div>
</div>

";
        // line 97
        if ((isset($context['debug']) ? $context['debug'] : null)) {
            echo "
<div id=\"kohana-profiler\">";
            // line 98
            echo twig_safe_filter((isset($context['debuge']) ? $context['debuge'] : null));
            echo "</div>
";
        }
        // line 99
        echo "

";
        // line 101
        if ($this->getAttribute((isset($context['config']) ? $context['config'] : null), "ga_account_id", array(), "any")) {
            echo "
<script type=\"text/javascript\">
var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");
document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));
</script>
<script type=\"text/javascript\">
try {
var pageTracker = _gat._getTracker(\"";
            // line 108
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context['config']) ? $context['config'] : null), "ga_account_id", array(), "any"), "1");
            echo "\");
pageTracker._trackPageview();
} catch(err) {}</script>
";
        }
        // line 111
        echo "
</body>
</html>";
    }

    protected function checkSecurity() {
        $this->env->getExtension('sandbox')->checkSecurity(
            array('for', 'html', 'if'),
            array('escape', 'translate', 'safe', 'format')
        );
    }

}
