<?php namespace App\Libraries;

class TicTacToe
{
    private $bot_player = '';
    private $human_player = '';
    private $move_number = 0;
    private $original_board_arr = [];
    private $board_arr = [];
    private $bot_moves_arr = [];
    private $human_moves_arr = [];
    private $empty_cells_arr = [];
    private $winning_lines = [];
    private $player_1 = 'X';
    private $player_2 = 'O';
    private $winner = '';
    private $is_game_over = 0;

    /**
     * This function initiates the TicTacToe game.
     * It gets all the possible winning lines. It parses the current board.
     *
     * @param array $board An array of 3 arrays, each containing 3 values
     * @param string $bot_player The bot player's name: "X" or "O"
     *
     * @return void
     */
    public function init($board, $bot_player)
    {
        // Generate the winning lines
        $this->generateWinningLines();
        // Assign the name of the players. Capitalize the name to avoid confusion
        $bot_player = strtoupper($bot_player);
        if ($bot_player == $this->player_1) {
            $this->bot_player = $this->player_1;
            $this->human_player = $this->player_2;
        } else {
            $this->bot_player = $this->player_2;
            $this->human_player = $this->player_1;
        }
        // Parse the TicTacToe board
        $this->parse($board);
    }

    /**
     * This function plays the TicTacToe game as a bot player.
     *
     * @return array An array containing 2 values: an array of 3 arrays, each
     * containing 3 values; and an integer for the game over flag
     */
    public function play()
    {
        // Pick a move and add it to the TicTacToe board
        $this->pickMove();
        // Export the TicTacToe board
        $board = $this->exportBoard();
        $result = ['board' => $board, 'is_game_over' => (int) $this->is_game_over, 'winner' => (string) $this->winner];
        return $result;
    }

    /**
     * This function ends the game
     *
     * @return void
     */
    private function endGame($winner)
    {
        $this->winner = $winner;
        $this->is_game_over = 1;
    }

    /**
     * This function generates all the possible winning lines
     *
     * @return void
     */
    private function generateWinningLines()
    {
        // Horizontal
        $this->winning_lines[] =[0, 1, 2];
        $this->winning_lines[] =[3, 4, 5];
        $this->winning_lines[] =[6, 7, 8];
        // Vertical
        $this->winning_lines[] =[0, 3, 6];
        $this->winning_lines[] =[1, 4, 7];
        $this->winning_lines[] =[2, 5, 8];
        // Diagonal
        $this->winning_lines[] =[0, 4, 8];
        $this->winning_lines[] =[2, 4, 6];
    }

    /**
     * This function exports the board in the same format it was sent.
     * It transforms the array of integers to an array of 3 arrays, each containing 3 values.
     *
     * @return array An array of 3 arrays, each containing 3 values
     */
    private function exportBoard()
    {
        $result = [];
        $result[] = [$this->board_arr[0], $this->board_arr[1], $this->board_arr[2]];
        $result[] = [$this->board_arr[3], $this->board_arr[4], $this->board_arr[5]];
        $result[] = [$this->board_arr[6], $this->board_arr[7], $this->board_arr[8]];
        return $result;
    }

    /**
     * This function checks if there's a winner.
     * It returns "true" if there's a winner and "false" if there isn't.
     *
     * @return boolean True if there's a winner and false if there isn't
     */
    private function checkForWinner()
    {
        for ($i=0; $i<count($this->winning_lines); $i++) {
            $winning_line = $this->winning_lines[$i];
            $current_line = [];
            $filled_cells = 0;
            for ($j=0; $j<count($this->board_arr); $j++) {
                $cell = $this->board_arr[$j];
                if (in_array($j, $winning_line, true)) {
                    $current_line[] = $cell;
                }
                if ($cell != '') {
                    $filled_cells++;
                }
            }
            if (count(array_unique($current_line)) === 1
              && ($current_line[0] === $this->human_player || $current_line[0] === $this->bot_player)) {
                if ($current_line[0] == $this->player_1) {
                    // Player 1 has won
                    $this->endGame($this->player_1);
                    return true;
                } elseif ($current_line[0] == $this->player_2) {
                    // Player 2 has won
                    $this->endGame($this->player_2);
                    return true;
                }
            }
            if ($filled_cells == 9) {
                // Draw
                $this->endGame('');
                return true;
            }
        }
        return false;
    }

    /**
     * This function will return an array of float numbers.
     * It allows us to calculate the rate of winning and the rate of loosing of a bot player for every winning line.
     *
     * @return array An array containing float numbers
     */
    public function getRates()
    {
        $rates = [];
        for ($i=0; $i<count($this->winning_lines); $i++) {
            $rates[$i] = 0;
            $winning_line = $this->winning_lines[$i];
            $current_line = [];
            for ($j=0; $j<count($this->board_arr); $j++) {
                $cell = $this->board_arr[$j];
                if (in_array($j, $winning_line, true)) {
                    $current_line[] = $cell;
                }
            }
            if (!(in_array($this->human_player, $current_line) && in_array($this->bot_player, $current_line))) {
                // There's not X and O together
                if (count(array_keys($current_line, $this->human_player, true)) === 2
                  && count(array_keys($current_line, '', true)) === 1) {
                    // Risk of losing
                    $rates[$i] = -(2/3);
                } elseif (count(array_keys($current_line, $this->bot_player, true)) === 2
                  && count(array_keys($current_line, '', true)) === 1) {
                    // Opportunity to win
                    $rates[$i] = 2/3;
                }
            }
        }
        return $rates;
    }

