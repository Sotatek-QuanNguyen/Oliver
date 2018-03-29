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
        var team1Key = "key1";
        var team2Key = "key2";
        var colNum = 6;
        var rowNum = 6;
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

        function tableText(tableCell) {
          var currentQuestion = $(tableCell).find("button")[0].innerText;
          var random = Math.floor(Math.random() * count) + 0;
          //var quizRandom = data[random];
          var el = data[random];
          var result = el['right_answer'];
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


    });

    function getCoordinates(index) {
          var col = (index - 1)%colNum;
          var row = (index - 1 - col)/colNum;
          return {"row":row, "col":col};
       }

    function chooseAnswer(i, result, currentIndex) {
          var answer = i + 1;
          if (answer == result) {
            $("#answer_"+i).css('color', 'red');
            $("#"+currentIndex).css({"background":"#f4424e","border":"solid 5px #f4424e"});
            updateBoards(currentIndex, team1Key);
            console.log('checkwin');
            console.log(checkWinner(team1Key));
          }else {
            $("#answer_"+i).css('color', 'blue');
          }
    }

    function updateBoards(currentIndex , keyTeam) {
      var index = getCoordinates(currentIndex);
      boards[index["row"]][index["col"]] = keyTeam;
    } 

    function checkWinner(keyTeam) {
      return checkWinnerByVertical(keyTeam);
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
        if (boards[0][j] == keyTeam) {
          console.log("updateV");
          if (findRoadByVertical(0,0,0, j, keyTeam,boardsTemporaryV)) return true;
        }
      }

      return false;
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
        if (boards[i][0] == keyTeam) {
          console.log("updateH");
          if (findRoadByHorizontal(i, 0, keyTeam,boardsTemporaryH)) return true;
        }
      }
      return false;
    }

    function findRoadByHorizontal(indexRow,indexCol,keyTeam, boardsTemporaryH) {
      //console.log(board);
      console.log('indexCol='+indexCol);
      if (indexCol == (colNum-1)) {
        return true;
      }

      if (indexRow == (rowNum - 1) || indexRow == 0) return false;

      if (boardsTemporaryH[indexRow][indexCol + 1] == keyTeam) {
        boardsTemporaryH[indexRow][indexCol] = initVal;
        return findRoadByHorizontal(indexRow, indexCol + 1, keyTeam, boardsTemporaryH);
      }else if ((boardsTemporaryH[indexRow + 1][indexCol] == keyTeam)) {
        boardsTemporaryH[indexRow][indexCol] = initVal;
        return findRoadByHorizontal(indexRow + 1, indexCol, keyTeam, boardsTemporaryH);
      }else if ((boardsTemporaryH[indexRow - 1][indexCol] == keyTeam)) {
        boardsTemporaryH[indexRow][indexCol] = initVal;
        return findRoadByHorizontal(indexRow - 1, indexCol, keyTeam, boardsTemporaryH);
      }      
    }

    function findRoadByVertical(priorRow, priorCol, indexRow, indexCol,keyTeam, boardsTemporaryV) {
      console.log('indexRow='+indexRow);
      if (indexRow == (rowNum - 1)) {
        return true;
      }

      if (indexCol == (colNum - 1) || indexCol == 0) { 
        boardsTemporaryV[priorRow][priorCol] = keyTeam;
        return false;
      }
      if (boardsTemporaryV[indexRow][indexCol + 1] == keyTeam) {
        boardsTemporaryV[indexRow][indexCol] = initVal;
        return findRoadByVertical(indexRow,indexCol,indexRow, indexCol + 1, keyTeam, boardsTemporaryV);
      }else if ((boardsTemporaryV[indexRow][indexCol - 1] == keyTeam)) {
        boardsTemporaryV[indexRow][indexCol] = initVal;
        return findRoadByVertical(indexRow,indexCol,indexRow, indexCol - 1, keyTeam, boardsTemporaryV);
      }else if ((boardsTemporaryV[indexRow + 1][indexCol] == keyTeam)) {
        boardsTemporaryV[indexRow][indexCol] = initVal;
        return findRoadByVertical(indexRow,indexCol,indexRow + 1, indexCol, keyTeam, boardsTemporaryV);
      }else {
        boardsTemporaryV[priorRow][priorCol] = keyTeam;
        return false;
      }      
    }

  </script>

@endsection

@section('page_content')
      <div class="container row">
        <div class="col-sm-6">
          <table id='chessboard'>
          </table>
        </div>
        <div class="col-sm-6">
          @include('app.question_box')
        </div>
          
      
      </div>
@endsection
