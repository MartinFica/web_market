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

    // Getting user products
    $sql = 'SELECT p.id, p.name, p.description, p.price, p.quantity, p.date, p.user_id, u.username
                FROM {product} p
                INNER JOIN {user} u
                ON u.id = p.user_id
                ORDER BY p.date DESC';
    $products = $DB->get_records_sql($sql, null);

    // Let's user edit his sales
    if($action == 'edit'){

        $products_table = new html_table();

        if(sizeof($products) > 0){

            $products_table->head = [
                'Nombre',
                'Precio',
                'Fecha Publicación',
            ];

            foreach($products as $product){
                /**
                 *Botón eliminar
                 * */
                $delete_url = new moodle_url('/local/web_market/misventas.php', [
                    'action' => 'delete',
                    'product_id' =>  $product->id,

                ]);
                $delete_ic = new pix_icon('t/delete', 'Eliminar');
                $delete_action = $OUTPUT->action_icon(
                    $delete_url,
                    $delete_ic,
                    new confirm_action('¿Ya no vende este articulo?')
                );

                /**
                 *Botón editar
                 * */
                $editar_url = new moodle_url('/local/web_market/cambiarventa.php', [
                    'action' => 'edit',
                    'product_id' =>  $product->id

                ]);
                $editar_ic = new pix_icon('i/edit', 'Editar');
                $editar_action = $OUTPUT->action_icon(
                    $editar_url,
                    $editar_ic
                );

                $products_table->data[] = array(
                    $product->name,
                    $product->price,
                    date('d-m-Y',strtotime($product->date)),
                    $editar_action.' '.$delete_action
                );
            }
        }

        $url_button = new moodle_url("/local/web_market/vender.php", array("action" => "add"));

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
            'misventas',
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


        // Displays all the records, tabs, and options
        echo $OUTPUT->tabtree($top_row, 'misventas');
        if (sizeof(getAllProducts()) == 0){
            echo html_writer::nonempty_tag('h4', 'No estas vendiendo nada.', array('align' => 'left'));
        }
        else{
            echo html_writer::table($products_table);
        }

        echo html_writer::nonempty_tag("div", $OUTPUT->single_button($url_button, "Poner a la Venta"), array("align" => "left"));

        }


    // Delete the selected record
    if ($action == "delete"){

        if(!deleteRegister($product_id)){
            print_error("Articulo no existe.");
        }
        $action = 'view';
    }

    // View his sales
    if($action == 'view'){
        $products_table = new html_table();

        if(sizeof($products) > 0){

            $products_table->head = [
                'Nombre',
                'Precio',
                'Fecha Publicación',
            ];

            foreach($products as $product){
                /**
                 *Botón eliminar
                 * */
                $delete_url = new moodle_url('/local/web_market/misventas.php', [
                    'action' => 'delete',
                    'product_id' =>  $product->id,
                ]);
                $delete_ic = new pix_icon('t/delete', 'Eliminar');
                $delete_action = $OUTPUT->action_icon(
                    $delete_url,
                    $delete_ic,
                    new confirm_action('¿Ya no vende este articulo?')
                );

                /**
                 *Botón editar
                 * */
                $editar_url = new moodle_url('/local/web_market/cambiarventa.php', [
                    'action' => 'edit',
                    'product_id' =>  $product->id

                ]);
                $editar_ic = new pix_icon('i/edit', 'Editar');
                $editar_action = $OUTPUT->action_icon(
                    $editar_url,
                    $editar_ic
                );

                $products_table->data[] = array(
                    $product->name,
                    $product->price,
                    date('d-m-Y',strtotime($product->date)),
                    $editar_action.' '.$delete_action
                );
            }
        }

        $url_button = new moodle_url("/local/web_market/vender.php", array("action" => "add"));

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
            'misventas',
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


        // Displays all the records, tabs, and options
        echo $OUTPUT->tabtree($top_row, 'misventas');
        if (sizeof($products) == 0){
            echo html_writer::nonempty_tag('h4', 'No estas vendiendo nada.', array('align' => 'left'));
        }
        else{
            echo html_writer::table($products_table);
        }

        echo html_writer::nonempty_tag("div", $OUTPUT->single_button($url_button, "Poner a la Venta"), array("align" => "left"));
        }

    echo $OUTPUT->footer();