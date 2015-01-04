/**
 * Created by User on 21.12.2014.
 */

$(window).load(function() {
    var cropperBlock = $('.cropper'),
        playerId = cropperBlock.attr('data-playerId'),
        lgImgSrc = cropperBlock.attr('data-lgImgSrc'),
        imageBox = $('.imageBox'),
        thumbContainer = $('.cropper__thumbContainer');
    var options =
    {
        thumbBox: '.thumbBox',
        spinner: '.spinner',
//            imgSrc: 'avatar.png'
        imgSrc: lgImgSrc
    };
    var cropper = imageBox.cropbox(options);
    $('#file').on('change', function(){
        console.log("on change");
        var reader = new FileReader();
        reader.onload = function(e) {
            options.imgSrc = e.target.result;
            cropper = imageBox.cropbox(options);
        };
        reader.readAsDataURL(this.files[0]);
        this.files = [];
        console.log("this files");
    });
    $('#btnCrop').on('click', function(){
        var img = cropper.getDataURL();
        thumbContainer.empty();
        thumbContainer.append('<img src="'+img+'" id="croppedImg" />');
        $('#btnSaveCropped').css('visibility','visible');
    })
    $('#btnZoomIn').on('click', function(){
        cropper.zoomIn();
    })
    $('#btnZoomOut').on('click', function(){
        cropper.zoomOut();
    })

});

$(function() {
    $('#btnSaveCropped').on('click', function(){
         var croppedSrc = $('#croppedImg').attr('src');
         var playerId = $('.cropper').attr('data-playerId');

        $.post("avatar_ajax.php",
            {
                'croppedSrc': croppedSrc,
                'playerId': playerId
            },
            function(data,status){
                    $('#saveResultMsg').text("Saved.");
            }
        );
        return false;

    });

})
