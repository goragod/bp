<?php
/**
 * @filesource modules/bp/controllers/profile.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Bp\Profile;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=bp-profile
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{
    /**
     * แก้ไขข้อมูลส่วนตัวสมาชิก
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        // ข้อความ title bar
        $this->title = Language::get('Profile');
        // เลือกเมนู
        $this->menu = 'family';
        // สมาชิก
        $login = Login::isMember();
        // อ่านข้อมูลสมาชิก
        if ($login && $user = \Bp\Profile\Model::get($request->request('id')->toInt(), $login)) {
            $title = Language::get($user->id == 0 ? 'Add' : 'Edit');
            $this->title = $title.' '.$this->title;
            // แสดงผล
            $section = Html::create('section', array(
                'class' => 'content_bg'
            ));
            // breadcrumbs
            $breadcrumbs = $section->add('div', array(
                'class' => 'breadcrumbs'
            ));
            $ul = $breadcrumbs->add('ul');
            $ul->appendChild('<li><a href="'.WEB_URL.'index.php" class="icon-heart">{LNG_Blood Pressure}</a></li>');
            $ul->appendChild('<li><a href="{BACKURL?module=bp-family&id=0}">{LNG_Family members}</a></li>');
            $ul->appendChild('<li><span>'.$title.'</span></li>');
            $section->add('header', array(
                'innerHTML' => '<h2 class="icon-profile">'.$this->title.'</h2>'
            ));
            // แสดงฟอร์ม
            $section->appendChild(\Bp\Profile\View::create()->render($request, $user, $login));
            // คืนค่า HTML
            return $section->render();
        }
        // 404
        return \Index\Error\Controller::execute($this, $request->getUri());
    }
}
