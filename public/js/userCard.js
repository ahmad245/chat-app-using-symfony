const createUserCard = (email, id, lastMessag = "") => {
    let li = document.createElement("li");
    li.classList.add("clearfix");
    li.setAttribute("id", id);
    li.innerHTML = `
          <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01.jpg" alt="avatar" />
             <div class="about">
          <div class="name">${email}</div>
          <div class="status">
           ${lastMessag || 'no yet message'}
          </div>
          <i class="fa fa-circle online" id="${email}"></i> 
    </div>
 
      `;
    return li;
};

const createFriendCard = (firstName, email, id) => {
    let div = document.createElement("div");
    div.classList.add("chat-header");
    div.innerHTML = `
      <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01_green.jpg" alt="avatar" />
        
        <div class="chat-about">
          <div class="chat-with">Chat with ${firstName}</div>
          <div class="chat-num-messages">${email}</div>
          
        </div>
          <i class="material-icons icon_add_conv" id="${id}">add</i>
  `;
    return div;
};