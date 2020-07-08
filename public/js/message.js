const createMessageLeft = (el) => {
    let me = document.createElement("li");
    me.classList.add("clearfix");
    if (el.mine) {
        me.innerHTML = `
         <div class="message-data align-right">
           <span class="message-data-time" >${moment(el.createdAt).format(
             "dddd, MMMM Do YY"
           )}
           <span class="message-data-name" >me</span> <i class="fa fa-circle me"></i>
           
         </div>
         <div class="message other-message float-right">
           ${el.content}
         </div>
      
        `;
        return me;
    }
};

const createMessageRight = (el) => {
    let me = document.createElement("li");
    me.classList.add("clearfix");
    me.innerHTML = `
         
    <div class="message-data">
      <span class="message-data-name"><i class="fa fa-circle online"></i> ${
        el.user.firstName
      }</span>
      <span class="message-data-time">${moment(el.createdAt).format(
        "dddd, MMMM Do YY"
      )}</span>
    </div>
    <div class="message my-message">
       ${el.content}
    </div>
  
    `;
    return me;
};

const createMessageNow = (content) => {
    let me = document.createElement("li");
    me.classList.add("clearfix");
    me.innerHTML = `
    <div class="message-data align-right">
      <span class="message-data-time" >${moment().format(
        "dddd, MMMM Do YY"
      )}</span>
      <span class="message-data-name" >me</span> <i class="fa fa-circle me"></i>
      
    </div>
    <div class="message other-message float-right">
      ${content}
    </div>
 
   `;
    return me;
};