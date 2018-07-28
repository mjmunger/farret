<?php
/**
 * {CLASS SUMMARY}
 *
 * Date: 7/27/18
 * Time: 11:14 PM
 * @author Michael Munger <michael@highpoweredhelp.com>
 */

namespace HPHIO\Farret;


abstract class AbstractTag
{
    protected $pattern;
    protected $tag;
    protected $args = [];
    protected $replacement = null;

    const TAG  = 0;
    const HOOK = 1;

    public function __construct($pattern, $tag)
    {
        $this->pattern = $pattern;
        $this->tag = $tag;
        $this->setup();
    }

    public function getArgs() {
        return $this->args;
    }

    public function setReplacement($replacement) {
        $this->replacement = $replacement;
    }

    abstract function getLabel();
    abstract function setup();
    abstract function fart($dictionary);
    abstract function getTag();
    abstract function getReplacement();
}