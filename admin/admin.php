<?php
if ( ! defined( 'RESPONSIVESTICKYNOTES_VERSION' ) ) exit; // Exit if accessed directly

//ACTIONS
add_action('admin_menu', 'responsivestickynotes_plugin_menu' );
add_action('admin_init', 'responsivestickynotes_admin_init');
/*
function responsivestickynotes_setupDB() {


	global $wpdb;


	$table_name = $wpdb->prefix . RESPONSIVESTICKYNOTES_TABLE;
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
	id mediumint(9) NOT NULL,
	created datetime DEFAULT '0000-00-00 00:00:00',
	updated datetime DEFAULT '0000-00-00 00:00:00',
	name tinytext NOT NULL,
	text text NOT NULL,
	elementchain text NOT NULL
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}
*/
function responsivestickynotes_admin_init(){
	register_setting( 'responsivestickynotes_options_group', 'responsivestickynotes_options', 'responsivestickynotes_options_validate' );
	add_settings_section('responsivestickynotes_main', 'Main Settings', 'responsivestickynotes_section_text', 'responsivestickynotes');
	add_settings_field('responsivestickynotes_text_string', 'responsivestickynotes Text Input', 'responsivestickynotes_setting_string', 'responsivestickynotes', 'responsivestickynotes_main');
	//responsivestickynotes_setupDB();
	
	//set up the first post
}

function responsivestickynotes_plugin_menu() {
	add_menu_page( "Sticky Notes", "Sticky Notes", "manage_options", RESPONSIVESTICKYNOTES_note::slug, "responsivestickynotes_display_admin_page", 'dashicons-list-view', 90 );
}


//admin list table
//taken from Custom List Table Example, with gratitude!

/*  Copyright 2015  Matthew Van Andel  (email : matt@mattvanandel.com)

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
/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary. In this tutorial, we are
 * going to use the WP_List_Table class directly from WordPress core.
 *
 * IMPORTANT:
 * Please note that the WP_List_Table class technically isn't an official API,
 * and it could change at some point in the distant future. Should that happen,
 * I will update this plugin with the most current techniques for your reference
 * immediately.
 *
 * If you are really worried about future compatibility, you can make a copy of
 * the WP_List_Table class (file path is shown just below) to use and distribute
 * with your plugins. If you do that, just remember to change the name of the
 * class to avoid conflicts with core.
 *
 */





/************************** CREATE A PACKAGE CLASS *****************************
 *******************************************************************************
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 *
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 *
 * Our theme for this list table is going to be movies.
 */
class responsivestickynotes_List_Table extends WP_List_Table_copy {


	/** ************************************************************************
	 * REQUIRED. Set up a constructor that references the parent constructor. We
	 * use the parent reference to set some default configs.
	 ***************************************************************************/
	function __construct(){
		global $status, $page;

		//Set parent defaults
		parent::__construct( array(
				'singular'  => 'Responsive Sticky Note',     //singular name
				'plural'    => 'Responsive Sticky Notes',    //plural name
				'ajax'      => false        //does this table support ajax?
		) );

	}


	/** ************************************************************************
	 * Recommended. This method is called when the parent class can't find a method
	 * specifically build for a given column. Generally, it's recommended to include
	 * one method for each column you want to render, keeping your package class
	 * neat and organized. For example, if the class needs to process a column
	 * named 'title', it would first see if a method named $this->column_title()
	 * exists - if it does, that method will be used. If it doesn't, this one will
	 * be used. Generally, you should try to use custom column methods as much as
	 * possible.
	 *
	 * Since we have defined a column_title() method later on, this method doesn't
	 * need to concern itself with any column with a name of 'title'. Instead, it
	 * needs to handle everything else.
	 *
	 * For more detailed insight into how columns are handled, take a look at
	 * WP_List_Table::single_row_columns()
	 *
	 * @param array $item A singular item (one full row's worth of data)
	 * @param array $column_name The name/slug of the column to be processed
	 * @return string Text or HTML to be placed inside the column <td>
	 **************************************************************************/
	function column_default($item, $column_name){
		return $item[$column_name];
	}


