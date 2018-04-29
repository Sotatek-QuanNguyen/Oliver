
@extends('app.layout')

@section('title', 'Register Team Form')

@section('header')
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <link type="text/css" rel="stylesheet" href="css/background.css" /> 
  <meta name="viewport" content="width=device-width, initial-scale=1">
@endsection

@section('script')
 <script type="text/javascript">
  var countTeamCurrent = 0;
  $(document).ready(function() {

    $("#add_team").on('click', function(event) {
            handleAddTeam(this);
    });

     $("#remove_team").on('click', function(event) {
            handleRemoveTeam(this);
    });

    $('#sizeTemp').change(function() {
      $('#size').val($(this).val());
    });

  }); 

function handleAddTeam() {
  var teamView = "<div id='teamI_"+(countTeamCurrent+1)+"'class='col-lg-3'> <div class='boxed-grey'> <div class='avatar text-center'> <img src='../imgs/human.png' width='140' height='auto'> </div> <input type='text' class='form-control nameInput' placeholder='Enter Team Name' name=info[]> </div> </div>";
  $($("#team-box .row")[parseInt(countTeamCurrent/4)]).append(teamView);
  countTeamCurrent ++;
   if (countTeamCurrent%4 == 0) {
    $("#team-box").append("<br><div class='row'></div>");
  }
}

function handleRemoveTeam() {
  if(countTeamCurrent > 0) {
    $("#teamI_"+countTeamCurrent).remove();
    countTeamCurrent --;
  }
}



 </script>
@endsection

@section('page_content')
<div class="container">

  <div class="background1">
    @include('app.background')
  </div>
  <div class="mainView">
     <div class="row">
    <div class="col-sm-6">
      <h2>Register Teams Form</h2>
    </div>
    <div class="col-sm-3"></div>
    <div class="col-sm-1">
       <button id="add_team" class="btn btn-success">
        <span class="glyphicon glyphicon-plus"></span> Add  
      </button>
    </div>
    <div class="col-sm-1">
      <button id="remove_team" class="btn btn-danger">
      <span class="glyphicon glyphicon-minus"></span> Remove  
      </button>
    </div>
    <div class="col-sm-1">
       <input id="sizeTemp" type='text' class='form-control'  placeholder='Size'  value="6">
    </div>
   
  </div>
  <br>
  <form id="login-box" method="post" action="/into">
      {{ csrf_field() }}
    <div id="team-box">
      <div class="row">
        
      </div>
    </div>
    <br>
    <button id="submit" type="submit" class="btn btn-default btn-block btn-custom">Submit</button>
    <input type='text' id="size" hidden="true" class='form-control'  name='size' value="6">
  </div>
 
  </form>
</div>
@endsection
