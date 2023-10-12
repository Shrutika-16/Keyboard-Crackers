<?php
session_start();
//error_reporting(0);
include('database/config.php');

// Function to generate OTP and send SMS
function sendOTP($mobile, $otp) {
    // Replace with your API key, sender ID, and message template
   // $apikey = "YOUR_API_KEY";
//    $senderid = "YOUR_SENDER_ID";
  //  $template = "Your OTP is $otp. Please enter this on the registration page.";

    
    $curl = curl_init();

    // Send POST request to API endpoint
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2?authorization=T5SPCEoQMHUrm8ntIVykY70wf3bzLDqdu6OpRNlGs1FhgKJiBedOwZr5XSglFzKIpxT0ePBiLh94Qybm&variables_values=".$otp."&route=otp&numbers=".urlencode($mobile),
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

    // Return true if SMS was sent successfully
    return (strpos($response, "SMS sent successfully") !== false);
}

// Function to generate random OTP
function generateOTP() {
    return rand(100000, 999999);
}

// Function to register user and OTP in database
function registerUser($fname,$lname,$email,$phone,$department,$category,$address,$dob,$gender,$pass,$otp) {
   include('database/config.php');
   include 'includes/uniques.php';

    $student_id = 'S'.get_rand_numbers(3).'-'.get_rand_numbers(3).'-'.get_rand_numbers(3).'';
 
    $sql = "INSERT INTO tbl_users (user_id, first_name, last_name, gender, dob, address, email, phone, department, category,login, otp)
    VALUES ('$student_id', '$fname', '$lname', '$gender', '$dob', '$address', '$email', '$phone', '$department', '$category','$pass','$otp')";
    
  //  $sql = "INSERT INTO tbl_users (First_Name, Last_Name, Email, Phone, otp,department,Password) VALUES ('$firstname', '$lastname', '$email', '$mobile', '$otp','$schoolname','$password')";
    mysqli_query($conn, $sql);

}

// Function to check if mobile number is already registered
function isMobileRegistered($mobile) {
    // Replace with your database connection code
    //$conn = mysqli_connect("localhost", "username", "password", "database_name");
    include('database/config.php');
    $sql = "SELECT * FROM tbl_users WHERE Phone='$mobile'";
    $result = mysqli_query($conn, $sql);
    return (mysqli_num_rows($result) > 0);
}

