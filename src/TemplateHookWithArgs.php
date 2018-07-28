<?php
/**
 * {CLASS SUMMARY}
 *
 * Date: 7/27/18
 * Time: 11:31 PM
 * @author Michael Munger <michael@highpoweredhelp.com>
 */

namespace HPHIO\Farret;


class TemplateHookWithArgs extends AbstractTag
{

    public function setup()
    {
        $this->type = AbstractTag::HOOK;
        $this->parseArgs();
    }

    private function trimArg($arg) {

        $arg = str_replace("|",'',$arg);
        $arg = trim($arg);
        return $arg;
    }

    public function parseArgs() {
        $argsPattern = '/(\|(?:{{){0,1}([A-Za-z0-9-]+)(?:}}){0,1})/';
        $matches = [];

        preg_match_all($argsPattern, $this->tag, $matches, PREG_PATTERN_ORDER);

        //Clean up the args.
        $this->args = array_map([$this,'trimArg'], $matches[0]);

    }

    public function getLabel()
    {
        $matches = [];
        $result = (preg_match_all($this->pattern, $this->tag,$matches, PREG_SET_ORDER) !== false);

        return ($result ? $matches[0][1] : false);
    }
}