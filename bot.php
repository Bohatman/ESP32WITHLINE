

<?php
////
///
// แบบใหม่ 11/7/2018
include 'database.php'; 
$accessToken = "<YOUR TOKEN>";
$content = file_get_contents('php://input');
$arrayHeader = array();
$arrayHeader[] = "Content-Type: application/json";
$arrayHeader[] = "Authorization: Bearer {$accessToken}";
$arrayJson = json_decode($content, true);
/**/
$fp = fopen('results.json', 'w');
fwrite($fp, json_encode($arrayJson));
fclose($fp);
/**/
$message = $arrayJson['events'][0]['message']['text'];
$messagedate = $arrayJson['events'][0]['postback']['params']['date'];


if($_POST['message']=="A54S89EF5"){
    $date = date("Y-m-d h:i:s");
    $date2 = date("Y_m_d_h_i_s");
    $url = "https://sputt.me/ln/uploads/".$date2.".jpg";//1
    echo "URL : ".$url."<br />";
    $uploaddir = realpath('./uploads') . '/';
    $uploadfile = $uploaddir .$date2.".jpg";
    echo $uploadfile;
    echo '<pre>';	
        if (move_uploaded_file($_FILES['imageFile']['tmp_name'], $uploadfile)) {
            echo "File is valid, and was successfully uploaded.\n";
        } else {
            echo "Possible file upload attack!\n";
        }
        echo 'Here is some more debugging info:';
        print_r($_FILES);
        echo "\n<hr />\n";
        print_r($_POST);
    print "</pr" . "e>\n"; //2
    $sql = "INSERT INTO `imagedata` (`Date`, `URL`) 
            VALUES (\"".$date."\", \"".$url."\")";
    if ($link->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $link->error;
    }
/*$content = file_get_contents("./template/TemplatePictureBox.json");
$ContentArray = json_decode($content, true);*/
    $sql = "select * from imagedata where URL = \"".$url."\""; 
    $query = $link->query($sql);
    $row = $query->fetch_assoc();
    $picId = $row['ID'];

    $content = file_get_contents("./template/TemplatePictureBox.json");
    $ContentArray = json_decode($content, true);  
    $sql = "select * from user_info where notification = 1"; 
    $query = $link->query($sql);    
while($row = $query->fetch_assoc()){
    $ContentArray['to'] = $row['UserToken'];
    $ContentArray["messages"][0] = singlePicBox($url,$url,$picId,$date,"ลบ ".$picId);
    pushMsg($arrayHeader,$ContentArray);
}
}
//upload image section

//
/* แบบเก่า
//ติดต่อdatabase
$hostname = "localhost";  
$database = "sputtme_minipro";  
$username = "sputtme_minipro";  
$password = "123456";  
$conn = mysql_connect($hostname, $username, $password);
if(!$conn){
    die("Connection failed: " . mysql_connect_error());
}
echo "Connected successfully";

mysql_query("use $database");
*/  
//Messaging API


//กรองข้อความ
list($word,$date,$time)=explode(" ",$message);

//แจ้งเตือน
if($word == "เปิดการแจ้งเตือน"){
    $sql = 'select * from user_info where UserToken = "'.$arrayJson['events'][0]['source']['userId'].'"';
    $query = $link->query($sql);

    if($query->num_rows == 0){
        $sql = 'INSERT INTO user_info (UserToken, notification) VALUES ("'.$arrayJson['events'][0]['source']['userId'].'", 1)';
        if($query = $link->query($sql)){
            $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken']; 
            $arrayPostData['messages'][0]['type'] = "text"; 
            $arrayPostData['messages'][0]['text'] = "เปิดแจ้งเตือนเรียบร้อยแล้ว";       
            replyMsg($arrayHeader,$arrayPostData);
        }
        else{
            $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken']; 
            $arrayPostData['messages'][0]['type'] = "text"; 
            $arrayPostData['messages'][0]['text'] = "[ERROR :503/01]เกิดข้อผิดพลาด";       
            replyMsg($arrayHeader,$arrayPostData);
        }
    }
    else{
        $sql = 'UPDATE user_info SET notification=1 WHERE UserToken="'.$arrayJson['events'][0]['source']['userId'].'"';
        $query = $link->query($sql);
        $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken']; 
        $arrayPostData['messages'][0]['type'] = "text"; 
        $arrayPostData['messages'][0]['text'] = "เปิดแจ้งเตือนเรียบร้อยแล้ว";       
        replyMsg($arrayHeader,$arrayPostData);
    }
 }
 else if($word == "ปิดการแจ้งเตือน"){
    $sql = 'UPDATE user_info SET notification=0 WHERE UserToken="'.$arrayJson['events'][0]['source']['userId'].'"';
    if($query = $link->query($sql)){
    $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken']; 
    $arrayPostData['messages'][0]['type'] = "text"; 
    $arrayPostData['messages'][0]['text'] = "ปิดแจ้งเตือนเรียบร้อยแล้ว";       
    replyMsg($arrayHeader,$arrayPostData);
    }
    else{
        $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken']; 
        $arrayPostData['messages'][0]['type'] = "text"; 
        $arrayPostData['messages'][0]['text'] = "[ERROR :503/02]เกิดข้อผิดพลาดหรือไม่มีชื่อในระบบ";       
        replyMsg($arrayHeader,$arrayPostData);
    }
 }
else if($word == "ถ่ายรูป"){
  /*  $arrayPostData2['to'] = $arrayJson['events'][0]['source']['userId'];
    $arrayPostData2['messages'][0]['type'] = "text";
    $arrayPostData2['messages'][0]['text'] = "https://sputt.me/ln/firebase/value.php";
    $arrayPostData2['messages'][1]['type'] = "sticker";
    $arrayPostData2['messages'][1]['packageId'] = "2";
    $arrayPostData2['messages'][1]['stickerId'] = "153";
*/
        $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];    
        $arrayPostData['messages'][0]['type'] = "flex";
        $arrayPostData['messages'][0]['altText'] = "this is a Flex template";     
        $arrayPostData['messages'][0]['contents']['type'] = "bubble"; 
        $arrayPostData['messages'][0]['contents']['body']['type'] = "box";     
        $arrayPostData['messages'][0]['contents']['body']['layout'] = "vertical";
        $arrayPostData['messages'][0]['contents']['body']['contents'][0]['type'] = "button";
        $arrayPostData['messages'][0]['contents']['body']['contents'][0]['style'] = "primary";
        $arrayPostData['messages'][0]['contents']['body']['contents'][0]['height'] = "sm";
        $arrayPostData['messages'][0]['contents']['body']['contents'][0]['action']['type'] = "uri";
        $arrayPostData['messages'][0]['contents']['body']['contents'][0]['action']['label'] = "ถ่ายรูป";
        $arrayPostData['messages'][0]['contents']['body']['contents'][0]['action']['uri'] = "https://sputt.me/ln/value.php";
        replyMsg($arrayHeader,$arrayPostData); 
            
    pushMsg($arrayHeader,$arrayPostData);
}
#ดูรูป ใส่วันเวลา
else if($word == "ดูรูป"){
    $content = file_get_contents("./template/TemplateSet.json");
    $ContentArray = json_decode($content, true);
    $sql = "select * from imagedata where Date like '".$date."%'";
    $query = $link->query($sql);
    $coutdata = $query->num_rows;
    $ContentArray['to'] = $arrayJson['events'][0]['source']['userId']; 
    $cout = 0;
    if ($query->num_rows == 0) {
        $arrayPostData2['to'] = $arrayJson['events'][0]['source']['userId'];
        $arrayPostData2['messages'][0]['type'] = "text";
        $arrayPostData2['messages'][0]['text'] = "[ERROR :404/01]\nไม่มีข้อมูล/พิมพ์รูปแบบคำสั่งไม่ถูกต้อง";
        $arrayPostData2['messages'][1]['type'] = "sticker";
        $arrayPostData2['messages'][1]['packageId'] = "2";
        $arrayPostData2['messages'][1]['stickerId'] = "153";

        pushMsg($arrayHeader,$arrayPostData2);
    }
    else if($coutdata <= 5){
        $sql = "select * from imagedata where Date like '".$date."%' LIMIT 5";
        $query = $link->query($sql);
        while($row = $query->fetch_assoc()){
            $ContentArray['messages'][0]['contents']['contents'][$cout++] = setPicBox($row['URL'],$row['URL'],$row['ID'],$row['Date'],"ลบ ".$row['ID']);
        }
        pushMsg($arrayHeader,$ContentArray);
    }
    else if($coutdata > 5){
        $sql = "select * from imagedata where Date like '".$date."%' LIMIT 4";
        $query = $link->query($sql);
        while($row = $query->fetch_assoc()){
            $ContentArray['messages'][0]['contents']['contents'][$cout++] = setPicBox($row['URL'],$row['URL'],$row['ID'],$row['Date'],"ลบ ".$row['ID']);
            }
            $ContentArray['messages'][0]['contents']['contents'][4] = seeMore("ถัดไป ".$date." 4");
            pushMsg($arrayHeader,$ContentArray);
        }
    }

