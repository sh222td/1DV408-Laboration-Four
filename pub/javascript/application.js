var app = {
	
	init : function(){
		this.bindEvents();
	},
	
	bindEvents : function(){
		$('.flash .flash-message .close').on('click', function(e){
			e.preventDefault();
			$(e.target).closest('.flash').fadeOut(250);
		});
	}
};

$(document).ready(function(){
	app.init();
});