const activeClass = (e) => {

    if (e.target.closest("a").classList.contains("active")) {
        e.preventDefault();
        return true;;
    } else {
        document.querySelectorAll("a").forEach((el) => {
            if (el.classList.contains("active")) {
                el.classList.remove("active");
            }
        });
        e.target.closest("a").classList.add("active");
    }
    return false;
};

const fetchMessages = (id, e = null) => {
    message.value = '';
    convId = id;
    let messages;


    message.disabled = false;

    if (document.getElementById(convId).querySelector('i').dataset.block) {
        sendMessage.disabled = true;
        message.disabled = true;

    }
    if (e) {
        let isClecked = activeClass(e);
        chat.scrollTo(0, chat.scrollHeight);

        if (isClecked) return;
    }
    messages = document.querySelector(`ul[data-conversation="${id}"]`);
    if (!messages) {
        messages = document.createElement("ul");
        messages.setAttribute("data-conversation", id);
    }

    return axios.get(`/messages/${id}`).then((response) => {
        chat.innerHTML = "";

        response.data.forEach((el) => {
            let me;

            if (el.mine) {
                me = createMessageLeft(el);
            } else {
                me = createMessageRight(el);
            }

            messages.append(me);
            chat.append(messages);
            chat.scrollTo(0, chat.scrollHeight);



        });

        return response.data;
    }).then(() => {

    });
};

const fetchUsers = () => {
        listUser.innerHTML = "";
        let urlUser = "/conversations";

        return axios
            .get(urlUser)
            .then((response) => {
                    urlHub = response.headers.link.match(
                        /<([^>]+)>;\s+rel=(?:mercure|"[^"]*mercure[^"]*")/
                    )[1];
                    console.log(response.data);
                    hub = new URL(urlHub);
                    hub.searchParams.append("topic", `/conversations/${response.data.user}`);
                    // Subscribe to updates
                    const eventSource = new EventSource(hub);
                    eventSource.onmessage = (event) => {
                        updateConversation(JSON.parse(event.data));
                    };
                    //   const hubUrl = response.headers.get('Link').match(/<([^>]+)>;\s+rel=(?:mercure|"[^"]*mercure[^"]*")/)[1];

                    response.data.conversations.forEach((el) => {
                                // userCard.js
                                let li = createUserCard(el.email, el.conversationId, el.content);
                                blockList = response.data.blockedBy;

                                blockList.forEach((userBlock) => {
                                    if (userBlock.email == el.email) {

                                        li.querySelector('i').setAttribute('data-block', true);
                                        li.querySelector('i').innerText = "blocked";
                                    }
                                })

                                li.addEventListener("click", (e) => {
                                            fetchMessages(el.conversationId, e);
                                            let aHeader = document.getElementById('chat-header');
                                            aHeader.innerHTML = `
                                            <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01.jpg" class="responsive-img circle" alt="avatar" />
                                                    <div class="about">
                                                <div class="name">${el.email}</div>
                                                
                                                <i class="fa fa-circle online" data-participantsId="${el.participantId}" id="${el.email}header">${document.getElementById(`${el.email}`).innerText}</i> 
                                            </div>
                                            <a class='dropdown-trigger  card-header icon_add_conv' href='#' data-target='dropdown1' ><i class="material-icons icon_add_conv" >more_vert</i> </a>
                                            <ul id='dropdown1' class='dropdown-content'>
                                                    <li><a href="#!" data-url="/blocklist/add" id="addToBlockList">Add To Block List</a></li>
                                                    <li class="divider" tabindex="-1"></li>
                                                    <li><a href="#!"><i class="material-icons">view_module</i>four</a></li>
                                                </ul>
                                        
                                       `;
                                        var dropdownelems = document.querySelectorAll('.dropdown-trigger');
                                        var dropdowninstances = M.Dropdown.init(dropdownelems, {});     

                                        document.getElementById('addToBlockList').addEventListener('click',(e)=>{
                                         

                                            axios.post(`/blocklist/add/${el.participantId}`).then((response)=>{
                                                console.log(response.data)
                                            })


                                            
                                        })

              
            
                });


                listUser.append(li);
                updateStateForFirstTime(el);
              
            });

            return response.data;
        })
        .then((data) => {
            hub.searchParams.append("topic", `/allUser`);
            // Subscribe to updates
            const eventSource = new EventSource(hub);
            eventSource.onmessage = (event) => {
                let online = JSON.parse(event.data);
                updateState(online);

            };
            return data;
        }).then(async() => {
            let urlUsers = "/user/allUsers";
            let allData = await axios.get(urlUsers);
            let users = allData.data.users;
              userBlockList=allData.data.userBlockList;
     
            users.forEach((el,index) => {
               
                if (!document.getElementById(el[0].email) && !userBlockList.find(element=>element.email==el[0].email)) {
                    let div = createFriendCard(el[0].firstName, el[0].email, el[0].id,el.participantId);
                    friends.append(div);
                    div.querySelector("i").addEventListener("click", addConv);
                }
                

            });
        })
        .catch((err) => {
            console.log(err);
        });
};