#ดูรูปวันนี้
    else if($word == "ดูรูปวันนี้"){
        $date=date("Y-m-d");
        $content = file_get_contents("./template/TemplateSet.json");
        $ContentArray = json_decode($content, true);
        $sql = "select * from imagedata where Date like '".$date."%'";
        $query = $link->query($sql);
        $coutdata = $query->num_rows;
        $ContentArray['to'] = $arrayJson['events'][0]['source']['userId']; 
        $cout = 0;
        if ($query->num_rows == 0) {
            $arrayPostData2['to'] = $arrayJson['events'][0]['source']['userId'];
            $arrayPostData2['messages'][0]['type'] = "text";
            $arrayPostData2['messages'][0]['text'] = "[ERROR :404/01]\nไม่มีข้อมูล/พิมพ์รูปแบบคำสั่งไม่ถูกต้อง";
            $arrayPostData2['messages'][1]['type'] = "sticker";
            $arrayPostData2['messages'][1]['packageId'] = "2";
            $arrayPostData2['messages'][1]['stickerId'] = "153";
    
            pushMsg($arrayHeader,$arrayPostData2);
        }
        else if($coutdata <= 5){
            $sql = "select * from imagedata where Date like '".$date."%' LIMIT 5";
            $query = $link->query($sql);
            while($row = $query->fetch_assoc()){
                $ContentArray['messages'][0]['contents']['contents'][$cout++] = setPicBox($row['URL'],$row['URL'],$row['ID'],$row['Date'],"ลบ ".$row['ID']);
            }
            pushMsg($arrayHeader,$ContentArray);
        }
        else if($coutdata > 5){
            $sql = "select * from imagedata where Date like '".$date."%' LIMIT 4";
            $query = $link->query($sql);
            while($row = $query->fetch_assoc()){
                $ContentArray['messages'][0]['contents']['contents'][$cout++] = setPicBox($row['URL'],$row['URL'],$row['ID'],$row['Date'],"ลบ ".$row['ID']);
                }
                $ContentArray['messages'][0]['contents']['contents'][4] = seeMore("ถัดไป ".$date." 4");
                pushMsg($arrayHeader,$ContentArray);
            }
        }

