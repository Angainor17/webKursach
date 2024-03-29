@extends("client.accountFrame", ["title"=>trans('app.cartPage')])

@section("innerContent")

    <script>
        function deleteProduct(id) {
            $.ajax({
                url: 'removeFromBasket/' + id,
                type: 'GET',
                success: function () {
                    initTable();
                }
            });
            orderChange();
        }

        function getTotalCost() {
            $.ajax({
                url: 'getTotalCost/',
                type: 'GET',
                success: function (result) {
                    initTotalCostView(result);
                }
            });
        }

        function initTotalCostView(s) {
            $('#totalCost').val(s);
            $('#totalCostLabel').val(" {{trans('app.rub')}}");
        }

        $(document).ready(function () {
            getTotalCost();
            initTable();
            initForm();
            menuActive();
        });

        function menuActive() {
         $('#cart').addClass("active");
        }

        function initForm() {
            $("#form").submit(function (event) {
                event.preventDefault();
                addOrder();
                return false;
            });
            $("#inputName").val("");
            $("#inputCity").val("");
            $("#inputAddress").val("");
            $("#inputTelephone").val("");
            $("#inputComment").val("");
        }

        function addOrder() {
            var lView = $("#listView").data("kendoListView");

            var arr = lView.dataSource.data();
            var list = [];
            for (var i = 0; i < arr.length; i++) {
                var item = {
                    id: arr[i].id,
                    amount: arr[i].ageFrom
                };
                list.push(item);
            }

            var totalCost = $("#totalCost").val();
            var name = $("#inputName").val();
            var city = $("#inputCity").val();
            var address = $("#inputAddress").val();
            var telephoneNumber = $("#inputTelephone").val();
            var comment = $("#inputComment").val();

            $.ajax({
                url: "makeOrder",
                type: "POST",
                dataType: "json",
                data: {
                    list: list,
                    cost: totalCost,
                    name: name,
                    city: city,
                    address: address,
                    telephoneNumber: telephoneNumber,
                    comment: comment

                },
                success: function (data) {
                    alert(data);
                   //window.location.href = "/home";
                },
                error: function () {

                }

            });
        }

        function orderChange(id) {
            var lView = $("#listView").data("kendoListView");
            var arr = lView.dataSource.data();
            var newValue = $("#nP" + id).val();
            console.log("newValue = " + newValue);
            var sum = 0;

            for (var i = 0; i < arr.length; i++) {
                if (id == arr[i].id) {
                    arr[i].ageFrom = newValue;
                }
                sum += arr[i].discount * arr[i].ageFrom;
            }
            $("#totalCost").val(sum);
            $("#totalCostLabel").val(" " + "{{trans('app.rub')}}");
        }

        function initTable() {
            console.log("Init Table");
            var dataSource = new kendo.data.DataSource({
                transport: {
                    read: {
                        url: "basketProductList",
                        dataType: "json"
                    }
                },
                requestEnd: function (e) {
                    var lView1 = $("#listView").data("kendoListView");

                    var arr1 = lView1.dataSource.data();

                    for (var i1 = 0; i1 < arr1.length; i1++) {
                        arr1[i1].ageFrom = 1;
                    }
                }
            });

            $("#listView").kendoListView({
                dataSource: dataSource,
                template: kendo.template($("#template").html()),
                autoBind: true
            }).data("kendoListView");
        }

    </script>

    <style>
        #listView {
            margin-left: 6%;
            padding-top: 50px;
            margin-right: 6%;
            padding-bottom: 6%;
            display: block;
            text-align: center;
        }

        #orderDiv {
            padding-top: 50px;
            width: 88%;
            margin: 40px 6%;
            padding-bottom: 6%;
            border: 1px solid #cbcbcb;
            display: inline-block;
            background-color: white;
            text-align: center;
        }

        .cost {
            color: red;
        }

        .product {
            border: 2px solid #b9b9b9;
            width: 300px;
            height: 340px;
            margin: 5px;
            font-size: 16px;
            padding: 5px;
            display: inline-block;
            text-align: center;
        }
    </style>

    <div>
        <div id="listView"></div>
    </div>

    <div id="orderDiv">
        <form id="form" style="position: relative">

            <div style="padding-left: 1%;">
                <div class="input-group" style="margin-top: 20px">
                    <span class="input-group-addon" style="width: auto">{{trans('app.nameLabel')}}</span>

                    <input type="text" style="width: 400px" class="form-control is-valid" id="inputName"
                           pattern=".{1,100}" required>
                </div>

                <div class="input-group" style="margin-top: 20px">
                    <span class="input-group-addon" style="width: auto">{{trans('app.cityLabel')}}</span>

                    <input type="text" style="width: 400px" class="form-control is-valid" id="inputCity"
                           pattern=".{1,100}" required>
                </div>


                <div class="input-group" style="margin-top: 20px">
                    <span class="input-group-addon" style="width: auto">{{trans('app.addressLabel')}}</span>

                    <input type="text" style="width: 400px" class="form-control is-valid" id="inputAddress"
                           pattern=".{1,100}" required>
                </div>


                <div class="input-group" style="margin-top: 20px;">
                    <span class="input-group-addon" style="width: auto">{{trans('app.telephoneNumber')}}</span>

                    <input type="text" class="form-control is-valid" id="inputTelephone"
                           placeholder="+ 7 (777) 777-77-77" style="width: 400px"
                           pattern=".{1,20}" required>
                </div>

                <div style="left: 30px;position: absolute">{{trans('app.commentLabel')}}</div>
                <textarea id="inputComment" rows="4" maxlength="700" style="width: 400px;margin-top: 40px"
                          class="form-control is-valid">

                </textarea>

            </div>
            <div class="input-group" style="position: absolute;top: 0; right: 0;">
                <span class="input-group-addon"
                      style="height:auto;width: auto; font-size: 20px; ">{{trans('app.totalCostLabel')}}</span>
                <input id="totalCost" type="text" class="form-control"
                       style="font-size: 20px; width: auto;height:auto;margin: inherit" disabled>
                <input id="totalCostLabel" type="text" class="form-control"
                       style="font-size: 20px; width: 30px;height:auto;margin: inherit" disabled>
            </div>

            <button style="position:absolute; margin-top:20px;left:5%;margin-left: 10px; font-size:20px;width: 300px;height: auto"
                    type="submit" id="addBtn"
                    class="btn btn-outline-primary">{{trans('app.orderLabel')}}</button>

        </form>
    </div>

    <script type="text/x-kendo-template" id="template">
        <div class="product">

            <div class="imageDiv">
                <a href="/product/#:id#"><img class="card-img-top" src="{{asset('/uploads/')}}/#:imageId#"
                                              style="height: 200px; "></a>
            </div>

            <p>#:name#</p>
            <div style="display: ruby-base">
                <div style="text-decoration: line-through; margin-right: 10px;color: lightslategray">#:cost#</div>
                <div class="cost">#:discount#{{trans('app.rub')}}</div>
            </div>
            <p>
                <button style="margin-top: 10px" class="btn btn-success"
                        onclick="deleteProduct('#:id#')">{{trans('app.deleteBtn')}}</button>
            </p>
            <input id="nP#:id#" type="number" name="numberPicker" value="1" min="1" max="#:instock#" step="1"
                   onchange="orderChange('#:id#')">
            #:instock#
        </div>
    </script>

@endsection