sendMessage.addEventListener("click", () => {
    let content = message.value;
    if(document.getElementById(convId).querySelector('i').dataset.block){
        sendMessage.disabled = true;
        message.disabled=true;
        return;
    }
    axios.post(`/messages/${convId}`, {
            content: content,
        })
        .then((response) => {
            // message.js
            let me = createMessageNow(content);
            message.value = "";
            let mess = chat.querySelector("ul");

            if (!mess) {
                mess = document.createElement("ul");
                mess.setAttribute("data-conversation", convId);
                chat.append(mess);
            }
            mess.append(me);

            let li = document.getElementById(`${convId}`);
            li.querySelector(".status").innerText = content.substr(0, 10);

            listUser.insertBefore(li, listUser.querySelectorAll("a")[0]);
            chat.scrollTo(0, chat.scrollHeight);
        });
});

const updateConversation = (el) => {
    let me;
    if (el.mine) {
        me = createMessageLeft(el);
    } else {
        me = createMessageRight(el);
    }
    if (document.querySelector(`ul[data-conversation="${el.conversation.id}"]`)) {
        document
            .querySelector(`ul[data-conversation="${el.conversation.id}"]`)
            .append(me);
    }

    let li = document.getElementById(`${el.conversation.id}`);
    if (!li) {
        li = createUserCard(el.user.email, el.conversation.id, el.content);

        li.addEventListener("click", fetchMessages.bind(null, el.conversation.id));
        listUser.append(li);
        updateStateForFirstTime(el);
    }
    li.querySelector(".status").innerText = el.content.substr(0, 10);

    listUser.insertBefore(li, listUser.querySelectorAll("a")[0]);
};

const removeFromBlockList=(e)=>{
     let idTarget = e.target.id;
     console.log(idTarget,userBlockList)
    if (idTarget) {
   let elm=userBlockList.find(el=>el.id==idTarget)
      if(elm){
         
          axios.post(`/blocklist/remove/${elm.blockId}`, {
            id: elm.id,
        }) .then((response) => {
            console.log(response)
        })

          return;
      }
  }
}
const addConv = (e) => {
    let idTarget = e.target.id;
    if (idTarget) {
   let elm=userBlockList.find(el=>el.userParticipant==idTarget)
      if(elm){
         
          axios.post(`/blocklist/remove/${elm.id}`, {
            id: elm.id,
        }) .then((response) => {
            console.log(response)
        })

          return;
      }
     // return;
        axios
            .post(`/conversations/add/${idTarget}`, {
                id: idTarget,
            })
            .then((response) => {
                let id = response.data.id;
                let user = response.data.otheruser;

                let li = createUserCard(user.email, id);

                li.addEventListener("click", fetchMessages.bind(null, id));
                listUser.append(li);
                updateStateForFirstTime(user);
                document.getElementById(idTarget).parentElement.remove();
            });
    }
};