function isEmailRegistered($email) {
    // Replace with your database connection code
    //$conn = mysqli_connect("localhost", "username", "password", "database_name");
    include('database/config.php');
    $sql = "SELECT * FROM tbl_users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    return (mysqli_num_rows($result) > 0);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $fname = ucwords(mysqli_real_escape_string($conn, $_POST['fname']));
    $lname = ucwords(mysqli_real_escape_string($conn, $_POST['lname']));
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $address = ucwords(mysqli_real_escape_string($conn, $_POST['address']));
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);

    $InputPassword = $_POST["InputPassword"];
    $RepeatPassword = $_POST["RepeatPassword"];
    $password=md5($InputPassword);
    
    if($InputPassword != $RepeatPassword) {
     header("Location:register.php?err=".urlencode ("The password and confirm password do not match"));
    exit();
    }
    

    if (isMobileRegistered($phone)) {
        // Mobile number is already registered, display error message
        echo "Mobile number is already registered. Please try again with a different number... Redirect to Home Page";
        header("Refresh:3; url=register.php");
        exit;
    }

    if (isEmailRegistered($email)) {
        // Mobile number is already registered, display error message
        echo "Email ID is already registered. Please try again with a different ID..Redirect to Home Page";
        header("Refresh:3; url=register.php");
        exit;
    }

    
    $otp = generateOTP();
   # $otp ="123";

   registerUser($fname,$lname,$email,$phone,$department,$category,$address,$dob,$gender,$password,$otp);


 //       header("Location: otp_verification.php?mobile=$mobile");
  //      exit;

    if (sendOTP($phone, $otp)) {
        // OTP sent successfully, redirect to OTP verification page
        header("Location: otp_verification.php?mobile=$phone");
        exit;
    } else {
        // OTP sending failed, display error message
        echo "Failed to send OTP. Please try again.";
       
    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta charset="UTF-8">
        <meta name="description" content="Agri Connect" />
        <meta name="keywords" content="Agri Connect" />
        <meta name="author" content="Keyboard Crackers" />

        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
        <link href="assets/plugins/pace-master/themes/blue/pace-theme-flash.css" rel="stylesheet"/>
        <link href="assets/plugins/uniform/css/uniform.default.min.css" rel="stylesheet"/>
        <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="assets/plugins/fontawesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
        <link href="assets/plugins/line-icons/simple-line-icons.css" rel="stylesheet" type="text/css"/>	
        <link href="assets/plugins/offcanvasmenueffects/css/menu_cornerbox.css" rel="stylesheet" type="text/css"/>	
        <link href="assets/plugins/waves/waves.min.css" rel="stylesheet" type="text/css"/>	
        <link href="assets/plugins/switchery/switchery.min.css" rel="stylesheet" type="text/css"/>
        <link href="assets/plugins/3d-bold-navigation/css/style.css" rel="stylesheet" type="text/css"/>	
        <link href="assets/images/icon.png" rel="icon">
        <link href="assets/css/modern.min.css" rel="stylesheet" type="text/css"/>
        <link href="assets/css/themes/green.css" class="theme-color" rel="stylesheet" type="text/css"/>
        <link href="assets/css/custom.css" rel="stylesheet" type="text/css"/>
         <link href="assets/css/snack.css" rel="stylesheet" type="text/css"/>
        <script src="assets/plugins/3d-bold-navigation/js/modernizr.js"></script>
        <script src="assets/plugins/offcanvasmenueffects/js/snap.svg-min.js"></script>
    
    <style>
        .error {
    font-size:1.5em;
    color: #483333;
    padding: 10px;
    background: #ffbcbc;
    border: #efb0b0 1px solid;
    border-radius: 3px;
    margin: 0 auto;
    margin-bottom: 20px;
    width: 350px;
    display:none;
    box-sizing: border-box;
}
    </style>

</head>

<body <?php if ($ms == "1") { print 'onload="myFunction()"'; } ?>  class="page-login">
    
    <div class="container">
        
        

    <div class="page-inner">
            <img src="Agri.png" alt="" height="100" width="250">
                <div id="main-wrapper">
                    <div class="row">

                    <div class="error"></div>
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                            </div>
                            
                            
                            	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            	    
                            	    <?php
                                    if(isset ($_GET['err']))  { ?>

                                    <div class="alert alert-danger"><?php echo $_GET['err']; ?></div>
                                    <?php } 
                                    ?>
                                                                        
                                    <div class="form-group">
                                            <label for="exampleInputEmail1">Full Name</label>
                                            <input type="text" class="form-control" placeholder="Enter Full name" name="fname" required autocomplete="off">
                                        </div>
										<div class="form-group">
                                            <label for="exampleInputEmail1">Email Address</label>
                                            <input type="email" class="form-control" placeholder="Enter email address" name="email" required autocomplete="off">
                                        </div>
										<div class="form-group">
                                            <label for="exampleInputEmail1">Phone</label>
                                            <input type="text" class="form-control" placeholder="Enter phone" name="phone" required autocomplete="off">
                                        </div>
										
									
									<div class="form-group">
                                            <label for="exampleInputEmail1">Address</label>
                                            <textarea style="resize: none;" rows="4" class="form-control" placeholder="Enter address" name="address" required autocomplete="off"></textarea>
                                     </div>
                                
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" class="form-control form-control-user"
                                            id="InputPassword" name="InputPassword" placeholder="Password" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control form-control-user"
                                            id="RepeatPassword" name="RepeatPassword" placeholder="Repeat Password" required>
                                    </div>
                                </div>
                               
				
				<input type="submit" class="btn btn-primary btn-user btn-block" value="OTP Verification">
				

                                <hr>
                                
                            </form>
                            
                            <div class="text-center">
                                <a class="small" href="index.php">Already have an account? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    	<script src="jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="verification.js"></script>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>