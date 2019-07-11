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
 * This file keeps track of upgrades to the evaluaciones block
 *
 * Sometimes, changes between versions involve alterations to database structures
 * and other major things that may break installations.
 *
 * The upgrade function in this file will attempt to perform all the necessary
 * actions to upgrade your older installation to the current version.
 *
 * If there's something it cannot do itself, it will tell you what you need to do.
 *
 * The commands in here will all be database-neutral, using the methods of
 * database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @since 2.0
 * @package blocks
 * @copyright 2019 Martin Fica (mafica@alumnos.uai.cl)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 *
 * @param int $oldversion
 * @param object $block
 */


function xmldb_local_web_market_upgrade($oldversion) {
	global $CFG, $DB;

	$dbman = $DB->get_manager();

    if ($oldversion < 2019070901) {

        // Define table product to be created.
        $table = new xmldb_table('product');

        // Adding fields to table product.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('price', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('quantity', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table product.
        $table->add_key('product', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('user_id', XMLDB_KEY_FOREIGN, ['user_id'], 'user', ['id']);

        // Conditionally launch create table for product.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table sale to be created.
        $table = new xmldb_table('sale');

        // Adding fields to table sale.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('product_id', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('details_id', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table sale.
        $table->add_key('id', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('product_id', XMLDB_KEY_FOREIGN, ['product_id'], 'product', ['id']);
        $table->add_key('details_id', XMLDB_KEY_FOREIGN, ['details_id'], 'details', ['id']);

        // Conditionally launch create table for sale.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table details to be created.
        $table = new xmldb_table('details');

        // Adding fields to table details.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('sale_id', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('quantity', XMLDB_TYPE_INTEGER, '20', null, null, null, null);

        // Adding keys to table details.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('sale_id', XMLDB_KEY_FOREIGN, ['sale_id'], 'sale', ['id']);
        $table->add_key('user_id', XMLDB_KEY_FOREIGN, ['user_id'], 'user', ['id']);

        // Conditionally launch create table for details.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


        // Web_market savepoint reached.
        upgrade_plugin_savepoint(true, 2019070901, 'local', 'web_market');
    }

    if ($oldversion < 2019071001) {

        // Define table product to be created.
        $table = new xmldb_table('product');

        // Adding fields to table product.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('price', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('quantity', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('date', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table product.
        $table->add_key('product', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('user_id', XMLDB_KEY_FOREIGN, ['user_id'], 'user', ['id']);

        // Conditionally launch create table for product.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table sale to be created.
        $table = new xmldb_table('sale');

        // Adding fields to table sale.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sale_status', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table sale.
        $table->add_key('id', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for sale.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table details to be created.
        $table = new xmldb_table('details');

        // Adding fields to table details.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('sale_id', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('product_id', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('quantity', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('datesold', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table details.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('sale_id', XMLDB_KEY_FOREIGN, ['sale_id'], 'sale', ['id']);
        $table->add_key('product_id', XMLDB_KEY_FOREIGN, ['product_id'], 'product', ['id']);

        // Conditionally launch create table for details.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Web_market savepoint reached.
        upgrade_plugin_savepoint(true, 2019071001, 'local', 'web_market');
    }



    return true;
}