#ดูรูปถัดไป
else if($word == "ถัดไป"){
    $content = file_get_contents("./template/TemplateSet.json");
    $ContentArray = json_decode($content, true);
    $sql = "select * from imagedata where Date like '".$date."%' LIMIT $time,6";
    $query = $link->query($sql);
    $coutdata = $query->num_rows;
    $ContentArray['to'] = $arrayJson['events'][0]['source']['userId']; 
    $cout = 0;
    if($coutdata <= 5){
        while($row = $query->fetch_assoc()){
            $ContentArray['messages'][0]['contents']['contents'][$cout++] = setPicBox($row['URL'],$row['URL'],$row['ID'],$row['Date'],"ลบ ".$row['ID']);
        }
        pushMsg($arrayHeader,$ContentArray);
    }
    else if($coutdata > 5){
        $sql = "select * from imagedata where Date like '".$date."%' LIMIT $time,4";
        $query = $link->query($sql);
        while($row = $query->fetch_assoc()){
            $ContentArray['messages'][0]['contents']['contents'][$cout++] = setPicBox($row['URL'],$row['URL'],$row['ID'],$row['Date'],"ลบ ".$row['ID']);
            }
            $time += $cout;
            $ContentArray['messages'][0]['contents']['contents'][4] = seeMore("ถัดไป ".$date." ".$time);
            pushMsg($arrayHeader,$ContentArray);
        }
}

#ดูรายการ ใส่วัน
    else if($word == "ดูรายการ"){
        if(!isset($date)){
            $d=date("Y-m-d");
        }
    //emo
    $code = '100071'; 
    $bin = hex2bin(str_repeat('0', 8 - strlen($code)) . $code); 
    $emoticon = mb_convert_encoding($bin, 'UTF-8', 'UTF-32BE');
    $code = '100041'; 
    $bin = hex2bin(str_repeat('0', 8 - strlen($code)) . $code); 
    $emoticon2 = mb_convert_encoding($bin, 'UTF-8', 'UTF-32BE');
    //
    $sql = "select * from imagedata where Date like '".$date."%'";
    $query = $link->query($sql);   
    if($query->num_rows > 0){
        while($row = $query->fetch_assoc()){
            $fullword .= $emoticon2."รูปภาพที่:".$row['ID']."\n".$emoticon."วันที่ : ".$row['Date']."\n";
        }
    }      
        else{
            $fullword ="ไม่พบรายการ";
        }
    $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];    
    $arrayPostData['messages'][0]['type'] = "text";
    $arrayPostData['messages'][0]['text'] = $fullword;    
    replyMsg($arrayHeader,$arrayPostData);
    }

