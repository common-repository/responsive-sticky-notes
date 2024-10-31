<?php
/*
Plugin Name: Responsive Sticky Notes
Plugin URI: http://www.nantinet.com/
Description: Easily add 'sticky' notes which move with responsive layout changes
Version: 1.02
Author: Richard Mark Watton
Author URI: http://www.nantinet.com
Text Domain: responsive-sticky-notes
License: GPLv2 or later
*/

/*
 Copyright 2016  Richard Watton  (email : richard@nantinet.com)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License, version 2, as
 published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
require_once("defines.php");
require_once("includes.php");
/*
if (is_admin() == true) {

	function responsivestickynotes_activate() {
		
	}
	function responsivestickynotes_deactivate() {
	
	}
	
	//HOOKS
	register_activation_hook(__FILE__, 'responsivestickynotes_activate');
	register_deactivation_hook(__FILE__, 'responsivestickynotes_deactivate');
}
*/

//ACTIONS

add_action( 'wp_enqueue_scripts', 'responsivestickynotes_loadscripts' );

function responsivestickynotes_loadscripts()
{
	$loadNotes = false;
	$showalways = get_option('responsive-sticky-notes_showalways',1); //default is to show always, in case of upgrade

	if ($showalways != 0) $loadNotes = true;
	else {
		$current_user = wp_get_current_user();
		$loadNotes = RESPONSIVESTICKYNOTES_note::can_edit();
	}

	if ($loadNotes==true) {

		add_action( 'wp_head','responsivestickynotes_ajaxurl');
		add_action( 'init', 'responsivestickynotes_create_posttype');
		
		if (!is_admin()&&RESPONSIVESTICKYNOTES_note::can_edit()) 
			add_action('admin_bar_menu', 'responsivestickynotes_add_admin_button', 998);
	

		wp_register_script( 'todo_notes_note', plugins_url( '/js/sticky-notes-note.js', __FILE__ ), array( 'jquery'), false, true );
		wp_register_script( 'todo_notes_single', plugins_url( '/js/sticky-notes-single.js', __FILE__ ), array( 'todo_notes_note'), false, true );
		wp_enqueue_script( 'todo_notes_note' );
		wp_enqueue_script( 'todo_notes_single' );	
	
		wp_localize_script( 'todo_notes_note', 'responsivestickynotes_vars', array(
				//nonce will be available as MyAjax.[nonce name] in javascript
				'postNoteNonce' => wp_create_nonce( 'myajax-post-note-nonce' ),
				'pageId' => get_the_ID(),
				'nextId' => RESPONSIVESTICKYNOTES_note::get_next_ID(),
				'close' => __('Close', 'responsive-sticky-notes'),
				'more' => __('More', 'responsive-sticky-notes'),
				'menu' => __('Menu', 'responsive-sticky-notes'),
				'bin_note' => __('Bin note', 'responsive-sticky-notes'),
				'set_note_color' => __('Set note color', 'responsive-sticky-notes'),
				'untitled_note' => __('Untitled note', 'responsive-sticky-notes'),
				'delete_this_note' => __('Delete this note?', 'responsive-sticky-notes')
		)
		);
		wp_enqueue_style( 'responsivestickynotes_styles',  plugins_url('/css/responsive-sticky-notes.css', __FILE__ ) );
	}
}

function responsivestickynotes_ajaxurl() {
	if (!is_admin()) {
		//ajaxurl is only defined if logged in, by default
		echo '<script type="text/javascript">';
		echo "var ajaxurl = '" . admin_url('admin-ajax.php') ."'";
		echo '</script>';
	}
}

// add a link to the WP Toolbar
function responsivestickynotes_add_admin_button($wp_admin_bar) {

	$args = array(

			'id' => 'addnote',
			'title' => '<span class="responsivestickynotes-button-text">'.__('Add Sticky Note', 'responsive-sticky-notes').'</span><span class="responsivestickynotes-button-text-active">'.__('Stop adding note', 'responsive-sticky-notes').'</span>',
			'href' => '#',
			'meta' => array(
					'title' => __('Add a new Sticky Note', 'responsive-sticky-notes'),
					'onclick' => 'responsivestickynotes_add_note()'
			)
	);
	$wp_admin_bar->add_node($args);
}

function responsivestickynotes_create_posttype() {
	RESPONSIVESTICKYNOTES_note::register_post_type();
}