	/** ************************************************************************
	 * Recommended. This is a custom column method and is responsible for what
	 * is rendered in any column with a name/slug of 'title'. Every time the class
	 * needs to render a column, it first looks for a method named
	 * column_{$column_title} - if it exists, that method is run. If it doesn't
	 * exist, column_default() is called instead.
	 *
	 * This example also illustrates how to implement rollover actions. Actions
	 * should be an associative array formatted as 'slug'=>'link html' - and you
	 * will need to generate the URLs yourself. You could even ensure the links
	 *
	 *
	 * @see WP_List_Table::::single_row_columns()
	 * @param array $item A singular item (one full row's worth of data)
	 * @return string Text to be placed inside the column <td> (movie title only)
	 **************************************************************************/
	function column_name($item){

		//Build row actions
		$actions = array(
				'edit'      => sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>',$_REQUEST['page'],'edit',$item['id']),
				'delete'    => sprintf("<a onclick='return confirm(\"".__('Delete this note?','responsive-sticky-notes')."\")' href=\"?page=%s&action=%s&id=%s\">Delete</a>", $_REQUEST['page'],'delete',$item['id'])
	//'rename'  => sprintf('<a onclick="return responsivestickynotes_confirmdelete()" href="?page=%s&action=%s&id=%s">Rename</a>',$_REQUEST['page'],'delete',$item['id']),
		);

		//Return the title contents
		$name = $item['name'];
		//return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
		//		/*$1%s*/ $name,
		//		/*$2%s*/ $item['id'],
		//		/*$3%s*/ $this->row_actions($actions)
		//		);
		return sprintf('%1$s %2$s',
				/*$1%s*/ $name,
				/*$3%s*/ $this->row_actions($actions)
				);
	}


	/** ************************************************************************
	 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
	 * is given special treatment when columns are processed. It ALWAYS needs to
	 * have it's own method.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 * @param array $item A singular item (one full row's worth of data)
	 * @return string Text to be placed inside the column <td> (movie title only)
	 **************************************************************************/
	function column_cb($item){
		return sprintf(
				'<input type="checkbox" name="%1$s[]" value="%2$s" />',
				/*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label
				/*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
				);
	}


	/** ************************************************************************
	 * REQUIRED! This method dictates the table's columns and titles. This should
	 * return an array where the key is the column slug (and class) and the value
	 * is the column's title text. If you need a checkbox for bulk actions, refer
	 * to the $columns array below.
	 *
	 * The 'cb' column is treated differently than the rest. If including a checkbox
	 * column in your table you must create a column_cb() method. If you don't need
	 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
	 **************************************************************************/
	function get_columns(){
		$columns = array(
				'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
				'name'     	=> __('Name', 'responsive-sticky-notes'),
				'page' 		=> __('Page', 'responsive-sticky-notes'),
				'created'   => __('Created', 'responsive-sticky-notes')
		);
		return $columns;
	}


	/** ************************************************************************
	 * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
	 * you will need to register it here. This should return an array where the
	 * key is the column that needs to be sortable, and the value is db column to
	 * sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 *
	 * This method merely defines which columns should be sortable and makes them
	 * clickable - it does not handle the actual sorting. You still need to detect
	 * the ORDERBY and ORDER querystring variables within prepare_items() and sort
	 * your data accordingly (usually by modifying your query).
	 *
	 * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
	 **************************************************************************/
	function get_sortable_columns() {
		$sortable_columns = array(
				'name'     => array('name',false),     //true means it's already sorted
				'page'    => array('page',false),
				'created'  => array('created',false)
		);
		return $sortable_columns;
	}


	/** ************************************************************************
	 * Optional. If you need to include bulk actions in your list table, this is
	 * the place to define them. Bulk actions are an associative array in the format
	 * 'slug'=>'Visible Title'
	 *
	 * If this method returns an empty value, no bulk action will be rendered. If
	 * you specify any bulk actions, the bulk actions box will be rendered with
	 * the table automatically on display().
	 *
	 * Also note that list tables are not automatically wrapped in <form> elements,
	 * so you will need to create those manually in order for bulk actions to function.
	 *
	 * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
	 **************************************************************************/
	//this is unused, the actions are defined in column_name(), where the ID is availale
	function get_bulk_actions() {
		$actions = array(
				'delete'    => 'Delete'
		);
		return $actions;
	}


	/** ************************************************************************
	 * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
	 * For this example package, we will handle it in the class to keep things
	 * clean and organized.
	 *
	 * @see $this->prepare_items()
	 **************************************************************************/
	function process_bulk_action() {

		if (isset($_REQUEST['responsivestickynote'])) {
			$responsivestickynotes = $_REQUEST['responsivestickynote'];

			if( 'delete'===$this->current_action() ) {
				foreach ($responsivestickynotes as $responsivestickynote ) {
					RESPONSIVESTICKYNOTES_note::delete_note((int)$responsivestickynote);
				}
			}
		}
		if (isset($_REQUEST['id'])) {
			$id = $_REQUEST['id'];
			if( 'delete'===$this->current_action() ) {
				RESPONSIVESTICKYNOTES_note::delete_note((int)$id);
			}
			
		}
	}


	/** ************************************************************************
	 * REQUIRED! This is where you prepare your data for display. This method will
	 * usually be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args(), although the following properties and methods
	 * are frequently interacted with here...
	 *
	 * @global WPDB $wpdb
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 **************************************************************************/
	function prepare_items() {
		global $wpdb; //This is used only if making any database queries

		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = 10;


		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();


		/**
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array($columns, $hidden, $sortable);


		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();


		/**
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example
		 * package slightly different than one you might build on your own. In
		 * this example, we'll be using array manipulation to sort and paginate
		 * our data. In a real-world implementation, you will probably want to
		 * use sort and pagination data to build a custom query instead, as you'll
		 * be able to use your precisely-queried data immediately.
		 */
		
		$data = RESPONSIVESTICKYNOTES_note::get_notes(NULL, false);

		/**
		 * This checks for sorting input and sorts the data in our array accordingly.
		 *
		 * In a real-world situation involving a database, you would probably want
		 * to handle sorting by passing the 'orderby' and 'order' values directly
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary.
		 */
		function usort_reorder($a,$b){
			$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'name'; //If no sort, default to title
			$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
			$result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
			return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
		}
		usort($data, 'usort_reorder');
			




		/**
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();

		/**
		 * REQUIRED for pagination. Let's check how many items are in our data array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
		$total_items = count($data);


		/**
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to
		 */

		$data = array_slice($data,(($current_page-1)*$per_page),$per_page);



		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$this->items = $data;


		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
				'total_items' => $total_items,                  //WE have to calculate the total number of items
				'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
				'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
		) );
	}

