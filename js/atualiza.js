 $(document).ready(function() {
 	 $("#responsecontainer").load("msgs");
   var refreshId = setInterval(function() {
      $("#responsecontainer").load('msg?tempo='+ Math.random());
   }, 9000);
   $.ajaxSetup({ cache: false });
});