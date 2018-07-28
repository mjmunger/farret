<?php
/**
 * {CLASS SUMMARY}
 *
 * Date: 7/27/18
 * Time: 11:15 PM
 * @author Michael Munger <michael@highpoweredhelp.com>
 */

namespace HPHIO\Farret;


class TemplateTag extends AbstractTag
{

    public function getLabel()
    {
        $matches = [];
        preg_match_all($this->pattern, $this->tag,$matches, PREG_SET_ORDER);
        return $matches[0][1];
    }

    public function setup() {
        $this->type = AbstractTag::TAG;
    }

    public function getArgs()
    {
        // TODO: Implement getArgs() method.
    }
}