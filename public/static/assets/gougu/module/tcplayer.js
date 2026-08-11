layui.define([], function (exports) {
	var MOD_NAME = 'tcplayer';
	var modFile = layui.cache.modules['tcplayer'];
	var modPath = modFile.substr(0, modFile.lastIndexOf('.'));
	var screenSwitchEnabled = true;
	var settings = {
		poster: '/static/home/images/bg.png',
		url: '',
		watermark: '勾股EDU学习培训考试系统',
		switch_enabled: true,//切换页面停止播放
		contextmenu: false,//禁止网页右击
		canplay:null,
		ended:null,
		timeupdate:null,
		controls:true,
		autoplay:false,
		loop:false,
		muted:false,
		width:853,
		height:480,
		playbackRateMenuButton:true,
		playbackRates:[0.5,1,1.25,1.5,2,3]
	};
	var tcplayer = {
		init: function (playerId, options) {
			loadScript();
			var opts = $.extend({}, settings, options);
			var id=Date.now();
			$('#'+playerId).html('<video id="player_'+id+'" width="'+opts.width+'" height="'+opts.height+'" preload="auto" playsinline webkit-playsinline></video>');
			if(player){
				player.dispose();
			}
			var player = TCPlayer('player_'+id, {
				poster:opts.poster,
				playbackRates:opts.playbackRates,
				controls:opts.controls,
				autoplay:opts.autoplay,
				loop:opts.loop,
				muted:opts.muted,
				sources:[],
				plugins:{
					DynamicWatermark: {
						type: 'text',
						speed: 0.3,// 建议取值范围 0<= speed <=1，默认值 0.2
						content: opts.watermark, // 类型必须为String，
						opacity: 0.6,
						fontSize: '16px',
						color: '#bdbdbd'
					}
				},
				controlBar:{
					playbackRateMenuButton:opts.playbackRateMenuButton
				}
			});
			player.src(opts.url);
			player.on('canplay', function (){
				let duration = player.duration();
				if(typeof opts.canplay==='function'){
					 opts.canplay(duration);
				}
			});
			player.on('ended', function (){
				let duration = player.duration();
				if(typeof opts.ended==='function'){
					 opts.ended(duration);
				}
			});
			player.on('timeupdate', function (){
				let currentTime = player.currentTime();
				if(typeof opts.timeupdate==='function'){
					 opts.timeupdate(currentTime);
				}
			});
			player.on('error', function(error) {
				console.log(error);
			   var content = '<div class="layui-font-red layui-padding-4">'+error.data.message+'</div>';
				layer.open({
					id: 'switch-layer',
					type: 1,
					title: '错误提示',
					area: ['500px', '180px'],
					shade: 0.5,
					content: content,
					cancel: function () {
						//player.play();
					}
				});
			});
			
			$('#'+playerId).on('contextmenu', function () {
				$('.vjs-menu-content').hide();
				return false;
			});
			if (opts.contextmenu == false) {
				$(document).on('contextmenu', function () {
					return false;
				});

				$(document).on('selectstart', function () {
					return false;
				});

				$(document).on('keydown', function (e) {
					if (e.key === 'F12') {
						return false;
					} else if (e.ctrlKey && e.key === 'u') {
						return false;
					} else if (e.ctrlKey && e.shiftKey && e.key === 'C') {
						return false;
					} else if (e.ctrlKey && e.shiftKey && e.key === 'I') {
						return false;
					}
				});
			}

			if (opts.switch_enabled == true) {
				screenSwitch();
				layerPopups();
			}

			function screenSwitch() {
				$('.dplayer-video').attr('disablePictureInPicture', true);
				$(document).on('visibilitychange', function () {
					if (document.hidden && screenSwitchEnabled) {
						screenSwitchHandler();
					}
				});
				$(window).on('blur', function () {
					if (screenSwitchEnabled) {
						screenSwitchHandler();
					}
				});
			}

			function screenSwitchHandler() {
				if(player.paused()==false){
					player.pause();
					var content = '<div class="layui-font-red layui-padding-4">监测到屏幕切换，自动停止播放！</div>';
					layer.open({
						id: 'switch-layer',
						type: 1,
						title: '系统提示',
						area: ['300px', '150px'],
						shade: 0.5,
						content: content,
						cancel: function () {
							player.play();
						}
					});
				}
			}
			
			function layerPopups() {
				var checkInterval = null;
				function hasLayerPopups() {
					return $('.layui-layer').length > 0;
				}

				function hasAntiSwitchLayerPopups() {
					return $('#switch-layer').length > 0;
				}

				function startMonitor() {
					if (checkInterval) return;
					checkInterval = setInterval(function () {
						if (hasLayerPopups() && !hasAntiSwitchLayerPopups()) {
							screenSwitchEnabled = false;
							player.pause();
						} else {
							screenSwitchEnabled = true;
						}
					}, 500);
				}
				startMonitor();
			}
			
			//事件文档https://cloud.tencent.com/document/product/881/30820
			/*
			var events = [
				"loadstart",
				"suspend",
				"abort",
				"error",
				"emptied",
				"stalled",
				"loadedmetadata",
				"loadeddata",
				"canplay",
				"canplaythrough",
				"playing",
				"waiting",
				"seeking",
				"seeked",
				"ended",
				"durationchange",
				"timeupdate",
				"progress",
				"play",
				"pause",
				"ratechange",
				"resize",
				"volumechange"
			];
			events.forEach(function(val, i){
				player.on(val, function (event) {
					console.info(val, event);
					let currentTime = player.currentTime();
					let duration = player.duration();
					console.log(duration);
				});
			});
			*/
		}
	}
	
	function loadScript() {
		if (typeof TCPlayer == 'undefined') {
			$.ajax({
				url: modPath + '/tcplayer.v4.8.0.min.js',
				dataType: 'script',
				cache: true,
				async: false
			});
			$.ajax({
				url: modPath + '/libs/hls.min.1.1.6.js',
				dataType: 'script',
				cache: true,
				async: false
			});
			loadCSS();
		}
	}
	
	function loadCSS() {
		var link = document.createElement("link");
		link.rel = "stylesheet";
		link.type = "text/css";
		link.href = modPath + '/tcplayer.min.css';
		document.getElementsByTagName("head")[0].appendChild(link);
	}
	
	loadScript();
	exports(MOD_NAME, tcplayer);
});
