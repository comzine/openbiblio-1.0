<script language="JavaScript" >
<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
   See the file COPYRIGHT.html for more details.
 */
?>
// JavaScript Document
"use strict";

var wc = {
	init: function () {
		wc.url = '../catalog/catalogServer.php';
		wc.initWidgets();
		$('.help').hide();

		wc.video = document.querySelector('video');
		wc.videoOpts = { video:true, audio:false, };
    var errBack = function (error) {
 			alert("<?php echo T("allowWebcamAccess4Camera"); ?>");
			console.log("Video capture error: ", error.code);
		};
		var handleVideo = function (stream) {
	    wc.video.src = window.URL.createObjectURL(stream);
		};
		navigator.getUserMedia = navigator.getUserMedia
													 || navigator.webkitGetUserMedia || navigator.mozGetUserMedia
													 || navigator.msGetUserMedia || navigator.oGetUserMedia ;
		if (navigator.getUserMedia) {
		    navigator.getUserMedia(wc.videoOpts, handleVideo, errBack);
		}

		wc.canvasOut = document.getElementById('canvasOut'),
		wc.ctxOut = canvasOut.getContext('2d');
		wc.canvasIn = document.getElementById('canvasIn'),
		wc.ctxIn = canvasIn.getContext('2d');

		$('input.fotoSrceBtns').on('click',null,wc.changeImgSource);

		$('#capture').on('click',null,function() {
    	$('#errSpace').hide();
			wc.ctxIn.drawImage(wc.video,0,0, 150,100);
			wc.rotateImage(-90);
			wc.ctxOut.drawImage(wc.canvasIn,0,0, 100,150, 0,0, 100,150);
		});

		$('#browse').on('change',null,function (e) {
			// Get the FileList object from the file select event
			var files = e.target.files;
			if(files.length === 0) return;
			var file = files[0];
			if(file.type !== '' && !file.type.match('image.*')) return;
			$('#fotoName').val($('#browse').val());
			wc.readFile(file)
		});

		wc.resetForm();
	},
	//------------------------------
	initWidgets: function () {
	},
	//----//
	resetForm: function () {
		$('.help').hide();
		//$('#errSpace').hide();
		$('#fotoAreaDiv').show();
		wc.changeImgSource();
	},
	//----//
	changeImgSource: function (e) {
		var chkd = $('input[name=imgSrce]:checked', '#fotoForm').val();
		if (chkd == 'cam') {
			$('#camera').attr('autoplay',true);
			$('#fotoName').val('filename.jpg');
			$('#capture').show();
			$('#browse').hide();
		} else {
			$('#camera').removeAttr('autoplay');
			$('#fotoName').val('');
			$('#capture').hide();
			$('#browse').show();
		}
	},
	//----//
	rotateImage: function (angle) {
    var tw = wc.canvasIn.width/2,
				th = wc.canvasIn.height/2,
				angle = angle*(Math.PI/180.0);
		wc.ctxIn.save();
		wc.ctxIn.translate(tw, th);
		wc.ctxIn.rotate(angle);
		wc.ctxIn.translate(-th, -tw);
		wc.ctxIn.drawImage(canvasIn, 0,0);
		wc.ctxIn.restore();
	},
	eraseImage: function () {
		wc.ctxOut.clearRect(0,0, wc.canvasOut.width,wc.canvasOut.height)
		wc.ctxIn.clearRect(0,0, wc.canvasIn.width,wc.canvasIn.height)
	},
	readFile: function (file) {
		var reader = new FileReader();  // output is to reader.result
		reader.onerror = function () {
			console.log('FileReader error: '+reader.error);
			return;
		}
		reader.onloadend = function(e) {
    	var tempImg = new Image();
    	tempImg.src = reader.result;
    	tempImg.onload = function() {
        wc.ctxOut.drawImage(tempImg, 0, 0, 100,150);
			}
		};
    reader.readAsDataURL(file);
	},

	//------------------------------
	sendFoto: function (e) {
		e.stopPropagation();
    $('#errSpace').hide();
		var imgMode = '',
				url = $('#fotoName').val();
		imgMode = (url.substr(-3) == 'png')? 'image/png' : 'image/jpeg';
		$.post(wc.url,{'mode':'addNewPhoto',
									 'type':'base64',
									 'img':wc.canvasOut.toDataURL(imgMode, 0.8),
                   'bibid':$('#fotoBibid').val(),
									 'url': url,
                   'position':0,
									},
									function (response) {
										var data = JSON.parse(response);
										//console.log('image posting OK');
										bs.crntFotoUrl = '../photos/' + data[0]['url'];
										$('#fotoMsg').html('cover photo posted').show();
										$('#bibBlkB').html('<img src="'+bs.crntFotoUrl+'" id="biblioFoto" class="hover" '
      									+ 'height="'+bs.fotoHeight+'" width="'+bs.fotoWidth+'" >');
										$('#photoAddBtn').hide();
										$('#photoEditBtn').show();
									}
		);
		//e.preventDefault();
		return false;
	},
	doUpdatePhoto: function () {
	  /// left as an exercise for the motivated - FL (I'm burned out on this project)
	},
	doDeletePhoto: function (e) {
		if (confirm("<?php echo T("Are you sure you want to delete this cover photo"); ?>")) {
	  	$.post(bs.url,{'mode':'deletePhoto',
										 'bibid':$('#fotoBibid').val(),
										 'url':$('#fotoName').val(),
										},
										function(response){
											wc.eraseImage();
											$('#bibBlkB').html('<img src="../images/shim.gif" id="biblioFoto" class="noHover" '
      													+ 'height="'+bs.fotoHeight+'" width="'+bs.fotoWidth+'" >');
                      idis.crntFoto = null;
						          $('#fotoName').val('');
											$('#photoAddBtn').show();
											$('#photoEditBtn').hide();
											$('#fotoMsg').html('cover photo deleted').show();
										}
			);
		}
		e.stopPropagation();
		return false;
	},

}
$(document).ready(wc.init);
</script>