<?php
class Seguranca
{
    /**
     * Check referer for xss-attacks, if post request
     *
     * @author Max (http://max-3000.com)
     *
     * Example: Security::checkReferer();
     */
    public static function checkReferer()
    {
        if ($_POST) {
            if (!isset($_SERVER['HTTP_REFERER'])) {
                die('Atenção! Ataque XSS sem referencia detectado');
            }
            $parse_url = parse_url($_SERVER['HTTP_REFERER']);
            if (isset($parse_url['host'])) {
                $p = $parse_url['host'];
            } else {
                $p = '';
            }
            if ($p and isset($parse_url['port']) and $parse_url['port'] != 80) {
                $p .= ':' . $parse_url['port'];
            }
            if ($p != $_SERVER['HTTP_HOST']) {
                die('Atenção! Ataque XSS sem referencia detectado!');
            }
        }
    }
    /**
     * Encode password with secret key
     *
     * @param string $password  Password
     * @param string $secretKey Secret key
     *
     * @return string
     *
     * Example Security::encodePassword('admin', 'hacker123');
     */
    public static function encodePassword($password, $secretKey)
    {
        return strrev(md5($password . $secretKey));
    }
    /**
     * Generate random key
     *
     * @param integer $length Length
     *
     * @return string
     *
     * Example: Security::generateRandomKey();
     */
    public static function generateRandomKey($length = 32)
    {
        $randomString = '';
        $whiteList = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($whiteList) - 1;
        while (strlen($randomString) < $length) {
            $randomString .= $whiteList[mt_rand(0, $charactersLength)];
        }
        return $randomString;
    }
    /**
     * Generate security headers for protection again XSS
     *
     * @author Chris Fasel (https://github.com/crypto-scythe)
     *
     * return void;
     */
    public static function generateSecurityHeaders()
    {
        $headers = [
            'X-Content-Type-Options'  => 'nosniff',
            'X-Frame-Options'         => 'SAMEORIGIN',
            'X-XSS-Protection'        => '1; mode=block',
            'Content-Security-Policy' => "default-src 'self' 'unsafe-eval' 'unsafe-inline' https://*.google.com https://*.googleapis.com https://*.gstatic.com https://*.google-analytics.com; img-src *",
        ];
        foreach ($headers as $header => $value) {
            header($header . ': ' . $value, true);
        }
    }
}