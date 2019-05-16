<?php
	class Book {
		var $title;
		var $description;
		var $author;

		public function __construct($default_title, $default_author, $default_description = '') {

			$this->title = $default_title;
			$this->author = $default_author;
			$this->description = $default_description;
		}

		public function set_prop($prop, $value){
		    try{
			    $this->{$prop} = $value;
		    }
		    catch(Exception $e ){
		        return $e->message;
		    }
		}

		public function get_prop($prop){
		    try{
		       return $this->{$prop};

		    }
		    catch(Exception $e ){
		        return $e->message;
		    }
		}
	}
	
?>

