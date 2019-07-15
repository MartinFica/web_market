<?php

    // function to add a product to database
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

    // function to change a product in the data base
    function updateRecord($product_id, $name, $description, $price, $quantity){
        global $DB;

        $record = new stdClass();
        $record->id = $product_id;
        $record->name = $name;
        $record->description = $description;
        $record->price = $price;
        $record->quantity = $quantity;
        $DB->update_record('product', $record);
    }

    // function to delete a product from database
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

    // funcition to add product to a cart (creates new entry in details table)
    // NOT READY
    function addToCart($product_id){
        global $DB, $USER;
        $record = new stdClass();
        $record -> sale_id = $sale_id;
        $record -> product_id = $product_id;
        $record -> date = date('Y-m-d H:i');
        $record -> user_id = $USER->id;

        $DB ->insert_record('details',$record);
    }

    // get's all the products that exist in the table products
    function getAllProducts(){
        global $DB;
        $sql = 'SELECT p.id, p.name, p.description, p.price, p.quantity, p.date, p.user_id, u.username
                    FROM {product} p
                    INNER JOIN {user} u
                    ON u.id = p.user_id
                    ORDER BY p.date DESC';

        $sales = $DB->get_records_sql($sql, null);

        return $sales;
    }

    // finds a specific product base on the product_id
    function findProduct($product_id){
        global $DB;

        /*$sql = 'SELECT p.id, p.name, p.description, p.price, p.quantity, p.date, p.user_id, u.username, u.email
                    FROM {product} p
                    INNER JOIN {user} u 
                    ON (u.id = p.user_id)
                    WHERE p.id = ?
                    ';

        $product = $DB->get_records_sql($sql,array($product_id));]*/

        $product = $DB->get_record('product',['id' => $product_id]);

        return $product;
    }

    // gets all the things a user is saling
    function getAllmisventas($OUTPUT){
        $products = getAllProducts();
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

    // lets the user see more details for a specific product
    function comprarProducto($product_id, $url){
        global $DB;
        if($url == 1){
            $url= '/local/web_market/index.php';
        }
        else if($url == 2){
            $url= '/local/web_market/misventas.php';
        }

        $product = findProduct($product_id);

        $udata = $DB->get_record('user',['id' => $product->user_id]);

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
    
            <a href='.new moodle_url($url).' class="btn btn-primary">ATRÁS</a>
            <a href='.new moodle_url('/local/web_market/cart.php').' class="btn btn-primary">AGREGAR AL CARRO</a>';
    }