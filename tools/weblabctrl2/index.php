<?
include "config.php";
?>
<html>
<head>
<script src="scriptaculous/prototype.js" type="text/javascript"></script>
<script src="scriptaculous/scriptaculous.js" type="text/javascript"></script>
<script src="weblabctrl.js" type="text/javascript"></script>
<?
if($_COOKIE['pwmx']) $pwmx=$_COOKIE['pwmx'];
else $pwmx=50;
if($_COOKIE['pwmy']) $pwmy=$_COOKIE['pwmy'];
else $pwmy=60;
if($_COOKIE['mpdx']) $mpdx=$_COOKIE['mpdx'];
else $mpdx=350;
if($_COOKIE['mpdy']) $mpdy=$_COOKIE['mpdy'];
else $mpdy=60;
if($_COOKIE['miscx']) $miscx=$_COOKIE['miscx'];
else $miscx=650;
if($_COOKIE['miscy']) $miscy=$_COOKIE['miscy'];
else $miscy=60;
echo "<style type=\"text/css\">
  * {font-family:Arial;}
  div.slider { width:256px; margin:10px 0; background-color:#ccc; height:10px; position: relative; }
  div.slider div.handle { width:10px; height:15px; background-color:#f00; cursor:move; position: absolute; }
  div.pwm {position:absolute; left:".$pwmx."; top: ".$pwmy."; background-color:#ddddff; width: 256px; padding:5px;}
  div.pwm_top {background-color:#ccccff; width: 256px; margin-bottom:5px;}
  div.mpd {position:absolute; left:".$mpdx."; top: ".$mpdy."; background-color:#ddddff; width: 256px; padding:5px;}
  div.mpd_top {background-color:#ccccff; width: 256px; margin-bottom:5px;}
  div.misc {position:absolute; left:".$miscx."; top: ".$miscy."; background-color:#ddddff; width: 256px; padding:5px;}
  div.misc_top {background-color:#ccccff; width: 256px; margin-bottom:5px;}
  body {margin: 0px; padding: 0px;}
  input {}
</style>";
?>

</head>
<body>
<script>
function set_value(cmd,id,value)
{
  new Ajax.Updater('ajax', 'set.php?cmd='+cmd+'&id='+id+'&value='+value,{method:'get', onComplete:function() {done=true;}, evalScripts: true} );
}
function mpd_cmd(room,cmd,value)
{
  new Ajax.Updater('ajax', 'mpd.php?cmd='+cmd+'&room='+room+'&value='+value,{method:'get', onComplete:function() {done=true;}} );
}
function beamer_on()
{
  new Ajax.Updater('ajax', 'set.php?cmd=beamer_on',{method:'get', onComplete:function() {done=true;}} );
}
function text_the_borg(text)
{
  new Ajax.Updater('ajax', 'set.php?cmd=text_the_borg&text='+text,{method:'get', onComplete:function() {done=true;}} );
}
function save_pos(element)
{
  new Ajax.Updater('ajax', 'set.php?cmd=save_pos&x='+document.getElementById(element.id).style.left+'&y='+document.getElementById(element.id).style.top,{method:'get', onComplete:function() {done=true;}} );
}
</script>
<div id="ajax">
#debug
</div>
<input type="button" value="svn update" onclick="new Ajax.Updater('ajax', 'set.php?update=doit',{method:'get'});">
<div id="status"> </div>
<div id="pwm" class="pwm">
<div class="pwm_top" on_click="Effect.toggle('pwm_entries','slide');">Light</div>
<div id="pwm_entries">
<?
foreach($pwm_ids as $id)
{
	$color=sprintf('#%02X%02X%02X',rand(0,255),rand(0,255),rand(0,255));
	echo "$id <input id=\"LAMP_$id\" type=\"checkbox\" onclick=\"if(this.checked)set_value('SW','LAMP_$id','ON');else set_value('SW','LAMP_$id','OFF')\">
	<div id=\"slider_$id\" class=\"slider\"> 
            <div class=\"handle\" style=\"background-color: $color;\"></div>
        </div>\n";
}
?>
</div>
</div>

<div id="mpd" class="mpd">
<div class="mpd_top" on_click="Effect.toggle('mpd_entries','slide');">Music</div>
<div id="mpd_entries">
<?
foreach($rooms as $room => $port)
{
	#unset($status);
	#exec("export MPD_PORT=$port; mpc",$status);
	$color=sprintf('#%02X%02X%02X',rand(0,255),rand(0,255),rand(0,255));
	#$color="#".dechex(rand(0,255)).dechex(rand(0,255)).dechex(rand(0,255));
	echo "$room<br> <input type=\"button\" id=\"".$room."_pause\" class=\"".$room."_button\" onclick=\"mpd_cmd('$room','pause');\" value=\"pause\">";
	echo "<input type=\"button\" id=\"".$room."_play\" class=\"".$room."_button\" onclick=\"mpd_cmd('$room','play');\" value=\"play\">";
	echo "<input type=\"button\" id=\"".$room."_prev\" class=\"".$room."_button\" onclick=\"mpd_cmd('$room','prev');\" value=\"prev\">";
	echo "<input type=\"button\" id=\"".$room."_next\" class=\"".$room."_button\" onclick=\"mpd_cmd('$room','next');\" value=\"next\"><br>";
	echo "<div id=\"".$room."_status\"></div>";
	#if(strpos($status[0],"volume")===false) echo substr(htmlspecialchars($status[0]),0,24);
	echo "<div id=\"slider_mpd_volume_$room\" class=\"slider\"> 
            <div class=\"handle\" style=\"background-color: $color;\"></div>
        </div>\n";
	$color=sprintf('#%02X%02X%02X',rand(0,255),rand(0,255),rand(0,255));
	#$color="#".dechex(rand(0,255)).dechex(rand(0,255)).dechex(rand(0,255));
	echo "<div id=\"slider_mpd_process_$room\" class=\"slider\"> 
            <div class=\"handle\" style=\"background-color: $color;\"></div>
        </div>\n";
}
?>
</div>
</div>

<div id="misc" class="misc">
<div class="misc_top">Misc</div>
<input type="button" id="beamer_button" onclick="beamer_on();" value="Beamer on">
<br>Laufschriftborg
<input type="text"  onchange="text_the_borg(this.value);this.value='';" value="">
</div>
<script>

  (function() {
<?
foreach($pwm_ids as $id)
{

echo "
      var slider_$id = $('slider_$id');

      slider_".$id."_control = new Control.Slider(slider_$id.select('.handle'), slider_$id, {
      range: \$R(0, 255),
      increment: 16,
      sliderValue: [100],
      onSlide: function(values) {
                        value=Math.round(values);
			if(done)set_value('PWM','$id',value);
			done=false;
      }
    });";
}

foreach($rooms as $room => $port)
{
	unset($volume);
	exec("export MPD_PORT=$port; mpc volume",$volume);
	preg_match('/volume: (..)%/',$volume[0],$volume);
	echo "
      var slider_mpd_volume_$room = $('slider_mpd_volume_$room');


      new Control.Slider(slider_mpd_volume_$room.select('.handle'), slider_mpd_volume_$room, {
      range: \$R(0, 100),
      increment: 10,
      sliderValue: [".$volume[1]."],
      onSlide: function(values) {
                        value=Math.round(values);
			if(done)mpd_cmd('$room','volume',value);
			done=false;
      }
    });";
	echo "var slider_mpd_process_$room = $('slider_mpd_process_$room');
	slider_process_".$room."_control = new Control.Slider(slider_mpd_process_$room.select('.handle'), slider_mpd_process_$room, {
      range: \$R(0, 100),disabled:true});";
}
echo "  })();\n";
echo "function update_status(){\n";
echo "new Ajax.Updater('status', 'status.php',{method:'get', evalScripts: true} );";
echo "\nwindow.setTimeout('update_status()',1000);\n";
echo "}\nupdate_status();";
?>    
new Draggable('pwm',{onEnd:function(element){new Ajax.Updater('ajax', 'set.php?cmd=save_pos&div=pwm&x='+document.getElementById('pwm').style.left+'&y='+document.getElementById('pwm').style.top,{method:'get', onComplete:function() {done=true;}} );}});
new Draggable('mpd',{onEnd:function(element){new Ajax.Updater('ajax', 'set.php?cmd=save_pos&div=mpd&x='+document.getElementById('mpd').style.left+'&y='+document.getElementById('mpd').style.top,{method:'get', onComplete:function() {done=true;}} );}});
new Draggable('misc',{onEnd:function(element){new Ajax.Updater('ajax', 'set.php?cmd=save_pos&div=misc&x='+document.getElementById('misc').style.left+'&y='+document.getElementById('misc').style.top,{method:'get', onComplete:function() {done=true;}} );}});

var done=true;
</script>
</body>
</html>
