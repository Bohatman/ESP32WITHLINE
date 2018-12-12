#include "WiFiClientSecure.h"
#include "Camera_Exp.h"
#define SENSOR 19
#define SERVER     "sputt.me"
#define PORT     443
#define BOUNDARY     "--------------------------133747188241686651551404"  
#define TIMEOUT      20000
CAMERA cam;
char ssid[] = "loveme";
char pass[] = "12su314w1";  
String sendImage(String message, uint8_t *data_pic,size_t size_pic);
String header(size_t length);
String body(String content , String message);
void detected();
//
#define FIREBASE_HOST   "https://esp32lineapi.firebaseio.com/"
#define FIREBASE_AUTH   "TONUbLADcrvjVL0XOQxj9Ff01OKLL4fmR7dfiO1g"
String TD32_Get_Firebase(String path);
int TD32_Set_Firebase(String path,String value);
const char* FIREBASE_ROOT_CA= \
        "-----BEGIN CERTIFICATE-----\n" \
        "MIIEXDCCA0SgAwIBAgINAeOpMBz8cgY4P5pTHTANBgkqhkiG9w0BAQsFADBMMSAw\n" \
        "HgYDVQQLExdHbG9iYWxTaWduIFJvb3QgQ0EgLSBSMjETMBEGA1UEChMKR2xvYmFs\n" \
        "U2lnbjETMBEGA1UEAxMKR2xvYmFsU2lnbjAeFw0xNzA2MTUwMDAwNDJaFw0yMTEy\n" \
        "MTUwMDAwNDJaMFQxCzAJBgNVBAYTAlVTMR4wHAYDVQQKExVHb29nbGUgVHJ1c3Qg\n" \
        "U2VydmljZXMxJTAjBgNVBAMTHEdvb2dsZSBJbnRlcm5ldCBBdXRob3JpdHkgRzMw\n" \
        "ggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDKUkvqHv/OJGuo2nIYaNVW\n" \
        "XQ5IWi01CXZaz6TIHLGp/lOJ+600/4hbn7vn6AAB3DVzdQOts7G5pH0rJnnOFUAK\n" \
        "71G4nzKMfHCGUksW/mona+Y2emJQ2N+aicwJKetPKRSIgAuPOB6Aahh8Hb2XO3h9\n" \
        "RUk2T0HNouB2VzxoMXlkyW7XUR5mw6JkLHnA52XDVoRTWkNty5oCINLvGmnRsJ1z\n" \
        "ouAqYGVQMc/7sy+/EYhALrVJEA8KbtyX+r8snwU5C1hUrwaW6MWOARa8qBpNQcWT\n" \
        "kaIeoYvy/sGIJEmjR0vFEwHdp1cSaWIr6/4g72n7OqXwfinu7ZYW97EfoOSQJeAz\n" \
        "AgMBAAGjggEzMIIBLzAOBgNVHQ8BAf8EBAMCAYYwHQYDVR0lBBYwFAYIKwYBBQUH\n" \
        "AwEGCCsGAQUFBwMCMBIGA1UdEwEB/wQIMAYBAf8CAQAwHQYDVR0OBBYEFHfCuFCa\n" \
        "Z3Z2sS3ChtCDoH6mfrpLMB8GA1UdIwQYMBaAFJviB1dnHB7AagbeWbSaLd/cGYYu\n" \
        "MDUGCCsGAQUFBwEBBCkwJzAlBggrBgEFBQcwAYYZaHR0cDovL29jc3AucGtpLmdv\n" \
        "b2cvZ3NyMjAyBgNVHR8EKzApMCegJaAjhiFodHRwOi8vY3JsLnBraS5nb29nL2dz\n" \
        "cjIvZ3NyMi5jcmwwPwYDVR0gBDgwNjA0BgZngQwBAgIwKjAoBggrBgEFBQcCARYc\n" \
        "aHR0cHM6Ly9wa2kuZ29vZy9yZXBvc2l0b3J5LzANBgkqhkiG9w0BAQsFAAOCAQEA\n" \
        "HLeJluRT7bvs26gyAZ8so81trUISd7O45skDUmAge1cnxhG1P2cNmSxbWsoiCt2e\n" \
        "ux9LSD+PAj2LIYRFHW31/6xoic1k4tbWXkDCjir37xTTNqRAMPUyFRWSdvt+nlPq\n" \
        "wnb8Oa2I/maSJukcxDjNSfpDh/Bd1lZNgdd/8cLdsE3+wypufJ9uXO1iQpnh9zbu\n" \
        "FIwsIONGl1p3A8CgxkqI/UAih3JaGOqcpcdaCIzkBaR9uYQ1X4k2Vg5APRLouzVy\n" \
        "7a8IVk6wuy6pm+T7HT4LY8ibS5FEZlfAFLSW8NwsVz9SBK2Vqn1N0PIMn5xA6NZV\n" \
        "c7o835DLAFshEWfC7TIe3g==\n" \
        "-----END CERTIFICATE-----\n";
