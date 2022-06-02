<!DOCTYPE html>
<html>
<head>
	<title>Register Form</title>
<link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.js"></script>


<style type="text/css">
	body{
	margin: 0;
	padding: 0;
	display: flex;
	justify-content: center;
	align-items: center;
	min-height: 100vh;
	font-family: 'Jost', sans-serif;
	background: linear-gradient(to bottom, #0f0c29, #302b63, #24243e);
}
.main{
	width: 350px;
	height: 500px;
	background: red;
	overflow: hidden;
	background: url("https://doc-08-2c-docs.googleusercontent.com/docs/securesc/68c90smiglihng9534mvqmq1946dmis5/fo0picsp1nhiucmc0l25s29respgpr4j/1631524275000/03522360960922298374/03522360960922298374/1Sx0jhdpEpnNIydS4rnN4kHSJtU1EyWka?e=view&authuser=0&nonce=gcrocepgbb17m&user=03522360960922298374&hash=tfhgbs86ka6divo3llbvp93mg4csvb38") no-repeat center/ cover;
	border-radius: 10px;
	box-shadow: 5px 20px 50px #000;
}
#chk{
	display: none;
}
.signup{
	position: relative;
	width:100%;
	height: 100%;
}
label{
	color: #fff;
	font-size: 2.3em;
	justify-content: center;
	display: flex;
	margin: 60px;
	font-weight: bold;
	cursor: pointer;
	transition: .5s ease-in-out;
}
input{
	width: 60%;
	height: 20px;
	background: #e0dede;
	justify-content: center;
	display: flex;
	margin: 20px auto;
	padding: 10px;
	border: none;
	outline: none;
	border-radius: 5px;
}
button{
	width: 60%;
	height: 40px;
	margin: 10px auto;
	justify-content: center;
	display: block;
	color: #fff;
	background: #573b8a;
	font-size: 1em;
	font-weight: bold;
	margin-top: 20px;
	outline: none;
	border: none;
	border-radius: 5px;
	transition: .2s ease-in;
	cursor: pointer;
}
button:hover{
	background: #6d44b8;
}
.login{
	height: 460px;
	background: #eee;
	border-radius: 60% / 10%;
	transform: translateY(-180px);
	transition: .8s ease-in-out;
}
.login label{
	color: #573b8a;
	transform: scale(.6);
}

#chk:checked ~ .login{
	transform: translateY(-500px);
}
#chk:checked ~ .login label{
	transform: scale(1);	
}
#chk:checked ~ .signup label{
	transform: scale(.6);
}

</style>
</head>
<body>
	<div class="main" style="position: relative;">  	
		<p class="form_message" style="text-align: center; padding: 10px; padding-top: 20px; font-weight: bold; position: absolute; width: 100%; text-align: center; margin: auto;"></p>
		<input type="checkbox" id="chk" aria-hidden="true">

			<div class="signup">
				<form autocomplete="off" id="signup_form" method="POST" action="../crud_php/api/auth/register.php">
					<label for="chk" aria-hidden="true">Sign up</label>
					<input type="text" name="name" placeholder="name" required="">
					<input type="email" name="email" placeholder="Email" required="">
					<input type="password" name="password" placeholder="Password" required="">
					<button>Sign up</button>
				</form>
			</div>

			<div class="login">
				<form autocomplete="off" id="login_form" action="../crud_php/api/auth/login.php" method="POST">
					<label for="chk" aria-hidden="true">Login</label>
					<input type="email" name="email" placeholder="Email" required="">
					<input type="password" name="password" placeholder="Password" required="">
					<button>Login</button>
				</form>
			</div>
	</div>

	<script type="text/javascript">
		$(document).ready(function (){

			function message_popup(status, message) {
				if ( status !== 'success') {
					$(".form_message").css('color', 'red');
				}
				else {
					$(".form_message").css('color', 'green');
				}
				$(".form_message").text(message);
			}

			function setCookie(name,value,days) {
			    var expires = "";
			    if (days) {
			        var date = new Date();
			        date.setTime(date.getTime() + (days*24*60*60*1000));
			        expires = "; expires=" + date.toUTCString();
			    }
			    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
			}

			function getCookie(name) {
			    var nameEQ = name + "=";
			    var ca = document.cookie.split(';');
			    for(var i=0;i < ca.length;i++) {
			        var c = ca[i];
			        while (c.charAt(0)==' ') c = c.substring(1,c.length);
			        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
			    }
			    return null;
			}



			$("form").submit(function (e) {
				let __that = $(this);
				let __data = {};
				let __formId = __that.attr('id');

				__that.find('[name]').each( function () {
					let __key = $(this).attr('name');
					let __val  = $(this).val();
					
					__data[__key] = __val;
				});



				// send ajax request to backend api
				$.ajax({
					url: $(this).attr('action'),
					type: 'POST',
					dataType: 'JSON',
					data: __data,


					complete: (response) => {

						if ( response.status !== 404 && response.statusText !== 'error' ) {
							let __data = response.responseText;
							__data = JSON.parse(__data);

							if ( __formId === "login_form" && __data.success === 200 ) {
								setCookie('loginToken', __data.message.token);

								window.location.href = './employees.php';

								message_popup( ( __data.success === 200 ) ? 'success':  'error', __data.message.message);
							}
							else {
								message_popup( ( __data.success === 200 ) ? 'success':  'error', __data.message);

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

					error: () => {
						message_popup('error', 'Error. Please Contact Admin');
					}
				});
				e.preventDefault();
				e.stopPropagation();

				return false;
			});
		});
	</script>
</body>
</html>