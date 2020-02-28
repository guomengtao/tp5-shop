<?php
include 'IPQuery.php';

$ip = new IPQuery();
$addr = $ip->query('misiyu.cn');

echo "<pre>
IP起始段：{$addr['beginip']}
IP结束段：{$addr['endip']}
实际地址：{$addr['pos']}
运 营 商：{$addr['isp']}
</pre>";
