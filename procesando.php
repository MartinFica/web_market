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
    $detail_id = optional_param("detail_id", null, PARAM_INT);

    require_login();
    if (isguestuser()){
        die();
    }

    $PAGE->set_title(get_string('title', 'local_web_market'));
    $PAGE->set_heading(get_string('heading', 'local_web_market'));

    echo $OUTPUT->header();

    $user_id = $USER->id;
    $record = new stdClass();
    $record->id = $sale_id;
    $record->user_id = $user_id;
    $record->sale_status = '0';
    $DB->update_record('sale', $record);

    $sql = 'SELECT d.id, d.sale_id, d.product_id ,d.quantity, p.name, p.price, u.username, u.email 
                FROM {details} d
                INNER JOIN {product} p
                ON d.product_id = p.id
                INNER JOIN {user} u
                ON p.user_id = u.id
                WHERE d.sale_id = ?
            ';
    $details = $DB->get_records_sql($sql, array($sale_id));

    foreach($details as $detail){
        $id = $detail->id;
        $sale_id = $detail->sale_id;
        $product_id = $detail->product_id;
        $quantity = $detail->quantity;

        $update = new stdClass();
        $update -> id = $id;
        $update -> sale_id = $sale_id;
        $update -> product_id = $product_id;
        $update -> quantity = $quantity;
        $update -> datesold = date('Y-m-d H:i');
        $DB->update_record('details', $update);
    }

    echo '<td><strong>Compra finalizada</strong></td>
            <br>
            <a href='.new moodle_url('/local/web_market/index.php').' class="btn btn-primary">Volver al inicio</a>
        ';

    echo $OUTPUT->footer();