@extends('app.layout')

@section('title', 'Game room')

@section('header')
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1">
@endsection

@section('script')

 <script type="text/javascript">
        var ArrowEnum = {
          UP:1,
          DOWN:2,
          RIGHT:3,
          LEFT:4
        }   
        var currentTurn = 0;
        var isStoped = false;     
        var infoTeams = [{"teamName":"Red Oliver","likeColor":"#ff7373","teamKey":"key1"},{"teamName":"Blue Sky","likeColor":"#4286f4","teamKey":"key2"}];
        var colNum = 7;
        var rowNum = 7;
        var initVal = 0;

        var init2DArray = function (xlen, ylen, factoryFn) {
          var ret = []
          for (var x = 0; x < xlen; x++) {
              ret[x] = []
              for (var y = 0; y < ylen; y++) {
                  ret[x][y] = factoryFn(x, y)
              }
          }
          return ret
        }

       var boards = init2DArray(rowNum,colNum, function() {
        return initVal;
       });
    $(document).ready(function(){

        var space = 1;
        var data = JSON.parse('<?php echo $quizzes ?>');
        var count = {{count($quizzes)}};
        for (var r=0; r<rowNum; r++) {
          var col = "";
          for (var c=0; c<colNum; c++) {
            col += "<td><button id='"+space+"' class='draw'>"+space+"</button></td>";
            space++; }
            $("#chessboard").append("<tr>"+col+"</tr>");
        }


        var table = document.getElementById("chessboard");
        if (table != null) {
            for (var i = 0; i < table.rows.length; i++) {
                for (var j = 0; j < table.rows[i].cells.length; j++)
                table.rows[i].cells[j].onclick = function () {
                    tableText(this);
                };
            }
        }

        function cellIsValid(indexQuestion) {
           var index = getCoordinates(indexQuestion);
           return boards[index["row"]][index["col"]] == initVal;
        }

        function tableText(tableCell) {
          var currentQuestion = $(tableCell).find("button")[0].innerText;
          if (!cellIsValid(currentQuestion)) return;
          isStoped = false;
          var random = Math.floor(Math.random() * count) + 0;
          //var quizRandom = data[random];
          var el = data[random];
          var result = el['right_answer'];
          console.log(result);
            //alert(data[random]);\
          var answers = el['answers']['answers'];
          $("#question").text("Question "+currentQuestion+" : "+el['question']);
          $("#table_body").empty();
          for (i = 0; i < answers.length; i++) {
            switch (i) {
              case 0:
                answers[i] = "A : " + answers[i];
                break;
              case 1:
                answers[i] = "B : " + answers[i];
                break;
              case 2:
                answers[i] = "C : " + answers[i];
                break;
              case 3:
                answers[i] = "D : " + answers[i];
                break;
              case 4:
                answers[i] = "E : " + answers[i];
                break;
              case 5:
                answers[i] = "F : " + answers[i];
                break;
              case 6:
                answers[i] = "G : " + answers[i];
                break;
              default:
                answers[i] = "unknown : " + answers[i];
                break;
            }
          }
          for (i = 0; i < answers.length; i++) {
            if (i%2 == 0) {
              if (typeof answers[i+1] === "undefined") {
                $("#table_body").append("<tr><td><p id= 'answer_"+i+"' onclick = 'chooseAnswer("+i+","+result+","+currentQuestion+")'>"+answers[i]+"</p></td></tr>");
              } else {
                $("#table_body").append("<tr><td><p id= 'answer_"+i+"' onclick = 'chooseAnswer("+i+","+result+","+currentQuestion+")'>"+answers[i]+"</p></td><td><p id= 'answer_"+(i+1)+"' onclick = 'chooseAnswer("+(i+1)+","+result+","+currentQuestion+")'>"+answers[i+1]+"</p></td></tr>");
              }
            }
          }

           $("#question_box").css("display", "block");

        }


        configInfoTeam();


    });

    function updateTeamStatus() {
      var teamNames = $(".team_name");
      for (i = 0; i < teamNames.length; i++ ) {
        if (i == currentTurn) {
          $(teamNames[i]).css({'font-size':"30px"});
        }else {
          $(teamNames[i]).css({'font-size':"20px"});
        }
      }
    }

    function getCoordinates(index) {
          var col = (index - 1)%colNum;
          var row = (index - 1 - col)/colNum;
          return {"row":row, "col":col};
       }

    function chooseAnswer(i, result, currentIndex) {

          if (isStoped) return;
          else {
            isStoped = true;
          }
          var answer = i + 1;
          if (answer == result) {
            $("#answer_"+i).css('color', 'red');
            $("#"+currentIndex).css({"background":infoTeams[currentTurn]["likeColor"],"border":"solid 5px "+infoTeams[currentTurn]["likeColor"]});
            updateBoards(currentIndex, infoTeams[currentTurn]["teamKey"]);
            console.log('checkwin');
            console.log(boards);
            if (checkWinner(infoTeams[currentTurn]["teamKey"])) {
              alert("Team "+infoTeams[currentTurn]["teamName"]+"Won !!! ");
            }
          }else {
            $("#answer_"+i).css('color', 'blue');
            nextTurn();
          }
          updateTeamStatus();
    }

    function nextTurn() {
      if (currentTurn < infoTeams.length - 1) {
        currentTurn++;
      }else {
        currentTurn = 0;
      }
    }

    function updateBoards(currentIndex , keyTeam) {
      var index = getCoordinates(currentIndex);
      boards[index["row"]][index["col"]] = keyTeam;
    } 

    function checkWinner(keyTeam) {
      return checkWinnerByVertical(keyTeam) || checkWinnerByHorizontal(keyTeam);  
    }

    function checkWinnerByVertical(keyTeam) {
      var boardsTemporaryV = init2DArray(rowNum,colNum, function() {
        return initVal;
       });
      for (i = 0; i< rowNum; i++) {
        for(j = 0; j< colNum; j++) {
          boardsTemporaryV[i][j] = boards[i][j];
        }
      }
        
      for (j = 0; j< colNum; j++) {
        if (j != (colNum - 1) && boards[0][j] == keyTeam && boards[0][j+1] == keyTeam) {
          console.log("updateV");
          if (findRoadByVertical(0, j+1, keyTeam,boardsTemporaryV, ArrowEnum.RIGHT)) return true;
        }else if (boards[0][j] == keyTeam && boards[1][j] == keyTeam) {
          if (findRoadByVertical(1, j, keyTeam,boardsTemporaryV, ArrowEnum.DOWN)) return true;
        }
      }
    }

    function checkWinnerByHorizontal(keyTeam) {
       var boardsTemporaryH = init2DArray(rowNum,colNum, function() {
        return initVal;
       });
      for (i = 0; i< rowNum; i++) {
        for(j = 0; j< colNum; j++) {
          boardsTemporaryH[i][j] = boards[i][j];
        }
      }
      
      for (i = 0; i< rowNum; i++) {
        if (i != (rowNum - 1) && boards[i][0] == keyTeam && boards[i+1][0] == keyTeam) {
          console.log("updateV");
          if (findRoadByHorizontal(i+1, 0, keyTeam,boardsTemporaryH, ArrowEnum.DOWN)) return true;
        }else if (boards[i][0] == keyTeam && boards[i][1] == keyTeam) {
          if (findRoadByHorizontal(i, 1, keyTeam,boardsTemporaryH, ArrowEnum.RIGHT)) return true;
        }
      }
    }

    function findRoadByHorizontal(indexRow,indexCol,keyTeam, boardsTemporaryH, arrow) {
      //console.log(board);
      console.log('indexCol='+indexCol);
      if (indexCol == (colNum-1)) {
        return true;
      }

      if (indexCol != (colNum - 1) && boardsTemporaryH[indexRow][indexCol + 1] == keyTeam && arrow != ArrowEnum.LEFT && findRoadByHorizontal(indexRow, indexCol + 1, keyTeam, boardsTemporaryH, ArrowEnum.RIGHT)) {
        return true;
      }else if (indexCol != 0 && boardsTemporaryH[indexRow][indexCol - 1] == keyTeam && arrow !=  ArrowEnum.RIGHT && findRoadByHorizontal(indexRow, indexCol - 1, keyTeam, boardsTemporaryH, ArrowEnum.LEFT)) {  
        return true;
      }else if (indexRow != (colNum - 1) && boardsTemporaryH[indexRow + 1][indexCol] == keyTeam && arrow != ArrowEnum.UP && findRoadByHorizontal(indexRow + 1, indexCol, keyTeam, boardsTemporaryH, ArrowEnum.DOWN)) {     
        return true;
      }else if (indexRow !=0 && boardsTemporaryH[indexRow - 1][indexCol] == keyTeam  && arrow != ArrowEnum.DOWN && findRoadByHorizontal(indexRow - 1, indexCol, keyTeam, boardsTemporaryH, ArrowEnum.UP)) {     
        return true;
      }else { 
        return false;
      }      
    }

    function findRoadByVertical(indexRow, indexCol,keyTeam, boardsTemporaryV, arrow) {
      console.log('indexRow='+indexRow);
      if (indexRow == (rowNum - 1)) {
        return true;  
      } 

      if (indexCol != (colNum - 1) && boardsTemporaryV[indexRow][indexCol + 1] == keyTeam && arrow != ArrowEnum.LEFT && findRoadByVertical(indexRow, indexCol + 1, keyTeam, boardsTemporaryV, ArrowEnum.RIGHT)) {
        return true;
      }else if (indexCol != 0 && boardsTemporaryV[indexRow][indexCol - 1] == keyTeam && arrow !=  ArrowEnum.RIGHT && findRoadByVertical(indexRow, indexCol - 1, keyTeam, boardsTemporaryV, ArrowEnum.LEFT)) {  
        return true;
      }else if (indexRow != (colNum - 1) && boardsTemporaryV[indexRow + 1][indexCol] == keyTeam && arrow != ArrowEnum.UP && findRoadByVertical(indexRow + 1, indexCol, keyTeam, boardsTemporaryV, ArrowEnum.DOWN)) {     
        return true;
      }else if (indexRow !=0 && boardsTemporaryV[indexRow - 1][indexCol] == keyTeam  && arrow != ArrowEnum.DOWN && findRoadByVertical(indexRow - 1, indexCol, keyTeam, boardsTemporaryV, ArrowEnum.UP)) {     
        return true;
      }else { 
        return false;
      }      
    }

    function configInfoTeam() {
        $($(".team_name")[0]).text(infoTeams[0]["teamName"]);
        $($(".team_name")[1]).text(infoTeams[1]["teamName"]);
        updateTeamStatus();
    }

    function updateScoreofTeam(teamIndex, score) {
      $($(".team_score")[teamIndex]).text(score);
    }

  </script>

@endsection

@section('page_content')
      <div class="container">
        <div class="row">
          <div class="col-sm-6">
            <table id='chessboard'>
            </table>
          </div>
          <div class="col-sm-6">
            @include('app.question_box')
          </div>
        </div>
        <br>
        <div class="teams">
          @include('app.teams_box')
        </div>
      </div>
@endsection
