@extends('client.accountFrame',['title'=> trans('app.accountPage')])

@section('innerContent')
    <script>
        $(document).ready(function () {
            menuActive();
        });

        function menuActive() {
            $('#account').addClass("active");
        }

    </script>
    account
@endsection