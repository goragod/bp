<?php
/**
 * @filesource modules/bp/controllers/home.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Bp\Home;

use Kotchasan\Http\Request;

/**
 * module=project-home
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{
    /**
     * ฟังก์ชั่นสร้าง card
     *
     * @param Request         $request
     * @param \Kotchasan\Html $card
     * @param array           $login
     */
    public static function addCard(Request $request, $card, $login)
    {
        if ($login) {
            self::renderCard($card, 'icon-users', '{LNG_Family members}', number_format(\Bp\Home\Model::getCount($login['id'])), 'index.php?module=bp-family');
            foreach (\Bp\Home\Model::favorite($login['id']) as $item) {
                self::renderCard($card, 'icon-heart', '{LNG_Blood Pressure}', $item->name, 'index.php?module=bp&amp;id='.$item->id);
            }
        }
    }

    /**
     * ฟังก์ชั่นสร้าง card ในหน้า Home
     *
     * @param Collection $card
     * @param string     $icon
     * @param string     $title
     * @param string     $text
     * @param string     $url
     */
    public static function renderCard($card, $icon, $title, $text, $url)
    {
        $content = '<a class="card-item bp_card" href="'.$url.'">';
        $content .= '<span class="card-subitem '.$icon.' icon"></span>';
        $content .= '<span class="card-subitem">';
        $content .= '<span class="cuttext title" title="'.strip_tags($title).'">'.$title.'</span>';
        $content .= '<b class="cuttext">'.$text.'</b>';
        $content .= '</span></a>';
        $card->set(\Kotchasan\Password::uniqid(), $content);
    }
}
