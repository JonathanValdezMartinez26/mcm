$(document).ready(function(){

	//var imgDefault = '';
        if($('#preView').attr('src'))
            imgDefault = $('#preView').attr('src');

 	$("#launch").click(function(){

	    $("#myModal").modal('show');

	    var data = new FormData();

            data.append('command',2);

            $.ajax({

                url: "http://test.coinkcoink.com:90/controller/FileManager.php?folder="+folder,

                type: "post",

                dataType: "json",

                data: data,

                cache: false,

                contentType: false,

                processData: false

            }).done(function(res){

                $('#modal-imagen').html(res.htmltext);

                if(res.total > $('.file_th').length){

                    $("#mas").prop("disabled",false);

                }else{

                    $("#mas").prop("disabled",true);

                }

                $('.file_th').dblclick(function(){

		    $("#imagenes").html('');
		    $("#imagenes").append("<input type='hidden' name='imagen-select' value='"+$(this).attr('data')+"'>");
		    $("#preView").attr('src',$(this).children().attr('src'));

                    $('#myModal').modal('hide');

		    alert('imagen añadida correctamente');

                });

            });



	});



        $("#cargar").click(function(){

	    $("#cargar").prop( "disabled", true );

	    var data = new FormData();

	    jQuery.each(jQuery('#imagen_input')[0].files, function(i, file) {

            	data.append('userfile', file);

	    });

 	    data.append('command', 1);

	    $.ajax({

    	        url: "http://test.coinkcoink.com:90/controller/FileManager.php?folder="+folder,

    	        type: "post",

    	        dataType: "json",

    	        data: data,

    	        cache: false,

    	        contentType: false,

    	        processData: false

    	    }).done(function(res){

	    alert(res.status);

	    $("#imagen_input").val('');

	    if(res.status == 'ok'){

		    $("#imagenes").html('');
		    $("#imagenes").append("<input type='hidden' name='imagen-select' value='"+res.fileName+"'>");
		    $("#preView").attr('src',$(this).children().attr('src'));

                    $('#myModal').modal('hide');

		    alert('imagen añadida correctamente');

	        }

	    });	

        });



    $("#actualizar").click(function(){

        var data = new FormData();

	data.append('command',2);

	$.ajax({

            url: "http://test.coinkcoink.com:90/controller/FileManager.php?folder="+folder,

            type: "post",

            dataType: "json",

            data: data,

            cache: false,

            contentType: false,

            processData: false

        }).done(function(res){

	    $('#modal-imagen').html(res.htmltext);

	    if(res.total > $('.file_th').length){

	        $("#mas").prop("disabled",false);

	    }else{

	        $("#mas").prop("disabled",true);

	    }

	        $('.file_th').dblclick(function(){

		    $("#imagenes").html('');
        	    $("#imagenes").append("<input type='hidden' name='imagen-select' value='"+$(this).attr('data')+"'>");
		    $("#preView").attr('src',$(this).children().attr('src'));

		    $('#myModal').modal('hide');

		    alert('imagen añadida correctamente');

    		});

        });

    });

	

    $( "#mas" ).click(function() {

	var data = new FormData();

        data.append('command',3);

	data.append('lastImg',$('.file_th:last').attr('data'));

	data.append('numImg',9);

        $.ajax({

            url: "http://test.coinkcoink.com:90/controller/FileManager.php?folder="+folder,

            type: "post",

            dataType: "json",

            data: data,

            cache: false,

            contentType: false,

            processData: false

        }).done(function(res){

            $('#modal-imagen').html($('#modal-imagen').html()+res.htmltext);

	    if($('.file_th').length < res.total){

                $("#mas").prop("disabled",false);

            }else{

                $("#mas").prop("disabled",true);

            }

	    $('.file_th').dblclick(function(){

		$("#imagenes").html('');
                $("#imagenes").append("<input type='hidden' name='imagen-select' value='"+$(this).attr('data')+"'>");
		$("#preView").attr('src',$(this).children().attr('src'));

		$('#myModal').modal('hide');

		 alert('imagen añadida correctamente');

    	    });

        });

    });

    

    $('.file_th').dblclick(function(){

	$("#imagenes").html('');
	$("#imagenes").append("<input type='hidden' name='imagen-select' value='"+$(this).attr('data')+"'>");
	$("#preView").attr('src',$(this).children().attr('src'));

	$('#myModal').modal('hide');

	 alert('imagen añadida correctamente');

    });



    $('#imagen_input').change(function(){

            if($('#imagen_input').val() != ''){

                $("#cargar").prop( "disabled", false );

            }

            else{

                $("#cargar").prop( "disabled", true );

            }

    });

    $('#preView').click(function(){

        if(imgDefault !== $(this).attr('src')){

            $(this).attr('src',imgDefault);

            $("#imagenes").html('');

            $("#imagenes").append("<input type='hidden' name='imagen-select' value='"+imgDefault+"'>");

        }

    });
});
