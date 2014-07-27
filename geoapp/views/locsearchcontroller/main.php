<style>
    body {
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        -webkit-font-smoothing: antialiased;
        -webkit-backface-visibility: hidden;
        -moz-osx-font-smoothing: grayscale;
        text-rendering: optimizeLegibility;
        font-smooth: always;
        line-height: 1.428571429;
        color: #333;
        background-color: #f4f4f4;
        font-size: 14px;
    }
    .bttn-dflt {
        background-color: #444;
        color: #fff;
        padding: 5px;
        display: block;
    }
    .placeholder {
        background-color: #f4f7f8;
        border: 1px solid #000;
        border-radius: 10px;
        width: 500px;
        height: 50px;
        padding: 10px;
        margin: 10px 0;
        outline: none;
    }
    .service-select {
        margin-top: 25px;
    }
    .styled-select {
        border: 1px solid #000;
        color: #fff;
        background: #444;
        padding: 5px;
        margin-top: 10px;
    }
    .left {
        width: 40%;
        float: left;
        text-align: left;
    }
    .right {
        width: 60%;
        float: left;
        text-align: right;
    }
    .row-header {
        font-weight: bold;
        font-size: 18px;
    }
    .row-holder {
        width: 100%;
        display: inline-block;
        border-bottom: 1px solid #000;
    }
    .error {
        color: #C00;
    }
    .linker {
        cursor: pointer;
    }
    .search-results, .direction-results {
        height: auto;
        position: relative;
        float: left;
    }
    .direction-results {
        margin-left: 10px;
        display: none;
    }
    .cust-bold {
        font-weight: bold;
    }
    .dir-step-cont {
        border-top: 1px solid #000;
        margin-top: 5px;
    }
</style>
<html>
    <body>
        <textarea id="user_loc" class="placeholder" placeholder="Type your address here"></textarea>
        <div id="user_locerr" class="placeholder error" style="display: none;"></div>
        <input type="button" value="Get my Address" id="bttn_find_loc" class="bttn-dflt"></input>
        <div class="placeholder service-select">Select the service that you want to search for<BR />
            <select class="styled-select" id="type_selector">
                <option value="airport">Airport</option>
                <option value="bank|atm">Bank/ATM</option>
                <option value="bus_station">Bus Stop</option>
                <option value="cafe">Cafe</option>
                <option value="food">Food</option>
                <option value="gas_station">Gas Station</option>
                <option value="grocery_or_supermarket">Super Market</option>
                <option value="hospital">Hospital</option>
                <option value="movie_theater">Movie Theater</option>
                <option value="park">Park</option>
                <option value="pharmacy">Pharmacy</option>
                <option value="police">Police Station</option>
                <option value="school">School</option>
                <option value="train_station">Train Station</option>
                <option value="taxi_stand">Taxi Stand</option>
                <option value="subway_station">Subway Station</option>
            </select>
        </div>
        <input type="button" value="Find" id="bttn_find_service" class="bttn-dflt"></input>
        <div id="res-cont">
            <div id="search_results" class="placeholder search-results">Your search results will be displayed here.</div>
            <div id="direction_results" class="placeholder direction-results">Click on any address to see the directions.</div>
        </div>
    </body>
</html>
<script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script type = "text/javascript" >
    $(document).ready(function() {
        var userLat, userLng, userLoc;

        $("#user_loc").on("input propertychange", function() {
            if (!$("#user_locerr").is(':hidden')) {
                $("#user_locerr").hide();
            }
        });

        $("#bttn_find_loc").on("click", function() {
            var geoLocation = navigator.geolocation;
            if (!geoLocation) {
                return false;
            }

            geoLocation.getCurrentPosition(_getUserLocationSuccess, _getUserLocationError);
        });

        $("#bttn_find_service").on("click", function() {
            var userIpLoc = $("#user_loc").val();
            if ($.trim(userIpLoc) === "") {
                $("#user_locerr").text("Please enter a valid address first.").show();
                return false;
            }

            var types = $("#type_selector").val();
            var qString = "&location=" + userLat + "," + userLng;
            if (userLoc !== userIpLoc) {
                qString = "&address=" + userIpLoc.replace(/\s+/gi, "+");
            }

            $.ajax({
                url: "/LocSearchController/getService/?radius=1000&types=" + types + qString,
                context: this
            }).done(function(data) {
                if (data) {
                    data = JSON.parse(data);
                    var len = data.length;
                    var divToAdd = "";
                    var obj;

                    for (var cnt = 0; cnt < len; cnt++) {
                        obj = data[cnt];
                        divToAdd += '<div class="row-holder" id="result_' + cnt + '"><div class="left">' + obj["name"] + '</div><div class="right"><span class="linker" data-lat=' + obj["lat"] + ' data-lng=' + obj["lng"] + '>' + obj["address"] + '</span></div>';
                        divToAdd += '</div>';
                    }

                    if (divToAdd === "") {
                        divToAdd = '<div class="no-result">No result found matching your criteria!!</div>';
                        $("#direction_results").hide();
                    } else {
                        divToAdd = '<div class="row-holder row-header"><div class="left">Name</div><div class="right">Address</div></div>' + divToAdd;
                        $("#direction_results").html("Click on any address to see the directions.");
                        $("#direction_results").show();
                    }

                    $("#search_results").html(divToAdd);
                }
            });
        });

        $("#search_results").on("click", ".linker", function() {
            var destLat = $(this).data("lat");
            var destLng = $(this).data("lng");
            _getDirection(userLat, userLng, destLat, destLng)
        });

        function _getUserLocationSuccess(pos) {
            userLat = pos.coords.latitude;
            userLng = pos.coords.longitude;

            $.ajax({
                url: "/LocSearchController/getLocation/?location_type=ROOFTOP&latlng=" + userLat + "," + userLng,
                context: this
            }).done(function(data) {
                var locData = JSON.parse(data);
                userLoc = locData.addr;
                if (userLoc) {
                    $("#user_loc").text(userLoc);
                    if (!$("#user_locerr").is(':hidden')) {
                        $("#user_locerr").hide();
                    }
                }
            });
        }

        function _getUserLocationError(e) {
            var errMsg = "Could not determine your location. Please type your address in the space provided."
            $("#user_locerr").text(errMsg).show();
        }

        function _getDirection(userLat, userLng, destLat, destLng) {
            var qString = "origin=" + userLat + "," + userLng + "&destination=" + destLat + "," + destLng;
            $.ajax({
                url: "/LocSearchController/getDirection/?" + qString,
                context: this
            }).done(function(data) {
                var routeData = JSON.parse(data);
                if (routeData) {
                    var markup = '<div id="dir_overview">Total Distance: <span class="cust-bold">' + routeData.totDist + '</span> Total time:<span class="cust-bold">' + routeData.totTime + '</span></div>';
                    if (routeData.steps) {
                        var stepData = routeData.steps;
                        var temp = "";
                        var len = stepData.length;
                        for (var cnt = 0; cnt < len; cnt++) {
                            temp = stepData[cnt];
                            markup += '<div class="dir-step-cont"><div class="dir-steps">' + temp.text + '</div><div class="cust-bold">' + temp.dist + ' ' + temp.time + '</div>';
                        }
                    }

                    $("#direction_results").html(markup);
                }
            });
        }
    });
</script>