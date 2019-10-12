<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://digitalideas.io/
 * @since      1.0.0
 *
 * @package    Eventbrite_For_Wordpress
 * @subpackage Eventbrite_For_Wordpress/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://pro.crunchify.com/typed.min.js" type="text/javascript"></script>
<style>
	
.btn{
  
  margin-left: -453px;
  margin-top: 43px; 
  
}

.save

{margin-top: 37px;}

.inputcls 
{
border-width: 2px;
border-style: inset; 
border-color: initial;
border-image: initial;	
}

</style>

<div class="wrap">
    <h2>MY MESSAGE</h2>
    <?php 
 		if ( $this->flag ) { // success!
			 $this->admin_notice(true);
		}   ?>
    <form action="" method="post">
		<?php
            settings_fields( $this->plugin_name ); ?>
            <div id='TextBoxesGroup' class="">
				<div id="TextBoxDiv1">
				   <?php //do_settings_sections( $this->plugin_name ); ?>
				   <div>					   
				   <input type='textbox' required id='_key1'  name='_key1' placeholder="API-Key" >
				   &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;
				   <input type='textbox' required id='_value1' name='_value1' placeholder="
				   Organizer-ID">
				   </div>
				  </div> 
				<div style=”clear:both;”></div>
           </div>
           <div>
           <div  style="width:20%;padding:0 30px 0 0;float:right;">
			   <button type="button" name='addmore' class="addmore btn">Add more</button>
			   
		   </div>   
           </div>
           <div class="row" style="margin-top: 37px;"><input type='submit' id='submit' name='submit' value="Save Changes" class="button button-primary" ></div> 
             
    </form>

</div>
<script>
$(document).ready(function(){

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
  function removeme(rowid) {
		  if(rowid==1){
			  alert("No more textbox to remove");
			  return false;
		   }   				
		   $("#TextBoxDiv" + rowid).remove();
		   counter--;	  
  } 
</script>