const updateState = (online) => {
    let element = document.getElementById(`${online.email}`);
    clearInterval(resttime[online.email]);
    if (element) {
         if( element.dataset && element.dataset.block){
           //  document.getElementById(`${el.email}header`).innerText = element.innerText;
         return;
    }
        element.innerText = online.online ?"online" : moment(online.lastActive).fromNow();
            if (document.getElementById(`${online.email}header`) ) {
              
                    document.getElementById(`${online.email}header`).innerText = element.innerText
                }
        if (!online.online) {
            resttime[online.email] = setInterval(() => {
                
                if (element.innerText != "online") {
                    element.innerText = moment(
                        online.lastActive
                    ).fromNow();
                
                }
                    if (document.getElementById(`${online.email}header`) ) {
                       
                    document.getElementById(`${online.email}header`).innerText =element.innerText
                        }
            }, 1000);
        }
    }
};


const updateStateForFirstTime = (el) => {
    let element = document.getElementById(`${el.email}`);
   
    clearInterval(resttime[el.email]);
    if (element) {
        console.log(element.dataset.block)
         if( element.dataset && element.dataset.block){
           //  document.getElementById(`${el.email}header`).innerText = element.innerText;
         return;
    }
        element.innerText = moment().subtract(2, "minutes") < moment(el.lastActivityAt) ? "online" : moment(el.lastActivityAt).fromNow();
         if (document.getElementById(`${el.email}header`) ) {
                    document.getElementById(`${el.email}header`).innerText = element.innerText
                }

        if (!(moment().subtract(1, "minutes") < moment(el.lastActivityAt))) {
            resttime[el.email] = setInterval(() => {
                
                if (document.getElementById(`${el.email}`).innerText != "online") {
                    document.getElementById(`${el.email}`).innerText = moment(
                        el.lastActivityAt
                    ).fromNow();

                   
                }
                 if (document.getElementById(`${el.email}header`) ) {
                     
                    document.getElementById(`${el.email}header`).innerText = element.innerText
                }
              
            }, 1000);
        }
    }
    
};




poulateAllUsers = async() => {
    let urlUsers = "/user/allUsers";
    let allData = await axios.get(urlUsers);
    let users = allData.data.users;
    users.forEach((el) => {
        let div = createFriendCard(el.firstName, el.email, el.id);
        friends.append(div);
        div.querySelector("i").addEventListener("click", addConv);
    });
};
// poulateAllUsers();

//createConv.addEventListener('click',fetchUsers)

message.addEventListener('input', (e) => {
    if(document.getElementById(convId).querySelector('i').dataset.block){
        sendMessage.disabled = true;
        message.disabled=true;
        return;
    }
    if (message.value!=='') {
        sendMessage.disabled = false;
    } else {
        sendMessage.disabled = true;
    }
})


const getBlockList=()=>{
    axios.get('/blocklist').then((response)=>{
        let userList=response.data;
        userList.forEach((el)=>{
             let div = createFriendCard(el.firstName, el.email, el.id);
              blockListCollection.append(div);
                div.querySelector("i").addEventListener("click", removeFromBlockList);
        })
    })
}

getBlockList();
fetchUsers();
createConv.addEventListener('click',(e)=>{
    if(friends.getElementsByTagName("li").length<1){
        let div = document.createElement("li");
    div.classList.add("collection-item");
    div.classList.add("userCard");
    div.innerText="you do not have friend yet";
      friends.append(div);
    }
})


// userBlockList.addEventListener('click',(e)=>{
//     if(friends.getElementsByTagName("li").length<1){
//         let div = document.createElement("li");
//     div.classList.add("collection-item");
//     div.classList.add("userCard");
//     div.innerText="you do not have friend yet";
//       friends.append(div);
//     }
// })