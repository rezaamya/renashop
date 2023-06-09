<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pasargad
{
    protected $CI;

    public function __construct()
    {
        // Assign the CodeIgniter super-object
        $this->CI =& get_instance();
        //$this->CI->load->library('session');
        $this->CI->load->helper('url');
        require(APPPATH . '/libraries/payment/pasargad/RSAProcessor.class.php');
        #$this->SMS_API = new \Kavenegar\KavenegarApi(KAVEHNEGAR_API_KEY);
        #$this->sender = KAVEHNEGAR_SENDER;
    }

    public function send_to_bank($merchantCode, $terminalCode, $amount, $invoiceNumber)
    {
        //$receptor should be something like this: 09193498734
        #$this->SMS_API->Send($this->sender, $receptor, $message);


        $processor = new RSAProcessor(APPPATH . '/libraries/payment/pasargad/certificate.xml', RSAKeyType::XMLFile);
        #$merchantCode = 111111; // كد پذيرنده
        #$terminalCode = 111111; // كد ترمينال
        #$amount = 1; // مبلغ فاكتور
        $redirectAddress = base_url("cart/finishing_message/pasargad");

        #$invoiceNumber = 16525; //شماره فاكتور
        $timeStamp = date("Y/m/d H:i:s");
        $invoiceDate = date("Y/m/d H:i:s"); //تاريخ فاكتور
        $action = "1003";    // 1003 : براي درخواست خريد
        $data = "#" . $merchantCode . "#" . $terminalCode . "#" . $invoiceNumber . "#" . $invoiceDate . "#" . $amount . "#" . $redirectAddress . "#" . $action . "#" . $timeStamp . "#";
        $data = sha1($data, true);
        $data = $processor->sign($data); // امضاي ديجيتال
        $result = base64_encode($data); // base64_encode

        $redirect_form = "<form Id='payment_redirect_form' Method='post' Action='https://pep.shaparak.ir/gateway.aspx'>
		<input type='hidden' name='invoiceNumber' value='$invoiceNumber' />
			<input type='hidden' name='invoiceDate' value='$invoiceDate' />
			<input type='hidden' name='amount' value='$amount' />
			<input type='hidden' name='terminalCode' value='$terminalCode' />
			<input type='hidden' name='merchantCode' value='$merchantCode' />
			<input type='hidden' name='redirectAddress' value='$redirectAddress' />
			<input type='hidden' name='timeStamp' value='$timeStamp' />
			<input type='hidden' name='action' value='$action' />
			<input type='hidden' name='sign' value='$result' />
		</form>
		";

        return $redirect_form;
    }

    public function makeXMLTree($data)
    {
        $ret = array();
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $data, $values, $tags);
        xml_parser_free($parser);
        $hash_stack = array();
        foreach ($values as $key => $val) {
            /*echo "<pre>";
            print_r($val);
            echo "</pre>";*/
            switch ($val['type']) {
                case 'open':
                    array_push($hash_stack, $val['tag']);
                    break;
                case 'close':
                    array_pop($hash_stack);
                    break;
                case 'complete':
                    array_push($hash_stack, $val['tag']);
                    // uncomment to see what this function is doing
                    // echo("\$ret['" . implode($hash_stack, "']['") . "'] = '{$val['value']}';\n");
                    eval("\$ret['" . implode($hash_stack, "']['") . "'] = '{$val['value']}';");
                    array_pop($hash_stack);
                    break;
            }
        }
        return $ret;
    }

    /* ------------------------------------- CURL POST TO HTTPS --------------------------------- */
    public function post2https($fields_arr, $url)
    {

        //url-ify the data for the POST
        $fields_string = "";
        foreach ($fields_arr as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        $fields_string = substr($fields_string, 0, -1);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields_arr));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $res = curl_exec($ch);

        //close connection
        curl_close($ch);
        return $res;
    }

    public function verify_payment($merchantCode, $terminalCode, $amount, $invoiceNumber, $invoiceDate)
    {
        require_once(APPPATH.'/libraries/payment/pasargad/RSAProcessor.class.php');

        $fields = array(
            'MerchantCode' => $merchantCode, 			//shomare ye moshtari e shoma.
            'TerminalCode' => $terminalCode, 			//shomare ye terminal e shoma.
            'InvoiceNumber' => $invoiceNumber,  			//shomare ye factor tarakonesh.
            'InvoiceDate' => $invoiceDate, //tarikh e tarakonesh.
            'amount' => $amount, 					//mablagh e tarakonesh. faghat adad.
            'TimeStamp' => date("Y/m/d H:i:s"), 	//zamane jari ye system.
            'sign' => '' 							//reshte ye ersali ye code shode. in mored automatic por mishavad.
        );

        $processor = new RSAProcessor(APPPATH . '/libraries/payment/pasargad/certificate.xml',RSAKeyType::XMLFile);

        $data = "#". $fields['MerchantCode'] ."#". $fields['TerminalCode'] ."#". $fields['InvoiceNumber'] ."#". $fields['InvoiceDate'] ."#". $fields['amount'] ."#". $fields['TimeStamp'] ."#";
        $data = sha1($data,true);
        $data =  $processor->sign($data);
        $fields['sign'] =  base64_encode($data); // base64_encode

        $sendingData =  "MerchantCode=". $merchantCode ."&TerminalCode=". $terminalCode ."&InvoiceNumber=". $invoiceNumber ."&InvoiceDate=". $invoiceDate ."&amount=". $amount ."&TimeStamp=". $fields['TimeStamp'] ."&sign=".$fields['sign'];
        $verifyresult = $this->post2https($fields,'https://pep.shaparak.ir/VerifyPayment.aspx');
        $array = $this->makeXMLTree($verifyresult);
        /*var_dump($this->);
        echo("<br /><br /><h1>");
        echo $array["resultObj"]["verifyresult"];
        echo("</h1>")*/
        return $array;
    }
}
