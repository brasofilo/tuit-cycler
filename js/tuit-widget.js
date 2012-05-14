/**
* @desc jQuery Cycle
* @author MAlsup
* @docs http://jquery.malsup.com/cycle/options.html
*/

jQuery(document).ready(function($){

	$('#tuit-show').cycle({ 
	    fx:     'shuffle', 
		shuffle: { 
		        top:  -100, 
		        left:  40 
		    }, 
	    easing: 'easeInOutBack',
	    easing: 'easeOutBounce', 
	    delay:  -2000,
		fit: 1
	 });

});