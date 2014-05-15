<?php
/*
*
*
*/

include('response.php');
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>Validate</title>

	<link rel="stylesheet" href="assets/css/normalize.css">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="assets/css/foundation.min.css">
	<link rel="stylesheet" href="assets/css/global.css">

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
</head>

<body>

	<header class="demo-header" role="banner">
		<div class="row">
			<div class="demo-header_left column small-6">
				Validate
			</div>
			<div class="demo-header_right column small-6">
				Form Validation With PHP &amp; AJAX
			</div>
		</div>
	</header>
	<section class="demo-top">
		<div class="row">
		<h1>Form Validation</h1>
		</div>
	</section>

	<main id="main" role="main">
	<div class="row">
		
		<div id="form-container" class="column medium-9 medium-centered">
		<form id="form" method="POST" action="">

			<h5 id="form-headline">A Starter Example.</h5>
			
			<div class="row">

				<div class="column medium-6">
				<label for="name">Name:</label>
				<input id="name" name="name" type="text" placeholder="Enter Your Name" value="<?php echo $_POST['name']; ?>">
				<small class="error">Name is a required field.</small>
				</div>
				
				<div class="column medium-6">
				<label for="email">Email:</label>
				<input id="email" name="email" type="email" placeholder="Enter Email Address" value="<?php echo $_POST['email']; ?>">
				<small class="error">Email is a required field.</small>
				</div>
			
			</div>

			<div class="row">
				
				<div class="column medium-6">
				<label for="url">Your Website:</label>
				<input id="url" name="url" type="text" placeholder="yourwebsite.com" value="<?php echo $_POST['url']; ?>">
				<small class="error">A valid domain name is required.</small>
				</div>
				
				<div class="column medium-6">
				<label for="email">Phone Number:</label>
				<input id="phone" name="phone" type="tel" placeholder="Your Phone Number" value="<?php echo $_POST['phone']; ?>">
				<small class="error">A phone number is required.</small>
				</div>
			</div>
			

			<div class="row">

				<div class="column">
				<label for="choice">Make a Choice:</label>
				<select name="choice" id="choice">
					<option value="">Choose an Option</option>
					<option value="first_choice" <?php if($v->checked['first_choice'] == true) {echo 'selected';} ?>>First Choice</option>
					<option value="second_choice" <?php if($v->checked['second_choice'] == true) {echo 'selected';} ?>>Second Choice</option>
				</select>
				<small class="error">You Must Make a Choice.</small>
				</div>
			</div>



			<div class="row">
				
				<div class="column large-6">
				<fieldset>
					<legend>Your Age:</legend>
					<div class="field-group">
					<input id="young" name="age_range" type="radio" value="young" <?php if($v->checked['young'] == true) {echo 'checked';} ?> />
					<label for="young">18-30</label>
					<input id="mid" name="age_range" type="radio" value="mid" <?php if($v->checked['mid'] == true) {echo 'checked';} ?> />
					<label for="mid">30-55</label>
					<input id="old" name="age_range" type="radio" value="old" <?php if($v->checked['old'] == true) {echo 'checked';} ?> />
					<label for="old">55 +</label>
					</div>
				</fieldset>
				</div>
				
				<div class="column large-6">
				<fieldset>
					<legend>Meals Required:</legend>

					<div class="field-group">
					<input id="brkfast" name="food[]" type="checkbox" value="brkfast" <?php if($v->checked['food']['brkfast'] == true) {echo 'checked';} ?> />
					<label for="brkfast">Breakfast</label>
					<input id="lunch" name="food[]" type="checkbox" value="lunch" <?php if($v->checked['food']['lunch'] == true) {echo 'checked';} ?> />
					<label for="lunch">Lunch</label>
					<input id="dinner" name="food[]" type="checkbox" value="dinner"<?php if($v->checked['food']['dinner'] == true) {echo 'checked';} ?> />
					<label for="dinner">Dinner</label>
					</div>
				</fieldset>
				</div>

			</div>
			
			<div class="row">
				<div class="column">
				<label for="msg">Message</label>
				<textarea name="msg" id="msg" placeholder="Enter Your Message..."><?php echo $_POST['msg']; ?></textarea>
				<small class="error">A Message is Required.</small>
				</div>
			</div>
			
			<div class="row">
				<div class="column">
				<input id="submit" type="submit" value="submit">
				</div>
			</div>

		</form>
</div>
</div>

</main>

<footer class="demo-footer" role="contentInfo">
	<div class="row">
		
	</div>
</footer>

	<script>

	// placholder and error clear
	$(function() {
		
		var ph;
		$('#form-container input, textarea').focus( function() {
			ph = $(this).attr('placeholder');
			$(this).attr('placeholder', '');
		});
		$('#form-container input, textarea').blur( function() {
			$(this).attr('placeholder', ph);
		});

		$('#form').on( 'change', 'input, select, textarea', function() {
			var prnt = $(this).parent('div');
			if ( prnt.hasClass('error') ) {
				prnt.removeClass('error');
			}
		});

	});


	// submit event starts
    $('#form').on( 'submit', function(e) {

    	e.preventDefault();

    	var ajaxUrl = "response.php";

    	var formData = { 
    		name: $('#name').val(), 
    		email: $('#email').val(), 
    		url: $('#url').val(), 
    		choice: $('#choice').val(),
    		phone: $('#phone').val(),
    		msg: $('#msg').val(),
    		age_range: $('input[name="age_range"]:checked').val(),
    		food: $('input[name^="food"]:checked').val()
    	}
	    
	    $.ajax({
	        type: "POST",
	        url: ajaxUrl,
	        data: formData,
	        datatype: "json",
	        async: true,
	        cache: false,
	        
	        beforeSend: function () {
	        	$('#form div').removeClass('error');	        	
	        },

	        success: function (data) {

	            var dObj = (typeof data === "string") ? JSON.parse(data) : data;

	            if (dObj.empty) {
		            $.each( dObj.empty, function(index, value) {	
		            	$('[name^="'+value+'"]').parent('div').addClass('error');			            
		            });
		        }

		       if (dObj.errors) {
		       		$.each( dObj.errors , function(index, value) {
		       			var elem = $('[name="'+index+'"]');
		       			elem.parent('div').addClass('error');
		       			elem.next('small.error').text(value);
		       		});
		       }

		       if (dObj.mail) {
		       		$('#form-headline').text(dObj.mail);
		       }
	           
	           if (dObj.sent) {
	           		$('#form-headline').text(dObj.sent);
	           } 
         
	        },

	        error: function () {
	        	$('#form-headline').text("An Error Occurred.");
	        }
	    });

    }); // end form handling

	</script>
	
</body>
</html>