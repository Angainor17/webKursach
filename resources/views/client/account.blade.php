@extends('client.accountFrame',['title'=> trans('app.accountPage')])

@section('innerContent')
    <script>

        $(document).ready(function () {
            $('#i1').tooltipster();
            $('#i2').tooltipster();
            $('#i3').tooltipster();

            menuActive();
            initFields();

            $("#form").submit(function (event) {
                event.preventDefault();
                refreshAccData();
                return false;
            });
        });

        function initFields() {
            $("#inputGender").val("{{$gender}}");

            $('#type1').prop('checked', "{{$trainingType}}" === "1");
            $('#type2').prop('checked', "{{$trainingType}}" === "2");
            $('#type3').prop('checked', "{{$trainingType}}" === "3");

            $('#typeBody1').prop('checked', "{{$bodyType}}" === "1");
            $('#typeBody2').prop('checked', "{{$bodyType}}" === "2");
            $('#typeBody3').prop('checked', "{{$bodyType}}" === "3");

            $("[name='true']").prop("checked", true);
            $("[name='false']").prop("checked", false);
        }

        function menuActive() {
            $('#account').addClass("active");
        }

        function refreshAccData() {
            var age = $("#inputAge").val();
            var name = $("#inputName").val();
            var email = $("#inputEmail").val();
            var weight = $("#inputWeight").val();
            var gender = $("#inputGender").val();
            var trainingType = 0;
            if ($('#type1').prop('checked')) {
                trainingType = 1;
            }

            if ($('#type2').prop('checked')) {
                trainingType = 2;
            }

            if ($('#type3').prop('checked')) {
                trainingType = 3;
            }

            var bodyType = 0;
            if ($('#typeBody1').prop('checked')) {
                bodyType = 1;
            }

            if ($('#typeBody2').prop('checked')) {
                bodyType = 2;
            }

            if ($('#typeBody3').prop('checked')) {
                bodyType = 3;
            }
            var trainingSchedule = "";

            for (var i = 1; i < 8; i++) {
                if ($('#day' + i).prop('checked')) {
                    trainingSchedule += "" + i;
                }
            }
            $.ajax({
                url: "/accountRefresh",
                type: "POST",
                dataType: "json",
                data: {
                    trainingSchedule: trainingSchedule,
                    age: age,
                    gender: gender,
                    weight: weight,
                    trainingType: trainingType,
                    bodyType: bodyType,
                    name: name,
                    email: email
                }
            });
        }

    </script>

    <div style="margin-bottom: 20px; margin-right: 200px; margin-left: 100px">
        <form id="form" enctype="multipart/form-data">

            <div class="input-group" style="margin-top: 20px">
                <span class="input-group-addon" style="width: auto">{{trans('app.nameLabel')}}</span>

                <input type="text" class="form-control is-valid" value="{{$name}}" id="inputName" pattern=".{1,100}"
                       required>
            </div>

            <div class="input-group" style="margin-top: 20px">
                <span class="input-group-addon" style="width: auto">{{trans('app.emailAddressLabel')}}</span>

                <input type="text" class="form-control is-valid" value="{{$email}}" id="inputEmail" pattern=".{1,100}"
                       required style="width: 500px">
            </div>


            <div class="form-group row">
                <div class="input-group" style="margin-top: 20px">
                    <span class="input-group-addon" style="width: auto">{{trans('app.age')}}</span>
                    <select class="form-control" style="width: 140px" id="inputAge">
                        @include("spinner",['selection'=>$age])
                    </select>
                </div>
            </div>


            <div class="input-group" style="margin-top: 20px; width: 50%">
                <span class="input-group-addon" style="width: auto">{{trans('app.weight')}}</span>

                <input type="text" class="form-control is-valid" value="{{$weight}}" id="inputWeight" pattern=".{1,100}"
                       required>
            </div>

            <div class="input-group" style="margin-top: 20px">
                <span class="input-group-addon" style="width: auto">{{trans('app.gender')}}</span>
                <select id="inputGender" class="form-control " required>
                    <option value="1" selected>{{trans('app.male')}}</option>
                    <option value="2">{{trans('app.female')}}</option>
                </select>
            </div>

            <div class="tooltip_templates" style="display: none">
                <div id="tooltip_content_ecto" style="word-wrap: break-word;max-width: 200px">
                    <img src="{{asset("/default/ecto.jpg")}}"/> {{trans('app.ecto_text')}}
                </div>
            </div>

            <div class="tooltip_templates" style="display: none;width: 400px">
                <div id="tooltip_content_mezo" style="word-wrap: break-word;max-width: 200px">
                    <img src="{{asset("/default/mezo.jpg")}}"/> {{trans('app.mezo_text')}}</div>
            </div>

            <div class="tooltip_templates" style="display: none">
                <div id="tooltip_content_endo" style="word-wrap: break-word;max-width: 200px">
                    <img src="{{asset("/default/endo.jpg")}}"/> {{trans('app.endo_text')}}
                </div>
            </div>

            <div style="margin-top: 35px">
                <div class="form-check form-check-inline">
                    @for($i=1; $i<8; $i++)
                        <label class="form-check-label">
                            <input id="day{{$i}}" type="checkbox"
                                   name="{{\App\Http\BusinessModel\WeekDay::isSelected($trainingSchedule,$i)}}"
                                   class="trainTypeCb" value="{{$i}}">{{\App\Http\BusinessModel\WeekDay::getString($i)}}
                        </label>
                    @endfor
                </div>
            </div>

            <div style="margin-top: 30px">
                <div class="form-check form-check-inline">
                    <label class="form-check-input">
                        <input id="type1" type="radio" class="trainTypeCb" name="inlineRadioOptions"
                               value="1" {{\App\Http\BusinessModel\TrainingType::isSelected($trainingType,'1')}}>
                        {{trans('app.mass')}}
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <label class="form-check-input">
                        <input id="type2" type="radio" name="inlineRadioOptions"
                               value="2" {{\App\Http\BusinessModel\TrainingType::isSelected($trainingType,'2')}}> {{trans('app.dry')}}
                    </label>
                </div>


                <div class="form-check form-check-inline">
                    <label class="form-check-input">
                        <input id="type3" type="radio" name="inlineRadioOptions"
                               value="3" {{\App\Http\BusinessModel\TrainingType::isSelected($trainingType,'3')}}>
                        {{trans('app.stamina')}}
                    </label>
                </div>
            </div>
            <div style="margin-top: 30px">
                <div class="form-check form-check-inline">
                    <label id="i1" class="form-check-input" data-tooltip-content="#tooltip_content_ecto">
                        <input id="typeBody1" type="radio" name="inlineRadioOptions1" value="1">
                        {{trans('app.ectomorph')}}
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <label id="i2" class="form-check-input" data-tooltip-content="#tooltip_content_endo">
                        <input id="typeBody2" type="radio" name="inlineRadioOptions1"
                               value="2"> {{trans('app.endomorph')}}
                    </label>
                </div>

                <div class="form-check form-check-inline">
                    <label id="i3" class="form-check-input" data-tooltip-content="#tooltip_content_mezo">
                        <input id="typeBody3" type="radio" name="inlineRadioOptions1" value="3">
                        {{trans('app.mezomorph')}}
                    </label>
                </div>
            </div>

            <button type="submit" id="submit" class="btn btn-outline-primary"
                    style="width:200px;border:1px solid darkgray; margin-left: 10px">{{trans('app.saveBtn')}}</button>
        </form>
    </div>

@endsection