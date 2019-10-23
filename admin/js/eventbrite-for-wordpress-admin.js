(function( $ ) {
	'use strict';
	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

/*(document).ready(function(){

    var counter = 2;	
    $(".addmore").click(function () {
				
	if(counter>10){
            alert("Only 10 textboxes allow");
            return false;
	}   
		
	var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);
                
	newTextBoxDiv.after().html('<div><input required placeholder="API-Key"  type="text" name="_key' + counter + 
	      '" id="_key' + counter + '" value="" >&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<input  required placeholder="Organizer-ID"type="text" name="_value' + counter + 
	      '" id="_value' + counter + '" value="" >&nbsp;&nbsp; <a href="#" id="remove'+ counter +' "  class="remove" onclick="removeme('+counter+')">Remove</a></div>');
            
	newTextBoxDiv.appendTo("#TextBoxesGroup");
			
	counter++;
     });

  }); 

})( jQuery );
  function removeme(rowid) {
		  if(rowid==1){
			  alert("No more textbox to remove");
			  return false;
		   }   				
		   $("#TextBoxDiv" + rowid).remove();
		   counter--;	  
  }

*/
