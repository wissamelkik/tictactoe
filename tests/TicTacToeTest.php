<?php namespace Tests;

use Tests\TestCase;
use App\Libraries\TicTacToe;

class TicTacToeTest extends TestCase
{
    /**
     * This function tests the first bot move if the bot player is "X"
     *
     * @return void
     */
    public function testFirstBotMoveIfXPlayer()
    {
        $bot_player = 'X';
        $board = [['', '', ''], ['', '', ''], ['', '', '']];
        // Play the game
        $obj = new TicTacToe();
        $obj->init($board, $bot_player);
        $result = $obj->play();
        $this->assertEquals($result['board'][1][1], 'X');
    }

    /**
     * This function tests the first bot move if the bot player is "O"
     *
     * @return void
     */
    public function testFirstBotMoveIfOPlayer()
    {
        // Case 1: X is in the corner
        $bot_player = 'O';
        $boards = [];
        $boards[] = [['X', '', ''], ['', '', ''], ['', '', '']];
        $boards[] = [['', '', 'X'], ['', '', ''], ['', '', '']];
        $boards[] = [['', '', ''], ['', '', ''], ['X', '', '']];
        $boards[] = [['', '', ''], ['', '', ''], ['', '', 'X']];
        for ($i=0; $i<count($boards); $i++) {
            // Play the game
            $obj = new TicTacToe();
            $obj->init($boards[$i], $bot_player);
            $result = $obj->play();
            $this->assertEquals($result['board'][1][1], 'O');
        }

        // Case 2: X is in the edge. O should pick the center
        $bot_player = 'O';
        $boards = [];
        $boards[] = [['', 'X', ''], ['', '', ''], ['', '', '']];
        $boards[] = [['', '', ''], ['X', '', ''], ['', '', '']];
        $boards[] = [['', '', ''], ['', '', 'X'], ['', '', '']];
        $boards[] = [['', '', ''], ['', '', ''], ['', 'X', '']];
        for ($i=0; $i<count($boards); $i++) {
            // Play the game
            $obj = new TicTacToe();
            $obj->init($boards[$i], $bot_player);
            $result = $obj->play();
            $this->assertEquals($result['board'][1][1], 'O');
        }

        // Case 3: X is in the center. O should pick a corner
        $bot_player = 'O';
        $board = [['', '', ''], ['', 'X', ''], ['', '', '']];
        $possibilities = [];
        $possibilities[] = [['O', '', ''], ['', 'X', ''], ['', '', '']];
        $possibilities[] = [['', '', 'O'], ['', 'X', ''], ['', '', '']];
        $possibilities[] = [['', '', ''], ['', 'X', ''], ['O', '', '']];
        $possibilities[] = [['', '', ''], ['', 'X', ''], ['', '', 'O']];
        // Play the game
        $obj = new TicTacToe();
        $obj->init($board, $bot_player);
        $result = $obj->play();
        $this->assertContains($result['board'], $possibilities);
    }

    /**
     * This function tests the defensive move
     *
     * @return void
     */
    public function testDefensiveMove()
    {
        $bot_player = 'O';
        $boards = [];
        $assertion_o_position = [];
        // Top horizontal line
        $boards[] = [['X', '', 'X'], ['', 'O', ''], ['', '', '']];
        $assertion_o_position[] = [0, 1];
        // Left vertical line
        $boards[] = [['X', '', ''], ['', 'O', ''], ['X', '', '']];
        $assertion_o_position[] = [1, 0];
        // Right vertical line
        $boards[] = [['', '', 'X'], ['', 'O', ''], ['', '', 'X']];
        $assertion_o_position[] = [1, 2];
        // Bottom horizontal line
        $boards[] = [['', '', ''], ['', 'O', ''], ['X', '', 'X']];
        $assertion_o_position[] = [2, 1];
        // Diagonal: top left to bottom right
        $boards[] = [['', 'O', ''], ['', 'X', ''], ['', '', 'X']];
        $assertion_o_position[] = [0, 0];
        // Diagonal: top right to bottom left
        $boards[] = [['', 'O', ''], ['', 'X', ''], ['X', '', '']];
        $assertion_o_position[] = [0, 2];
        // Center horizontal line
        $boards[] = [['', 'O', ''], ['', 'X', 'X'], ['', '', '']];
        $assertion_o_position[] = [1, 0];
        for ($i=0; $i<count($boards); $i++) {
            // Play the game
            $obj = new TicTacToe();
            $obj->init($boards[$i], $bot_player);
            $result = $obj->play();
            $index_1 = $assertion_o_position[$i][0];
            $index_2 = $assertion_o_position[$i][1];
            $this->assertEquals($result['board'][$index_1][$index_2], 'O');
        }
    }

