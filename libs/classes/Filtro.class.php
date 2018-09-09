<?php 
class Filtro
{
    /**
     * @author Max (http://maxsite.org)
     * @author Alexander Schilling
     *
     * htmlspecialchars - Convert special characters to HTML entities
     * blacklist - Remove danger chars
     * trim - Strip whitespace
     * stripslashes - Un-quotes a quoted string
     * strip_tags - Strip HTML and PHP tags
     * integer or int - Convert to number
     *
     * base - htmlspecialchars|blacklist|trim
     *
     * @param string $text      Input string
     * @param string $rules
     * @param array  $blacklist Blacklist
     *
     * @return int|mixed|string
     */
    public static function text(
        $text, $rules = 'base', array $blacklist = ['\\', '|', '/', '?', '%', '*', '`', '<', '>']
    ) {
        if (!$text) {
            return $text;
        }
        $rules = explode('|', $rules);
        $rules = array_map('trim', $rules);
        $rules = array_unique($rules);
        foreach ($rules as $rule) {
            if ($rule == 'htmlspecialchars' or $rule == 'base') {
                $text = htmlspecialchars($text);
            }
            if ($rule == 'blacklist' or $rule == 'base') {
                $text = str_replace($blacklist, '', $text);
            }
            if ($rule == 'trim' or $rule == 'base') {
                $text = trim($text);
            }
            if ($rule == 'stripslashes') {
                $text = stripslashes($text);
            }
            if ($rule == 'strip_tags') {
                $text = strip_tags($text);
            }
            if ($rule == 'int' or $rule == 'integer') {
                $text = intval($text);
            }
        }
        return $text;
    }
    /**
     * Convert special characters to HTML entities
     * Method to prevent XSS injections
     *
     * @param string  $value        - String to convert
     * @param boolean $doubleEncode - Encode existing entities
     *
     * @return string
     */
    public static function chars($value, $doubleEncode = true)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'utf-8', $doubleEncode);
    }
}