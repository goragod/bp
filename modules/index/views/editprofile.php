<?php
/**
 * @filesource modules/index/views/editprofile.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Index\Editprofile;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=editprofile
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{
    /**
     * ฟอร์มแก้ไขสมาชิก
     *
     * @param Request $request
     * @param array   $user
     * @param array   $login
     *
     * @return string
     */
    public function render(Request $request, $user, $login)
    {
        // แอดมิน
        $login_admin = Login::isAdmin();
        // form
        $form = Html::create('form', array(
            'id' => 'setup_frm',
            'class' => 'setup_frm',
            'autocomplete' => 'off',
            'action' => 'index.php/index/model/editprofile/submit',
            'onsubmit' => 'doFormSubmit',
            'ajax' => true,
            'token' => true
        ));
        if ($user['active'] == 1) {
            $fieldset = $form->add('fieldset', array(
                'title' => '{LNG_Login information}'
            ));
            $groups = $fieldset->add('groups');
            // username
            $groups->add('text', array(
                'id' => 'register_username',
                'itemClass' => 'width50',
                'labelClass' => 'g-input icon-email',
                'label' => '{LNG_Email}',
                'comment' => '{LNG_Email address used for login or request a new password}',
                'disabled' => $login_admin ? false : true,
                'maxlength' => 255,
                'value' => $user['username'],
                'validator' => array('keyup,change', 'checkUsername', 'index.php/index/model/checker/username')
            ));
            // password, repassword
            $groups = $fieldset->add('groups', array(
                'comment' => '{LNG_To change your password, enter your password to match the two inputs}'
            ));
            // password
            $groups->add('password', array(
                'id' => 'register_password',
                'itemClass' => 'width50',
                'labelClass' => 'g-input icon-password',
                'label' => '{LNG_Password}',
                'placeholder' => '{LNG_Passwords must be at least four characters}',
                'maxlength' => 20,
                'showpassword' => true,
                'validator' => array('keyup,change', 'checkPassword')
            ));
            // repassword
            $groups->add('password', array(
                'id' => 'register_repassword',
                'itemClass' => 'width50',
                'labelClass' => 'g-input icon-password',
                'label' => '{LNG_Confirm password}',
                'placeholder' => '{LNG_Enter your password again}',
                'maxlength' => 20,
                'showpassword' => true,
                'validator' => array('keyup,change', 'checkPassword')
            ));
        }
        $fieldset = $form->add('fieldset', array(
            'title' => '{LNG_Details of} {LNG_User}'
        ));
        $groups = $fieldset->add('groups');
        // name
        $groups->add('text', array(
            'id' => 'register_name',
            'labelClass' => 'g-input icon-customer',
            'itemClass' => 'width50',
            'label' => '{LNG_Name}',
            'value' => $user['name']
        ));
        // sex
        $groups->add('select', array(
            'id' => 'register_sex',
            'labelClass' => 'g-input icon-sex',
            'itemClass' => 'width50',
            'label' => '{LNG_Sex}',
            'options' => Language::get('SEXES'),
            'value' => $user['sex']
        ));
        // หมวดหมู่
        $category = \Index\Category\Model::init(false);
        $a = 0;
        foreach ($category->items() as $k => $label) {
            if (in_array($k, self::$cfg->categories_select)) {
                if (!$category->isEmpty($k)) {
                    if ($a % 2 == 0) {
                        $groups = $fieldset->add('groups');
                    }
                    $a++;
                    $groups->add('select', array(
                        'id' => $k,
                        'labelClass' => 'g-input icon-menus',
                        'itemClass' => 'width50',
                        'label' => $label,
                        'options' => array(0 => '{LNG_Please select}') + $category->toSelect($k),
                        'value' => isset($user[$k]) ? $user[$k] : ''
                    ));
                }
            } else {
                if ($a % 2 == 0) {
                    $groups = $fieldset->add('groups');
                }
                $a++;
                $groups->add('text', array(
                    'id' => $k,
                    'labelClass' => 'g-input icon-menus',
                    'itemClass' => 'width50',
                    'label' => $label,
                    'datalist' => $category->toSelect($k),
                    'value' => isset($user[$k]) ? $user[$k] : '',
                    'text' => ''
                ));
            }
        }
        $groups = $fieldset->add('groups');
        // id_card
        $groups->add('number', array(
            'id' => 'register_id_card',
            'labelClass' => 'g-input icon-profile',
            'itemClass' => 'width50',
            'label' => '{LNG_Identification No.}',
            'maxlength' => 13,
            'value' => $user['id_card'],
            'validator' => array('keyup,change', 'checkIdcard')
        ));
        // birthday
        $groups->add('date', array(
            'id' => 'register_birthday',
            'labelClass' => 'g-input icon-calendar',
            'itemClass' => 'width50',
            'label' => '{LNG_Birthday}',
            'value' => $user['birthday']
        ));
        $groups = $fieldset->add('groups');
        // phone
        $groups->add('text', array(
            'id' => 'register_phone',
            'labelClass' => 'g-input icon-phone',
            'itemClass' => 'width50',
            'label' => '{LNG_Phone}',
            'maxlength' => 32,
            'value' => $user['phone']
        ));
        // address
        $groups->add('text', array(
            'id' => 'register_address',
            'labelClass' => 'g-input icon-address',
            'itemClass' => 'width50',
            'label' => '{LNG_Address}',
            'maxlength' => 150,
            'value' => $user['address']
        ));
        $groups = $fieldset->add('groups');
        // country
        $groups->add('text', array(
            'id' => 'register_country',
            'labelClass' => 'g-input icon-world',
            'itemClass' => 'width33',
            'label' => '{LNG_Country}',
            'datalist' => \Kotchasan\Country::all(),
            'value' => $user['country']
        ));
        // provinceID
        $groups->add('text', array(
            'id' => 'register_province',
            'name' => 'register_provinceID',
            'labelClass' => 'g-input icon-location',
            'itemClass' => 'width33',
            'label' => '{LNG_Province}',
            'datalist' => array(),
            'text' => $user['province'],
            'value' => $user['provinceID']
        ));
        // zipcode
        $groups->add('number', array(
            'id' => 'register_zipcode',
            'labelClass' => 'g-input icon-number',
            'itemClass' => 'width33',
            'label' => '{LNG_Zipcode}',
            'maxlength' => 10,
            'value' => $user['zipcode']
        ));
        if (!empty(self::$cfg->line_official_account) && !empty(self::$cfg->line_channel_access_token) && $user['social'] != 3) {
            // line_uid
            $fieldset->add('text', array(
                'id' => 'register_line_uid',
                'itemClass' => 'item',
                'labelClass' => 'g-input icon-line',
                'label' => '{LNG_LINE user ID}',
                'placeholder' => 'U1234abc...',
                'comment' => '{LNG_Enter the LINE user ID you received when adding friends. Or type userId sent to the official account to request a new user ID. This information is used for receiving private messages from the system via LINE.}',
                'maxlength' => 33,
                'value' => $user['line_uid']
            ));
        }
        if ($login_admin) {
            $fieldset = $form->add('fieldset', array(
                'title' => '{LNG_Other}'
            ));
            // status
            $fieldset->add('select', array(
                'id' => 'register_status',
                'itemClass' => 'item',
                'label' => '{LNG_Member status}',
                'labelClass' => 'g-input icon-star0',
                'disabled' => $login_admin['id'] == $user['id'] ? true : false,
                'options' => self::$cfg->member_status,
                'value' => $user['status']
            ));
            // permission
            $fieldset->add('checkboxgroups', array(
                'id' => 'register_permission',
                'itemClass' => 'item',
                'label' => '{LNG_Permission}',
                'labelClass' => 'g-input icon-list',
                'options' => \Gcms\Controller::getPermissions(),
                'value' => $user['permission']
            ));
        }
        $fieldset = $form->add('fieldset', array(
            'class' => 'submit'
        ));
        // submit
        $fieldset->add('submit', array(
            'class' => 'button save large icon-save',
            'value' => '{LNG_Save}'
        ));
        $fieldset->add('hidden', array(
            'id' => 'register_id',
            'value' => $user['id']
        ));
        // Javascript
        $form->script('initEditProfile("register");');
        $form->script('birthdayChanged("register_birthday", "%s ({LNG_age} %y {LNG_year}, %m {LNG_month} %d {LNG_days})");');
        // คืนค่า HTML
        return $form->render();
    }
}
