<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    public function __construct()
    {
        helper('app');
    }
    
    public function index()
    {
        $data['records']	=  db('projects')->get()->getResult();
        return view('home',$data);
    }
    
    
    public function lang()
    {
        if(sess()->get('lang') == 'fa')
        {    
            sess()->remove('lang');
            sess()->set('lang', 'en');
        }
        else
        {
            sess()->remove('lang');
            sess()->set('lang', 'fa');
        }
        return redirect()->back();
    }
    
    public function color($val)
    {
        if($val == 'w3-dark-grey')
        {    
            session()->set('color', 'w3-light-grey');
        }
        else
        {
            session()->set('color', 'w3-dark-grey');
        }
        
        $url = base_url();
        if(isset($_SERVER['HTTP_REFERER']))
        {
            $url = $_SERVER['HTTP_REFERER'];
        }
        
        return redirect()->to($url);
    }
    
    public function add($sec=false)
    {
		if(sess()->get('pclogin'))
		{
			if($_POST)
			{
                array_pop($_POST);
                
				db($sec)->insert($_POST);
        
				session()->setFlashdata('flash', "<b class='w3-text-blue'>".lang('app.opr_done')."</b>");
				return redirect()->back();
			
			}
			else
			{
				return view($sec.'/add');
			}
		}
        else
        {
            $url = base_url();
            if(isset($_SERVER['HTTP_REFERER']))
            {
                $url = $_SERVER['HTTP_REFERER'];
            }  
        }
    }
    
    public function edit($sec='', $id='')
	{
        
		if(sess()->get('pclogin'))
		{
			if($_POST)
			{
                array_pop($_POST);
                db($sec)->set($_POST)->where('id',$id)->update();
				session()->setFlashdata('flash', "<b class='w3-text-blue'>".lang('app.opr_done')."</b>");
				return redirect()->back();
				
			}
			else
			{
				$data['record']	=  db($sec.'s')->where('id',$id)->get()->getResult();
				return view($sec.'/edit',$data);
			}
			
		}
	}

    public function hsk3()
    {
        return view('chineseteste-cncom');
    }

    public function hsk2()
    {
        return view('chineseteste-cncom2');
    }

    public function cwnu()
    {
        $senderEmail = 'info@isunp.org';
        $recipientEmail = 'zero@hamidmahzon.com';
        $subject = 'Notification';
        if(sess()->get('ename'))
        {
            $visitor = sess()->get('ename');
        }
        else
        {
            $ip = $_SERVER['REMOTE_ADDR'];
            $apiUrl = "http://ip-api.com/json/$ip";
            $response = file_get_contents($apiUrl);
            $data = json_decode($response, true);
            $visitor = 'Someone';
            if ($data['status'] == 'success') 
            {
                $country = $data['country'];
                $visitor = 'Someone from '. $country;
            }
        }
        
        $message = $visitor.' Visiting the Old App';
        $headers = "From: $senderEmail\r\n";
        $headers .= "Reply-To: $senderEmail\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $mailSent = mail($recipientEmail, $subject, $message, $headers);

        sess()->set('goto','cwnu');
        return view('cwnu/home');
    }

    public function download() {
        $file_path = FCPATH . 'assets/img/isuqr.png'; // Full server path to the file

        if (file_exists($file_path)) {
            // Set headers to initiate download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            flush(); // Flush system output buffer
            readfile($file_path); // Read the file
            exit;
        } else {
            // File not found
            show_404();
        }
    }
}