#ดูรายการวันนี้วันปัจจุบัน
else if($word == "ดูรายการวันนี้"){
        $date=date("Y-m-d");
    //emo
    $code = '100071'; 
    $bin = hex2bin(str_repeat('0', 8 - strlen($code)) . $code); 
    $emoticon = mb_convert_encoding($bin, 'UTF-8', 'UTF-32BE');
    $code = '100041'; 
    $bin = hex2bin(str_repeat('0', 8 - strlen($code)) . $code); 
    $emoticon2 = mb_convert_encoding($bin, 'UTF-8', 'UTF-32BE');
    //
    $sql = "select * from imagedata where Date like '".$date."%'";
    $query = $link->query($sql);   
    if($query->num_rows > 0){
        while($row = $query->fetch_assoc()){
            $fullword .= $emoticon2."รูปภาพที่:".$row['ID']."\n".$emoticon."วันที่ : ".$row['Date']."\n";
        }
    }      
        else{
            $fullword ="ไม่พบรายการ";
        }
    $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];    
    $arrayPostData['messages'][0]['type'] = "text";  
    $arrayPostData['messages'][0]['text'] = $fullword;
/*    
    $arrayPostData['messages'][0]['quickReply']['items'][0]['type'] = 'action'; 
    $arrayPostData['messages'][0]['quickReply']['items'][0]['action']['type'] = 'cameraRoll'; 
    $arrayPostData['messages'][0]['quickReply']['items'][0]['action']['label'] = 'cameraRoll';
*/
    replyMsg($arrayHeader,$arrayPostData);
}

else if($word == "Time"){
    $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
    $arrayPostData['messages'][0]['type'] = "text";
    $arrayPostData['messages'][0]['text'] = "WEW";
    $arrayPostData['messages'][0]['quickReply']['items'][0]['type'] = 'action'; 
    $arrayPostData['messages'][0]['quickReply']['items'][0]['action']['type'] = 'datetimepicker'; 
    $arrayPostData['messages'][0]['quickReply']['items'][0]['action']['label'] = 'Select date';
    $arrayPostData['messages'][0]['quickReply']['items'][0]['action']['data'] = 'storeId=12345';
    $arrayPostData['messages'][0]['quickReply']['items'][0]['action']['mode'] = 'date';
    $arrayPostData['messages'][0]['quickReply']['items'][0]['action']['initial'] = '2018-09-11';
    $arrayPostData['messages'][0]['quickReply']['items'][0]['action']['max'] = '2018-12-31';
    $arrayPostData['messages'][0]['quickReply']['items'][0]['action']['min'] = '2018-01-01';
    replyMsg($arrayHeader,$arrayPostData);
}
    
#เลือกลบ
    else if($word == "เลือกลบรูปภาพ"){
        //emo
        $code = '10000B'; 
        $bin = hex2bin(str_repeat('0', 8 - strlen($code)) . $code); 
        $emoticon = mb_convert_encoding($bin, 'UTF-8', 'UTF-32BE');
        //
        $sql0 = "select * from imagedata";
        $query0 = $link->query($sql0);
        $a=$query0->num_rows;

        $sql = "select * from imagedata LIMIT 12";
        $query = $link->query($sql);
        
         $cout = 0;
         $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
         $arrayPostData['messages'][0]['type'] = "text";
         $arrayPostData['messages'][0]['text'] = "เลือกลบจากรายการด้านล่างได้เลย".$emoticon;
         
            while($row = $query->fetch_assoc()){
                /*
        $arrayPostData['messages'][0]['type'] = "image";
        $arrayPostData['messages'][0]['originalContentUrl'] = $show[2];
        $arrayPostData['messages'][0]['previewImageUrl'] = $show[2]; */
        
        $arrayPostData['messages'][0]['quickReply']['items'][$cout]['type'] = 'action'; 
        $arrayPostData['messages'][0]['quickReply']['items'][$cout]['action']['type'] = 'message'; 
        $arrayPostData['messages'][0]['quickReply']['items'][$cout]['action']['label'] = $row['ID'];
        $arrayPostData['messages'][0]['quickReply']['items'][$cout]['action']['text'] = "ลบ ".$row['ID'];
        $cout++;
                /*$arrayPostData['messages'][1]['type'] = "text";
        $arrayPostData['messages'][1]['text'] = "ต้องการลบใช่หรือไม่?";*/
       // }
            if($cout == 12){
            $a=$a-12;
            $arrayPostData['messages'][0]['quickReply']['items'][$cout]['type'] = 'action'; 
            $arrayPostData['messages'][0]['quickReply']['items'][$cout]['action']['type'] = 'message'; 
            $arrayPostData['messages'][0]['quickReply']['items'][$cout]['action']['label'] = "ถัดไปรายการที่ 13";
            $arrayPostData['messages'][0]['quickReply']['items'][$cout]['action']['text'] = "ถัดไปรายการที่ 13 ".$a; 
            
            } 
        }
        replyMsg($arrayHeader,$arrayPostData);
        
    }

