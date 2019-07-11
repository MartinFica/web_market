<?php

    // READY
    function insertRecord($name, $description, $price, $quantity){
        global $DB, $USER;
        $record = new stdClass();
        $record->name = $name;
        $record->description = $description;
        $record->price = $price;
        $record->quantity = $quantity;
        $record->fecha_creacion = date('Y-m-d H:i');
        $record->id_user = $USER->id;
        // Insert register
        $DB->insert_record('product', $record);
    }

    // Cambiar
    function updateRecord($id_aviso, $titulo, $descripcion, $id_tipo_aviso){
        global $DB;

        $record = new stdClass();
        $record->id = $id_aviso;
        $record->titulo = $titulo;
        $record->descripcion = $descripcion;;
        $record->id_tipo_aviso = $id_tipo_aviso;
        $DB->update_record('aviso', $record);
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

    // Cambiar
    function getAvisosUsuario($id_usuario){
        global $DB;
        $sql = 'SELECT a.id, a.titulo, ta.nombre as Categoria
                FROM {aviso} a
                INNER JOIN {tipo_aviso} ta
                ON ta.id = a.id_tipo_aviso
                INNER JOIN {user} u
                ON u.id = a.id_user
                AND u.id ='. $id_usuario.
                ' ORDER BY a.fecha_creacion DESC';

        var_dump($sql);
        exit;

        $avisos = $DB->get_records_sql($sql, null);
        return $avisos;
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

    // Cambiar
    function findAviso($id_aviso){
        global $DB;
        $aviso = $DB->get_record('aviso', ['id' => $id_aviso]); //select*

        return $aviso;
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

    // Cambiar
    function retornarVistaAviso($id_aviso, $url){


        if($url == 1){
            $url= '/local/diario_mural/index.php';
        }
        else if($url == 2){
            $url= '/local/diario_mural/usuario_avisos.php';
        }

        $aviso = findAviso($id_aviso);
        echo
            '<table style="width:50%">
              <tr>
                <td><strong>Título</strong></td>
                <td>'.$aviso->titulo.'</td>
              </tr>
              <tr>
                <td><strong>Descripción</strong></td>
                <td>'.$aviso->descripcion.'</td>
            </tr>
              <tr>
                <td rowspan="2"><strong>Fecha Publicación</strong></td>
                <td>'.$aviso->fecha_creacion.'</td>
              </tr>
            </table> 
            <br>
    
            <a href='.new moodle_url($url).' class="btn btn-primary">ATRÁS</a>';
    }