<?php /* Smarty version 2.6.19, created on 2015-07-21 11:10:52
         compiled from player.htm */ ?>
<div id="jquery_jplayer_1" class="jp-jplayer"></div>
<div id="jp_container_1" class="jp-audio">
	<div class="jp-type-single">
		<div class="jp-gui jp-interface">
			<ul class="jp-controls">
				<li><a href="javascript:;" class="jp-play" tabindex="1" style="display:block;">play</a></li>
				<li><a href="javascript:;" class="jp-pause" tabindex="1" style="display:block;">pause</a></li>
				<li><a href="javascript:;" class="jp-stop" tabindex="1" style="display:block;">stop</a></li>
				<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute" style="display:block;">mute</a></li>
				<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute" style="display:block;">unmute</a></li>
				<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume" style="display:block;">max volume</a></li>
			</ul>
			<div class="jp-progress">
				<div class="jp-seek-bar">
					<div class="jp-play-bar"></div>
				</div>
			</div>
			<div class="jp-volume-bar">
				<div class="jp-volume-bar-value"></div>
			</div>
			<div class="jp-time-holder">
				<div class="jp-current-time"></div>
				<div class="jp-duration"></div>

				<ul class="jp-toggles">
					<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat" style="display:block;">repeat</a></li>
					<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off" style="display:block;">repeat off</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<link href="themes/default/blue.monday/jplayer.blue.monday.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jssrc/jquery.jplayer.min.js"></script>
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
	$("#jquery_jplayer_1").jPlayer({
		swfPath: "charts",
		supplied: "mp3",
		ready:function(){
			<?php if ($this->_tpl_vars['callid']): ?>
			fn_listen(<?php echo $this->_tpl_vars['callid']; ?>
);
			<?php endif; ?>
		}
	});
});

//听录音
function fn_listen(callid,ag_id){
    if (ag_id) {
        var url = 'index.php?c=callrecords&m=get_record_url&callid='+callid+'&ag_id='+ag_id;
    } else {
        var url = 'index.php?c=callrecords&m=get_record_url&callid='+callid;
    }

	$("#jquery_jplayer_1").jPlayer("setMedia", {
		mp3:url
	});
	$("#jquery_jplayer_1").jPlayer('play');
}

//停止收听录音
function fn_stop_player()
{
	$("#jquery_jplayer_1").jPlayer('stop');
}
</script>