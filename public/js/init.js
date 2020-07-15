moment().format();
let listUser = document.querySelector(".list_user");
let chat = document.querySelector(".chat-history");
let message = document.getElementById("message-to-send");
let sendMessage = document.getElementById("btn");
let createConv = document.getElementById("createConv");
let modal = document.querySelector(".modal");

let convId;
let urlHub;
let hub;
let friends = document.querySelector(".friends");
let  blockListCollection= document.querySelector(".blockListCollection");
let resttime = [];

let blockList=[];
let userBlockList=[];