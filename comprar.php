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
    require(__DIR__.'/../../config.php');

    global $DB, $PAGE, $OUTPUT, $USER;

    $url_view= '/local/web_market/comprar.php';

    $context = context_system::instance();
    $url = new moodle_url($url_view);
    $PAGE->set_url($url);
    $PAGE->set_context($context);
    $PAGE->set_pagelayout("standard");

    // Possible actions -> view, delete. Standard is view mode
    $action = optional_param("action", "view", PARAM_TEXT);
    $previous = optional_param("confirmed", "other", PARAM_TEXT);
    $product_id = optional_param("product_id", null, PARAM_INT);
    $sale_id = optional_param("sale_id", null, PARAM_INT);
    $detail_id = optional_param("detail_id", null, PARAM_INT);

    require_login();
    if (isguestuser()){
    die();
}

    $PAGE->set_title(get_string('title', 'local_web_market'));
    $PAGE->set_heading(get_string('heading', 'local_web_market'));

    echo $OUTPUT->header();

    // Delete the selected record
    if ($action == "delete"){
        $bool = $DB->delete_records("details", array("id" => $detail_id));
        if($bool == true){
            $action = 'view';
            }
        elseif($bool == false){
            print_error("Articulo no existe.");
            }
        $action = 'view';
    }

    if($action == 'view'){

        // Get the user current sale's details
        $sql = 'SELECT d.id, d.sale_id, d.product_id ,d.quantity, p.name, p.price, u.username, u.email 
                    FROM {details} d
                    INNER JOIN {product} p
                    ON d.product_id = p.id
                    INNER JOIN {user} u
                    ON p.user_id = u.id
                    WHERE d.sale_id = ?
        ';
        $details = $DB->get_records_sql($sql, array($sale_id));

        $details_table = new html_table();


        if ($previous = 'confirmed'){
            // Add product to detail
            if (!in_array($product_id,$details) and !is_null($product_id)) {
                $record = new stdClass();
                $record->sale_id = $sale_id;
                $record->product_id = $product_id;
                $record->quantity = 1;
                $record->date = date('Y-m-d H:i');
                $DB->insert_record('details', $record);
            }
            $previous = 'other';
            $sql = 'SELECT d.id, d.sale_id, d.product_id ,d.quantity, p.name, p.price, u.username, u.email 
                    FROM {details} d
                    INNER JOIN {product} p
                    ON d.product_id = p.id
                    INNER JOIN {user} u
                    ON p.user_id = u.id
                    WHERE d.sale_id = ?
            ';
            $details = $DB->get_records_sql($sql, array($sale_id));
        }

        if(sizeof($details) > 0){

            $details_table->head = [
                'Name',
                'Price',
                'Owner',
                'Email'
            ];

            foreach($details as $detail){
                $id = $detail->id;
                /**
                 *Botón eliminar
                 * */
                $delete_url = new moodle_url('/local/web_market/comprar.php',[
                    'action' => 'delete',
                    'detail_id' =>  $detail->id,
                    'previous' => 'other',
                    'sale_id' => $sale_id
                    ]);
                $delete_ic = new pix_icon('t/delete', 'Eliminar');
                $delete_action = $OUTPUT->action_icon(
                    $delete_url,
                    $delete_ic,
                    new confirm_action('¿No lo desea comprar?')
                );

                $details_table->data[] = array(
                    $detail->name,
                    $detail->price,
                    $detail->username,
                    $detail->email,
                    $delete_action
                );
            }
        }

        $top_row = [];
        $top_row[] = new tabobject(
            'products',
            new moodle_url('/local/web_market/index.php', [
                'previous' => 'other',
                'sale_id' => $sale_id
            ]),
            'En Venta'
        );

        $top_row[] = new tabobject(
            'vender',
            new moodle_url('/local/web_market/misventas.php', [
                'previous' => 'other',
                'sale_id' => $sale_id
            ]),
            'Mis Ventas'
        );

        $top_row[] = new tabobject(
            'carro',
            new moodle_url('/local/web_market/comprar.php', [
                'previous' => 'other',
                'sale_id' => $sale_id
            ]),
            'Mi Carro'
        );
    }

    if ($action == 'view'){
        echo $OUTPUT->tabtree($top_row, 'carro');
        if (sizeof($details) == 0){
            echo html_writer::nonempty_tag('h4', 'No tienes items en tu carro.', array('align' => 'left'));
        }else{
            echo html_writer::table($details_table);
            $url = new moodle_url('/local/web_market/procesando.php',[
                'previous' => 'other',
                'sale_id' => $sale_id
            ]);
            echo '<a href='.new moodle_url($url).' class="btn btn-primary">FINALIZAR</a>';
        }
    }

    echo $OUTPUT->footer();

