<html>

<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
  <title>台南市運動地圖</title>
  <style>
    body{
      font-family:Arial;
      background: #000 url(picture/background.jpg) center center fixed no-repeat; 
      -moz-background-size:cover; 
      background-size: 100% 100%;
    }
  </style>

  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

  <!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="index.css" />

<!--angularjs 天氣預報-->
  <script src="ionic.bundle.js"></script>
  <script src="angular.min.js"></script>
    
  <script type="text/javascript">

  var app=angular.module('starter',[]);
  app.controller('controller', function ($scope, $http) {
             $scope.show=false;
            $scope.img_base_url = image_base_url;
            
            //$http.get("http://api.openweathermap.org/data/2.5/weather?q=London,uk")
              $http.get("http://api.openweathermap.org/data/2.5/weather", {params: {"lat": 22.99,"lon": 120.21}})
                   .then(function (resp) {
                      $scope.weather = resp.data;
                      $scope.show = true;
                  }, function (err) {
                      alert("Oops!");
                  });
              
          });
    var image_base_url = "http://openweathermap.org/img/w/"; // open weather api icon
    </script>


  <!--呼叫TGOS MAP API (lite)-->
  <script type="text/javascript"

src="http://api.tgos.nat.gov.tw/TGOS_API/tgos?ver=2&AppID=eEspyezvRdTOejpoc5n6vqarEeonOSrCS6INc4CouFqgWDNeP0dRWA==&APIKey=cGEErDNy5yN/1fQ0vyTOZrghjE+jIU6uYXA6z8TxlOKEF1JOD1jWmQPvSqmQN7vSxWvYAVFglFaC5LjGQWgYpNs+vP1WMjHudUh56I7iI+rwl6IvbGfYICsd6lCJq4yksLTDUjZdsNwjxiVeovhFYRnI5OBS/8kef12LYT/V2+2B/f9FPqUvFm3mO33PgqX9VS6xe6dzgkV4z1ZDOIR6MdYQvhS1FBcbxLDLMY7ZSERlgz6ZoAYXzvSQZbIt5HA2qouiStcwAz0gpOIj/N8WaxsEmV6qxI70jVEbB7hn/DZEtYJ0tvGB72a8miaxOj8kXDUhU49Z5oAhTeIwWk6y4pcxLytzfy1o" charset="utf-8"></script>

  <script type="text/javascript">
    var messageBox;  //訊息視窗物件  
    var pMap = null;   //初始化地圖物件
    var startPoint, startMarker;
    var BufferArea = null;
    var totalMarker = new Array();
    var totalMessageBox = new Array();

    //-----------------抓使用者位置---------------------------
    function getLocation() 
    {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(WGS84toTWD97);
        } else { 
            alert("您的瀏覽器不支援讀取現在位置");
        }
    }

    function WGS84toTWD97(position) //轉換座標
    {
      var Y84 = Number(position.coords.latitude);
      var X84 = Number(position.coords.longitude);
      var TT = new TGOS.TGTransformation();
      TT.wgs84totwd97(X84,Y84);
      var user_location_X = Number(TT.transResult.x);
      var user_location_Y = Number(TT.transResult.y);

      startPoint = new TGOS.TGPoint(user_location_X, user_location_Y); //地標坐標位置
      pMap.setCenter(startPoint);   //初始地圖中心點
      var infotext = '<B>使用者現在大約位置</B><p>(可自行拖曳至實際位置)</p>';  //地標名稱及訊息視窗內容
      var imgUrl = "picture/star.gif";  //標記點圖示來源

      //------------------建立標記點---------------------
      var markerImg = new TGOS.TGImage(imgUrl, new TGOS.TGSize(38, 33), new TGOS.TGPoint(0, 0), new TGOS.TGPoint(10, 33));       //設定標記點圖片及尺寸大小
      startMarker = new TGOS.TGMarker(pMap, startPoint,'', markerImg); //建立機關單位標記點
      startMarker.setDraggable(true);
      
      //-----------------建立訊息視窗--------------------
      var InfoWindowOptions = {
        maxWidth:4000,       //訊息視窗的最大寬度
        pixelOffset: new TGOS.TGSize(5, -30),         //InfoWindow起始位置的偏移量, 使用TGSize設定, 向右X為正, 向上Y為負 
        zIndex:99                                //視窗堆疊順序
      };                                       
      messageBox = new TGOS.TGInfoWindow(infotext, startPoint, InfoWindowOptions);   // 建立訊息視窗                                                               
      TGOS.TGEvent.addListener(startMarker, "mouseover", openInfoWindow);   //滑鼠監聽事件--開啟訊息視窗
      TGOS.TGEvent.addListener(startMarker, "mouseout", closeInfoWindow);     //滑鼠監聽事件--關閉訊息視窗
    }

    //------------------初始化地圖--------------------
    function InitWnd()
    {
      var pOMap = document.getElementById("OMap");
      var mapOptiions = {
        scaleControl: true,  //顯示比例尺
        navigationControl: true,   //顯示地圖縮放控制項

        navigationControlOptions: {        //設定地圖縮放控制項
          controlPosition: TGOS.TGControlPosition.TOP_LEFT,  //控制項位置
          navigationControlStyle: TGOS.TGNavigationControlStyle.SMALL         //控制項樣式
        },
          mapTypeControl: false       //不顯示地圖類型控制項
      };

      pMap = new TGOS.TGOnlineMap(pOMap, TGOS.TGCoordSys.EPSG3826, mapOptiions);    //建立地圖,選擇TWD97坐標
      pMap.setZoom(11);                                   //初始地圖縮放層級
    }

    function openInfoWindow() {      //開啟訊息視窗函式
      var nowPoint = new TGOS.TGPoint(startMarker.getPosition().x, startMarker.getPosition().y);
      messageBox.setPosition(nowPoint);
      messageBox.open(pMap);
    }
    function closeInfoWindow() {      //關閉訊息視窗函式
      messageBox.close();
    }


    //----------------繪製環域圖形-------------------
    function searchArea() 
    {
      if(BufferArea)   //假設地圖上已存在環域圖形(TGFill), 則先行移除
        BufferArea.setMap(null);

      var radius = parseFloat(document.getElementById("BufferDist").value); //取得使用者輸入的環域半徑
      var pt = new TGOS.TGPoint(startMarker.getPosition().x, startMarker.getPosition().y); //取得現在位置
      var circle = new TGOS.TGCircle(); //建立一個圓形物件(TGCircle)
      circle.setCenter(pt);       //位置為圓心
      circle.setRadius(radius);     //設定半徑

      var pgnOption = {         //設定圖形樣式
        fillColor: "#0099FF",
        fillOpacity : 0.1,
        strokeWeight : 2,
        strokeColor : "#ff00ff"
      };
      BufferArea = new TGOS.TGFill(pMap, circle, pgnOption);  //使用TGFill物件將圓形繪製出來
      pMap.fitBounds(BufferArea.getBounds());
      pMap.setZoom(pMap.getZoom()-1);     //取得環域圖形的邊界後, 調整地圖顯示的範圍

    }

    //----------------放置醫院點-------------------
    var hospital_Markers = new Array();  //新增一個陣列, 準備存放使用者新增的Marker
    var hospital_MessageBox = new Array(); //新增一個陣列, 存醫院資訊
    var hospital_location_X, hospital_location_Y;
    function setHospitalMarker()
    {
      <?php include("hospital.php") ?>
    }

    function translate(loc_x, loc_y)
    {
      var Y84 = Number(loc_x);
      var X84 = Number(loc_y);
      var TT = new TGOS.TGTransformation();
      TT.wgs84totwd97(X84,Y84);
      hospital_location_X = Number(TT.transResult.x);
      hospital_location_Y = Number(TT.transResult.y);
    }

    //----------------放置藥局點-------------------
    var pharmacy_Markers = new Array();  //新增一個陣列, 準備存放使用者新增的Marker
    var pharmacy_MessageBox = new Array(); //新增一個陣列, 存醫院資訊
    function setPharmacyMarker() 
    {
      pharmacy_Markers = [];
      pharmacy_MessageBox = [];
      <?php include("pharmacy.php") ?>
    }

    //----------------清除所有標籤-------------------
    function RmvMarker() {
      for (i=0; i < totalMarker.length; i++) {
        totalMarker[i].setMap(null);
        totalMessageBox[i].close(pMap);
      }   
      totalMarker = [];
      totalMessageBox = [];
      hospital_MessageBox = [];
      hospital_Markers = [];
      pharmacy_MessageBox = [];
      pharmacy_Markers = [];
      if(BufferArea)   //假設地圖上已存在環域圖形(TGFill), 則先行移除
        BufferArea.setMap(null);
    }

    //-------------抓紫外線資料--------------------------
    function parseUltraviolet()
    {
      <?php
        $row = 1;
        if (($handle = fopen("Ultraviolet.csv", "r")) !== FALSE) {
          while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row != 7) {
            $row++;
            echo "document.getElementById('Ultraviolet').innerHTML = \"" . $data[1] . "\"; "; 
          }
          fclose($handle);
        }
      ?>
    }

  </script>

  
