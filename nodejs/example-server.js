var pg = require ('pg');
var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var path = require('path');
var i = 0;
var pgConString = "pg://" +  process.argv[2] + ":" +  process.argv[3] + "@localhost:" +  process.argv[4] + "/" +  process.argv[5];
if(  process.argv[5] == undefined ) console.log( 'Usage: server.js User Passwd Port Database'  );
console.log( pgConString )

app.get('/', function(req, res){
    //res.sendFile(path.join(__dirname, '../', 'index.html'));
    //res.sendFile('index.html', { root: path.join(__dirname, '..') });
    res.sendFile('example-client.html');
});

pg.connect(pgConString, function(err, client) {
  if(err) {
    console.log(err);
  }
  client.on('notification', function(msg) {
      //console.log(msg);
      io.emit('chat message', msg.payload );
      console.log(msg.payload );
  });
  var query = client.query("LISTEN crmti_watcher");
});

/*
io.on('connection', function(socket){
    console.log(++i + ' user connected');
    io.emit('chat message', 'Ein neuer User ist hinzugekommen');
    socket.on('disconnect', function(){
    console.log('user disconnected');
    i--;
    });
    socket.on('chat message', function(msg){
    console.log('message: ' + msg);
    io.emit('chat message', msg);
    });
    //socket.broadcast.emit('hi');
});
*/
io.on('connection', function(socket){
    socket.on('chat message', function(msg){
        console.log('message: ' + msg);
        io.emit('chat message', msg  );
    });
});

http.listen(3000,"0.0.0.0", function(){
  console.log('listening on *:3000');
});

// Inspirationen:
//http://socket.io/get-started/chat/
//http://www.gianlucaguarini.com/blog/nodejs-and-a-simple-push-notification-server/
//http://bjorngylling.com/2011-04-13/postgres-listen-notify-with-node-js.html
//https://denibertovic.com/talks/real-time-notifications/#/6
