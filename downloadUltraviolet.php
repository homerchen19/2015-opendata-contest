<?php
	ignore_user_abort(); //即使Client断开(如关掉浏览器)，PHP脚本也可以继续执行.
	set_time_limit(0); // 执行时间为无限制，php默认的执行时间是30秒，通过set_time_limit(0)可以让程序无限制的执行下去
	$interval=60*60; // 每隔5分钟运行
	do{
		$url = 'http://data.gov.tw/iisi/logaccess?dataUrl=http://opendata.epa.gov.tw/ws/Data/UV/?format=csv&ndctype=CSV&ndcnid=6076';
        $source = file_get_contents($url);
        file_put_contents('Ultraviolet.csv', $source);
        sleep($interval); // 等待5分钟
	}while(true);
?>