</head>

<body onload="InitWnd(); getLocation(); parseUltraviolet();">

  <div id="container">
    <div id="header" style="width:40%;">
     <img src="picture/title.jpg" style="width:100%;">
    </div>
    </br>

    <div id="buttonZone">
      <input type="image" src="picture/hospital_button.png" width=70% onclick="setHospitalMarker()"> 
      </br>
      <input type="image" src="picture/pharmacy_button.png" width=70% onclick="setPharmacyMarker()"></button>
      </br>
      <button type="button" onclick="parseUltraviolet()">Click Me! </button>
      </br>
      <button type="button">Click Me! </button>
      </br>
      <button type="button">Click Me! </button>
    </div>

    <div id="OMap">
      <button id="clearMap" type="button" class="btn btn-danger" onclick="RmvMarker();">清除所有標籤</button>
    </div>

    <div id="right">
      <div id="search">
        <form class="form-group">
          <h2><b>搜尋條件</b></h2>
          <h3><b>搜尋半徑</b></h3>
          <input id="BufferDist" style="height:4%; width:60%; margin: 0px auto;" placeholder="(公尺)">
          <br>
          <h3><b>運動類型</b></h3>
            <label class="checkbox-inline">
            <input type="checkbox" id="option-running" value="option1">跑步
            </label>
            <label class="checkbox-inline">
              <input type="checkbox" id="option-basketball" value="option2">籃球
            </label>
            <label class="checkbox-inline">
              <input type="checkbox" id="option-badminton" value="option3">羽球
            </label>
            <br>
            <label class="checkbox-inline">
              <input type="checkbox" id="option-baseball" value="option1">棒球
            </label>
            <label class="checkbox-inline">
              <input type="checkbox" id="option-volleyball" value="option2">排球
            </label>
            <label class="checkbox-inline">
              <input type="checkbox" id="option-tennis" value="option3">網球
            </label>
          <br>
          <br>
          <button onclick="searchArea()" type="button" class="btn btn-success btn-lg">搜尋</button>
          <br>
          <br>
        </form>
      </div>

      <div id="weather" ng-controller="controller" ng-app="starter">
        <div ng-show="show">
            <div>
                <h2><b>{{weather.name}}</b></h2>                
            </div>
            <div >
                <img width="55" height="55" src="{{img_base_url + weather.weather[0].icon + '.png'}}">
                <h3>{{weather.main['temp'] - 273.15| number:1}}°C</h3>
            </div>
            <div>
                <div>
                    <p><b>濕氣</b>:{{weather.main.humidity}}%</p>
                </div>
                <div>
                    <p><b>風速</b>:{{weather.wind.speed}}mile/s</p>
                </div>
                <div>
                    <b>紫外線指數</b>:
                    <nobr id = "Ultraviolet"></nobr>
                </div>
            </div>
            <h5>更新時間:{{(weather.dt)*1000 | date:'yyyy-MM-dd HH:mm:ss'}}</h5>
            <br>
        </div>         
      </div>

    </div>
  </div>
</html>