    /**
     * This function will return the index of a cell on the TicTacToe board.
     * First it gets all the rates to evaluate whether the bot should be in attack mode or defensive mode.
     * If in attack mode, it tries to do find a winning line. Otherwise, it does a strong attack move.
     * If in defensive mode, it tries to block a winning line.
     * Otherwise, it'll pick a random move.
     *
     * @return integer The index of the cell on the TicTacToe board
     */
    private function smartPick()
    {
        $cell = null;
        // Get the rates
        $rates = $this->getRates($this->winning_lines, $this->board_arr);
        $min = min($rates);
        if ($min < 0) {
            // The bot risks of losing. Counter attack mode
            $index = array_search($min, $rates);
            $line = $this->winning_lines[$index];
            // Strategy: Try to pick the center of the line. Otherwise, pick any other cell
            if (in_array($line[1], $this->empty_cells_arr, true)) {
                $cell = $line[1];
            } else {
                $arr = array_intersect([$line[0], $line[2]], $this->empty_cells_arr);
                if (count($arr) > 0) {
                    $cell = $this->pickRandomValue($arr);
                }
            }
        } else {
            // The bot cannot loose. Attack mode
            $max = max($rates);
            if ($max > 0) {
                // The bot has a chance of winning. Attack mode
                $index = array_search($max, $rates);
                $line = $this->winning_lines[$index];
                // Strategy: Try to pick the center of the line. Otherwise, pick any other cell
                if (in_array($line[1], $this->empty_cells_arr, true)) {
                    $cell = $line[1];
                } else {
                    $arr = array_intersect([$line[0], $line[2]], $this->empty_cells_arr);
                    if (count($arr) > 0) {
                        $cell = $this->pickRandomValue($arr);
                    }
                }
            } else {
                $cell = $this->strongAttackMove();
            }
        }
        // Random move
        if ($cell === null) {
            if (count($this->empty_cells_arr) > 0) {
                $cell = $this->pickRandomValue($this->empty_cells_arr);
            }
        }
        return $cell;
    }

    /**
     * This function will return the index of a cell on the TicTacToe board.
     * It allows the bot to do a strong attack move.
     * Otherwise, it'll pick a random move.
     *
     * @return integer The index of the cell on the TicTacToe board
     */
    private function strongAttackMove()
    {
        $cell = null;
        if (($this->move_number == 0 && $this->bot_player == $this->player_1)
          || ($this->move_number == 1 && $this->bot_player == $this->player_2)) {
            // First bot move
            // Strategy: Pick the center
            if (in_array(4, $this->empty_cells_arr, true)) {
                $cell = 4;
            }
        }
        // Strategy: Defend the flank. Check potential winning move for the human player in next round
        if ($cell === null) {
            $flank_combinations = [];
            // Top left corner
            $flank_combinations[] = [0, 5, 2];
            $flank_combinations[] = [0, 7, 6];
            // Top right corner
            $flank_combinations[] = [2, 3, 0];
            $flank_combinations[] = [2, 7, 8];
            // Bottom left corner
            $flank_combinations[] = [6, 1, 0];
            $flank_combinations[] = [6, 5, 8];
            // Bottom right corner
            $flank_combinations[] = [8, 1, 2];
            $flank_combinations[] = [8, 3, 6];
            for ($i=0; $i<count($flank_combinations); $i++) {
                if (in_array($flank_combinations[$i][0], $this->human_moves_arr, true)
                  && in_array($flank_combinations[$i][1], $this->human_moves_arr, true)) {
                    if (in_array($flank_combinations[$i][2], $this->empty_cells_arr, true)) {
                        $cell = $flank_combinations[$i][2];
                    }
                }
            }
        }
        // Strategy: Pick the center or any corner
        if ($cell === null) {
            $arr = array_intersect([0, 2, 4, 6, 8], $this->empty_cells_arr);
            if (count($arr) > 0) {
                $cell = $this->pickRandomValue($arr);
            }
        }
        // Random move
        if ($cell === null) {
            if (count($this->empty_cells_arr) > 0) {
                $cell = $this->pickRandomValue($this->empty_cells_arr);
            }
        }
        return $cell;
    }

    /**
     * This function is a helper to pick any value from an array of integers.
     *
     * @param array $arr The array of integers
     *
     * @return integer The index of the cell on the TicTacToe board
     */
    private function pickRandomValue($arr)
    {
        return $arr[array_rand($arr, 1)];
    }

    /**
     * This function is the main function to pick a move.
     * It picks a move and add it on the board.
     *
     * @return void
     */
    private function pickMove()
    {
        $cell = null;
        // Check if there's a winner
        if (!$this->checkForWinner()) {
            if ($this->move_number <= 1) {
                // First move: Attack mode
                $cell = $this->strongAttackMove();
            } else {
                // Attack mode and defensive mode
                $cell = $this->smartPick();
            }
            // Choose the move
            $this->board_arr[$cell] = $this->bot_player;
            // Check if there's a winner
            $this->checkForWinner();
        }
    }

    /**
     * This function parses the TicTacToe board.
     * It returns an array of values instead of an array of arrays.
     *
     * @param array $board The array of 3 arrays, each containing 3 values
     *
     * @return void
     */
    private function parse($board)
    {
        $index = 0;
        for ($i=0; $i<count($board); $i++) {
            for ($j=0; $j<count($board[$i]); $j++) {
                // Make sure the cell's value is capitalized
                $cell = strtoupper($board[$i][$j]);
                if ($cell != '') {
                    $this->move_number++;
                }
                $this->board_arr[] = $cell;
                if ($cell == $this->bot_player) {
                    $this->bot_moves_arr[] = $index;
                } elseif ($cell == $this->human_player) {
                    $this->human_moves_arr[] = $index;
                } else {
                    $this->empty_cells_arr[] = $index;
                }
                $index++;
            }
        }
        $this->original_board_arr = $this->board_arr;
    }
}
