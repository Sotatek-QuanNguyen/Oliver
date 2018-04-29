@extends('app.layout')

@section('title', 'Game room')

@section('header')
  <script src="js/sweetalert.min.js"></script>
  <script src="js/popper.min.js"></script>
  <link type="text/css" rel="stylesheet" href="css/background1.css" /> 
  <link type="text/css" rel="stylesheet" href="css/border_style.css" /> 
  <script src="js/background1.js"></script>

  <meta name="viewport" content="width=device-width, initial-scale=1">
@endsection

@section('script')

 <script type="text/javascript"> 
  const ArrowEnum = {
          UP:1,
          DOWN:2,
          RIGHT:3,
          LEFT:4
  }

  const Level = {
        EASY:"D",
        MEDIUM:"TB",
        HARD:"K"
  }
        var cycleTimes = 1;
        var currentTurn = 0;
        var isStoped = false;     
        var infoTeams = [];
        var colNum = 6;
        var rowNum = 6;
        var initVal = 0;
        var initData;
        var currentQuestion;
        var currentLevel;
        const cycleTimeMax = 5;
        var TEAMBOX = "<div class='team_box'> <div class='row'> <div class='col-centered'> <div class='avatar_team'> <img src='../imgs/chibi/chibi1.png'> </div> <p class='team_name'></p> <p class='team_score'>100</p> </div></div> </div>"

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

       function getData(info) {
          var data = [];
          for( i = 0; i<info.length; i++) {
            var teamName = info[i];
            var likeColor = getRandomColor(i);
            var teamKey = "teamI_" + i;
            var teamScore = 0;
            data.push({"teamName":teamName, "likeColor": likeColor, "teamKey":teamKey, "teamScore":teamScore});
          }
         return data;
        }

      function getRandomColor(index) {
        var colors = ["#f49242","#b563d3","#a51d7f","#5fd33f","#c15124","#a80d08","#6bbc1a","#06891c","#1cdbc7","#04d2f2","#0a55d6","#b0a2db","#c48396"];
        return colors[i];
      }

    $(document).ready(function(){
      Array.prototype.max = function() {
        return Math.max.apply(null, this);
      };

      $('body').css('color', 'black');
        var names = '<?php echo $infos ?>';
        infoTeams = getData(names.split(","));
        var space = 1;
        var objects = <?php echo json_encode(array_merge($quizzes,$questions)) ?>;
        colNum = <?php echo $size?>;
        rowNum = <?php echo $size?>;
        initData = shuffle(objects);
        for (var r=0; r<rowNum; r++) {
          var col = "";
          for (var c=0; c<colNum; c++) {
            col += "<td><button id='"+space+"' class='draw'>"+space+"<p class='level'>TB<p></button> </td>";
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
        $('#back-button').on('click', function(event) {
            window.history.back();
        });

        $('#answer_button').on('click', function(event) {
            handleAnnswerBtn(this);
        });

        $('#answer_correct').on('click', function(event) {
            handleRightAnswer(this);
        });

        $('#answer_incorrect').on('click', function(event) {
            handleWrongAnswer(this);
        });

        configInfoTeam();

    });

    function handleAnnswerBtn(el) {
        if(el.innerText == "Show Answer") {
          $("#answer").css("display", "block");
          $("#answer_correct").css("display", "block");
          $("#answer_incorrect").css("display", "block");
          el.innerText = "Hide Answer";
        }else {
          $("#answer").css("display", "none");
          $("#answer_correct").css("display", "none");
          $("#answer_incorrect").css("display", "none");
          el.innerText = "Show Answer";
        }
    }

    function generateLevel() {
      var levels = $(".level");
      for ( i = 0; i < levels.length; i++) {
         const j = Math.floor(Math.random() * (10 + 1));
         if (j%6 < 3) {
          $(levels[i]).text('D');
         }else if (j%6 < 5) {
          $(levels[i]).text('TB');
         }else {
          $(levels[i]).text('K');
         }
      }
    }

    function checkWinByCycleTimes() {
      if (cycleTimes == cycleTimeMax) {
        var arr = [];
        var messageText = "";
        for (i = 0; i < infoTeams.length; i++) {
            arr.push(infoTeams[i]['teamScore']);
        }
        var maxScore = arr.max();
        var data = infoTeams.filter(function(item) { 
              return item['teamScore'] == maxScore;
        });

        for (i = 0 ; i < data.length; i++) {
          messageText = messageText + data[i]["teamName"] + " ";
        }

        debugger;
        showAlert(messageText);
      }
    }

    function handleRightAnswer(el) {
       $("#answer_"+i).css('color', 'red');
       play('correct');
       infoTeams[currentTurn]["teamScore"] = parseInt(infoTeams[currentTurn]["teamScore"]) + parseScore();
          $("#"+currentQuestion).css({"background":infoTeams[currentTurn]["likeColor"],"border":"solid 5px "+infoTeams[currentTurn]["likeColor"]});
          updateBoards(infoTeams[currentTurn]["teamKey"]);
          
          if (checkWinner(infoTeams[currentTurn]["teamKey"])) {
            play('victory');
            showAlert(infoTeams[currentTurn]["teamName"]);
          }
          updateTeamStatus();
    }

    function handleWrongAnswer(el) {
        play('fail');
        $("#answer_"+i).css('color', 'blue');
        nextTurn();
        updateTeamStatus();
    }

    function cellIsValid(indexQuestion) {
           var index = getCoordinates(indexQuestion);
           return boards[index["row"]][index["col"]] == initVal;
    }

    function configQuizzBox(el) {
      var result = el['right_answer'];

            //alert(data[random]);\
            
          var answers = el['answers'];
          $("#question").text("Question "+currentQuestion+" : "+el['question']);
          $("#description_answer").text("Explain the answer:" +el['description_answer']);
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
                $("#table_body").append("<tr><td><p id= 'answer_"+i+"' onclick = 'chooseAnswer("+i+","+result+")'>"+answers[i]+"</p></td></tr>");
              } else {
                $("#table_body").append("<tr><td><p id= 'answer_"+i+"' onclick = 'chooseAnswer("+i+","+result+")'>"+answers[i]+"</p></td><td><p id= 'answer_"+(i+1)+"' onclick = 'chooseAnswer("+(i+1)+","+result+")'>"+answers[i+1]+"</p></td></tr>");
              }
            }
          }

           $("#question_box").css("display", "block");

   
    }

    function configQuestionBox(el) {
       $("#question").text("Question "+currentQuestion+" : "+el['question']);
       $("#answer").text("Answer : "+el['answer']);
    }

    function tableText(tableCell) {
      $("#description_answer").css({"display":"none"});
          var buttonCurrent = $(tableCell).find("button")[0];
          currentQuestion = buttonCurrent.id;
          currentLevel = $(buttonCurrent).find("p").first().text();
          if (!cellIsValid(currentQuestion)) return;
          isStoped = false;   

          var el = getElementByArray(initData, currentLevel);
          initData = initData.filter(function(item) { 
              return item !== el;
          })
          if (el['type'] == 'question') {
            $("#answers_box").css("display", "none");
            $("#show_answer").css("display", "block");
            configQuestionBox(el);
            $("#answer").css("display", "none");
            $("#answer_correct").css("display", "none");
            $("#answer_incorrect").css("display", "none");
            $("#answer_button").innerText = "Show Answer";
          }else {
            $("#answers_box").css("display", "block");
            $("#show_answer").css("display", "none");            
            configQuizzBox(el);
          }
        }

    function getElementByArray(initData, level) {
      var levelValue;
      if (level == Level.EASY) {
        levelValue = 1;
      }else if (level == Level.MEDIUM) {
        levelValue = 2;
      }else {
        levelValue = 3;
      }

      for(i = 0; i < initData.length; i++ ) {
          if(initData[i]['level'] == levelValue) {
            return initData[i];
          }
      }
    }

    function shuffle(a) {
      for (let i = a.length - 1; i > 0; i--) {
          const j = Math.floor(Math.random() * (i + 1));
          [a[i], a[j]] = [a[j], a[i]];
      }
      return a;
    }


    function updateTeamStatus() {
      var teamNames = $(".team_name");
      var avatarTeams = $(".avatar_team");
      var teamScores = $(".team_score");
      for (i = 0; i < teamNames.length; i++ ) {
        var team = infoTeams.filter(function(item) { 
              return item['teamName'] == teamNames[i].innerText;
          })
        $(teamScores[i]).text(team[0]["teamScore"]);
        if (i == currentTurn) {
          $(teamNames[i]).css({'font-size':"27px"});
          $(avatarTeams[i]).css({"opacity":"1.0"});
       
        }else {
          $(teamNames[i]).css({'font-size':"20px"});
          $(avatarTeams[i]).css({"opacity":"0.4"});
        }
      }
    }

    function getCoordinates(index) {
          var col = (index - 1)%colNum;
          var row = (index - 1 - col)/colNum;
          return {"row":row, "col":col};
       }

    function chooseAnswer(i, result) {
          $("#description_answer").css({"display":"block"});
          if (isStoped) return;
          else {
            isStoped = true;
          }
          var answer = i + 1;
          if (answer == result) {
            play('correct');
            $("#answer_"+i).css('color', 'red');
            infoTeams[currentTurn]["teamScore"] = parseInt(infoTeams[currentTurn]["teamScore"]) + parseScore(); 
            $("#"+currentQuestion).css({"background":infoTeams[currentTurn]["likeColor"],"border":"solid 5px "+infoTeams[currentTurn]["likeColor"]});
            $("descrition_answer").text("true");
            updateBoards(infoTeams[currentTurn]["teamKey"]);
            if (checkWinner(infoTeams[currentTurn]["teamKey"])) {
              //alert("Team "+infoTeams[currentTurn]["teamName"]+"Won !!! ");
              showAlert(infoTeams[currentTurn]["teamName"]);
            }
          }else {
            play('fail');
            $("#answer_"+i).css('color', 'blue');
            nextTurn();
          }
          updateTeamStatus();
    }

    function parseScore() {
       var levelValue;
      if (currentLevel == Level.EASY) {
        levelValue = 1;
      }else if (currentLevel == Level.MEDIUM) {
        levelValue = 2;
      }else {
        levelValue = 3;
      }
      return levelValue;
    }

    function nextTurn() {
      if (currentTurn < infoTeams.length - 1) {
        currentTurn++;
      }else {
        currentTurn = 0;
        cycleTimes++;
      }
      checkWinByCycleTimes();
    }

    function updateBoards(keyTeam) {
      var index = getCoordinates(currentQuestion);
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
      
          if (findRoadByHorizontal(i+1, 0, keyTeam,boardsTemporaryH, ArrowEnum.DOWN)) return true;
        }else if (boards[i][0] == keyTeam && boards[i][1] == keyTeam) {
          if (findRoadByHorizontal(i, 1, keyTeam,boardsTemporaryH, ArrowEnum.RIGHT)) return true;
        }
      }
    }

    function findRoadByHorizontal(indexRow,indexCol,keyTeam, boardsTemporaryH, arrow) {
    
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

    function generateAvartar() {
      var i = Math.floor(Math.random() * 10);  
     
      return "../imgs/chibi/chibi"+i+".png";
    }

    function createLayoutTeams() {
        var teams = $(".teams");
       for (i = 0; i < infoTeams.length; i++) {
            $(teams[i%2]).append(TEAMBOX);
       }
    }

    function configInfoTeam() {
      createLayoutTeams();  
      for (i = 0; i < infoTeams.length; i++) {
        $($(".team_name")[i]).text(infoTeams[i]["teamName"]);
        $($(".avatar_team")[i]).css({"display":"block"});
        $($(".team_name")[i]).css({"color":infoTeams[i]['likeColor']});
        $($(".avatar_team")[i]).find("img").attr("src",generateAvartar());
      }
      generateLevel();
      updateTeamStatus();
    }

    function play(id) {
      var audio = document.getElementById(id);
          audio.currentTime = 0
          audio.play();
    }

    function showAlert(teamWin) {
        swal("Congratulations!", "Team "+teamWin+" Won !!!");
    }

  </script>

@endsection

@section('page_content')
      <div class="background">
           <div id="particles-js"></div>
      </div>
      <div class="container">
        <div class="mainView">
            <div class="row">
              <div class="col-3">
                <div class="teams"></div>
              </div>

              <div class="col-6">
                <table id='chessboard'></table>
              </div>

              <div class="col-3">
                <div class="teams"></div>
              </div>

            </div>
            <br>
            <div class="row">
              <div class="col-12">
                 @include('app.question_box')
              </div>
            </div>
            </div>  
        </div>
       <audio id="victory" src="../sounds/victory.mp3" preload="auto"></audio>
       <audio id="correct" src="../sounds/correct.mp3" preload="auto"></audio>
       <audio id="fail" src="../sounds/fail.mp3" preload="auto"></audio>
      
      
@endsection