//
void setup() 
{
 pinMode(SENSOR,INPUT);
  Serial.begin(115200);
  Serial.println("\r\nSetting up please wait.");
  cam.setFrameSize(CAMERA_FS_QVGA);
  esp_err_t err = cam.init();
  if (err != ESP_OK)
  {
    Serial.println("Camera init failed with error =" + String( err));
    return;
  }
  WiFi.begin(ssid, pass);
  unsigned char led_cnt=0;
  while (WiFi.status() != WL_CONNECTED) 
  {
     delay(500);
     Serial.print(".");
  }
  Serial.println("");
  Serial.println("WiFi connected.");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());
  xTaskCreatePinnedToCore(Thread2,"Thread2", 6400,NULL,1,NULL,0);
}

void loop() 
{
    while(!digitalRead(SENSOR));
      detected();
    while(digitalRead(SENSOR));
}
void Thread2(void *p) 
{
  while(1){
        if(TD32_Get_Firebase("capture") == "1"){
            String res;
      Serial.println("Send Picture");
      esp_err_t err;
      err = cam.capture();
      if (err == ESP_OK)
      {
        res = sendImage("A54S89EF5",cam.getfb(),cam.getSize());
        Serial.println(res);
      }
      else
        Serial.println("Camera Error");
        TD32_Set_Firebase("capture", "0");
    }
  }
  }
  
void detected(){
    String res;
      Serial.println("Send Picture");
      esp_err_t err;
      err = cam.capture();
      if (err == ESP_OK)
      {
        res = sendImage("A54S89EF5",cam.getfb(),cam.getSize());
        Serial.println(res);
      }
      else
        Serial.println("Camera Error");
      
        
  }
//////