#ถัดไป
else if($word == "ถัดไปรายการที่"){
    $time=$time-12;
    $date=$date-1;
    $arrayPostData3['to'] = $arrayJson['events'][0]['source']['userId'];
    listCall($time,$date,$arrayHeader,$arrayPostData3,$link);

}

#ลบจริงงงงงง
    else if($word == "ยืนยันลบ"){
        $sql = "select * from imagedata where ID=".$date;
        $query = $link->query($sql);
        $row = $query->fetch_assoc();
        $del = strchr($row['URL'],"uploads");
        unlink($del);

        $sql = "delete from imagedata where ID=".$date;
        $query = $link->query($sql);
        $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
        $arrayPostData['messages'][0]['type'] = "text";
        $arrayPostData['messages'][0]['text'] = "ลบเรียบร้อย";
        $arrayPostData['messages'][1]['type'] = "sticker";
        $arrayPostData['messages'][1]['packageId'] = "1";
        $arrayPostData['messages'][1]['stickerId'] = "407";
        replyMsg($arrayHeader,$arrayPostData);
    }

#ยืนยัน เพื่อลบ
else if($word=="ลบ"){
        $sql = "select * from imagedata where ID=".$date;
        $query = $link->query($sql);
        $row = $query->fetch_assoc();
    $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];    
    $arrayPostData['messages'][0]['type'] = "template";
    $arrayPostData['messages'][0]['altText'] = "this is a confirm template";     
    $arrayPostData['messages'][0]['template']['type'] = "confirm"; 
    $arrayPostData['messages'][0]['template']['text'] = "คุณแน่ใจหรือว่าต้องการลบ?";     
    $arrayPostData['messages'][0]['template']['actions'][0]['type'] = "message";
    $arrayPostData['messages'][0]['template']['actions'][0]['label'] = "ใช่";
    $arrayPostData['messages'][0]['template']['actions'][0]['text'] = "ยืนยันลบ ".$row['ID'];
    $arrayPostData['messages'][0]['template']['actions'][1]['type'] = "message";
    $arrayPostData['messages'][0]['template']['actions'][1]['label'] = "ไม่";
    $arrayPostData['messages'][0]['template']['actions'][1]['text'] = "ยกเลิกลบ";
    replyMsg($arrayHeader,$arrayPostData); 
}
#ไม่ลบ
else if($word=="ยกเลิกลบ"){
        $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
        $arrayPostData['messages'][0]['type'] = "text";
        $arrayPostData['messages'][0]['text'] = "ยกเลิกการลบเรียบร้อย";
        $arrayPostData['messages'][1]['type'] = "sticker";
        $arrayPostData['messages'][1]['packageId'] = "1";
        $arrayPostData['messages'][1]['stickerId'] = "130";
        replyMsg($arrayHeader,$arrayPostData);
}
#วิธีใช้
    else if($word == "วิธีใช้"){
        $image_url = "https://sputt.me/ln/miniprobj/Help.jpg";
        $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
        $arrayPostData['messages'][0]['type'] = "image";
        $arrayPostData['messages'][0]['originalContentUrl'] = $image_url;
        $arrayPostData['messages'][0]['previewImageUrl'] = $image_url;
 
        replyMsg($arrayHeader,$arrayPostData);
    }
