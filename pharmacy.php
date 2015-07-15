<?php 
        $host="140.116.247.27";
        $user="openopen";
        $pw="openopen";
        $db="2015-opendata-contest";
        $link=mysql_connect($host,$user,$pw) or trigger_error(mysql_error(),E_USER_ERROR);
        mysql_select_db($db) or die ("Unable to select database!");
        $result=mysql_query("SELECT * FROM `Tainan_pharmacy`");
        $all_pharmacy = mysql_num_rows($result); 
        echo "var nowPoint = new TGOS.TGPoint(startMarker.getPosition().x, startMarker.getPosition().y); ";
        echo "var url = \"picture/pharmacy.jpg\"; "; //取得圖示URL
        echo "var size = new TGOS.TGSize(30, 30); "; //取得圖示大小
        echo "var anchor = new TGOS.TGPoint(16, 33); ";  //取得錨點位移
        echo 'var markerOptions = {
                  flat:true,
                  draggable:false
                }; ';
        echo 'var InfoWindowOptions = {
                      maxWidth:4000, //訊息視窗的最大寬度 
                      opacity:0.9, 
                      pixelOffset: new TGOS.TGSize(5, -30) //InfoWindow起始位置的偏移量, 使用TGSize設定, 向右X為正, 向上Y為負  
                }; ';
        for ($i = 0; $i < $all_pharmacy; $i++)
        {
          $row = mysql_fetch_assoc($result);
          echo "var storeAddress = \"". $row["address1"] . $row["address2"] . $row["address3"] ."\"; ";
          echo "var Add = storeAddress;" ; //取得地址
          echo 'var LService = new TGOS.TGLocateService();      //宣告一個新的定位服務
                var request = {       //設定定位所需的參數, 使用address進行地址定位
                  address: Add
                }; ';
          echo 'LService.locateTWD97(request, function(result, status){ //進行定位查詢, 並指定回傳資訊為TWD97坐標系統
                var loc = result[0].geometry.location;';      //利用geometry.location取得地址點位(TGPoint)

          echo "var path1 = [nowPoint, loc]; ";  //設定path節點順序
          echo "var s1 = new TGOS.TGLineString(path1); "; //設定線資料的path
          echo "var howFar = s1.getLength(); ";
          echo "if(howFar <= 3000) { "; //篩選距離3公里內的藥局

          echo "var title = \" " . $row["name"] . "\"; "; //取得標記點標題
          echo "var icon = new TGOS.TGImage(url, size, new TGOS.TGPoint(0, 0), anchor); "; 
          echo "var marker = new TGOS.TGMarker(pMap, loc, title, icon, markerOptions); ";
          echo "pharmacy_Markers[" . $i . "] = marker; ";
          echo "totalMarker.push(marker); ";

          echo "var tmpinfotext = '<h3><b>" . $row["name"] . "</b></h3><p>地址：" . $row["address1"] . $row["address2"] . $row["address3"] . "<br />電話：" . $row["phone"] . "</p>'; "; //地標名稱及訊息視窗內容
          echo "var tmpMessageBox = new TGOS.TGInfoWindow(tmpinfotext, marker, InfoWindowOptions); ";//訊息視窗出現位置 
          echo "tmpMessageBox.setPosition(loc); ";
          echo "pharmacy_MessageBox[" . $i . "] = tmpMessageBox; ";
          echo "totalMessageBox.push(tmpMessageBox); ";
          
          echo 'TGOS.TGEvent.addListener(pharmacy_Markers[' . $i . '], "mouseover", function(){
                  for(i = 0; i < 5; i++) {
                    pharmacy_MessageBox[' . $i . '].open(pMap);
                    pharmacy_MessageBox[' . $i . '].close(pMap);
                  }
                  pharmacy_MessageBox[' . $i . '].open(pMap);
                }); '; //滑鼠監聽事件*/
          echo 'TGOS.TGEvent.addListener(pharmacy_Markers[' . $i . '], "mouseout", function(){
                  pharmacy_MessageBox[' . $i . '].close(pMap);
                }); '; //滑鼠監聽事件*/
          echo "} ";
          echo "  }); ";
        }
        //----------------繪製環域圖形-------------------
        echo 'if(BufferArea)   //假設地圖上已存在環域圖形(TGFill), 則先行移除
        BufferArea.setMap(null);

        var radius = 3000; //取得使用者輸入的環域半徑
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
        pMap.setZoom(pMap.getZoom()-1); ';     //取得環域圖形的邊界後, 調整地圖顯示的範圍
      ?>