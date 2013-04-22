# GutenPress

An OOP toolset for WordPress.

At the moment, **GutenPress** it's a rough-draft for a bunch of tools for developing themes or plugins for WordPress.

Currently working:

* A post type generator GUI that creates new custom post types as plugins
* A set of metabox generator classes to add custom meta data to generated custom post types
* A wrapper class for WP_Query that returns instances of custom post type objects wrapped on a custom class, so you can extend the post-like objects according to your needs

## Installation

* Download the [master archive](https://github.com/felipelavinz/GutenPress/archive/master.zip) and extract into `wp-content/mu-plugins/`

## Usage

### Custom Post Type generator

* A new sub-menu it's added to the **Tools** admin menu.
* Carefully fill the form and select the required options to configure your new custom post type.
* On submit, a new folder will be created on your plugins folder; you'll be redirected to the plugins management so you can activate the new CPT
* The plugin activation will add the relevant permissions to the admin user, if you need to add permissions for other groups, check the [Members](http://wordpress.org/extend/plugins/members/) plugin by Justin Tadlock