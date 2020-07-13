(function() {
    // Elements
    var game = document.getElementById('game');
    var boxes = [];
    var gameMessages = document.getElementById('game-messages');
    var playerOneScoreCard = document.getElementById('player-one-score');
    var playerTwoScoreCard = document.getElementById('player-two-score');
    var side_length = document.getElementById('side_length');

    // Vars
    var context = { 'player1' : 'x', 'player2' : 'o' };
    var board = [];

    var playerOneScore = 0;
    var playerTwoScore = 0;

    var turns;
    var currentContext;

    // Constructor
    var Board = function(side_length) {
        turns = 0;

        // Get current context
        currentContext = computeContext();

        // Setup n x n board
        for(var i = 0; i < side_length; i++) {
            board[i] = new Array(side_length);
        }

        // create box and bind events
        for(var i = 0; i < side_length;  i++) {
            for( var j = 0; j < side_length; j++) {
                var li = document.createElement("li");
                li.setAttribute('data-pos',`${i},${j}`);
                game.appendChild(li);
                li.addEventListener('click', clickHandler, false);
                boxes.push(li);
            }
        }
        game.style.width = side_length * 100 + "px";
        game.style.height = side_length * 100 + "px";
    }

    //Keeps track of player's turn
    var computeContext = function() {
        return (turns % 2 == 0) ? context.player1 : context.player2;
    }

    // Bind the dom element to the click callback
    var clickHandler = function() {
        this.removeEventListener('click', clickHandler);

        this.className = currentContext;
        this.innerHTML = currentContext;

        var pos = this.getAttribute('data-pos').split(',');

       // board[pos[0]][pos[1]] = computeContext() == 'x' ? 1 : 0;
        board[pos[0]][pos[1]] = computeContext();
        if(checkStatus(pos[0],pos[1],board[pos[0]][pos[1]])) {
            gameWon();
        }
        turns++;
        currentContext = computeContext();
    }


    // Check to see if player has won
    var checkStatus = function(x,y,computeContext) {
        var used_boxes = 0;

        var column_total = 0;
        //check columns
        for(var columns = 0; columns < board[x].length; columns++) {
            if(board[x][columns] == computeContext) {
                column_total++;
            }
        }

        //check rows
        var row_total = 0;
        for(var row = 0; row < board[y].length; row++) {
            if(board[row][y] == computeContext) {
                row_total++;
            }
        }
        console.log(computeContext,column_total,row_total,board[x],board[y]);
        if(column_total == 3 || row_total == 3) {
            return true;
        }
    }
    var gameWon = function() {
        clearEvents();

        // show game won message
        gameMessages.className = 'player-' + computeContext() + '-win';

        // update the player score
        switch(computeContext()) {
            case 'x':
                playerOneScoreCard.innerHTML = ++playerOneScore;
                break;
            case 'o':
                playerTwoScoreCard.innerHTML = ++playerTwoScore;
        }
    }
    // Tells user when game is a draw.
    var gameDraw = function() {
        gameMessages.className = 'draw';
        clearEvents();
    }

    // Stops user from clicking empty cells after game is over
    var clearEvents = function() {
        for(var i = 0; i < boxes.length; i++) {
            boxes[i].removeEventListener('click', clickHandler);
        }
    }

   //initial setup
    game && Board(5);

    //when box size change, init variables
    side_length.onchange = function () {
        //clear the exist data
        game.innerHTML = "";
        boxes = [];
        board = [];
        gameDraw();
        if(this.value >= 3) Board(this.value);
    }

})();
