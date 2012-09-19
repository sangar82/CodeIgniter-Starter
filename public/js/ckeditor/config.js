CKEDITOR.editorConfig = function( config )
	{
	   // Define changes to default configuration here. For example:
	   // config.language = 'fr';
	   // config.skin = 'office2003';
	   //config.removePlugins =  'elementspath,enterkey,entities,forms,pastefromword,htmldataprocessor,specialchar' ;
	   config.removePlugins =  'elementspath,enterkey,entities,forms,htmldataprocessor,specialchar,horizontalrule,wsc' ;
	   
	   //config.toolbar = 'Basic';
	   CKEDITOR.config.toolbar = [
	   ['Styles','Format','Font','FontSize'],
	  
	   ['Bold','Italic','Underline','StrikeThrough','-','Undo','Redo','-','Cut','Copy','Paste','PasteText','PasteFromWord','Find','Replace','-','Outdent','Indent'],
	   '/',
	   ['NumberedList','BulletedList','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
	   ['Image','Table','-','Link','Smiley','TextColor','BGColor','Source']
	] ;
	};


