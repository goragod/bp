<?php
/**
 * @filesource modules/index/models/editprofile.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Index\Editprofile;

use Gcms\Login;
use Kotchasan\ArrayTool;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=editprofile
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
    /**
     * อ่านข้อมูลสมาชิกที่ $id
     * คืนค่าข้อมูล array ไม่พบคืนค่า false
     *
     * @param int $id
     *
     * @return array|bool
     */
    public static function get($id)
    {
        if (!empty($id)) {
            $user = static::createQuery()
                ->from('user')
                ->where(array('id', $id))
                ->toArray()
                ->first();
            if ($user) {
                // permission
                $user['permission'] = empty($user['permission']) ? array() : explode(',', trim($user['permission'], " \t\n\r\0\x0B,"));
                return $user;
            }
        }
        return false;
    }

    /**
     * บันทึกข้อมูล (editprofile.php)
     *
     * @param Request $request
     */
    public function submit(Request $request)
    {
        $ret = array();
        // session, token, สมาชิก และไม่ใช่สมาชิกตัวอย่าง
        if ($request->initSession() && $request->isSafe() && $login = Login::isMember()) {
            if (Login::notDemoMode($login)) {
                try {
                    // รับค่าจากการ POST
                    $save = array(
                        'username' => $request->post('register_username')->username(),
                        'phone' => $request->post('register_phone')->number(),
                        'name' => $request->post('register_name')->topic(),
                        'sex' => $request->post('register_sex')->topic(),
                        'id_card' => $request->post('register_id_card')->number(),
                        'address' => $request->post('register_address')->topic(),
                        'provinceID' => $request->post('register_provinceID')->number(),
                        'province' => $request->post('register_province')->topic(),
                        'zipcode' => $request->post('register_zipcode')->number(),
                        'birthday' => $request->post('register_birthday')->date(),
                        'country' => $request->post('register_country')->filter('A-Z')
                    );
                    // ชื่อตาราง
                    $table_user = $this->getTableName('user');
                    // database connection
                    $db = $this->db();
                    // แอดมิน
                    $isAdmin = Login::isAdmin();
                    // ตรวจสอบค่าที่ส่งมา
                    $user = self::get($request->post('register_id')->toInt());
                    if ($user) {
                        // ข้อมูลการเข้าระบบ
                        $login_fields = Language::get('LOGIN_FIELDS');
                        if ($isAdmin) {
                            // แอดมิน
                            $permission = $request->post('register_permission', array())->filter('a-z_');
                            $save['permission'] = empty($permission) ? '' : ','.implode(',', $permission).',';
                            // แอดมินและไม่ใช่ตัวเอง สามารถอัปเดต status ได้
                            if ($login['id'] != $user['id']) {
                                $save['status'] = $request->post('register_status')->toInt();
                            }
                        } elseif ($login['id'] != $user['id']) {
                            // ไม่ใช่แอดมินแก้ไขได้แค่ตัวเองเท่านั้น
                            $user = null;
                        } else {
                            // สมาชิก ใช้ username เดิม
                            $save['username'] = $user['username'];
                        }
                    }
                    if ($user) {
                        if ($request->post('register_line_uid')->exists()) {
                            $save['line_uid'] = $request->post('register_line_uid')->filter('Ua-z0-9');
                        }
                        $save['phone'] = $save['phone'] == '' ? null : $save['phone'];
                        $save['id_card'] = $save['id_card'] == '' ? null : $save['id_card'];
                        // ตรวจสอบค่าที่ส่งมา
                        $checking = array(
                            'phone' => $save['phone'],
                            'id_card' => $save['id_card']
                        );
                        foreach (self::$cfg->login_fields as $field) {
                            $k = $field == 'email' || $field == 'username' ? 'username' : $field;
                            if (!empty($save[$k])) {
                                $checking[$k] = $save[$k];
                            } elseif ($user['active'] == 1) {
                                // สามารถเข้าระบบได้ต้องมีข้อมูลการเข้าระบบ
                                $ret['ret_register_'.$k] = 'Please fill in';
                            }
                        }
                        // ตรวจสอบข้อมูลซ้ำ
                        foreach ($checking as $k => $value) {
                            if (!empty($value)) {
                                $search = $db->first($table_user, array($k, $value));
                                if ($search && $search->id != $user['id']) {
                                    $ret['ret_register_'.$k] = Language::replace('This :name already exist', array(':name' => $login_fields[$k]));
                                }
                            }
                        }
                        // password
                        $password = $request->post('register_password')->password();
                        $repassword = $request->post('register_repassword')->password();
                        if (!empty($password) || !empty($repassword)) {
                            if (mb_strlen($password) < 4) {
                                // รหัสผ่านต้องไม่น้อยกว่า 4 ตัวอักษร
                                $ret['ret_register_password'] = 'this';
                            } elseif ($repassword != $password) {
                                // ถ้าต้องการเปลี่ยนรหัสผ่าน กรุณากรอกรหัสผ่านสองช่องให้ตรงกัน
                                $ret['ret_register_repassword'] = 'this';
                            }
                        }
                        if ($save['name'] == '') {
                            // ไม่ได้กรอก ชื่อ
                            $ret['ret_register_name'] = 'Please fill in';
                        }
                        // บันทึก
                        if (empty($ret)) {
                            // หมวดหมู่
                            $category = \Index\Category\Model::init();
                            foreach ($category->items() as $k => $label) {
                                if (in_array($k, self::$cfg->categories_select)) {
                                    if (!$category->isEmpty($k)) {
                                        $save[$k] = $request->post($k)->topic();
                                    }
                                } else {
                                    $save[$k] = $category->save($k, $request->post($k.'_text')->topic());
                                }
                            }
                            if (!empty($password)) {
                                $save['salt'] = \Kotchasan\Password::uniqid();
                                $save['password'] = sha1(self::$cfg->password_key.$password.$save['salt']);
                            }
                            // แก้ไข
                            $db->update($table_user, $user['id'], $save);
                            if ($login['id'] == $user['id']) {
                                // ตัวเอง อัปเดตข้อมูลการ login
                                if ($isAdmin) {
                                    $save['permission'] = $permission;
                                }
                                unset($save['password']);
                                $_SESSION['login'] = ArrayTool::replace($_SESSION['login'], $save);
                                // reload หน้าเว็บ
                                $ret['location'] = 'reload';
                            } else {
                                // ไปหน้าเดิม แสดงรายการ
                                $ret['location'] = $request->getUri()->postBack('index.php', array('module' => 'member', 'id' => null));
                            }
                            // คืนค่า
                            $ret['alert'] = Language::get('Saved successfully');
                            // เคลียร์
                            $request->removeToken();
                        }
                    }
                } catch (\Kotchasan\InputItemException $e) {
                    $ret['alert'] = $e->getMessage();
                }
            }
        }
        if (empty($ret)) {
            $ret['alert'] = Language::get('Unable to complete the transaction');
        }
        // คืนค่าเป็น JSON
        echo json_encode($ret);
    }
}