#โหมดการแจ้งเตือน
    else if($word == "โหมดการแจ้งเตือน"){
        $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];    
        $arrayPostData['messages'][0]['type'] = "template";
        $arrayPostData['messages'][0]['altText'] = "this is a confirm template";     
        $arrayPostData['messages'][0]['template']['type'] = "confirm"; 
        $arrayPostData['messages'][0]['template']['text'] = "คุณต้องการเปิด/ปิดการแจ้งเตือน?";     
        $arrayPostData['messages'][0]['template']['actions'][0]['type'] = "message";
        $arrayPostData['messages'][0]['template']['actions'][0]['label'] = "เปิด";
        $arrayPostData['messages'][0]['template']['actions'][0]['text'] = "เปิดการแจ้งเตือน";
        $arrayPostData['messages'][0]['template']['actions'][1]['type'] = "message";
        $arrayPostData['messages'][0]['template']['actions'][1]['label'] = "ปิด";
        $arrayPostData['messages'][0]['template']['actions'][1]['text'] = "ปิดการแจ้งเตือน";
        replyMsg($arrayHeader,$arrayPostData); 
    }
#เลือกวันที่
    else if($word == "เลือกวันที่"){
    $date=date('Y-m-d');
    $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
    $arrayPostData['messages'][0]['type'] = "text";
    $arrayPostData['messages'][0]['text'] = "โปรดเลือกวันที่";
    $arrayPostData['messages'][0]['quickReply']['items'][0]['type'] = 'action'; 
    $arrayPostData['messages'][0]['quickReply']['items'][0]['action']['type'] = 'datetimepicker'; 
    $arrayPostData['messages'][0]['quickReply']['items'][0]['action']['label'] = 'Select date';
    $arrayPostData['messages'][0]['quickReply']['items'][0]['action']['data'] = 'storeId=12345';
    $arrayPostData['messages'][0]['quickReply']['items'][0]['action']['mode'] = 'date';
    $arrayPostData['messages'][0]['quickReply']['items'][0]['action']['initial'] = $date;
    $arrayPostData['messages'][0]['quickReply']['items'][0]['action']['max'] = '2018-12-31';
    $arrayPostData['messages'][0]['quickReply']['items'][0]['action']['min'] = '2018-01-01';
    replyMsg($arrayHeader,$arrayPostData);
    }
