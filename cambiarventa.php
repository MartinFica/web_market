<?php

//Para incresar a esta página la URL es http://localhost/moodle_2019_06_13/local/diario_mural/create.php

//include simplehtml_form.php
require_once('forms/edit_form.php');
require_once('lib/formslib.php');

global $DB, $PAGE, $OUTPUT, $USER;

$edit_form = new edit_form();
$context = context_system::instance();
$url = new moodle_url('/local/diario_mural/edit.php');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout("standard");

// Possible actions -> view, add, edit or delete. Standard is view mode
$action = optional_param("action", "edit", PARAM_TEXT);
$id_aviso = optional_param("id_aviso", null, PARAM_INT);

require_login();
if (isguestuser()) {
    die();
}

$PAGE->set_title(get_string('title', 'local_diario_mural'));
$PAGE->set_heading(get_string('heading', 'local_diario_mural'));

echo $OUTPUT->header();

if ($action == 'edit') {

    if ($data = findAviso($id_aviso)) {

        $edit_form = new edit_form(null, ['id_aviso'=>$id_aviso]);
        $edit_form->set_data($data);


        if($nuevo_aviso = $edit_form->get_data()){
            updateRecord($id_aviso, $nuevo_aviso->titulo, $nuevo_aviso->descripcion, $nuevo_aviso->id_tipo_aviso);
            /*$record = new stdClass();
            $record->id = $id_aviso;
            $record->titulo = $nuevo_aviso->titulo;
            $record->descripcion = $nuevo_aviso->descripcion;
            $record->fecha_creacion = date('Y-m-d H:i');
            $record->id_tipo_aviso = $nuevo_aviso->id_tipo_aviso;
            $record->id_user = $USER->id;
            $DB->update_record('aviso', $record);*/

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


//Lista de todos los registros de aviso
if ($action == 'view') {
    retornaVistaMisAvisos($OUTPUT);
}

echo $OUTPUT->footer();





