	function editNote() {
		echo "here";
	}


}

function responsivestickynotes_section_text() {
	echo '<p>Main description of this section here.</p>';
}
function responsivestickynotes_setting_string() {
	$options = get_option('responsivestickynotes_options');
	echo "<input id='responsivestickynotes_text_string' name='responsivestickynotes_options[text_string]' size='40' type='text' value='{$options['text_string']}' />";
}





function responsivestickynotes_options_validate($plugin_options) {
	echo "validated!";
	wp_die();
	return $plugin_options;
}
function responsivestickynotes_display_admin_page() {
	global $wpdb;
	//Create an instance of our package class...
	$listTable = new responsivestickynotes_List_Table();
	//Fetch, prepare, sort, and filter our data...
	?>

<?php 
	//checkbox in just a few lines of code... how easy is that
	$showalways = get_option('responsive-sticky-notes_showalways', 1);
	if (isset($_REQUEST['showalways_submit'])) {
		$showalways = isset($_REQUEST['showalways']) && $_REQUEST['showalways']=='on' ? 1 : 0;
		update_option('responsive-sticky-notes_showalways', $showalways);
	}
	?>
	<form method="post" action="<?php echo esc_url( add_query_arg( array('page' => 'responsive-sticky-notes'), menu_page_url( 'responsivestickynotes', false ) ) ); ?>">
	<table><tr><td>Show notes when not logged in: <input type="checkbox" name="showalways" <?php checked( 0 != $showalways ); ?> /></td><td><?php submit_button('Apply',"submit","showalways_submit"); ?></td></tr></table>
		
		<?php
	$action = null;
	
	if (isset($_REQUEST['action'])) {
		$action = $_REQUEST['action'];
	}
	if (isset($_REQUEST['submit'])) {
		$action = 'update';
	}
	if ($action == 'help') {

		?> 
				<div class="wrap">
				 <div id="icon-edit" class="icon32"></div>
				<h1><?php echo esc_html( __( 'Sticky Notes - help', 'responsive-sticky-notes' ) );?></h1>
				<div style="height: 10px"></div>
				 			
					
		
		<form method="post" action="<?php echo esc_url( add_query_arg( array('page' => 'responsive-sticky-notes'), menu_page_url( 'responsivestickynotes', false ) ) ); ?>">
<p>This plugin lets you add 'sticky' notes to any post or non-admin page. The notes are attached to HTML elements within the page, so are ideal for text annotations or to add extra information to images, etc., and will move with responsive layout changes so they never vanish off the edge of the screen.  
</p>
<p>Sticky notes can be added to any post or non-admin page. To add a note, leave the admin area and go to the page you want to add a note to. Then click the 'Add Sticky Note' button in the top admin bar, and move the cursor over the page. As you move the cursor, page elements are highlighted with an outline. Click again to add a note at this position.</p>
<p>Notes are attached to the beginning of a page element, i.e. at the top left corner of a &ltp&gt;, &ltdiv&gt;, etc. A note can be attached to anything on a page.</p>
<p>Once you have added a note, click it to open it. Then type text as required - it is saved as you type. 
<p>To move a note, click and drag it to another position on the page. If the note is open, click and drag from top, between the Close and Menu icons. The The page will scroll automatically when the note is close to an edge (you may need to move the cursor a little to get the page to scroll).</p>
<p>To change the note color, delete the note, or go to the edit page, click the 'menu' icon in the top right corner.

<p>The note 'tooltip' is set to the first few words of the note, unless the note has a title. To set a title, open the note menu then click the ... icon, to go to the note edit screen.</p>

<p>If you delete a page element to which a note is attached, the note will 'float' to the top left of the window. This may also happen if you change an ID somewhere on the page. Floated notes can be dragged to any other page element, as normal.</p>

<p>Notes are unique to the page you stick them on, so a note attached to a banner, footer or other site-wide element will only appear on one page. Notes cannot be moved between pages.</p>

		<?php submit_button('Done',"submit"); ?>
			
					</form>
					</div>
			 
			<?php
			return;
	}
	if ($action == 'update') {
		if (isset($_REQUEST['name']) && isset($_REQUEST['content'])) { //form resubmission issue
			$name = trim(sanitize_text_field($_REQUEST['name']));
			$id = (int) ($_REQUEST['id']);
			$content = $_REQUEST['content'];
			
			RESPONSIVESTICKYNOTES_note::update_post($id, $content, $name);
		}
	}
	if ($action == 'edit') {


		if (isset($_REQUEST['id'])) {
			$id = (int) $_REQUEST['id'];
			$post = get_post($id);
			$content = trim(esc_textarea($post->post_content));
			$title = $post->post_title;
			if (strpos($title, 'untitled') !== FALSE) {
				$title=RESPONSIVESTICKYNOTES_note::default_title;
			}
			
			$title = esc_attr(__($title, "responsive-sticky-notes"));
			?>
				
				 <div class="wrap">
				 <div id="icon-edit" class="icon32"></div>

				<h1><?php echo esc_html( __( 'Edit Sticky Note', 'responsive-sticky-notes' ) );?></h1>
				<div style="height: 10px"></div>

				<form method="post" action="<?php echo esc_url( add_query_arg( array('page' => 'responsive-sticky-notes', 'action' => 'update', 'id'=>$id), menu_page_url( 'responsivestickynotes', false ) ) ); ?>">
				 <div id="titlediv">
				<div id="titlewrap">
				<input type="text" name="name" style="width:100%" value="<?php echo $title; ?>" id="title" spellcheck="true" autocomplete="off" /><br>
				</div>
				</div>
				<textarea name="content" rows="10" cols="100" style="width: 100%"><?php echo $content; ?></textarea><br>
				<input type="hidden" name="id" value = "<?php echo $id;?>">
				
			<?php submit_button('',"submit"); ?>
		
				</form>
				</div>

		<?php
		}
	}
	else {
		$listTable->prepare_items();
		?>
    <div class="wrap">
        
        <div id="icon-users" class="icon32"><br/></div>
        <h2>Sticky Notes</h2>
         
        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="responsivestickynotes-list-table" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $listTable->display() ?>
        </form>
        
    </div>
    		 	<form method="post" action="<?php echo esc_url( add_query_arg( array('page' => 'responsive-sticky-notes', 'action' => 'help'), menu_page_url( 'responsivestickynotes', false ) ) ); ?>">
	<?php submit_button('help',"submit"); ?>
	</form>
    <?php 
	}
}