<?php
session_start(); 
//エラー文の表示
ini_set("display_errors", 1);
error_reporting(E_ALL);
//ここでライブラリの読み込み
require_once 'pel/autoload.php';
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelTag;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelTiff;

  //ファイルがpostメソッドで送信されたか確認
  if(!empty($_FILES)){
    $userfile = $_FILES["userfile"]['name'];
    //ファイルネームが空でないか
    if($userfile != ""){
      $image = $userfile;
      //echo $image; //$imageの中身を確認
      //print $_FILES['userfile']['error'];
      //imageフォルダの中に投稿したファイルを格納
      move_uploaded_file($_FILES['userfile']['tmp_name'], './image/'.$image);
      
      /*
        ここでexifを取得する。
      */
      $filename = 'image/'.$image;
      $jpeg = new PelJpeg($filename);
      $app1 = $jpeg->getExif();

      if($app1){
        $tiff = $app1->getTiff();
        $ifd0 = $tiff->getIfd();

        if($ifd0){
          $exif = $ifd0->getSubIfd(PelIfd::EXIF);
          $gps = $ifd0->getSubIfd(PelIfd::GPS);
          //緯度
          $lat_0 = $gps->getEntry(PelTag::GPS_LATITUDE)->getValue()[0][0];  
          $lat_1 = $gps->getEntry(PelTag::GPS_LATITUDE)->getValue()[1][0];
          $lat_2 = $gps->getEntry(PelTag::GPS_LATITUDE)->getValue()[2][0];
          $lat = $lat_0 + $lat_1/60 + $lat_2/100/3600;
          //経度
          $lng_0 = $gps->getEntry(PelTag::GPS_LONGITUDE)->getValue()[0][0];
          $lng_1 = $gps->getEntry(PelTag::GPS_LONGITUDE)->getValue()[1][0];
          $lng_2 = $gps->getEntry(PelTag::GPS_LONGITUDE)->getValue()[2][0];
          $lng = $lng_0 + $lng_1/60 + $lng_2/100/3600;
          //方角
          $dir_0 = $gps->getEntry(PelTag::GPS_IMG_DIRECTION)->getValue()[0];
          $dir = $dir_0/1000;          
        }

        //DBへの接続
        $pdo = new PDO("sqlite:sharettori.sqlite");
        //エラーがあったら表示させる
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        //DBに挿入
        $st = $pdo->prepare("INSERT INTO GII(image,latitude,longitude,direction) VALUES(?,?,?,?)");
        $st->execute(array($userfile,$lat,$lng,$dir));

        $result = "登録しました。";
      }
    } 
  }
  else {
      $result = "内容がありません。";
    
  }
  //echo implode($lat_2);

  //echo $dir;

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
      <meta name="viewport" content="initial-scale=1.0">
  <title>Sharettori</title>
          <link rel="stylesheet" href="slidebars.min.css">
          <script src="js/jquery.js"></script>
          <script src="js/slidebars.min.js"></script>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        #map {
            height: 100%;
            width: 100%;
            z-index : 1;
        }
        body{
        background: rgb(255,255,255);
    }
    .div1{
      position: absolute;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
      width:220px;
      height:1080px;
      padding:10px;
      /* div要素を右寄せしてブロック作成
      無理だったらtext-alignで代用可能 */
      top: 0;
      bottom: 0;
      right: 0;
      z-index : 2;
  }
    .img1 {
      position: relative;  
      text-align : center;
      /* padding : 10px; */
      margin : 10px;
      z-index : 1;
    }
    p.sb-toggle-left{
        z-index: 2;
    }
    </style>



  <script>
    // 現在地取得処理
    function initMap() {
      // Geolocation APIに対応している
      if (navigator.geolocation) {
        // 現在地を取得
        navigator.geolocation.getCurrentPosition(
          // 取得成功した場合
          function(position) {
            // 緯度・経度を変数に格納
            var mapLatLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            // マップオプションを変数に格納
            var mapOptions = {
              zoom : 19,          // 拡大倍率
              center : mapLatLng  // 緯度・経度
            };
            // マップオブジェクト作成
            var map = new google.maps.Map(
              document.getElementById("map"), // マップを表示する要素
              mapOptions         // マップオプション
            );
            // 　マップにマーカーを表示する
            var imagePath ='tam.png';//表示させたい画像を取得
            var imagePaths = new google.maps.MarkerImage(
              'tam.png',
    // new google.maps.Size(50,50),アイコンサイズ設定
    //  new google.maps.Point(25,25)/*アイコン位置設定*/
     //画像が小さくなってるのではなく、切り取りになっている。↑

    );

            var marker = new google.maps.Marker({
              map : map,             // 対象の地図オブジェクト
              position : mapLatLng,   // 緯度・経度
              animation: google.maps.Animation.BOUNCE,
              icon: imagePaths
            });

            // マーカー毎の処理
  for (var i = 0; i < markerData.length; i++) {
    markerLatLng = new google.maps.LatLng({lat: markerData[i]['lat'], lng: markerData[i]['lng']}); // 緯度経度のデータ作成
    marker[i] = new google.maps.Marker({ // マーカーの追加
      position: markerLatLng, // マーカーを立てる位置を指定
      map: map, // マーカーを立てる地図を指定
  icon:  {
        path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
        scale: 6,
        fillColor: "red",
        fillOpacity: 0.8,
        strokeWeight: 2,
        rotation: markerData[i]['rotate'] //角度
    }
    });


  }
          },
          // 取得失敗した場合
          function(error) {
            // エラーメッセージを表示
            switch(error.code) {
              case 1: // PERMISSION_DENIED
                alert("位置情報の利用が許可されていません");
                break;
              case 2: // POSITION_UNAVAILABLE
                alert("現在位置が取得できませんでした");
                break;
              case 3: // TIMEOUT
                alert("タイムアウトになりました");
                break;
              default:
                alert("その他のエラー(エラーコード:"+error.code+")");
                break;
            }
          }
        );
      // Geolocation APIに対応していない
      } else {
        alert("この端末では位置情報が取得できません");
      }
    }


  </script>

