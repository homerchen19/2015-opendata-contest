<?php
        header("Content-Type:text/html; charset=utf-8");
        $host="140.116.247.27";
        $user="openopen";
        $pw="openopen";
        $db="2015-opendata-contest";
        $link=mysql_connect($host,$user,$pw) or trigger_error(mysql_error(),E_USER_ERROR);
        mysql_select_db($db) or die ("Unable to select database!");
        $result=mysql_query("SELECT * FROM `Tainan_hospital`");
        $all_hospital = mysql_num_rows($result); 
        for ($i = 0; $i < $all_hospital; $i++)
        {
          $row = mysql_fetch_assoc($result);
          echo "translate(" . $row["latitude"] . ", " . $row["longtitude"] ."); "; //translate(x, y);
          echo "var pt = new TGOS.TGPoint(hospital_location_X, hospital_location_Y); ";    //取得醫院位置
          echo "var url = \"picture/hospitalMarker.gif\"; "; //取得圖示URL
          echo "var size = new TGOS.TGSize(30, 30); "; //取得圖示大小
          echo "var anchor = new TGOS.TGPoint(16, 33); ";  //取得錨點位移
          echo "var title = \" " . $row["name"] . "\"; "; //取得標記點標題

          echo 'var markerOptions = {
                  flat:true,
                  draggable:false
                }; ';
          echo "var icon = new TGOS.TGImage(url, size, new TGOS.TGPoint(0, 0), anchor); "; 
          echo "var marker = new TGOS.TGMarker(pMap, pt, title, icon, markerOptions); ";
          echo "hospital_Markers.push(marker); ";

          echo 'var InfoWindowOptions = {
                      maxWidth:4000, //訊息視窗的最大寬度 
                      opacity:0.9, 
                      pixelOffset: new TGOS.TGSize(5, -30) //InfoWindow起始位置的偏移量, 使用TGSize設定, 向右X為正, 向上Y為負  
                }; ';
          echo "var tmpinfotext = '<h3><b>" . $row["name"] . "</b></h3><p>地址：" . $row["address"] . "<br />電話：" . $row["phone"] . "</p>'; "; //地標名稱及訊息視窗內容
          echo "var tmpMessageBox = new TGOS.TGInfoWindow(tmpinfotext, marker, InfoWindowOptions); ";//訊息視窗出現位置 
          echo "tmpMessageBox.setPosition(pt); ";
          echo "hospital_MessageBox.push(tmpMessageBox); ";
          
          echo 'TGOS.TGEvent.addListener(hospital_Markers[' . $i . '], "mouseover", function(){
                  for(i = 0; i < 10; i++) {
                    hospital_MessageBox[' . $i . '].open(pMap);
                    hospital_MessageBox[' . $i . '].close(pMap);
                  }
                  hospital_MessageBox[' . $i . '].open(pMap);
                }); '; //滑鼠監聽事件
          echo 'TGOS.TGEvent.addListener(hospital_Markers[' . $i . '], "mouseout", function(){
                  hospital_MessageBox[' . $i . '].close(pMap);
                }); '; //滑鼠監聽事件
        }
      ?>