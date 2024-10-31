=== Plugin Name ===
Contributors: ardmark
Tags: notes, sticky, responsive
Requires at least: 4.3.4
Tested up to: 4.7
Stable tag: 1.03
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily add 'sticky' notes which move with responsive layout changes

== Description ==

This plugin lets you add 'sticky' notes to any post or non-admin page. The notes are attached to HTML elements within the page, so are ideal for text annotations or to add extra information to images, etc., and will move with responsive layout changes so they never vanish off the edge of the screen.  

To add a note, click the 'Add Sticky Note' button in the top admin bar, then move the cursor over the page. As you move the cursor, page elements are highlighted with an outline. Click again to add a note at this position.
Notes are attached to the beginning of a page element, i.e. at the top left corner of a `<p>`, `<div>`, etc. A note can be attached to anything on a page.
Once you have added a note, click it to open it. Then type text as required - it is saved as you type. 
To move a note, click and drag it to another position on the page. If the note is open, click and drag from top, between the Close and Menu icons. The The page will scroll automatically when the note is close to an edge (you may need to move the cursor a little to get the page to scroll).
To change the note color, delete the note, or go to the edit page, click the 'menu' icon in the top right corner.

The note 'tooltip' is set to the first few words of the note, unless the note has a title. To set a title, open the note menu then click the ... icon, to go to the note edit screen.

If you delete a page element to which a note is attached, the note will 'float' to the top left of the window. This may also happen if you change an ID somewhere on the page. Floated notes can be dragged to any other page element, as normal.

== Installation ==

1. Download plugin from Wordpress plugin repository
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to any page. There will be an 'Add Sticky Note' button in the admin bar at the top. That's all, you are good to go.

== Frequently Asked Questions ==

= What happens if I delete an image or div which a note is attached to =

The note will 'float' to the top of the page, and you can drag it to another page element if you wish.

= What happens if the page layout changes - will my notes still be visible =

Your notes should track any movement of the HTML elements within the page. If an element is hidden by a responsive CSS change, the note will float to the top of the page.

= Can I use my own CSS to change note colors =

Responsive Sticky Notes are meant to be quick and simple to use, so have pre-defined colors. The note colors are defined in responsive-sticky-notes.css and can be changed here if you wish.

= How do I add notes to posts =

Same as for pages, and the note will 'track' the post wherever it is displayed in the site

= What sort of things can I add to a note =

Notes are plain text, images and hyperlinks are not currently supported

= Can I share notes between pages =

Notes are specific to the page they are created on, and cannot currently be shared between posts or pages

= Can notes be shown to anyone or just logged-in users =

There is an option to 'Show notes when not logged in' on the admin page

== Changelog ==

= 0.1 =
* Initial release
= 0.1.1 =
* Small fix to text domain
= 1.0 =
* AJAX error will not automatically delete a note
* Option added to allow notes to be hidden from non-admin users
= 1.01 =
* Fixed bug where user without edit priviledges could add a note (briefly)
= 1.02 =
* Can now edit notes from admin page
