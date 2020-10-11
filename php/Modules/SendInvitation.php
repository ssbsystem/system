<?php

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once('../PHPMailer/PHPMailer.php');
require_once('../PHPMailer/SMTP.php');
require_once('../PHPMailer/Exception.php');

class SendEmail
{
	function __construct()
	{
		require_once('Modules/Connect.php');
		$PDOConnect = new PDOConnect();
		$this->pdo = $PDOConnect->pdo;
	}

	function Create($data){
	    $employeeId = $data['EmployeeId'];
		$query = "SELECT 
				c_200_id,
				c_8 AS FirstName,
				c_74 AS LastName,
				c_75 AS UserPassword,
				c_76 AS Email,
				c_77 AS ActivationCode,
				c_78 AS VerificationStatus
			FROM t_200 
			WHERE c_200_id = :employeeId";

		$statement = $this->pdo->prepare($query);
		$statement->execute(
		array(
			':employeeId'	=>	$employeeId
		)
		);


		foreach ($statement as $result) {
			$userFName = $result['FirstName']; //Emailbe megszólításhoz
			$userEmail = $result['Email'];
		}

		$query = "SELECT 
				c_200_id,
				c_8 AS FirstName,
				c_74 AS LastName,
				c_75 AS UserPassword,
				c_76 AS Email,
				c_77 AS ActivationCode,
				c_78 AS VerificationStatus
			FROM t_200 
			WHERE c_76 = :useremail";

		$statement = $this->pdo->prepare($query);
		$statement->execute(
			array(
				':useremail'	=>	$userEmail
			)
		);

		$no_of_row = $statement->rowCount();

		if ($no_of_row > 1) {
			$main_data['Message'] = "Invitation failed: email already exists!";
			$main_data['Result'] = 'E';
		} else {
			$user_actcode_seed = rand(100000,999999);
			$user_activation_code = hash('sha256', $user_actcode_seed);

			$insert_query = "
			UPDATE t_200
			SET c_77 = :user_activation_code, c_78=0
			WHERE c_76 = :user_email
			";

			$statement = $this->pdo->prepare($insert_query);
	
			$statement->execute(
				array(
					':user_email'			=>	$userEmail,
					':user_activation_code'	=>	$user_activation_code
				)
			);

			
			$newPassword = TRUE;
			$host = $_SERVER['HTTP_HOST'];
			$invitationLink = "https://$host/login.php?act_code=$user_activation_code&new_pass=$newPassword";
			if ($host=="localhost") {
				$main_data['Message'] = "Would send invitation for $userEmail link: $invitationLink";
				$main_data['Result'] = 'S';
			}else {

				/* EMAIL KÜLDÉS MAJD A SZERVEREN (phpMailer be kell húzni még)  */       	;
				require_once('php/EmailTemplates.php');
				$emailTemplates = new EmailTemplates();
				$mail_body = $emailTemplates->invitationHU($userFName, $invitationLink, $userEmail);

				$mail = new PHPMailer;

				$mail->CharSet = 'UTF-8';
				$mail->Encoding = 'base64';
				$mail->IsSMTP();								//Sets Mailer to send message using SMTP
				$mail->Host = 'mail.ssbsystem.com'; 		//Sets the SMTP hosts of your Email hosting, this for Godaddy
				$mail->Port = 465;								//Sets the default SMTP server port
				$mail->SMTPAuth = true;							//Sets SMTP authentication. Utilizes the Username and Password variables
				$mail->Username = 'info@ssbsystem.com';					//Sets SMTP username
				$mail->Password = 'Fradi2020BLGyőztes';					//Sets SMTP password
				$mail->SMTPSecure = 'ssl';							//Sets connection prefix. Options are "", "ssl" or "tls"
				$mail->setFrom('info@ssbsystem.com', 'SSB System');
				$mail->addAddress($data->user_email, $data->user_name);		//Adds a "To" address			
				$mail->WordWrap = 50;							//Sets word wrapping on the body of the message to a given number of characters
				$mail->IsHTML(true);							//Sets message type to HTML				
				$mail->Subject = 'Meghívó';			//Sets the Subject of the message
				$mail->Body = $mail_body;							//An HTML or plain text message body
				if($mail->Send())								//Send an Email. Return true on success or false on error
				{
					$main_data['Message'] = "Sending invitation for " . $userEmail;
					$main_data['Result'] = 'S';
				}else{
					$main_data['Message'] = "Invitation failed: cannot send email!";
					$main_data['Result'] = 'E';
				}
			}
		}

		return($main_data);
	}
}