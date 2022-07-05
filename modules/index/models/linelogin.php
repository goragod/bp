<?php
/**
 * @filesource modules/index/models/linelogin.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Index\Linelogin;

use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * LINE Login
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
    /**
     * รับข้อมูลที่ส่งมาจากการเข้าระบบด้วยบัญชี LINE
     *
     * @param Request $request
     * @param array $user
     *
     * @return array|string สำเร็จคืนค่า Array ข้อมูลสมาชิก ไม่สำเร็จคืนค่าข้อความผิดพลาด
     */
    public static function chklogin(Request $request, $user)
    {
        // Model
        $model = static::create();
        // db
        $db = $model->db();
        // table
        $user_table = $model->getTableName('user');
        // userId or email
        $username = empty($user['email']) ? $user['sub'] : $user['email'];
        // ตรวจสอบสมาชิกกับ db
        $search = $db->createQuery()
            ->from('user')
            ->where(array(
                array('username', $username),
                array('line_uid', $user['sub'])
            ), 'OR')
            ->toArray()
            ->first();
        if ($search === false) {
            // ยังไม่เคยลงทะเบียน, ลงทะเบียนใหม่
            if (self::$cfg->demo_mode) {
                $permissions = array_keys(\Gcms\Controller::getPermissions());
                unset($permissions['can_config']);
            } else {
                $permissions = array();
            }
            $save = \Index\Register\Model::execute($model, array(
                'username' => $username,
                'password' => \Kotchasan\Password::uniqid(),
                'name' => $user['name'],
                // LINE
                'social' => 3,
                'line_uid' => $user['sub'],
                'visited' => 1,
                'lastvisited' => time(),
                // โหมดตัวอย่างเป็นแอดมิน, ไม่ใช่เป็นสมาชิกทั่วไป
                'status' => self::$cfg->demo_mode ? 1 : 0,
                'token' => \Kotchasan\Password::uniqid(40),
                'active' => 1
            ), $permissions);
        } elseif ($search['social'] == 3) {
            // สมาชิก LINE
            if ($search['active'] == 1) {
                // เคยเยี่ยมชมแล้ว อัปเดตการเยี่ยมชม
                $save = $search;
                ++$save['visited'];
                $save['lastvisited'] = time();
                $save['ip'] = $request->getClientIp();
                $save['token'] = \Kotchasan\Password::uniqid(40);
                $save['line_uid'] = $user['sub'];
                // อัปเดต
                $db->update($user_table, $search['id'], $save);
                $save['permission'] = explode(',', trim($save['permission'], " \t\n\r\0\x0B,"));
            } else {
                // ไม่ใช่สมาชิกปัจจุบัน ไม่สามารถเข้าระบบได้
                $save = Language::get('Can not be performed this request. Because they do not find the information you need or you are not allowed');
            }
        } else {
            if ($username == $search['username']) {
                // อัปเดตสมาชิกถ้า username ตรงกันกับบัญชีไลน์
                $db->update($user_table, $search['id'], array(
                    'line_uid' => $user['sub']
                ));
            }
            // ไม่สามารถ login ได้ เนื่องจากมี email อยู่ก่อนแล้ว
            $save = Language::replace('This :name already exist', array(':name' => Language::get('Username')));
        }
        if (is_array($save)) {
            // ส่งข้อความ ยินดีต้อนรับ
            $message = Language::replace('Welcome %s, login complete', array('%s' => $save['name']));
            \Gcms\Line::sendTo($save['username'], $message);
        }
        return $save;
    }

    /**
     * คืนค่า URL สำหรับการเข้าระบบด้วย LINE
     *
     * @param string $ret_url
     *
     * @return string
     */
    public static function url($ret_url)
    {
        $params = array(
            'response_type' => 'code',
            'client_id' => self::$cfg->line_channel_id,
            'redirect_uri' => str_replace('www.', '', WEB_URL.'line/callback.php'),
            'state' => base64_encode($ret_url),
            'scope' => 'profile openid email',
            'nonce' => uniqid(),
            'openExternalBrowser' => 1
        );
        return 'https://access.line.me/oauth2/v2.1/authorize?'.http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }
}
