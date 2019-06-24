<?php

require_once __DIR__ . '/../../vendor/autoload.php';

class SendingMail extends CI_Controller
{
    
    /********************************************************************************/
    /*                                                                               */
    /********************************************************************************/
    function __construct()
    {
        parent::__construct();
    }

    /********************************************************************************/
    /*                                                                               */
    /********************************************************************************/
    public function index()
    {
       
        $data['dtStart'] = '2019-06-23';
        $data['dtEnd'] = '2019-06-23';
        $data['object'] = "Joeux Noel !!!!";
        $data['body']='body of the message' ;
        $data['sendTo']="receveir@orange.fr" ;  // put your receiver
        $data['sendFrom']="sender@free.fr" ;
        $data['message']='my message' ;

        $this->SendEventCalendar($data);
    }


    /********************************************************************************/
    /*
        $data['dtStart'] = '2019-06-23';
        $data['dtEnd'] = '2019-06-23';
        $data['object'] = "Joeux Noel !!!!";  
        $data['body']= "Body of the message";    
        $data['sendTo']=['laurent.mci85@orange.fr', 'other@domain.org' => 'A name']
                                                                                 */
    /********************************************************************************/
    public function SendEventCalendar($data)
    {
        date_default_timezone_set('Europe/Berlin');

        // We create a new 'conteneur' for the event 
        $iCalString = new \Eluceo\iCal\Component\Calendar('-//Microsoft Corporation//Outlook 15.0 MIMEDIR//EN');
        // 2. Create an event
        $vEvent = new \Eluceo\iCal\Component\Event();
        $vEvent->setDtStart(new \DateTime($data['dtStart'])); // au format YYYY-MM-JJ
        $vEvent->setDtEnd(new \DateTime($data['dtEnd'])); // au format YYYY-MM-JJ
        $vEvent->setNoTime(true);
        $vEvent->setSummary($data['object']);
        // Adding Timezone (optional)
        $vEvent->setUseTimezone(true);
        // 3. Add event to calendar
        $iCalString->addComponent($vEvent);
        // 4. Set headers        
        
        //header('Content-Type: text/calendar; charset=utf-8');
        //header('Content-Disposition: attachment; filename="cal.ics"');

        // 5. Output
        $iCalStringRender =   $iCalString->render();

        $needle = "summary";
        $haystack = $iCalStringRender;
        // TODO : generate an error if not found
        $pos      = strripos($iCalStringRender, $needle);
        if ($pos === false) {
            echo "Désolé, impossible de trouver ($needle) dans ($haystack)";
        } else {
            // insert the element
            $temp = substr_replace($iCalStringRender, "ATTENDEE;PARTSTAT=ACCEPTED;RSVP=FALSE:mailto:example@domain.com\r\n", $pos, 0);
        }

        /********************** incorporation  METHOD REQUEST */
        $needle = "Begin:VEVENT";
        $haystack = $iCalStringRender;
        $pos      = strripos($iCalStringRender, $needle);
        // TODO : generate an error if not found
        if ($pos === false) {
            echo "Désolé, impossible de trouver ($needle) dans ($haystack)";
        } else {
            $temp = substr_replace($temp, "METHOD:REQUEST\r\n", $pos, 0);
        }

        // var_export($temp);
        // 
        $attachment = new Swift_Calendar($temp, Swift_Calendar::METHOD_REQUEST);

        /***********************************************************************************/
        /* Sending         */
        /***********************************************************************************/
        // Create the Transport
    
        $transport = (new Swift_SmtpTransport($this->config->item('srvSMTP'), $this->config->item('srvPort'),$this->config->item('SSL')))
            ->setUsername($this->config->item('glbUsername'))
            ->setPassword($this->config->item('glbPassword'));
        /*
            You could alternatively use a different transport such as Sendmail:

            // Sendmail
            $transport = new Swift_SendmailTransport('/usr/sbin/sendmail -bs');
            */

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Create a message
        $message = (new Swift_Message($data['object']))
            ->setFrom($data['sendFrom'])
            ->setTo($data['sendTo']) 
            ->setBody($data['body']);
        $message->attach($attachment);

        // Send the message
        $result = $mailer->send($message, $failures);
        print_r($result); //  1 : OK, 0 :NOK
    }
}
