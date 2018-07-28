<?php
/**
 * {CLASS SUMMARY}
 *
 * Date: 7/27/18
 * Time: 11:26 PM
 * @author Michael Munger <michael@highpoweredhelp.com>
 */

namespace HPHIO\Farret;


class TemplateHook extends AbstractTag
{
    public function getLabel()
    {
        $matches = [];
        $result = (preg_match_all($this->pattern, $this->tag,$matches, PREG_SET_ORDER) !== false);

        return ($result ? $matches[0][1] : false);
    }

    public function setup() {
        $this->type = AbstractTag::HOOK;
    }
}