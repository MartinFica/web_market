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
        $product_id = $instance['product_id'];

        $product = $DB->get_record('product', ['id'=>$product_id]);

        // Title input
        $edit_form->addElement ("text", "name", get_string('name', 'local_web_market'));
        $edit_form->setType ("name", PARAM_TEXT);

        //Description input
        $edit_form->addElement ('textarea','description', get_string('description', 'local_web_market'), 'wrap="virtual" rows="5" cols="50"');
        $edit_form->setType ('description', PARAM_RAW);


        // Set action to "add"
        $edit_form->addElement ("hidden", "action", "edit");
        $edit_form->setType ("action", PARAM_TEXT);
        $edit_form->addElement('hidden', 'product_id', $product_id);
        $edit_form->setType('product_id', PARAM_INT);

        $this->add_action_buttons(true);

    }

    //Custom validation should be added here
    function validation($data, $files) {

        global $DB;
        $errors = array();

        $name = $data["name"];
        $description = $data["description"];
        $price = $data["price"];
        $quantity = $data ["quantity"];


        if( strlen($name)== 0){
            $errors[$name] = "Campo requerido.";
        }

        if( strlen($description)== 0){
            $errors[$description] = "Campo requerido.";
        }

        if( strlen($price)== 0){
            $errors[$price] = "Campo requerido.";
        }

        if( strlen($quantity)== 0){
            $errors[$quantity] = "Campo requerido.";
        }

        return $errors;
    }

}

