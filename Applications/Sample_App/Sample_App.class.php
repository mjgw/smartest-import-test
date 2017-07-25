<?php
  
// include any needed files - optional of course.
// require_once "%NEEDED_FILE%.php";

// Extend SmartestApplication
// not strictly required if you like to do everything by hand, but no good reason not to.
// Gives access to many aspects of Smartest API that would otherwise be unavailable
// Gives access to Controller values and templating object
class Sample_App extends SmartestUserApplication{
  
	// declare any vars/constants/whatever, as normal
	protected $_foo;
	const FOO = 'BAR';

	// SmartestApplication already has a constructor, so if you want your class to have a constructor,
	// put it here called __smartestApplicationInit() and SmartestApplication will call it.
	public function __smartestApplicationInit(){
		
	}
	
	// no other requirements at all.
	// define your methods as normal and have fun...
	
	// By convention, the default action for a module is called startPage,
	// but you can make this whatever you like in %module_dir%/Configuration/quince.yml
	public function startPage(){
	    $this->send("Hello world!", 'message');
	}
	
	// will be callable via the url yoursite.com/appshortname/foo
    // send data to the presentation layer with $this->send($data, 'name');
    // a corresponding template foo.tpl in ./Presentation/ folder will display any data sent
	public function foo(){          	
		
	}
	
	// will be callable via the url yoursite.com/appshortname/bar
    // send data to the presentation layer with $this->send($data, 'name');
    // a corresponding template foo.tpl in ./Presentation/ folder will display any data sent
	public function bar(){
		
	}
	
	// private method will not be accessible in the url
	private function foobar(){
	    
	}
	
}