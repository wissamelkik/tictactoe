import React, { Component } from 'react';
import ReactDOM from 'react-dom';

// Handle online and offline events
window.addEventListener('online',  () => {
  alert("You're back online. The page will be refreshed");
  window.location.reload();
});
window.addEventListener('offline', () => {
  alert("You're currently offline. The game needs an active internet connection.");
});

/**
 * The Board component
 */
class Board extends React.Component {

  /**
   * This is the constructor of the Board component
   *
   * @param {object} props The properties
   * @public
   */
  constructor(props) {
    super(props);
    this.handleRegistrationInput = this.handleRegistrationInput.bind(this);
    this.state = {
      user_player: '',
      bot_player: '',
      cells: Array(9).fill(''),
      is_bot_turn: '',
      message: ''
    };
  }

  /**
   * This function renders a Cell component
   *
   * @param {integer} index The index within the Board array
   * @public
   */
  renderCell(index) {
    return <Cell value={this.state.cells[index]} onClick={() => this.handleCellClick(index)} />;
  }

  /**
   * This function converts the board from a format to another.
   * It converts an array to an array of 3 items, each containing an array of 3 values.
   *
   * @param {array} cells An array of cells
   * @public
   */
  convertBoard(cells){
    let board = [];
    if(cells.length > 0){
      let column_index = -1;
      for(let i=0; i<cells.length; i++){
        let cell_value = cells[i];
        if((i % 3) == 0){
          column_index++;
        }
        if(board[column_index] == undefined) board[column_index] = [];
        board[column_index].push(cell_value)
      }
    }
    return board;
  }

  /**
   * This function handles the click event on the Cell component
   *
   * @param {integer} index The index
   * @public
   */
  handleCellClick(index){
    if(this.state.is_bot_turn === false){
      const cells = this.state.cells.slice();
      cells[index] = this.state.user_player;
      this.setState({cells: cells});
      let game_over = false;
      let board = this.convertBoard(cells);
      this.pickAMove(board);
    }
  }

  /**
   * This function picks a move by sending an API request
   *
   * @param {array} board An array containing the board. An array of 3 items, each containing an array of 3 values
   * @public
   */
  pickAMove(board) {
    this.setState({is_bot_turn: true});
    doApiCall('POST', 'move', {player: this.state.bot_player, board: JSON.stringify(board)}).then((response) => {
      this.setState({is_bot_turn: false});
      if(response.result.board != undefined && response.result.winner != undefined && response.result.is_game_over != undefined){
        let winner = response.result.winner;
        let is_game_over = response.result.is_game_over;
        let new_board = JSON.parse(response.result.board);
        if(new_board){
          let new_cells = Array(9).fill('');
          let index = 0;
          for(let i=0; i<new_board.length; i++){
            for(let j=0; j<new_board[i].length; j++){
              new_cells[index] = new_board[i][j];
              index++;
            }
          }
          this.setState({cells: new_cells});
        }
        if(is_game_over == 1){
          if(winner == this.state.bot_player){
            this.setState({message: 'Game over. You lost'});
          } else if(winner == this.state.user_player){
            this.setState({message: 'You won'});
          } else {
            this.setState({message: 'Draw'});
          }
        }
      } else {
        // Error
        this.setState({message: 'A system error occurred. Please contact the administrator'});
      }
    }).catch(() => {
      // Error
      this.setState({message: 'A system error occurred. Please contact the administrator'});
    });
  }

  /**
   * This function handles the form in which the user can choose to play as "X" or "O"
   *
   * @param {string} value The value: "X" or "O"
   * @public
   */
  handleRegistrationInput(value) {
    this.setState({user_player: value});
    if(value == 'X'){
      this.setState({bot_player: 'O', is_bot_turn: false});
    } else {
      this.setState({bot_player: 'X', is_bot_turn: true}, () => {
        const cells = this.state.cells.slice();
        let board = this.convertBoard(cells);
        this.pickAMove(board);
      });
    }
  }

  /**
   * This function restarts the game
   *
   * @public
   */
  restartGame() {
    this.setState({user_player: '', bot_player: '', cells: Array(9).fill(''), is_bot_turn: '', message: ''});
  }

  /**
   * This function renders the Board component
   *
   * @public
   */
  render() {
    const user_player = this.state.user_player;
    let message = this.state.message;
    if (user_player != '' && (user_player == 'X' || user_player == 'O')) {
      // Load the board
      let cells = [];
      for (var i = 0; i < 9; i++) {
        cells.push(this.renderCell(i));
      }
      // Handle turn
      let current_player = '';
      if(this.state.is_bot_turn){
        current_player = 'Bot';
      } else {
        current_player = 'Your';
      }
      return <div className="game_container">
        <Message message={message} onClick={() => this.restartGame()} />
        <Status current_player={current_player} />
        <div className="container">{cells}</div>
        <button className="mrgt-10px" onClick={() => this.restartGame()}>Restart Game</button>
      </div>;
    } else {
      // The user is not registered. Load the registration form
      return <div>
        <Message message={message} onClick={() => this.restartGame()} />
        <h3>Please choose your player</h3>
        <PlayerInput value="X" onClick={() => this.handleRegistrationInput("X")} />
        <PlayerInput value="O" onClick={() => this.handleRegistrationInput("O")} />
      </div>
    }
  }
}

/**
 * The Cell component
 */
class Cell extends React.Component {

  /**
   * This function renders the Cell component
   *
   * @public
   */
  render() {
    return (
      <div>
        <button onClick={() => this.props.onClick()}>{this.props.value}</button>
      </div>
    );
  }

}

/**
 * The PlayerInput component
 */
class PlayerInput extends React.Component {

  /**
   * This function renders the PlayerInput component
   *
   * @public
   */
  render() {
    return (
      <button className="form-button" onClick={() => this.props.onClick()}>{this.props.value}</button>
    );
  }

}

/**
 * The Status component
 */
class Status extends React.Component {

  /**
   * This function renders the Status component
   *
   * @public
   */
  render() {
    return (
      <h4>{this.props.current_player}&nbsp;turn</h4>
    );
  }

}

/**
 * The Message component
 */
class Message extends React.Component {

  /**
   * This function renders the Message component
   *
   * @public
   */
  render() {
    if(this.props.message == ''){
      return ('');
    }
    return (<div className="message">
      <h4>{this.props.message}</h4>
      <button onClick={() => this.props.onClick()}>Restart Game</button>
    </div>
    );
  }

}

/**
 * The Header component
 */
class Header extends React.Component {

  /**
   * This function renders the Header component
   *
   * @public
   */
  render() {
    return (
      <div className="text-center">
        <div><img src="dist/images/apple-icon-120x120.png" alt="Tic Tac Toe" /></div>
        <h1>Tic Tac Toe</h1>
      </div>
    );
  }

}

/**
 * This function does an API call
 *
 * @param {string} method The HTTP method
 * @param {string} route The API route
 * @param {object} params An object containing all the parameters
 * @public
 */
function doApiCall(method, route, params){
  return new Promise((resolve, reject) => {
    fetch(settings.api_base + '/' + route, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(params)
    })
    .then(response => { return response.json(); })
    .then((data) => {
      if(data.status == 'success'){
        resolve(data)
      } else {
        reject(data.message)
      }
    }).catch(reject);
  })
}

/**
 * This function renders the whole app
 */
ReactDOM.render(
  <div className="text-center"><Header /><Board /></div>,
  document.getElementById("root")
);
