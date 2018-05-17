<?php
  if(isset($_GET["place_id"])){
    $place_id = $_GET['place_id'];
    $places=file_get_contents('https://maps.googleapis.com/maps/api/place/details/json?placeid='.$place_id.'&key=AIzaSyDjgPK_wZClwhgznl7Za343PBjYykQ8KAU');
    $output= json_decode($places);
    $counter = 1;
    while($counter<=5){
      if(isset($output->result->photos[$counter-1])){
        $width = $output->result->photos[$counter-1]->width;
        $photo_reference = $output->result->photos[$counter-1]->photo_reference;
        $img_url = 'https://maps.googleapis.com/maps/api/place/photo?maxwidth='.$width.'&photoreference='.$photo_reference.'&key=AIzaSyDjgPK_wZClwhgznl7Za343PBjYykQ8KAU';
        $data = file_get_contents($img_url);
        $new = 'new_image_'.$counter.'.jpeg';
        file_put_contents($new, $data);
      }
      $counter += 1;
    }
    echo $places;
    exit();
  }
?>
<html>
  <head>
    <script type="text/javascript">
      var lat, lng, clat, clng;
      var marker = null;
      
      function init_location(){
        document.getElementById('location').required = true;
        document.getElementById('location').disabled = false;
      }

      function init(){
        viewSearch();
        setValues();
        var xhr;
        if(window.XMLHttpRequest) {
          xhr = new XMLHttpRequest();
        }
        else if(window.ActiveXObject) {
          try{
            xhr = new ActiveXObject("Msxml2.XMLHTTP");
          }
          catch(e){
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
          }
        }
        xhr.open('GET', "http://ip-api.com/json", true);
        xhr.send();
        xhr.onreadystatechange = processRequest;
 
        function processRequest(e) {
          if (xhr.readyState == 4 && xhr.status == 200) {
            var response = JSON.parse(xhr.responseText);
            clat = response.lat;
            clng = response.lon;
            document.getElementById('lat').value = clat;
            document.getElementById('lon').value = clng;
            document.getElementById('search').disabled = false;
          }
        }
      }

      function init_here(from){
        viewSearch();
        // localStorage.setItem('from', 'here');
        var from_radio = document.getElementsByName('from');
        from_radio[0].checked = true;
        document.getElementById('location').value = '';
        document.getElementById('location').required = false;
        document.getElementById('location').disabled = true;
        document.getElementById('search').disabled = false;
        // setValues();
      }

      function viewSearch(){
        var from = document.getElementsByName('from');
        var val = "<?php echo isset($_POST['from']) ? $_POST['from'] : 'here'?>";
        // if(localStorage["from"] == undefined){
        //   from[0].checked = true;
        // }
        // else{
          // var val = document.getElementById('from').value;
        if(from[0].value == val){
          from[0].checked = true;
        }
        else{
          from[1].checked = true;
        }
        // }
        if(from[0].checked){
          document.getElementById('location').required = false;
          document.getElementById('location').disabled = true;
          document.getElementById('search').disabled = true;
        }
        else{
          document.getElementById('location').disabled = false;
          document.getElementById('search').disabled = false;
        }
      }

      // function getValues(form){
      //   var parameters = ['keyword', 'distance', 'location', 'category'];
      //   for(var i=0;i<parameters.length;i++){
      //     var key = parameters[i];
      //     localStorage.setItem(key, form[key].value);
      //   }
      //   var loc = document.forms[0].elements['from'];
      //   if(loc[0].checked){
      //     localStorage.setItem('from', loc[0].value);
      //   }
      //   else{
      //     localStorage.setItem('from', loc[1].value);
      //   }
      // }

      function setValues(){
        var parameters = ['keyword', 'distance', 'location', 'category', 'from'];
        document.getElementById('keyword').value = "<?php echo isset($_POST['keyword']) ? $_POST['keyword'] : '' ;?>";
        document.getElementById('location').value = "<?php echo isset($_POST['location']) ? $_POST['location'] : '' ;?>";
        document.getElementById('distance').value = "<?php echo isset($_POST['distance']) ? $_POST['distance'] : '' ;?>";
        var from = document.getElementsByName('from');
        var from_value = "<?php echo isset($_POST['from']) ? $_POST['from'] : 'here'?>";
        if(from[0].value == from_value){
          from[0].checked = true;
        }
        if(from[1].value == from_value){
          from[1].checked = true;
        }
        document.getElementById('category').selected = "<?php echo isset($_POST['category']) ? $_POST['category'] : '' ;?>";
        document.getElementById('category').value = "<?php echo isset($_POST['category']) ? $_POST['category'] : 'default';?>";
        // for(var i=0;i<parameters.length;i++){
        //   var key = parameters[i];
        //   if(localStorage[key] != undefined){
        //     if(key == 'category'){
        //       document.getElementById(key).selected = localStorage.getItem(key);
        //     }
        //     if(key == 'from'){
        //       var from = document.getElementsByName('from');
        //       if(from[0].value == localStorage.getItem('from')){
        //         from[0].checked = true;
        //       }
        //       if(from[1].value == localStorage.getItem('from')){
        //         from[1].checked = true;
        //       }
        //     }
        //     else{
        //       document.getElementById(key).value = localStorage.getItem(key);
        //     }
        //   }
        // }
        // localStorage.clear();
      }

      function clearFields(form){
        // localStorage.clear();
        document.getElementById('keyword').required = false;
        form.keyword.value = "";
        form.category.value = 'default';
        form.distance.value = "";
        form.location.value = "";
        form.from.value = "here";
      }

      function displayTable(response, latitude, longitude){
        clat = latitude;
        clng = longitude;
        var maindiv = document.getElementById('display');
        if(response[0] == undefined){
          var center = document.createElement('center');
          var p = document.createElement('p');
          p.className = 'box2';
          p.innerHTML = 'No Records have been found.'
          center.appendChild(p);
          maindiv.appendChild(center);
        }
        else{
          var table = document.createElement('table');
          table.setAttribute('align', 'center');
          table.setAttribute('style', 'font-family:arial, sans-serif');
          table.setAttribute('style', 'border-collapse:collapse');
          table.setAttribute('style', 'font-size:14px');
          table.setAttribute('width', '80%');
          var thead = document.createElement('thead');
          var th = document.createElement('th');
          th.setAttribute('style', 'border:1px solid #dddddd');
          th.setAttribute('style', 'text-align:middle');
          th.setAttribute('style', 'padding:8px');
          th.setAttribute('width', '50px');
          th.innerHTML = 'Category';
          thead.appendChild(th);
          var th = document.createElement('th');
          // th.setAttribute('width', '500px');
          th.innerHTML = 'Name';
          thead.appendChild(th);
          var th = document.createElement('th');
          // th.setAttribute('width', '500px');
          th.innerHTML = 'Address';
          thead.appendChild(th);
          table.appendChild(thead);
          var tbody = document.createElement('tbody');
          for(var key in response){
            var tr = document.createElement('tr');
            var td = document.createElement('td');
            td.setAttribute('style', 'border:1px solid #dddddd');
            td.setAttribute('style', 'text-align:left');
            td.setAttribute('style', 'padding:8px');
            td.setAttribute('width', '5%');
            var img = document.createElement('img');
            img.src = response[key]["icon"];
            img.setAttribute('width', '30px');
            img.setAttribute('height', '30px');
            td.appendChild(img);
            tr.appendChild(td);
            var td = document.createElement('td');
            td.setAttribute('style', 'border:1px solid #dddddd');
            td.setAttribute('style', 'text-align:left');
            td.setAttribute('style', 'padding:8px');
            td.setAttribute('width', '35%');
            var div = document.createElement('div');
            div.innerHTML = response[key]["name"];
            div.setAttribute('id', response[key]["place_id"]);
            div.onclick = getCategoryDetails;
            div.className = 'popup_name';
            div.setAttribute('style', 'font: 14px normal arial, sans-serif');
            div.setAttribute('name', 'places');
            // var input = document.createElement('div');
            // input.setAttribute('type', 'button');
            // input.setAttribute('name', 'places');
            // input.setAttribute('id', response[key]["place_id"]);
            // input.setAttribute('value', response[key]["name"]);
            // input.innerHTML = response[key]["name"];
            // input.onclick = getCategoryDetails;
            // input.className = 'place';
            // td.appendChild(input);
            td.appendChild(div);
            tr.appendChild(td);
            var td = document.createElement('td');
            td.setAttribute('style', 'border:1px solid #dddddd');
            td.setAttribute('style', 'text-align:left');
            td.setAttribute('style', 'padding:8px');
            td.setAttribute('width', '40%');
            var div = document.createElement('div');
            div.innerHTML = response[key]["vicinity"];
            div.setAttribute('id', response[key]["geometry"]["location"]["lat"]+'&'+response[key]["geometry"]["location"]["lng"]);
            div.setAttribute('location', clat+'&'+clng);
            div.onclick = displayMap;
            div.className = 'popup';
            // div.setAttribute('name', 'address');
            var smalldiv = document.createElement('div');
            smalldiv.className = 'popuptext';
            smalldiv.setAttribute('id', 'map');
            // var floating_outside = document.createElement('div');
            var floating = document.createElement('div');
            floating.className = 'upper_div';
            floating.setAttribute('id', 'floating-panel');
            var select = document.createElement('select');
            select.setAttribute('id','mode');
            select.setAttribute('size', 3);
            var option = document.createElement('option');
            option.value = 'WALKING';
            option.innerHTML = 'Walk there';
            select.appendChild(option);
            var option = document.createElement('option');
            option.value = 'BICYCLING';
            option.innerHTML = 'Bike there';
            select.appendChild(option);
            var option = document.createElement('option');
            option.value = 'DRIVING';
            option.innerHTML = 'Drive there';
            select.appendChild(option);
            floating.appendChild(select);
            // floating_outside.appendChild(floating);
            td.appendChild(div);
            td.appendChild(smalldiv);
            td.appendChild(floating);
            // td.appendChild(floating_outside);
            tr.appendChild(td);
            tbody.appendChild(tr);
          }
          table.append(tbody);
          maindiv.appendChild(table);
        }
      }

      function getCategoryDetails(){
        var req;
        if(window.XMLHttpRequest) {
          req = new XMLHttpRequest();
        }
        else if(window.ActiveXObject) {
          try{
            req = new ActiveXObject("Msxml2.XMLHTTP");
          }
          catch(e){
            req = new ActiveXObject("Microsoft.XMLHTTP");
          }
        }
        req.onreadystatechange = function(){
          if (req.readyState == 4 && req.status == 200) {
            var json_response = JSON.parse(req.responseText);
            displayCategoryDetails(json_response);
          }
        }
        req.open('GET', "place.php?place_id=" + this.id, true);
        req.send();
      }

      function displayMap(){
        lat = this.id.split("&")[0];
        lng = this.id.split("&")[1];
        setstyle = setStyle(this);
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyDjgPK_wZClwhgznl7Za343PBjYykQ8KAU&callback=initMap";
        document.body.appendChild(script);
      }

      var latlngid;
      // var status = null;
      // var stat = 'hide';

      function setStyle(latlng){
        positions = document.getElementById(latlng.id).getBoundingClientRect();
        myPopup = document.getElementById('map');
        floating = document.getElementById('floating-panel');
        if(myPopup.classList[1]=='show'){
          // var anchors = document.getElementsByName('address');
          // for (var i = 0; i < anchors.length; i++) {
          //   anchors[i].classList.remove('address');
          // }
          if(latlngid == latlng.id){
            floating.style.visibility = 'hidden';
            myPopup.style.visibility = 'hidden';
          }
          else{
            myPopup.classList.toggle("show");
            latlngid = latlng.id;
            floating.style.visibility = 'visible';
            myPopup.style.visibility = 'visible';
          }
        }
        else{
          latlngid = latlng.id;
          // var anchors = document.getElementsByName('address');
          // for (var i = 0; i < anchors.length; i++) {
          //   anchors[i].classList.add('address');
          // }
          // document.getElementById(latlng.id).classList.remove('address');
          floating.style.visibility = 'visible';
          myPopup.style.visibility = 'visible';
        }
        scrollValueTop = document.getElementsByTagName('body')[0].scrollTop;
        scrollValueLeft = document.getElementsByTagName('body')[0].scrollLeft;
        myPopup.style.left = positions.left + scrollValueLeft;
        myPopup.style.top = positions.bottom + scrollValueTop;
        floating.style.left = positions.left + scrollValueLeft;
        floating.style.top = positions.bottom + scrollValueTop;
      }

      function initMap() {
        var positions = {};
        positions['lat'] = parseFloat(lat);
        positions['lng'] = parseFloat(lng);
        var directionsDisplay = new google.maps.DirectionsRenderer;
        var directionsService = new google.maps.DirectionsService;
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 12,
          center: positions
        });
        marker = new google.maps.Marker({
          position: positions,
          map: map
        });
        directionsDisplay.setMap(map);
        calculateAndDisplayRoute(directionsService, directionsDisplay);
        document.getElementById('mode').addEventListener('change', function() {
          calculateAndDisplayRoute(directionsService, directionsDisplay);
        });
        var popup = document.getElementById('map');
        popup.classList.toggle("show");
      }

      function calculateAndDisplayRoute(directionsService, directionsDisplay) {
        if(myPopup.classList[1]=='show'){
          var selectedMode = document.getElementById('mode').value;
        }
        else{
          var selectedMode = "";
          document.getElementById('mode').value = '';
        }
        coordinates = document.getElementById(lat+'&'+lng).getAttribute("location").split('&');
        clat = coordinates[0];
        clng = coordinates[1];
        
        if(selectedMode){
          origin = constructMarkers(clat, clng);
          destination = constructMarkers(lat, lng);
          directionsService.route({
            origin: origin,
            destination: destination,
            travelMode: google.maps.TravelMode[selectedMode]
          }, function(response, status) {
            if (status == 'OK') {
              marker.setMap(null);
              directionsDisplay.setDirections(response);
            }
          });
        }
      }

      function constructMarkers(latitude, longitude){
        coordinates = {}
        coordinates['lat'] = parseFloat(latitude);
        coordinates['lng'] = parseFloat(longitude);
        return coordinates;
      }

      function displayCategoryDetails(places){
        var center = document.createElement('center');
        var b = document.createElement('b');
        b.innerHTML = places["result"]["name"];
        var br = document.createElement('br');
        b.appendChild(br);
        var br = document.createElement('br');
        b.appendChild(br);
        center.appendChild(b);
        var p = document.createElement('p');
        p.setAttribute('id', 'show_review');
        p.setAttribute('style', 'text-align:center');
        p.innerHTML = 'click to show reviews';
        var br = document.createElement('br');
        p.appendChild(br);
        center.appendChild(p);
        var img = document.createElement('img');
        img.src = 'http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png';
        img.setAttribute('width', '30px');
        img.onclick = changeArrowReviews;
        img.setAttribute('id', 'down_review');
        center.appendChild(img);
        var br = document.createElement('br');
        center.appendChild(br);
        var br = document.createElement('br');
        center.appendChild(br);
        if("reviews" in places["result"]){
          var div = document.createElement('div');
          div.setAttribute('id', 'reviews');
          div.setAttribute('width', '50%');
          div.setAttribute('style', 'border: 0.5px solid #c4c4c4');
          var reviews = places["result"]["reviews"];
          for(var i=0;i<5;i++){
            if(reviews[i] != undefined){
              if(i!=0){
                var hr = document.createElement('hr');
                div.appendChild(hr);
              }
              var icenter = document.createElement('center');
              if(reviews[i]["profile_photo_url"] != undefined){
                var img = document.createElement('img');
                img.src = reviews[i]["profile_photo_url"];
                img.setAttribute('height', '30');
                img.setAttribute('width', '30');
                icenter.appendChild(img);
              }
              var b = document.createElement('b');
              b.innerHTML = reviews[i]["author_name"];
              icenter.appendChild(b);
              div.appendChild(icenter);
              // var br= document.createElement('br');
              // div.appendChild(br);
              if(reviews[i]["text"] != ""){
                var hr = document.createElement('hr');
                var p = document.createElement('p');
                p.innerHTML = reviews[i]["text"];
                div.appendChild(hr);
                div.appendChild(p);
              }
            }
          }
          var br = document.createElement('br');
          div.appendChild(br);
          center.appendChild(div);
        }
        else{
          var div = document.createElement('div');
          div.setAttribute('id', 'reviews');
          div.setAttribute('width', '50%');
          div.setAttribute('style', 'border: 0.5px solid #c4c4c4');
          var icenter = document.createElement('center');
          var b = document.createElement('b');
          b.innerHTML = 'No Reviews Found.';
          icenter.appendChild(b);
          div.appendChild(icenter);
          center.appendChild(div);
        }
        var p = document.createElement('p');
        p.setAttribute('id', 'show_photo');
        p.setAttribute('style' , 'text-align:center');
        p.innerHTML = 'click to show photos';
        var br = document.createElement('br');
        p.appendChild(br);
        center.appendChild(p);
        var img = document.createElement('img');
        img.setAttribute('id', 'down_photo');
        img.src = 'http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png';
        img.setAttribute('width', '30px');
        img.onclick = changeArrowPhotos;
        center.appendChild(img);
        var br = document.createElement('br');
        center.appendChild(br);
        var br = document.createElement('br');
        center.appendChild(br);
        if("photos" in places["result"]){
          var div = document.createElement('div');
          div.setAttribute('id', 'photos');
          div.setAttribute('width','40%');
          div.setAttribute('style', '0.5px solid #c4c4c4');
          var photos = places["result"]["photos"];
          var br = document.createElement('br');
          div.appendChild(br);
          for(var i=0;i<5;i++){
            var j = i+1;
            if(photos[i] != undefined){
              if(i!=0){
                var br = document.createElement('br');
                div.appendChild(br);
                var hr = document.createElement('hr');
                div.appendChild(hr);
              }
              var a = document.createElement('a');
              a.href = 'new_image_'+ j +'.jpeg';
              a.setAttribute('target', '_blank');
              var img = document.createElement('img');
              img.src = 'new_image_'+ j +'.jpeg';
              img.setAttribute('height', '400');
              img.setAttribute('width', '95%');
              a.appendChild(img);
              div.appendChild(a);
            }
          }
          var br = document.createElement('br'); 
          div.appendChild(br);
          var br = document.createElement('br'); 
          div.appendChild(br);
          center.appendChild(div);
          var br = document.createElement('br'); 
          center.appendChild(br);
        }
        else{
          var div = document.createElement('div');
          div.setAttribute('id', 'photos');
          div.setAttribute('width','40%');
          div.setAttribute('style', '0.5px solid #c4c4c4');
          div.setAttribute('height', '3%');
          var icenter = document.createElement('center');
          var b = document.createElement('b');
          b.innerHTML = 'No Photos Found.';
          icenter.appendChild(b);
          div.appendChild(icenter);
          center.appendChild(div);
        }
        document.getElementById('display').innerHTML = "";
        var maindiv = document.getElementById('details');
        maindiv.appendChild(center);
        initCategoryDetails();
      }

      function initCategoryDetails(){
        document.getElementById('reviews').style.display = 'none';
        document.getElementById('photos').style.display = 'none';
      }

      function changeArrowReviews(){
        var review = document.getElementById('reviews')
        if(review.style.display == 'block'){
          this.src = 'http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png';
          review.style.display = 'none';
          var sreview = document.getElementById('show_review');
          sreview.innerHTML = 'click to show reviews';
        }
        else{
          var arrow = document.getElementById('down_review');
          review.style.display = 'block';
          this.src = 'http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png';
          document.getElementById('photos').style.display = 'none';
          document.getElementById('down_photo').src = 'http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png';
          var sreview = document.getElementById('show_review');
          sreview.innerHTML = 'click to hide reviews';
          var sphoto = document.getElementById('show_photo');
          sphoto.innerHTML = 'click to show photos';
        }
      }

      function changeArrowPhotos(){
        var photo = document.getElementById('photos')
        if(photo.style.display == 'block'){
          this.src = 'http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png';
          photo.style.display = 'none';
          var sphoto = document.getElementById('show_photo');
          sphoto.innerHTML = 'click to show photos';
        }
        else{
          photo.style.display = 'block';
          this.src = 'http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png';
          document.getElementById('reviews').style.display = 'none';
          document.getElementById('down_review').src = 'http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png';
          var sphoto = document.getElementById('show_photo');
          sphoto.innerHTML = 'click to hide photos';
          var sreview = document.getElementById('show_review');
          sreview.innerHTML = 'click to show reviews';
        }
      }
    </script>
    <style>
      body, html{
        padding:0;
        margin:0;
      }

      table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        font-size: 12px;
      }
      td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
      }
      th {
        border: 1px solid #dddddd;
        text-align: middle;
        padding: 8px;
      }
      .location{
        margin-left: 61%;
      }
      .box{
        background-color: #f9f9f9;
        width: 500px;
        border: 1px solid #c1c1c1;
        padding: 0px 10px 20px 10px;
        margin: 25px 32%;
      }
      .box2{
        background-color: #f9f9f9;
        width: 650px;
        border: 1px solid #c1c1c1;
        padding: 0px 10px 4px 10px;
        margin: 25px 23%;
        text-align: center;
      }
      input[type=text] {
        width: 30%;
        padding: 4px 7px;
        margin: 2px 0;
        display: inline-block;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
      }

      select {
        width: 23%;
        margin: 2px 0;
        display: inline-block;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
      }

      #category{
        padding: 4px 7px;
        width: 30%;
      }

      .submit-button {
        width: 15%;
        background-color: white;
        color: black;
        padding: 4px 20px;
        margin: 8px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        cursor: pointer;
      }
      a{
        text-decoration: none;
        color: black;
      }

      /*.place{
        border: none;
        background-color: white;
      }*/

      .map{
        border: none;
        background-color: white;
      }

      .show {
        width: 350px;
        height: 350px;
      }

      /*#map{
        width: 350px;
        height: 350px;
      }*/

      #photos{
        width: 40%;
        border: 0.5px solid #c4c4c4;
      }

      #reviews{
        width: 50%;
        border: 0.5px solid #c4c4c4;
      }

      #show_review{
        text-align: center;
      }

      #show_photo{
        text-align: center;
      }

      hr{
        height: 0.5px;
        color: #bfbfbf;
      }

      p{
        text-align:left;
        margin-left: 5px;
        margin-right: 5px;
      }

      .popup {
        display: inline-block;
        cursor: pointer;
        user-select: none;
        wordWrap: 'break-word';
      }

      .popup_name {
        display: inline-block;
        cursor: pointer;
      }

      .popup:hover{
        color: #808B96;
      }

      .popuptext {
        visibility: hidden;
        /*width: 500px;
        height: 500px;*/
        background-color: #555;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 8px 0;
        position: absolute;
        z-index: 1;
      }

      .popuptext::after {
        content: "";
        position: absolute;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #555 transparent transparent transparent;
      }

      .map .show {
        visibility: visible;
      }

      #floating-panel{
        visibility: hidden;
        position: absolute;
        z-index: 5;
        font-family: 'Roboto','sans-serif';
        line-height: 30px;
        /*width: 350px;*/
        width: 91px;
        margin-top: -2px;
      }

      .upper_div{
        /*background-color: lightblue;*/
        width: 110px;
        height: 110px;
        overflow: hidden;
      }

      #mode{
        /*height: 5%;*/
        height: 87%;
        font-size: 13px;
        background-color: #f0f0f0;
        overflow: hidden;
        /*width:23%;*/
        width:117%;
      }

      #mode option{
        padding-top: 7px;
        padding-bottom: 7px;
        padding-left: 3px;
        font-weight: bold;
      }

      #mode option:checked{
        background: #dcdcdc;
      }

      #mode option:hover{
        background: #dcdcdc;
      }

      .address{
        pointer-events: none;
      }
    </style>
  </head>
  <body onload="init()">
    <?php
      if(isset($_POST['clear'])){
        $keyword = "";
        $location = "";
        $distance = 10;
        $from = 'here';
        $category = 'default';
      }
      else{
        $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
        $location = isset($_POST['location']) ? $_POST['location'] : "";
        $distance = isset($_POST['distance']) ? $_POST['distance'] : "";
        $from = isset($_POST['from']) ? $_POST['from'] : 'here';
        $category = isset($_POST['category']) ? $_POST['category'] : 'default';
      }
    ?>
    <div class="box">
      <center style='margin-top: -15px;margin-bottom: -13px;'><h1><i>Travel and Entertainment Search</i></h1></center><hr>
      <form style="margin-bottom: -12px;" method="post" action="">  
        <b>Keyword</b> <input type="text" name="keyword" id="keyword" size="15" required><br>
        <b>Category</b><select name="category" id='category'>
          <option value="default">default</option>
          <option value="cafe" >cafe</option>
          <option value="bakery" >bakery</option>
          <option value="restaurant" >restaurant</option>
          <option value="beauty salon" >beauty salon</option>
          <option value="casino" >casino</option>
          <option value="movie theater" >movie theater</option>
          <option value="lodging">lodging</option>
          <option value="airport" >airport</option>
          <option value="train station" >train station</option>
          <option value="subway station" >subway station</option>
          <option value="bus station">bus station</option>
        </select><br>
        <b>Distance(miles)</b> <input type="text" name="distance" id='distance' placeholder= "10" size="15">
        <b>from</b> <input type="radio" name="from" id="here" value="here" onClick="init_here(this)">Here<br>
        <input type="radio" name="from" id="loc" value="location" class="location" onClick="init_location()">
        <input type="text" name="location" placeholder="location" size="15" id="location"><br>
        <input type="hidden" name="lat" id="lat" value="">
        <input type="hidden" name="lon" id="lon" value="">
        <input type="submit" name="submit" class="submit-button" value="search" id='search' style="
        margin-left: 61px;">
        <input type="submit" name="clear" class="submit-button" value="clear" onClick='clearFields(this.form)'>  
      </form>
    </div>
    <div id='display'></div>
    <div id='details'></div>
    <?php
      function getApiKey(){
        return 'AIzaSyDjgPK_wZClwhgznl7Za343PBjYykQ8KAU';
      }

      function processRequest(){
        $key = getApiKey(); # google api key
      
        $keyword = urlencode($_POST['keyword']);
        $category = urlencode(str_replace(' ', '_', $_POST['category']));
        $distance = (is_numeric($_POST['distance']) ? $_POST['distance'] : 10) * 1609.344;
        $from = $_POST['from'];
        if($from == "here"){
          $lat = $_POST["lat"];
          $lng = $_POST["lon"];
        }
        else{
          $location = urlencode($_POST['location']);
          $geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$location.'&key='.$key);
          $output= json_decode($geocode);
          if($output->results == []){
            $lat = "";
            $lng = "";
          }
          else{
            $lat = $output->results[0]->geometry->location->lat;
            $lng = $output->results[0]->geometry->location->lng;
          }
        }
        $results = file_get_contents('https://maps.googleapis.com/maps/api/place/nearbysearch/json?location='.$lat.','.$lng.'&radius='.$distance.'&type='.$category.'&keyword='.$keyword.'&key='.$key);
        return array($results, $lat, $lng);
      }
    ?>
    <?php
      if(isset($_POST['submit'])){
        $requests = processRequest();
        $results = $requests[0];
        $lat = $requests[1];
        $lng = $requests[2];
        $output = json_decode($results);
      ?>
      <script type="text/javascript">
        var content = <?php echo json_encode($output) ?>;
        var lat = <?php echo json_encode($lat) ?>;
        var lng = <?php echo json_encode($lng) ?>;
        res = content["results"];
        displayTable(res, lat, lng);
      </script>
      <?php
      }
    ?>
  </body>
</html>
