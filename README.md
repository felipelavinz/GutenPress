# GutenPress

An OOP toolset for WordPress.

At the moment, **GutenPress** it's a rough-draft for a bunch of tools for developing themes or plugins for WordPress.

Currently working:

* A post type generator GUI that creates new custom post types as plugins
* A set of metabox generator classes to add custom meta data to generated custom post types
* A extendable wrapper class for WP_Query that returns instances of custom post type objects wrapped on an extendable custom class, so you can customize the post-like objects to your needs

## Installation

* Download the [master archive](https://github.com/felipelavinz/GutenPress/archive/master.zip) and extract into `wp-content/mu-plugins/`
* You'll need PHP 5.3 or greater, since GutenPress uses namespacing to lazy-load whenever's possible

## Usage

### Custom Post Type generator

* A new sub-menu it's added to the **Tools** admin menu.
* Carefully fill the form and select the required options to configure your new custom post type.
* On submit, a new folder will be created on your plugins folder; you'll be redirected to the plugins management so you can activate the new CPT
* The plugin activation will add the relevant permissions to the admin user
* If you need to add permissions for other groups, check the [Members](http://wordpress.org/extend/plugins/members/) plugin by Justin Tadlock
* The generated plugin consists of three classes:
  * A custom post type definition that extends \GutenPress\Model\PostType
  * A custom query class that extends \GutenPress\Model\PostQuery, a wrapper for WP_Query that implements the Iterator interface
  * A custom object class that extends \GutenPress\Model\PostObject, a wrapper for WP_Post that you can extend with custom methods

### Using the custom query class

Let's suppose we just created a **Songs** custom post type.

Getting a list of songs works just like directly calling WP_Query, so you can use all the arguments you could use with it, including tax queries and meta queries:

```php
$latest_songs = new SongsQuery( array(
	'posts_per_page' => '10',
	'tax_query' => array(
		array(
			'taxonomy' => 'performed_by',
			'terms' => 'pink-floyd',
			'field' => 'slug'
		)
	),
	'meta_query' => array(
		array(
			'key' => 'author_composer',
			'value' => 'David Gilmour'
		)
	)
) );
```

Since *SongsQuery* (or whatever your generated CPT class is called) implements the Iterator interface, you can loop through the objects with a simple `foreach`. It also implements the Countable interface, so you can check if your query actually has items:

```php
if ( count($latest_songs) ) :
	foreach ( $latest_songs as $song ) :
		// do stuff
		// each $song will be an instance of SongObject
		echo $song->authors;
	endforeach;
endif;
```

### Using and extending custom post type objects

You can extend your "Object" class to add custom methods around WP_Post:

```php
class SongObject extends \GutenPress\Model\PostObject{
	public function getAuthors(){
		$composer = $this->post->author_composer;
		$lyrics   = $this->post->author_lyrics;
		if ( $composer === $lyrics ) {
			return 'Music and Lyrics by '. $lyrics;
		} else {
			return 'Music: '. $composer .' / Lyrics: '. $lyrics;
		}
	}
	/**
	 * You can also overwrite the __get() magic method
	 */
	public function __get( $key ){
		if ( $key === 'authors' ) {
			return $this->getAuthors();
		}
		// pass it on to WP_Post
		parent::__get( $key );
	}
}
```

### Adding metaboxes

You can use the \GutenPress\Model\PostMeta class to add a metabox to your CPT:

```php
// using the \GutenPress\Model namespace as Model;
class SongAuthors extends Model\PostMeta{
	protected function setId(){
		// will be used for the metabox ID
		// will be prepended to the metadata defined by this class
		return 'author';
	}
	protected function setDataModel(){
		return array(
			new Model\PostMetaData(
				'composer',
				'Composer',
				'\GutenPress\Forms\Element\InputText', // can be any of the Elements defined on the corresponding folder
				array(
					'placeholder' => 'Who composed the music for this song?'
				)
			),
			new Model\PostMetaData(
				'lyrics',
				'Lyrics',
				'\GutenPress\Forms\Element\InputText',
				array(
					'placeholder' => 'Who wrote the lyrics?'
				)
			)
		);
	}
}

// finally, register as metabox
new Model\Metabox( 'SongAuthors', 'Authorship information', 'song', array('priority' => 'high') );
```