<?php
/**
 * {CLASS SUMMARY}
 *
 * Date: 7/26/18
 * Time: 6:54 PM
 * @author Michael Munger <michael@highpoweredhelp.com>
 */

namespace HPHIO\Farret;

use \Exception;

class Notif
{
    private $tagPattern       = '/\{{2} {0,2}([A-Z]+) {0,2}\}{2}/';
    private $hookPattern      = '/\{{1}\%{1} {0,2}([A-Z]+[\|[a-zA-Z0-9-]+]{0,}) {0,2}\%{1}\}{1}/';

    public $templateDirectory = null;
    public $template          = null;
    public $fromName          = null;
    public $fromAddress       = null;
    public $subjectTemplate   = null;
    public $body              = null;

    public $to                = [];
    public $cc                = [];
    public $bcc               = [];
    public $fartDictionary    = [];

    public $hooks             = [];

    public function __construct()
    {
        $this->addHook('DATE', 'getDate');
    }

    public function setTemplateDirectory($directory) {

        if(file_exists($directory) == false) return false;

        $this->templateDirectory = $directory;
        return file_exists($this->templateDirectory);
    }

    /**
     * Loads a template from the template directory.
     * @param $template
     * @throws Exception
     */

    public function loadTemplate($template) {

        $targetTemplate = $this->templateDirectory . "$template.html";

        if(file_exists($this->templateDirectory) == false) throw new Exception("Template directory not set!");
        if(file_exists($targetTemplate)          == false) throw new Exception("Requested template does not exist in $targetTemplate");
        if(is_readable($targetTemplate)          == false) throw new Exception("Requested template is not readable ($targetTemplate)");

        $this->template = file_get_contents($targetTemplate);

        return strlen($this->template) > 0;
    }

    public function setFromName($name) {
        $this->fromName = $name;
    }

    /**
     * Validates an email passed to it, and if valid, sets the from address for the email notification.
     * @param $email
     * @return bool
     * @throws Exception
     */

    public function setFromAddress($email) {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);

        if($email == false) throw new Exception("$email is not a valid email address!");

        $this->fromAddress = $email;

        return true;
    }

    public function setSubjectTemplate($subject) {
        $this->subjectTemplate = $subject;
    }

    /**
     * Adds a Find And Replace Template pair. These will be used to find {{ TEMPLATETAGS }} and perform a substitution.
     * @param $find
     * @param $replace
     */

    public function addFart($find, $replace) {
        $this->fartDictionary[$find] = $replace;
    }

    /**
     * Returns unmatched, unreplaced tags from the template.
     * @return mixed
     * @throws Exception
     */

    public function getTemplateTags() {
        if(strlen($this->template) == 0) throw new Exception("Template not set!");
        return $this->getTags($this->template);
    }

    public function getBodyTags() {
        return $this->getTags($this->body);
    }

    public function getTags($body) {

        $matches = [];
        preg_match_all($this->tagPattern,$body,$matches,PREG_PATTERN_ORDER);
        return $matches[0];
    }

    public function getHooks($body) {
        $matches = [];
        preg_match_all($this->hookPattern,$body,$matches,PREG_PATTERN_ORDER);
        return $matches[0];

    }

    public function makeTag($find) {
        return sprintf("{{%s}}", strtoupper($find));
    }

    public function matchFind($tag, $find) {
        //Remove spaces
        $tag = str_replace(" ", '',$tag);
        //Decorate the find
        $find = $this->makeTag($find);
        return (strcmp($tag,$find) === 0);
    }

    /**
     * @param $body
     * @throws Exception
     */

    private function doFart($body) {
        $tags = $this->getTemplateTags();
        foreach($tags as $tag) {
            foreach($this->fartDictionary as $find => $replace) {
                if($this->matchFind($tag,$find)) $body = str_replace($tag, $replace, $body);
            }
        }
        $tags = $this->getBodyTags();

        if(count($tags) > 0) $this->doFart($this->body);

        return $body;

    }

    public function render() {
        $this->body = $this->doFart($this->template);
    }

    public function addHook($hook, $callback) {
        $this->hooks[$hook] = $callback;
    }

    public function getLabel($subject) {
        $matches = [];

        switch($this->getTagType($subject)) {
            case 'tag':
                preg_match($this->tagPattern, $subject,$matches);
                break;
            case 'hook':
                preg_match($this->hookPattern, $subject,$matches);
                break;
            default:
                return false;
        }

        return $matches[1];
    }

    public function getTagType($tag) {
        if(preg_match($this->tagPattern  , $tag) > 0) return 'tag';
        if(preg_match($this->hookPattern , $tag) > 0) return 'hook';
        return false;
    }

    /**
     * @param $hook
     * @return mixed
     * @throws Exception
     */

    public function renderHook($hook) {

        $args = [];

        $hook = $this->getLabel($hook);

        //1. Check for arguments
        if(strpos($hook, "|") > 0) {
            $buffer = explode("|",$hook);
            $hook   = array_shift($buffer);
            $args   = $buffer;
        }

        //2. Lookup the callback in the hooks dictionary.
        $callback = $this->hooks[$hook];

        if(method_exists($this, $callback) == false) throw new Exception("Hook method does not exist! Cannot execute $callback in " . __FILE__ . ":" .  __LINE__);

        return (count($args) == 0 ? $this->$callback() : $this->$callback($args));

    }

    public function getDate($formatArray) {
        $now = new \DateTime();
        return $now->format($formatArray[0]);
    }

    public function getCurrentMonth() {
        return $this->getDate(["m"]);
    }

    public function getCurrentDay() {
        return $this->getDate(["d"]);
    }

    public function getCurrentYear() {
        return $this->getDate(["Y"]);
    }
}