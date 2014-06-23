<?php
/*
  This is the stories creation feature
  gmaps key AIzaSyAEby7YD0fAH-SKQamGMydNJtK-oL7g6vA
*/

//connect to the database
require "db_connection.php";
require "functions.php";
require "template_functions.php";

//start the session
if(!isset($_SESSION['email'])) {
    session_start();
    $email = $_SESSION['email'];
    //Session Info
    $currenttime = time();
    $info = getinfo($email);
    $name = $info["fullname"];
    $profpic = 'https://photorankr.com/'.$info["profilepic"];
    $time = formatUnixTime($currenttime);
}
else{

}
//if login form has been submitted
if(isset($_GET['action'])) {
    $action = $_GET['action'];
    if($action == "login") { 
        login();
    }

    elseif($action == "logout") { 
        logout();
    }
}
?>
<!DOCTYPE html>
<head>
    <title>Stories</title>
    <link rel="stylesheet" type="text/css" href="https://photorankr.com/css/newfeed.css"/>
    <link rel="stylesheet" type="text/css" href="https://photorankr.com/css/newsfeedmatt.css"/>
    <link rel="stylesheet" type="text/css" href="https://photorankr.com/css/galleries.css"/>
    <link rel="stylesheet" type="text/css" href="https://photorankr.com/css/newprofile2.css"/>
    <link rel="stylesheet" href="../css/style.css" type="text/css" />
    <link rel="shortcut icon" type="image/x-png" href="graphics/favicon.png"/>
    <link href="http://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
    <link href='http://fonts.googleapis.com/css?family=Fauna+One|Roboto:400,700,900' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900,300italic,400italic,700italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Alegreya+Sans+SC:400,500,700,800,300italic,400italic,500italic,700italic' rel='stylesheet' type='text/css'>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

 	<script type="text/javascript"src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyAEby7YD0fAH-SKQamGMydNJtK-oL7g6vA&sensor=true"></script>
    <script type="text/javascript">
        var newid = 0;
        var useremail = "<?php echo $email; ?>";
        var locationDict = {};
        var current_story_id = 0;
    </script>
    <script src="js/stories.js"></script>
    <script type="text/javascript" src="js/jquery.sticky.js"></script>
    <script>

    function parseUri (str) {
      var o   = parseUri.options,
        m   = o.parser[o.strictMode ? "strict" : "loose"].exec(str),
        uri = {},
        i   = 14;

      while (i--) uri[o.key[i]] = m[i] || "";

      uri[o.q.name] = {};
      uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
        if ($1) uri[o.q.name][$1] = $2;
      });

      return uri;
    };

    parseUri.options = {
      strictMode: true,
      key: ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],
      q:   {
        name:   "queryKey",
        parser: /(?:^|&)([^&=]*)=?([^&]*)/g
      },
      parser: {
        strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
        loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
      }
    };
    function scroll(inputstring){
      var offset = $(this).offset();
      $('html, body').animate({scrollTop: $(inputstring).offset().top}, 500);
    }
    function youtube_parser(url){
      var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
      var match = url.match(regExp);
      if (match&&match[7].length==11){
          return match[7];
      }else{
          alert("Url incorrecta");
      }
    }
    function vimeo_parser(url){
      url = url.split("https://player.vimeo.com/video/");
      window.alert(url[1]);
      return url[1];
    }
    </script>
	<script type="text/javascript">


	</script>
    <style>
    html,body{
      width:100%;
      height:100%;
    }
    #map-canvas{
    	 height: 100%;
        margin: 0px;
        padding: 0px
    }
    #modalbg{
      background-color: rgba(30,30,30,0.9);
      width: 100%;
      height: 100%;
      position: fixed;
      top: 0px;
      left: 0px;
      display: none;
      z-index: 100;
    }
    .toolBar{
      padding-right:20px;
      padding-left: 20px;
      padding-top: 5px;
      padding-bottom: 5px;
      font-size: 13px;
      font-weight:bold;
      cursor: pointer;
      z-index: 2147483647;
      background-color: rgba(255,255,255,0.8);
      position: fixed;
      color: rgb(99,164,255);
      text-align: center;
      right: 0;
    -webkit-border-top-left-radius: 4px;
    -webkit-border-bottom-left-radius: 4px;
    -moz-border-radius-topleft: 4px;
    -moz-border-radius-bottomleft: 4px;
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
    }
    #saveStory > img {
      width:35px;
    }
    #publishStory > img {
      width:35px;
    }

    .blockQuoteBody{
  		outline: none;
  		background-color: rgba(21,22,23,0);
  		border: none;
  		color: black;
  		font-family: Garamond;
  		font-size: 30px;
  		resize: none;
  		padding: 0px;
  		vertical-align: bottom;
  		margin-top: 40px;
  		width:80%;
  		text-align: center;
    }
    .blockQuoteAuthor{
      outline: none;
      background-color: rgba(21,22,23,0);
      border: none;
      color: black;
      font-family: Garamond;
      font-size: 30px;
      resize: none;
      padding: 0px;
      vertical-align: bottom;
      padding-top: 20px;
      text-align: center;
    }
    .blockQuote{
  		width: 100%;
  		padding-top: 20px;
  		padding-bottom: 20px;
  		background-color: white;
  		overflow: hidden;
    }
    .videoBlock{
      width: 100%;
  		padding-top: 30px;
  		padding-bottom: 30px;
  		background-color: white;
  		overflow: hidden;
  		text-align: center;

    }
    .videoCaption{
  		outline: none;
  		background-color: rgba(21,22,23,0);
  		border: none;
  		color: rgba(0,0,0,0.5);
  		font-family: 'Garamond';
  		font-size: 20px;
  		text-align: center;
  		resize: none;
  		margin-top: 25px;
  		pading-top:20px;
  		padding-bottom: 20px;
  		width: 80%;
    }
    #youtubeLink{
  		outline: none;
  		background-color: rgba(21,22,23,0);
  		border: none;
  		color: black;
  		font-family: Garamond;
  		font-size: 25px;
  		text-align: center;
  		resize: none;
  		margin-top: 100px;
  		min-height: 100px;
  		width: 80%;
    }
    #vimeoLink{
  		outline: none;
  		background-color: rgba(21,22,23,0);
  		border: none;
  		color: black;
  		font-family: Garamond;
  		font-size: 25px;
  		text-align: center;
  		resize: none;
  		margin-top: 10px;
  		min-height: 100px;
  		width: 80%;
    }
   .controls {
      margin-top: 5px;
      border: 1px solid transparent;
      border-radius: 2px 0 0 2px;
      box-sizing: border-box;
      -moz-box-sizing: border-box;
      height: 32px;
      outline: none;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }
    #pac-input {
      background-color: #fff;
      padding: 0 11px 0 13px;
      width: 400px;
      font-family: Roboto;
      font-size: 15px;
      font-weight: 300;
      text-overflow: ellipsis;
    }
    #pac-input:focus {
      border-color: #4d90fe;
      margin-left: -1px;
      padding-left: 14px;  /* Regular padding-left + 1. */
      width: 401px;
    }
    .pac-container {
      font-family: Roboto;
    }
    #type-selector {
      color: #fff;
      background-color: #4d90fe;
      padding: 5px 11px 0px 11px;
    }
    #type-selector label {
      font-family: Roboto;
      font-size: 13px;
      font-weight: 300;
    }
    </style>

    <script type="text/javascript">
    var pictureList = new Array();
    var idList = new Array();
    var pictureListPos = 0;
    var numberCurrentPics = 1;
    var floor = 0;
    var map;
    var marker;
    var geocoder;

    var max = 10;

      //adjust textarea
    function textAreaAdjust(o) {
      o.style.height = "1px";
      o.style.height = (25+o.scrollHeight)+"px";
    }
    function textAreaAdjust2(o,b) {
      o.style.height = "1px";
      o.style.height = (25+o.scrollHeight)+"px";
      b.style.height = "1px";
      b.style.height = (25+b.scrollHeight)+"px";
    }
    /**
     * Returns a random number between min and max
     */
    function getRandomArbitary (min, max) {
        return Math.random() * (max - min) + min;
    }

    /**
     * Returns a random integer between min and max
     * Using Math.round() will give you a non-uniform distribution!
     */
    function getRandomInt (min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }


  function storySave(story, jsonString){

 

    ajaxRequest = createRequestObject();
    // Create a function that will receive data sent from the server
    ajaxRequest.onreadystatechange = function(){
        if(ajaxRequest.readyState == 4  && ajaxRequest.status == 200){
            console.log(ajaxRequest.responseText);
            var newurl = "http://coversplash.com/story/"+ajaxRequest.responseText;
            $('#addTitleInfo > div').html("Your story has been saved!");
            $('#addTitleInfo').fadeIn(500); 
            $('#addTitleInfo').delay(3000).fadeOut(1000);
            current_story_id = ajaxRequest.responseText;
        }
    }
    //Make sure they don't fave more than once
    var c = JSON.stringify(jsonString);
    console.log("in story save");
    console.log(c);

    if(current_story_id == 0){
      ajaxRequest.open("POST", "/storySave/ " + story + "/" + c + "/" + "<?php echo $email; ?>", true);
      ajaxRequest.send(null); 
    }
    else{
      ajaxRequest.open("POST", "/storyUpdate/ " + story + "/" + c + "/" + "<?php echo $email; ?>/"+current_story_id, true);
      ajaxRequest.send(null); 
    }
}

  function publishStory(){

    if(current_story_id == 0){
      $('#addTitleInfo > div').html("Please save then publish");
      $('#addTitleInfo').fadeIn(500); 
      $('#addTitleInfo').delay(3000).fadeOut(1000);
      return;
    }
    else{
      ajaxRequest = createRequestObject();
      ajaxRequest.onreadystatechange = function(){
          if(ajaxRequest.readyState == 4  && ajaxRequest.status == 200){
              console.log(ajaxRequest.responseText);
              current_story_id = ajaxRequest.responseText.trim();
              var newurl = "http://coversplash.com/story/"+current_story_id;
              console.log(newurl);
              window.location = newurl;
          }
      }
      ajaxRequest.open("POST", "http://coversplash.com/storyPublish/ " + current_story_id, true);
      ajaxRequest.send(null); 
    }
    

  }

    $( document ).ready(function() {

        $(document.body).on('change','#story_title',function(){
            if($("#story_title").val() == ""){
             $("#story_title").val("Untitled Story"); 
            }
        });

        $(document.body).on('change','#story_subtitle',function(){
            if($("#story_subtitle").val() == ""){
             $("#story_subtitle").val("Enter a Subtitle"); 
            }
        });
        
        $('#title1').on( 'keyup', 'textarea', function (e){
            $(this).css('height', 'auto' );
            $(this).height( this.scrollHeight );
        });
        $("#saveMap").click(function(){
            
            var markPos = marker.getPosition();
            var lat = markPos.lat();
            var lng = markPos.lng();
            var street;
            var newmarker;
            var newmapid = "mapEmbed"+newid;
            var newmapcaptionid = "mapCaption"+newid;
            var newmapdelete = "mapDelete"+newid;
            var b = 0;

            var mapInfoArray = {};
            mapInfoArray["lat"] = lat;
            mapInfoArray["lng"] = lng;

            geocoder.geocode({'latLng': markPos}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
              if (results[1]) {
                mapInfoArray["street"] = results[1].formatted_address;
                b = 1;
              } else {
              }
            }
            });

            locationDict[newmapid] = mapInfoArray;
            
            if(b = 0){
              street = "Add a caption to this map";
            }
            $("#storyWrap").append('<div id="'+newmapid+'" class="gmapsWrap"></div><div id="captionwrap'+ newmapid +'" class=\"fullsizeStoryPicCaption\" style=\'margin-top:0px;\'><textarea id="' + newmapcaptionid + '"rows=\"1\" value=\"Untitled Story\" class=\"fullsizeStoryPicCaption_input\"> Add a caption to this map </textarea></div><div id="'+newmapdelete+'" class="fullPicDelete" style="margin-top:-140px;"><img id=\"mapEmbedDelete' + newid + '"\" class=\"fullPicDelete_x\" src=\"img/delete.png\" /><input type="hidden" id="mapInfoInput'+newid+'" value="'+mapInfoArray+'" /></div>');
            $("#mapEmbedDelete" + newid).on( "click", function() {
                $("#"+newmapid).remove();
                $("#captionwrap"+newmapid).remove();
                $("#"+newmapdelete).remove();
                $("#"+newmapcaptionid).remove();
            });
            var newmap;
            function initialize(){
              var mapOptions = {
                zoom: 8,
                disableDefaultUI:true,
                scrollwheel: false,
                center: markPos
              };
              
              newmap = new google.maps.Map(document.getElementById(newmapid),
                    mapOptions);
              
              newmarker = new google.maps.Marker({
              map: newmap,
              position: markPos
              });
            }
            
            initialize();
            newid++;
            $("#saveMap").hide();
            $("#saveMapModal").hide();
            $("#mapsModal").css('margin-left','400%');
            $("#modalbg").hide();
        });

        $("#selectImage").click(function(){ 
          //disableScroll();
          $("#modalbg").show();
          $("#coverPictureModal").show();
        });
        $("#modalbg").click(function(){    
          //enableScroll();
          $("#modalbg").hide();
          $("#coverPictureModal").hide();
          $("#fullPictureModal").hide();
          $("#groupPictureModal").hide();
          $("#videoModal").hide();
          $(".saveMapModal").hide();
          $("#saveMap").hide();
          $("#mapsModal").css('margin-left','400%');

        });
        
    
        $("#arrowDown").click(function(){

          scroll("#toolbox");

        });
		$("#addLocationTag").click(function(){

        $("#modalbg").show();
        $( "#mapsModal" ).animate({ "margin-left": "-400px" }, 300 );
        setTimeout(function() {
          $(".saveMapModal").show();
          $("#saveMap").show();
        }, 300);
        
       	});


      function pushStory(){
        var elementArray = new Array();
         var b = {};

         var title = document.getElementById("story_title").value;
         var subtitle = document.getElementById("story_subtitle").value;
         console.log(subtitle);
         var story_cover = $("#storyCover").css('background-image');



         if(title == "Untitled Story"){
            $('#addTitleInfo > div').html("Please add a title and subtitle");
            $('#addTitleInfo').fadeIn(500); 
            $('#addTitleInfo').delay(3000).fadeOut(1000);
           return;
         }
        
        if(subtitle == "Enter a Subtitle"){
            $('#addTitleInfo > div').html("Please add a title and subtitle");
            $('#addTitleInfo').fadeIn(500); 
            $('#addTitleInfo').delay(3000).fadeOut(1000);
           return;
         }

         else if(story_cover == "none"){
           $('#addTitleInfo > div').html("Please add a cover photo");
           $('#addTitleInfo').fadeIn(500); 
           $('#addTitleInfo').delay(3000).fadeOut(1000);
          return;
         }
         
         if(newid < 1){
          $('#addTitleInfo > div').html("Please add more elements to your story");
            $('#addTitleInfo').fadeIn(500); 
            $('#addTitleInfo').delay(3000).fadeOut(1000);
           return;
         }

         if(story_cover != "none"){

         var passImg = story_cover.split('userphotos/');
         var d = passImg[1];
         d.slice(0,-1);                
         passImg = d.split("&w");
         story_cover = passImg[0];

        }
        else{
          story_cover = "none";
        }
    

         title = title.replace(/"/g, "_~_~_~");  
         title = title.replace(/'/g, "@@@@@@");
         title = title.replace(/\//g,"_!!_!");
         title = title.replace(/[?]/g, "-%-%-%-%-");

         subtitle = subtitle.replace(/"/g, "_~_~_~");  
         subtitle = subtitle.replace(/'/g, "@@@@@@");
         subtitle = subtitle.replace(/\//g,"_!!_!");
         subtitle = subtitle.replace(/[?]/g, "-%-%-%-%-");

         title = encodeURIComponent(title);
         subtitle = encodeURIComponent(subtitle);

         console.log("first_title");
         console.log(title);

         b["storyTitle"] = title;
         b["storySubTitle"] = subtitle;
         b["storyCoverPhoto"] = story_cover;

        $('.fullsizeStoryPic').each( function(i,e) {
            var id = $(e).attr('id');
            elementArray.push(id);
            var idSplice = id.slice(11,id.length);
            var fullSizeCaption = document.getElementById("fullsizeStoryCaptionValue" + idSplice).value;
            fullSizeCaption = fullSizeCaption.replace(/"/g, "_~_~_~");  
            fullSizeCaption = fullSizeCaption.replace(/'/g, "@@@@@@");
            fullSizeCaption = fullSizeCaption.replace(/\//g,"_!!_!");
            fullSizeCaption = fullSizeCaption.replace(/[?]/g, "-%-%-%-%-");

            var childImage = $(e).css('background-image');
            var passImg = childImage.split('userphotos/');
            var photoBlockItems = new Array(); 

            var d = passImg[1];
            d.slice(0,-1);                
            passImg = d.split("&w");

            photoBlockItems.push(passImg[0]);

            photoBlockItems.push(fullSizeCaption);
            b[id] = (photoBlockItems);

            elementArray.push(photoBlockItems);
            // elementArray.push();
            // b[id] = $(e).css('background-image');
        });
        $('.textBox').each( function(i,e) {
            var id = $(e).attr('id');
            elementArray.push(id);
            var idSplice = id.slice(9,id.length);
            var inputId = 'textblockinput'+idSplice;
            var hVal = 'textblockheader'+idSplice;

            var textBoxItems = new Array();

            var value = document.getElementById(inputId).value;
            value = value.trim();
            value = value.replace(/"/g, "_~_~_~");  
            value = value.replace(/'/g, "@@@@@@"); 
            value = value.replace(/\n/g, "%%%%%%"); 
            value = value.replace(/\//g,"_!!_!");
            value = value.replace(/[?]/g, "-%-%-%-%-");

            var headerVal = document.getElementById(hVal).value;
            headerVal = headerVal.replace(/"/g, "_~_~_~");  
            headerVal = headerVal.replace(/'/g, "@@@@@@"); 
            headerVal = headerVal.replace(/\//g,"_!!_!");
            headerVal = headerVal.replace(/[?]/g, "-%-%-%-%-");

            headerVal = headerVal.trim();

            textBoxItems.push(encodeURIComponent(value));
            textBoxItems.push(encodeURIComponent(headerVal));
            console.log("textBoxItems");
            console.log(textBoxItems);

            b[id] = textBoxItems;
            elementArray.push(textBoxItems);
            // window.alert(value + " " + headerVal);

        });

        $('.blockQuote').each( function(i, e) {
          var id = $(e).attr('id');
          var idSplice = id.slice(10,id.length);
          var body = "blockQuoteBody" + idSplice;
          var author = "blockQuoteAuthor" + idSplice;
          var blockQuoteItems = new Array();
          
          var b_body = document.getElementById(body).value;
          b_body = b_body.replace(/"/g, "_~_~_~");  
          b_body = b_body.replace(/'/g, "@@@@@@");
          b_body = b_body.replace(/\//g,"_!!_!");
          b_body = b_body.replace(/[?]/g, "-%-%-%-%-");
          console.log(b_body);
          console.log("lol");

          blockQuoteItems.push(encodeURIComponent(b_body));

          var b_author = document.getElementById(author).value;
          b_author = b_author.replace(/"/g, "_~_~_~");  
          b_author = b_author.replace(/'/g, "@@@@@@");
          b_author = b_author.replace(/\//g,"_!!_!");
          b_author = b_author.replace(/[?]/g, "-%-%-%-%-");
          blockQuoteItems.push(encodeURIComponent(b_author));
          // window.alert(blockQuoteItems);
          b[id] = blockQuoteItems;

          console.log(b);
          console.log("quotesadded");

        });
        $('.gmapsWrap').each( function(i,e) {
          var id = $(e).attr('id');

          var idSplice = id.slice(8,id.length);
          var mapID = "mapEmbed"+idSplice;
          var mapcaption = "mapCaption" + idSplice;
          var mapinfo = "mapInfoInput"+idSplice;
          var mapInfo = locationDict[mapID];

          var uploadArray = {};
          uploadArray["mapinfo"] = mapInfo;
          var mapCaptionVal = document.getElementById(mapcaption).value;
          mapCaptionVal = mapCaptionVal.replace(/"/g, "_~_~_~");  
          mapCaptionVal = mapCaptionVal.replace(/'/g, "@@@@@@");
          mapCaptionVal = mapCaptionVal.replace(/\//g,"_!!_!");
          mapCaptionVal = mapCaptionVal.replace(/[?]/g, "-%-%-%-%-");
          uploadArray["mapcaption"] = mapCaptionVal;
          b[mapID] = uploadArray;

        });
        $('.videoBlock').each( function(i,e) {
          var id = $(e).attr('id');
          var idSplice = id.slice(10,id.length);

          var videoId = "videoLink" +  idSplice;
          var videoCaption = "videoCaption" + idSplice;
          

          console.log("newcaption");
          

          var videoItems = { };
          var vidSrc = document.getElementById(videoId).src;
          var vidCaption = document.getElementById(videoCaption).value;
          vidCaption = vidCaption.replace(/"/g, "_~_~_~");  
          vidCaption = vidCaption.replace(/'/g, "@@@@@@");
          vidCaption = vidCaption.replace(/\//g,"_!!_!");
          vidCaption = vidCaption.replace(/[?]/g, "-%-%-%-%-");
          vidCaption = encodeURIComponent(vidCaption);
          console.log(vidCaption);
          var youtubeValid = vidSrc.indexOf("youtube");
          var vimeoValid = vidSrc.indexOf("vimeo");
          if(youtubeValid != "-1"){
            /*
          Youtube Actions
            */
          vidSrc = youtube_parser(vidSrc);
          videoItems["type"] = "youtube";
          videoItems["source"] = vidSrc;
          videoItems["caption"] = vidCaption;
          }
          else if(vimeoValid != "-1"){
            /*
          Vimeo Actions
            */
          vidSrc = vimeo_parser(vidSrc);
          videoItems["type"] = "vimeo";
          videoItems["source"] = vidSrc;
          videoItems["caption"] = vidCaption;
          }
          
          // window.alert(videoItems);
          console.log("video items");
          console.log(videoItems);
          console.log(b);
          b[id] = videoItems;
        });

        $('.groupPhotoBlock').each( function(i,e) {
            var id = $(e).attr('id');
            elementArray.push(id);
            var idSplice = id.slice(15,id.length);
            var photoBlockItems = new Array(); 

            var caption_id = "photoGroupText" + idSplice;
            var caption = document.getElementById(caption_id).value;
            caption = caption.replace(/"/g, "_~_~_~");  
            caption = caption.replace(/'/g, "@@@@@@");
            caption = caption.replace(/[?]/g, "-%-%-%-%-");
            photoBlockItems.push(caption);
            
            $('#'+id).children('div').each(function () {
              var childId = this.id;
              var childImage = $("#"+childId).css('background-image');
              if(childImage){
                var passImg = childImage.split('userphotos/');
                var b = passImg[1];
                b.slice(0,-1);                
                passImg = b.split("&w");
                photoBlockItems.push(passImg[0]);
              }
               
            });
           
            b[id] = photoBlockItems;
            elementArray.push(photoBlockItems);

        });

        console.log(b);
        storySave(title,b);

      }
      

        $("#saveStory").click(function(){
          pushStory();
        });
        $("#publishStory").click(function(){
          pushStory();
          publishStory();
        });

        $(document).bind('drop dragover', function (e) {
            e.preventDefault();
        });

        //implementing our drag and drop
        $(document).on('dragenter', function (e) 
        {
          e.stopPropagation();
          e.preventDefault();
        });
        $(document).on('dragover', function (e) 
        {
          e.stopPropagation();
          e.preventDefault();

        });
        $(document).on('drop', function (e) 
        {
          e.stopPropagation();
          e.preventDefault();
        });

    });


    function setCoverPic(picture){

      $("#storyCover").css("background-color",'rgb(21,22,23)');
      $("#storyCover").css("background-image",'url('+picture+')');
      $("#modalbg").hide();
      $("#coverPictureModal").hide();

    }

    function addFullPic(picture){
      var fullid = "fullPicture"+newid;
      var caption = "fullsizeStoryCaptionDiv" + newid
      var captionvalue = "fullsizeStoryCaptionValue" + newid
      var jquery_fullid = "#"+fullid;
      var windowheight = $( window ).height();

      $("#storyWrap").append('<div id=\''+ fullid +'\' class="fullsizeStoryPic"><div class="fullPicDelete"><img id=\"fullPicDelete' + newid + '"\" class=\"fullPicDelete_x\" src=\"img/delete.png\" /></div></div><div id="fullsizeStoryCaptionDiv'+newid+'" class="fullsizeStoryPicCaption"><textarea  id=\"'+captionvalue+'\" rows=\"1\" value=\"Untitled Story\" class=\"fullsizeStoryPicCaption_input\">Add a note to this picture</textarea></div>');
      $(jquery_fullid).css("background-image",'url('+picture+')');
      $(jquery_fullid).height(windowheight);

      $("#modalbg").hide();
      $("#fullPictureModal").hide();
      $("#groupPictureModal").hide();

      $("#fullPicDelete" + newid).on( "click", function() {
          $("#"+fullid).remove();

          $("#"+caption).remove();
      });

      newid++;
    }

    function addFullPic_Group(pictureId, localPicId){
        numberCurrentPics++;
        var currentLength = pictureList.length;

        if(numberCurrentPics > 5){
            for(var i = 0; i < currentLength; i++){
              pictureList[i] = "used";
                if(idList[i] != "NV"){
                   $(idList[i]).css("opacity","1.0");
                }
                else{
                  idList[i] = "NV";
                }
            }
            console.log(pictureList);
            numberCurrentPics = 1;
        }
        else{
          pictureList[pictureListPos] = pictureId;
          idList[pictureListPos] = localPicId;
          pictureListPos++;
          $(localPicId).css("opacity","0.5");
        }
        
    }

    function disableScroll(){
      $('html, body').css({
          'overflow': 'hidden',
          'height': '100%'
        }) 
    }
    function enableScroll(){
      $('html, body').css({
          'overflow': 'auto',
          'height': ''
      })
    }

  

    </script>
    
    <!--Google Analytics-->
    <?php include_once("analyticstracking.php") ?>

</head>


<body style="background-color:rgb(35,35,45)!important;">
<div id="addTitleInfo" class="fillDomainDiv" >
    <div>Please add a title and subtitle</div>
</div>

<div style="overflow:hidden;padding-top:0px;background-color:rgb(48,49,62);">
    <?php navbar(); ?>
</div>

<div class="toolBar" ><div id="saveStory"><img src="img/story_save_icon.png" /><br>Save</div><div id="publishStory"><img src="img/story_publish_icon.png" /><br>Publish</div></div>
<div id="modalbg" style="position:fixed;top:0!important;"></div>

<!--What Stories Are-->
<div style="width:100%;height:60px;background-color:rgb(0,0,0);text-align:center;color:#eee;font-size:16px;">
  <p style="width:900px;margin:auto;padding-top:5px;">
    Coversplash provides artists the ability to create elegant narratives through photos, videos, and text. Try it out by writing your first story below.
  </p>
</div>

<div id="coverPictureModal" class="pictureModal">
  <div class="modalHead" style="position:fixed;width:760px;z-index: 2147483647;overflow:hidden;">Select a photo</div>
  <div id="coverPictureModal_scroll" class="pictureSelect" style="margin-top:80px;">

    <?
      $photosquery = mysql_query("SELECT * FROM photos WHERE emailaddress = '$email' ORDER BY id DESC LIMIT 20");
      $numphotos = mysql_num_rows($photosquery);
      for($i = 0; $i < $numphotos; $i++){

        $picture = mysql_result($photosquery,$i,'source');
        $background = getTimThumb($picture,180,180);
        $largepic = getTimThumb($picture,-1,800);
        $id =  mysql_result($photosquery,$i,'id');
        echo'
        <div id="coverpic_'.$id.'" class="to_be_Selected_cover" style="background-image:url('.$background.');">

        </div>

        <script type="text/javascript">
            $( document ).ready(function(){
                $("#coverpic_'.$id.'").click(function(){   
                    setCoverPic("'.$largepic.'");
                });
            });

        </script>
        ';
      }
    ?>
  <script type="text/javascript">
    var last = 0;
    $("#coverPictureModal").scroll(function(){
        if($(this)[0].scrollHeight - $(this).scrollTop() <= $(this).outerHeight()) {
            if(last != $(".to_be_Selected_cover:last").attr("id")) {

                $.ajax({
                    url: "loadStoryPics/" + $(".to_be_Selected_cover:last").attr("id") + "/<?php echo $email; ?>" + "/cover",
                    success: function(html) {
                        if(html) {
                            $("#coverPictureModal_scroll").append(html);

                        }
                    }
                });

                last = $(".to_be_Selected_cover:last").attr("id");
              }
            }
        });
    </script>
  </div>

</div>

<div id="fullPictureModal" class="pictureModal">
  <div class="modalHead" style="position:fixed;width:760px;z-index: 2147483647;overflow:hidden;">Select a photo</div>
  <div id="fullPictureModal_scroll" class="pictureSelect" style="margin-top:80px;">

    <?
      $photosquery = mysql_query("SELECT * FROM photos WHERE emailaddress = '$email' ORDER BY id DESC LIMIT 20");
      $numphotos = mysql_num_rows($photosquery);
      for($i = 0; $i < $numphotos; $i++){
        $picture = mysql_result($photosquery,$i,'source');
        $background = getTimThumb($picture,180,180);
        $largepic = getTimThumb($picture,-1,800);
        $id =  mysql_result($photosquery,$i,'id');
        echo'
        <div id="'.$id.'" class="to_be_Selected_full" style="background-image:url('.$background.');">

        </div>

        <script type="text/javascript">
            $( document ).ready(function(){
                $("#'.$id.'").click(function(){   
                    addFullPic("'.$largepic.'");
                });
            });

        </script>
        ';
      }
    ?>
  <script type="text/javascript">
    var last = 0;
    $("#fullPictureModal").scroll(function(){
        if($(this)[0].scrollHeight - $(this).scrollTop() <= $(this).outerHeight()) {
            // window.alert('past');
            if(last != $(".to_be_Selected_full:last").attr("id")) {

                $.ajax({
                    url: "loadStoryPics/" + $(".to_be_Selected_full:last").attr("id") + "/<?php echo $email; ?>" + "/full",
                    success: function(html) {
                        if(html) {
                            $("#fullPictureModal_scroll").append(html);
                        }
                    }
                });
                last = $(".to_be_Selected_full:last").attr("id");

              }
            }
        });
    </script>
  </div>

</div>

<div id="groupPictureModal" class="pictureModal">
  <div class="modalHead" style="position:fixed;width:760px;z-index: 2147483647;overflow:hidden;">Select a photo  <div id="savePictureGroup" style="float:right;height:100%;font-size:20px;color:black;cursor:pointer;padding:20px;">Save</div></div>
  <div id="groupPictureModal_scroll" class="pictureSelect" style="margin-top:80px;">

    <?
      $photosquery = mysql_query("SELECT * FROM photos WHERE emailaddress = '$email' ORDER BY id DESC LIMIT 20");
      $numphotos = mysql_num_rows($photosquery);
      for($i = 0; $i < $numphotos; $i++){
        $picture = mysql_result($photosquery,$i,'source');
        $background = getTimThumb($picture,180,180);
        $largepic = getTimThumb($picture,-1,800);
        $id =  mysql_result($photosquery,$i,'id');
        echo'
        <div id="groupPic_'.$id.'" class="to_be_Selected_group" style="background-image:url('.$background.');">

        </div>
        <script type="text/javascript">
            $( document ).ready(function(){
                $("#groupPic_'.$id.'").click(function(){   
                     addFullPic_Group("'.$largepic.'", "#groupPic_'.$id.'");
                });
            });
        </script>
        ';
      }
    ?>
    <script type="text/javascript">
    var last = 0;
    $("#groupPictureModal").scroll(function(){
        if($(this)[0].scrollHeight - $(this).scrollTop() <= $(this).outerHeight()) {
            // window.alert('past');
            if(last != $(".to_be_Selected_group:last").attr("id")) {
                $.ajax({
                    url: "loadStoryPics/" + $(".to_be_Selected_group:last").attr("id") + "/<?php echo $email; ?>" + "/group",
                    success: function(html) {
                        if(html) {
                            $("#groupPictureModal_scroll").append(html);
                        }
                    }
                });
                last = $(".to_be_Selected_group:last").attr("id");

              }
            }
        });
    </script>
  </div>

</div>
<div id="videoModal" class="pictureModal" style="margin-top:-20px;text-align:center;height:400px;">
  <div class="modalHead" style="position:fixed;width:760px;z-index: 2147483647;overflow:hidden;"><!-- Enter a quote -->  <div id="saveVideoEmbed" style="float:right;height:100%;font-size:20px;color:black;cursor:pointer;padding:20px;">Save</div></div>
  <div style="width:100%;text-align:center;"><textarea id="youtubeLink">Paste a YouTube Link Here</textarea></div>
  <div style="width:100%;text-align:center;"><textarea id="vimeoLink">Paste a Vimeo Link Here</textarea></div>
</div>

<div id="mapsModal" class="pictureModal" style="display:block;margin-left:400%;margin-top:-20px;text-align:center;height:400px;">
<input id="pac-input" style="top:10px!important;outline:none!important;outline-color: transparent!important;" class="controls" type="text" placeholder="Search Box">
<div id="map-canvas"></div>
</div>
<div class="saveMapModal">
  <div id="saveMap">Add Location</div>
</div>

<div id="storyWrap" class="storyWrap">
  <div class="story_cover" id="storyCover">

      <div id="title1" class="titleDiv">
         <textarea rows="1" id="story_title" value="Untitled Story" class="story_title">Untitled Story</textarea>
      </div>
      <div class="titleDiv">
         <textarea id="story_subtitle" value="Untitled Story" class="story_subtitle">Enter a Subtitle</textarea>
      </div>

      <div class="titleDiv">
          <div class="imageButton"><div id="selectImage">Click to select an image from your portfolio</div></div>
      </div>

      <div class="titleDiv" style="margin-top:30px;">
         <div class="picDiv"><img src="<?php echo $profpic; ?>" /></div>
      </div>
      <div class="titleDiv" style="margin-top:10px;">
         <div class="preNameDiv">Story By&nbsp</div><div class="nameDiv"><?php echo $name; ?></div>
      </div>
      <div class="titleDiv" style="margin-top:10px;">
         <div class="preNameDiv"><?php echo $time; ?></div>
      </div>

      <div id="arrowDown"></div>
  </div>
  

</div>


<div id="toolbox" class="storyToolWrap">
      <div class="storyHead">CREATE</div>
      <div class="toolBox">
          <div id="addPhotoGroup" class="toolElement">
            <div  class="toolHead">PHOTO GROUP</div>
            <div class="toolPic" ><img src="img/stories_multiplepictures.png" /></div>
            <div class="toolDescription" style="margin-top:15px;">Add a group of 2 to 4 photos</div>
            <div class="toolDescription" style="margin-top:-15px;">Click to select</div>

          </div>
          <div id="addFullsizePhoto" class="toolElement">
            <div class="toolHead">FULLSIZE PHOTO</div>
            <div class="toolPic"><img src="img/stories_singlepicture.png" /></div>
            <div class="toolDescription" style="margin-top: 15px;">Add a full size photo</div>
            <div class="toolDescription" style="margin-top:-15px;">Click to select from your portfolio</div>

          </div>

          <div id="addTextBlock" class="toolElement" >
            <div class="toolHead">TEXT BLOCK</div>
            <div class="toolPic" style="margin-top: 15px;"><img src="img/stories_textblock.png" /></div>
            <div class="toolDescription" style="margin-top: 25px;">Add a block of text</div>
          </div>

      </div>
</div>
<div id="toolbox" class="storyToolWrap" style="border-top:1px solid rgba(0,0,0,0);margin-top:-40px;">
      <div class="toolBox">
          <div id="addBlockQuote" class="toolElement">
            <div class="toolHead">BLOCK QUOTE</div>
            <div class="toolPic"><img src="img/stories_blockquote.png" style="opacity:0.5;margin-top:15px;"/></div>
            <div class="toolDescription" style="margin-top: 25px;">Add a quote</div>
 
          </div>
          <div id="addVideoLink" class="toolElement">
            <div class="toolHead">VIDEO LINK</div>
            <div class="toolPic"><img src="img/stories_videolink.png" style="opacity:0.35;"/></div>
            <div class="toolDescription" style="margin-top: 15px;">Add an embedded video</div>

          </div>

          <div id="addLocationTag" class="toolElement" >
            <div class="toolHead">LOCATION</div>
            <div class="toolPic"><img src="img/stories_locationtag.png" style="opacity:0.35;" /></div>
            <div class="toolDescription" style="margin-top: 15px;">Add a Location</div>
          </div>

      </div>
</div>
    <script type="text/javascript">
    var markers = [];
    var iconBase = 'graphics/';

    function initialize() {
      geocoder = new google.maps.Geocoder();
      map = new google.maps.Map(document.getElementById('map-canvas'), {
        zoom: 2,
        mapTypeControl: true,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      });

      var defaultBounds = new google.maps.LatLngBounds(
          new google.maps.LatLng(39.19075519349455, -78.208984375),
          new google.maps.LatLng(38.19075519349455, -77.3208984375));

      map.fitBounds(defaultBounds);
      map.setZoom(1);
      // Create the search box and link it to the UI element.
      var input = /** @type {HTMLInputElement} */(
          document.getElementById('pac-input'));
      map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

      var searchBox = new google.maps.places.SearchBox((input));
      marker = new google.maps.Marker({
        map: map,
        draggable:true,
        title: "DC",
        position: new google.maps.LatLng(38.9075519349455, -77.3208984375)
      });

      google.maps.event.addListener(searchBox, 'places_changed', function() {
        var places = searchBox.getPlaces();

        for (var i = 0, marker; marker = markers[i]; i++) {
          marker.setMap(null);
        }
        // For each place, get the icon, place name, and location.
        markers = [];
        var bounds = new google.maps.LatLngBounds();
        for (var i = 0, place; place = places[i]; i++) {
          bounds.extend(place.geometry.location);
          map.panTo(bounds.getCenter());
          addMarker(place.geometry.location);
          marker.setPosition(place.geometry.location);
          map.setZoom(1);
          map.fitBounds(bounds);
        }
        google.maps.event.trigger(map, 'resize');
        map.panTo(bounds.getCenter());
        map.setZoom(1);
        map.fitBounds(bounds);
      });

      // Bias the SearchBox results towards places that are within the bounds of the
      // current map's viewport.
      google.maps.event.addListener(map, 'bounds_changed', function() {
        var bounds = map.getBounds();
        searchBox.setBounds(bounds);
      });
      google.maps.event.addListener(map, 'click', function(event) {
          addMarker(event.latLng);
          geocoder.geocode({'latLng': event.latLng}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
              if (results[1]) {
                $("#pac-input").val(results[1].formatted_address);
              } else {
              }
            }
          });
      });
      google.maps.event.addListener(marker, 'drag', function(event){
        addMarker(event.latLng);
          geocoder.geocode({'latLng': event.latLng}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
              if (results[1]) {
                $("#pac-input").val(results[1].formatted_address);
              } else {
              }
            }
          });
    });     
  }
  
  function addMarker(location) {
      marker.setPosition(location);
	}
	function clearMarkers() {
	  setAllMap(null);
	}
	function deleteMarkers() {
	  clearMarkers();
	  markers = [];
	}
	// Sets the map on all markers in the array.
	function setAllMap(map) {
		for (var i = 0; i < markers.length; i++) {
		markers[i].setMap(map);
		}
	}

  google.maps.event.addDomListener(window, 'load', initialize);

    </script>
</body>
</html>