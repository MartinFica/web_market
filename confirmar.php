<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 *
 * @package    local
 * @subpackage web_market
 * @copyright  2019  Martin Fica (mafica@alumnos.uai.cl)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    require_once ('forms/viewform.php');
    //require_once ('lib/formslib.php');
    require(__DIR__.'/../../config.php');

    global $DB, $PAGE, $OUTPUT, $USER;

    $url_view= '/local/web_market/view.php';

    $context = context_system::instance();
    $url = new moodle_url($url_view);
    $PAGE->set_url($url);
    $PAGE->set_context($context);
    $PAGE->set_pagelayout("standard");

    // Possible actions -> view, add. Standard is view mode
    $action = optional_param("action", "view", PARAM_TEXT);
    $previous = optional_param("confirmed", "other", PARAM_TEXT);
    $product_id = optional_param("product_id", null, PARAM_INT);
    $sale_id = optional_param("sale_id", null, PARAM_INT);

    require_login();
    if (isguestuser()){
        die();
    }

    $PAGE->set_title(get_string('title', 'local_web_market'));
    $PAGE->set_heading(get_string('heading', 'local_web_market'));

    echo $OUTPUT->header();

    $url1 = new moodle_url('/local/web_market/index.php', [
        'action' => 'view',
        'previous' => 'other',
        'sale_id' => $sale_id
    ]);

    $url2 = new moodle_url('/local/web_market/comprar.php', [
        'action' => 'view',
        'previous' => 'confirmed',
        'product_id' =>  $product_id,
        'sale_id' => $sale_id
    ]);

    echo
        '<table style="width:50%">
            <tr>
                <td><strong>¿Agregar al Carro?</strong></td>
            </tr>
            </table>
            <br>
            <a href='.new moodle_url($url1).' class="btn btn-primary">ATRÁS</a>
            <a href='.new moodle_url($url2).' class="btn btn-primary">AGREGAR AL CARRO</a>';

    echo $OUTPUT->footer();