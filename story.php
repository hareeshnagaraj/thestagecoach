<?php
/*
  This is the stories display feature
*/

//connect to the database
require "db_connection.php";
require "functions.php";
require "template_functions.php";


$storyQuery = pr_mysql_query("SELECT * FROM stories WHERE id = '$storyId'");
$storyTitle = mysql_result($storyQuery,0,'title');
$storyContent =  (mysql_result($storyQuery,0,'content'));
$storyOwner =  trim((mysql_result($storyQuery,0,'emailaddress')));
$storyTime =  (mysql_result($storyQuery,0,'time'));
$storyURL = "http://coversplash.com/story/".$storyId;

$time = formatUnixTime($storyTime);
$ownerInfo = getInfo($storyOwner);
$ownerPic = "https://photorankr.com/".$ownerInfo['profilepic'];
$ownerName = $ownerInfo["fullname"];
$ownerDomain = $ownerInfo["domain"];

session_start();
if(!isset($_SESSION['email'])) {

$email = "";

}
else{
    $email = $_SESSION['email'];

}
if($storystatus == 1){
    $initialAccess = 1;

}
else{
    $initialAccess = 0;
}
$ownerView = 0;
if($email == $storyOwner){
    $ownerView = 1;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <link rel="shortcut icon" type="image/x-png" href="graphics/favicon.png"/>

    <title>Story</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://photorankr.com/css/newfeed.css"/>
    <link rel="stylesheet" type="text/css" href="https://photorankr.com/css/newsfeedmatt.css"/>
    <link rel="stylesheet" type="text/css" href="https://photorankr.com/css/galleries.css"/>
    <link rel="stylesheet" type="text/css" href="https://photorankr.com/css/newprofile2.css"/>
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900,300italic,400italic,700italic' rel='stylesheet' type='text/css'>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyAEby7YD0fAH-SKQamGMydNJtK-oL7g6vA&sensor=true">
    </script>
    <script>
    var ownerView = "<?php echo $ownerView ?>";
    function keys(obj)
    {
        var keys = [];

        for(var key in obj)
        {
            if(obj.hasOwnProperty(key))
            {
            keys.push(key);
            }
        }

        return keys;
    }
    function createRequestObject() {
        var ajaxRequest;  //ajax variable
        try{
            // Opera 8.0+, Firefox, Safari
            ajaxRequest = new XMLHttpRequest();
        } catch (e){
            // Internet Explorer Browsers
            try{
                ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try{
                    ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e){
                    // Something went wrong
                    alert("Your browser broke!");
                    return false;
                }
            }
        }
    return ajaxRequest;
    }
    var mapid = 0;
    var locationDict = {};
    var current_story_id = "<?php echo $storyId; ?>";
    var fullsizepicid = 0;
    var video_id_global = 0;
    var groupphoto_id_global = 0;
    var editmode = 0;
    var global_id=0;
    var marker;

    var numberCurrentPics = 1;
    var pictureList = new Array();
    var idList = new Array();
    var pictureListPos = 0;

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
      var regExp = /https:\/\/(www\.)?vimeo.com\/(\d+)($|\/)/;
      var match = url.match(regExp);
      if (match){
       return(match[2]);
      }else{
          var regExp = /http:\/\/(www\.)?vimeo.com\/(\d+)($|\/)/;
          var match = url.match(regExp);
          if(match){
            return(match[2]);
          }
          else{

          }
      }
    }
    function editToggle(){
        if(editmode == 0){
             $('#edit').html("SAVE");
             $("#toolbox1").fadeIn();
             $("#toolbox").fadeIn();

             // $('#toolbox1').css('opacity',0).animate({opacity:1}, 1000);
             
             // $("#toolbox1"+localid).fadeIn(300, function(){ $(this).show();});
             // $("#toolbox"+localid).fadeIn(300, function(){ $(this).show();});

             $('.cover_edit').each( function(i,e) {
                $(this).show();
             });
             $('.group_edit').each( function(i,e) {
                $(this).show();
             });
             $('.delete_icon').each( function(i,e) {
                $(this).show();
             });
             $('.titleTextDiv').attr("contenteditable","true");
             $('.subTitleText').attr("contenteditable","true");
             

             $('.groupPhotoCaptionInner').each( function(i,e) {
                $(this).attr("contenteditable","true");
             });
             $('.fullsizeStoryCaptionInner').each( function(i,e) {
                $(this).attr("contenteditable","true");
                $(this).css('color','rgb(99,164,255);');
             });
             $('.textBoxBody').each( function(i,e) {
                $(this).attr("contenteditable","true");
             });
             $('.textBoxHeaderDiv').each( function(i,e) {
                $(this).attr("contenteditable","true");
             });
             $('.blockQuoteHeader').each( function(i,e) {
                $(this).attr("contenteditable","true");
             });
             $('.blockQuoteAuthor').each( function(i,e) {
                $(this).attr("contenteditable","true");
             });
             $('.videoBlockCaptionInner').each( function(i,e) {
                $(this).attr("contenteditable","true");
             });
             
             editmode = 1;
        }
        else{
            var title = document.getElementById("story_title").innerHTML;
            var subtitle = document.getElementById("story_subtitle").innerHTML;
            var story_cover = $("#storyCover").css('background-image');

            if(title == "Untitled Story" || subtitle == "Add a subtitle"){
                $('#addTitleInfo > div').html("Please add a title and subtitle");
                $('#addTitleInfo').fadeIn(500); 
                $('#addTitleInfo').delay(3000).fadeOut(1000);
                return;
            }
            var children = $("#storyWrap > div");
            if(children <= 4){
                $('#addTitleInfo > div').html("Please add elements to your story");
                $('#addTitleInfo').fadeIn(500); 
                $('#addTitleInfo').delay(3000).fadeOut(1000);
                return;
            }

            $('#edit').html("EDIT");
            $("#toolbox1").fadeOut();
             $("#toolbox").fadeOut();

            $('.cover_edit').each( function(i,e) {
                $(this).hide();
             });
            $('.group_edit').each( function(i,e) {
                $(this).hide();
             });
            $('.delete_icon').each( function(i,e) {
                $(this).hide();
             });
            $('.titleTextDiv').attr("contenteditable","false");
            $('.subTitleText').attr("contenteditable","false");
            $('.groupPhotoCaptionInner').each( function(i,e) {
                $(this).attr("contenteditable","false");
             });
            $('.fullsizeStoryCaptionInner').each( function(i,e) {
                $(this).attr("contenteditable","false");
                $(this).css('color','rgb(80,90,100);');
             });
            $('.textBoxBody').each( function(i,e) {
                $(this).attr("contenteditable","false");
             });
            $('.textBoxHeaderDiv').each( function(i,e) {
                $(this).attr("contenteditable","false");
             });
            $('.blockQuoteHeader').each( function(i,e) {
                $(this).attr("contenteditable","false");
             });
            $('.blockQuoteAuthor').each( function(i,e) {
                $(this).attr("contenteditable","false");
             });
            $('.videoBlockCaptionInner').each( function(i,e) {
                $(this).attr("contenteditable","false");
             });
            editmode = 0;
            pushStory();
        }

    }
    </script>
    <style>
    #map-canvas{
         height: 100%;
        margin: 0px;
        padding: 0px
    }
    .gmapsWrap {
    width: 100%;
    height: 600px;
    /* margin-top: 2px; */
    background-color: white;
    }
    #pac-input {
      background-color: #fff;
      padding: 0 11px 0 13px;
      width: 400px;
      font-family: Roboto;
      font-size: 15px;
      font-weight: 300;
      text-overflow: ellipsis;
      outline: none;
    }
    #pac-input:focus {
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
    #youtubeLink {
    outline: none;
    background-color: rgba(21,22,23,0);
    border: none;
    color: rgb(80,90,100);
    font-family: Garamond;
    font-size: 25px;
    text-align: center;
    resize: none;
    margin-top: 100px;
    min-height: 100px;
    width: 80%;
    }
    #vimeoLink {
    outline: none;
    background-color: rgba(21,22,23,0);
    border: none;
    color: rgb(80,90,100);
    font-family: Garamond;
    font-size: 25px;
    text-align: center;
    resize: none;
    margin-top: 10px;
    min-height: 100px;
    width: 80%;
    }
    #saveMap {
    padding: 10px;
    cursor: pointer;
    position: fixed;
    background-color: white;
    z-index: 2147483647;
    color: rgba(0,0,0,0.6);
    font-family: Roboto;
    display: none;
    }
    .saveMapModal {
    width: 800px;
    height: 50px;
    border-radius: 5px;
    margin-top: -50px;
    position: fixed;
    top: 18.5%;
    left: 50%;
    margin-left: -400px;
    overflow: scroll;
    overflow-x: hidden;
    text-align: center;
    z-index: 2000;
    display: none;
    }
    html,body{
    	width: 100%;
    	height: 100%;
    	margin:0;
        background-color: white;
    }
    #storyWrap{
        height: 100%;
        box-shadow: 4px 2px rgb(0,0,0);
    }
    .story_cover{
		width: 100%;
		height: 100%;
		background-color: white;
		background-size: cover;
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
      overflow: hidden;
    }
    .modalHead{
        width: 100%;
        font-family: 'Lato',serif;
        font-weight: normal;
        text-align: left;
        padding: 20px;
        padding-top: 10px;
        font-size: 30px;
        background-color: white;
    }
    .to_be_Selected_cover {
        width: 180px;
        height: 180px;
        overflow: hidden;
        display: inline-block;
        margin: 5px;
        cursor: pointer;
    }
    .to_be_Selected_full{
        width: 180px;
        height: 180px;
        overflow: hidden;
        display: inline-block;
        margin: 5px;
        cursor: pointer;
    }
    .to_be_Selected_group{
        width: 180px;
        height: 180px;
        overflow: hidden;
        display: inline-block;
        margin: 5px;
        cursor: pointer;
    }
    .to_be_Selected_groupAdd{
        width: 180px;
        height: 180px;
        overflow: hidden;
        display: inline-block;
        margin: 5px;
        cursor: pointer;
    }
    .textBoxDelete {
        position: absolute;
        right: 0;
        margin-right: 65px;
        margin-top: 10px;
        cursor: pointer;
        z-index: 2147483647;
    }
    .fillDomainDiv{
        display: none;
        width: 500px;
        height: 40px;
        position: fixed;
        top: 50%;
        left: 50%;
        margin-left: -250px;
        margin-top: -210px;
        font-weight: 500;
        border-radius: 6px;
        font-family: 'Helvetica Neue', Helvetica, sans-serif;
        background-color: rgba(91,164,229,0.8);
        z-index: 100;
        font-family: Open Sans;
        text-align: center;
        color:white;
    }
    .fillDomainDiv > div{
        margin-top:8px;
    }
    .pictureModal{
        width: 800px;
        height: 500px;
        background-color: white;
        border-radius: 5px;
        margin-top: -50px;
        position: fixed;
        top: 20%;
        left: 50%;
        margin-left: -400px;
        overflow: scroll;
        overflow-x: hidden;
        display: none;
        z-index: 2000;
    }
    .pictureSelect {
    width: 100%;
    text-align: center;
    }
    @media handheld,screen and (min-width: 730px) {

    .storyLine{
        width:100%;
        padding-top:100px;
        padding-bottom: 100px;
        position: relative;
    }

    .storyFooter{
        width:100%;
        height: 300px;
        background-color: #f5f5f5;;
        text-align: center;
        border-top: 1px solid rgb(80,90,100);
    }
    .storyFooterInner{
        width: 80%;
        margin: auto;
        margin-top: 50px;
        font-family: 'Garamond';
        font-size:25px;
        text-align: center;
    }
    .storyFooterInner > div {
        display: inline-block;
        text-align: center;
    }
    .storyFooterInner > div > ul {
        padding:0px;

    }
    .storyFooterInner > div > ul > li {
        display: inline;
        cursor:pointer;
        margin:10px;
    }
    .storyFooterInner > div > ul > li > a > img{
        width: 30px;
    }

    .dialogBox{
    position: fixed;
    width: 600px;
    left:50%;
    top:50%;
    margin-left:-305px;
    margin-top:-100px;
    background-color: white;
    text-align: center;
    z-index: 400;
    font-size: 30px;
    height: 180px;
    display: none;
    }
    .dialogBox > div{
    margin:auto;
    text-align: left;
    margin-top: 30px;
    width: 80%;
    font-family: 'Garamond';
    color:rgb(80,90,100);

    }
    .dialogBox > ul{
    height:100px;
    width: 100%;
    margin-left: -40px;
    }
    .dialogBox > ul > li{
    width:50%;
    height: 80px;
    color:white;
    background-color: rgb(80,90,100);
    display: inline-block;
    cursor: pointer;
    }
    .dialogBox > ul > li > div{
    margin-top: 20px;
    }
    .toolElement:hover .toolPic > img{
        width:105px;
    }
    .toolPic{
        padding-top : 0px;
        width:100%;
        text-align: center;
        margin-top: 25px;
        -webkit-transition: 500ms ease-out 1s;
        -moz-transition: 500ms ease-out 1s;
        -o-transition: 500ms ease-out 1s;
        transition: 500ms ease-out 1s;
    }
    .toolPic > img{
        width:100px;
        -webkit-transition:   0.1s;
        -moz-transition: 0.1s;
        -o-transition:  0.1s;
        transition:  0.1s;
    }
    .toolElement {
        height: 300px;
        width: 300px;
        margin: 20px;
        margin-top: 0px;
        margin-left: 40px;
        border: 1px solid rgb(80,90,100);
        overflow: hidden;
        cursor: pointer;
        text-align: center;
        display: inline-block;
    }
    .toolHead {
        font-family: 'Lato',serif;
        font-weight: normal;
        font-size: 20px;
        margin: auto;
        padding-top: 30px;
        color: rgb(80,90,100);
    }
    .toolDescription {
        font-family: 'Lato',serif;
        font-weight: normal;
        font-size: 15px;
        margin: auto;
        padding-top: 15px;
        color: rgb(80,90,100);
    }
    .storyToolWrap {
        width: 100%;
        height: 60%;
        padding-bottom: 100px;
        border-top: 1px solid rgb(80,90,100);
        background-color: white;
        display: none;
    }
    .storyHead {
        padding: 40px;
        padding-top: 20px;
        width: 80%;
        margin: auto;
        text-align: center;
        font-family: 'Lato',serif;
        font-weight: normal;
        font-size: 50px;
        color: rgb(80,90,100);
    }

    /*Tablets  + desktops*/
    .toolBox{
        text-align: center;
    }

    .gmaps-canvas{
        width:100%;
        height: 600px;
        margin-top: 2px;
    }
    .photoElement_wide{
        width: 1000px;
        min-height: 400px;
        display: inline-block;
        margin: 10px 10px 10px;
        background-size: cover;
        position: relative;
    }
    .photoElement_4{
        width: 450px;
        min-height: 400px;
        display: inline-block;
        margin: 30px 50px 50px;
        background-size: cover;
        position: relative;
    }
     .story_cover{
        width: 100%;
        height: 100%;
        background-color: white;
        background-size: cover;
        overflow: hidden;
        background-repeat: no-repeat;
    }
    .titleDiv{
        width: 100%;
        margin: auto;
        text-align: center;
    }
    .subTitleDiv1{
        width: 100%;
        margin: auto;
        text-align: center;
        top:50%;
        position: absolute;
    }
    .subTitleDiv2{
        width: 100%;
        margin: auto;
        text-align: center;
        top:65%;
        position: absolute;
    }
    .subTitleDiv3{
        width: 100%;
        margin: auto;
        text-align: center;
        top:80%;
        position: absolute;
    }
    .subTitleDiv4{
        width: 100%;
        margin: auto;
        text-align: center;
        top:83%;
        position: absolute;
    }
    .titleTextDiv{
        font-family: 'Lato',serif;
        font-size: 90px;
        color: white;
        text-align: center;
        resize: none;
        padding: 0px;
        vertical-align: bottom;
        margin-top: 18%;
        min-height: 100px;
        outline:0px;
    }
    .subTitleText{
        font-family: 'Garamond';
        font-size: 40px;
        font-style: italic;
        text-align: center;
        resize: none;
        margin-top: 30px;
        color: white;
        margin-top: 20px;
        outline: 0px;
    }
    .picDiv{
        padding-top: 50px;
        margin: auto;
    }
    .picDiv > img{
        width:50px;
        height: 50px;
        border-radius: 50%;
    }
    .preNameDiv{
        font-family: 'Garamond';
        font-size: 20px;
        font-style: italic;
        color: white;
        display: inline-block;
    }
    .nameDiv {
        font-family: 'Lato',serif;
        font-size: 20px;
        color: white;
        display: inline-block;
    }
    .creationTimeDiv{
        font-family: 'Garamond';
        font-size: 20px;
        font-style: italic;
        color: white;
        display: inline-block;

    }
    #arrowDown{
        background-image: url('../img/arrow_down.png');
        background-size: cover;
        width: 50px;
        height: 50px;
        margin-left: 20px;
        margin-bottom: 40px;
        cursor: pointer;
        z-index: 2000;
    }
    #arrowDownDiv{
        margin-top: -20px;
    }
    .fullSizePic{
        width: 100%;
        margin-top: 2px;
        background-size: cover;
        position: relative;
    }
    .fullSizeStoryCaption{
        width: 100%;
        min-height: 50px;
        text-align: center;
        background-color: white;
        overflow: hidden;
        outline:none;
    }
    .fullsizeStoryCaptionInner{
        color: rgb(80,90,100);
        font-family: 'Garamond';
        font-size: 20px;
        text-align: center;
        resize: none;
        margin-top: 25px;
        min-height: 40px;
        outline:none;
    }
    .groupPhotoBlock{
        width: 100%;
        padding-top: 10px;
        padding-bottom: 10px;
        text-align: center;
        background-color: white;
        overflow: hidden;
        position: relative;
    }
    .groupPhotoCaption{
        width: 100%;
        min-height: 70px;
        text-align: center;
        background-color: white;
    }
    .groupPhotoCaptionInner{
        font-size: 20px;
        text-align: center;
        resize: none;
        padding-top: 5px;
        padding-bottom: 5px;
        color: rgb(80,90,100);
        font-family: 'Garamond';
        outline: none;

    }
    .textBlock{
        width: 100%;
        min-height: 200px;
        background-color: white;
        overflow: hidden;
        text-align: center;
    }
    .textBoxHeaderDiv{
        width: 100%;
        margin:auto;
        margin-top: 50px;
        color: rgb(80,90,100);
        font-family: Garamond;
        font-size: 35px;
        outline:none;
        position: relative;
    }
    .textBoxBody{
        font-family: 'Garamond';
        font-size: 25px;
        text-align: left;
        margin: auto;
        resize: none;
        width: 80%;
        margin-top: 10px;
        padding-bottom: 10px;
        color: rgba(0,0,0,0.7);
        outline:none;
        position: relative;
    }
    .blockQuote{
        width: 100%;
        padding-top: 10px;
        padding-bottom: 10px;
        background-color: white;
        overflow: hidden;
        text-align: center;
        outline: none;
    }
    .blockQuoteHeader{
        font-family: Garamond;
        font-size: 30px;
        padding: 0px;
        margin-top: 40px;
        padding-top: 10px;
        padding-bottom: 10px;
        text-align: center;
        color:rgb(80,90,100);
        outline: none;
    }
    .blockQuoteAuthor{
        font-family: Garamond;
        font-size: 30px;
        min-height: 50px;
        text-align: center;
        outline: none;
        position: relative;
        color:rgb(80,90,100);
    }
     .videoBlock{
        width: 100%;
        padding-top: 20px;
        padding-bottom: 10px;
        background-color: white;
        overflow: hidden;
        text-align: center;
        position: relative;

    }
    .videoBlock > iframe{
        width:800px;
        height:500px;
    }
    .videoBlockCaption{
        width: 100%;
        min-height: 70px;
        text-align: center;
        background-color: white;
    }
    .videoBlockCaptionInner{
        font-size: 20px;
        text-align: center;
        resize: none;
        padding-top: 30px;
        padding-bottom: 10px;
        color: rgb(80,90,100);
        font-family: 'Garamond';
        outline:none;
        position: relative;
    }

    .testBlock{
        position: fixed;
        width:100%;
        height:50px;
        right:0;
        overflow: hidden;
        top:0px;
        background-color: rgba(0,0,0,0.6);
        z-index: 200;
    }
    .testBlock > ul{
        display: inline;
        float: right;
        margin-right: 100px;
    }
    .testBlock > ul > li{
        display: inline;
        color:white;
        margin-left:20px;
        cursor: pointer;

    }
    .cover_edit{
        display: none;
        position: absolute;
        left: 50%;
        bottom: 10px;
        cursor: pointer;
        background-color: rgb(10,10,10);
        border-radius: 3px;
        padding: 0;
        width: 120px;
        height: 30px;
        margin-left:-60px;
        text-align: center;
        line-height: 30px;
        color:white;
        font-family: 'Lato',serif;

    }
    .cover_edit > img{
        width: 30px;
        margin-top: 10px;
    }
    .cover_arrow{
        display: none;
        position: absolute;
        left: 50px;
        bottom: 50px;
        cursor: pointer;
        background-color: rgba(0,0,0,0.2);
        border-radius: 50%;
        padding: 0;
        width: 50px;
        height: 50px;
        text-align: center;
    }
    .cover_arrow > img{
        width: 50px;
        margin-top: 10px;
    }
    .group_edit{
        display: none;
        position: absolute;
        left: 50%;
        bottom: 10px;
        cursor: pointer;
        background-color: rgb(10,10,10);
        border-radius: 3px;
        padding: 0;
        width: 70px;
        height: 30px;
        margin-left:-35px;
        text-align: center;
        line-height: 30px;
        color:white;
        font-family: 'Lato',serif;
    }
    .group_edit > img{
        width: 30px;
        margin-top: 10px;
    }
    .delete_icon{
        display: none;
        position: absolute;
        left: 50%;
        bottom: 10px;
        cursor: pointer;
        background-color: red;
        border-radius: 3px;
        padding: 0;
        width: 70px;
        height: 30px;
        margin-left:-35px;
        text-align: center;
        line-height: 30px;
        color:white;
        font-family: 'Lato',serif;
    }
    .delete_icon > img{
        width: 30px;
        margin-top: 10px;
    }


    }

    @media handheld,screen and (max-width: 729px) {
     .dialogBox{
        position: fixed;
        width: 600px;
        left:50%;
        top:50%;
        margin-left:-305px;
        margin-top:-100px;
        background-color: white;
        text-align: center;
        z-index: 400;
        font-size: 30px;
        height: 180px;
        display: none;
    }
    .dialogBox > div{
        margin:auto;
        text-align: left;
        margin-top: 30px;
        width: 80%;
        font-family: 'Garamond';
        color:rgb(80,90,100);

    }
    .dialogBox > ul{
        height:100px;
        width: 100%;
        margin-left: -40px;
    }
    .dialogBox > ul > li{
        width:50%;
        height: 100px;
        color:white;
        background-color: rgb(80,90,100);
        display: inline-block;
        cursor: pointer;
    }
    .dialogBox > ul > li > div{
     margin-top: 30px;
    }

    .toolElement {
    height: 300px;
    width: 300px;
    margin: 20px;
    margin-top: 0px;
    margin-left: 40px;
    border: 1px solid rgb(80,90,100);
    overflow: hidden;
    cursor: pointer;
    text-align: center;
    display: inline-block;
    display: none;
    }
    .toolHead {
    font-family: 'Lato',serif;
    font-weight: normal;
    font-size: 20px;
    margin: auto;
    padding-top: 30px;
    color: rgb(80,90,100);
    display: none;
    }
    .toolDescription {
    font-family: 'Lato',serif;
    font-weight: normal;
    font-size: 15px;
    margin: auto;
    padding-top: 15px;
    color: rgb(80,90,100);
    display: none;
    }
    .storyToolWrap {
    width: 100%;
    height: 60%;
    padding-bottom: 100px;
    border-top: 1px solid rgb(80,90,100);
    background-color: white;
    display: none;
    }
    .storyHead {
    padding: 40px;
    padding-top: 20px;
    width: 80%;
    margin: auto;
    text-align: center;
    font-family: 'Lato',serif;
    font-weight: normal;
    font-size: 50px;
    color: rgb(80,90,100);
    display: none;
    }

    .group_edit{
        display: none;
        position: absolute;
        right:20px;
        bottom:20px;
        cursor: pointer;
    }
    .group_edit > img{
        width:30px;
    }
    .testBlock{
        display: none;
    }
    .cover_edit{
    position: absolute;
    right:50px;
    bottom:50px;
    cursor: pointer;
    display: none;
    }
    .cover_edit > img{
    width:50px;
    }
    .subTitleDiv1{
        width: 100%;
        margin: auto;
        text-align: center;
        top:35%;
        position: absolute;
    }
    .subTitleDiv2{
        width: 100%;
        margin: auto;
        text-align: center;
        top:50%;
        position: absolute;
    }
    .subTitleDiv3{
        width: 100%;
        margin: auto;
        text-align: center;
        top:57%;
        position: absolute;
    }
    .subTitleDiv4{
        width: 100%;
        margin: auto;
        text-align: center;
        top:60%;
        position: absolute;
    }
    .gmaps-canvas{
        width:100%;
        height: 350px;
        margin-top: 2px;
    }
    .videoBlockCaption{
        width: 100%;
        min-height: 70px;
        text-align: center;
        background-color: white;
    }
    .videoBlockCaptionInner{
        font-size: 20px;
        text-align: center;
        resize: none;
        padding-top: 30px;
        padding-bottom: 30px;
        color: rgb(80,90,100);
        font-family: 'Garamond';
    }
    .videoBlock > iframe{
        width:100%;
        height:300px;
    }
    .videoBlock{
        width: 100%;
        padding-top: 10px;
        padding-bottom: 10px;
        background-color: white;
        overflow: hidden;
        text-align: center;

    }
      .blockQuoteAuthor{
        font-family: Garamond;
        font-size: 30px;
        padding-top: 20px;
        text-align: center;
        color:rgb(80,90,100);
    }
    .blockQuoteHeader{
        font-family: Garamond;
        font-size: 30px;
        padding: 0px;
        padding-top: 10px;
        padding-bottom: 10px;
        text-align: center;
        color:rgb(80,90,100);
        width:80%;
        margin: auto;
    }
    .blockQuote{
        width: 100%;
        padding-top: 20px;
        padding-bottom: 20px;
        background-color: white;
        overflow: hidden;
    }
      .textBoxBody{
        font-family: 'Garamond';
        font-size: 25px;
        text-align: left;
        margin: auto;
        resize: none;
        width: 80%;
        margin-top: 30px;
        padding-bottom: 30px;
        min-height: 150px;
        color: rgb(80,90,100);
    }
     .textBoxHeaderDiv{
        width: 80%;
        margin:auto;
        margin-top: 50px;
        color: rgb(80,90,100);
        font-family: Garamond;
        font-size: 35px;
    }
    .textBlock{
        width: 100%;
        padding-bottom: 10px;
        padding-top:10px;
        background-color: white;
        overflow: hidden;
        text-align: center;
    }
    .photoElement_wide{
        width: 100%;
        min-height: 400px;
        display: inline-block;
        background-size: cover;
    }
    .groupPhotoCaption{
        width: 100%;
        min-height: 50px;
        text-align: center;
        background-color: white;
        overflow: hidden;
    }
    .groupPhotoCaptionInner{
        font-size: 20px;
        text-align: center;
        resize: none;
        min-height: 50px;
        color: rgb(80,90,100);
        font-family: 'Garamond';

    }
    .groupPhotoBlock{
        width: 100%;
        padding-top: 30px;
        padding-bottom: 30px;
        text-align: center;
        background-color: white;
        overflow: hidden;
    }
    .photoElement_4{
        width: 100%;
        min-height: 400px;
        display: inline-block;
        background-size: cover;
    }
    .fullSizeStoryCaption{
        width: 100%;
        min-height: 50px;
        text-align: center;
        background-color: white;
    }
    .fullsizeStoryCaptionInner{
        color: rgb(80,90,100);
        font-family: 'Garamond';
        font-size: 20px;
        text-align: center;
        resize: none;
        margin-top: 25px;
        min-height: 40px;
    }
    .fullSizePic{
        width: 100%;
        margin-top: 2px;
        background-size: cover;
    }
    #arrowDownDiv{
        margin-top: -60px;
    }
    #arrowDown{
        background-image: url('../img/arrow_down.png');
        background-size: cover;
        width: 50px;
        height: 50px;
        margin-left: 20px;
        margin-bottom: 30px;
        cursor: pointer;
        z-index: 2000;
    }

    /*Phones + smaller screens */
     .story_cover{
        width: 100%;
        height: 70%;
        background-color: white;
        background-size: cover;
        overflow: hidden;
    }
    .titleDiv{
        width: 100%;
        margin: auto;
        text-align: center;
    }
    .titleTextDiv{
        font-family: 'Lato',serif;
        font-size: 60px;
        color: white;
        text-align: center;
        resize: none;
        padding: 0px;
        vertical-align: bottom;
        margin-top: 25%;
        min-height: 100px;
        outline:0px;
    }
    .subTitleText{
        font-family: 'Garamond';
        font-size: 30px;
        font-style: italic;
        text-align: center;
        resize: none;
        margin-top: 10px;
        color: white;
        outline:0px;
    }
    .picDiv{
        margin-top: 120px;
        margin: auto;
    }
    .picDiv > img{
        width:50px;
        height: 50px;
        border-radius: 50%;
    }
    .preNameDiv{
        font-family: 'Garamond';
        font-size: 20px;
        font-style: italic;
        color: white;
        display: inline-block;
    }
    .nameDiv {
        font-family: 'Lato',serif;
        font-size: 20px;
        color: white;
        display: inline-block;
    }
     .creationTimeDiv{
        font-family: 'Garamond';
        font-size: 20px;
        font-style: italic;
        color: white;
        margin-top: -20px;
        display: inline-block;

    }
    }

    </style>
    <script type="text/javascript">
    $(document).ready(function(){

        var scrollTimer = null;

/*

        //Future Scroll Functions

        $(window).scroll(function(){
            var offset = $(this).scrollTop();
            console.log(offset);
            debounce(handleScroll, 100);
        });
        function debounce(method, delay) {
            clearTimeout(method._tId);
            method._tId= setTimeout(function(){
                handleScroll();
            }, delay);
        }
        function handleScroll() {
            scrollTimer = null;
            var headerBottom = 50;
            var ScrollTop = $(window).scrollTop();
            if (ScrollTop > headerBottom) {
                // window.alert('s');
                $(".testBlock").animate({ "height": "50px" }, 300 );
            } else {
                $(".testBlock").animate({ "height": "80px" }, 300 );
            }
        }
*/
        $("#modalbg").click(function(){    

          $("#modalbg").hide();
          $("#coverPictureModal").hide();
          $("#fullsizePictureModal").hide();
          $("#groupPictureModal").hide();
          $("#videoModal").hide();
          $("#mapsModal").css('margin-left','400%');
          $("#saveMapModal").hide();
          $("#saveMap").hide();
          $("#addGroupPictureModal").hide();
          $("#deleteBox").hide();
          $('body').css('overflow', 'auto');

        });

    	var storyTitle = "<?php echo $storyTitle ?>";
        storyTitle = storyTitle.replace(/_~_~_~/g, "\"");
        storyTitle = storyTitle.replace(/@@@@@@/g, "\'");
    	storyTitle = storyTitle.replace(/_!!_!/g, "/");
        storyTitle = storyTitle.replace(/-%-%-%-%-/g, "?");

        var jsonEncode =  JSON.parse(('<?php print $storyContent; ?>')); 
    	// var storyTitle = JSON.parse(jsonEncode);
    	var storySubTitle = jsonEncode["storySubTitle"];
        storySubTitle = storySubTitle.replace(/_~_~_~/g, "\"");
        storySubTitle = storySubTitle.replace(/@@@@@@/g, "\'");
        storySubTitle = storySubTitle.replace(/_!!_!/g, "/");
        storySubTitle = storySubTitle.replace(/-%-%-%-%-/g, "?");

    	console.log(jsonEncode);
        if(jsonEncode["storyCoverPhoto"] != "none"){
            var storyCover = "https://photorankr.com/scripts/timthumb.php?src=https://photorankr.com/userphotos/"+jsonEncode["storyCoverPhoto"]+"&w=800&q=100";
            $("#storyCover").css('background-image','url(' + storyCover + ')');
        }
        else{
            $("#storyCover").css('background-color','black');
        }
        
        $(".titleTextDiv").html(storyTitle);
        $(".subTitleText").html(storySubTitle);
        $("#ownerPic").attr("src", "<?php echo $ownerPic; ?>");
        $(".nameDiv").html("<?php echo $ownerName; ?>");
        $(".creationTimeDiv").html("<?php echo $time; ?>");
        $("#saveMap").click(function(){ //addmap
            
            var markPos = marker.getPosition();
            var lat = markPos.lat();
            var lng = markPos.lng();
            var street;
            var newmarker;
            var newmapid = "mapEmbed"+global_id;
            var newmapcaptionid = "mapEmbedCaption"+global_id;
            var newmapdelete = "mapDelete"+global_id;
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
            console.log(locationDict);
            
            if(b = 0){
              street = "Add a caption to this map";
            }
            $("#toolbox1").before('<div id="'+newmapid+'" class="gmaps-canvas"></div><div id="captionwrap'+ newmapid +'" class=\"videoBlockCaption\" style=\'margin-top:0px;\'><div id="'+newmapcaptionid+'" class="videoBlockCaptionInner">Add a caption to this map<div class=\"delete_icon\" style="bottom: 20px;right: 20px;"><img id="delete'+global_id+'" src="http://coversplash.com/img/deletewhite.png" /></div></div></div><input type="hidden" id="mapInfoInput'+global_id+'" value="'+mapInfoArray+'" /></div>');
            $("#delete" + global_id).on( "click", function() {
                var localid = $(this).attr('id');
                localid = localid.replace("delete","");
                $("#mapEmbed"+localid).remove();
                $("#mapEmbedCaption"+localid).remove();
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
            global_id++;
            $("#saveMap").hide();
            $("#saveMapModal").hide();
            $("#mapsModal").css('margin-left','400%');
            $("#modalbg").hide();

        });

        $("#saveVideoEmbed").click(function(){
            var youtubeSrc = $("#youtubeLink").val();
            var vimeoSrc = $("#vimeoLink").val();
            var youtubeValid = youtubeSrc.indexOf("youtube");
            var vimeoValid = vimeoSrc.indexOf("vimeo");

            if(youtubeValid != "-1"){
                var final_youtube_src = youtube_parser(youtubeSrc);
                var displayLink = "//www.youtube.com/embed/"+final_youtube_src;

                 if(video_id_global > 0){
                    $("#videoLink"+video_id_global).attr('src',displayLink);
                 }
                 else{
                    var lastid = global_id;
                    $("#toolbox1").before("<div class='videoBlock' id='videoBlock"+lastid+"' ><iframe id='videoLink"+lastid+"'  src='"+displayLink+"' style='margin:auto;'></iframe><div class='videoBlockCaption'><div id='videoBlockCaption"+lastid+"'  class='videoBlockCaptionInner' >Add Caption Here</div><div class=\"cover_edit\"><img id='videoEdit" + lastid + "' src='http://coversplash.com/graphics/editwhite.png' /><div class=\"delete_icon\"><img id='delete" + lastid + "' src='http://coversplash.com/img/deletewhite.png' /></div></div></div>");
                    
                    $("#videoEdit"+lastid).click(function(){
                        $("#modalbg").show();
                        $("#videoModal").show();
                        var vidlocalid = $(this).attr('id');
                        vidlocalid = vidlocalid.slice(9,vidlocalid.length);
                        video_id_global = vidlocalid;
                    })
                    

                    global_id++;
                 }

                 $("#videoModal").hide();
                 $("#modalbg").hide();

            }
            else if(vimeoValid != "-1"){
                var final_vimeo_src = vimeo_parser(vimeoSrc);
                var displayLink = "//player.vimeo.com/video/"+final_vimeo_src;

                if(video_id_global > 0){
                     $("#videoLink"+video_id_global).attr('src',displayLink);
                }
                else{
                    var lastid = global_id;
                    $("#toolbox1").before("<div class='videoBlock' id='videoBlock"+lastid+"' ><iframe id='videoLink"+lastid+"'  src='"+displayLink+"' style='margin:auto;'></iframe><div class='videoBlockCaption'><div id='videoBlockCaption"+lastid+"' class='videoBlockCaptionInner' >"+vidcaption+"</div><div class=\"cover_edit\"><img id='videoEdit" + lastid + "' src='http://coversplash.com/graphics/editwhite.png' /><div class=\"delete_icon\"><img id='delete" + lastid + "' src='http://coversplash.com/img/deletewhite.png' /></div></div></div></div>");
                    global_id++;
                }

                $("#videoModal").hide();
                $("#modalbg").hide();
            }

        });
        $("#savePictureGroup").click(function(){
          picCount = 0;
          var picArray = new Array();
          for(var i = 0; i < pictureList.length; i++){
            if(pictureList[i] != "used"){
              picArray[picCount] = pictureList[i];
              picCount++;
            }
          }
          if(picCount == 2){
            $("#toolbox1").before('<div class="groupPhotoBlock" id="groupPhotoBlock'+global_id+'"></div>');
            var elementid = 1;
            for(var i = 0; i < pictureList.length; i++){
                if(pictureList[i] != "used"){
                    $("#groupPhotoBlock"+global_id).append('<div class="photoElement_4" id="photoElement_'+global_id+'_'+elementid+'"><div class=\"group_edit\"><img id="photoElement_'+id+'_'+elementid+'_edit"  src="http://coversplash.com/graphics/editwhite.png" /></div></div>');
                    $("#photoElement_"+global_id+"_"+elementid).css("background-image",'url('+pictureList[i]+')');
                    $("#photoElement_"+global_id+"_"+elementid+"_edit").click(function(){
                        $("#modalbg").show();
                        $("#groupPictureModal").show();
                        $('body').css('overflow', 'hidden');
                        groupphoto_id_global = $(this).attr('id');
                    });
                    elementid++;
                }
            }
          }
          else if(picCount == 3){
            $("#toolbox1").before('<div class="groupPhotoBlock" id="groupPhotoBlock'+global_id+'"></div>');
            var elementid = 1;
            for(var i = 0; i < pictureList.length; i++){
                if(pictureList[i] != "used"){
                    
                    if(elementid == 1){
                        $("#groupPhotoBlock"+global_id).append('<div class="photoElement_wide" id="photoElement_'+global_id+'_'+elementid+'"><div class=\"group_edit\"><img id="photoElement_'+id+'_'+elementid+'_edit"  src="http://coversplash.com/graphics/editwhite.png" /></div></div>');
                    }
                    else{
                        $("#groupPhotoBlock"+global_id).append('<div class="photoElement_4" id="photoElement_'+global_id+'_'+elementid+'"><div class=\"group_edit\"><img id="photoElement_'+id+'_'+elementid+'_edit"  src="http://coversplash.com/graphics/editwhite.png" /></div></div>');
                    }
                    $("#photoElement_"+global_id+"_"+elementid).css("background-image",'url('+pictureList[i]+')');
                    $("#photoElement_"+global_id+"_"+elementid+"_edit").click(function(){
                        $("#modalbg").show();
                        $("#groupPictureModal").show();
                        $('body').css('overflow', 'hidden');
                        groupphoto_id_global = $(this).attr('id');
                    });
                    elementid++;
                }
            }

          }
          else if(picCount == 4){
            var elementid = 1;
            $("#toolbox1").before('<div class="groupPhotoBlock" id="groupPhotoBlock'+global_id+'"></div>');
            for(var i = 0; i < pictureList.length; i++){
                if(pictureList[i] != "used"){
 
                    $("#groupPhotoBlock"+global_id).append('<div class="photoElement_4" id="photoElement_'+global_id+'_'+elementid+'"><div class=\"group_edit\"><img id="photoElement_'+id+'_'+elementid+'_edit"  src="http://coversplash.com/graphics/editwhite.png" /></div></div>');
                    
                    $("#photoElement_"+global_id+"_"+elementid).css("background-image",'url('+pictureList[i]+')');
                    $("#photoElement_"+global_id+"_"+elementid+"_edit").click(function(){
                        $("#modalbg").show();
                        $("#groupPictureModal").show();
                        $('body').css('overflow', 'hidden');
                        groupphoto_id_global = $(this).attr('id');
                    });
                    elementid++;
                }
            }
          }
          $("#toolbox1").before('<div class="groupPhotoCaption"><div class="groupPhotoCaptionInner" id="groupPhotoCaption'+global_id+'">Add a caption to this group of photos</div></div>');

          global_id++;
          $("#addGroupPictureModal").hide();
          $("#modalbg").hide();
          $('body').css('overflow', 'auto');

        });

        /*
        Now we parse the story from the server's JSON response and create an appropriately ordered array
        */
        var textblockstring = "textblock";
        var fullpicstring = "fullPicture";
        var groupphotostring = "groupPhotoBlock";
        var blockquotestring = "blockQuote";
        var videoblockstring = "videoBlock";
        var mapembedstring = "mapEmbed";
        var textblock_length = textblockstring.length;
        var fullpic_length = fullpicstring.length;
        var groupphoto_length = groupphotostring.length;
        var blockquote_length = blockquotestring.length;
        var videoblock_length = videoblockstring.length;
        var mapembed_length = mapembedstring.length;

        var DisplayArray = { };
        for (var key in jsonEncode) {

          var slice = key.slice(0, 8);
          console.log(slice);
          // window.alert(slice);
          if(slice == "textbloc"){
            var id = key.slice(textblock_length);
            var localMap = { };
            localMap[textblockstring] = jsonEncode[key];
            DisplayArray[id] = localMap;
          }
          else if(slice == "fullPict"){
            var id = key.slice(fullpic_length);
            var localMap = { };
            localMap[fullpicstring] = jsonEncode[key];
            DisplayArray[id] = localMap;
          } 
          else if(slice == "groupPho"){
            var id = key.slice(groupphoto_length);
            var localMap = { };
            localMap[groupphotostring] = jsonEncode[key];
            DisplayArray[id] = localMap;
          }
          else if(slice == "blockQuo"){
            var id = key.slice(blockquote_length);
            var localMap = { };
            localMap[blockquotestring] = jsonEncode[key];
            DisplayArray[id] = localMap;

          }
          else if(slice == "videoBlo"){
            var id = key.slice(videoblock_length);
            var localMap = { };
            localMap[videoblockstring] = jsonEncode[key];
            DisplayArray[id] = localMap;
          }
          else if(slice == "mapEmbed"){
            var id = key.slice(mapembed_length);
            var localMap = { };
            localMap[mapembedstring] = jsonEncode[key];
            DisplayArray[id] = localMap;
          }
        }
        keys(DisplayArray).sort();

        console.log(DisplayArray);
        for( var key in DisplayArray ){

            currentObject = DisplayArray[key];
            var type;
            if(currentObject[fullpicstring]){
                console.log(currentObject[fullpicstring]);
                var pic = "https://photorankr.com/userphotos/" + currentObject[fullpicstring][0];
                var caption = currentObject[fullpicstring][1];
                caption = caption.replace(/_~_~_~/g, "\"");
                caption = caption.replace(/@@@@@@/g, "\'");
                caption = caption.replace(/_!!_!/g, "/");
                caption = caption.replace(/-%-%-%-%-/g, "?");

                $("#storyWrap").append('<div class="fullSizePic" id="fullPicture'+id+'" style=""><div class="cover_edit" style="width:70px;margin-left:-75px;" id="fullsizeEdit'+id+'">EDIT</div><div style="width:70px;margin-left:5px;" id="delete'+id+'" class="delete_icon">DELETE</div><img id="fullsizePic'+id+'"  style="width:100%" src = "'+pic+'" /></div>');
                
                if(caption != "Add a note to this picture"){
                    $("#storyWrap").append('<div id="fullsizeStoryCaption'+id+'"  class="fullSizeStoryCaption" ><div id="fullsizeStoryCaptionValue'+id+'" class="fullsizeStoryCaptionInner" >' + caption + '</div></div>');
                }

                $("#storyWrap").append('<div class="storyLine" id="storyLine'+id+'"><hr noshade size=1 width=300></div>');

                var localid  = id;
                $("#fullsizeEdit"+localid).click(function(){
                    var localnew = $(this).attr('id');
                    localnew = localnew.slice(12,localnew.length);
                    // window.alert(localnew);
                    fullsizepicid = localnew;
                    $('body').css('overflow', 'hidden');
                    $("#modalbg").show();
                    $("#fullsizePictureModal").show();
                });
                $("#delete"+localid).click(function(){
                    var deleteid = $(this).attr('id');
                    deleteid = deleteid.replace("delete","");
                    // window.alert(deleteid);
                    $("#storyLine"+deleteid).fadeOut(300, function(){ $(this).remove();});
                    $('#fullPicture'+deleteid).fadeOut(300, function(){ $(this).remove();});
                    $("#fullsizeStoryCaption"+deleteid).fadeOut(300, function(){ $(this).remove();});
                    // $("#fullPicture"+deleteid).remove();
                    // $("#fullsizeStoryCaption"+deleteid).remove();
                });
    
                //}
                id++;
                global_id=id;
            }
            else if(currentObject[textblockstring]){

                console.log(currentObject[textblockstring]);
                var header = currentObject[textblockstring][1];
                header = header.replace(/_~_~_~/g, "\"");
                header = header.replace(/@@@@@@/g, "\'");  
                header = header.replace(/_!!_!/g, "/");
                header = header.replace(/-%-%-%-%-/g, "?");

                var content = currentObject[textblockstring][0];
                content = content.replace(/_~_~_~/g, "\"");
                content = content.replace(/@@@@@@/g, "\'");
                content = content.replace(/_!!_!/g, "/");
                content = content.replace(/-%-%-%-%-/g, "?");
                content = content.replace(/%%%%%%/g, "<br>");

                $("#storyWrap").append('<div class="textBlock" id="textblock'+id+'"><div id="textblockheader'+id+'" class="textBoxHeaderDiv" ><div class="delete_icon" style="left:90%;right:5%;top:5px;font-size: 16px;" id="textblockdelete'+id+'"  >DELETE</div>'+ header + '</div><div id="textblockbody'+id+'" class="textBoxBody"> ' + content + '</div></div>');
                 var localid = id;
                $("#storyWrap").append('<div class="storyLine" id="storyLine'+id+'"><hr noshade size=1 width=300></div>');

                $("#textblockdelete"+id).click(function(){
                    var toberemoved = $(this).attr('id');
                    toberemoved = toberemoved.replace("textblockdelete","");
                    $("#storyLine"+toberemoved).fadeOut(300, function(){ $(this).remove();});

                    console.log(toberemoved);
                    $("#textblock"+toberemoved).fadeOut(300, function(){ $(this).remove();});
                });

                id++;
                global_id=id;
            }
            else if(currentObject[groupphotostring]){
                var picCount = 0;
                var caption = currentObject[groupphotostring][0];
                caption = caption.replace(/_~_~_~/g, "\"");
                caption = caption.replace(/@@@@@@/g, "\'");
                caption = caption.replace(/-%-%-%-%-/g, "?");

                var pic1 = currentObject[groupphotostring][1];
                if(pic1){picCount++};
                pic1 = "https://photorankr.com/userphotos/" + pic1;
                
                var pic2 =currentObject[groupphotostring][2];
                if(pic2){picCount++};
                pic2 =  "https://photorankr.com/userphotos/" + pic2;
                
                var pic3 = currentObject[groupphotostring][3];
                if(pic3){picCount++};
                pic3 =  "https://photorankr.com/userphotos/" + pic3;
                
                var pic4 = currentObject[groupphotostring][4];
                if(pic4){picCount++};
                pic4 =  "https://photorankr.com/userphotos/" + pic4;

                if(picCount == 4){
                    $("#storyWrap").append('<div class="groupPhotoBlock" id="groupPhotoBlock'+id+'" ><div id="photoElement_'+id+'_1" class="photoElement_4" style="background-image:url('+pic1+')"><div class=\"group_edit\" id="photoElement_'+id+'_1_edit" style="bottom:5px;" >EDIT</div></div><div id="photoElement_'+id+'_2" class="photoElement_4" style="background-image:url('+pic2+')"><div class=\"group_edit\" id="photoElement_'+id+'_2_edit" style="bottom:5px;">EDIT</div></div><div id="photoElement_'+id+'_3" class="photoElement_4" style="background-image:url('+pic3+')"><div class=\"group_edit\" id="photoElement_'+id+'_3_edit"  style="bottom:5px;">EDIT</div></div><div id="photoElement_'+id+'_4" class="photoElement_4" style="background-image:url('+pic4+')"><div class=\"group_edit\" id="photoElement_'+id+'_4_edit" style="bottom:5px;">EDIT</div></div></div>');
                    
                    if(ownerView == 1){
                        $("#storyWrap").append('<div class="groupPhotoCaption" ><div id="groupPhotoCaption'+id+'" class="groupPhotoCaptionInner">'+caption+'</div></div>');
                    }
                    else{
                        if(caption != "Add a caption to this group of photos"){
                        $("#storyWrap").append('<div class="groupPhotoCaption" ><div id="groupPhotoCaption'+id+'" class="groupPhotoCaptionInner">'+caption+'</div></div>');
                        }
                    }
                    
                    
                    $("#photoElement_"+id+"_1_edit").click(function(){
                        $("#modalbg").show();
                        $("#groupPictureModal").show();
                        $('body').css('overflow', 'hidden');
                        groupphoto_id_global = $(this).attr('id');
                    });
                    $("#photoElement_"+id+"_2_edit").click(function(){
                        $("#modalbg").show();
                        $("#groupPictureModal").show();
                        $('body').css('overflow', 'hidden');
                        groupphoto_id_global = $(this).attr('id');

                    });
                    $("#photoElement_"+id+"_3_edit").click(function(){
                        $("#modalbg").show();
                        $("#groupPictureModal").show();
                        $('body').css('overflow', 'hidden');
                        groupphoto_id_global = $(this).attr('id');

                    });
                    $("#photoElement_"+id+"_4_edit").click(function(){
                        $("#modalbg").show();
                        $("#groupPictureModal").show();
                        $('body').css('overflow', 'hidden');
                        groupphoto_id_global = $(this).attr('id');

                    });
                    
                }
                else if (picCount == 2){
                    $("#storyWrap").append('<div class="groupPhotoBlock" id="groupPhotoBlock'+id+'"><div id="photoElement_'+id+'_1"  class="photoElement_4" style="background-image:url('+pic1+')"><div class=\"group_edit\" id="photoElement_'+id+'_1_edit" style="bottom:5px;">EDIT</div></div><div id="photoElement_'+id+'_2"  class="photoElement_4" style="background-image:url('+pic2+')"><div class=\"group_edit\" id="photoElement_'+id+'_2_edit" style="bottom:5px;">EDIT</div></div></div>');
                                    
                    if(ownerView == 1){
                        $("#storyWrap").append('<div class="groupPhotoCaption" ><div id="groupPhotoCaption'+id+'" class="groupPhotoCaptionInner">'+caption+'</div></div>');
                    }
                    else{
                        if(caption != "Add a caption to this group of photos"){
                        $("#storyWrap").append('<div class="groupPhotoCaption" ><div id="groupPhotoCaption'+id+'" class="groupPhotoCaptionInner">'+caption+'</div></div>');
                        }
                    }
                    $("#photoElement_"+id+"_1_edit").click(function(){
                        $("#modalbg").show();
                        $("#groupPictureModal").show();
                        $('body').css('overflow', 'hidden');
                        groupphoto_id_global = $(this).attr('id');
                    });
                    $("#photoElement_"+id+"_2_edit").click(function(){
                        $("#modalbg").show();
                        $("#groupPictureModal").show();
                        $('body').css('overflow', 'hidden');
                        groupphoto_id_global = $(this).attr('id');

                    });
                }
                else if(picCount == 3){
     
                        $("#storyWrap").append('<div class="groupPhotoBlock" id="groupPhotoBlock'+id+'"><div id="photoElement_'+id+'_1" class="photoElement_wide" style="background-image:url(' + pic1 + ')"><div class=\"group_edit\" style="bottom:5px;" id="photoElement_'+id+'_1_edit" >EDIT</div></div><div id="photoElement_'+id+'_2" class="photoElement_4" style="background-image:url('+pic2+')"><div class=\"group_edit\" id="photoElement_'+id+'_2_edit" style="bottom:5px;"  >EDIT</div></div><div id="photoElement_'+id+'_3" class="photoElement_4" style="background-image:url('+pic3+')"><div class=\"group_edit\" id="photoElement_'+id+'_3_edit" style="bottom:5px;" >EDIT</div></div></div>');
                        
                        if(ownerView == 1){
                        $("#storyWrap").append('<div class="groupPhotoCaption" ><div id="groupPhotoCaption'+id+'" class="groupPhotoCaptionInner">'+caption+'</div></div>');
                        }
                        else{
                            if(caption != "Add a caption to this group of photos"){
                            $("#storyWrap").append('<div class="groupPhotoCaption" ><div id="groupPhotoCaption'+id+'" class="groupPhotoCaptionInner">'+caption+'</div></div>');
                            }
                        }
                        
                        $("#photoElement_"+id+"_1_edit").click(function(){
                            $("#modalbg").show();
                            $("#groupPictureModal").show();
                            $('body').css('overflow', 'hidden');
                            groupphoto_id_global = $(this).attr('id');
                        });
                        $("#photoElement_"+id+"_2_edit").click(function(){
                            $("#modalbg").show();
                            $("#groupPictureModal").show();
                            $('body').css('overflow', 'hidden');
                            groupphoto_id_global = $(this).attr('id');

                        });
                        $("#photoElement_"+id+"_3_edit").click(function(){
                            $("#modalbg").show();
                            $("#groupPictureModal").show();
                            $('body').css('overflow', 'hidden');
                            groupphoto_id_global = $(this).attr('id');

                        });                    
                }
                $("#groupPhotoBlock"+id).append('<div class="delete_icon" style="bottom:25px;" id="groupPhotoDelete'+id+'">DELETE</div>');
                $("#storyWrap").append('<div class="storyLine" id="storyLine'+id+'"><hr noshade size=1 width=300></div>');
                
                $("#groupPhotoDelete"+id).click(function(){
                    var a = $(this).attr('id');
                    a = a.replace("groupPhotoDelete","");
                    // $("#groupPhotoBlock"+a).remove();
                    // $("#groupPhotoCaption"+a).parent().remove(); 
                    $("#storyLine"+a).fadeOut(300, function(){ $(this).remove();});
                    $("#groupPhotoBlock"+a).fadeOut(300, function(){ $(this).remove();});
                    $("#groupPhotoCaption"+a).fadeOut(300, function(){ $(this).remove();});

                });
                

                id++;
                global_id=id;
            }
            else if(currentObject[blockquotestring]){
                var quote = currentObject[blockquotestring][0];
                quote = quote.replace(/_~_~_~/g, "\"");
                quote = quote.replace(/@@@@@@/g, "\'");
                quote = quote.replace(/_!!_!/g, "/");
                quote = quote.replace(/-%-%-%-%-/g, "?");
                var author = currentObject[blockquotestring][1];
                author = author.replace(/_~_~_~/g, "\"");
                author = author.replace(/@@@@@@/g, "\'");
                author = author.replace(/_!!_!/g, "/");
                author = author.replace(/-%-%-%-%-/g, "?");

                $("#storyWrap").append('<div id="blockQuote'+id+'" class="blockQuote"><div id="blockQuoteBody'+id+'" class="blockQuoteHeader" > ' + quote + ' </div><div id="blockQuoteAuthor'+id+'" class="blockQuoteAuthor">' + author + ' <div style="left:90%;bottom:0px;font-size:16px;" class=\"delete_icon\" id="blockQuoteDelete' + id + '">DELETE</div></div></div>');
                
                var localid = id;
                $("#storyWrap").append('<div class="storyLine" id="storyLine'+id+'"><hr noshade size=1 width=300></div>');

                $("#blockQuoteDelete"+localid).click(function(){
                    var deleteid = $(this).attr('id');
                    deleteid = deleteid.replace("blockQuoteDelete","");
                    // window.alert(deleteid);
                    $("#storyLine"+deleteid).fadeOut(300, function(){ $(this).remove();});
                    $("#blockQuote"+deleteid).fadeOut(300, function(){ $(this).remove();});
                });

                id++;
                global_id=id;
            }
            else if(currentObject[videoblockstring]){
                var vidtype = currentObject[videoblockstring]["type"];
                var vidsrc = currentObject[videoblockstring]["source"];
                var vidcaption = currentObject[videoblockstring]["caption"];
                vidcaption = vidcaption.replace(/_~_~_~/g, "\"");
                vidcaption = vidcaption.replace(/@@@@@@/g, "\'");
                vidcaption = vidcaption.replace(/_!!_!/g, "/");
                vidcaption = vidcaption.replace(/-%-%-%-%-/g, "?");
         
                if(vidtype == "youtube"){

                    var displayLink = "//www.youtube.com/embed/"+vidsrc;
                    //id='videoEdit" + id + 
                    $("#storyWrap").append("<div class='videoBlock' id='videoBlock"+id+"' ><div class=\"group_edit\" id='videoEdit"+id+"' style='left:90%;bottom:250px;'>EDIT</div><div style='left:90%;bottom:200px;' class=\"delete_icon\" id='delete" + id + "'>DELETE</div><iframe id='videoLink"+id+"'  src='"+displayLink+"' style='margin:auto;'></iframe><div class='videoBlockCaption'><div id='videoBlockCaption"+id+"'  class='videoBlockCaptionInner' >"+vidcaption+"</div></div></div>");
                }
                else if(vidtype == "vimeo"){

                    var displayLink = "//player.vimeo.com/video/"+vidsrc;
                    $("#storyWrap").append("<div class='videoBlock' id='videoBlock"+id+"' ><div class=\"group_edit\" id='videoEdit"+id+"' style='left:90%;bottom:250px;'>EDIT</div><div style='left:90%;bottom:200px;' class=\"delete_icon\" id='delete" + id + "'>DELETE</div><iframe id='videoLink"+id+"'  src='"+displayLink+"' style='margin:auto;'></iframe><div class='videoBlockCaption'><div id='videoBlockCaption"+id+"' class='videoBlockCaptionInner' >"+vidcaption+"</div></div></div>");
                }
                var localid = id;
                $("#storyWrap").append('<div class="storyLine" id="storyLine'+localid+'"><hr noshade size=1 width=300></div>');
                $("#delete"+localid).click(function(){
                    var deleteid = $(this).attr('id');
                    deleteid = deleteid.replace("delete","");
                    // window.alert(deleteid);
                    $("#storyLine"+deleteid).fadeOut(300, function(){ $(this).remove();});
                    $("#videoBlock"+deleteid).fadeOut(300, function(){ $(this).remove();});
                });
                $("#videoEdit"+id).click(function(){
                    $("#modalbg").show();
                    $("#videoModal").show();
                    var vidlocalid = $(this).attr('id');
                    vidlocalid = vidlocalid.slice(9,vidlocalid.length);
                    video_id_global = vidlocalid;
                });

               id++;
               global_id=id;
            }
            else if(currentObject[mapembedstring]){
                var mapcaption = currentObject[mapembedstring]["mapcaption"];
                mapcaption = mapcaption.replace(/_~_~_~/g, "\"");
                mapcaption = mapcaption.replace(/@@@@@@/g, "\'");
                mapcaption = mapcaption.replace(/_!!_!/g, "/");
                mapcaption = mapcaption.replace(/-%-%-%-%-/g, "?");

                var mapinfo_obj = currentObject[mapembedstring]["mapinfo"]; 
                var lat = mapinfo_obj["lat"];
                var lng = mapinfo_obj["lng"];
                var street = mapinfo_obj["street"];

                var mapInfoArray = {};
                mapInfoArray["lat"] = lat;
                mapInfoArray["lng"] = lng;

                var newmapid = "mapEmbed"+id;
                
                if(ownerView == 0){
                    if(mapcaption != " Add a caption to this map "){
                    $("#storyWrap").append('<div id="'+newmapid+'" class="gmaps-canvas"></div><div class=\'videoBlockCaption\'><div id="mapEmbedCaption'+id+'" class=\'videoBlockCaptionInner\'>' + mapcaption + '</div></div>');
                    }
                    else{
                        $("#storyWrap").append('<div id="'+newmapid+'" class="gmaps-canvas"></div>');
                    }

                }
                else{
                    $("#storyWrap").append('<div id="'+newmapid+'" class="gmaps-canvas"></div><div class=\'videoBlockCaption\'><div id="mapEmbedCaption'+id+'" class=\'videoBlockCaptionInner\' contenteditable="true">' + mapcaption + '<div class=\"delete_icon\" style="bottom: 10px;left: 90%;font-size:16px;" id="delete'+id+'" >DELETE</div></div>');

                    $("#storyWrap").append('<div class="storyLine" id="storyLine'+id+'"><hr noshade size=1 width=300></div>');

                    $("#delete" + id).on( "click", function() {
                        // window.alert(id);
                        var localid = $(this).attr('id');
                        localid = localid.replace("delete","");
                        $("#storyLine"+localid).fadeOut(300, function(){ $(this).remove();});
                        $("#mapEmbed"+localid).fadeOut(300, function(){ $(this).remove();});
                        $("#mapEmbedCaption"+localid).fadeOut(300, function(){ $(this).remove();});
                    });

                }
                locationDict[newmapid] = mapInfoArray;

                var newmap;
                var pos = new google.maps.LatLng(lat, lng);
                function initialize(){
                  var mapOptions = {
                    zoom: 8,
                    disableDefaultUI:true,
                    scrollwheel: false,
                    center: pos
                  };
                  
                  newmap = new google.maps.Map(document.getElementById("mapEmbed"+id),
                        mapOptions);
                  
                  if(ownerView==0){
                    newmarker = new google.maps.Marker({
                      map: newmap,
                      position: pos,
                      animation: google.maps.Animation.DROP
                    });
                  }
                  else{
                    newmarker = new google.maps.Marker({
                      map: newmap,
                      position: pos,
                      draggable:true,
                      animation: google.maps.Animation.DROP
                    });

                 google.maps.event.addListener(newmarker, "dragend", function(event) { 
                      
                      var lat = event.latLng.lat(); 
                      var lng = event.latLng.lng(); 
                      mapInfoArray["lat"] = lat;
                      mapInfoArray["lng"] = lng;

                      locationDict[newmapid] = mapInfoArray;

                    }); 

                  }
                }
            initialize();
            mapid++;
            id++;
            global_id=id;
            }

        }

        var owneremail = "<?php echo $storyOwner; ?>";
        var email = "<?php echo $email; ?>";
        
        if(email){
            if(owneremail == "henagaraj@email.wm.edu"){
                   
            }
            if(email == owneremail){
                
                    $("#storyWrap").append("<div  class='testBlock'><img src='http://coversplash.com/graphics/logowhite.png' style='width:150px;float:left;margin:15px;margin-left:45px;' /><ul><li id='deleteStory' style='background-color:red;border-radius:5px;padding:5px;'>DELETE</li><li id='edit' style='background-color:rgb(99,164,255);border-radius:5px;padding:5px;'>EDIT</li><li>PUBLISH</li></ul></div>");
                    $("#storyWrap").append('<div id="toolbox1" class="storyToolWrap" style="margin-top:100px;"><div class="storyHead">CREATE</div><div class="toolBox"><div id="addPhotoGroup" class="toolElement"><div  class="toolHead">PHOTO GROUP</div><div class="toolPic" ><img src="http://coversplash.com/img/stories_multiplepictures.png" /></div><div class="toolDescription" style="margin-top:15px;">Add a group of 2 to 4 photos</div><div class="toolDescription" style="margin-top:-15px;">Click to select</div></div><div id="addFullsizePhoto" class="toolElement"><div class="toolHead">FULLSIZE PHOTO</div><div class="toolPic"><img src="http://coversplash.com/img/stories_singlepicture.png" /></div><div class="toolDescription" style="margin-top: 15px;">Add a full size photo</div><div class="toolDescription" style="margin-top:-15px;">Click to select from your portfolio</div></div><div id="addTextBlock" class="toolElement" ><div class="toolHead">TEXT BLOCK</div><div class="toolPic" style="margin-top: 15px;"><img src="http://coversplash.com/img/stories_textblock.png" /></div><div class="toolDescription" style="margin-top: 25px;">Add a block of text</div></div></div></div>');
                    $("#storyWrap").append('<div id="toolbox" class="storyToolWrap" style="border-top:1px solid rgba(0,0,0,0);margin-top:-40px;padding-bottom:20px;"><div class="toolBox"><div id="addBlockQuote" class="toolElement"><div class="toolHead">BLOCK QUOTE</div><div class="toolPic"><img src="http://coversplash.com/img/stories_blockquote.png" style="margin-top:15px;"/></div> <div class="toolDescription" style="margin-top: 25px;">Add a quote</div></div><div id="addVideoLink" class="toolElement"><div class="toolHead">VIDEO LINK</div><div class="toolPic"><img src="http://coversplash.com/img/stories_videolink.png" style=""/></div><div class="toolDescription" style="margin-top: 15px;">Add an embedded video</div></div><div id="addLocationTag" class="toolElement" > <div class="toolHead">LOCATION</div><div class="toolPic"><img src="http://coversplash.com/img/stories_locationtag.png" style="" /></div><div class="toolDescription" style="margin-top: 15px;">Add a Location</div></div></div></div>');

                    var footerOwner = "<?php echo $ownerName; ?>";
                    var footerStoryCover = $("#storyCover").css('background-image');
                    footerStoryCover = footerStoryCover.split("userphotos/");
                    footerStoryCover = footerStoryCover[1];
                    footerStoryCover = footerStoryCover.replace("&w=800&q=100","");
                    footerStoryCover = footerStoryCover.replace(")","");
                    footerStoryCover = "http://coversplash.com/userphotos/"+footerStoryCover;
                    console.log(footerStoryCover);
                    var footerTitle = document.getElementById("story_title").innerHTML;

                    var ownerSite = "http://<?php echo $ownerDomain; ?>.coversplash.com";
                    $("#storyWrap").append('<div class="storyFooter" style="margin-top:50px;"><div class="storyFooterInner"><div style="color:rgba(0,0,0,0.5);">Story by</div> <div><a href="'+ownerSite+'">'+footerOwner+'</a></div><br><br><div style="font-size:15px;"><a href="'+ownerSite+'"> &copy 2014 '+footerOwner+' </a></div><br><br><br><div style="width:100%;"><ul><li><a href="https://www.facebook.com/sharer.php?u=<?php echo $storyURL; ?>"><img src="http://coversplash.com/graphics/fbshare.png" /></a></li><li><a href="https://twitter.com/share?url=<?php echo $storyURL; ?>"><img src="http://coversplash.com/graphics/twittershare.png" /></a></li><li><a href="http://www.tumblr.com/share/photo?source=<?php echo $storyURL; ?>&caption='+footerTitle+'"><img src="http://coversplash.com/graphics/tumblrshare.png" /></a></li><li><a href="https://pinterest.com/pin/create/button/?url=<?php echo $storyURL; ?>&media='+footerStoryCover+'" class="pin-it-button"><img src="http://coversplash.com/graphics/pinterestshare.png" /></a></li></ul></div></div></div>');
                        
                        var initialAccess = "<?php echo $initialAccess; ?>";
                        // window.alert(global_id);
                        if(initialAccess == 1){
                            editToggle();
                            if(id){
                                global_id = id++;
                            }
                            
                            // window.alert(global_id);
                        }

                        $("#addTextBlock").click(function(){
                            $("#toolbox1").before('<div class="textBlock" id="textblock'+id+'"><div id="textblockheader'+id+'" class="textBoxHeaderDiv">Header<div class="delete_icon" style="bottom:5px"><img id="textblockdelete'+id+'"  src="http://coversplash.com/img/deletewhite.png"></div></div><div id="textblockbody'+id+'" class="textBoxBody">Tell your story</div></div>');
                            var localid = id;
                            $("#textblockdelete"+id).click(function(){
                                var toberemoved = $(this).attr('id');
                                console.log(toberemoved);
                                toberemoved = toberemoved.replace("textblockdelete","");
                                $("#textblock"+toberemoved).remove();
                            });
                            id++;
                        });
                        $("#addFullsizePhoto").click(function(){
                            fullsizepicid = -1;
                            $("#modalbg").show();
                            $("#fullsizePictureModal").show();
                            $('body').css('overflow', 'hidden');
                        });
                        $("#addBlockQuote").click(function(){
                            $("#toolbox1").before('<div id="blockQuote'+id+'" class="blockQuote"><div id="blockQuoteBody'+id+'" class="blockQuoteHeader" > "The memory of how we work will endure beyond the products of our work"</div><div id="blockQuoteAuthor'+id+'" class="blockQuoteAuthor"> Jony Ive <div style="left:90%;bottom:0px;font-size:16px;" class=\"delete_icon\" id="blockQuoteDelete' + id + '">DELETE</div></div></div>');
                            var localid = id;
                            $("#blockQuoteDelete"+localid).click(function(){
                                var deleteid = $(this).attr('id');
                                deleteid = deleteid.replace("blockQuoteDelete","");
                                // window.alert(deleteid);
                                $("#blockQuote"+deleteid).fadeOut(300, function(){ $(this).remove();});
                            });
                            id++;
                        });
                        $("#addVideoLink").click(function(){
                            $("#modalbg").show();
                            $("#videoModal").show();
                            var vidlocalid = $(this).attr('id');
                            vidlocalid = vidlocalid.slice(9,vidlocalid.length);
                            video_id_global = vidlocalid;

                        });
                        $("#addLocationTag").click(function(){

                            $("#modalbg").show();
                            $( "#mapsModal" ).animate({ "margin-left": "-400px" }, 300 );
                            setTimeout(function() {
                            $(".saveMapModal").show();
                            $("#saveMap").show();
                            }, 300);

                        });
                        $("#addPhotoGroup").click(function(){
                            $("#modalbg").show();
                            $("#addGroupPictureModal").show();
                            $('body').css('overflow', 'hidden');
                        });

                        $("#deleteStory").click(function(){
                            $("#modalbg").show();
                            $("#deleteBox").show();
                            $('body').css('overflow', 'hidden');
                        });
                        $("#noDeleteStory").click(function(){
                            $("#modalbg").hide();
                            $("#deleteBox").hide();
                            $('body').css('overflow', 'auto');
                        })
                        $("#yesDeleteStory").click(function(){
                            var ajaxRequest = createRequestObject();
                            ajaxRequest.onreadystatechange = function(){
                                if(ajaxRequest.readyState == 4  && ajaxRequest.status == 200){
                                    window.location = "http://<?php echo $ownerDomain; ?>.coversplash.com/?view=stories";
                                }
                            }
                            var w = "/storyDelete/<?php echo $storyId; ?>";
                            console.log(w);
                            ajaxRequest.open("POST", w, true);
                            ajaxRequest.send(null); 

                        });
                
                        
            }
            else{

            }
        }
        $("#update").click(function(){
            pushStory();
        });
         $("#edit").click(function(){
            var children = $("#storyWrap > div").length;
            if(children <= 5){
                $('#addTitleInfo > div').html("Please add elements to your story");
                $('#addTitleInfo').fadeIn(500); 
                $('#addTitleInfo').delay(3000).fadeOut(1000);
                scroll("#toolbox1",500);
                return;
            }
            else{
            editToggle();                
            }
        });
        $("#delete").click(function(){
            $('body').css('overflow', 'hidden');
            $("#modalbg").show();
            $('#deleteBox').show();
        });

        $(".storyLine").last().remove();

    });

    function getLastElementid(){
        var c = $("#toolbox1").prev();
        return c;
    }

    function setCoverPic(picture){
      $("#storyCover").css("background-color",'rgb(21,22,23)');
      $("#storyCover").css("background-image",'url('+picture+')');
      $("#modalbg").hide();
      $("#coverPictureModal").hide();
      $('body').css('overflow', 'auto');
    }

    function addFullPic(picture){
      if(fullsizepicid > 0){
        $("#fullsizePic"+fullsizepicid).attr("src",picture);
        $("#modalbg").hide();
        $("#fullsizePictureModal").hide();
        $('body').css('overflow', 'auto');
      }
      else{
        var lastid = global_id;
         $("#toolbox1").before('<div class="fullSizePic" id="fullPicture'+lastid+'" style=""><div class="cover_edit" style="width:70px;margin-left:-75px;display:block;" id="fullsizeEdit'+lastid+'">EDIT</div><div style="width:70px;margin-left:5px;display:block;" id="delete'+lastid+'" class="delete_icon">DELETE</div><img id="fullsizePic'+lastid+'"  style="width:100%" src = "'+picture+'" /></div>');
         $("#toolbox1").before('<div class="fullSizeStoryCaption" id="fullsizeStoryCaption'+lastid+'" ><div id="fullsizeStoryCaptionValue'+lastid+'" class="fullsizeStoryCaptionInner" contenteditable="true">Add a caption to this picture</div></div>');
         var b = $("#fullsizeStoryCaptionValue"+lastid).parent().prev().prev();
         console.log(b[0]);
         // console.log(b);
         if(b[0].className != "storyLine"){
            var newid = b[0].id;
            newid = newid.replace("fullPicture","");
            window.alert(newid);

         }
        $("#storyWrap").append('<div class="storyLine" id="storyLine'+lastid+'"><hr noshade size=1 width=300></div>');

         $("#fullsizeEdit"+lastid).click(function(){
            var localnew = $(this).attr('id');
            localnew = localnew.slice(12,localnew.length);
            // window.alert(localnew);
            fullsizepicid = localnew;
            $('body').css('overflow', 'hidden');
            $("#modalbg").show();
            $("#fullsizePictureModal").show();
        });
         $("#delete"+lastid).click(function(){
            var deleteid = $(this).attr('id');
            deleteid = deleteid.replace("delete","");
            // window.alert(deleteid);
            $("#storyLine"+deleteid).fadeOut(300, function(){ $(this).remove();});
            $('#fullPicture'+deleteid).fadeOut(300, function(){ $(this).remove();});
            $("#fullsizeStoryCaption"+deleteid).fadeOut(300, function(){ $(this).remove();});
            // $("#fullPicture"+deleteid).remove();
            // $("#fullsizeStoryCaption"+deleteid).remove();
        });
         global_id++;
        $("#modalbg").hide();
        $("#fullsizePictureModal").hide();
        $('body').css('overflow', 'auto');

      }

    }

    function addFullPic_Group(picture){
      var newid = groupphoto_id_global;
      newid = newid.replace("_edit","");
      $("#"+newid).css('background-image','url('+picture+')');
      $("#modalbg").hide();
      $("#groupPictureModal").hide();
      $('body').css('overflow', 'auto');

    }

    function addPictureGroup(pictureId, localPicId){
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
     function scroll(inputstring,delay){
        var offset = $(this).offset();
        $('html, body').animate({scrollTop: $(inputstring).offset().top}, delay);
      }

    function storySave(story, jsonString){
        ajaxRequest = createRequestObject();
        ajaxRequest.onreadystatechange = function(){
        if(ajaxRequest.readyState == 4  && ajaxRequest.status == 200){
            console.log(ajaxRequest.responseText);
            var newurl = "http://coversplash.com/story/"+ajaxRequest.responseText;
            $('#addTitleInfo > div').html("Your story has been saved!");
            
            if($("#edit").html() == "EDIT"){
                $('#addTitleInfo').fadeIn(500); 
                $('#addTitleInfo').delay(3000).fadeOut(1000);
            }

            scroll("body",0);

            current_story_id = ajaxRequest.responseText;
            console.log("successful save");
        }
        }
        var c = JSON.stringify(jsonString);
        // console.log("in story save");
        // console.log(c);
        var saveLocation = "/storyUpdate/ " + story + "/" + c + "/" + "<?php echo $email; ?>/"+current_story_id;
        // console.log(saveLocation);
        ajaxRequest.open("POST", saveLocation, true);
        ajaxRequest.send(null); 
    }

    function pushStory(){

        var children = $("#storyWrap > div").length;
        if(children <= 4){
            $('#addTitleInfo > div').html("Please add elements to your story");
            $('#addTitleInfo').fadeIn(500); 
            $('#addTitleInfo').delay(3000).fadeOut(1000);
            return;
        }
        console.log("saving story");
        var elementArray = new Array();
        var b = {};
        
        var title = document.getElementById("story_title").innerHTML;
        var subtitle = document.getElementById("story_subtitle").innerHTML;
        var story_cover = $("#storyCover").css('background-image');

        if(title == "Untitled Story" || subtitle == "Add a subtitle"){
            $('#addTitleInfo > div').html("Please add a title and subtitle");
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
        console.log(subtitle);
        console.log(story_cover);
        b["storyTitle"] = title;
        b["storySubTitle"] = subtitle;
        b["storyCoverPhoto"] = story_cover;

        $('.fullSizePic').each( function(i,e) {
            var id = $(e).attr('id');
            elementArray.push(id);
            var idSplice = id.slice(11,id.length);

            var fullSizeCaption = document.getElementById("fullsizeStoryCaptionValue" + idSplice).innerHTML;
            fullSizeCaption = fullSizeCaption.replace(/"/g, "_~_~_~");  
            fullSizeCaption = fullSizeCaption.replace(/'/g, "@@@@@@");
            fullSizeCaption = fullSizeCaption.replace(/\//g,"_!!_!");
            fullSizeCaption = fullSizeCaption.replace(/[?]/g, "-%-%-%-%-");

            var childImage = $("#"+id + " > img").attr('src');
            var passImg = childImage.split('userphotos/');
            var photoBlockItems = new Array(); 

            var d = passImg[1];
            d.slice(0,-1);                
            passImg = d.split("&w");
            photoBlockItems.push(passImg[0]);
            photoBlockItems.push(fullSizeCaption);
            b[id] = (photoBlockItems);
            elementArray.push(photoBlockItems);

        });
        $('.textBlock').each( function(i,e) {
            var id = $(e).attr('id');
            elementArray.push(id);
            var idSplice = id.slice(9,id.length);
            console.log(idSplice);
            var inputId = 'textblockbody'+idSplice;
            var hVal = 'textblockheader'+idSplice;

            var textBoxItems = new Array();

            var value = document.getElementById(inputId).innerHTML;
            value = value.trim();
            value = value.replace(/"/g, "_~_~_~");  
            value = value.replace(/'/g, "@@@@@@"); 
            value = value.replace(/\n/g, "%%%%%%"); 
            value = value.replace(/\//g,"_!!_!");
            value = value.replace(/[?]/g, "-%-%-%-%-");
            // value = value.replace(deleteid, "");

            var rawheader = document.getElementById(hVal).innerHTML;
            rawheader = rawheader.split('<div');
            var headerVal = rawheader[0];
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
            // $(deleteid).show();

        });
        $('.groupPhotoBlock').each( function(i,e) {
            var id = $(e).attr('id');
            elementArray.push(id);
            var idSplice = id.slice(15,id.length);
            var photoBlockItems = new Array(); 
            var caption_id = "groupPhotoCaption" + idSplice;
            var caption = document.getElementById(caption_id).innerHTML;
            caption = caption.replace(/"/g, "_~_~_~");  
            caption = caption.replace(/'/g, "@@@@@@");
            caption = caption.replace(/[?]/g, "-%-%-%-%-");
            console.log(caption);
            photoBlockItems.push((caption));
            $('#'+id).children('div').each(function (i,e) {
              var childImage = $(e).css('background-image');
              console.log("NEWTEST"+childImage + " ID" + "#"+id);
              if(childImage.indexOf("userphotos/") != -1){
                var passImg = childImage.split('userphotos/');

                var b = passImg[1];
                b.slice(0,-1);                
                passImg = b.replace("&w=800&q=100","");
                var c = passImg;
                c = c.slice(0,-1);
                console.log(c);
                photoBlockItems.push(c);
              }
               
            });

         b[id] = photoBlockItems;
         elementArray.push(photoBlockItems);

        });
        $('.videoBlock').each( function(i,e) {
          var id = $(e).attr('id');
          var idSplice = id.slice(10,id.length);

          var videoId = "videoLink" +  idSplice;
          var videoCaption = "videoBlockCaption" + idSplice;
          console.log(videoId);
          var videoItems = { };
          var vidSrc = document.getElementById(videoId).src;
          var vidCaption = document.getElementById(videoCaption).innerHTML;

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

          var a = vidSrc.split("http://player.vimeo.com/video/");
          vidSrc = a[1];
          videoItems["type"] = "vimeo";
          videoItems["source"] = vidSrc;
          videoItems["caption"] = vidCaption;
          }
          
          b[id] = videoItems;
        });
        
        $('.gmaps-canvas').each( function(i,e) {
          var id = $(e).attr('id');

          var idSplice = id.slice(8,id.length);
          var mapID = "mapEmbed"+idSplice;
          var mapcaption = "mapEmbedCaption" + idSplice;
          var mapinfo = "mapInfoInput"+idSplice;
          var mapInfo = locationDict[mapID];

          var uploadArray = {};
          uploadArray["mapinfo"] = mapInfo;

          var rawcaption = document.getElementById(mapcaption).innerHTML;
          rawcaption = rawcaption.split('<div');
          var mapCaptionVal = rawcaption[0];
          mapCaptionVal = mapCaptionVal.replace(/"/g, "_~_~_~");  
          mapCaptionVal = mapCaptionVal.replace(/'/g, "@@@@@@");
          mapCaptionVal = mapCaptionVal.replace(/\//g,"_!!_!");
          mapCaptionVal = mapCaptionVal.replace(/[?]/g, "-%-%-%-%-");

          uploadArray["mapcaption"] = mapCaptionVal;
          b[mapID] = uploadArray;

        });
        $('.blockQuote').each( function(i, e) {
          var id = $(e).attr('id');
          var idSplice = id.slice(10,id.length);
          var body = "blockQuoteBody" + idSplice;
          var author = "blockQuoteAuthor" + idSplice;
          var blockQuoteItems = new Array();

          var b_body = document.getElementById(body).innerHTML;
          b_body = b_body.replace(/"/g, "_~_~_~");  
          b_body = b_body.replace(/'/g, "@@@@@@");
          b_body = b_body.replace(/\//g,"_!!_!");
          b_body = b_body.replace(/[?]/g, "-%-%-%-%-");
          console.log(b_body);
          console.log("lol");
          blockQuoteItems.push(encodeURIComponent(b_body));

          var raw_b_author = document.getElementById(author).innerHTML;
          raw_b_author = raw_b_author.split("<div");
          // window.alert(raw_b_author);
          var b_author = raw_b_author[0];
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
    

        console.log(b);
        storySave(title,b);

    }	
    </script>

    <!--Google Analytics-->
    <?php include_once("analyticstracking.php") ?>
   
</head>
<body>
<div id="addTitleInfo" class="fillDomainDiv" >
    <div>Please add a title and subtitle</div>
</div>

<div id="deleteBox" class="dialogBox" >
    <div>Are you sure you would like to delete this story?</div>
    <ul><li id="yesDeleteStory" style="background-color: rgb(99,164,255);"><div >YES</div></li><li id="noDeleteStory" style="background-color: red;"><div >NO</div></li></ul>
</div>

<div id="modalbg" style="position:fixed;top:0!important;"></div>

<div id="coverPictureModal" class="pictureModal">
<div class="modalHead" style="position:fixed;width:760px;z-index: 2147483647;overflow:hidden;">Select a photo</div>
<div id="coverPictureModal_scroll" class="pictureSelect" style="margin-top:80px;">

  <?
    if($ownerView == 1){
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
    }
    ?>
    <script type="text/javascript">
    var last = 0;
    $("#coverPictureModal").scroll(function(){
        if($(this)[0].scrollHeight - $(this).scrollTop() <= $(this).outerHeight()) {
            if(last != $(".to_be_Selected_cover:last").attr("id")) {
                var ajaxlocation = "loadStoryPics/" + $(".to_be_Selected_cover:last").attr("id") + "/<?php echo $email; ?>" + "/cover";
                // console.log("cover_scroll");
                // console.log(ajaxlocation);
                $.ajax({
                    url: "/loadStoryPics/" + $(".to_be_Selected_cover:last").attr("id") + "/<?php echo $email; ?>" + "/cover",
                    success: function(html) {
                        // console.log(html);
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

<div id="fullsizePictureModal" class="pictureModal">
<div class="modalHead" style="position:fixed;width:760px;z-index: 2147483647;overflow:hidden;">Select a photo</div>
<div id="fullsizePictureModal_scroll" class="pictureSelect" style="margin-top:80px;">

  <?
    if($ownerView == 1){
      $photosquery = mysql_query("SELECT * FROM photos WHERE emailaddress = '$email' ORDER BY id DESC LIMIT 20");
      $numphotos = mysql_num_rows($photosquery);
      for($i = 0; $i < $numphotos; $i++){

        $picture = mysql_result($photosquery,$i,'source');
        $background = getTimThumb($picture,180,180);
        $largepic = getTimThumb($picture,-1,800);
        $id =  mysql_result($photosquery,$i,'id');
        echo'
        <div id="fullsize_pic'.$id.'" class="to_be_Selected_full" style="background-image:url('.$background.');">

        </div>

        <script type="text/javascript">
            $( document ).ready(function(){
                $("#fullsize_pic'.$id.'").click(function(){  
                    addFullPic("'.$largepic.'");
                });
            });

        </script>
        ';
      }
    }
    ?>
    <script type="text/javascript">
    var last = 0;
    $("#fullsizePictureModal").scroll(function(){
        if($(this)[0].scrollHeight - $(this).scrollTop() <= $(this).outerHeight()) {
            if(last != $(".to_be_Selected_full:last").attr("id")) {
                var ajaxlocation = "loadStoryPics/" + $(".to_be_Selected_full:last").attr("id") + "/<?php echo $email; ?>" + "/full";
                console.log($(".to_be_Selected_full:last").attr("id"));
                $.ajax({
                    url: "/loadStoryPics/" + $(".to_be_Selected_full:last").attr("id") + "/<?php echo $email; ?>" + "/full",
                    success: function(html) {
                        console.log(html);
                        if(html) {
                            $("#fullsizePictureModal_scroll").append(html);
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
</div>

<div id="groupPictureModal" class="pictureModal">
<div class="modalHead" style="position:fixed;width:760px;z-index: 2147483647;overflow:hidden;">Select a photo</div>
<div id="groupPictureModal_scroll" class="pictureSelect" style="margin-top:80px;">

  <?
    if($ownerView == 1){
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
                    addFullPic_Group("'.$largepic.'");
                });
            });

        </script>
        ';
      }
    }
    ?>
    <script type="text/javascript">
    var last = 0;
    $("#groupPictureModal").scroll(function(){

        if($(this)[0].scrollHeight - $(this).scrollTop() <= $(this).outerHeight()) {
            if(last != $(".to_be_Selected_group:last").attr("id")) {
                var ajaxlocation = "loadStoryPics/" + $(".to_be_Selected_group:last").attr("id") + "/<?php echo $email; ?>" + "/group";
                console.log($(".to_be_Selected_group:last").attr("id"));
                $.ajax({
                    url: "/loadStoryPics/" + $(".to_be_Selected_group:last").attr("id") + "/<?php echo $email; ?>" + "/group",
                    success: function(html) {
                        console.log(html);
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
</div>

<div id="addGroupPictureModal" class="pictureModal">
<div class="modalHead" style="position:fixed;width:760px;z-index: 2147483647;overflow:hidden;">Select up to 4 photos<div id="savePictureGroup" style="float:right;height:100%;font-size:20px;color:black;cursor:pointer;padding:20px;">Save</div></div>
<div id="addGroupPictureModal_scroll" class="pictureSelect" style="margin-top:80px;">

  <?
    if($ownerView == 1){
      $photosquery = mysql_query("SELECT * FROM photos WHERE emailaddress = '$email' ORDER BY id DESC LIMIT 20");
      $numphotos = mysql_num_rows($photosquery);
      for($i = 0; $i < $numphotos; $i++){

        $picture = mysql_result($photosquery,$i,'source');
        $background = getTimThumb($picture,180,180);
        $largepic = getTimThumb($picture,-1,800);
        $id =  mysql_result($photosquery,$i,'id');
        echo'
        <div id="addGroupPic_'.$id.'" class="to_be_Selected_groupAdd" style="background-image:url('.$background.');">

        </div>

        <script type="text/javascript">
            $( document ).ready(function(){
                $("#addGroupPic_'.$id.'").click(function(){  
                    addPictureGroup("'.$largepic.'","#addGroupPic_'.$id.'");
                });
            });

        </script>
        ';
      }
    }
    ?>
    <script type="text/javascript">
    var last = 0;
    $("#addGroupPictureModal").scroll(function(){

        if($(this)[0].scrollHeight - $(this).scrollTop() <= $(this).outerHeight()) {

            if(last != $(".to_be_Selected_groupAdd:last").attr("id")) {
                var ajaxlocation = "loadStoryPics/" + $(".to_be_Selected_groupAdd:last").attr("id") + "/<?php echo $email; ?>" + "/group";
                console.log($(".to_be_Selected_groupAdd:last").attr("id"));
                $.ajax({
                    url: "/loadStoryPics/" + $(".to_be_Selected_groupAdd:last").attr("id") + "/<?php echo $email; ?>" + "/groupelement",
                    success: function(html) {
                        console.log(html);
                        if(html) {
                            $("#addGroupPictureModal_scroll").append(html);
                        }
                    }
                });
                last = $(".to_be_Selected_groupAdd:last").attr("id");
              }
            }
        });
    </script>
</div>
</div>
</div>

<div id="storyWrap" class="storyWrap">
    <div id="storyCover" class="story_cover">
        <div style="position:absolute;width:100%;height:100%;background-color:rgba(0,0,0,0.2);top:0;left:0;"></div>
        <div class="titleDiv" style="position:absolute;">
            <div id="story_title" class="titleTextDiv"  >
            </div>
        </div>
        <!-- <div class="titleDiv" style="position:absolute;">
            <div id="titleText">
            </div>
        </div> -->
        <div class="subTitleDiv1" >
            <div id="story_subtitle" class="subTitleText" >
            </div>
        </div>
        <div class="subTitleDiv2">
            <?php echo'
            <a href="http://'.$ownerDomain.'.coversplash.com">
            <div class="picDiv">
                <img id="ownerPic" src="" />
            </div>
            </a>';
            ?>
        </div>
        <div class="subTitleDiv3">
        <?php echo'
        <a href="http://'.$ownerDomain.'.coversplash.com">
            <div class="preNameDiv">
                Story By&nbsp
            </div>
            <div class="nameDiv"></div>
        </a>
            ';
        ?>
        </div>
        <div class="subTitleDiv4" style="margin-top:10px;">
            <div class="creationTimeDiv"></div>
        </div>
      <!--   <div class="titleDiv" id="arrowDownDiv" >
            <div id="arrowDown"></div>
        </div> -->
        <?php if($ownerView == 1){
         echo '<div class="cover_edit" id="coveredit" style="width:120px;margin-left:-60px;">Change Cover</div>'; 
         echo '<div class="cover_arrow" id="coverarrow" style="background-color:rgba(0,0,0,0);"><img  src="http://coversplash.com/img/arrow_down.png" /></div>'; 

         echo '<script>
                $(document).ready(function(){
                    $("#coverarrow").show();
                    $("#coveredit").click(function(){
                        $("#modalbg").show();
                        $("#coverPictureModal").show();
                        $(\'body\').css(\'overflow\', \'hidden\');
                    });
                    $("#coverarrow").click(function(){
                        scroll("#toolbox1",500);
                    });
                    
                });
               </script>';
         }?>
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
      map.setZoom(6);
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