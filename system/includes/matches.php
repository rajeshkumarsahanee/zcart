<?php

/**
 * Helper class to remove the need to use eval to replace $matches[] in query strings.
 * Taken From Wordpress
 */
class MatchesMapRegex {

    private $_matches;
    public $output;
    private $_subject;
    public $_pattern = '(\$matches\[[1-9]+[0-9]*\])'; // magic number

    public function __construct($subject, $matches) {
        $this->_subject = $subject;
        $this->_matches = $matches;
        $this->output = $this->_map();
    }

    public static function apply($subject, $matches) {
        $oSelf = new MatchesMapRegex($subject, $matches);
        return $oSelf->output;
    }

    private function _map() {
        $callback = array($this, 'callback');
        return preg_replace_callback($this->_pattern, $callback, $this->_subject);
    }

    public function callback($matches) {
        $index = intval(substr($matches[0], 9, -1));
        return ( isset($this->_matches[$index]) ? urlencode($this->_matches[$index]) : '' );
    }

}