    /**
     * This function tests the attack flank move
     *
     * @return void
     */
    public function testRates()
    {
        $bot_player = 'O';
        $boards = [];
        $rates = [];
        // No risks of losing -- Top left corner
        $boards[] = [['X', '', ''], ['', 'O', 'X'], ['', '', '']];
        $rates[] = [0, 0, 0, 0, 0, 0, 0, 0];
        $boards[] = [['X', '', ''], ['', 'O', ''], ['', 'X', '']];
        $rates[] = [0, 0, 0, 0, 0, 0, 0, 0];
        // No risks of losing -- Top center
        $boards[] = [['', 'X', ''], ['', 'O', 'X'], ['', '', '']];
        $rates[] = [0, 0, 0, 0, 0, 0, 0, 0];
        $boards[] = [['', 'X', ''], ['', 'O', ''], ['', 'X', '']];
        $rates[] = [0, 0, 0, 0, 0, 0, 0, 0];
        // No risks of losing -- Top right corner
        $boards[] = [['', '', 'X'], ['X', 'O', ''], ['', '', '']];
        $rates[] = [0, 0, 0, 0, 0, 0, 0, 0];
        $boards[] = [['', '', 'X'], ['', 'O', ''], ['', 'X', '']];
        $rates[] = [0, 0, 0, 0, 0, 0, 0, 0];
        // No risks of losing -- Middle left
        $boards[] = [['', 'X', ''], ['X', 'O', ''], ['', '', '']];
        $rates[] = [0, 0, 0, 0, 0, 0, 0, 0];
        $boards[] = [['', '', ''], ['X', 'O', ''], ['', 'X', '']];
        $rates[] = [0, 0, 0, 0, 0, 0, 0, 0];
        // No risks of losing -- Middle right
        $boards[] = [['', 'X', ''], ['', 'O', 'X'], ['', '', '']];
        $rates[] = [0, 0, 0, 0, 0, 0, 0, 0];
        $boards[] = [['', '', ''], ['', 'O', 'X'], ['', 'X', '']];
        $rates[] = [0, 0, 0, 0, 0, 0, 0, 0];
        // No risks of losing -- Bottom left corner
        $boards[] = [['', 'X', ''], ['', 'O', ''], ['X', '', '']];
        $rates[] = [0, 0, 0, 0, 0, 0, 0, 0];
        $boards[] = [['', '', ''], ['', 'O', 'X'], ['X', '', '']];
        $rates[] = [0, 0, 0, 0, 0, 0, 0, 0];
        // No risks of losing -- Bottom center
        $boards[] = [['', '', ''], ['X', 'O', ''], ['', 'X', '']];
        $rates[] = [0, 0, 0, 0, 0, 0, 0, 0];
        $boards[] = [['', '', ''], ['', 'O', 'X'], ['', 'X', '']];
        $rates[] = [0, 0, 0, 0, 0, 0, 0, 0];
        // No risks of losing -- Bottom right corner
        $boards[] = [['', 'X', ''], ['', 'O', ''], ['', '', 'X']];
        $rates[] = [0, 0, 0, 0, 0, 0, 0, 0];
        $boards[] = [['', '', ''], ['X', 'O', ''], ['', '', 'X']];
        $rates[] = [0, 0, 0, 0, 0, 0, 0, 0];
        // No risks of losing because it's blocked
        $boards[] = [['X', '', ''], ['', 'O', ''], ['', '', 'X']];
        $rates[] = [0, 0, 0, 0, 0, 0, 0, 0];
        // Top horizontal risk of losing
        $boards[] = [['X', '', 'X'], ['', 'O', ''], ['', '', '']];
        $rates[] = [-(2/3), 0, 0, 0, 0, 0, 0, 0];
        // Middle horizontal risk of losing
        $boards[] = [['', 'O', ''], ['X', '', 'X'], ['', '', '']];
        $rates[] = [0, -(2/3), 0, 0, 0, 0, 0, 0];
        // Bottom horizontal risk of losing
        $boards[] = [['', '', ''], ['', 'O', ''], ['X', '', 'X']];
        $rates[] = [0, 0, -(2/3), 0, 0, 0, 0, 0];
        // Left vertical risk of losing
        $boards[] = [['X', '', ''], ['', 'O', ''], ['X', '', '']];
        $rates[] = [0, 0, 0, -(2/3), 0, 0, 0, 0];
        // Middle vertical risk of losing
        $boards[] = [['', 'X', ''], ['O', '', ''], ['', 'X', '']];
        $rates[] = [0, 0, 0, 0, -(2/3), 0, 0, 0];
        // Right vertical risk of losing
        $boards[] = [['', '', 'X'], ['O', '', ''], ['', '', 'X']];
        $rates[] = [0, 0, 0, 0, 0, -(2/3), 0, 0];
        // Diagonal risk of losing
        $boards[] = [['X', '', ''], ['O', '', ''], ['', '', 'X']];
        $rates[] = [0, 0, 0, 0, 0, 0, -(2/3), 0];
        $boards[] = [['', '', 'X'], ['O', '', ''], ['X', '', '']];
        $rates[] = [0, 0, 0, 0, 0, 0, 0, -(2/3)];
        // Top horizontal opportunity to win
        $boards[] = [['O', 'O', ''], ['', 'X', ''], ['', 'X', '']];
        $rates[] = [(2/3), 0, 0, 0, 0, 0, 0, 0];
        // Middle horizontal opportunity to win
        $boards[] = [['', 'X', ''], ['O', '', 'O'], ['', '', 'X']];
        $rates[] = [0, (2/3), 0, 0, 0, 0, 0, 0];
        // Bottom horizontal opportunity to win
        $boards[] = [['', '', 'X'], ['', 'X', ''], ['O', 'O', '']];
        $rates[] = [0, 0, (2/3), 0, 0, 0, 0, 0];
        // Left vertical opportunity to win
        $boards[] = [['O', '', ''], ['', 'X', ''], ['O', '', 'X']];
        $rates[] = [0, 0, 0, (2/3), 0, 0, 0, 0];
        // Middle vertical opportunity to win
        $boards[] = [['', 'O', 'X'], ['X', '', ''], ['', 'O', '']];
        $rates[] = [0, 0, 0, 0, (2/3), 0, 0, 0];
        // Right vertical opportunity to win
        $boards[] = [['X', '', 'O'], ['', 'X', ''], ['', '', 'O']];
        $rates[] = [0, 0, 0, 0, 0, (2/3), 0, 0];
        // Diagonal opportunity to win
        $boards[] = [['O', 'X', ''], ['X', 'O', ''], ['', '', '']];
        $rates[] = [0, 0, 0, 0, 0, 0, (2/3), 0];
        $boards[] = [['', 'X', 'O'], ['', 'O', 'X'], ['', '', '']];
        $rates[] = [0, 0, 0, 0, 0, 0, 0, (2/3)];
        for ($i=0; $i<count($boards); $i++) {
            // Play the game
            $obj = new TicTacToe();
            $obj->init($boards[$i], $bot_player);
            $result = $obj->getRates();
            $this->assertEquals($result, $rates[$i]);
        }
    }

