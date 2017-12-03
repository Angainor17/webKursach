@extends("layouts.app", ["title"=>"Product"])

@section("content")

    <style>
        .articleInfo {
            margin-left: 15%;
            margin-right: 15%;
            background-color: #cfcfbf;
            text-align: center;
            padding-left: 60px;
            padding-right: 60px;
            padding-top: 80px;
            padding-bottom: 80px;
        }

        .articleInfo img {
            height: 250px;
            border: 2px solid #0c5460;
        }

        .articleTitle {
            font-size: 20px;
        }

        .articleText {
            text-align: left;
            font-size: 16px;
        }

        .articleDate {
            float: left;
            font-size: 15px;
        }

    </style>

    <div class="articleInfo">
        <div style="font-size: 30px; text-align: left; margin-bottom: 30px; color: red">
            @if($item->type == 1) News @endif

            @if($item->type == 2) Auction @endif
        </div>

        <img class="card-img-top" src="{{asset('/uploads/')}}/{{$item->imageId}}">

        <p class="articleTitle">{{$item->title}}</p>

        <p class="articleDate">{{$item->date}}</p>

        <div class="articleText"><br>{{$item->full}}</div>
    </div>

@endsection