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

    abstract function getLabel();
    abstract function setup();
}