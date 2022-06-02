
<?php 
  
  $id = ( $_GET['id'] ) ?? null;
  
?>

<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Employee</title>
        <link href='https://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/main.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.js"></script>

        <style type="text/css">
          *, *:before, *:after {
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}

body {
  font-family: 'Nunito', sans-serif;
  color: #384047;
}

form {
  max-width: 300px;
  margin: 10px auto;
  padding: 10px 20px;
  background: #f4f7f8;
  border-radius: 8px;
}

h1 {
  margin: 0 0 30px 0;
  text-align: center;
}

input[type="text"],
input[type="password"],
input[type="date"],
input[type="datetime"],
input[type="email"],
input[type="number"],
input[type="search"],
input[type="tel"],
input[type="time"],
input[type="url"],
textarea,
select {
  background: rgba(255,255,255,0.1);
  border: none;
  font-size: 16px;
  height: auto;
  margin: 0;
  outline: 0;
  padding: 15px;
  width: 100%;
  background-color: #e8eeef;
  color: #8a97a0;
  box-shadow: 0 1px 0 rgba(0,0,0,0.03) inset;
  margin-bottom: 30px;
}

input[type="radio"],
input[type="checkbox"] {
  margin: 0 4px 8px 0;
}

select {
  padding: 6px;
  height: 32px;
  border-radius: 2px;
}

button {
  padding: 19px 39px 18px 39px;
  color: #FFF;
  background-color: #4bc970;
  font-size: 18px;
  text-align: center;
  font-style: normal;
  border-radius: 5px;
  width: 100%;
  border: 1px solid #3ac162;
  border-width: 1px 1px 3px;
  box-shadow: 0 -1px 0 rgba(255,255,255,0.1) inset;
  margin-bottom: 10px;
}

fieldset {
  margin-bottom: 30px;
  border: none;
}

legend {
  font-size: 1.4em;
  margin-bottom: 10px;
}

label {
  display: block;
  margin-bottom: 8px;
}

label.light {
  font-weight: 300;
  display: inline;
}

.number {
  background-color: #5fcf80;
  color: #fff;
  height: 30px;
  width: 30px;
  display: inline-block;
  font-size: 0.8em;
  margin-right: 4px;
  line-height: 30px;
  text-align: center;
  text-shadow: 0 1px 0 rgba(255,255,255,0.2);
  border-radius: 100%;
}

@media screen and (min-width: 480px) {

  form {
    max-width: 480px;
  }

}

        </style>
    </head>
    <body>
      <div class="row">
    <div class="col-md-12">
      <form action="index.html" method="post">
        <h1> Update </h1>
        
        <fieldset>
          
          <legend><span class="number">1</span> Basic Info</legend>
        
          <label for="firstname">FirstName:</label>
          <input type="text" id="firstname" name="firstname">
        
          <label for="lastname">LastName:</label>
          <input type="text" id="lastname" name="lastname">

          <label for="email">Email:</label>
          <input type="email" id="email" name="email">
       
  
          
        </fieldset>
        <fieldset>  
        
          <legend><span class="number">2</span> Profile</legend>
               
        
          <label for="job">Job Role:</label>
          <input type="text" id="user_job" name="user_job">
                    
         </fieldset>

         <input type="hidden" name="id" value="<?= $id;?>">
                <div class="form_message" style="text-align: center; padding: 10px; padding-top: 20px; font-weight: bold; position: relative; width: 100%; text-align: center; margin: auto;"></div>

        <button type="submit">Update</button>
        
       </form>
        </div>
      </div>
      

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

      // 

      

      $.ajax({
        url: '../crud_php/api/employees/get.php',
        type: 'GET',
        dataType: 'JSON',
        data: __data,


        complete: (response) => {
          if ( response.status !== 404 && response.statusText !== 'error' ) {
            let __data = response.responseText;
              __data = JSON.parse(__data);
           
              if ( __data.success === 200 ) {
                $("[name=firstname]").val(__data.message[0].firstname);
                $("[name=lastname]").val(__data.message[0].lastname);
                $("[name=email]").val(__data.message[0].email);
                $("[name=user_job]").val(__data.message[0].job_title);

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

        error:() => message_popup('error', 'An Error Occurred')
      });



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
          url: '../crud_php/api/employees/update.php',
          type: 'POST',
          dataType: 'JSON',
          data: __data,


          complete: (response) => {
            if ( response.status !== 404 && response.statusText !== 'error' ) {
              let __data = response.responseText;
              __data = JSON.parse(__data);

              if ( __data.success === 200 ) {
                message_popup( ( __data.success === 200 ) ? 'success':  'error', __data.message);
                // window.location.href = window.location.href;
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
