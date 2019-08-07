<!DOCTYPE html>
<html>
<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
    }

    h3 {
        color: #3B5998;
        font-family: Arial;
    }

    .content {
        padding: 30px;
        background-color: #F7F7F7;
        margin-bottom: 30px;
        text-align: center;
    }

</style>
<body>

<div class="content">
    <h2>New Candidate for <{!! $vacancy !!}></h2>
    <h3>Name of candidate {!! $name !!}</h3>
    <h3>Job title {!! $title !!}</h3>


    <a href={!! $file !!}>Download CV of candidate</a>
</div>


{{--<form >--}}
{{--<div class="container">--}}
{{--<h2>Subscribe to our Newsletter</h2>--}}
{{--<p>Click the below button to subscribe.</p>--}}
{{--</div>--}}

{{--<div class="container" style="background-color:white">--}}

{{--</div>--}}


{{--</form>--}}

</body>
</html>