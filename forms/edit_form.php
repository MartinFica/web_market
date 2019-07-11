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
 * @author  2019 Martin Fica <mafica@alumnos.uai.cl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// You can access the database via the $DB method calls here.
require(__DIR__.'/../../../config.php');

//Necesario para desplegar el formulario
require_once ($CFG->libdir . '/formslib.php');

class edit_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $DB;

        $edit_form = $this->_form;
        $instance = $this->_customdata;
        $id_aviso = $instance['id_aviso'];
        $tipos_aviso = $this->getTiposAviso();
        $dropDownAviso = [];
        foreach ($tipos_aviso as $tipo_aviso){
            $dropDownAviso[$tipo_aviso->id] = $tipo_aviso->nombre;
        }

        $aviso = $DB->get_record('aviso', ['id'=>$id_aviso]);

        //Tipo Aviso Drop Down
        $edit_form->addElement("select", "id_tipo_aviso", get_string('titulo', 'local_diario_mural'), $dropDownAviso);
        // Title input
        $edit_form->addElement ("text", "titulo", get_string('titulo', 'local_diario_mural'));
        $edit_form->setType ("titulo", PARAM_TEXT);

        //Description input
        $edit_form->addElement ('textarea','descripcion', get_string('titulo', 'local_diario_mural'), 'wrap="virtual" rows="5" cols="50"');
        $edit_form->setType ('descripcion', PARAM_RAW);


        // Set action to "add"
        $edit_form->addElement ("hidden", "action", "edit");
        $edit_form->setType ("action", PARAM_TEXT);
        $edit_form->addElement('hidden', 'id_aviso', $id_aviso);
        $edit_form->setType('id_aviso', PARAM_INT);

        $this->add_action_buttons(true);

    }
    //Custom validation should be added here
    function validation($data, $files) {

        global $DB;
        $errors = array();

        $id_tipo_aviso = $data["id_tipo_aviso"];
        $titulo = $data["titulo"];
        $descripcion = $data["descripcion"];

        if(!isset($id_tipo_aviso) && empty($id_tipo_aviso)){

            $errors[$id_tipo_aviso] = "Campo requerido.";
        }

        //!isset($titulo) || empty($titulo) ||
        if( strlen($titulo)== 0){
            $errors[$titulo] = "Campo requerido.";
        }

        if( strlen($descripcion)== 0){
            $errors[$descripcion] = "Campo requerido.";
        }

        return $errors;
    }

    function getTiposAviso(){
        global $DB;

        // Query para onbtener los registro de la tabla Tipo de Aviso
        $sql = 'SELECT id, nombre
				FROM {tipo_aviso}';

        // Retornando registros de la tabla Tipo Aviso
        return  $DB->get_records_sql($sql,null);
    }



}

