<?php

require_once('forms/edit_form.php');
require_once('lib/formslib.php');

global $DB, $PAGE, $OUTPUT, $USER;

$edit_form = new edit_form();
$context = context_system::instance();
$url = new moodle_url('/local/web_market/cambiarventa.php');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout("standard");

// Possible actions -> view, add, edit or delete. Standard is view mode
$action = optional_param("action", "edit", PARAM_TEXT);
$id_aviso = optional_param("product_id", null, PARAM_INT);

require_login();
if (isguestuser()) {
    die();
}

$PAGE->set_title(get_string('title', 'local_web_market'));
$PAGE->set_heading(get_string('heading', 'local_web_market'));

echo $OUTPUT->header();

if ($action == 'edit') {

    if ($data = findProduct($product_id)) {

        $edit_form = new edit_form(null, ['product_id'=>$product_id]);
        $edit_form->set_data($data);


        if($new_product = $edit_form->get_data()){
            updateRecord($new_product->name, $new_product->description, $new_product->price, $new_product->quantity);
            $action = 'view';
        }
        //Form processing and displaying is done here
        else if ($edit_form->is_cancelled()) {
            //Handle form cancel operation, if cancel button is present on form
            $action = 'view';
        }
        else{
            $edit_form->display();
        }
    }
}

if ($action == 'view') {
    retornarVistaProduct($OUTPUT);
}

echo $OUTPUT->footer();





















