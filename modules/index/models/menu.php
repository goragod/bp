<?php
/**
 * @filesource modules/index/models/menu.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Index\Menu;

use Gcms\Login;
use Kotchasan\Language;

/**
 * รายการเมนู
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model
{
    /**
     * รายการเมนู
     *
     * @param array $login
     *
     * @return array
     */
    public static function getMenus($login)
    {
        // แอดมิน
        $isAdmin = Login::notDemoMode(Login::isAdmin());
        // สามารถตั้งค่าได้
        $can_config = Login::checkPermission($login, 'can_config');
        // เมนูตั้งค่า
        $settings = array();
        if ($can_config) {
            // สามารถตั้งค่าระบบได้
            $settings['system'] = array(
                'text' => '{LNG_Site settings}',
                'url' => 'index.php?module=system'
            );
            $settings['mailserver'] = array(
                'text' => '{LNG_Email settings}',
                'url' => 'index.php?module=mailserver'
            );
        }
        if ($isAdmin) {
            $settings['linesettings'] = array(
                'text' => '{LNG_LINE settings}',
                'url' => 'index.php?module=linesettings'
            );
            $settings['apis'] = array(
                'text' => 'API',
                'url' => 'index.php?module=apis'
            );
            $settings['modules'] = array(
                'text' => '{LNG_Module}',
                'url' => 'index.php?module=modules'
            );
        }
        if ($can_config) {
            $settings['memberstatus'] = array(
                'text' => '{LNG_Member status}',
                'url' => 'index.php?module=memberstatus'
            );
            $settings['language'] = array(
                'text' => '{LNG_Language}',
                'url' => 'index.php?module=language'
            );
            foreach (Language::get('CATEGORIES', array()) as $k => $label) {
                $settings[$k] = array(
                    'text' => $label,
                    'url' => 'index.php?module=categories&amp;type='.$k
                );
            }
        }
        if ($isAdmin) {
            foreach (Language::get('PAGES', array()) as $src => $label) {
                $settings['write'.$src] = array(
                    'text' => $label,
                    'url' => 'index.php?module=write&amp;src='.$src,
                    'target' => '_self'
                );
            }
            $settings['consentsettings'] = array(
                'text' => '{LNG_Cookie Policy}',
                'url' => 'index.php?module=consentsettings'
            );
        }
        if ($login) {
            return array(
                'home' => array(
                    'text' => '{LNG_Home}',
                    'url' => 'index.php?module=home'
                ),
                'module' => array(
                    'text' => '{LNG_Module}',
                    'submenus' => array()
                ),
                'member' => array(
                    'text' => '{LNG_Users}',
                    'url' => 'index.php?module=member'
                ),
                'report' => array(
                    'text' => '{LNG_Report}',
                    'url' => 'index.php?module=report',
                    'submenus' => array()
                ),
                'settings' => array(
                    'text' => '{LNG_Settings}',
                    'url' => 'index.php?module=settings',
                    'submenus' => $settings
                ),
                'logout' => array(
                    'text' => '{LNG_Logout}',
                    'url' => 'index.php?action=logout'
                )
            );
        }
        // ไม่ได้ login
        return array(
            'home' => array(
                'text' => '{LNG_Home}',
                'url' => 'index.php?module=home',
                'target' => '_self'
            )
        );
    }
}
