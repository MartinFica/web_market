<?php

    // Ready? No guarda bn los datos
    function insertRecord($name, $description, $price, $quantity){
        global $DB, $USER;
        $record = new stdClass();
        $record->name = $name;
        $record->description = $description;
        $record->price = $price;
        $record->quantity = $quantity;
        $record->date = date('Y-m-d H:i');
        $record->user_id = $USER->id;
        // Insert register
        $DB->insert_record('product', $record);
    }

    // Revisar
    function updateRecord($name, $description, $price, $quantity){
        global $DB;

        $record = new stdClass();
        $record->name = $name;
        $record->description = $description;
        $record->price = $price;
        $record->quantity = $quantity;
        $DB->update_record('product', $record);
    }

    // READY
    function deleteRegister($product_id){
        global $DB, $action;
        //Borrar venta
        if ($DB->delete_records("product", array("id" => $product_id))){
            $action = 'view';
        }
        else {
            return false;
        }
        return true;
    }

    // Revisar
    function getAvisosUsuario($id_usuario){
        global $DB;
        $sql = 'SELECT p.id, p.name, p.description, p.price, p.quantity, p.date, p.user_id
                FROM {product} p
                INNER JOIN {user} u
                ON u.id = p.user_id
                AND u.id ='. $id_usuario.
                ' ORDER BY p.date DESC';

        var_dump($sql);
        exit;

        $products = $DB->get_records_sql($sql, null);
        return $products;
    }

    // READY
    function getAllProducts(){
        global $DB;
        $sql = 'SELECT p.id, p.name, p.description, p.price, p.quantity, p.date, p.user_id
                    FROM {product} p
                    ORDER BY p.date DESC';

        $sales = $DB->get_records_sql($sql, null);

        return $sales;
    }

    // Revisar
    function findProduct($product_id){
        global $DB;
        $product = $DB->get_record('product', ['id' => $product_id]); //select*

        return $product;
    }

    // READY
    function getAllmisventas($OUTPUT){
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

                /**
                 *Botón ver
                 * */
                $ver_url = new moodle_url('/local/web_market/view.php', [
                    'action' => 'view',
                    'product_id' =>  $product->id,
                    'url' => 2

                ]);
                $ver_ic = new pix_icon('t/hide', 'Ver');
                $ver_action = $OUTPUT->action_icon(
                    $ver_url,
                    $ver_ic
                );

                $products_table->data[] = array(
                    $product->nombre,
                    $product->precio,
                    date('d-m-Y',strtotime($product->fecha)),
                    $ver_action.' '.$editar_action.' '.$delete_action
                );
            }
        }

        $url_button = new moodle_url("/local/web_market/vender.php", array("action" => "add"));

        $top_row = [];
        $top_row[] = new tabobject(
            'products',
            new moodle_url('/local/web_market/index.php'),
            ' En Venta'
        );
        $top_row[] = new tabobject(
            'misventas',
            new moodle_url('/local/web_market/misventas.php'),
            'Mis Ventas'
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

    // Revisar
    function retornarVistaProduct($product_id, $url){

        if($url == 1){
            $url= '/local/web_market/index.php';
        }
        else if($url == 2){
            $url= '/local/web_market/misventas.php';
        }

        $product = findProduct($product_id);
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
                <td rowspan="2"><strong>Date put on sale</strong></td>
                <td>'.$produt->date.'</td>
              </tr>
            </table> 
            <br>
    
            <a href='.new moodle_url($url).' class="btn btn-primary">ATRÁS</a>';
    }