    /**
     * This function tests the attack flank move
     *
     * @return void
     */
    public function testAttackFlankMove()
    {
        $bot_player = 'O';
        $boards = [];
        $assertion_o_position = [];
        // Defend top right corner
        $boards[] = [['X', '', ''], ['', 'O', 'X'], ['', '', '']];
        $assertion_o_position[] = [0, 2];
        $boards[] = [['', 'X', ''], ['', 'O', ''], ['', '', 'X']];
        $assertion_o_position[] = [0, 2];
        // Defend bottom left corner
        $boards[] = [['X', '', ''], ['', 'O', ''], ['', 'X', '']];
        $assertion_o_position[] = [2, 0];
        $boards[] = [['', '', ''], ['X', 'O', ''], ['', '', 'X']];
        $assertion_o_position[] = [2, 0];
        // Defend top left corner
        $boards[] = [['', '', 'X'], ['X', 'O', ''], ['', '', '']];
        $assertion_o_position[] = [0, 0];
        $boards[] = [['', 'X', ''], ['', 'O', ''], ['X', '', '']];
        $assertion_o_position[] = [0, 0];
        // Defend bottom right corner
        $boards[] = [['', '', 'X'], ['', 'O', ''], ['', 'X', '']];
        $assertion_o_position[] = [2, 2];
        $boards[] = [['', '', ''], ['', 'O', 'X'], ['X', '', '']];
        $assertion_o_position[] = [2, 2];
        for ($i=0; $i<count($boards); $i++) {
            // Play the game
            $obj = new TicTacToe();
            $obj->init($boards[$i], $bot_player);
            $result = $obj->play();
            $index_1 = $assertion_o_position[$i][0];
            $index_2 = $assertion_o_position[$i][1];
            $this->assertEquals($result['board'][$index_1][$index_2], 'O');
        }
    }

