<?php
set_time_limit(0);
/***********************************************
* Multithreaded Proxy Checker
* Coded by Miyachung
* Janissaries.Org
* Miyachung@hotmail.com
------------------------------------------------
* Demonstration -> http://www.youtube.com/watch?v=4icPZHv3W9g
* Type list like IP:PORT in a file
***********************************************/

/*-----------------------------------------------------------------------*/
	echo "\n[+]Enter your proxy list: ";
	$proxy_list = fgets(STDIN);
	$proxy_list = str_replace("\r\n","",$proxy_list);
	$proxy_list = trim($proxy_list);

	echo "[+]Enter number of thread: ";
	$thread = fgets(STDIN);
	$thread = str_replace("\r\n","",$thread);
	$thread = trim($thread);
	echo "[+]Enter timeout sec: ";
	$timeout = fgets(STDIN);
	$timeout = str_replace("\r\n","",$timeout);
	$timeout = trim($timeout);
	echo "[+]Checking proxies\n";
	echo "-------------------------------------------------------\n";
	$open_file	=	file($proxy_list);
	$open_file  =	preg_replace("#\r\n#si","",$open_file);

		
	checker($open_file,$thread);
/*-----------------------------------------------------------------------*/
function checker($ips,$thread)
{
	global $timeout;
	
	$multi 	= curl_multi_init();
	$ips 	= array_chunk($ips,$thread);
	$total 	= 0;
	$time1  = time();
		foreach($ips as $ip)
		{
			for($i=0;$i<=count($ip)-1;$i++)
			{
			$curl[$i] = curl_init();
			curl_setopt($curl[$i],CURLOPT_RETURNTRANSFER,1);
			curl_setopt($curl[$i],CURLOPT_SSL_VERIFYHOST,0);
			curl_setopt($curl[$i],CURLOPT_SSL_VERIFYPEER,0);
			curl_setopt($curl[$i],CURLOPT_URL,"https://www.google.com.tr");
			curl_setopt($curl[$i],CURLOPT_PROXY,$ip[$i]);
			curl_setopt($curl[$i],CURLOPT_TIMEOUT,$timeout);
			curl_multi_add_handle($multi,$curl[$i]);
			}
			
			do
			{
			curl_multi_exec($multi,$active);
			usleep(11);
			}while( $active > 0 );
			
			foreach($curl as $cid => $cend)
			{
				$con[$cid] = curl_multi_getcontent($cend);
				curl_multi_remove_handle($multi,$cend);
				if(preg_match('#calendar\?tab=wc#si',$con[$cid]))
				{
					$total++;
					echo "[~]Proxy works -> ".$ip[$cid]."\n";
					save_file("works.txt",$ip[$cid]);
				}
			}
		}
	$time2 = time();
	echo "\n[+]Total working proxies: $total,checking completed\n";
	echo "[+]Elapsed time -> ".($time2-$time1)." seconds\n";
	echo "[+]Coded by miyachung || Janissaries.Org\n";
	echo "-------------------------------------------------------\n";
}
	
function save_file($file,$content)
{
	$open = fopen($file,'ab');
	fwrite($open,$content."\r\n");
	fclose($open);
}

?>