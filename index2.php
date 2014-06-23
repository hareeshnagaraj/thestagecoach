<!DOCTYPE html>
<head>
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<link rel="icon" href="img/stagecoachlogo.png" type="image/x-icon"/>
	<link href='css/style.css' rel='stylesheet' type='text/css'>

	<title>The Stage Coach</title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<link href='http://fonts.googleapis.com/css?family=Fauna+One|Roboto:400,700,900' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900,300italic,400italic,700italic' rel='stylesheet' type='text/css'>

	<style>
	#top_left{
		background-color: #fff;
		width: 100%;
		background-size: cover;
		height: 100%;
		overflow: hidden;
		background-repeat: no-repeat;
		position: absolute;
		background-position: 0px 0px;
		background-image: url('../img/frontpic2.jpg');
		background-attachment: fixed;
	}
	#top_right{
		background-color: #fff;
		width: 50%;
		background-size: cover;
		height: 50%;
		overflow: hidden;
		background-repeat: no-repeat;
		position: absolute;
		background-position: 300px -200px;;
		background-image: url('../img/pic3.jpg');
		background-attachment: fixed;
		right:0;
	}
	#bottom_left{
		background-color: #fff;
		width: 100%;
		background-size: cover;
		height: 50%;
		overflow: hidden;
		background-repeat: no-repeat;
		position: absolute;
		background-position: 0px 300px;
		background-image: url('../img/pic3.jpg');
		background-attachment: fixed;
		bottom:0;
	}
	#bottom_right{
		background-color: #fff;
		width: 50%;
		background-size: cover;
		height: 50%;
		overflow: hidden;
		background-repeat: no-repeat;
		position: absolute;
		background-position: 300px 0px;;
		background-image: url('../img/pic1.jpg');
		background-attachment: fixed;
		bottom:0;
		right:0;
	}
	#white_horizontal{
		width:100%;
		position: absolute;
		height: 40px;
		background-color: white;
		top:45%;
		z-index:100;
		text-shadow:0px 2px 2px rgba(0, 0, 0, 0.4); 

	}
	#white_vertical{
		width:40px;
		position: absolute;
		height: 100%;
		background-color: white;
		left:50%;
		z-index:100;
	}
	#logo{
		width: 200px;
		height: 200px;
		background-color: white;
		background-image: url('img/stagecoachlogo.png');
		position: absolute;
		background-size: cover;
		border-radius: 50%;
		top:35%;
		left:43%;
		z-index: 200;
		text-shadow:0px 2px 2px rgba(0, 0, 0, 0.4); 
	}
	</style>
</head>
<body>


	<div id="top_left"></div>
<!-- 	<div id="top_right"></div>
 -->	<div id="white_horizontal"></div>
<!-- 	<div id="white_vertical"></div>
 -->	<div id="logo"></div>
<!-- 	<div id="bottom_left"></div>
 --><!-- 	<div id="bottom_right"></div>
 -->
<!-- 	<div id="footer">
		<div>
			<div id="contactus">Contact: 111 - 111 -1121</div>
			<div id="socialicon"><img src="img/twitter.png" /></div>
			<div id="socialicon"><img src="img/facebook.png" /></div>
		</div>
	</div> -->

</body>
</html>