</head>
<body>

      <div canvas="container">
        <!--ここにページのコンテンツ内容-->
         <div id="map"></div>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDQrsiMMQ6uai9uQByo_1M64FeSSYEyWaw&callback=initMap"></script>
          <p class="sb-toggle-left">MENU</p>
    </div>

  <div off-canvas="sb-left left push">
    <div class="left-content">
      <ul class="menu">
      <li><a href="pictureIndex.html" class="menu">画像登録</a></li>
      <li class="menu">
      <?php

       if (isset($_SESSION["user"])) {

        $username=$_SESSION["user"];

        print '<p>ようこそ'.$username.'さん! <br>[<a href="logout.php">ログアウト</a>]</p>';

      }else {

        print '<a href="login_form.php">'."ログイン".'</a>';

      }
      ?></a></li>
      </ul>
    </div>
  </div>
<script>
( function ( $ ) {
 // Initialize Slidebars
 var controller = new slidebars();
 controller.init();



 /**
 * Original Version Style Control Classes
 */

 // Open left
 $( '.sb-open-left' ).on( 'click', function ( event ) {
 event.preventDefault();
 event.stopPropagation();

 controller.open( 'sb-left' );
 } );

 // Toggle left
 $( '.sb-toggle-left' ).on( 'click', function ( event ) {
 event.preventDefault();
 event.stopPropagation();

 controller.toggle( 'sb-left' );
 } );

} ) ( jQuery );
</script>
<br>
<div class="div1">
    <?php 
     
     //sqlから緯度経度方角情報を取得
     echo <<<EOD

     <script>
         var marker = [];
         var infoWindow = [];
         var markerData = [ // マーカーを立てる場所名・緯度・経度


EOD;

     $db = new PDO("sqlite:sharettori.sqlite");
     $result = $db->query("SELECT * from GII");
     for($i= 0; $row=$result->fetch(); ++$i){
       
       $ids = $row['id'];
       $latitude = $row['latitude'];
       $longitude = $row['longitude'];
       $direction =$row['direction'];
       
       echo <<<EOD

        
         {
           name: $ids,
           lat: $latitude,
           lng: $longitude,
           rotate:$direction
         },     
       
       
EOD;

      }

      echo <<<EOD
    ];
    </script>     


EOD;
      $sql = "SELECT * FROM GII ORDER BY id DESC";
      $st2 = $db->query($sql);
      $data = $st2->fetchAll();
      foreach($data as $GII){
        echo '<img src="image/'.$GII['image'].'" class="img1" width="200" height="130">';
      }     

    ?>
    </div>
    <p>  
    <a href="pictureindex.html">元のページに戻る</a>
    </p>
    



</body>
</html>