String sendImage(String message, uint8_t *data_pic,size_t size_pic)
{
  String bodyTxt =  body("message",message);
  String bodyPic =  body("imageFile",message);
  String bodyEnd =  String("--")+BOUNDARY+String("--\r\n");
  size_t allLen = bodyTxt.length()+bodyPic.length()+size_pic+bodyEnd.length();
  String headerTxt =  header(allLen);
  WiFiClientSecure client;
   if (!client.connect(SERVER,PORT)) 
   {
    return("connection failed");   
   }
   
   client.print(headerTxt+bodyTxt+bodyPic);
   client.write(data_pic,size_pic);
   client.print("\r\n"+bodyEnd);
   
   delay(20);
   long tOut = millis() + TIMEOUT;
   while(client.connected() && tOut > millis()) 
   {
    if (client.available()) 
    {
      String serverRes = client.readStringUntil('\r');
        return(serverRes);
    }
   }
}
String header(size_t length)
{
  String  data;
      data =  F("POST /ln/bot.php HTTP/1.1\r\n");
      data += F("cache-control: no-cache\r\n");
      data += F("Content-Type: multipart/form-data; boundary=");
      data += BOUNDARY;
      data += "\r\n";
      data += F("User-Agent: PostmanRuntime/6.4.1\r\n");
      data += F("Accept: */*\r\n");
      data += F("Host: ");
      data += SERVER;
      data += F("\r\n");
      data += F("accept-encoding: gzip, deflate\r\n");
      data += F("Connection: keep-alive\r\n");
      data += F("content-length: ");
      data += String(length);
      data += "\r\n";
      data += "\r\n";
    return(data);
}
String body(String content , String message)
{
  String data;
  data = "--";
  data += BOUNDARY;
  data += F("\r\n");
  if(content=="imageFile")
  {
    data += F("Content-Disposition: form-data; name=\"imageFile\"; filename=\"picture.jpg\"\r\n");
    data += F("Content-Type: image/jpeg\r\n");
    data += F("\r\n");
  }
  else
  {
    data += "Content-Disposition: form-data; name=\"" + content +"\"\r\n";
    data += "\r\n";
    data += message;
    data += "\r\n";
  }
   return(data);
}
int TD32_Set_Firebase(String path,String value) {
  WiFiClientSecure ssl_client;
  String host = String(FIREBASE_HOST); host.replace("https://", "");
  if(host[host.length()-1] == '/' ) host = host.substring(0,host.length()-1);
  String resp = "";
  int httpCode = 404; // Not Found

  String firebase_method = "PUT ";
  ssl_client.setCACert(FIREBASE_ROOT_CA);
  if( ssl_client.connect( host.c_str(), 443)){
    String uri = ((path[0]!='/')? String("/"):String("")) + path + String(".json?auth=") + String(FIREBASE_AUTH); 
         Serial.println(uri);
    String request = "";
          request +=  firebase_method + uri +" HTTP/1.1\r\n";
          request += "Host: " + host + "\r\n";
          request += "User-Agent: TD_ESP32\r\n";
          request += "Connection: close\r\n";
          request += "Accept-Encoding: identity;q=1,chunked;q=0.1,*;q=0\r\n";
          request += "Content-Length: "+String( value.length())+"\r\n\r\n";
          request += value;
    Serial.println(request);
    ssl_client.print(request);
    while( ssl_client.connected() && !ssl_client.available()) delay(10);
    if( ssl_client.connected() && ssl_client.available() ) {
      resp      = ssl_client.readStringUntil('\n');
      httpCode  = resp.substring(resp.indexOf(" ")+1, resp.indexOf(" ", resp.indexOf(" ")+1)).toInt();
    }
    ssl_client.stop();    
  }
  else {
    Serial.println("[Firebase] can't connect to Firebase Host");
  }
  return httpCode;
}
String TD32_Get_Firebase(String path ) {
  WiFiClientSecure ssl_client;
  String host = String(FIREBASE_HOST); host.replace("https://", "");
  if(host[host.length()-1] == '/' ) host = host.substring(0,host.length()-1);
  String resp = "";
  String value = "";
  ssl_client.setCACert(FIREBASE_ROOT_CA);
  if( ssl_client.connect( host.c_str(), 443)){
    String uri = ((path[0]!='/')? String("/"):String("")) + path + String(".json?auth=") + String(FIREBASE_AUTH);      
    String request = "";
          request += "GET "+ uri +" HTTP/1.1\r\n";
          request += "Host: " + host + "\r\n";
          request += "User-Agent: TD_ESP32\r\n";
          request += "Connection: close\r\n";
          request += "Accept-Encoding: identity;q=1,chunked;q=0.1,*;q=0\r\n\r\n";

    ssl_client.print( request);
    while( ssl_client.connected() && !ssl_client.available()) delay(10);
    if( ssl_client.connected() && ssl_client.available() ) {
      while( ssl_client.available()) resp += (char)ssl_client.read();
      value = resp.substring( resp.lastIndexOf('\n')+1, resp.length()-1);
    }
    ssl_client.stop();    
  } else {
    Serial.println("[Firebase] can't connect to Firebase Host");
  }
  return value;
}