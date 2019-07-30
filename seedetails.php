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
 * @package    local
 * @subpackage web_market
 * @author  2019  Martin Fica (mafica@alumnos.uai.cl)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    require_once ('forms/viewform.php');
    //require_once ('lib/formslib.php');
    require(__DIR__.'/../../config.php');

    global $DB, $PAGE, $OUTPUT, $USER;

    $url_view= '/local/web_market/seedetails.php';

    $context = context_system::instance();
    $url = new moodle_url($url_view);
    $PAGE->set_url($url);
    $PAGE->set_context($context);
    $PAGE->set_pagelayout("standard");

    // Possible actions -> view, add. Standard is view mode
    $action = optional_param("action", "view", PARAM_TEXT);
    $product_id = optional_param("product_id", null, PARAM_INT);
    $back_url = optional_param("url", null, PARAM_INT);
    $previous = optional_param("confirmed", "other", PARAM_TEXT);
    $sale_id = optional_param("sale_id", null, PARAM_INT);

    require_login();
    if (isguestuser()){
        die();
    }

    $PAGE->set_title(get_string("title", 'local_web_market'));
    $PAGE->set_heading(get_string("heading", 'local_web_market'));

    echo $OUTPUT->header();

    /*if($url == 1){
        $url= '/local/web_market/index.php';
    }
    else if($url == 2){
        $url= '/local/web_market/misventas.php';
    }*/

    $product = $DB->get_record('product',['id' => $product_id]);

    $udata = $DB->get_record('user',['id' => $product->user_id]);

    $back = new moodle_url('/local/web_market/index.php', [
        'action' => 'view',
        'product_id' =>  $product_id,
        'sale_id' => $sale_id,
        'previous' => 'other'
    ]);

    $add_toChart = new moodle_url('/local/web_market/confirmar.php', [
        'action' => 'view',
        'product_id' =>  $product_id,
        'sale_id' => $sale_id,
        'previous' => 'confirmed'
    ]);

    echo
        '<table style="width:50%">
                  <tr>
                    <td><strong>Name</strong></td>
                    <td>'.$product->name.'</td>
                  </tr>
                  <tr>
                    <td><strong>Description</strong></td>
                    <td>'.$product->description.'</td>
                </tr>
                  <tr>
                    <td><strong>Date put on sale</strong></td>
                    <td>'.$product->date.'</td>
                  </tr>
                  <tr>
                    <td><strong>Price ($)</strong></td>
                    <td>'.$product->price.'</td>
                  </tr>
                 <tr>
                    <td><strong>Owner</strong></td>
                    <td>'.$udata->username.'</td>
                  </tr>
                 <tr>
                    <td rowspan="2"><strong>Email</strong></td>
                    <td>'.$udata->email.'</td>
                  </tr>
                    </table> 
                <br>
    
                <a href='.new moodle_url($back).' class="btn btn-primary">ATRÁS</a>
                <a href='.new moodle_url($add_toChart).' class="btn btn-primary">COMPRAR</a>';

    echo $OUTPUT->footer();