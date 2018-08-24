<?php
use Illuminate\Http\Request;

use App\Libraries\TicTacToe;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () {
    return view('game', []);
});

$router->post('/move', function (Request $request) {
    $result = array();
    $this->validate($request, [
        'player' => [
          'required',
          'regex:/X|O/',
        ],
        'board' => 'required'
    ]);
    // Check if the board is valid
    $invalid_board_message = 'Validation error: Invalid board';
    $valid_cell_values = array("X", "O", "");
    $board_str = $request->input("board");
    $board = json_decode($board_str);
    if ($board === null || count($board) != 3) {
        return response()->json([
          'status' => 'failure',
          'result' => [],
          'message' => $invalid_board_message
        ], 400);
    } else {
        for ($i=0; $i<count($board); $i++) {
            $row = $board[$i];
            if (count($row) != 3) {
                $message = 'Validation error: Invalid board';
                return response()->json([
                  'status' => 'failure',
                  'result' => [],
                  'message' => $invalid_board_message
                ], 400);
            } else {
                foreach ($row as $row_value) {
                    if (!in_array($row_value, $valid_cell_values, true)) {
                        return response()->json([
                          'status' => 'failure',
                          'result' => [],
                          'message' => $invalid_board_message
                        ], 400);
                    }
                }
            }
        }
    }
    // Get the player
    $bot_player = $request->input('player');
    // Pick a move
    $obj = new TicTacToe();
    $obj->init($board, $bot_player);
    $result = $obj->play();
    $final_board = json_encode($result['board']);
    $is_game_over = $result['is_game_over'];
    $winner = $result['winner'];
    return response()->json([
      'status' => 'success',
      'result' => [
        'board' => $final_board,
        'is_game_over' => $is_game_over,
        'winner' => $winner
      ],
      'message' => ''
    ], 200);
});
