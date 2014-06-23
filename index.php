<!DOCTYPE html>
<head>
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<link rel="icon" href="img/stagecoachlogo.png" type="image/x-icon"/>
	<link href='css/style.css' rel='stylesheet' type='text/css'>

	<title>The Stage Coach</title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<link href='http://fonts.googleapis.com/css?family=Fauna+One|Roboto:400,700,900' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900,300italic,400italic,700italic' rel='stylesheet' type='text/css'>
		<script type="text/javascript">

			var navswitch = 0;
			$(document).ready(function(){
				$("#smallNav").click(function(){
					if(navswitch == 0){
						$("#smallNavList").animate({"height":"175px"},300);
						navswitch = 1;
					}
					else{
						$("#smallNavList").animate({"height":"0px"},300);
						navswitch = 0;
					}
				});
			});
		
		</script>
</head>
<body>
	<div class="navbar">
		<div class="logo">
			<img src="img/stagecoachlogo.png" />
		</div>

		<div class="navElement">
			Contact Us
		</div>
		<div class="navElement">
			Testimonials
		</div>
		<div class="navElement">
			Services
		</div>
		<div class="navElement">
			Our Work
		</div>
		<div class="navElement">
			About
		</div>

		<div id="smallNav">Menu</div>
		<div id="smallNavList">
			<div>About</div>
			<div>Our Work</div>
			<div>Services</div>
			<div>Testimonials</div>
			<div>Contact Us</div>
		</div>
	</div>
	<div id="wrapper">
		

	</div>
<!-- 	<div id="initialText">
		Lorem Ipsum
	</div> -->
	<div id="smallWrap">
	</div>
	<div id="bottomWrap">
		<div>
			<div id="subtitle">Welcome</div>
			<div id="welcomeSubText">
			In today's competitive real estate market, it is important to
			understand the difference between living in a home, and staging and photographing 
			a house. It is a well-known fact that staging your house is a good investment. 
			Staged houses, if priced properly, will sell in the shortest amount of time for the 
			most amount of money.<br><br>
			We offer a fresh and objective perspective and know how to highlight your home's
			best features and minimize its flaws. If a buyer cannot visually start "moving 
			themselves in," a potential sale is lost. We easily solve problems with clutter, color, 
			and flow to create a warm, inviting and positive first impression -- whether that first 
			impression begins online or in person.<br><br>
			We can stage a single room or a whole house. We understand the subtle differences
			between staging for a walk-through, and staging for marketing photography.
			</div>
		</div>
	</div>
	<div id="footer">
		<div>
			<div id="contactus">Contact: 111 - 111 -1121</div>
			<div id="socialicon"><img src="img/twitter.png" /></div>
			<div id="socialicon"><img src="img/facebook.png" /></div>
		</div>
	</div>

</body>
</html>