    /**
     * This function tests the end game feature
     *
     * @return void
     */
    public function testEndGame()
    {
        // Draw
        $bot_player = 'O';
        $board = [['X', 'O', 'X'], ['X', 'X', 'O'], ['O', 'X', 'O']];
        $obj = new TicTacToe();
        $obj->init($board, $bot_player);
        $result = $obj->play();
        $this->assertEquals($result['winner'], '');
        $this->assertEquals($result['is_game_over'], 1);
        $bot_player = 'X';
        $board = [['X', 'O', 'X'], ['X', 'X', 'O'], ['O', 'X', 'O']];
        $obj = new TicTacToe();
        $obj->init($board, $bot_player);
        $result = $obj->play();
        $this->assertEquals($result['winner'], '');
        $this->assertEquals($result['is_game_over'], 1);

        // Win
        $bot_player = 'O';
        $board = [['O', '', 'X'], ['', 'O', ''], ['X', 'X', 'O']];
        $obj = new TicTacToe();
        $obj->init($board, $bot_player);
        $result = $obj->play();
        $this->assertEquals($result['winner'], 'O');
        $this->assertEquals($result['is_game_over'], 1);
        $bot_player = 'X';
        $board = [['X', 'X', 'X'], ['O', 'O', 'X'], ['X', 'O', 'O']];
        $obj = new TicTacToe();
        $obj->init($board, $bot_player);
        $result = $obj->play();
        $this->assertEquals($result['winner'], 'X');
        $this->assertEquals($result['is_game_over'], 1);

        $bot_player = 'O';
        $board = [['O', 'X', 'O'], ['', 'X', 'O'], ['X', 'O', 'X']];
        $obj = new TicTacToe();
        $obj->init($board, $bot_player);
        $result = $obj->play();
        $this->assertEquals($result['winner'], '');
        $this->assertEquals($result['is_game_over'], 1);
    }
}