#วันที่เลือกมา
    else if(isset($messagedate)){
        $date = $messagedate;
        $sql = "select * from imagedata where Date like '".$date."%'";
        $query = $link->query($sql);  
        if ($query->num_rows == 0) {
            $arrayPostData2['to'] = $arrayJson['events'][0]['source']['userId'];
            $arrayPostData2['messages'][0]['type'] = "text";
            $arrayPostData2['messages'][0]['text'] = "ไม่มีข้อมูลของ ".$date;
            $arrayPostData2['messages'][1]['type'] = "sticker";
            $arrayPostData2['messages'][1]['packageId'] = "2";
            $arrayPostData2['messages'][1]['stickerId'] = "153";   
            pushMsg($arrayHeader,$arrayPostData2);
        } 
        else{
            $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
            $arrayPostData['messages'][0]['type'] = "template";
            $arrayPostData['messages'][0]['altText'] = "this is a confirm template";     
            $arrayPostData['messages'][0]['template']['type'] = "confirm"; 
            $arrayPostData['messages'][0]['template']['text'] = "กรุณาเลือกรูปแบบคำสั่งที่ต้องการ";     
            $arrayPostData['messages'][0]['template']['actions'][0]['type'] = "message";
            $arrayPostData['messages'][0]['template']['actions'][0]['label'] = "ดูรูป";
            $arrayPostData['messages'][0]['template']['actions'][0]['text'] = "ดูรูป ".$messagedate;
            $arrayPostData['messages'][0]['template']['actions'][1]['type'] = "message";
            $arrayPostData['messages'][0]['template']['actions'][1]['label'] = "ดูรายการ";
            $arrayPostData['messages'][0]['template']['actions'][1]['text'] = "ดูรายการ ".$messagedate;       
            replyMsg($arrayHeader,$arrayPostData);     
        } 
    }

    else if($word == "Test"){
        $content2 = file_get_contents("./template/TemplatePictureBox.json");
        $ContentArray = json_decode($content2, true);
        $ContentArray['to'] = $arrayJson['events'][0]['source']['userId'];  
        $ContentArray["messages"][0] = singlePicBox("https://i.redd.it/spo5q1n66gg11.jpg","https://i.redd.it/spo5q1n66gg11.jpg","1000","99","ลบ");
        /*
        $ContentArray["messages"][0]["contents"]["hero"]["url"]  = "https://sputt.me/ln/uploads/2018_11_08_09_11_14.jpg"; // รูปที่แสดง
        $ContentArray["messages"][0]["contents"]["hero"]["action"]['uri'] = "https://sputt.me/ln/uploads/2018_11_08_09_11_14.jpg"; //กดรูปแล้วเกิดอะไร
        $ContentArray['messages'][0]["contents"]['body']['contents'][0]['contents'][0]['contents'][1]['text'] = "9999"; // ไอดีรูปภาพ
        $ContentArray['messages'][0]["contents"]['body']['contents'][0]['contents'][1]['contents'][1]['text']= "21.31"; // เวลา
        $ContentArray['messages'][0]["contents"]['footer']['contents'][0]['action']['text'] = "EIEI"; // ข้อความตอนกดปุ่มลบ

        //
        $ContentArray["messages"][1] = $ContentArray["messages"][0];
        $ContentArray["messages"][1]["contents"]["hero"]["url"]  = "https://sputt.me/ln/uploads/2018_11_08_09_11_14.jpg"; // รูปที่แสดง
        $ContentArray["messages"][1]["contents"]["hero"]["action"]['uri'] = "https://sputt.me/ln/uploads/2018_11_08_09_11_14.jpg"; //กดรูปแล้วเกิดอะไร
        $ContentArray['messages'][1]["contents"]['body']['contents'][0]['contents'][0]['contents'][1]['text'] = "9999"; // ไอดีรูปภาพ
        $ContentArray['messages'][1]["contents"]['body']['contents'][0]['contents'][1]['contents'][1]['text']= "21.31"; // เวลา
        $ContentArray['messages'][1]["contents"]['footer']['contents'][0]['action']['text'] = "EIEI"; // ข้อความตอนกดปุ่มลบ
        */
        pushMsg($arrayHeader,$ContentArray);
    }
    else if($word == "ลบรายการ"){
        $content = file_get_contents("./template/TemplateSet.json");
        $ContentArray = json_decode($content, true);

        $content = file_get_contents("./template/pBox.json");
        $ContentArray2 = json_decode($content, true);

        $sql0 = "select * from imagedata";
        $query0 = $link->query($sql0);
        $a=$query0->num_rows; //จำนวนรูปทั้งหมด

        $sql = "select * from imagedata LIMIT 4";
        $query = $link->query($sql);
        $cout = 0;
        while($row = $query->fetch_assoc()){
            $ID = $row['ID'];
            $date = $row['Date'];
            $date = strchr($date," ");
            $url = $row['URL'];
        $ContentArray['to'] = $arrayJson['events'][0]['source']['userId'];  
        $ContentArray['messages'][0]['contents']['contents'][$cout] = setPicBox($url,$url,$ID,$date,"ลบ ".$ID);
        $cout++;
           if($cout == 4){
                $ContentArray['messages'][0]['contents']['contents'][$cout] = seeMore("ถัดไปรายการ 5 ".$a);       
            }
       
        }
        pushMsg($arrayHeader,$ContentArray);
    }
    else if($word == "ถัดไปรายการ"){
    $time=$time-4;
    $date=$date-1;
    $content = file_get_contents("./template/TemplateSet.json");
        $ContentArray = json_decode($content, true);

        $content = file_get_contents("./template/pBox.json");
        $ContentArray2 = json_decode($content, true);

        $ContentArray['to'] = $arrayJson['events'][0]['source']['userId'];
    listdel($time,$date,$arrayHeader,$ContentArray,$link);
    }
    else if($word == "a"){

    }

    else{
        //emo
        $code = '100010'; 
        $bin = hex2bin(str_repeat('0', 8 - strlen($code)) . $code); 
        $emo = mb_convert_encoding($bin, 'UTF-8', 'UTF-32BE');
        $code1 = '100041'; 
        $bin1 = hex2bin(str_repeat('0', 8 - strlen($code1)) . $code1); 
        $emo1 = mb_convert_encoding($bin1, 'UTF-8', 'UTF-32BE');
        //
        $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
        $arrayPostData['messages'][0]['type'] = "text";
        $arrayPostData['messages'][0]['text'] = "กรุณากรอกคำสั่งใหม่ให้ถูกต้อง".$emo."\n".$emo1."'ดูรูป YYYY-MM-DD'\nเช่น ดูรูป 2018-10-27\n".$emo1."'ดูรายการ YYYY-MM-DD'\nเช่น ดูรูป 2018-10-27\n".$emo1."'ลบรายการ':สำหรับเลือกลบรูปภาพ";
        replyMsg($arrayHeader,$arrayPostData);
    }
