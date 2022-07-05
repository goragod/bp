<?php
/**
 * @filesource modules/index/models/email.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Index\Email;

use Kotchasan\Language;

/**
 * ส่งอีเมลสมัครสมาชิก
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\KBase
{
    /**
     * ส่งอีเมลสมัครสมาชิก, ยืนยันสมาชิก
     *
     * @param array $save
     * @param string $password
     *
     * @return string
     */
    public static function send($save, $password)
    {
        $msg = "{LNG_Your registration information}<br>\n<br>\n";
        $msg .= '{LNG_Username} : '.$save['username']."<br>\n";
        $msg .= '{LNG_Password} : '.$password."<br>\n";
        $msg .= '{LNG_Name} : '.$save['name'];
        if (!empty($save['activatecode'])) {
            $url = WEB_URL.'index.php?module=welcome&amp;id='.$save['activatecode'];
            $msg .= "<br>\n{LNG_Please click the link to verify your email address.} : <a href='$url'>".$url.'</a>';
        }
        $msg = Language::trans($msg);
        $subject = '['.self::$cfg->web_title.'] '.Language::get('Welcome new members');
        $err = \Kotchasan\Email::send($save['username'], self::$cfg->noreply_email, $subject, $msg);
        if ($err->error()) {
            // คืนค่า error
            return strip_tags($err->getErrorMessage());
        }
        // success
        return '';
    }
}
