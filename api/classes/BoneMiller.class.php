<?php
include_once dirname(__FILE__).'/Autoloader.class.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/* Exception class. */
require $_SERVER['DOCUMENT_ROOT'].'/api/classes/phpMiller/src/Exception.php';

/* The main PHPMailer class. */
require $_SERVER['DOCUMENT_ROOT'].'/api/classes/phpMiller/src/PHPMailer.php';

/* SMTP class, needed if you want to use SMTP. */
require $_SERVER['DOCUMENT_ROOT'].'/api/classes/phpMiller/src/SMTP.php';

/* ... */
/**
 * *********************************************************************************************************
 * @_forProject: Shell Bone
 * @_purpose: This class handles the Epad external user emails operations. 
 * @_version Release: 1.0
 * @_created Date: 11/23/2020
 * @_author(s):
 *   --------------------------------------------------------------------------------------------------
 *   1) Fullname of engineer. (Paul Glaydor)
 *      @contact Phone: (+231) 770558804
 *      @contact Mail: conteeglaydor@gmail.com
 * *********************************************************************************************************
 */

class BoneMiller{
    public $method;
    public $url;
    function __construct()
    {
        $this->method       = $_SERVER['REQUEST_METHOD'];
        $this->url          = $_SERVER['REQUEST_URI'];
        // $this->get_unread_mails('pglaydor@tammacorp.com', 'Conett@contee@123');
        // $this->email_sender(['email' => 'conteeglaydor@gmail.com', 'subject'  =>'Test Subject', 'message' => 'This is just a test message.']);
    }

    //This method sends emails
    public function email_sender(array $details){
        $mail = new PHPMailer(TRUE);    
        try {
            // $mail->SMTPDebug  = 2;
            $mail->SMTPDebug = 0;                                  
            $mail->isSMTP();                                            
            $mail->Host       = 'mail.nem.alj.mybluehost.me';             
            $mail->SMTPAuth   = true;
            $mail->Username   = 'my-epad-email@nem.alj.mybluehost.me';
            $mail->Password   = '?USGRun4,g=o';                        
            $mail->SMTPSecure = 'ssl';              
            $mail->Port       = 465;
        
            $mail->setFrom('my-epad-email@nem.alj.mybluehost.me', 'VoteAdvisor');           
            $mail->addAddress($details['email']);
            $image         =  $_SERVER['DOCUMENT_ROOT']."/media/images/VoteAdvisorClipArt.png";
            $mail->AddEmbeddedImage($image, 'VoteAdvisorClipArt');
            $mail->isHTML(true);                                  
            $mail->Subject = $details['subject'];
            $mail->Body    = '
                    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml" lang="en">
                        <head>
                            <link rel="stylesheet" type="text/css" hs-webfonts="true" href="https://fonts.googleapis.com/css?family=Lato|Lato:i,b,bi">
                            <title>VoteAdvisor Team</title>
                            <meta property="og:title" content="VoteAdvisor Team">
                            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                            <meta http-equiv="X-UA-Compatible" content="IE=edge">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
                            <style type="text/css">
                                a{ 
                                    text-decoration: underline;
                                    color: inherit;
                                    font-weight: bold;
                                    color: #253342;
                                }
                                
                                h1 {
                                    font-size: 56px;
                                }
                                    h2{
                                    font-size: 28px;
                                    font-weight: 900; 
                                }
                            
                                p {
                                    font-weight: 100;
                                }
                                
                                td {
                                vertical-align: top;
                                }
                            
                                #email {
                                    margin: auto;
                                    width: 600px;
                                    background-color: white;
                                }
                            
                                button{
                                    font: inherit;
                                    background-color: #FF7A59;
                                    border: none;
                                    padding: 10px;
                                    text-transform: uppercase;
                                    letter-spacing: 2px;
                                    font-weight: 900; 
                                    color: white;
                                    border-radius: 5px; 
                                    box-shadow: 3px 3px #d94c53;
                                }
                                
                                .subtle-link {
                                    font-size: 9px; 
                                    text-transform:uppercase; 
                                    letter-spacing: 1px;
                                    color: #CBD6E2;
                                }
    
                                .big-logo {
                                    padding: 20px;
                                    padding-bottom: 5px; 
                                    height: 100px;
                                    width: 100px;
                                    background-position: center;
                                    background-size: contain;
                                    background-repeat: no-repeat;
                                }
                                .banner{
                                    box-shadow: rgba(0, 0, 0, 0.09) 0px 3px 12px !important;
                                    background-color: rgba(112, 179, 250, 0.286);
                                    backdrop-filter: blur( 8.0px );
                                }
                            </style>
                        </head>
    
                        <body bgcolor="#fff" style="width: 100%; margin: auto 0; padding:0; font-family:Lato, sans-serif; font-size:18px; color:#33475B; word-break:break-word">
                            <! View in Browser Link --> 
                            <div id="email">
                            <! Banner --> 
                                    <table role="presentation" width="100%">
                                        <tr>
                                    
                                        <td class="banner" bgcolor="#ffffff" align="center" style="color: dark;">
                                            <img alt="VoteAdvisor" class="big-logo" src="cid:VoteAdvisorClipArt" />
                                            <h3">VoteAdvisor</h3>
                                            <p style="padding: 0px;">Your Choice Matter</p>
                                        </td>
                                    </table>
                                <! First Row --> 
    
                                <table role="presentation" border="0" cellpadding="0" cellspacing="10px" style="padding: 30px 30px 30px 60px;">
                                    <tr>
                                        <td>
                                            <h3>'.$details["subject"].'</h3>
                                            <p>
                                                '.$details["message"].'
                                            </p>
                                        </td>
                                    </tr>
                            </table>

                <! Second Row with Two Columns-->
                <! Banner Row -->
                    <table role="presentation" class="banner" style="background-color: #2d56a3 !important; color: #ffffff;"
                        width="100%" style="margin-top: 50px;">
                        <tr>
                            <td align="center" style="padding: 30px 30px;">

                                <div class="p-0">
                                    <div class=""><a style="color: #ffffff; text-decoration: none !important " href="https://voteadvisor.com"><b>VoteAdvisor Team</b></a> ©
                                        '.gmdate('Y').'</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    </div>
                </body>
                </html>
            ';
            // $mail->AltBody = 'Body in plain text for non-HTML mail clients';
            $mail->send();
            return 200;
        // return ['status' => 200, 'message' => 'Mail has been sent successfully!'];
        } catch (Exception $e) {
            return 400;
        // return ['status' => 200, 'message' => 'Message could not be sent. Mailer Error:'.$mail->ErrorInfo];
        }
    }
}