function singlePicBox($pic,$url,$id,$date,$txt){
    $content = file_get_contents("./template/TemplatePictureBox.json");
    $ContentArray = json_decode($content, true);
    $ContentArray['to'] = $arrayJson['events'][0]['source']['userId'];  
    
    $ContentArray["messages"][0]["contents"]["hero"]["url"]  = $pic; // รูปที่แสดง
    $ContentArray["messages"][0]["contents"]["hero"]["action"]['uri'] = $url; //กดรูปแล้วเกิดอะไร
    $ContentArray['messages'][0]["contents"]['body']['contents'][0]['contents'][0]['contents'][1]['text'] = $id; // ไอดีรูปภาพ
    $ContentArray['messages'][0]["contents"]['body']['contents'][0]['contents'][1]['contents'][1]['text']= $date; // เวลา
    $ContentArray['messages'][0]["contents"]['footer']['contents'][0]['action']['text'] = $txt; // ข้อความตอนกดปุ่มลบ
    return $ContentArray["messages"][0];
    }
function setPicBox($pic,$url,$id,$date,$txt){
        $content = file_get_contents("./template/pBox.json");
        $ContentArray = json_decode($content, true);
        $ContentArray['messages'][0]['hero']['url'] = $pic;
        $ContentArray['messages'][0]['hero']['action']['uri'] = $url;
        $ContentArray['messages'][0]['body']['contents'][0]['contents'][0]['contents'][1]['text'] = $id;
        $ContentArray['messages'][0]['body']['contents'][0]['contents'][1]['contents'][1]['text'] = $date;
        $ContentArray['messages'][0]['footer']['contents'][0]['action']['text'] = $txt;
        return $ContentArray['messages'][0];
        }
function seeMore($txt){
        $content = file_get_contents("./template/pBox.json");
        $ContentArray = json_decode($content, true);
        $ContentArray['messages'][1]['body']['contents'][0]['action']['text'] = $txt;
        return $ContentArray['messages'][1];
        }
function replyMsg($arrayHeader,$arrayPostData){
        $strUrl = "https://api.line.me/v2/bot/message/reply";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$strUrl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);    
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($arrayPostData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close ($ch);
        // tester
        $fp = fopen('results.json', 'w');
        fwrite($fp, json_encode($arrayPostData));
        fclose($fp);
        // tester
        }
function pushMsg($arrayHeader,$arrayPostData){
    $strUrl = "https://api.line.me/v2/bot/message/push";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$strUrl);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);    
    curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($arrayPostData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close ($ch);
            }
function listCall($a,$num,$arrayHeader,$arrayPostData,$link){
 /*   
    $arrayPostData['messages'][0]['type'] = "text";
    $arrayPostData['messages'][0]['text'] = "Janeedok";
    pushMsg($arrayHeader,$arrayPostData);
*/  
    $sql = "select * from imagedata LIMIT $num,12";
    $query = $link->query($sql);
    $cout = 0;
   
    $arrayPostData['messages'][0]['type'] = "text";
    $arrayPostData['messages'][0]['text'] = "หน้าถัดไป"; 
        while($row = $query->fetch_assoc()){
        
    $arrayPostData['messages'][0]['quickReply']['items'][$cout]['type'] = 'action'; 
    $arrayPostData['messages'][0]['quickReply']['items'][$cout]['action']['type'] = 'message'; 
    $arrayPostData['messages'][0]['quickReply']['items'][$cout]['action']['label'] = $row['ID'];
    $arrayPostData['messages'][0]['quickReply']['items'][$cout]['action']['text'] = "ลบ ".$row['ID'];
    $cout++;
   
        if($cout==12 && $a!=0){
        $num = $num+13;
        $arrayPostData['messages'][0]['quickReply']['items'][$cout]['type'] = 'action'; 
        $arrayPostData['messages'][0]['quickReply']['items'][$cout]['action']['type'] = 'message'; 
        $arrayPostData['messages'][0]['quickReply']['items'][$cout]['action']['label'] = "ถัดไปรายการที่ ".$num;
        $arrayPostData['messages'][0]['quickReply']['items'][$cout]['action']['text'] = "ถัดไปรายการที่ ".$num." ".$a;         
        } 
    }
    pushMsg($arrayHeader,$arrayPostData);
}

    mysqli_close($link);
    exit;
?>





