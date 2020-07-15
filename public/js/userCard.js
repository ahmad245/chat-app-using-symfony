const createUserCard = (email, id, lastMessag = "") => {
    let li = document.createElement("a");
    li.classList.add("collection-item");
    li.setAttribute("id", id);
    li.innerHTML = `
          <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01.jpg" class="responsive-img circle" alt="avatar" />
             <div class="about">
          <div class="name">${email}</div>
          <div class="status">
           ${  lastMessag ? lastMessag.substr(0,10) : 'no yet message'}
          </div>
          <i class="fa fa-circle online" id="${email}"></i> 
    </div>
 
      `;
    return li;
};

const createFriendCard = (firstName, email, id,participantId) => {
    let div = document.createElement("li");
    div.classList.add("collection-item");
    div.classList.add("userCard");
    div.innerHTML = `
      <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01_green.jpg" class="responsive-img circle" alt="avatar" />
        
        <div class="chat-about">
          <div class="chat-with">Chat with ${firstName}</div>
          <div class="chat-num-messages">${email}</div>
          
        </div>
        <i class="material-icons icon_add_conv" data-participantId='${participantId}' id='${id}'>add</i>
  `;
    return div;
};