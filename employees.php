
<?php 
	
	$id = ( $_GET['id'] ) ?? null;

	// check if users is logged in
	
	
?>

<!DOCTYPE html>
<html>
<head>
	<title>Employees</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">



	<style type="text/css">
		table { 
	width: 750px; 
	border-collapse: collapse; 
	margin:50px auto;
	}

/* Zebra striping */
tr:nth-of-type(odd) { 
	background: #eee; 
	}

th { 
	background: #3498db; 
	color: white; 
	font-weight: bold; 
	}

td, th { 
	padding: 10px; 
	border: 1px solid #ccc; 
	text-align: left; 
	font-size: 18px;
	}

/* 
Max width before this PARTICULAR table gets nasty
This query will take effect for any screen smaller than 760px
and also iPads specifically.
*/
@media 
only screen and (max-width: 760px),
(min-device-width: 768px) and (max-device-width: 1024px)  {

	table { 
	  	width: 100%; 
	}

	/* Force table to not be like tables anymore */
	table, thead, tbody, th, td, tr { 
		display: block; 
	}
	
	/* Hide table headers (but not display: none;, for accessibility) */
	thead tr { 
		position: absolute;
		top: -9999px;
		left: -9999px;
	}
	
	tr { border: 1px solid #ccc; }
	
	td { 
		/* Behave  like a "row" */
		border: none;
		border-bottom: 1px solid #eee; 
		position: relative;
		padding-left: 50%; 
	}

	td:before { 
		/* Now like a table header */
		position: absolute;
		/* Top/left values mimic padding */
		top: 6px;
		left: 6px;
		width: 45%; 
		padding-right: 10px; 
		white-space: nowrap;
		/* Label the data */
		content: attr(data-column);

		color: #000;
		font-weight: bold;
	}

}
	</style>
</head>
<body>
	<h2>Employees</h2>
	<a href="./create.php" style="padding: 13px 18px; border-radius: 7px; background-color: #216ea7; color: white; font-weight: bolder; text-decoration: none;">Insert New Record</a>
	<table>
	  <thead>
	    <tr>
	      <th>First Name</th>
	      <th>Last Name</th>
	      <th>Job Title</th>
	      <th>Email</th>
	      <th>Edit</th>
	      <th>Delete</th>
	    </tr>
	  </thead>
	  <tbody class="table_bd">
	   
		<div class="form_message" style="text-align: center; padding: 10px; padding-top: 20px; font-weight: bold; position: absolute; width: 100%; text-align: center; margin: auto;"></div>
	  </tbody>
	</table>

	<script type="text/javascript">
		$(document).ready(function () {
			let __id = `<?= $id;?>`;
			let __data = {};

			if ( __id !== '' ) {
				__data['id'] = __id;
			}

			function message_popup(status, message) {
				if ( status !== 'success') {
					$(".form_message").css('color', 'red');
				}
				else {
					$(".form_message").css('color', 'green');
				}
				$(".form_message").text(message);
			}

			

			$.ajax({
				url: '../crud_php/api/employees/get.php',
				type: 'GET',
				dataType: 'JSON',
				data: __data,


				complete: (response) => {
					if ( response.status !== 404 && response.statusText !== 'error' ) {
						let __data = response.responseText;
							__data = JSON.parse(__data);


						if ( Array.isArray(__data.message) ) {

							$(".form_message").fadeOut('fast');

							$(".table_bd").html('');

							__data.message.forEach(__element => {
								$(".table_bd").append(`
									 <tr>
								      <td data-column="First Name">${__element.firstname}</td>
								      <td data-column="Last Name">${__element.lastname}</td>
								      <td data-column="Job Title">${__element.job_title}</td>
								      <td data-column="Email">${__element.email}</td>
								      <td data-column="Email">
								      	<a href="./edit.php?id=${__element.id}"><i class="fa fa-edit"></i></a>
								      </td>
								      <td data-column="Email">
								      	<a class="delete_employee" id="${__element.id}" href="./api/employees/delete.php?id=${__element.id}"><i class="fa fa-trash"></i></a>
								      </td>
								    </tr>
								`);
							});


								/**
								 * Delete Employee Record
								 * @param  {Function} e){ let __that        [description]
								 * @param  {[type]}   error:()         [description]
								 * @return {[type]}                    [description]
								 */
								$(".delete_employee").click(function (e){
									let __that = $(this);
									let __url = __that.attr('href');
									let __data = {};
										__data['id'] = __that.attr('id');

									if ( __url !== undefined ) {
										$.ajax({
											url: __url,
											type: 'GET',
											dataType: 'JSON',
											data: __data,

											complete: response => {
												if ( response.status !== 404 && response.statusText !== 'error' ) {
													let __data = response.responseText;
													__data = JSON.parse(__data);

													if ( __data.status === 200 ) {
														__that.parent().parent().fadeOut('fast');
														message_popup("success", __data.message);
													}
													else {
														message_popup("error", __data.message);
													}

													$(".form_message").fadeIn('fast');

												}
													
											},

											error:() => message_popup('error', 'An Error Occurred')
										});
									}
									e.preventDefault();
									e.stopPropagation();

									return false;
								});
						}
						else {
							message_popup(404, __data.message);
						}

					}
					else {
						if ( response.statusText === 'error' ) {
							message_popup('error', 'Internal Server Error');
						}
						else {
							message_popup('error', 'An Error Occurred Please Try Again Later');
						}
					}
				},

				error:() => message_popup('error', 'An Error Occurred')
			});


		});
	</script>
</body>
</html>