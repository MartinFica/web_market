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

    require_once ('forms/view_form.php');
    require_once ('lib/formslib.php');
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
    $product_id = optional_param("product_id", null, PARAM_INT);

    require_login();
    if (isguestuser()){
        die();
    }

    $PAGE->set_title(get_string('title', 'local_web_market'));
    $PAGE->set_heading(get_string('heading', 'local_web_market'));

    echo $OUTPUT->header();

    if($action == 'view'){

        $products = getAllProducts();
        $products_table = new html_table();

        if(sizeof($products) > 0){

            $products_table->head = [
                'Nombre',
                'Precio',
                'Fecha Publicación',
                'Vendedor'
            ];

            foreach($products as $product){
                /**
                *Botón ver
                * */
                $ver_url = new moodle_url('/local/web_market/view.php', [
                    'action' => 'view',
                    'product_id' =>  $product->id,
                    'url' =>  1,
                ]);

                $ver_ic = new pix_icon('t/hide', 'View');
                $ver_action = $OUTPUT->action_icon(
                    $ver_url,
                    $ver_ic
                );

                $products_table->data[] = array(
                    $product->name,
                    $product->price,
                    date('d-m-Y',strtotime($product->date)),
                    $product->username,
                    $ver_action
                );
            }
        }

        $top_row = [];
        $top_row[] = new tabobject(
            'products',
            new moodle_url('/local/web_market/index.php'),
            ' En Venta'
        );

        $top_row[] = new tabobject(
            'vender',
            new moodle_url('/local/web_market/misventas.php'),
            'Mis Ventas'
        );
    }

    // Displays all the records, tabs, and options
    if ($action == 'view'){
        echo $OUTPUT->tabtree($top_row, 'products');
        if (sizeof(getAllProducts()) == 0){
            echo html_writer::nonempty_tag('h4', 'En este momento no hay articulos a la venta.', array('align' => 'left'));
        }else{
            echo html_writer::table($products_table);
        }
    }

    echo $OUTPUT->footer();